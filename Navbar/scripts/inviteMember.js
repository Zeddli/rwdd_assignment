/**
 * Invite Member Functionality
 * Handles the invite member modal and permission checking
 */

var currentWorkspaceId = null;

/**
 * Handle invite member action from workspace dropdown
 */
function handleInviteMember(workspaceItem) {
    if (!workspaceItem) {
        console.error('No workspace item provided');
        return;
    }
    
    // Get workspace ID
    currentWorkspaceId = workspaceItem.dataset.workspaceId;
    
    if (!currentWorkspaceId) {
        console.error('No workspace ID found');
        alert('Error: Could not find workspace ID');
        return;
    }
    
    console.log('Inviting member to workspace:', currentWorkspaceId);
    
    // First check permission before showing modal
    checkPermissionAndShowModal(currentWorkspaceId);
}

/**
 * Check user permission before showing invite modal
 */
async function checkPermissionAndShowModal(workspaceId) {
    try {
        const formData = new FormData();
        formData.append('workspaceID', workspaceId);
        
        const response = await fetch('../Navbar/functions/inviteMember.php', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            // User has permission, show the modal
            showInviteModal();
        } else {
            // User doesn't have permission
            alert(result.error || 'You do not have permission to invite members');
        }
    } catch (error) {
        console.error('Error checking permission:', error);
        alert('Error checking permissions. Please try again.');
    }
}

/**
 * Show the invite member modal
 */
function showInviteModal() {
    const modal = document.getElementById('searchMemberModal');
    if (modal) {
        modal.style.display = 'flex';
        // Focus on email input
        const emailInput = document.getElementById('memberEmailInput');
        if (emailInput) {
            emailInput.focus();
        }
    }
}

/**
 * Hide the invite member modal
 */
function hideInviteModal() {
    const modal = document.getElementById('searchMemberModal');
    if (modal) {
        modal.style.display = 'none';
        // Clear form
        clearInviteForm();
    }
}

/**
 * Clear the invite form
 */
function clearInviteForm() {
    const emailInput = document.getElementById('memberEmailInput');
    const roleSelect = document.getElementById('memberRole');
    const messageDiv = document.getElementById('inviteMessage');
    
    if (emailInput) emailInput.value = '';
    if (roleSelect) roleSelect.value = 'Employee';
    if (messageDiv) {
        messageDiv.style.display = 'none';
        messageDiv.textContent = '';
    }
}

/**
 * Send invite request
 */
async function sendInviteRequest() {
    const emailInput = document.getElementById('memberEmailInput');
    const roleSelect = document.getElementById('memberRole');
    const messageDiv = document.getElementById('inviteMessage');
    
    if (!emailInput || !roleSelect || !currentWorkspaceId) {
        console.error('Missing required elements');
        return;
    }
    
    const email = emailInput.value.trim();
    const role = roleSelect.value;
    
    // Basic validation
    if (!email) {
        showMessage('Please enter an email address', 'error');
        return;
    }
    
    if (!email.includes('@')) {
        showMessage('Please enter a valid email address', 'error');
        return;
    }
    
    try {
        // Show loading state
        showMessage('Sending invite...', 'info');
        
        const formData = new FormData();
        formData.append('id', currentWorkspaceId);
        formData.append('type', 'workspace');
        formData.append('email', email);
        formData.append('role', role);
        
        const response = await fetch('../ManagerFunction/InviteMember.php', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            showMessage('Member invited successfully!', 'success');
            // Clear form after successful invite
            setTimeout(() => {
                hideInviteModal();
                window.location.reload();
            }, 1500);
        } else {
            showMessage(result.error || 'Failed to invite member', 'error');
        }
    } catch (error) {
        console.error('Error sending invite:', error);
        showMessage('Error sending invite. Please try again.', 'error');
    }
}

/**
 * Show message to user
 */
function showMessage(message, type) {
    const messageDiv = document.getElementById('inviteMessage');
    if (messageDiv) {
        messageDiv.textContent = message;
        messageDiv.className = `invite-message ${type}`;
        messageDiv.style.display = 'block';
    }
}

/**
 * Initialize invite member functionality
 */
function initializeInviteMember() {
    // Close modal button
    const closeBtn = document.getElementById('closeSearchModal');
    if (closeBtn) {
        closeBtn.addEventListener('click', hideInviteModal);
    }
    
    // Cancel button
    const cancelBtn = document.getElementById('cancelInviteBtn');
    if (cancelBtn) {
        cancelBtn.addEventListener('click', hideInviteModal);
    }
    
    // Invite button
    const inviteBtn = document.getElementById('inviteMemberBtn');
    if (inviteBtn) {
        inviteBtn.addEventListener('click', sendInviteRequest);
    }
    
    // Enter key on email input
    const emailInput = document.getElementById('memberEmailInput');
    if (emailInput) {
        emailInput.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                sendInviteRequest();
            }
        });
    }
    
    // Close modal when clicking outside
    const modal = document.getElementById('searchMemberModal');
    if (modal) {
        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                hideInviteModal();
            }
        });
    }
}

// Export functions for use in other files
window.handleInviteMember = handleInviteMember;
window.initializeInviteMember = initializeInviteMember;
