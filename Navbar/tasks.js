/**
 * task management
 * handles task actions like pinning, deleting and access control
 */

/**
 * handle pinning/unpinning tasks
 */
function handlePinTask(taskItem) {
    if (!taskItem) return;
    
    const isPinned = taskItem.dataset.pinned === 'true';
    
    if (isPinned) {
        // unpin the task
        taskItem.dataset.pinned = 'false';
        
        
            // move it to the end of the submenu
        const workspaceItem = taskItem.closest('.workspace-item');
        const submenu = workspaceItem.querySelector('.workspace-submenu');
        submenu.appendChild(taskItem);
        
        // Update dropdown text back to "Pin"
        const dropdown = taskItem.querySelector('.dropdown');
        const pinButton = dropdown.querySelector('[data-action="pin"]');
        pinButton.textContent = 'Pin';
        pinButton.dataset.pinText = 'Pin';
        
        console.log('Task unpinned');
    } else {
        // Pin the task
        taskItem.dataset.pinned = 'true';
        
        // Move task to top of submenu
        const workspaceItem = taskItem.closest('.workspace-item');
        const submenu = workspaceItem.querySelector('.workspace-submenu');
        submenu.insertBefore(taskItem, submenu.firstChild);
        
        // "Unpin" the task
        const dropdown = taskItem.querySelector('.dropdown');
        const pinButton = dropdown.querySelector('[data-action="pin"]');
        pinButton.textContent = 'Unpin';
        pinButton.dataset.pinText = 'Unpin';
        
        console.log('Task pinned to top');
    }
}

/**
 * Handle deleting task - REMOVED
 * Delete functionality has been removed and will be reimplemented
 */
// handleDeleteTask function removed - to be reimplemented

/**
 * handle inviting members 
 */
function handleInviteMember(workspaceItem) {
    console.log('Invite member functionality - placeholder');
    alert('Invite member functionality would be implemented here');
}

/**
 * handle granting access 
 */
function handleGrantAccess(taskItem) {
    console.log('Grant access functionality - placeholder');
    alert('Grant access functionality would be implemented here');
}

// export to other file
window.handlePinTask = handlePinTask;
// window.handleDeleteTask = handleDeleteTask;
window.handleInviteMember = handleInviteMember;
window.handleGrantAccess = handleGrantAccess;
