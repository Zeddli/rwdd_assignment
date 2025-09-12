
/**
 * Inline Editing System
 * This handles renaming workspaces and tasks by turning them into text input fields
 * Users can rename by clicking "Rename" in dropdown menus, then typing new names
 */

/**
 * Initialize editable elements (workspace names, task names)
 * This used to set up click-to-rename, but now renaming only works via dropdown menus
 * Keeping this function so other code doesn't break, but it doesn't do anything
 */
function initializeEditableElements() {
    // Click-to-rename disabled; renaming only via dropdown action.
    // All renaming now happens through dropdown menu > "Rename" option
}

/**
 * Start editing an element (turn text into input field)
 * This gets called when user clicks "Rename" from a dropdown menu
 * It replaces the text with a text input field so user can type a new name
 */
function startEditing(element) {
    console.log('startEditing called for element:', element);
    console.log('Element text content:', element ? element.textContent : 'null');
    console.log('allowProgrammaticEdit:', SidebarState.allowProgrammaticEdit);
    console.log('editingElement:', SidebarState.editingElement);
        
    // Security check - only allow editing when triggered from dropdown menu
    if (!SidebarState.allowProgrammaticEdit) {
        console.log('startEditing blocked - allowProgrammaticEdit is false');
        return;
    }
    
    // Don't allow editing multiple things at once
    if (SidebarState.editingElement) {
        console.log('startEditing blocked - another element is already being edited');
        return;
    }
    
    console.log('Proceeding with editing...');
    // Mark this element as being edited and remember the original text
    SidebarState.editingElement = element;
    const currentText = element.textContent;
    element.dataset.originalValue = currentText;
    
    // Create a text input field to replace the text
    const input = document.createElement('input');
    input.type = 'text';
    input.value = currentText;
    input.className = 'edit-input';
    
    // Style the input to look like the original text but with a blue border
    input.style.cssText = `
        border: none;
        background: transparent;
        font-size: inherit;
        font-family: inherit;
        color: inherit;
        width: 100%;
        outline: none;
        padding: 2px 4px;
        border-radius: 3px;
        background-color: #e3f2fd;
        border: 2px solid #2196f3;
    `;
    
    // Replace the text with the input field
    element.textContent = '';
    element.appendChild(input);
    element.classList.add('editing'); // Add CSS class for styling
    
    // Put cursor in the input and select all text so user can type immediately
    input.focus();
    input.select();
    
    // Set up event listeners for the input field
    // When user clicks away, save the changes
    input.addEventListener('blur', () => finishEditing(element, input.value));
    
    // Handle keyboard shortcuts
    input.addEventListener('keydown', (e) => {
        if (e.key === 'Enter') {
            // Enter key = save changes
            finishEditing(element, input.value);
        } else if (e.key === 'Escape') {
            // Escape key = cancel changes
            cancelEditing(element, currentText);
        }
    });
}

/**
 * Finish editing an element (user pressed Enter or clicked away)
 * This saves the new name to the database and switches back from input to text
 */
function finishEditing(element, newValue) {
    if (!SidebarState.editingElement) return;
    
    const trimmedValue = newValue.trim();
    const originalValue = element.dataset.originalValue;
    
    if (trimmedValue && trimmedValue !== originalValue) {
        // Save to database
        saveRename(element, trimmedValue, originalValue);
    } else {
        // No change, just cleanup
        element.textContent = originalValue;
        cleanupEditing(element);
    }
}

/**
 * Save rename to database
 * This figures out what type of thing is being renamed (workspace/task/goal)
 * and sends the new name to the server via AJAX
 */
function saveRename(element, newValue, originalValue) {
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
        cleanupEditing(element);
        return;
    }
    
    // Show loading state
    element.textContent = 'Saving...';
    
    // Save via API
    fetch('navbar_api.php', {
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
            console.log(`Successfully renamed to: ${newValue}`);
        } else {
            console.error('Failed to rename:', data.message);
            alert('Failed to rename: ' + data.message);
            element.textContent = originalValue;
        }
        cleanupEditing(element);
    })
    .catch(error => {
        console.error('Error renaming:', error);
        alert('Error renaming. Please try again.');
        element.textContent = originalValue;
        cleanupEditing(element);
    });
}

/**
 * Cleanup editing state
 */
function cleanupEditing(element) {
    delete element.dataset.originalValue;
    element.classList.remove('editing');
    SidebarState.editingElement = null;
}

/**
 * Cancel editing an element
 */
function cancelEditing(element, originalValue) {
    if (!SidebarState.editingElement) return;
    
    element.textContent = originalValue;
    cleanupEditing(element);
}

/**
 * Handle renaming elements
 * This is the main entry point for renaming - called when user clicks "Rename" in dropdown
 * It temporarily allows editing and starts the inline editing process
 */
function handleRename(element) {
    console.log('handleRename called with element:', element);
    if (!element) {
        console.log('handleRename: element is null, returning');
        return;
    }
    console.log('Element text content:', element.textContent);
    console.log('Element class list:', element.classList);
    console.log('Element tag name:', element.tagName);
    
    // Temporarily allow editing (security flag) and remember original text
    element.dataset.originalValue = element.textContent;
    SidebarState.allowProgrammaticEdit = true;
    console.log('allowProgrammaticEdit set to:', SidebarState.allowProgrammaticEdit);
    
    // Start the inline editing process
    startEditing(element);
    
    // Reset the security flag immediately after (editing is now in progress)
    SidebarState.allowProgrammaticEdit = false;
    console.log('allowProgrammaticEdit reset to:', SidebarState.allowProgrammaticEdit);
}

// Export for use in other modules
window.initializeEditableElements = initializeEditableElements;
window.startEditing = startEditing;
window.finishEditing = finishEditing;
window.cancelEditing = cancelEditing;
window.handleRename = handleRename;
