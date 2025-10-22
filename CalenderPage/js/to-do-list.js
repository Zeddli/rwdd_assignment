/**
 * To-Do List Client-Side Functionality
 * Handles sidebar interactions, task management, and API calls
 */

// DOM Elements
const sidebarToggle = document.getElementById('todoSidebarToggle');
const todoSidebar = document.getElementById('todoSidebar');
const closeSidebar = document.getElementById('closeSidebar');
const mainContent = document.getElementById('mainContent');
const openTaskDetailBtn = document.getElementById('openTaskDetailBtn');
const taskList = document.getElementById('taskList');
const emptyState = document.getElementById('emptyState');
const todoMobileOverlay = document.getElementById('todoMobileOverlay');

// API endpoints
const API_BASE = 'php/';
const API_ENDPOINTS = {
    getTasks: API_BASE + 'getTasks.php',
    createTask: API_BASE + 'createTask.php',
    updateTask: API_BASE + 'updateTask.php',
    deleteTask: API_BASE + 'deleteTask.php'
};

/**
 * Check if we're on mobile view
 */
function isMobileView() {
    return window.innerWidth <= 768;
}

/**
 * Toggle to-do sidebar open/close
 */
function toggleTodoSidebar() {
    if (todoSidebar && sidebarToggle && mainContent) {
        todoSidebar.classList.toggle('open');
        sidebarToggle.classList.toggle('active');
        
        // Handle mobile overlay
        if (isMobileView() && todoMobileOverlay) {
            todoMobileOverlay.classList.toggle('active');
            // Prevent body scroll when sidebar is open on mobile
            document.body.style.overflow = todoSidebar.classList.contains('open') ? 'hidden' : '';
        } else {
            // Desktop/tablet behavior
            mainContent.classList.toggle('sidebar-open');
        }
    }
}

/**
 * Close to-do sidebar
 */
function closeTodoSidebarHandler() {
    if (todoSidebar && sidebarToggle && mainContent) {
        todoSidebar.classList.remove('open');
        sidebarToggle.classList.remove('active');
        
        // Handle mobile overlay
        if (isMobileView() && todoMobileOverlay) {
            todoMobileOverlay.classList.remove('active');
            // Restore body scroll
            document.body.style.overflow = '';
        } else {
            // Desktop/tablet behavior
            mainContent.classList.remove('sidebar-open');
        }
    }
}

/**
 * Fetch and display all tasks
 */
async function loadTasks() {
    try {
        const response = await fetch(API_ENDPOINTS.getTasks);
        const data = await response.json();

        if (data.success) {
            renderTasks(data.tasks);
        } else {
            console.error('Error loading tasks:', data.message);
            taskList.innerHTML = '<li class="loading">Error loading tasks</li>';
        }
    } catch (error) {
        console.error('Error fetching tasks:', error);
        taskList.innerHTML = '<li class="loading">Error loading tasks</li>';
    }
}

/**
 * Render tasks in the UI
 * @param {Array} tasks - Array of task objects
 */
function renderTasks(tasks) {
    // Clear current list
    taskList.innerHTML = '';
    
    // Show empty state if no tasks
    if (!tasks || tasks.length === 0) {
        emptyState.classList.remove('hidden');
        return;
    }
    
    emptyState.classList.add('hidden');
    
    // Render each task
    tasks.forEach(task => {
        const taskItem = createTaskElement(task);
        taskList.appendChild(taskItem);
    });
}

/**
 * Create a task DOM element
 * @param {Object} task - Task object
 * @returns {HTMLElement} Task list item
 */
function createTaskElement(task) {
    const li = document.createElement('li');
    li.className = `task-item ${task.status === 'completed' ? 'completed' : ''}`;
    li.dataset.taskId = task.id;
    
    // Ensure task title is properly handled
    const taskTitle = task.title && task.title.trim() ? task.title.trim() : 'Untitled Task';
    const taskDate = task.task_date ? formatDate(task.task_date) : '';
    
    // Create task HTML structure
    li.innerHTML = `
        <input 
            type="checkbox" 
            class="task-checkbox" 
            ${task.status === 'completed' ? 'checked' : ''}
            onchange="toggleTaskStatus(${task.id}, this.checked)"
        />
        <div class="task-content">
            <div class="task-title">${escapeHtml(taskTitle)}</div>
            ${taskDate ? `<div class="task-date">${taskDate}</div>` : ''}
        </div>
        <div class="task-actions">
            <button class="task-btn" onclick="deleteTask(${task.id})" title="Delete task">
                🗑️
            </button>
        </div>
    `;
    
    return li;
}

/**
 * Open task detail window for creating a new task
 */
function openTaskDetailWindow() {
    // Get current workspace ID from session or default to 1
    const workspaceId = window.currentWorkspaceId || 1;
    
    console.log('Attempting to open task detail window...');
    console.log('Workspace ID:', workspaceId);
    console.log('showTaskDetailWindow type:', typeof window.showTaskDetailWindow);
    
    // If the show function exists, call it immediately
    if (typeof window.showTaskDetailWindow === 'function') {
        console.log('Calling showTaskDetailWindow with workspace selection...');
        // Show workspace selection when opened from calendar page to-do sidebar
        window.showTaskDetailWindow({
            workspaceId: workspaceId,
            showWorkspaceSelection: true
        });
        console.log('Task detail window opened successfully with workspace selection');
        
        // Ensure modal close functionality is properly set up
        ensureModalCloseFunctionality();
        return;
    }

    // If initialize function exists, try initializing and then call the show function
    if (typeof window.initializeTaskDetailWindow === 'function') {
        try {
            console.log('initializeTaskDetailWindow found, calling it to wire modal handlers');
            window.initializeTaskDetailWindow();
        } catch (err) {
            console.warn('initializeTaskDetailWindow threw an error:', err);
        }

        if (typeof window.showTaskDetailWindow === 'function') {
            // Show workspace selection when opened from calendar page to-do sidebar
            window.showTaskDetailWindow({
                workspaceId: workspaceId,
                showWorkspaceSelection: true
            });
            console.log('Task detail window opened after initialization with workspace selection');
            
            // Ensure modal close functionality is properly set up
            ensureModalCloseFunctionality();
            return;
        }
    }

    // As a last resort, retry shortly to allow other scripts to finish loading, then fallback to direct modal display
    console.warn('showTaskDetailWindow function not available yet, retrying shortly...');
    setTimeout(() => {
        if (typeof window.showTaskDetailWindow === 'function') {
            // Show workspace selection when opened from calendar page to-do sidebar
            window.showTaskDetailWindow({
                workspaceId: workspaceId,
                showWorkspaceSelection: true
            });
            console.log('Task detail window opened after retry with workspace selection');
            
            // Ensure modal close functionality is properly set up
            ensureModalCloseFunctionality();
            return;
        }

        console.log('Available window functions:', Object.keys(window).filter(key => key.includes('Task') || key.includes('show')));
        const modal = document.getElementById('taskDetailModal');
        if (modal) {
            console.log('Found modal element, showing directly as fallback...');
            modal.style.display = 'flex';
            
            // Show workspace selection when opened from calendar page to-do sidebar (fallback)
            console.log('Setting up workspace selection in fallback mode...');
            const workspaceGroup = document.getElementById('workspaceSelectionGroup');
            const workspaceSelect = document.getElementById('workspaceSelect');
            
            if (workspaceGroup) {
                workspaceGroup.style.display = 'block';
                console.log('Workspace group shown in fallback mode');
            }
            
            if (workspaceSelect) {
                workspaceSelect.required = true;
                console.log('Workspace select made required in fallback mode');
                // Load workspaces
                loadWorkspacesForFallback();
            }
            
            // Focus on workspace select if available, otherwise task name
            if (workspaceSelect) {
                workspaceSelect.focus();
            } else {
                const taskNameInput = document.getElementById('taskNameInput');
                if (taskNameInput) taskNameInput.focus();
            }
            
            // Ensure modal close functionality is properly set up
            ensureModalCloseFunctionality();
        } else {
            console.error('Modal element not found after retry');
            alert('Task creation modal not available. Please refresh the page.');
        }
    }, 50);
}

/**
 * Load workspaces for fallback mode
 */
async function loadWorkspacesForFallback() {
    try {
        console.log('loadWorkspacesForFallback: Starting to load workspaces...');
        const response = await fetch('../Navbar/navbar_api.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'action=get_workspaces'
        });
        
        console.log('loadWorkspacesForFallback: Response status:', response.status);
        const data = await response.json();
        console.log('loadWorkspacesForFallback: Response data:', data);
        
        if (data.success && data.workspaces) {
            console.log('loadWorkspacesForFallback: Workspaces loaded successfully:', data.workspaces);
            populateWorkspaceDropdownForFallback(data.workspaces);
        } else {
            console.error('loadWorkspacesForFallback: Error loading workspaces:', data.message);
            // Show error message in modal
            const messageDiv = document.getElementById('taskMessage');
            if (messageDiv) {
                messageDiv.textContent = 'Error loading workspaces: ' + (data.message || 'Unknown error');
                messageDiv.className = 'task-message error';
                messageDiv.style.display = 'block';
            }
        }
    } catch (error) {
        console.error('loadWorkspacesForFallback: Error fetching workspaces:', error);
        // Show error message in modal
        const messageDiv = document.getElementById('taskMessage');
        if (messageDiv) {
            messageDiv.textContent = 'Error loading workspaces. Please try again.';
            messageDiv.className = 'task-message error';
            messageDiv.style.display = 'block';
        }
    }
}

/**
 * Populate workspace dropdown for fallback mode
 */
function populateWorkspaceDropdownForFallback(workspaces) {
    console.log('populateWorkspaceDropdownForFallback: Called with workspaces:', workspaces);
    const workspaceSelect = document.getElementById('workspaceSelect');
    
    if (!workspaceSelect) {
        console.error('populateWorkspaceDropdownForFallback: Workspace select element not found');
        return;
    }
    
    console.log('populateWorkspaceDropdownForFallback: Found workspace select element');
    
    // Clear existing options except the first one
    workspaceSelect.innerHTML = '<option value="">Select Workspace</option>';
    
    // Add workspace options
    workspaces.forEach(workspace => {
        console.log('populateWorkspaceDropdownForFallback: Adding workspace:', workspace);
        const option = document.createElement('option');
        option.value = workspace.WorkSpaceID;
        option.textContent = workspace.WorkspaceName; // Fixed: use WorkspaceName instead of Name
        workspaceSelect.appendChild(option);
        console.log('populateWorkspaceDropdownForFallback: Added workspace option:', workspace.WorkspaceName, 'ID:', workspace.WorkSpaceID);
    });
    
    console.log('populateWorkspaceDropdownForFallback: Workspace dropdown populated with', workspaces.length, 'workspaces');
}

/**
 * Ensure modal close functionality is properly set up
 */
function ensureModalCloseFunctionality() {
    // Wait a bit for the modal to be rendered
    setTimeout(() => {
        const modal = document.getElementById('taskDetailModal');
        const closeBtn = document.getElementById('closeTaskDetailModal');
        const cancelBtn = document.getElementById('cancelTaskBtn');
        
        if (modal) {
            console.log('Setting up modal close functionality...');
            
            // Re-ensure close button functionality
            if (closeBtn && !closeBtn.hasAttribute('data-listener-added')) {
                closeBtn.addEventListener('click', () => {
                    console.log('Close button clicked');
                    if (typeof window.hideTaskDetailWindow === 'function') {
                        window.hideTaskDetailWindow();
                    } else {
                        modal.style.display = 'none';
                    }
                });
                closeBtn.setAttribute('data-listener-added', 'true');
            }
            
            // Re-ensure cancel button functionality
            if (cancelBtn && !cancelBtn.hasAttribute('data-listener-added')) {
                cancelBtn.addEventListener('click', () => {
                    console.log('Cancel button clicked');
                    if (typeof window.hideTaskDetailWindow === 'function') {
                        window.hideTaskDetailWindow();
                    } else {
                        modal.style.display = 'none';
                    }
                });
                cancelBtn.setAttribute('data-listener-added', 'true');
            }
            
            // Re-ensure click outside to close functionality
            if (!modal.hasAttribute('data-listener-added')) {
                modal.addEventListener('click', (e) => {
                    if (e.target === modal) {
                        console.log('Modal backdrop clicked');
                        if (typeof window.hideTaskDetailWindow === 'function') {
                            window.hideTaskDetailWindow();
                        } else {
                            modal.style.display = 'none';
                        }
                    }
                });
                modal.setAttribute('data-listener-added', 'true');
            }
            
            // Add keyboard escape key handler
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && modal.style.display === 'flex') {
                    console.log('Escape key pressed, closing modal');
                    if (typeof window.hideTaskDetailWindow === 'function') {
                        window.hideTaskDetailWindow();
                    } else {
                        modal.style.display = 'none';
                    }
                }
            });
            
            console.log('Modal close functionality set up successfully');
        }
    }, 100);
}

/**
 * Refresh tasks after modal operations
 * This function can be called from the taskDetailWindow after task creation/update
 */
function refreshTasksAfterModal() {
    // Reload tasks to show any new ones created via the modal
    loadTasks();
}

/**
 * Toggle task completion status
 * @param {number} taskId - Task ID
 * @param {boolean} isCompleted - Completion status
 */
async function toggleTaskStatus(taskId, isCompleted) {
    try {
        const response = await fetch(API_ENDPOINTS.updateTask, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                id: taskId,
                status: isCompleted ? 'completed' : 'pending'
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            // Update UI
            const taskItem = document.querySelector(`[data-task-id="${taskId}"]`);
            if (taskItem) {
                if (isCompleted) {
                    taskItem.classList.add('completed');
                } else {
                    taskItem.classList.remove('completed');
                }
            }
        } else {
            alert('Error updating task: ' + data.message);
            await loadTasks(); // Reload to sync state
        }
    } catch (error) {
        console.error('Error updating task:', error);
        alert('Error updating task. Please try again.');
    }
}

/**
 * Delete a task
 * @param {number} taskId - Task ID
 */
async function deleteTask(taskId) {
    if (!confirm('Are you sure you want to delete this task?')) {
        return;
    }
    
    try {
        const response = await fetch(API_ENDPOINTS.deleteTask, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ id: taskId })
        });
        
        const data = await response.json();
        
        if (data.success) {
            await loadTasks();
        } else {
            alert('Error deleting task: ' + data.message);
        }
    } catch (error) {
        console.error('Error deleting task:', error);
        alert('Error deleting task. Please try again.');
    }
}

/**
 * Escape HTML to prevent XSS
 * @param {string} text - Text to escape
 * @returns {string} Escaped text
 */
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

/**
 * Format date for display
 * @param {string} dateString - Date string
 * @returns {string} Formatted date
 */
function formatDate(dateString) {
    const date = new Date(dateString);
    const today = new Date();
    const tomorrow = new Date(today);
    tomorrow.setDate(tomorrow.getDate() + 1);
    
    // Check if date is today or tomorrow
    if (date.toDateString() === today.toDateString()) {
        return 'Today';
    } else if (date.toDateString() === tomorrow.toDateString()) {
        return 'Tomorrow';
    }
    
    // Format as "Mon, Oct 8"
    return date.toLocaleDateString('en-US', { 
        weekday: 'short', 
        month: 'short', 
        day: 'numeric' 
    });
}

// Event Listeners
if (sidebarToggle) {
    sidebarToggle.addEventListener('click', toggleTodoSidebar);
}
if (closeSidebar) {
    closeSidebar.addEventListener('click', closeTodoSidebarHandler);
}
if (openTaskDetailBtn) {
    openTaskDetailBtn.addEventListener('click', openTaskDetailWindow);
}

// Mobile overlay click to close sidebar
if (todoMobileOverlay) {
    todoMobileOverlay.addEventListener('click', closeTodoSidebarHandler);
}

// Handle window resize to adjust sidebar behavior
window.addEventListener('resize', () => {
    // If resizing from mobile to desktop, close mobile overlay
    if (!isMobileView() && todoMobileOverlay) {
        todoMobileOverlay.classList.remove('active');
        document.body.style.overflow = '';
    }
});

// Initialize: Load tasks on page load
document.addEventListener('DOMContentLoaded', () => {
    loadTasks();
    
    // Set up form submission for fallback mode
    const form = document.getElementById('taskDetailForm');
    if (form) {
        form.addEventListener('submit', handleFallbackTaskSubmit);
    }
});

/**
 * Handle form submission in fallback mode
 */
async function handleFallbackTaskSubmit(event) {
    event.preventDefault();
    
    console.log('Handling fallback task submission...');
    
    const taskName = document.getElementById('taskNameInput').value.trim();
    const description = document.getElementById('taskDescriptionInput').value.trim();
    const startDate = document.getElementById('startDateInput').value;
    const deadline = document.getElementById('deadlineInput').value;
    const priority = document.getElementById('prioritySelect').value;
    const status = document.getElementById('statusSelect').value;
    const workspaceSelect = document.getElementById('workspaceSelect');
    
    // Get workspace ID from dropdown if workspace selection is shown
    let workspaceId = window.currentWorkspaceId || 1;
    if (workspaceSelect && workspaceSelect.style.display !== 'none' && workspaceSelect.offsetParent !== null) {
        workspaceId = workspaceSelect.value;
        console.log('Using workspace from dropdown:', workspaceId);
    }
    
    console.log('Task submission data:', {
        taskName, description, startDate, deadline, priority, status, workspaceId
    });
    
    // Basic validation
    if (!taskName) {
        showFallbackMessage('Task name is required', 'error');
        return;
    }
    
    if (!startDate) {
        showFallbackMessage('Start date is required', 'error');
        return;
    }
    
    if (!deadline) {
        showFallbackMessage('Deadline is required', 'error');
        return;
    }
    
    if (workspaceSelect && workspaceSelect.required && !workspaceId) {
        showFallbackMessage('Please select a workspace', 'error');
        return;
    }
    
    try {
        showFallbackMessage('Saving task...', 'info');
        
        const formData = new FormData();
        formData.append('action', 'create_task');
        formData.append('workspace_id', workspaceId);
        formData.append('task_name', taskName);
        formData.append('task_description', description);
        formData.append('start_date', startDate);
        formData.append('deadline', deadline);
        formData.append('priority', priority);
        formData.append('status', status);
        
        const response = await fetch('../Navbar/navbar_api.php', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            showFallbackMessage('Task created successfully!', 'success');
            
            // Close modal after successful save
            setTimeout(() => {
                const modal = document.getElementById('taskDetailModal');
                if (modal) {
                    modal.style.display = 'none';
                }
                // Refresh tasks
                loadTasks();
                // Refresh calendar if available
                if (window.fullCalendarIntegration && typeof window.fullCalendarIntegration.refreshEvents === 'function') {
                    window.fullCalendarIntegration.refreshEvents();
                }
            }, 1500);
        } else {
            showFallbackMessage(result.message || 'Failed to create task', 'error');
        }
    } catch (error) {
        console.error('Error saving task:', error);
        showFallbackMessage('Error saving task. Please try again.', 'error');
    }
}

/**
 * Show message in fallback mode
 */
function showFallbackMessage(message, type) {
    const messageDiv = document.getElementById('taskMessage');
    if (messageDiv) {
        messageDiv.textContent = message;
        messageDiv.className = `task-message ${type}`;
        messageDiv.style.display = 'block';
    }
}

// Export refresh function globally so taskDetailWindow can call it
window.refreshTasksAfterModal = refreshTasksAfterModal;

// Export ensureModalCloseFunctionality globally for debugging
window.ensureModalCloseFunctionality = ensureModalCloseFunctionality;

// Debug function to check modal state
window.debugModalState = function() {
    const modal = document.getElementById('taskDetailModal');
    const closeBtn = document.getElementById('closeTaskDetailModal');
    const cancelBtn = document.getElementById('cancelTaskBtn');
    
    console.log('Modal Debug Info:');
    console.log('- Modal element:', modal);
    console.log('- Modal display style:', modal ? modal.style.display : 'not found');
    console.log('- Close button:', closeBtn);
    console.log('- Cancel button:', cancelBtn);
    console.log('- Close button has listener:', closeBtn ? closeBtn.hasAttribute('data-listener-added') : 'not found');
    console.log('- Cancel button has listener:', cancelBtn ? cancelBtn.hasAttribute('data-listener-added') : 'not found');
    console.log('- Modal has listener:', modal ? modal.hasAttribute('data-listener-added') : 'not found');
};

