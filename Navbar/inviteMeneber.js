/**
 * Invite Member functionality
 * Handles the invite modal, search, and invite actions
 */

// store the current context (workspace or task being invited to)
let inviteContext = {
    type: null,        // 'workspace' or 'task'
    workspaceID: null,
    taskID: null
};

/**
 * handle invite member action from dropdown
 * shows the invite modal for workspace invitation
 */
function handleInviteMember(workspaceItem) {
    if (!workspaceItem) {
        console.error('No workspace item provided');
        return;
    }
    
    // get workspace ID from the element
    const workspaceID = parseInt(workspaceItem.dataset.workspaceId);
    
    if (!workspaceID) {
        console.error('Invalid workspace ID');
        return;
    }
    
    // save context for later
    inviteContext = {
        type: 'workspace',
        workspaceID: workspaceID,
        taskID: null
    };
    
    // show the invite modal
    showInviteModal();
}

/**
 * handle grant access action for tasks
 * shows the invite modal for task access
 */
function handleGrantAccess(taskItem) {
    if (!taskItem) {
        console.error('No task item provided');
        return;
    }
    
    // get task ID and workspace ID from the element
    const taskID = parseInt(taskItem.dataset.taskId);
    const workspaceItem = taskItem.closest('.workspace-item');
    const workspaceID = workspaceItem ? parseInt(workspaceItem.dataset.workspaceId) : null;
    
    if (!taskID || !workspaceID) {
        console.error('Invalid task or workspace ID');
        return;
    }
    
    // save context for later
    inviteContext = {
        type: 'task',
        workspaceID: workspaceID,
        taskID: taskID
    };
    
    // show the invite modal
    showInviteModal();
}

/**
 * create and show the invite modal
 */
function showInviteModal() {
    // remove any existing modal first
    removeInviteModal();
    
    // create the modal HTML
    const modal = document.createElement('div');
    modal.className = 'invite-modal-overlay';
    modal.innerHTML = `
        <div class="invite-modal">
            <div class="invite-search-container">
                <input 
                    type="text" 
                    class="invite-search-input" 
                    placeholder="Search person"
                    id="inviteSearchInput"
                />
            </div>
            <div class="invite-results" id="inviteResults">
                <!-- search results will appear here -->
            </div>
        </div>
    `;
    
    // add to page
    document.body.appendChild(modal);
    
    // setup event listeners
    setupInviteModalListeners(modal);
    
    // focus on search input
    const searchInput = modal.querySelector('#inviteSearchInput');
    if (searchInput) {
        searchInput.focus();
    }
}

/**
 * setup all event listeners for the invite modal
 */
function setupInviteModalListeners(modal) {
    const searchInput = modal.querySelector('#inviteSearchInput');
    const overlay = modal;
    const modalContent = modal.querySelector('.invite-modal');
    
    // search when user presses Enter
    if (searchInput) {
        searchInput.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                const email = searchInput.value.trim();
                if (email) {
                    searchUsers(email);
                }
            }
        });
    }
    
    // close modal when clicking outside
    overlay.addEventListener('click', (e) => {
        if (e.target === overlay) {
            removeInviteModal();
        }
    });
    
    // prevent clicks inside modal from closing it
    modalContent.addEventListener('click', (e) => {
        e.stopPropagation();
    });
}

/**
 * search for users by email
 */
async function searchUsers(email) {
    const resultsContainer = document.getElementById('inviteResults');
    
    if (!resultsContainer) {
        console.error('Results container not found');
        return;
    }
    
    // show loading state
    resultsContainer.innerHTML = '<div class="invite-loading">Searching...</div>';
    
    try {
        const formData = new FormData();
        formData.append('action', 'search_user');
        formData.append('email', email);
        
        const response = await fetch('navbar_api.php', {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        if (data.success && data.users && data.users.length > 0) {
            // show search results
            displaySearchResults(data.users);
        } else {
            resultsContainer.innerHTML = '<div class="invite-no-results">No users found</div>';
        }
    } catch (error) {
        console.error('Search error:', error);
        resultsContainer.innerHTML = '<div class="invite-error">Search failed</div>';
    }
}

/**
 * display search results in the modal
 */
function displaySearchResults(users) {
    const resultsContainer = document.getElementById('inviteResults');
    
    if (!resultsContainer) {
        return;
    }
    
    // create result items for each user
    resultsContainer.innerHTML = users.map(user => `
        <div class="invite-result-item" data-user-id="${user.UserID}">
            <div class="invite-user-info">
                <div class="invite-user-avatar">
                    ${user.PictureName ? 
                        `<img src="../Assets/ProfilePic/${user.PictureName}" alt="${user.Username}">` : 
                        `<div class="invite-avatar-placeholder">${user.Username.charAt(0).toUpperCase()}</div>`
                    }
                </div>
                <div class="invite-user-details">
                    <div class="invite-user-name">${user.Username}</div>
                    <div class="invite-user-email">@${user.Email}</div>
                </div>
            </div>
            <button class="invite-btn" onclick="inviteUser(${user.UserID}, '${user.Username}')">
                Invite Member
            </button>
        </div>
    `).join('');
}

/**
 * invite a user to workspace or grant task access
 */
async function inviteUser(userID, username) {
    if (!inviteContext.type) {
        console.error('No invite context set');
        return;
    }
    
    try {
        const formData = new FormData();
        
        if (inviteContext.type === 'workspace') {
            formData.append('action', 'invite_to_workspace');
            formData.append('workspace_id', inviteContext.workspaceID);
            formData.append('invited_user_id', userID);
        } else if (inviteContext.type === 'task') {
            formData.append('action', 'invite_to_task');
            formData.append('task_id', inviteContext.taskID);
            formData.append('workspace_id', inviteContext.workspaceID);
            formData.append('invited_user_id', userID);
        }
        
        const response = await fetch('navbar_api.php', {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
            alert(`${username} has been invited successfully!`);
            removeInviteModal();
            
            // reload workspaces to show updated member list
            if (window.loadWorkspaces) {
                window.loadWorkspaces();
            }
        } else {
            alert(data.message || 'Failed to invite member');
        }
    } catch (error) {
        console.error('Invite error:', error);
        alert('Failed to invite member');
    }
}

/**
 * remove the invite modal from page
 */
function removeInviteModal() {
    const modal = document.querySelector('.invite-modal-overlay');
    if (modal) {
        modal.remove();
    }
    
    // reset context
    inviteContext = {
        type: null,
        workspaceID: null,
        taskID: null
    };
}

// make functions available globally
window.handleInviteMember = handleInviteMember;
window.handleGrantAccess = handleGrantAccess;
window.inviteUser = inviteUser;
window.removeInviteModal = removeInviteModal;

