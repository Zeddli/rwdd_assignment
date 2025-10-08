/**
 * workspace management system
 * handles creating new workspaces, adding tasks and managing workspace actions
 * interacts with the database via AJAX calls to the navbar_api.php endpoint
 */

/**
 * create a brand new workspace for the user
 */
function addNewWorkspace() {
    console.log('Creating new workspace...');
    
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
                        <div class="goal-item" data-goal-id="${data.goalID}">
                            <img src="../navbar-icon/goal.svg" alt="Goal" class="submenu-icon" width="16" height="16">
                            <span class="goal-name">${data.goalName}</span>
                            <div class="dropdown">
                                <button class="dropdown-toggle" aria-label="Goal options">
                                    <svg width="16" height="16" viewBox="0 0 16 16">
                                        <circle cx="8" cy="4" r="1" fill="currentColor"/>
                                        <circle cx="8" cy="8" r="1" fill="currentColor"/>
                                        <circle cx="8" cy="12" r="1" fill="currentColor"/>
                                    </svg>
                                </button>
                                <div class="dropdown-menu">
                                    <button class="dropdown-item" data-action="rename">Rename</button>
                                    <button class="dropdown-item" data-action="view-details">View Details</button>
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
    window.isCreatingTask = true;
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
    console.log('Creating new task for workspace:', workspaceID);
    
    // create task via API
    fetch('../Navbar/navbar_api.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `action=create_task&workspace_id=${workspaceID}&task_name=New Task`
    })
    .then(response => {
        // check if response is actually JSON
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        // check if response is JSON by looking at content type
        const contentType = response.headers.get('content-type');
        if (!contentType || !contentType.includes('application/json')) {
            // if it's not JSON, get the text to see what went wrong
            return response.text().then(text => {
                console.error('Server returned non-JSON response:', text);
                throw new Error('Server returned HTML instead of JSON. Check console for details.');
            });
        }
        
        return response.json();
    })
    .then(data => {
        if (data.success) {
            console.log('Task created successfully:', data);
            
            // create task HTML with database ID
            const taskHTML = `
                <div class="task-item" data-task-id="${data.taskID}" data-pinned="false">
                    <img src="../navbar-icon/task.svg" alt="Task" class="submenu-icon" width="16" height="16">
                    <span class="task-name">${data.taskName}</span>
                    <div class="dropdown">
                        <button class="dropdown-toggle" aria-label="Task options">
                            <svg width="16" height="16" viewBox="0 0 16 16">
                                <circle cx="8" cy="4" r="1" fill="currentColor"/>
                                <circle cx="8" cy="8" r="1" fill="currentColor"/>
                                <circle cx="8" cy="12" r="1" fill="currentColor"/>
                            </svg>
                        </button>
                        <div class="dropdown-menu">
                            <button class="dropdown-item" data-action="grant-access">Grant access</button>
                            <button class="dropdown-item" data-action="rename">Rename</button>
                            <button class="dropdown-item" data-action="pin" data-pin-text="Pin">Pin</button>
                            <button class="dropdown-item" data-action="delete">Delete</button>
                        </div>
                    </div>
                </div>
            `;
            
            const submenu = workspaceItem.querySelector('.workspace-submenu');
            submenu.insertAdjacentHTML('beforeend', taskHTML);
            
            // get the new task element and initialize dropdown
            const newTask = document.querySelector(`[data-task-id="${data.taskID}"]`);
            const dropdown = newTask.querySelector('.dropdown');
            
            // init dropdown functionality only for the new task
            if (typeof window.initializeSingleDropdown === 'function') {
                window.initializeSingleDropdown(dropdown);
            } else {
                console.warn('initializeSingleDropdown not available, using fallback');
                // init dropdown functionality only for the new task
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
            
            console.log(`New task added with ID: ${data.taskID}`);
        } else {
            console.error('Failed to create task:', data.message);
            alert('Failed to create task: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error creating task:', error);
        alert('Error creating task. Please try again.');
    })
    .finally(() => {
        // Reset the flag when done (success or error)
        workspaceItem.dataset.addingTask = 'false';
    });
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
function handleWorkspaceClick(event, workspaceItem) {
    if (!workspaceItem) return;
    
    // Ignore clicks coming from dropdowns or buttons within the header
    if (event.target.closest('.dropdown') || event.target.closest('button')) return;
    
    const workspaceID = workspaceItem.dataset.workspaceId;
    if (!workspaceID) {
        console.error('Workspace ID not found');
        return;
    }
    
    window.location.href = `../WorkspacePage/workspace.php/${workspaceID}`;
}

/**
 * delete an entire workspace (dangerous!)
 * called when user clicks "Delete" from workspace dropdown
 * this action can't be undone
 */
// handleDeleteWorkSpace function removed - to be reimplemented


// export these functions so other js files can use them
// makes them available globally via the window object
window.addNewWorkspace = addNewWorkspace;
window.handleAddTask = handleAddTask;
window.handleHideUnhide = handleHideUnhide;
window.handleWorkspaceClick = handleWorkspaceClick;
// handleDeleteWorkSpace export removed
