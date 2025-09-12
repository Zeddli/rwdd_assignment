
/**
 * Initialize the sidebar when DOM is loaded
 * This is the main setup function that gets everything ready to go
 * It's called from main.js when the page loads
 */
function initializeSidebar() {
    // First, find and cache all the important HTML elements
    initializeDOM();
    
    // Then, set up all the click handlers and event listeners
    bindEventListeners();
    
    // Initialize other modules that depend on the basic setup
    initializeDropdowns();           // Set up dropdown menus
    initializeEditableElements();    // Set up inline renaming (currently disabled)
    
    console.log('Sidebar initialized successfully');
}

/**
 * Bind all event listeners for the sidebar
 * This sets up all the click handlers and keyboard shortcuts
 * Think of it like wiring up all the buttons to actually do something
 */
function bindEventListeners() {
    // Wire up the sidebar collapse/expand button
    DOM.sidebarToggle.addEventListener('click', toggleSidebar);
    
    // Wire up the main "+" button to create new workspace
    DOM.addWorkspaceBtn.addEventListener('click', addNewWorkspace);
    
    // Handle "+" buttons for adding tasks to workspaces
    // We use "event delegation" here - instead of adding listeners to every button,
    // we listen on the container and check what was clicked
    DOM.workspacesContainer.addEventListener('click', (e) => {
        if (e.target.closest('.add-task-btn')) {
            // User clicked a "+" button next to a workspace
            const workspaceItem = e.target.closest('.workspace-item');
            handleAddTask(workspaceItem);
        }
    });
    
    // Close any open dropdown menus when user clicks elsewhere
    document.addEventListener('click', handleOutsideClick);
    
    // Handle keyboard shortcuts (like Escape key to cancel editing)
    document.addEventListener('keydown', handleKeyDown);
}

/**
 * Toggle sidebar between open and closed states
 * This is what happens when user clicks the collapse/expand button
 * It switches between wide sidebar and narrow icon-only sidebar
 */
function toggleSidebar() {
    // Flip the state - if open, make it closed; if closed, make it open
    SidebarState.isOpen = !SidebarState.isOpen;
    
    if (SidebarState.isOpen) {
        // Remove the "closed" CSS class to show full sidebar
        DOM.sidebar.classList.remove('closed');
        console.log('Sidebar opened');
    } else {
        // Add the "closed" CSS class to show icon-only sidebar
        DOM.sidebar.classList.add('closed');
        console.log('Sidebar closed');
    }
    
    // Close any open dropdown menus when toggling (they look weird on collapsed sidebar)
    closeAllDropdowns();
}

/**
 * Handle clicks outside dropdowns
 * This closes dropdown menus when user clicks somewhere else
 * It's like clicking away from a menu to close it
 */
function handleOutsideClick(event) {
    // If the click wasn't inside a dropdown menu, close all dropdowns
    if (!event.target.closest('.dropdown')) {
        closeAllDropdowns();
    }
}

/**
 * Handle keyboard events
 * This listens for special keys like Escape to cancel actions
 */
function handleKeyDown(event) {
    if (event.key === 'Escape') {
        // If user is currently renaming something, cancel it
        if (SidebarState.editingElement) {
            const originalValue = SidebarState.editingElement.dataset.originalValue || '';
            cancelEditing(SidebarState.editingElement, originalValue);
        }
        // Also close any open dropdown menus
        closeAllDropdowns();
    }
}

// Export these functions so other JavaScript files can use them
window.initializeSidebar = initializeSidebar;
window.toggleSidebar = toggleSidebar;
window.handleOutsideClick = handleOutsideClick;
window.handleKeyDown = handleKeyDown;
