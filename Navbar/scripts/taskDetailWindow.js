/**
 * Task Detail Window Functionality
 * Handles creating/editing tasks and managing task details
 */

console.log('TaskDetailWindow.js: Script loading...');

let currentWorkspaceId = null;
let currentTaskId = null;
let isEditMode = false;

/**
 * Show task detail window for creating a new task
 */
function showTaskDetailWindow(workspaceId) {
    currentWorkspaceId = workspaceId;
    currentTaskId = null;
    isEditMode = false;
    
    // Reset form
    resetTaskForm();
    
    // Show modal
    const modal = document.getElementById('taskDetailModal');
    if (modal) {
        modal.style.display = 'flex';
        // Focus on task name input
        const taskNameInput = document.getElementById('taskNameInput');
        if (taskNameInput) {
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
    const endDate = document.getElementById('endDateInput').value;
    const deadline = document.getElementById('deadlineInput').value;
    const priority = document.getElementById('prioritySelect').value;
    const status = document.getElementById('statusSelect').value;
    
    // Basic validation
    if (!taskName) {
        showTaskMessage('Task name is required', 'error');
        return;
    }
    
    if (!currentWorkspaceId) {
        showTaskMessage('No workspace selected', 'error');
        return;
    }
    
    try {
        showTaskMessage('Saving task...', 'info');
        
        const formData = new FormData();
        formData.append('action', isEditMode ? 'update_task' : 'create_task');
        formData.append('workspace_id', currentWorkspaceId);
        formData.append('task_name', taskName);
        formData.append('task_description', description);
        formData.append('start_date', startDate);
        formData.append('end_date', endDate);
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
    // Close modal button
    const closeBtn = document.getElementById('closeTaskDetailModal');
    if (closeBtn) {
        closeBtn.addEventListener('click', hideTaskDetailWindow);
    }
    
    // Cancel button
    const cancelBtn = document.getElementById('cancelTaskBtn');
    if (cancelBtn) {
        cancelBtn.addEventListener('click', hideTaskDetailWindow);
    }
    
    // Grant access button
    const grantAccessBtn = document.getElementById('grantAccessBtn');
    if (grantAccessBtn) {
        grantAccessBtn.addEventListener('click', handleGrantAccessClick);
    }
    
    // Form submission
    const form = document.getElementById('taskDetailForm');
    if (form) {
        form.addEventListener('submit', handleTaskSubmit);
    }
    
    // Close modal when clicking outside
    const modal = document.getElementById('taskDetailModal');
    if (modal) {
        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                hideTaskDetailWindow();
            }
        });
    }
}

// Export functions for use in other files
window.showTaskDetailWindow = showTaskDetailWindow;
window.showEditTaskWindow = showEditTaskWindow;
window.hideTaskDetailWindow = hideTaskDetailWindow;
window.initializeTaskDetailWindow = initializeTaskDetailWindow;

console.log('TaskDetailWindow.js: Functions exported to window object');
console.log('showTaskDetailWindow available:', typeof window.showTaskDetailWindow === 'function');
