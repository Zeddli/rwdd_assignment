/**
 * Grant Access Window Functionality
 * Handles selecting workspace members to collaborate on tasks
 */

let currentGrantAccessWorkspaceId = null;
let currentGrantAccessTaskId = null;
let workspaceMembers = [];
let filteredMembers = [];

/**
 * Show grant access window
 */
function showGrantAccessWindow(workspaceId, taskId = null) {
    currentGrantAccessWorkspaceId = workspaceId;
    currentGrantAccessTaskId = taskId;
    
    // Clear search
    const searchInput = document.getElementById('memberSearchInput');
    if (searchInput) {
        searchInput.value = '';
    }
    
    // Show modal
    const modal = document.getElementById('grantAccessModal');
    if (modal) {
        modal.style.display = 'flex';
        // Focus on search input
        if (searchInput) {
            searchInput.focus();
        }
    }
    
    // Load workspace members
    loadWorkspaceMembers(workspaceId);
}

/**
 * Hide grant access window
 */
function hideGrantAccessWindow() {
    const modal = document.getElementById('grantAccessModal');
    if (modal) {
        modal.style.display = 'none';
    }
    
    // Clear data
    currentGrantAccessWorkspaceId = null;
    currentGrantAccessTaskId = null;
    workspaceMembers = [];
    filteredMembers = [];
}

/**
 * Load workspace members
 */
async function loadWorkspaceMembers(workspaceId) {
    try {
        const memberList = document.getElementById('memberList');
        if (memberList) {
            memberList.innerHTML = '<div class="loading-message">Loading members...</div>';
        }
        
        const formData = new FormData();
        formData.append('action', 'get_workspace_members');
        formData.append('workspace_id', workspaceId);
        
        const response = await fetch('../Navbar/navbar_api.php', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            workspaceMembers = result.members || [];
            filteredMembers = [...workspaceMembers];
            displayMembers();
        } else {
            showGrantAccessMessage(result.message || 'Failed to load members', 'error');
            if (memberList) {
                memberList.innerHTML = '<div class="no-members-message">No members found</div>';
            }
        }
    } catch (error) {
        console.error('Error loading workspace members:', error);
        showGrantAccessMessage('Error loading members. Please try again.', 'error');
        const memberList = document.getElementById('memberList');
        if (memberList) {
            memberList.innerHTML = '<div class="no-members-message">Error loading members</div>';
        }
    }
}

/**
 * Display members in the list
 */
function displayMembers() {
    const memberList = document.getElementById('memberList');
    if (!memberList) return;
    
    if (filteredMembers.length === 0) {
        memberList.innerHTML = '<div class="no-members-message">No members found</div>';
        return;
    }
    
    memberList.innerHTML = '';
    
    filteredMembers.forEach(member => {
        const memberItem = createMemberItem(member);
        memberList.appendChild(memberItem);
    });
}

/**
 * Create a member item element
 */
function createMemberItem(member) {
    const memberItem = document.createElement('div');
    memberItem.className = 'member-item';
    memberItem.dataset.userId = member.UserID;
    
    // Check if user already has access to the task
    const hasAccess = member.hasTaskAccess || false;
    
    memberItem.innerHTML = `
        <div class="member-avatar">
            <svg viewBox="0 0 24 24">
                <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
            </svg>
        </div>
        <div class="member-info">
            <div class="member-name">${escapeHtml(member.UserName || member.Email || 'Unknown User')}</div>
            <div class="member-email">${escapeHtml(member.Email || 'No email')}</div>
        </div>
        <button class="collaborate-btn ${hasAccess ? 'granted' : ''}" 
                ${hasAccess ? 'disabled' : ''}
                onclick="handleCollaborateClick(${member.UserID})">
            ${hasAccess ? 'Granted' : 'Collaborate'}
        </button>
    `;
    
    return memberItem;
}

/**
 * Handle collaborate button click
 */
async function handleCollaborateClick(userId) {
    if (!currentGrantAccessWorkspaceId || !userId) {
        showGrantAccessMessage('Missing required information', 'error');
        return;
    }
    
    try {
        showGrantAccessMessage('Granting access...', 'info');
        
        const formData = new FormData();
        formData.append('action', 'invite_to_task');
        formData.append('task_id', currentGrantAccessTaskId || 0);
        formData.append('workspace_id', currentGrantAccessWorkspaceId);
        formData.append('invited_user_id', userId);
        
        const response = await fetch('../Navbar/navbar_api.php', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            showGrantAccessMessage('Access granted successfully!', 'success');
            
            // Update the button state
            const memberItem = document.querySelector(`[data-user-id="${userId}"]`);
            if (memberItem) {
                const button = memberItem.querySelector('.collaborate-btn');
                if (button) {
                    button.textContent = 'Granted';
                    button.className = 'collaborate-btn granted';
                    button.disabled = true;
                }
            }
            
            // Update the member data
            const member = workspaceMembers.find(m => m.UserID == userId);
            if (member) {
                member.hasTaskAccess = true;
            }
            
        } else {
            showGrantAccessMessage(result.message || 'Failed to grant access', 'error');
        }
    } catch (error) {
        console.error('Error granting access:', error);
        showGrantAccessMessage('Error granting access. Please try again.', 'error');
    }
}

/**
 * Filter members based on search input
 */
function filterMembers(searchTerm) {
    if (!searchTerm.trim()) {
        filteredMembers = [...workspaceMembers];
    } else {
        const term = searchTerm.toLowerCase();
        filteredMembers = workspaceMembers.filter(member => {
            const name = (member.UserName || '').toLowerCase();
            const email = (member.Email || '').toLowerCase();
            return name.includes(term) || email.includes(term);
        });
    }
    displayMembers();
}

/**
 * Show message to user
 */
function showGrantAccessMessage(message, type) {
    const messageDiv = document.getElementById('grantAccessMessage');
    if (messageDiv) {
        messageDiv.textContent = message;
        messageDiv.className = `grant-access-message ${type}`;
        messageDiv.style.display = 'block';
        
        // Hide message after 3 seconds
        setTimeout(() => {
            messageDiv.style.display = 'none';
        }, 3000);
    }
}

/**
 * Escape HTML to prevent XSS
 */
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

/**
 * Initialize grant access window functionality
 */
function initializeGrantAccessWindow() {
    // Close modal button
    const closeBtn = document.getElementById('closeGrantAccessModal');
    if (closeBtn) {
        closeBtn.addEventListener('click', hideGrantAccessWindow);
    }
    
    // Search input
    const searchInput = document.getElementById('memberSearchInput');
    if (searchInput) {
        searchInput.addEventListener('input', (e) => {
            filterMembers(e.target.value);
        });
    }
    
    // Close modal when clicking outside
    const modal = document.getElementById('grantAccessModal');
    if (modal) {
        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                hideGrantAccessWindow();
            }
        });
    }
}

// Export functions for use in other files
window.showGrantAccessWindow = showGrantAccessWindow;
window.hideGrantAccessWindow = hideGrantAccessWindow;
window.handleCollaborateClick = handleCollaborateClick;
window.initializeGrantAccessWindow = initializeGrantAccessWindow;
