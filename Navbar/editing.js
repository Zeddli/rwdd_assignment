/**
 * Initialize editable elements (workspace names, task names)
 * This used to set up click-to-rename, but now renaming only works via dropdown menus
 */
// function initializeEditableElements() {
//     // Click-to-rename disabled; renaming only via dropdown action.
//     // All renaming now happens through dropdown menu > "Rename" option
// }

/**
 * Start editing an element (turn text into input field)
 */
function startEditing(element) {
    console.log('startEditing called for element:', element);
    console.log('Element text content:', element ? element.textContent : 'null');
    console.log('allowProgrammaticEdit:', SidebarState.allowProgrammaticEdit);
    console.log('editingElement:', SidebarState.editingElement);
        
    // security check - only allow editing when triggered from dropdown menu
    if (!SidebarState.allowProgrammaticEdit) {
        console.log('startEditing blocked - allowProgrammaticEdit is false');
        return;
    }
    
    // don't allow editing multiple things at once
    if (SidebarState.editingElement) {
        console.log('startEditing blocked - another element is already being edited');
        return;
    }
    
    console.log('Proceeding with editing...');
    // mark this element as being edited and remember the original text
    SidebarState.editingElement = element;
    const currentText = element.textContent;
    element.dataset.originalValue = currentText;
    
    // create a text input field to replace the text
    const input = document.createElement('input');
    input.type = 'text';
    input.value = currentText;
    input.className = 'edit-input';
    
    // style the input to look like the original text but with a blue border
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
    
    // replace the text with the input field
    element.textContent = '';
    element.appendChild(input);
    element.classList.add('editing'); // add CSS class for styling
    
    // put cursor in the input and select all text so user can type immediately
    input.focus();
    input.select();
    
    // set up event listeners for the input field
    // when user clicks away, save the changes
    input.addEventListener('blur', () => finishEditing(element, input.value));
    
    // handle keyboard shortcuts
    input.addEventListener('keydown', (e) => {
        if (e.key === 'Enter') {
            // enter key = save changes
            finishEditing(element, input.value);
        } else if (e.key === 'Escape') {
            cancelEditing(element, currentText);
        }
    });
}

/**
 * finish editing an element 
 * saves the new name to the database and get back from input to text
 */
function finishEditing(element, newValue) {
    if (!SidebarState.editingElement) return;
    
    const trimmedValue = newValue.trim();
    const originalValue = element.dataset.originalValue;
    
    if (trimmedValue && trimmedValue !== originalValue) {
        // save to database
        saveRename(element, trimmedValue, originalValue);
    } else {
        // no change, just cleanup
        element.textContent = originalValue;
        cleanupEditing(element);
    }
}

/**
 * save rename to database
 * what type of thing is being renamed (workspace/task/goal)
 * send the new name to the server via ajax
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

    // show loading state
    element.textContent = 'Saving...';
    
    // save via api
    fetch('/protask/Navbar/navbar_api.php', {
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
 * cleanup editing state
 */
function cleanupEditing(element) {
    delete element.dataset.originalValue;
    element.classList.remove('editing');
    SidebarState.editingElement = null;
}

/**
 * cancel editing an element
 */
function cancelEditing(element, originalValue) {
    if (!SidebarState.editingElement) return;
    
    element.textContent = originalValue;
    cleanupEditing(element);
}

/**
 * handle renaming elements
 * main entry point for renaming
 * called when user clicks "Rename" in dropdown
 * temporarily allows editing and starts the inline editing process
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
    
    // temporarily allow editing and remember original text
    element.dataset.originalValue = element.textContent;
    SidebarState.allowProgrammaticEdit = true;
    console.log('allowProgrammaticEdit set to:', SidebarState.allowProgrammaticEdit);
    
    // start the inline editing process
    startEditing(element);
    
    // reset the security flag after editing
    SidebarState.allowProgrammaticEdit = false;
    console.log('allowProgrammaticEdit reset to:', SidebarState.allowProgrammaticEdit);
}

// export for use in other modules
window.initializeEditableElements = initializeEditableElements;
window.startEditing = startEditing;
window.finishEditing = finishEditing;
window.cancelEditing = cancelEditing;
window.handleRename = handleRename;
