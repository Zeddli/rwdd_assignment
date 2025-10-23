<!-- To-Do List Sidebar Component -->
<!-- This component can be included in any page that needs the to-do sidebar -->

<!-- Mobile Overlay for Todo Sidebar -->
<div class="todo-mobile-overlay" id="todoMobileOverlay"></div>

<!-- Toggle Button for To-Do Sidebar -->
<button class="todo-sidebar-toggle" id="todoSidebarToggle" title="Toggle To-Do List">
    ✓
</button>

<!-- Right Sidebar for To-Do List -->
<div class="todo-sidebar" id="todoSidebar">
    <!-- Sidebar Header -->
    <div class="sidebar-header">
        <h2>My Tasks</h2>
        <button class="close-btn" id="closeSidebar">×</button>
    </div>

    <!-- Task Input Section -->
    <div class="task-input-section">
        <button class="calendar-add-task-btn" id="openTaskDetailBtn">
            Add New Task
        </button>
    </div>

    <!-- Task List Container -->
    <div class="task-list-container">
        <ul class="task-list" id="taskList">
            <!-- Tasks will be dynamically loaded here -->
            <li class="loading">Loading tasks...</li>
        </ul>
        
        <!-- Empty State (hidden by default) -->
        <div class="empty-state hidden" id="emptyState">
            <div class="empty-state-icon">📝</div>
            <div class="empty-state-text">No tasks yet<br>Add your to-dos and keep track of them</div>
        </div>
    </div>
</div>

