<?php
/**
 * Task Detail Window Component
 * Modal window for creating/editing tasks
 */
?>

<div class="task-detail-modal" id="taskDetailModal" style="display: none;">
    <div class="task-detail-container">
        <div class="task-detail-header">
            <h3>Task Detail</h3>
            <button class="close-modal" id="closeTaskDetailModal">&times;</button>
        </div>
        
        <div class="task-detail-content">
            <form id="taskDetailForm">
                <!-- Workspace Selection (shown only when opened from calendar) -->
                <div class="form-group" id="workspaceSelectionGroup" style="display: none;">
                    <label for="workspaceSelect">Workspace *</label>
                    <select id="workspaceSelect" class="attribute-input" required>
                        <option value="">Select Workspace</option>
                        <!-- Workspace options will be populated by JavaScript -->
                    </select>
                </div>
                
                <!-- Task Name Input -->
                <div class="form-group">
                    <input type="text" id="taskNameInput" placeholder="Task Name" class="task-input" required>
                </div>
                
                <!-- Description Textarea -->
                <div class="form-group">
                    <textarea id="taskDescriptionInput" placeholder="Description" class="task-textarea" rows="4"></textarea>
                </div>
                
                <!-- Date Selection Fields -->
                <div class="date-fields">
                    <div class="form-group">
                        <label for="startDateInput">Start date *</label>
                        <input type="datetime-local" id="startDateInput" class="date-input" required>
                    </div>
                </div>
                
                <!-- Task Attribute Fields -->
                <div class="attribute-fields">
                    <div class="form-group">
                        <label for="deadlineInput">Deadline: *</label>
                        <input type="datetime-local" id="deadlineInput" class="attribute-input" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="prioritySelect">Priority:</label>
                        <select id="prioritySelect" class="attribute-input">
                            <option value="">Select Priority</option>
                            <option value="Low">Low</option>
                            <option value="Medium">Medium</option>
                            <option value="High">High</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="statusSelect">Status:</label>
                        <select id="statusSelect" class="attribute-input">
                            <option value="Pending">Pending</option>
                            <option value="In Progress">In Progress</option>
                            <option value="Completed">Completed</option>
                        </select>
                    </div>
                </div>
                
                <!-- Action Buttons -->
                <div class="task-actions">
                    <button type="button" id="grantAccessBtn" class="grant-access-btn">Grant access</button>
                    <div class="right-buttons">
                        <button type="button" id="cancelTaskBtn" class="cancel-btn">Cancel</button>
                        <button type="submit" id="saveTaskBtn" class="save-btn">Save</button>
                    </div>
                </div>
            </form>
            
            <div id="taskMessage" class="task-message" style="display: none;"></div>
        </div>
    </div>
</div>

<style>
.task-detail-modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    z-index: 1000;
    display: flex;
    justify-content: center;
    align-items: center;
}

.task-detail-container {
    background: white;
    border-radius: 12px;
    border: 1px solid #ccc;
    width: 500px;
    max-width: 90vw;
    max-height: 90vh;
    overflow-y: auto;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
}

.task-detail-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px 20px 0 20px;
    border-bottom: 1px solid #eee;
    margin-bottom: 20px;
}

.task-detail-header h3 {
    margin: 0;
    color: #333;
    font-size: 18px;
}

.close-modal {
    background: none;
    border: none;
    font-size: 24px;
    cursor: pointer;
    color: #666;
    padding: 0;
    width: 30px;
    height: 30px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.close-modal:hover {
    color: #333;
}

.task-detail-content {
    padding: 0 20px 20px 20px;
}

.form-group {
    margin-bottom: 20px;
}

.task-input {
    width: 100%;
    padding: 12px 16px;
    border: 1px solid #ddd;
    border-radius: 8px;
    font-size: 14px;
    background-color: #f8f9fa;
    box-sizing: border-box;
}

.task-input:focus {
    outline: none;
    border-color: #007bff;
    background-color: white;
}

.task-textarea {
    width: 100%;
    padding: 12px 16px;
    border: 1px solid #ddd;
    border-radius: 8px;
    font-size: 14px;
    background-color: #f8f9fa;
    box-sizing: border-box;
    resize: vertical;
    font-family: inherit;
}

.task-textarea:focus {
    outline: none;
    border-color: #007bff;
    background-color: white;
}

.date-fields {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: 500;
    color: #333;
}

.date-input, .attribute-input {
    width: 100%;
    padding: 10px 12px;
    border: 1px solid #ddd;
    border-radius: 6px;
    font-size: 14px;
    background-color: white;
    box-sizing: border-box;
}

.date-input:focus, .attribute-input:focus {
    outline: none;
    border-color: #007bff;
}

.attribute-fields {
    display: grid;
    grid-template-columns: 1fr;
    gap: 15px;
    margin-bottom: 20px;
}

/* Workspace Selection Styling */
#workspaceSelectionGroup {
    background-color: #f8f9fa;
    border: 1px solid #e9ecef;
    border-radius: 8px;
    padding: 15px;
    margin-bottom: 20px;
}

#workspaceSelectionGroup label {
    color: #495057;
    font-weight: 600;
    margin-bottom: 8px;
    display: block;
}

#workspaceSelect {
    background-color: white;
    border: 2px solid #dee2e6;
    border-radius: 6px;
    padding: 10px 12px;
    font-size: 14px;
    transition: border-color 0.2s ease;
}

#workspaceSelect:focus {
    outline: none;
    border-color: #007bff;
    box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.1);
}

#workspaceSelect option {
    padding: 8px 12px;
}

.task-actions {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 12px;
}

.grant-access-btn {
    padding: 10px 20px;
    border: none;
    border-radius: 6px;
    font-size: 14px;
    cursor: pointer;
    font-weight: 500;
    background-color: #333;
    color: white;
}

.grant-access-btn:hover {
    background-color: #555;
}

.right-buttons {
    display: flex;
    gap: 12px;
}

.save-btn, .cancel-btn {
    padding: 10px 20px;
    border: none;
    border-radius: 6px;
    font-size: 14px;
    cursor: pointer;
    font-weight: 500;
}

.save-btn {
    background-color: #333;
    color: white;
}

.save-btn:hover {
    background-color: #555;
}

.cancel-btn {
    background-color: #6c757d;
    color: white;
}

.cancel-btn:hover {
    background-color: #545b62;
}

.task-message {
    margin-top: 15px;
    padding: 10px;
    border-radius: 6px;
    font-size: 14px;
}

.task-message.success {
    background-color: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

.task-message.error {
    background-color: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}
</style>