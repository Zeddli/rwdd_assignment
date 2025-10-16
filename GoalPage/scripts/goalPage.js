/**
 * Goal Page JavaScript Functionality
 * Handles goal creation, editing, and deletion for workspace-specific goals
 */

// Get workspace ID from URL parameters
function getWorkspaceId() {
    const urlParams = new URLSearchParams(window.location.search);
    return urlParams.get('workspace_id');
}

// Add new goal functionality
function addNewGoal() {
    const workspaceId = getWorkspaceId();
    if (!workspaceId) {
        alert('Workspace ID not found');
        return;
    }
    
    // Get goal description from user (database only stores description)
    const description = prompt('Enter goal description:');
    if (!description || description.trim() === '') {
        return;
    }
    
    const dueDate = prompt('Enter due date (YYYY-MM-DD) or leave empty:');
    
    // Create goal object
    const goalData = {
        workspace_id: workspaceId,
        description: description.trim(),
        due_date: dueDate.trim() || null,
        status: 'Pending'
    };
    
    // Send AJAX request to create goal
    createGoal(goalData);
}

// Edit existing goal
function editGoal(goalId) {
    // Get current goal data (you might want to fetch this from the server)
    const description = prompt('Edit goal description:');
    if (!description || description.trim() === '') {
        return;
    }
    
    const dueDate = prompt('Edit due date (YYYY-MM-DD) or leave empty:');
    
    const goalData = {
        goal_id: goalId,
        description: description.trim(),
        due_date: dueDate.trim() || null
    };
    
    // Send AJAX request to update goal
    updateGoal(goalData);
}

// Delete goal
function deleteGoal(goalId) {
    if (confirm('Are you sure you want to delete this goal?')) {
        // Send AJAX request to delete goal
        deleteGoalRequest(goalId);
    }
}

// AJAX function to create a new goal
function createGoal(goalData) {
    fetch('backend/createGoal.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(goalData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Goal created successfully!');
            location.reload(); // Refresh the page to show new goal
        } else {
            alert('Error creating goal: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error creating goal. Please try again.');
    });
}

// AJAX function to update an existing goal
function updateGoal(goalData) {
    fetch('backend/updateGoal.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(goalData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Goal updated successfully!');
            location.reload(); // Refresh the page to show updated goal
        } else {
            alert('Error updating goal: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error updating goal. Please try again.');
    });
}

// AJAX function to delete a goal
function deleteGoalRequest(goalId) {
    fetch('backend/deleteGoal.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ goal_id: goalId })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Goal deleted successfully!');
            location.reload(); // Refresh the page to remove deleted goal
        } else {
            alert('Error deleting goal: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error deleting goal. Please try again.');
    });
}

// Initialize goal page functionality when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    // Add event listeners for buttons
    const addGoalBtn = document.getElementById('addGoalBtn');
    if (addGoalBtn) {
        addGoalBtn.addEventListener('click', addNewGoal);
    }
    
    // Check if workspace ID is present
    const workspaceId = getWorkspaceId();
    if (!workspaceId) {
        alert('No workspace ID found. Redirecting to home page.');
        window.location.href = '../HomePage/home.php';
    }
    
    console.log('Goal page initialized for workspace ID:', workspaceId);
});
