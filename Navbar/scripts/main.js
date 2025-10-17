/**
 * main entry point
 * waits for the page to load, then kicks off the sidebar initialization
 */

/**
 * Initialize goal link functionality
 * Handles clicking on goal links to set workspace in session before navigation
 */
function initializeGoalLinks() {
    // Add click handler for goal links
    document.addEventListener('click', async (e) => {
        if (e.target.closest('.goal-link')) {
            e.preventDefault();
            const goalLink = e.target.closest('.goal-link');
            const workspaceId = goalLink.dataset.workspaceId;
            
            if (workspaceId) {
                // Set workspace in session before navigating
                try {
                    const formData = new FormData();
                    formData.append('action', 'set_workspace_session');
                    formData.append('workspace_id', workspaceId);
                    
                    const response = await fetch('../Navbar/navbar_api.php', {
                        method: 'POST',
                        body: formData,
                        credentials: 'same-origin'
                    });
                    
                    if (response.ok) {
                        // Navigate to goal page
                        window.location.href = '../GoalPage/GoalPage.php';
                    } else {
                        console.error('Failed to set workspace session');
                        alert('Error: Could not access workspace');
                    }
                } catch (error) {
                    console.error('Error setting workspace session:', error);
                    alert('Error: Could not access workspace');
                }
            }
        }
    });
}


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
    
    // initialize goal link functionality
    initializeGoalLinks();
    
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
