/**
 * Core State and DOM Management
 * Handles global state and DOM element caching for the sidebar
 */

// Global state management
const SidebarState = {
    isOpen: true,
    activeDropdown: null,
    editingElement: null,
    workspaceCounter: 1,
    taskCounter: 1
};

// DOM elements cache
const DOM = {
    sidebar: null,
    sidebarToggle: null,
    workspacesContainer: null,
    addWorkspaceBtn: null
};

/**
 * init DOM element references
 */
function initializeDOM() {
    DOM.sidebar = document.getElementById('sidebar');
    DOM.sidebarToggle = document.getElementById('sidebarToggle');
    DOM.workspacesContainer = document.getElementById('workspacesContainer');
    DOM.addWorkspaceBtn = document.getElementById('addWorkspaceBtn');
    
    console.log('DOM elements initialized');
}

// Export for use in other modules
window.SidebarState = SidebarState;
window.DOM = DOM;
window.initializeDOM = initializeDOM;
