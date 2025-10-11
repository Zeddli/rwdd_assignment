<?php
/**
 * Grant Access Window Component
 * Modal window for selecting workspace members to collaborate on a task
 */
?>

<div class="grant-access-modal" id="grantAccessModal" style="display: none;">
    <div class="grant-access-container">
        <div class="grant-access-header">
            <h3>Grant Access</h3>
            <button class="close-modal" id="closeGrantAccessModal">&times;</button>
        </div>
        
        <div class="grant-access-content">
            <!-- Search Bar -->
            <div class="search-container">
                <input type="text" id="memberSearchInput" placeholder="Search person" class="search-input">
            </div>
            
            <!-- Member List -->
            <div class="member-list" id="memberList">
                <!-- Members will be loaded dynamically -->
                <div class="loading-message">Loading members...</div>
            </div>
            
            <div id="grantAccessMessage" class="grant-access-message" style="display: none;"></div>
        </div>
    </div>
</div>

<style>
.grant-access-modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    z-index: 1001;
    display: flex;
    justify-content: center;
    align-items: center;
}

.grant-access-container {
    background: white;
    border-radius: 12px;
    border: 1px solid #ccc;
    width: 400px;
    max-width: 90vw;
    max-height: 80vh;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
    display: flex;
    flex-direction: column;
}

.grant-access-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px 20px 0 20px;
    border-bottom: 1px solid #eee;
    margin-bottom: 20px;
}

.grant-access-header h3 {
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

.grant-access-content {
    padding: 0 20px 20px 20px;
    display: flex;
    flex-direction: column;
    flex: 1;
    min-height: 0;
}

.search-container {
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

.member-list {
    flex: 1;
    overflow-y: auto;
    max-height: 400px;
    border: 1px solid #eee;
    border-radius: 8px;
    padding: 10px;
}

.member-item {
    display: flex;
    align-items: center;
    padding: 12px;
    border-bottom: 1px solid #f0f0f0;
    transition: background-color 0.2s;
}

.member-item:last-child {
    border-bottom: none;
}

.member-item:hover {
    background-color: #f8f9fa;
}

.member-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background-color: #333;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 12px;
    flex-shrink: 0;
}

.member-avatar svg {
    width: 20px;
    height: 20px;
    fill: white;
}

.member-info {
    flex: 1;
    min-width: 0;
}

.member-name {
    font-size: 14px;
    font-weight: 500;
    color: #333;
    margin: 0 0 4px 0;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.member-email {
    font-size: 12px;
    color: #666;
    margin: 0;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.collaborate-btn {
    padding: 8px 16px;
    border: none;
    border-radius: 6px;
    font-size: 12px;
    cursor: pointer;
    font-weight: 500;
    background-color: #333;
    color: white;
    transition: background-color 0.2s;
    flex-shrink: 0;
}

.collaborate-btn:hover {
    background-color: #555;
}

.collaborate-btn.granted {
    background-color: #28a745;
    cursor: default;
}

.collaborate-btn.granted:hover {
    background-color: #28a745;
}

.loading-message, .no-members-message {
    text-align: center;
    color: #666;
    font-style: italic;
    padding: 20px;
}

.grant-access-message {
    margin-top: 15px;
    padding: 10px;
    border-radius: 6px;
    font-size: 14px;
}

.grant-access-message.success {
    background-color: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

.grant-access-message.error {
    background-color: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}

/* Custom scrollbar for member list */
.member-list::-webkit-scrollbar {
    width: 6px;
}

.member-list::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 3px;
}

.member-list::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 3px;
}

.member-list::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}
</style>
