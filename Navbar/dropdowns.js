/**
 * Set up all the dropdown menus when the page first loads
 */
function initializeDropdowns() {
    // find every single dropdown menu on the page
    const dropdowns = document.querySelectorAll('.dropdown');
    
    // go through each one and make it work
    dropdowns.forEach(dropdown => {
        initializeSingleDropdown(dropdown);
    });
}

/**
 * use this for the initial setup AND when we create new workspaces/tasks
 */
function initializeSingleDropdown(dropdown) {
    // don't set up the same dropdown twice
    if (dropdown.dataset.initialized === 'true') {
        return;
    }
    
    // find the three-dot button and the menu that pops up
    const toggle = dropdown.querySelector('.dropdown-toggle');
    const menu = dropdown.querySelector('.dropdown-menu');
    
    // when someone clicks the three dots, show or hide the menu
    toggle.addEventListener('click', (e) => {
        e.stopPropagation(); // don't let this click mess with other stuff
        toggleDropdown(dropdown);
    });
    
    // handle when someone clicks a menu option (like "Rename" or "Delete")
    const items = dropdown.querySelectorAll('.dropdown-item');
    items.forEach(item => {
        item.addEventListener('click', (e) => {
            e.stopPropagation(); // keep this click to ourselves
            handleDropdownAction(item, dropdown);
        });
    });
    
    // remember that we already set this one up
    dropdown.dataset.initialized = 'true';
}

/**
 * Show or hide a dropdown menu when someone clicks the three dots
 * only one menu can be open at a time
 */
function toggleDropdown(dropdown) {
    // close any other menus that might be open
    closeAllDropdowns();
    
    // Is this menu already showing?
    if (dropdown.classList.contains('active')) {
        // close it
        dropdown.classList.remove('active');
        SidebarState.activeDropdown = null;
    } else {
        dropdown.classList.add('active');
        SidebarState.activeDropdown = dropdown;
        
        // make sure the menu shows up in the right spot on screen
        const menu = dropdown.querySelector('.dropdown-menu');
        const toggle = dropdown.querySelector('.dropdown-toggle');
        const toggleRect = toggle.getBoundingClientRect();
        
        // position it nicely below and to the left of the three-dot button
        menu.style.left = (toggleRect.left - 160 + toggleRect.width) + 'px';
        menu.style.top = (toggleRect.bottom + 5) + 'px';
    }
}

/**
 * Close all dropdown menus
 */
function closeAllDropdowns() {
    // find any menus that are currently showing
    const activeDropdowns = document.querySelectorAll('.dropdown.active');
    
    // tell each one to close up
    activeDropdowns.forEach(dropdown => {
        dropdown.classList.remove('active');
    });
    
    // Remember that no menus are open now
    SidebarState.activeDropdown = null;
}

/**
 * figure out what the user wants to do when they click a menu item
 */
function handleDropdownAction(item, dropdown) {
    // What did they click? (the tag tells us)
    const action = item.dataset.action;
    
    // What kind of thing does this menu belong to?
    const workspaceItem = dropdown.closest('.workspace-item');  // Is it a workspace menu?
    const taskItem = dropdown.closest('.task-item');            // Is it a task menu?
    const goalItem = dropdown.closest('.goal-item');            // Is it a goal menu?
    
    console.log(`User wants to: ${action}`);
    
    // send them to the right function based on what they clicked
    switch (action) {
        case 'invite':
            // invite someone to help with a workspace
            handleInviteMember(workspaceItem);
            break;
            
        case 'add-task':
            // add a new task to a workspace
            handleAddTask(workspaceItem);
            break;
            
        case 'rename':
            // rename something (task, goal, or workspace)
            console.log('Time to rename something!');
            if (taskItem) {
                // rename a task
                console.log('Renaming a task:', taskItem);
                const taskNameElement = taskItem.querySelector('.task-name');
                console.log('Found the task name to rename:', taskNameElement);
                handleRename(taskNameElement);
            } else if (goalItem) {
                // rename a goal
                console.log('Renaming a goal:', goalItem);
                const goalNameElement = goalItem.querySelector('.goal-name');
                console.log('Found the goal name to rename:', goalNameElement);
                handleRename(goalNameElement);
            } else if (workspaceItem) {
                // rename a workspace
                console.log('Renaming a workspace:', workspaceItem);
                const workspaceNameElement = workspaceItem.querySelector('.workspace-name');
                console.log('Found the workspace name to rename:', workspaceNameElement);
                handleRename(workspaceNameElement);
            }
            break;
            
        case 'view-details':
            // see more info about a goal (we'll add this feature later)
            if (goalItem) {
                console.log('Showing goal details - coming soon!');
                alert('Goal details feature is coming soon!');
            }
            break;
        case 'hide':
            // hide/show the tasks under a workspace
            handleHideUnhide(workspaceItem);
            break;
            
        case 'grant-access':
            // let someone else work on a task
            handleGrantAccess(taskItem);
            break;
            
        case 'pin':
            // pin a task to the top (like a sticky note)
            handlePinTask(taskItem);
            break;
            
        case 'delete':
            if (taskItem) {
                // Delete a task
                handleDeleteTask(taskItem);
            } else if (workspaceItem) {
                // Delete a workspace
                handleDeleteWorkspace(workspaceItem);
            }
            break;
    }
    
    // Close the menu now that they've made their choice
    dropdown.classList.remove('active');
}

// Share these functions with other js files
window.initializeDropdowns = initializeDropdowns;
window.initializeSingleDropdown = initializeSingleDropdown;
window.toggleDropdown = toggleDropdown;
window.closeAllDropdowns = closeAllDropdowns;
window.handleDropdownAction = handleDropdownAction;
