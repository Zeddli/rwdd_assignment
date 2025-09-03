/**
 * workspace management
 * workspace creation, tasks and workspace actions
 */

/**
 * add new workspace
 */
function addNewWorkspace() {
    SidebarState.workspaceCounter++;
    const workspaceId = `workspace-${SidebarState.workspaceCounter}`;

    /**
     * workspaceId用途
     * 给每个workspace一个id
     * 
     */
    
    
    const workspaceHTML = `
        <div class="workspace-item" data-workspace-id="${workspaceId}">
            <div class="workspace-header-item">
                <img src="navbar-icon/workspace.svg" alt="Workspace" class="workspace-icon" width="18" height="18">
                <span class="workspace-name" data-editable="true">New Workspace</span>
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
                            <button class="dropdown-item" data-action="hide">Hide</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="workspace-submenu" data-visible="true">
                <div class="submenu-item">
                    <img src="navbar-icon/goal.svg" alt="Goal" class="submenu-icon" width="16" height="16">
                    <span class="submenu-label">Goal</span>
                </div>
            </div>
        </div>
    `;
    
    DOM.workspacesContainer.insertAdjacentHTML('beforeend', workspaceHTML);
    
    // re-init dropdown and editable elements for new workspace
    initializeDropdowns();
    initializeEditableElements();
    
    // start editing the new workspace name
    const newWorkspace = document.querySelector(`[data-workspace-id="${workspaceId}"]`);
    const nameElement = newWorkspace.querySelector('.workspace-name');
    setTimeout(() => startEditing(nameElement), 100);
    
    console.log(`New workspace added: ${workspaceId}`);
}

/**
 * adding new task to a workspace
 */
function handleAddTask(workspaceItem) {
    if (!workspaceItem) return;
    
    SidebarState.taskCounter++;
    const taskId = `task-${SidebarState.taskCounter}`;
    
    const taskHTML = `
        <div class="task-item" data-task-id="${taskId}" data-pinned="false">
            <img src="navbar-icon/task.svg" alt="Task" class="submenu-icon" width="16" height="16">
            <span class="task-name" data-editable="true">New Task</span>
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
    
    // get the new task element
    const newTask = document.querySelector(`[data-task-id="${taskId}"]`);
    
    // manually init the dropdown for this new task
    const dropdown = newTask.querySelector('.dropdown');
    const toggle = dropdown.querySelector('.dropdown-toggle');
    const menu = dropdown.querySelector('.dropdown-menu');
    
    // add click event to toggle
    toggle.addEventListener('click', (e) => {
        e.stopPropagation();
        toggleDropdown(dropdown);
    });
    
    // add click events to dropdown items
    const items = dropdown.querySelectorAll('.dropdown-item');
    items.forEach(item => {
        item.addEventListener('click', (e) => {
            e.stopPropagation();
            handleDropdownAction(item, dropdown);
        });
    });
    
    // init editable function for the task name
    const nameElement = newTask.querySelector('.task-name');
    nameElement.addEventListener('click', () => {
        if (!SidebarState.editingElement) {
            startEditing(nameElement);
        }
    });
    
    // edit the new task name
    setTimeout(() => startEditing(nameElement), 100);
    
    console.log(`New task added: ${taskId}`);
}

/**
 *  hide/unhide workspace submenu
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

// export to other file
window.addNewWorkspace = addNewWorkspace;
window.handleAddTask = handleAddTask;
window.handleHideUnhide = handleHideUnhide;
