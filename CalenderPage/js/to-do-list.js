/**
 * To-Do List Client-Side Functionality
 * Handles sidebar interactions, task management, and API calls
 */

// DOM Elements
const sidebarToggle = document.getElementById('todoSidebarToggle');
const todoSidebar = document.getElementById('todoSidebar');
const closeSidebar = document.getElementById('closeSidebar');
const mainContent = document.getElementById('mainContent');
const taskForm = document.getElementById('taskForm');
const taskList = document.getElementById('taskList');
const emptyState = document.getElementById('emptyState');

// API endpoints
const API_BASE = 'php/';
const API_ENDPOINTS = {
    getTasks: API_BASE + 'getTasks.php',
    createTask: API_BASE + 'createTask.php',
    updateTask: API_BASE + 'updateTask.php',
    deleteTask: API_BASE + 'deleteTask.php'
};

/**
 * Toggle to-do sidebar open/close
 */
function toggleTodoSidebar() {
    if (todoSidebar && sidebarToggle && mainContent) {
        todoSidebar.classList.toggle('open');
        sidebarToggle.classList.toggle('active');
        mainContent.classList.toggle('sidebar-open');
    }
}

/**
 * Close to-do sidebar
 */
function closeTodoSidebarHandler() {
    if (todoSidebar && sidebarToggle && mainContent) {
        todoSidebar.classList.remove('open');
        sidebarToggle.classList.remove('active');
        mainContent.classList.remove('sidebar-open');
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
    
    // Create task HTML structure
    li.innerHTML = `
        <input 
            type="checkbox" 
            class="task-checkbox" 
            ${task.status === 'completed' ? 'checked' : ''}
            onchange="toggleTaskStatus(${task.id}, this.checked)"
        />
        <div class="task-content">
            <div class="task-title">${escapeHtml(task.title)}</div>
            ${task.task_date ? `<div class="task-date">${formatDate(task.task_date)}</div>` : ''}
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
 * Create a new task
 * @param {Event} e - Form submit event
 */
async function createTask(e) {
    e.preventDefault();
    
    const titleInput = document.getElementById('taskTitle');
    const dateInput = document.getElementById('taskDate');
    
    const taskData = {
        title: titleInput.value.trim(),
        task_date: dateInput.value || null
    };
    
    if (!taskData.title) return;
    
    try {
        const response = await fetch(API_ENDPOINTS.createTask, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(taskData)
        });
        
        const data = await response.json();
        
        if (data.success) {
            // Clear form
            titleInput.value = '';
            dateInput.value = '';
            
            // Reload tasks
            await loadTasks();
        } else {
            alert('Error creating task: ' + data.message);
        }
    } catch (error) {
        console.error('Error creating task:', error);
        alert('Error creating task. Please try again.');
    }
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
if (taskForm) {
    taskForm.addEventListener('submit', createTask);
}

// Initialize: Load tasks on page load
document.addEventListener('DOMContentLoaded', () => {
    loadTasks();
});

