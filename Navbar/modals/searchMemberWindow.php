<?php
/**
 * Search Member Window Component
 * Modal window for searching and inviting members to workspace
 */
?>

<div class="search-member-modal" id="searchMemberModal" style="display: none;">
    <div class="search-member-container">
        <div class="search-member-header">
            <h3>Invite Member</h3>
            <button class="close-modal" id="closeSearchModal">&times;</button>
        </div>
        
        <div class="search-member-content">
            <div class="search-input-container">
                <input type="email" id="memberEmailInput" placeholder="Search person" class="search-input">
            </div>
            
            <div class="role-selection">
                <label for="memberRole">Role:</label>
                <select id="memberRole" class="role-select">
                    <option value="Employee">Employee</option>
                    <option value="Manager">Manager</option>
                </select>
            </div>
            
            <div class="search-actions">
                <button id="inviteMemberBtn" class="invite-btn">Invite</button>
                <button id="cancelInviteBtn" class="cancel-btn">Cancel</button>
            </div>
            
            <div id="inviteMessage" class="invite-message" style="display: none;"></div>
        </div>
    </div>
</div>

<style>
.search-member-modal {
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

.search-member-container {
    background: white;
    border-radius: 12px;
    border: 1px solid #ccc;
    width: 400px;
    max-width: 90vw;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
}

.search-member-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px 20px 0 20px;
    border-bottom: 1px solid #eee;
    margin-bottom: 20px;
}

.search-member-header h3 {
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

.search-member-content {
    padding: 0 20px 20px 20px;
}

.search-input-container {
    margin-bottom: 20px;
}

.search-input {
    width: 100%;
    padding: 12px 16px;
    border: 1px solid #ddd;
    border-radius: 8px;
    font-size: 14px;
    background-color: #f8f9fa;
    box-sizing: border-box;
}

.search-input:focus {
    outline: none;
    border-color: #007bff;
    background-color: white;
}

.role-selection {
    margin-bottom: 20px;
}

.role-selection label {
    display: block;
    margin-bottom: 8px;
    font-weight: 500;
    color: #333;
}

.role-select {
    width: 100%;
    padding: 10px 12px;
    border: 1px solid #ddd;
    border-radius: 6px;
    font-size: 14px;
    background-color: white;
    box-sizing: border-box;
}

.search-actions {
    display: flex;
    gap: 12px;
    justify-content: flex-end;
}

.invite-btn, .cancel-btn {
    padding: 10px 20px;
    border: none;
    border-radius: 6px;
    font-size: 14px;
    cursor: pointer;
    font-weight: 500;
}

.invite-btn {
    background-color: #007bff;
    color: white;
}

.invite-btn:hover {
    background-color: #0056b3;
}

.cancel-btn {
    background-color: #6c757d;
    color: white;
}

.cancel-btn:hover {
    background-color: #545b62;
}

.invite-message {
    margin-top: 15px;
    padding: 10px;
    border-radius: 6px;
    font-size: 14px;
}

.invite-message.success {
    background-color: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

.invite-message.error {
    background-color: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}
</style>
