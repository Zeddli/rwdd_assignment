/**
 * Core State and DOM Management
 * This is like the "brain" of the sidebar - keeps track of what's happening
 * and stores references to important HTML elements so we don't have to find them every time
 */

// Global state management - this keeps track of the current sidebar state
const SidebarState = {
    isOpen: true,                    // Is the sidebar currently expanded or collapsed?
    activeDropdown: null,            // Which dropdown menu is currently open? (only one at a time)
    editingElement: null,            // Which element is currently being renamed? (workspace/task name)
    workspaceCounter: 1,             // Counter for creating new workspaces (legacy - now uses database IDs)
    taskCounter: 1,                  // Counter for creating new tasks (legacy - now uses database IDs)
    allowProgrammaticEdit: false     // Security flag - only allow renaming when triggered from dropdown
};

// DOM elements cache - store references to important HTML elements
// This is faster than using document.getElementById every time we need them
const DOM = {
    sidebar: null,                   // The main sidebar container
    sidebarToggle: null,             // The button that expands/collapses sidebar
    workspacesContainer: null,       // The container that holds all workspaces
    addWorkspaceBtn: null            // The "+" button to add new workspace
};

/**
 * Initialize DOM element references
 * This runs once when the page loads to find and store all the important HTML elements
 * It's like creating a phonebook of elements so we can call them quickly later
 */
function initializeDOM() {
    // Find and store the main sidebar container
    DOM.sidebar = document.getElementById('sidebar');
    
    // Find and store the collapse/expand button
    DOM.sidebarToggle = document.getElementById('sidebarToggle');
    
    // Find and store the container that holds all workspaces
    DOM.workspacesContainer = document.getElementById('workspacesContainer');
    
    // Find and store the "+" button for adding new workspaces
    DOM.addWorkspaceBtn = document.getElementById('addWorkspaceBtn');
    
    console.log('DOM elements initialized');
}

// Export these to the global scope so other JavaScript files can use them
// Think of this like making these variables "public" for other files
window.SidebarState = SidebarState;
window.DOM = DOM;
window.initializeDOM = initializeDOM;
