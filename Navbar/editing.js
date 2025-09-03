
/**
 * init editable elements (workspace names, task names)
 */
function initializeEditableElements() {
    const editableElements = document.querySelectorAll('[data-editable="true"]');
    
    editableElements.forEach(element => {
        element.addEventListener('click', () => {
            if (!SidebarState.editingElement) {
                startEditing(element);
            }
        });
    });
}

/**
 * Start editing an element
 */
function startEditing(element) {
    if (SidebarState.editingElement) return;
    
    SidebarState.editingElement = element;
    const currentText = element.textContent;
    
    // Create input field
    const input = document.createElement('input');
    input.type = 'text';
    input.value = currentText;
    input.className = 'edit-input';
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
    
    // Replace text with input
    element.textContent = '';
    element.appendChild(input);
    element.classList.add('editing');
    
    // Focus and select text
    input.focus();
    input.select();
    
    // Handle input events
    input.addEventListener('blur', () => finishEditing(element, input.value));
    input.addEventListener('keydown', (e) => {
        if (e.key === 'Enter') {
            finishEditing(element, input.value);
        } else if (e.key === 'Escape') {
            cancelEditing(element, currentText);
        }
    });
}

/**
 * Finish editing an element
 */
function finishEditing(element, newValue) {
    if (!SidebarState.editingElement) return;
    
    const trimmedValue = newValue.trim();
    if (trimmedValue && trimmedValue !== element.textContent) {
        element.textContent = trimmedValue;
        console.log(`Renamed to: ${trimmedValue}`);
    }
    
    element.classList.remove('editing');
    SidebarState.editingElement = null;
}

/**
 * Cancel editing an element
 */
function cancelEditing(element, originalValue) {
    if (!SidebarState.editingElement) return;
    
    element.textContent = originalValue;
    element.classList.remove('editing');
    SidebarState.editingElement = null;
}

/**
 * Handle renaming elements
 */
function handleRename(element) {
    if (element && element.dataset.editable === 'true') {
        startEditing(element);
    }
}

// Export for use in other modules
window.initializeEditableElements = initializeEditableElements;
window.startEditing = startEditing;
window.finishEditing = finishEditing;
window.cancelEditing = cancelEditing;
window.handleRename = handleRename;
