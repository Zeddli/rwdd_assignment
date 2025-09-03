/**
 * Main Entry Point
 * Imports all modules and initializes the sidebar
 */

// Import all modules by loading them in the correct order
// Note: In a real module system, you would use ES6 imports
// For now, we're using script tags in HTML to load dependencies

/**
 * init sidebar when DOM is loaded
 */
document.addEventListener('DOMContentLoaded', () => {
    // init the sidebar
    initializeSidebar();
    
    console.log('All modules loaded and sidebar initialized');
});

// Export main functions for potential external use
window.SidebarManager = {
    toggleSidebar,
    addNewWorkspace,
    addNewTask: handleAddTask,
    renameElement: handleRename,
    pinTask: handlePinTask,
    deleteTask: handleDeleteTask
};
