
/**
 * init dropdown functionality for all dropdown elements
 * handle toggle, position
 */
function initializeDropdowns() {
    const dropdowns = document.querySelectorAll('.dropdown');
    
    dropdowns.forEach(dropdown => {
        const toggle = dropdown.querySelector('.dropdown-toggle');
        const menu = dropdown.querySelector('.dropdown-menu');
        
        toggle.addEventListener('click', (e) => {
            e.stopPropagation();
            toggleDropdown(dropdown);
        });
        
        // Handle dropdown item clicks
        const items = dropdown.querySelectorAll('.dropdown-item');
        items.forEach(item => {
            item.addEventListener('click', (e) => {
                e.stopPropagation();
                handleDropdownAction(item, dropdown);
            });
        });
    });
}

/**
 * dropdown visibility
 */
function toggleDropdown(dropdown) {
    // Close other dropdowns first
    closeAllDropdowns();
    
    if (dropdown.classList.contains('active')) {
        dropdown.classList.remove('active');
        SidebarState.activeDropdown = null;
    } else {
        dropdown.classList.add('active');
        SidebarState.activeDropdown = dropdown;
        
        // Position the dropdown menu for fixed positioning
        const menu = dropdown.querySelector('.dropdown-menu');
        const toggle = dropdown.querySelector('.dropdown-toggle');
        const toggleRect = toggle.getBoundingClientRect();
        
        // Position the menu below the toggle button
        menu.style.left = (toggleRect.left - 160 + toggleRect.width) + 'px';
        menu.style.top = (toggleRect.bottom + 5) + 'px';
    }
}

/**
 * close all open dropdowns
 */
function closeAllDropdowns() {
    const activeDropdowns = document.querySelectorAll('.dropdown.active');
    activeDropdowns.forEach(dropdown => {
        dropdown.classList.remove('active');
    });
    SidebarState.activeDropdown = null;
}

/**
 * handle dropdown action clicks
 */
function handleDropdownAction(item, dropdown) {
    const action = item.dataset.action;
    const workspaceItem = dropdown.closest('.workspace-item');
    const taskItem = dropdown.closest('.task-item');
    
    console.log(`Dropdown action: ${action}`);
    
    switch (action) {
        case 'invite':
            handleInviteMember(workspaceItem);
            break;
        case 'add-task':
            handleAddTask(workspaceItem);
            break;
        case 'rename':
            if (taskItem) {
                // if is task dropdown - rename the task
                handleRename(taskItem.querySelector('.task-name'));
            } else if (workspaceItem) {
                // if is workspace dropdown - rename the workspace
                handleRename(workspaceItem.querySelector('.workspace-name'));
            }
            break;
        case 'hide':
            handleHideUnhide(workspaceItem);
            break;
        case 'grant-access':
            handleGrantAccess(taskItem);
            break;
        case 'pin':
            handlePinTask(taskItem);
            break;
        case 'delete':
            handleDeleteTask(taskItem);
            break;
    }
    
    // close dropdown 
    dropdown.classList.remove('active');
}

// export to other file
window.initializeDropdowns = initializeDropdowns;
window.toggleDropdown = toggleDropdown;
window.closeAllDropdowns = closeAllDropdowns;
window.handleDropdownAction = handleDropdownAction;
