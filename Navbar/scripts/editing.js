/**
 * Simple rename functionality - only works through dropdown menus
 * No click-to-rename, no complex state management
 */

/**
 * Start renaming an element (workspace, task, or goal)
 * Called only from dropdown menu "Rename" action
 */
function startRename(element) {
    if (!element) return;
    
    const currentText = element.textContent.trim();
    
    // Create input field
    const input = document.createElement('input');
    input.type = 'text';
    input.value = currentText;
    input.className = 'rename-input';
    
    // Style the input
    input.style.cssText = `
        border: 2px solid #2196f3;
        background: #e3f2fd;
        font-size: inherit;
        font-family: inherit;
        color: inherit;
        width: 100%;
        outline: none;
        padding: 2px 4px;
        border-radius: 3px;
    `;
    
    // Replace text with input
    element.textContent = '';
    element.appendChild(input);
    input.focus();
    input.select();
    
    // Handle input events
    input.addEventListener('blur', () => finishRename(element, input.value, currentText));
    input.addEventListener('keydown', (e) => {
        if (e.key === 'Enter') {
            finishRename(element, input.value, currentText);
        } else if (e.key === 'Escape') {
            cancelRename(element, currentText);
        }
    });
}

/**
 * Finish renaming - save to database or cancel
 */
function finishRename(element, newValue, originalValue) {
    const trimmedValue = newValue.trim();
    
    if (trimmedValue && trimmedValue !== originalValue) {
        // Save to database
        saveRename(element, trimmedValue, originalValue);
    } else {
        // No change or empty - restore original
        element.textContent = originalValue;
    }
}

/**
 * Cancel renaming - restore original text
 */
function cancelRename(element, originalValue) {
    element.textContent = originalValue;
}

/**
 * Save rename to database via API
 */
function saveRename(element, newValue, originalValue) {
    // Determine what type of element this is
    const isWorkspace = element.classList.contains('workspace-name');
    const isTask = element.classList.contains('task-name');
    const isGoal = element.classList.contains('goal-name');
    
    let action, idParam, id;
    
    if (isWorkspace) {
        action = 'rename_workspace';
        idParam = 'workspace_id';
        const workspaceItem = element.closest('.workspace-item');
        id = workspaceItem.dataset.workspaceId;
    } else if (isTask) {
        action = 'rename_task';
        idParam = 'task_id';
        const taskItem = element.closest('.task-item');
        id = taskItem.dataset.taskId;
    } else if (isGoal) {
        action = 'rename_goal';
        idParam = 'goal_id';
        const goalItem = element.closest('.goal-item');
        id = goalItem.dataset.goalId;
    } else {
        console.error('Unknown element type for renaming');
        element.textContent = originalValue;
        return;
    }

    // Show saving state
    element.textContent = 'Saving...';
    
    // Send to API
    fetch('../Navbar/navbar_api.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `action=${action}&${idParam}=${id}&new_name=${encodeURIComponent(newValue)}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            element.textContent = newValue;
        } else {
            console.error('Failed to rename:', data.message);
            alert('Failed to rename: ' + data.message);
            element.textContent = originalValue;
        }
    })
    .catch(error => {
        console.error('Error renaming:', error);
        alert('Error renaming. Please try again.');
        element.textContent = originalValue;
    });
}

/**
 * Handle rename action from dropdown menu
 * Main entry point for renaming - called from dropdowns.js
 */
function handleRename(element) {
    if (!element) return;
    startRename(element);
}

// Export functions for use in dropdowns.js
window.handleRename = handleRename;
