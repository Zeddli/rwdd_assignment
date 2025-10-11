/**
 * main entry point
 * waits for the page to load, then kicks off the sidebar initialization
 */


/**
 * initialize sidebar when DOM is loaded
 * first function that runs when the page is ready
 * waits for all HTML to load, then starts up the sidebar
 */
document.addEventListener('DOMContentLoaded', () => {
    // start up the sidebar
    initializeSidebar();
    
    // initialize invite member functionality
    if (typeof initializeInviteMember === 'function') {
        initializeInviteMember();
    }
    
    // initialize task detail window functionality
    if (typeof initializeTaskDetailWindow === 'function') {
        initializeTaskDetailWindow();
    }
    
    // initialize grant access window functionality
    if (typeof initializeGrantAccessWindow === 'function') {
        initializeGrantAccessWindow();
    }
    
    console.log('All modules loaded and sidebar initialized');
});

// export main functions for potential external use
// creates a public API that other parts of the app could use if needed
// like creating a remote control for the sidebar
window.SidebarManager = {
    toggleSidebar,                   // Function to open/close sidebar
    addNewWorkspace,                 // Function to create new workspace
    addNewTask: handleAddTask,       // Function to create new task
    renameElement: handleRename,     // Function to rename workspace/task
    pinTask: handlePinTask           // Function to pin/unpin tasks
    // deleteTask function removed - to be reimplemented
};
