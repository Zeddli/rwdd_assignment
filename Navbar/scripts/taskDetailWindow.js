/**
 * Task Detail Window Functionality
 * Handles creating/editing tasks and managing task details
 */

console.log('TaskDetailWindow.js: Script loading...');

let currentWorkspaceId = null;
let currentTaskId = null;
let isEditMode = false;
let showWorkspaceSelection = false;
let taskDetailWindowInitialized = false; // prevent double initialization

/**
 * Show task detail window for creating a new task
 * @param {number|object} workspaceId - Workspace ID or options object
 * @param {object} options - Options for modal display
 */
function showTaskDetailWindow(workspaceId, options = {}) {
    // Handle different parameter formats
    if (typeof workspaceId === 'object') {
        options = workspaceId;
        workspaceId = options.workspaceId || null;
    }
    
    console.log('showTaskDetailWindow called with:', { workspaceId, options, showWorkspaceSelection: options.showWorkspaceSelection });
    
    currentWorkspaceId = workspaceId;
    currentTaskId = null;
    isEditMode = false;
    showWorkspaceSelection = options.showWorkspaceSelection || false;
    
    console.log('showWorkspaceSelection set to:', showWorkspaceSelection);
    
    // Reset form
    resetTaskForm();
    
    // Show/hide workspace selection based on options
    toggleWorkspaceSelection(showWorkspaceSelection);
    
    // If workspace selection is shown, load workspaces
    if (showWorkspaceSelection) {
        console.log('Loading workspaces...');
        loadWorkspaces();
    }
    
    // Show modal
    const modal = document.getElementById('taskDetailModal');
    if (modal) {
        modal.style.display = 'flex';
        // Focus on appropriate input
        const taskNameInput = document.getElementById('taskNameInput');
        const workspaceSelect = document.getElementById('workspaceSelect');
        if (showWorkspaceSelection && workspaceSelect) {
            workspaceSelect.focus();
        } else if (taskNameInput) {
            taskNameInput.focus();
        }
    }
}

/**
 * Show task detail window for editing an existing task
 */
function showEditTaskWindow(taskId, workspaceId) {
    currentTaskId = taskId;
    currentWorkspaceId = workspaceId;
    isEditMode = true;
    
    // Load task data
    loadTaskData(taskId);
    
    // Show modal
    const modal = document.getElementById('taskDetailModal');
    if (modal) {
        modal.style.display = 'flex';
    }
}

/**
 * Toggle workspace selection visibility
 * @param {boolean} show - Whether to show workspace selection
 */
function toggleWorkspaceSelection(show) {
    console.log('toggleWorkspaceSelection called with show:', show);
    
    const workspaceGroup = document.getElementById('workspaceSelectionGroup');
    const workspaceSelect = document.getElementById('workspaceSelect');
    
    console.log('workspaceGroup found:', !!workspaceGroup);
    console.log('workspaceSelect found:', !!workspaceSelect);
    
    if (workspaceGroup) {
        workspaceGroup.style.display = show ? 'block' : 'none';
        console.log('workspaceGroup display set to:', workspaceGroup.style.display);
    }
    
    if (workspaceSelect) {
        workspaceSelect.required = show;
        console.log('workspaceSelect required set to:', show);
    }
}

/**
 * Load available workspaces for the dropdown
 */
async function loadWorkspaces() {
    console.log('loadWorkspaces: Starting to load workspaces...');
    try {
        const response = await fetch('../Navbar/navbar_api.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'action=get_workspaces'
        });
        
        console.log('loadWorkspaces: Response status:', response.status);
        const data = await response.json();
        console.log('loadWorkspaces: Response data:', data);
        
        if (data.success && data.workspaces) {
            console.log('loadWorkspaces: Workspaces received:', data.workspaces);
            populateWorkspaceDropdown(data.workspaces);
        } else {
            console.error('Error loading workspaces:', data.message);
            showTaskMessage('Error loading workspaces: ' + (data.message || 'Unknown error'), 'error');
        }
    } catch (error) {
        console.error('Error fetching workspaces:', error);
        showTaskMessage('Error loading workspaces: ' + error.message, 'error');
    }
}

/**
 * Populate workspace dropdown with available workspaces
 * @param {Array} workspaces - Array of workspace objects
 */
function populateWorkspaceDropdown(workspaces) {
    console.log('populateWorkspaceDropdown: Called with workspaces:', workspaces);
    const workspaceSelect = document.getElementById('workspaceSelect');
    
    if (!workspaceSelect) {
        console.error('populateWorkspaceDropdown: workspaceSelect element not found');
        return;
    }
    
    console.log('populateWorkspaceDropdown: Found workspace select element');
    
    // Clear existing options except the first one
    workspaceSelect.innerHTML = '<option value="">Select Workspace</option>';
    
    // Add workspace options
    workspaces.forEach(workspace => {
        console.log('populateWorkspaceDropdown: Adding workspace:', workspace);
        const option = document.createElement('option');
        option.value = workspace.WorkSpaceID;
        option.textContent = workspace.WorkspaceName; // Fixed: use WorkspaceName instead of Name
        workspaceSelect.appendChild(option);
    });
    
    console.log('populateWorkspaceDropdown: Added', workspaces.length, 'workspace options');
    
    // Set default selection if currentWorkspaceId is provided
    if (currentWorkspaceId) {
        workspaceSelect.value = currentWorkspaceId;
        console.log('populateWorkspaceDropdown: Set default workspace to:', currentWorkspaceId);
    }
}

/**
 * Reset the task form to default values
 */
function resetTaskForm() {
    const form = document.getElementById('taskDetailForm');
    if (form) {
        form.reset();
    }
    
    // Set default values
    const statusSelect = document.getElementById('statusSelect');
    if (statusSelect) {
        statusSelect.value = 'Pending';
    }
    
    // Reset workspace selection
    const workspaceSelect = document.getElementById('workspaceSelect');
    if (workspaceSelect) {
        workspaceSelect.value = '';
    }
    
    const messageDiv = document.getElementById('taskMessage');
    if (messageDiv) {
        messageDiv.style.display = 'none';
        messageDiv.textContent = '';
    }
}

/**
 * Load task data for editing
 */
async function loadTaskData(taskId) {
    try {
        // This would typically fetch task data from the server
        // For now, we'll show a placeholder
        console.log('Loading task data for ID:', taskId);
        // TODO: Implement actual task data loading
    } catch (error) {
        console.error('Error loading task data:', error);
        showTaskMessage('Error loading task data', 'error');
    }
}

/**
 * Handle form submission
 */
async function handleTaskSubmit(event) {
    event.preventDefault();
    
    const taskName = document.getElementById('taskNameInput').value.trim();
    const description = document.getElementById('taskDescriptionInput').value.trim();
    const startDate = document.getElementById('startDateInput').value;
    const deadline = document.getElementById('deadlineInput').value;
    const priority = document.getElementById('prioritySelect').value;
    const status = document.getElementById('statusSelect').value;
    
    // Get workspace ID - either from dropdown or current workspace
    let workspaceId = currentWorkspaceId;
    if (showWorkspaceSelection) {
        const workspaceSelect = document.getElementById('workspaceSelect');
        workspaceId = workspaceSelect ? workspaceSelect.value : null;
    }
    
    // Basic validation
    if (!taskName) {
        showTaskMessage('Task name is required', 'error');
        return;
    }
    
    if (!startDate) {
        showTaskMessage('Start date is required', 'error');
        return;
    }
    
    if (!deadline) {
        showTaskMessage('Deadline is required', 'error');
        return;
    }
    
    // Validate that deadline is after start date
    if (startDate && deadline && deadline <= startDate) {
        showTaskMessage('Deadline must be after the start date', 'error');
        return;
    }
    
    if (!workspaceId) {
        showTaskMessage('No workspace selected', 'error');
        return;
    }
    
    try {
        showTaskMessage('Saving task...', 'info');
        
        const formData = new FormData();
        formData.append('action', isEditMode ? 'update_task' : 'create_task');
        formData.append('workspace_id', workspaceId);
        formData.append('task_name', taskName);
        formData.append('task_description', description);
        formData.append('start_date', startDate);
        formData.append('deadline', deadline);
        formData.append('priority', priority);
        formData.append('status', status);
        
        if (isEditMode && currentTaskId) {
            formData.append('task_id', currentTaskId);
        }
        
        const response = await fetch('../Navbar/navbar_api.php', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            showTaskMessage(isEditMode ? 'Task updated successfully!' : 'Task created successfully!', 'success');
            
            // Close modal after successful save
            setTimeout(() => {
                hideTaskDetailWindow();
                // Refresh workspace list if needed
                if (typeof refreshWorkspaces === 'function') {
                    refreshWorkspaces();
                } else if (typeof window.refreshWorkspaces === 'function') {
                    window.refreshWorkspaces();
                } else if (typeof window.refreshTasksAfterModal === 'function') {
                    // For calendar page todo list
                    window.refreshTasksAfterModal();
                } else {
                    // Fallback: reload the page to show the new task
                    console.log('Refreshing page to show new task...');
                    window.location.reload();
                }
            }, 1500);
        } else {
            showTaskMessage(result.message || 'Failed to save task', 'error');
        }
    } catch (error) {
        console.error('Error saving task:', error);
        showTaskMessage('Error saving task. Please try again.', 'error');
    }
}

/**
 * Hide the task detail window
 */
function hideTaskDetailWindow() {
    const modal = document.getElementById('taskDetailModal');
    if (modal) {
        modal.style.display = 'none';
        resetTaskForm();
    }
}

/**
 * Show message to user
 */
function showTaskMessage(message, type) {
    const messageDiv = document.getElementById('taskMessage');
    if (messageDiv) {
        messageDiv.textContent = message;
        messageDiv.className = `task-message ${type}`;
        messageDiv.style.display = 'block';
    }
}

/**
 * Handle grant access button click
 */
function handleGrantAccessClick() {
    if (!currentWorkspaceId) {
        alert('No workspace selected');
        return;
    }
    
    // Hide task detail modal
    hideTaskDetailWindow();
    
    // Show grant access modal
    if (typeof showGrantAccessWindow === 'function') {
        showGrantAccessWindow(currentWorkspaceId, currentTaskId);
    }
}

/**
 * Initialize task detail window functionality
 */
function initializeTaskDetailWindow() {
    // Avoid duplicate event bindings
    if (taskDetailWindowInitialized) {
        return;
    }
    // Close modal button
    const closeBtn = document.getElementById('closeTaskDetailModal');
    if (closeBtn) {
        if (!closeBtn.dataset.listenerAdded) {
            closeBtn.addEventListener('click', hideTaskDetailWindow);
            closeBtn.dataset.listenerAdded = 'true';
        }
    }
    
    // Cancel button
    const cancelBtn = document.getElementById('cancelTaskBtn');
    if (cancelBtn) {
        if (!cancelBtn.dataset.listenerAdded) {
            cancelBtn.addEventListener('click', hideTaskDetailWindow);
            cancelBtn.dataset.listenerAdded = 'true';
        }
    }
    
    // Grant access button
    const grantAccessBtn = document.getElementById('grantAccessBtn');
    if (grantAccessBtn) {
        if (!grantAccessBtn.dataset.listenerAdded) {
            grantAccessBtn.addEventListener('click', handleGrantAccessClick);
            grantAccessBtn.dataset.listenerAdded = 'true';
        }
    }
    
    // Form submission
    const form = document.getElementById('taskDetailForm');
    if (form) {
        if (!form.dataset.listenerAdded) {
            form.addEventListener('submit', handleTaskSubmit);
            form.dataset.listenerAdded = 'true';
        }
    }
    
    // Close modal when clicking outside
    const modal = document.getElementById('taskDetailModal');
    if (modal) {
        if (!modal.dataset.listenerAdded) {
            modal.addEventListener('click', (e) => {
                if (e.target === modal) {
                    hideTaskDetailWindow();
                }
            });
            modal.dataset.listenerAdded = 'true';
        }
    }

    taskDetailWindowInitialized = true;
}

// Export functions for use in other files
window.showTaskDetailWindow = showTaskDetailWindow;
window.showEditTaskWindow = showEditTaskWindow;
window.hideTaskDetailWindow = hideTaskDetailWindow;
window.initializeTaskDetailWindow = initializeTaskDetailWindow;

// Debug function to manually show workspace selection
window.debugShowWorkspaceSelection = function() {
    console.log('Manually showing workspace selection...');
    const workspaceGroup = document.getElementById('workspaceSelectionGroup');
    const workspaceSelect = document.getElementById('workspaceSelect');
    
    console.log('workspaceGroup:', workspaceGroup);
    console.log('workspaceSelect:', workspaceSelect);
    
    if (workspaceGroup) {
        workspaceGroup.style.display = 'block';
        console.log('Workspace group shown');
    }
    
    if (workspaceSelect) {
        workspaceSelect.required = true;
        console.log('Workspace select made required');
    }
    
    // Load workspaces
    loadWorkspaces();
};

console.log('TaskDetailWindow.js: Functions exported to window object');
console.log('showTaskDetailWindow available:', typeof window.showTaskDetailWindow === 'function');

export {
    showTaskDetailWindow,
    showEditTaskWindow,
    hideTaskDetailWindow,
    initializeTaskDetailWindow,
    showTaskMessage,
    handleTaskSubmit,
    handleGrantAccessClick,
    loadWorkspaces,
    populateWorkspaceDropdown,
    resetTaskForm,
}