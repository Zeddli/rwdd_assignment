
/**
 * Dropdown Menu System
 * Handles all the three-dot menus (⋮) throughout the sidebar
 * This manages opening/closing menus and handling the actions when you click menu items
 */
/**
 * Initialize all dropdown menus on the page
 * This finds every dropdown menu and sets up the click handlers
 * Called once when the sidebar starts up
 */
function initializeDropdowns() {
    // Find all dropdown containers (the three-dot menus)
    const dropdowns = document.querySelectorAll('.dropdown');
    
    // Set up each dropdown menu
    dropdowns.forEach(dropdown => {
        // Find the button that opens the menu (the three dots)
        const toggle = dropdown.querySelector('.dropdown-toggle');
        const menu = dropdown.querySelector('.dropdown-menu');
        
        // When user clicks the three dots, open/close the menu
        toggle.addEventListener('click', (e) => {
            e.stopPropagation(); // Don't let this click bubble up to parent elements
            toggleDropdown(dropdown);
        });
        
        // Handle clicks on menu items (like "Rename", "Delete", etc.)
        const items = dropdown.querySelectorAll('.dropdown-item');
        items.forEach(item => {
            item.addEventListener('click', (e) => {
                e.stopPropagation(); // Don't let this click bubble up
                handleDropdownAction(item, dropdown);
            });
        });
    });
}

/**
 * Toggle a specific dropdown menu open or closed
 * This is called when user clicks the three-dot button
 */
function toggleDropdown(dropdown) {
    // First, close any other open dropdowns (only one menu open at a time)
    closeAllDropdowns();
    
    // Check if this dropdown is currently open
    if (dropdown.classList.contains('active')) {
        // It's open, so close it
        dropdown.classList.remove('active');
        SidebarState.activeDropdown = null;
    } else {
        // It's closed, so open it
        dropdown.classList.add('active');
        SidebarState.activeDropdown = dropdown;
        
        // Position the dropdown menu properly on screen
        // We use fixed positioning so it doesn't get cut off by the sidebar
        const menu = dropdown.querySelector('.dropdown-menu');
        const toggle = dropdown.querySelector('.dropdown-toggle');
        const toggleRect = toggle.getBoundingClientRect();
        
        // Position the menu below and to the left of the three-dot button
        menu.style.left = (toggleRect.left - 160 + toggleRect.width) + 'px';
        menu.style.top = (toggleRect.bottom + 5) + 'px';
    }
}

/**
 * Close all open dropdown menus
 * This ensures only one dropdown is open at a time
 * Also called when user clicks outside or presses Escape
 */
function closeAllDropdowns() {
    // Find all currently open dropdown menus
    const activeDropdowns = document.querySelectorAll('.dropdown.active');
    
    // Close each one by removing the "active" class
    activeDropdowns.forEach(dropdown => {
        dropdown.classList.remove('active');
    });
    
    // Clear the global state
    SidebarState.activeDropdown = null;
}

/**
 * Handle dropdown menu action clicks
 * This is the "traffic controller" - it figures out what the user clicked and calls the right function
 * Each dropdown item has a data-action attribute that tells us what to do
 */
function handleDropdownAction(item, dropdown) {
    // Get the action from the clicked item (like "rename", "delete", etc.)
    const action = item.dataset.action;
    
    // Figure out what kind of item this dropdown belongs to
    const workspaceItem = dropdown.closest('.workspace-item');  // Is this a workspace dropdown?
    const taskItem = dropdown.closest('.task-item');            // Is this a task dropdown?
    const goalItem = dropdown.closest('.goal-item');            // Is this a goal dropdown?
    
    console.log(`Dropdown action: ${action}`);
    
    // Route the action to the appropriate handler function
    switch (action) {
        case 'invite':
            // User wants to invite someone to a workspace
            handleInviteMember(workspaceItem);
            break;
            
        case 'add-task':
            // User wants to add a new task to a workspace
            handleAddTask(workspaceItem);
            break;
            
        case 'rename':
            // User wants to rename something - figure out what
            console.log('Rename action triggered');
            if (taskItem) {
                // Renaming a task
                console.log('Renaming task:', taskItem);
                const taskNameElement = taskItem.querySelector('.task-name');
                console.log('Task name element found:', taskNameElement);
                handleRename(taskNameElement);
            } else if (goalItem) {
                // Renaming a goal (though goals don't have dropdowns anymore)
                console.log('Renaming goal:', goalItem);
                const goalNameElement = goalItem.querySelector('.goal-name');
                console.log('Goal name element found:', goalNameElement);
                handleRename(goalNameElement);
            } else if (workspaceItem) {
                // Renaming a workspace
                console.log('Renaming workspace:', workspaceItem);
                const workspaceNameElement = workspaceItem.querySelector('.workspace-name');
                console.log('Workspace name element found:', workspaceNameElement);
                handleRename(workspaceNameElement);
            }
            break;
            
        case 'view-details':
            // User wants to see goal details (placeholder for future feature)
            if (goalItem) {
                console.log('View goal details - placeholder functionality');
                alert('Goal details functionality would be implemented here');
            }
            break;
        case 'hide':
            // User wants to hide/show workspace tasks
            handleHideUnhide(workspaceItem);
            break;
            
        case 'grant-access':
            // User wants to give someone access to a task
            handleGrantAccess(taskItem);
            break;
            
        case 'pin':
            // User wants to pin/unpin a task to the top
            handlePinTask(taskItem);
            break;
            
        case 'delete':
            // User wants to delete something - figure out what
            if (taskItem) {
                // Deleting a task
                handleDeleteTask(taskItem);
            } else if (workspaceItem) {
                // Deleting a workspace
                handleDeleteWorkSpace(workspaceItem);
            }
            break;
    }
    
    // Always close the dropdown after an action is performed
    dropdown.classList.remove('active');
}

// Export these functions so other JavaScript files can use them
window.initializeDropdowns = initializeDropdowns;
window.toggleDropdown = toggleDropdown;
window.closeAllDropdowns = closeAllDropdowns;
window.handleDropdownAction = handleDropdownAction;
