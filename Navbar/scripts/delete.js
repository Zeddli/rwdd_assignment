/**
 * Delete functionality for workspaces and tasks
 * Handles confirmation popups and API calls
 */

/**
 * Show delete confirmation popup
 * Creates and displays a modal popup for delete confirmation
 */
function showDeletePopup(options) {
    // Create popup HTML structure
    const popupHTML = `
        <div class="delete-popup-overlay" id="deletePopupOverlay">
            <div class="delete-popup">
                <div class="delete-popup-header">
                    <svg class="delete-popup-icon" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M1 21h22L12 2 1 21zm12-3h-2v-2h2v2zm0-4h-2v-4h2v4z"/>
                    </svg>
                    <h3 class="delete-popup-title">${options.title}</h3>
                </div>
                <p class="delete-popup-message">${options.message}</p>
                ${options.warning ? `<p class="delete-popup-warning">${options.warning}</p>` : ''}
                <div class="delete-popup-buttons">
                    <button class="delete-popup-btn delete-popup-cancel" onclick="hideDeletePopup()">Cancel</button>
                    <button class="delete-popup-btn delete-popup-delete" onclick="confirmDelete()">Delete</button>
                </div>
            </div>
        </div>
    `;
    
    // Add popup to page
    document.body.insertAdjacentHTML('beforeend', popupHTML);
    
    // Show popup with animation
    const overlay = document.getElementById('deletePopupOverlay');
    setTimeout(() => overlay.classList.add('active'), 10);
    
    // Store callback function for when user confirms
    window.currentDeleteCallback = options.onConfirm;
}

/**
 * Hide and remove delete popup
 * Removes the popup from DOM with animation
 */
function hideDeletePopup() {
    const overlay = document.getElementById('deletePopupOverlay');
    if (overlay) {
        overlay.classList.remove('active');
        setTimeout(() => overlay.remove(), 300);
    }
    window.currentDeleteCallback = null;
}

/**
 * Handle delete confirmation
 * Called when user clicks "Delete" button in popup
 */
function confirmDelete() {
    if (window.currentDeleteCallback) {
        window.currentDeleteCallback();
    }
    hideDeletePopup();
}

/**
 * Handle task deletion
 * Shows confirmation popup and calls API to delete task
 */
function handleDeleteTask(taskItem) {
    if (!taskItem) return;
    
    const taskID = taskItem.dataset.taskId;
    const taskName = taskItem.querySelector('.task-name').textContent;
    
    showDeletePopup({
        title: 'Delete Task',
        message: `Are you sure you want to delete "${taskName}"?`,
        warning: 'This action cannot be undone.',
        onConfirm: () => deleteTaskFromDatabase(taskID, taskItem)
    });
}

/**
 * Handle workspace deletion  
 * Shows confirmation popup and calls API to delete workspace
 */
function handleDeleteWorkspace(workspaceItem) {
    if (!workspaceItem) return;
    
    const workspaceID = workspaceItem.dataset.workspaceId;
    const workspaceName = workspaceItem.querySelector('.workspace-name').textContent;
    
    showDeletePopup({
        title: 'Delete Workspace',
        message: `Are you sure you want to delete "${workspaceName}"?`,
        warning: 'This will delete all tasks and goals in this workspace. This cannot be undone.',
        onConfirm: () => deleteWorkspaceFromDatabase(workspaceID, workspaceItem)
    });
}

/**
 * Delete task from database via API
 * Makes AJAX call to delete task and updates UI
 */
function deleteTaskFromDatabase(taskID, taskItem) {
    fetch('../Navbar/navbar_api.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `action=delete_task&task_id=${taskID}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Remove task from UI
            taskItem.remove();
            console.log('Task deleted successfully');
        } else {
            alert('Failed to delete task: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error deleting task:', error);
        alert('Error deleting task. Please try again.');
    });
}

/**
 * Delete workspace from database via API
 * Makes AJAX call to delete workspace and updates UI
 */
function deleteWorkspaceFromDatabase(workspaceID, workspaceItem) {
    fetch('../Navbar/navbar_api.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `action=delete_workspace&workspace_id=${workspaceID}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Remove workspace from UI
            workspaceItem.remove();
            
            // Check if no workspaces remain
            const remainingWorkspaces = document.querySelectorAll('.workspace-item');
            if (remainingWorkspaces.length === 0) {
                // Show "no workspace" message
                const noWorkspaceHTML = `
                    <div class="no-workspace-message">
                        <p>You don't have any workspace yet.</p>
                        <button class="create-first-workspace-btn" onclick="addNewWorkspace()">Create Workspace</button>
                    </div>
                `;
                DOM.workspacesContainer.innerHTML = noWorkspaceHTML;
            }
            
            console.log('Workspace deleted successfully');
        } else {
            alert('Failed to delete workspace: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error deleting workspace:', error);
        alert('Error deleting workspace. Please try again.');
    });
}

// Export functions for global access
window.handleDeleteTask = handleDeleteTask;
window.handleDeleteWorkspace = handleDeleteWorkspace;
window.showDeletePopup = showDeletePopup;
window.hideDeletePopup = hideDeletePopup;
window.confirmDelete = confirmDelete; 