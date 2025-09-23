
/**
 * initialize the sidebar when DOM is loaded
 * it's called from main.js when the page loads
 */
function initializeSidebar() {
    // prevent multiple initializations
    if (window.sidebarInitialized) {
        console.log('Sidebar already initialized, skipping...');
        return;
    }
    
    // find and cache all the important HTML elements
    initializeDOM();
    
    // set up all the click handlers and event listeners
    bindEventListeners();
    
    // init other modules that depend on the basic setup
    initializeDropdowns();           // set up dropdown menus
    initializeEditableElements();    // set up inline renaming (currently disabled)
    
    // mark as initialized
    window.sidebarInitialized = true;
    console.log('Sidebar initialized successfully');
}

/**
 * bind all event listeners for the sidebar
 * only set up listeners once to prevent duplicates
 */
function bindEventListeners() {
    // check if we've already set up the listeners to prevent duplicates
    if (!DOM.workspacesContainer) {
        console.error('workspacesContainer not found!');
        return;
    }
    
    if (DOM.workspacesContainer.dataset.listenersBound === 'true') {
        console.log('Event listeners already bound, skipping...');
        return;
    }
    
    // wire up the sidebar collapse/expand button
    DOM.sidebarToggle.addEventListener('click', toggleSidebar);
    
    // wire up the main "+" button to create new workspace
    DOM.addWorkspaceBtn.addEventListener('click', addNewWorkspace);
    
    // handle "+" buttons for adding tasks to workspaces
    DOM.workspacesContainer.addEventListener('click', (e) => {
        if (e.target.closest('.add-task-btn')) {
            e.stopPropagation(); //stop the + button and add task  trigger same time
            console.log('🔥 + button clicked - calling handleAddTask');
            // user clicked a "+" button next to a workspace
            const workspaceItem = e.target.closest('.workspace-item');
            handleAddTask(workspaceItem);
        }
    });
    
    // close any open dropdown menus when user clicks elsewhere
    document.addEventListener('click', handleOutsideClick);
    
    // handle keyboard shortcuts (like Escape key to cancel editing)
    document.addEventListener('keydown', handleKeyDown);
    
    // mark that we've set up the listeners
    DOM.workspacesContainer.dataset.listenersBound = 'true';
    console.log('Event listeners bound successfully');
}

/**
 * toggle sidebar between open and closed states
 */
function toggleSidebar() {
    // flip the state - if open, make it closed; if closed, make it open
    SidebarState.isOpen = !SidebarState.isOpen;
    
    if (SidebarState.isOpen) {
        // remove the "closed" CSS class to show full sidebar
        DOM.sidebar.classList.remove('closed');
        console.log('Sidebar opened');
    } else {
        // add the "closed" CSS class to show icon-only sidebar
        DOM.sidebar.classList.add('closed');
        console.log('Sidebar closed');
    }
    
    // close any open dropdown menus when toggling 
    closeAllDropdowns();
}

/**
 * Handle clicks outside dropdowns
 */
function handleOutsideClick(event) {
    // if the click wasn't inside a dropdown menu, close all dropdowns
    if (!event.target.closest('.dropdown')) {
        closeAllDropdowns();
    }
}


function handleKeyDown(event) {
    if (event.key === 'Escape') {
        // if user is currently renaming something, cancel it
        if (SidebarState.editingElement) {
            const originalValue = SidebarState.editingElement.dataset.originalValue || '';
            cancelEditing(SidebarState.editingElement, originalValue);
        }
        // also close any open dropdown menus
        closeAllDropdowns();
    }
}

// export these functions so other js files can use them
window.initializeSidebar = initializeSidebar;
window.toggleSidebar = toggleSidebar;
window.handleOutsideClick = handleOutsideClick;
window.handleKeyDown = handleKeyDown;
