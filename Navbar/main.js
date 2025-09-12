/**
 * Main Entry Point
 * This is where everything starts! It's like the "main()" function in other languages
 * Waits for the page to load, then kicks off the sidebar initialization
 */

// Import all modules by loading them in the correct order
// Note: In a real module system, you would use ES6 imports
// For now, we're using script tags in HTML to load dependencies (see navbar.php bottom)

/**
 * Initialize sidebar when DOM is loaded
 * This is the first function that runs when the page is ready
 * It waits for all HTML to load, then starts up the sidebar
 */
document.addEventListener('DOMContentLoaded', () => {
    // Start up the sidebar - this calls initializeSidebar() from sidebar.js
    initializeSidebar();
    
    console.log('All modules loaded and sidebar initialized');
});

// Export main functions for potential external use
// This creates a public API that other parts of the app could use if needed
// It's like creating a remote control for the sidebar
window.SidebarManager = {
    toggleSidebar,                   // Function to open/close sidebar
    addNewWorkspace,                 // Function to create new workspace
    addNewTask: handleAddTask,       // Function to create new task
    renameElement: handleRename,     // Function to rename workspace/task
    pinTask: handlePinTask,          // Function to pin/unpin tasks
    deleteTask: handleDeleteTask     // Function to delete tasks
};
