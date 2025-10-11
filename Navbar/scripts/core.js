/**
 * keeps track of all the important HTML pieces so we don't lose them
 */

// remembers what state everything is in
if (!window.SidebarState){
    const SidebarState = {
        isOpen: true,                    // Is the sidebar wide open or squished closed?
        activeDropdown: null,            // Which three-dot menu is currently showing? 
        editingElement: null,            // What are we currently renaming? 
        workspaceCounter: 1,             // Old counter for new workspaces 
        taskCounter: 1,                  // Old counter for new tasks 
        allowProgrammaticEdit: false     // Security thing - only let renaming happen fro
    };
    window.SidebarState = SidebarState;
}

// Our "phonebook" of important HTML elements 
if (!window.DOM){
    const DOM = {
        sidebar: null,                   // whole sidebar container
        sidebarToggle: null,             //  little arrow button that opens/closes the sidebar
        workspacesContainer: null,       // scrollable area where all workspaces live
        addWorkspaceBtn: null            // "+" button to make new workspaces
    };
    window.DOM = DOM;
}


/**
 * Find and remember all important HTML elements
 * This runs once when the page loads
 */
function initializeDOM() {
    // Go find the main sidebar and remember where it is
    DOM.sidebar = document.getElementById('sidebar');
    
    // Find that collapse/expand button and remember it
    DOM.sidebarToggle = document.getElementById('sidebarToggle');
    
    // Find where all the workspaces live and remember that spot
    DOM.workspacesContainer = document.getElementById('workspacesContainer');
    
    // Find the "+" button for making new workspaces
    DOM.addWorkspaceBtn = document.getElementById('addWorkspaceBtn');
    
    console.log('Found and remembered all our important HTML elements!');
}

// Make these available to other js files - like sharing your toys with friends
window.SidebarState = SidebarState;
window.DOM = DOM;
window.initializeDOM = initializeDOM;
