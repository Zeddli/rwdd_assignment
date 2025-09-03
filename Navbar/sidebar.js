
/**
 * init the sidebar when DOM is loaded
 */
function initializeSidebar() {
    // init DOM elements
    initializeDOM();
    
    // Bind event listeners
    bindEventListeners();
    
    // init other modules
    initializeDropdowns();
    initializeEditableElements();
    
    console.log('Sidebar initialized successfully');
}

/**
 * Bind all event listeners for the sidebar
 */
function bindEventListeners() {
    // Sidebar toggle functionality
    DOM.sidebarToggle.addEventListener('click', toggleSidebar);
    
    // Add workspace button
    DOM.addWorkspaceBtn.addEventListener('click', addNewWorkspace);
    
    // Add task buttons (delegated event handling)
    DOM.workspacesContainer.addEventListener('click', (e) => {
        if (e.target.closest('.add-task-btn')) {
            const workspaceItem = e.target.closest('.workspace-item');
            handleAddTask(workspaceItem);
        }
    });
    
    // Close dropdowns when clicking outside
    document.addEventListener('click', handleOutsideClick);
    
    // Handle escape key for editing
    document.addEventListener('keydown', handleKeyDown);
}

/**
 * Toggle sidebar between open and closed states
 */
function toggleSidebar() {
    SidebarState.isOpen = !SidebarState.isOpen;
    
    if (SidebarState.isOpen) {
        DOM.sidebar.classList.remove('closed');
        console.log('Sidebar opened');
    } else {
        DOM.sidebar.classList.add('closed');
        console.log('Sidebar closed');
    }
    
    // Close any open dropdowns when toggling
    closeAllDropdowns();
}

/**
 * Handle clicks outside dropdowns
 */
function handleOutsideClick(event) {
    if (!event.target.closest('.dropdown')) {
        closeAllDropdowns();
    }
}

/**
 * Handle keyboard events
 */
function handleKeyDown(event) {
    if (event.key === 'Escape') {
        if (SidebarState.editingElement) {
            const originalValue = SidebarState.editingElement.dataset.originalValue || '';
            cancelEditing(SidebarState.editingElement, originalValue);
        }
        closeAllDropdowns();
    }
}

// Export for use in other modules
window.initializeSidebar = initializeSidebar;
window.toggleSidebar = toggleSidebar;
window.handleOutsideClick = handleOutsideClick;
window.handleKeyDown = handleKeyDown;
