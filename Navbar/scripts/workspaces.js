/**
 * workspace management system
 * handles creating new workspaces, adding tasks and managing workspace actions
 * interacts with the database via AJAX calls to the navbar_api.php endpoint
 */

/**
 * create a brand new workspace for the user
 */
function addNewWorkspace() {
    
    // create workspace via API
        fetch('../Navbar/navbar_api.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'action=create_workspace&workspace_name=New Workspace'
    })
    .then(response => {
        // check if response is actually JSON
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        // check if response is JSON by looking at content type
        const contentType = response.headers.get('content-type');
        if (!contentType || !contentType.includes('application/json')) {
            // if it's not JSON, get the infp
            return response.text().then(text => {
                console.error('Server returned non-JSON response:', text);
                throw new Error('Server returned HTML instead of JSON. Check console for details.');
            });
        }
        
        return response.json();
    })
    .then(data => {
        if (data.success) {
            console.log('Workspace created successfully:', data);
            
            // remove "no workspace", if iworlspacet exists
            const noWorkspaceMsg = document.querySelector('.no-workspace-message');
            if (noWorkspaceMsg) {
                noWorkspaceMsg.remove();
            }
            
            // Create workspace HTML with database ID
            const workspaceHTML = `
                <div class="workspace-item" data-workspace-id="${data.workspaceID}">
                    <div class="workspace-header-item">
                        <img src="../navbar-icon/workspace.svg" alt="Workspace" class="workspace-icon" width="18" height="18">
                        <span class="workspace-name">${data.workspaceName}</span>
                        <div class="workspace-actions">
                            <button class="add-task-btn" aria-label="Add new task">
                                <svg width="16" height="16" viewBox="0 0 16 16">
                                    <line x1="8" y1="2" x2="8" y2="14" stroke="currentColor" stroke-width="2"/>
                                    <line x1="2" y1="8" x2="14" y2="8" stroke="currentColor" stroke-width="2"/>
                                </svg>
                            </button>
                            <div class="dropdown">
                                <button class="dropdown-toggle" aria-label="Workspace options">
                                    <svg width="16" height="16" viewBox="0 0 16 16">
                                        <circle cx="8" cy="4" r="1" fill="currentColor"/>
                                        <circle cx="8" cy="8" r="1" fill="currentColor"/>
                                        <circle cx="8" cy="12" r="1" fill="currentColor"/>
                                    </svg>
                                </button>
                                <div class="dropdown-menu">
                                    <button class="dropdown-item" data-action="invite">Invite member</button>
                                    <button class="dropdown-item" data-action="add-task">Add task</button>
                                    <button class="dropdown-item" data-action="rename">Rename</button>
                                    <button class="dropdown-item" data-action="delete">Delete</button>
                                    <button class="dropdown-item" data-action="hide">Hide</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="workspace-submenu" data-visible="true">
                        <div class="goal-item" data-goal-id="0">
                            <img src="../navbar-icon/goal.svg" alt="Goal" class="submenu-icon" width="16" height="16">
                            <span class="goal-name">Goals</span>
                            <div class="dropdown">
                                <button class="dropdown-toggle" aria-label="Goal options">
                                    <svg width="16" height="16" viewBox="0 0 16 16">
                                        <circle cx="8" cy="4" r="1" fill="currentColor"/>
                                        <circle cx="8" cy="8" r="1" fill="currentColor"/>
                                        <circle cx="8" cy="12" r="1" fill="currentColor"/>
                                    </svg>
                                </button>
                                <div class="dropdown-menu">
                                    <button class="dropdown-item" data-action="view-goals">View Goals</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            
            DOM.workspacesContainer.insertAdjacentHTML('beforeend', workspaceHTML);
            
            // init dropdown functionality only for the new workspace
            const newWorkspace = document.querySelector(`[data-workspace-id="${data.workspaceID}"]`);
            const dropdown = newWorkspace.querySelector('.dropdown');
            
            // init dropdown functionality only for the new workspace
            if (typeof window.initializeSingleDropdown === 'function') {
                window.initializeSingleDropdown(dropdown);
            } else {
                console.warn('initializeSingleDropdown not available, using fallback');
                // init dropdown functionality only for the new workspace
                const toggle = dropdown.querySelector('.dropdown-toggle');
                toggle.addEventListener('click', (e) => {
                    e.stopPropagation();
                    window.toggleDropdown(dropdown);
                });
                
                const items = dropdown.querySelectorAll('.dropdown-item');
                items.forEach(item => {
                    item.addEventListener('click', (e) => {
                        e.stopPropagation();
                        window.handleDropdownAction(item, dropdown);
                    });
                });
            }
            
            console.log(`New workspace added with ID: ${data.workspaceID}`);
            window.location.reload();
        } else {
            console.error('Failed to create workspace:', data.message);
            alert('Failed to create workspace: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error creating workspace:', error);
        alert('Error creating workspace. Please try again.');
    });
}

// debugging
if(window.isCreatingTask === undefined){
    window.isCreatingTask = false;
}
    

/**
 * Temporary fallback function for showing task detail window
 * This will be replaced once the taskDetailWindow.js loads properly
 */
function showTaskDetailWindowFallback(workspaceId) {
    console.log('Using fallback showTaskDetailWindow function');
    
    // Show the modal directly
    const modal = document.getElementById('taskDetailModal');
    if (modal) {
        modal.style.display = 'flex';
        console.log('Task detail modal displayed');
        
        // Focus on task name input
        const taskNameInput = document.getElementById('taskNameInput');
        if (taskNameInput) {
            taskNameInput.focus();
        }
        
        // Set the workspace ID for form submission
        window.currentWorkspaceId = workspaceId;
        
        // Add basic form handling
        const form = document.getElementById('taskDetailForm');
        if (form && !form.dataset.handlerAdded) {
            form.addEventListener('submit', handleTaskFormSubmit);
            form.dataset.handlerAdded = 'true';
        }
        
        // Add close button functionality
        const closeBtn = document.getElementById('closeTaskDetailModal');
        if (closeBtn && !closeBtn.dataset.handlerAdded) {
            closeBtn.addEventListener('click', () => {
                modal.style.display = 'none';
            });
            closeBtn.dataset.handlerAdded = 'true';
        }
        
        // Add cancel button functionality
        const cancelBtn = document.getElementById('cancelTaskBtn');
        if (cancelBtn && !cancelBtn.dataset.handlerAdded) {
            cancelBtn.addEventListener('click', () => {
                modal.style.display = 'none';
            });
            cancelBtn.dataset.handlerAdded = 'true';
        }
    } else {
        console.error('taskDetailModal element not found');
        alert('Task detail window not found. Please refresh the page.');
    }
}

/**
 * Handle task form submission (fallback)
 */
async function handleTaskFormSubmit(event) {
    event.preventDefault();
    if (window.isCreatingTask) {
        console.warn('Duplicate submit prevented');
        return;
    }
    window.isCreatingTask = true;
    
    const taskName = document.getElementById('taskNameInput').value.trim();
    const description = document.getElementById('taskDescriptionInput').value.trim();
    const startDate = document.getElementById('startDateInput').value;
    const deadline = document.getElementById('deadlineInput').value;
    const priority = document.getElementById('prioritySelect').value;
    const status = document.getElementById('statusSelect').value;
    
    // Basic validation
    if (!taskName) {
        alert('Task name is required');
        return;
    }
    
    if (!startDate) {
        alert('Start date is required');
        return;
    }
    
    if (!deadline) {
        alert('Deadline is required');
        return;
    }
    
    // Validate that deadline is after start date
    if (startDate && deadline && deadline <= startDate) {
        alert('Deadline must be after the start date');
        return;
    }
    
    if (!window.currentWorkspaceId) {
        alert('No workspace selected');
        return;
    }
    
    try {
        const formData = new FormData();
        formData.append('action', 'create_task');
        formData.append('workspace_id', window.currentWorkspaceId);
        formData.append('task_name', taskName);
        formData.append('task_description', description);
        formData.append('start_date', startDate);
        formData.append('deadline', deadline);
        formData.append('priority', priority);
        formData.append('status', status);
        
        // Debug: Log the form data being sent
        console.log('Form data being sent:');
        console.log('Task Name:', taskName);
        console.log('Description:', description);
        console.log('Start Date:', startDate);
        console.log('Deadline:', deadline);
        console.log('Priority:', priority);
        console.log('Status:', status);
        
        const response = await fetch('../Navbar/navbar_api.php', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            
            // Hide modal
            const modal = document.getElementById('taskDetailModal');
            if (modal) {
                modal.style.display = 'none';
            }
            
            // Refresh page to show new task
            window.location.reload();
        } else {
            alert('Failed to create task: ' + (result.message || 'Unknown error'));
        }
    } catch (error) {
        console.error('Error creating task:', error);
        alert('Error creating task. Please try again.');
    } finally {
        window.isCreatingTask = false;
    }
}

/**
 * Add a new task to an existing workspace
 * gets called when user clicks the "+" button next to a workspace name
 * It creates the task in the database, then adds the task HTML under the workspace
 */
function handleAddTask(workspaceItem) {
    if (!workspaceItem) return;

    // Prevent duplicate calls using global flag
    if (workspaceItem.dataset.addingTask === 'true') {
        console.log('🔥 handleAddTask already in progress, skipping duplicate call');
        return;
    }
    
    // Mark as processing to prevent duplicates
    workspaceItem.dataset.addingTask = 'true';

    //debugging
    const callId = Math.random().toString(36).substr(2, 9);
    console.log(`🔥 handleAddTask called [${callId}] for workspace:`, workspaceItem.dataset.workspaceId);
    
    const workspaceID = workspaceItem.dataset.workspaceId;
    console.log('Opening task detail window for workspace:', workspaceID);
    
    // Show task detail window
    console.log('Checking for showTaskDetailWindow function...');
    console.log('window.showTaskDetailWindow:', window.showTaskDetailWindow);
    console.log('typeof window.showTaskDetailWindow:', typeof window.showTaskDetailWindow);
    
    if (typeof window.showTaskDetailWindow === 'function') {
        console.log('Calling showTaskDetailWindow with workspaceID:', workspaceID);
        window.showTaskDetailWindow(workspaceID);
    } else {
        console.error('showTaskDetailWindow function not available, using fallback');
        console.log('Available window properties:', Object.keys(window).filter(key => key.includes('Task')));
        
        // Use fallback function
        showTaskDetailWindowFallback(workspaceID);
    }
    
    // Reset the flag
    workspaceItem.dataset.addingTask = 'false';
}


/**
 * toggle workspace submenu visibility (show/hide tasks and goals)
 * called when user clicks "Hide" or "Unhide" from workspace dropdown   
 */
function handleHideUnhide(workspaceItem) {
    if (!workspaceItem) return;
    
    const submenu = workspaceItem.querySelector('.workspace-submenu');
    const isVisible = submenu.dataset.visible === 'true';
    
    submenu.dataset.visible = !isVisible;
    
    // update dropdown button text
    const dropdown = workspaceItem.querySelector('.dropdown');
    const hideButton = dropdown.querySelector('[data-action="hide"]');
    hideButton.textContent = isVisible ? 'Unhide' : 'Hide';
    
    console.log(`Workspace submenu ${isVisible ? 'hidden' : 'shown'}`);
}


/**
 * Handle workspace click to open workspace page
 * Redirects to ../WorkspacePage/workspace.php/{workspaceID}
 */
// ...existing code...
function handleWorkspaceClick(event, workspaceItem) {
    if (!workspaceItem) return;
    
    // Ignore clicks coming from dropdowns or buttons within the header
    if (event.target.closest('.dropdown') || event.target.closest('button')) return;
    
    const workspaceID = workspaceItem.dataset.workspaceId;
    if (!workspaceID) {
        console.error('Workspace ID not found');
        return;
    }

    const formData = new FormData();
    // backend expects 'set_workspace_session'
    formData.append('action', 'set_workspace_session');
    formData.append('workspace_id', workspaceID);

    fetch('../Navbar/navbar_api.php', {
        method: 'POST',
        body: formData,
        credentials: 'same-origin'
    })
    .then(async response => {
        // if server returned non-OK, capture body for debugging
        const text = await response.text();
        const ct = response.headers.get('content-type') || '';
        // try to parse JSON if possible
        if (ct.includes('application/json')) {
            try {
                return JSON.parse(text);
            } catch (e) {
                console.error('Failed to parse JSON from response:', text);
                throw new Error('Invalid JSON from server');
            }
        }
        // not JSON — surface the body for debugging
        console.error('Non-JSON response from server:', text);
        throw new Error('Server returned non-JSON response');
    })
    .then(data => {
        console.log('set_workspace_session response:', data);
        if (data && data.success) {
            window.location.href = `../WorkspacePage/Workspace.php`;
        } else {
            const msg = (data && data.message) ? data.message : 'Unknown error';
            console.error('Failed to set workspace session:', data);
            alert('Failed to open workspace: ' + msg);
        }
    })
    .catch(error => {
        console.error('Error setting workspace:', error);
        alert('Error setting workspace. Check console and network tab for details.');
    });
}
// ...existing code...

/**
 * delete an entire workspace (dangerous!)
 * called when user clicks "Delete" from workspace dropdown
 * this action can't be undone
 */
// handleDeleteWorkSpace function removed - to be reimplemented

/**
 * Temporary fallback function for showing grant access window
 * This will be replaced once the grantAccessWindow.js loads properly
 */
function showGrantAccessWindowFallback(workspaceId, taskId = null) {
    console.log('Using fallback showGrantAccessWindow function');
    console.log('Workspace ID:', workspaceId, 'Task ID:', taskId);
    
    // Show the modal directly
    const modal = document.getElementById('grantAccessModal');
    if (modal) {
        modal.style.display = 'flex';
        console.log('Grant access modal displayed');
        
        // Focus on search input
        const searchInput = document.getElementById('memberSearchInput');
        if (searchInput) {
            searchInput.focus();
        }
        
        // Set the workspace and task IDs for later use
        window.currentGrantAccessWorkspaceId = workspaceId;
        window.currentGrantAccessTaskId = taskId;
        
        // Load workspace members
        loadWorkspaceMembersFallback(workspaceId);
        
        // Add event listeners if not already added
        addGrantAccessEventListeners();
    } else {
        console.error('grantAccessModal element not found');
        alert('Grant access window not found. Please refresh the page.');
    }
}

/**
 * Load workspace members (fallback function)
 */
async function loadWorkspaceMembersFallback(workspaceId) {
    try {
        const memberList = document.getElementById('memberList');
        if (memberList) {
            memberList.innerHTML = '<div class="loading-message">Loading members...</div>';
        }
        
        const formData = new FormData();
        formData.append('action', 'get_workspace_members');
        formData.append('workspace_id', workspaceId);
        
        console.log('Loading members for workspace:', workspaceId);
        
        const response = await fetch('../Navbar/navbar_api.php', {
            method: 'POST',
            body: formData
        });
        
        console.log('Response status:', response.status);
        console.log('Response headers:', response.headers);
        
        // Check if response is ok
        if (!response.ok) {
            const errorText = await response.text();
            console.error('HTTP Error loading workspace members:', response.status, errorText);
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        // Check if response is JSON
        const contentType = response.headers.get('content-type');
        if (!contentType || !contentType.includes('application/json')) {
            const errorText = await response.text();
            console.error('Server returned non-JSON response:', errorText);
            throw new Error('Server returned HTML instead of JSON. Check console for details.');
        }
        
        const result = await response.json();
        console.log('Members loaded successfully:', result);
        
        if (result.success) {
            displayMembersFallback(result.members || []);
        } else {
            showGrantAccessMessageFallback(result.message || 'Failed to load members', 'error');
            if (memberList) {
                memberList.innerHTML = '<div class="no-members-message">No members found</div>';
            }
        }
    } catch (error) {
        console.error('Error loading workspace members:', error);
        showGrantAccessMessageFallback('Error loading members. Please try again.', 'error');
        const memberList = document.getElementById('memberList');
        if (memberList) {
            memberList.innerHTML = '<div class="no-members-message">Error loading members</div>';
        }
    }
}

/**
 * Display members in the list (fallback function)
 */
function displayMembersFallback(members) {
    const memberList = document.getElementById('memberList');
    if (!memberList) return;
    
    if (members.length === 0) {
        memberList.innerHTML = '<div class="no-members-message">No members found</div>';
        return;
    }
    
    memberList.innerHTML = '';
    
    members.forEach(member => {
        const memberItem = createMemberItemFallback(member);
        memberList.appendChild(memberItem);
    });
}

/**
 * Create a member item element (fallback function)
 */
function createMemberItemFallback(member) {
    const memberItem = document.createElement('div');
    memberItem.className = 'member-item';
    memberItem.dataset.userId = member.UserID;
    
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
        <button class="collaborate-btn" onclick="handleCollaborateClickFallback(${member.UserID})">
            Collaborate
        </button>
    `;
    
    return memberItem;
}

/**
 * Handle collaborate button click (fallback function)
 */
async function handleCollaborateClickFallback(userId) {
    if (!window.currentGrantAccessWorkspaceId || !userId) {
        showGrantAccessMessageFallback('Missing required information', 'error');
        return;
    }
    
    try {
        showGrantAccessMessageFallback('Granting access...', 'info');
        
        const formData = new FormData();
        formData.append('action', 'invite_to_task');
        formData.append('task_id', window.currentGrantAccessTaskId || 0);
        formData.append('workspace_id', window.currentGrantAccessWorkspaceId);
        formData.append('invited_user_id', userId);
        
        console.log('Granting access - TaskID:', window.currentGrantAccessTaskId, 'WorkspaceID:', window.currentGrantAccessWorkspaceId, 'UserID:', userId);
        
        const response = await fetch('../Navbar/navbar_api.php', {
            method: 'POST',
            body: formData
        });
        
        console.log('Grant access response status:', response.status);
        console.log('Grant access response headers:', response.headers);
        
        // Check if response is ok
        if (!response.ok) {
            const errorText = await response.text();
            console.error('HTTP Error granting access:', response.status, errorText);
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        // Check if response is JSON
        const contentType = response.headers.get('content-type');
        if (!contentType || !contentType.includes('application/json')) {
            const errorText = await response.text();
            console.error('Server returned non-JSON response when granting access:', errorText);
            throw new Error('Server returned HTML instead of JSON. Check console for details.');
        }
        
        const result = await response.json();
        console.log('Grant access result:', result);
        
        if (result.success) {
            showGrantAccessMessageFallback('Access granted successfully!', 'success');
            
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
        } else {
            showGrantAccessMessageFallback(result.message || 'Failed to grant access', 'error');
        }
    } catch (error) {
        console.error('Error granting access:', error);
        showGrantAccessMessageFallback('Error granting access. Please try again.', 'error');
    }
}

/**
 * Show message to user if fallback happens
 */
function showGrantAccessMessageFallback(message, type) {
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
 * Escape HTML to prevent fallback
 */
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

/**
 * Add event listeners for grant access modal (fallback function)
 */
function addGrantAccessEventListeners() {
    // Close modal button
    const closeBtn = document.getElementById('closeGrantAccessModal');
    if (closeBtn && !closeBtn.dataset.handlerAdded) {
        closeBtn.addEventListener('click', () => {
            const modal = document.getElementById('grantAccessModal');
            if (modal) {
                modal.style.display = 'none';
            }
        });
        closeBtn.dataset.handlerAdded = 'true';
    }
    
    // Search input
    const searchInput = document.getElementById('memberSearchInput');
    if (searchInput && !searchInput.dataset.handlerAdded) {
        searchInput.addEventListener('input', (e) => {
            filterMembersFallback(e.target.value);
        });
        searchInput.dataset.handlerAdded = 'true';
    }
}

/**
 * Filter members based on search input (fallback function)
 */
function filterMembersFallback(searchTerm) {
    const memberItems = document.querySelectorAll('.member-item');
    
    memberItems.forEach(item => {
        const name = item.querySelector('.member-name').textContent.toLowerCase();
        const email = item.querySelector('.member-email').textContent.toLowerCase();
        const term = searchTerm.toLowerCase();
        
        if (name.includes(term) || email.includes(term)) {
            item.style.display = 'flex';
        } else {
            item.style.display = 'none';
        }
    });
}

/**
 * Handle grant access action for tasks
 */
function handleGrantAccess(taskItem) {
    if (!taskItem) {
        console.error('No task item provided');
        return;
    }
    
    const taskID = taskItem.dataset.taskId;
    const workspaceItem = taskItem.closest('.workspace-item');
    const workspaceID = workspaceItem ? workspaceItem.dataset.workspaceId : null;
    
    if (!workspaceID) {
        console.error('No workspace ID found');
        alert('Error: Could not find workspace ID');
        return;
    }
    
    console.log('Granting access for task:', taskID, 'in workspace:', workspaceID);
    
    // Show grant access window
    if (typeof window.showGrantAccessWindow === 'function') {
        window.showGrantAccessWindow(workspaceID, taskID);
    } else {
        console.error('showGrantAccessWindow function not available, using fallback');
        // Use fallback function
        showGrantAccessWindowFallback(workspaceID, taskID);
    }
}

// export these functions so other js files can use them
// makes them available globally via the window object
window.addNewWorkspace = addNewWorkspace;
window.handleAddTask = handleAddTask;
window.handleHideUnhide = handleHideUnhide;
window.handleWorkspaceClick = handleWorkspaceClick;
window.handleGrantAccess = handleGrantAccess;
window.handleCollaborateClickFallback = handleCollaborateClickFallback;
// handleDeleteWorkSpace export removed
