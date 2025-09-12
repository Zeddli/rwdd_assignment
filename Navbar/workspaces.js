/**
 * Workspace Management System
 * This handles creating new workspaces, adding tasks to them, and managing workspace actions
 * All operations talk to the database via AJAX calls to the navbar_api.php endpoint
 */

/**
 * Create a brand new workspace for the user
 * This sends a request to the server to create a workspace in the database,
 * then adds the new workspace HTML to the sidebar so user can see it immediately
 */
function addNewWorkspace() {
    console.log('Creating new workspace...');
    
    // Create workspace via API
    fetch('/protask/Navbar/navbar_api.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'action=create_workspace&workspace_name=New Workspace'
    })
    .then(response => {
        // Better error handling - check if response is actually JSON
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        // Check if response is JSON by looking at content type
        const contentType = response.headers.get('content-type');
        if (!contentType || !contentType.includes('application/json')) {
            // If it's not JSON, get the text to see what went wrong
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
            
            // Remove "no workspace" message if it exists
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
            
            // Initialize dropdown functionality only for the new workspace
            const newWorkspace = document.querySelector(`[data-workspace-id="${data.workspaceID}"]`);
            const dropdown = newWorkspace.querySelector('.dropdown');
            
            // Check if initializeSingleDropdown is available, otherwise use fallback
            if (typeof window.initializeSingleDropdown === 'function') {
                window.initializeSingleDropdown(dropdown);
            } else {
                console.warn('initializeSingleDropdown not available, using fallback');
                // Fallback: manually initialize just this dropdown
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

/**
 * Add a new task to an existing workspace
 * This gets called when user clicks the "+" button next to a workspace name
 * It creates the task in the database, then adds the task HTML under the workspace
 */
function handleAddTask(workspaceItem) {
    if (!workspaceItem) return;
    
    const workspaceID = workspaceItem.dataset.workspaceId;
    console.log('Creating new task for workspace:', workspaceID);
    
    // Create task via API
    fetch('/protask/Navbar/navbar_api.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `action=create_task&workspace_id=${workspaceID}&task_name=New Task`
    })
    .then(response => {
        // Better error handling - check if response is actually JSON
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        // Check if response is JSON by looking at content type
        const contentType = response.headers.get('content-type');
        if (!contentType || !contentType.includes('application/json')) {
            // If it's not JSON, get the text to see what went wrong
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
            
            // Create task HTML with database ID
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
            
            // Get the new task element and initialize dropdown
            const newTask = document.querySelector(`[data-task-id="${data.taskID}"]`);
            const dropdown = newTask.querySelector('.dropdown');
            
            // Check if initializeSingleDropdown is available, otherwise use fallback
            if (typeof window.initializeSingleDropdown === 'function') {
                window.initializeSingleDropdown(dropdown);
            } else {
                console.warn('initializeSingleDropdown not available, using fallback');
                // Fallback: manually initialize just this dropdown
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
    });
}

/**
 * Toggle workspace submenu visibility (show/hide tasks and goals)
 * This is called when user clicks "Hide" or "Unhide" from workspace dropdown
 * It just toggles the CSS visibility - doesn't affect the database
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
 * Delete an entire workspace (dangerous!)
 * This removes the workspace from the database along with all its tasks and goals
 * Shows a confirmation dialog first because this action can't be undone
 */
function handleDeleteWorkSpace(workspaceItem) {
    if (!workspaceItem) return;

    const workspaceID = workspaceItem.dataset.workspaceId;
    const workspaceName = workspaceItem.querySelector('.workspace-name').textContent;

    if (confirm(`Are you sure you want to delete workspace "${workspaceName}"? This will also delete all tasks in this workspace.`)) {
        // Delete via API
        fetch('/protask/Navbar/navbar_api.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `action=delete_workspace&workspace_id=${workspaceID}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                workspaceItem.remove();
                console.log('Workspace deleted successfully');
                
                // Check if no workspaces left
                const remainingWorkspaces = document.querySelectorAll('.workspace-item');
                if (remainingWorkspaces.length === 0) {
                    const noWorkspaceHTML = `
                        <div class="no-workspace-message">
                            <p>You don't have any workspace yet.</p>
                            <button class="create-first-workspace-btn" onclick="addNewWorkspace()">Create Workspace</button>
                        </div>
                    `;
                    DOM.workspacesContainer.innerHTML = noWorkspaceHTML;
                }
            } else {
                console.error('Failed to delete workspace:', data.message);
                alert('Failed to delete workspace: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error deleting workspace:', error);
            alert('Error deleting workspace. Please try again.');
        });
    }
}


// Export these functions so other JavaScript files can use them
// This makes them available globally via the window object
window.addNewWorkspace = addNewWorkspace;
window.handleAddTask = handleAddTask;
window.handleHideUnhide = handleHideUnhide;
window.handleDeleteWorkSpace = handleDeleteWorkSpace;
