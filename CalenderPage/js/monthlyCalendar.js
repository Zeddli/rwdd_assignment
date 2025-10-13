/**
 * Calendar JavaScript Module
 * Handles monthly calendar view, navigation, and task display
 */

class Calendar {
    constructor() {
        this.currentDate = new Date();
        this.currentYear = this.currentDate.getFullYear();
        this.currentMonth = this.currentDate.getMonth() + 1; // 1-12
        this.today = new Date();
        
        this.initializeElements();
        this.bindEvents();
        this.render();
    }
    
    /**
     * Initialize DOM elements
     */
    initializeElements() {
        this.calendarContainer = document.getElementById('calendarContainer');
        this.navBar = document.getElementById('calendarNavBar');
        this.monthYearDisplay = document.getElementById('monthYearDisplay');
        this.prevBtn = document.getElementById('prevMonthBtn');
        this.nextBtn = document.getElementById('nextMonthBtn');
        this.todayBtn = document.getElementById('todayBtn');
        this.viewSelector = document.getElementById('viewSelector');
        this.calendarGrid = document.getElementById('calendarGrid');
    }
    
    /**
     * Bind event listeners
     */
    bindEvents() {
        if (this.prevBtn) {
            this.prevBtn.addEventListener('click', () => this.previousMonth());
        }
        if (this.nextBtn) {
            this.nextBtn.addEventListener('click', () => this.nextMonth());
        }
        if (this.todayBtn) {
            this.todayBtn.addEventListener('click', () => this.goToToday());
        }
        if (this.viewSelector) {
            this.viewSelector.addEventListener('change', (e) => this.changeView(e.target.value));
        }
    }
    
    /**
     * Render the complete calendar
     */
    async render() {
        await this.renderNavigation();
        await this.renderCalendarGrid();
        await this.loadTasks();
    }
    
    /**
     * Render navigation bar
     */
    renderNavigation() {
        if (this.monthYearDisplay) {
            const monthNames = [
                'January', 'February', 'March', 'April', 'May', 'June',
                'July', 'August', 'September', 'October', 'November', 'December'
            ];
            this.monthYearDisplay.textContent = `${monthNames[this.currentMonth - 1]} ${this.currentYear}`;
        }
    }
    
    /**
     * Render calendar grid
     */
    renderCalendarGrid() {
        if (!this.calendarGrid) return;
        
        // Clear existing content
        this.calendarGrid.innerHTML = '';
        
        // Create day headers
        const dayNames = ['SUN', 'MON', 'TUE', 'WED', 'THU', 'FRI', 'SAT'];
        dayNames.forEach(day => {
            const dayHeader = document.createElement('div');
            dayHeader.className = 'day-header';
            dayHeader.textContent = day;
            this.calendarGrid.appendChild(dayHeader);
        });
        
        // Calculate the exact number of weeks needed for this month
        const firstDay = new Date(this.currentYear, this.currentMonth - 1, 1);
        const lastDay = new Date(this.currentYear, this.currentMonth, 0);
        const startDate = new Date(firstDay);
        startDate.setDate(startDate.getDate() - firstDay.getDay()); // Start from Sunday
        
        // Calculate end date (last day of month + remaining days to complete the week)
        const endDate = new Date(lastDay);
        const daysToCompleteWeek = (6 - lastDay.getDay()) % 7;
        endDate.setDate(endDate.getDate() + daysToCompleteWeek);
        
        // Calculate total days needed
        const totalDays = Math.ceil((endDate - startDate) / (1000 * 60 * 60 * 24)) + 1;
        
        // Generate only the days needed for this month
        for (let i = 0; i < totalDays; i++) {
            const currentDate = new Date(startDate);
            currentDate.setDate(startDate.getDate() + i);
            
            // Check if this date belongs to the current month
            if (currentDate.getMonth() + 1 === this.currentMonth) {
                const dayCell = this.createDayCell(currentDate);
                this.calendarGrid.appendChild(dayCell);
            } else {
                // Create empty cell for non-current month days
                const emptyCell = this.createEmptyCell();
                this.calendarGrid.appendChild(emptyCell);
            }
        }
        
        // Update CSS grid to use dynamic number of rows
        const numberOfWeeks = Math.ceil(totalDays / 7);
        this.calendarGrid.style.gridTemplateRows = `auto repeat(${numberOfWeeks}, 1fr)`;
    }
    
    /**
     * Create individual day cell
     * @param {Date} date - Date for the cell
     * @returns {HTMLElement} Day cell element
     */
    createDayCell(date) {
        const cell = document.createElement('div');
        cell.className = 'day-cell';
        cell.dataset.date = this.formatDate(date);
        
        // Add date number
        const dayNumber = document.createElement('div');
        dayNumber.className = 'day-number';
        dayNumber.textContent = date.getDate();
        cell.appendChild(dayNumber);
        
        // Add task container
        const taskContainer = document.createElement('div');
        taskContainer.className = 'day-tasks';
        cell.appendChild(taskContainer);
        
        // Style based on date
        this.styleDayCell(cell, date);
        
        // Add click handler for task creation
        cell.addEventListener('click', () => this.onDayClick(date));
        
        return cell;
    }
    
    /**
     * Create empty cell for non-current month days
     * @returns {HTMLElement} Empty cell element
     */
    createEmptyCell() {
        const cell = document.createElement('div');
        cell.className = 'day-cell empty-cell';
        cell.innerHTML = '';
        return cell;
    }
    
    /**
     * Style day cell based on date properties
     * @param {HTMLElement} cell - Day cell element
     * @param {Date} date - Date for the cell
     */
    styleDayCell(cell, date) {
        const isCurrentMonth = date.getMonth() + 1 === this.currentMonth;
        const isToday = this.isToday(date);
        const isPast = date < this.today && !this.isToday(date);
        
        if (!isCurrentMonth) {
            cell.classList.add('other-month');
        }
        if (isToday) {
            cell.classList.add('today');
        }
        if (isPast) {
            cell.classList.add('past-day');
        }
    }
    
    /**
     * Load tasks for current month
     */
    async loadTasks() {
        try {
            const formData = new FormData();
            formData.append('action', 'get_month_tasks');
            formData.append('year', this.currentYear);
            formData.append('month', this.currentMonth);
            
            const response = await fetch('php/calendarData.php', {
                method: 'POST',
                body: formData
            });
            
            const data = await response.json();
            
            if (data.success && data.tasks) {
                this.displayTasks(data.tasks);
            }
        } catch (error) {
            console.error('Error loading tasks:', error);
        }
    }
    
    /**
     * Display tasks on calendar
     * @param {Array} tasks - Array of task objects
     */
    displayTasks(tasks) {
        // Clear existing task indicators
        document.querySelectorAll('.day-tasks').forEach(container => {
            container.innerHTML = '';
        });
        
        // Group tasks by date
        const tasksByDate = {};
        tasks.forEach(task => {
            const taskDate = this.getTaskDate(task);
            if (!tasksByDate[taskDate]) {
                tasksByDate[taskDate] = [];
            }
            tasksByDate[taskDate].push(task);
        });
        
        // Display tasks on calendar
        Object.keys(tasksByDate).forEach(dateStr => {
            const dayCell = document.querySelector(`[data-date="${dateStr}"]`);
            if (dayCell) {
                const taskContainer = dayCell.querySelector('.day-tasks');
                const dayTasks = tasksByDate[dateStr];
                
                // Show task count indicator (blue circle with number)
                if (dayTasks.length > 0) {
                    const taskIndicator = document.createElement('div');
                    taskIndicator.className = 'task-indicator';
                    taskIndicator.textContent = dayTasks.length;
                    taskIndicator.title = `${dayTasks.length} task(s)`;
                    taskContainer.appendChild(taskIndicator);
                }
                
                // Add individual task priority dots below the count
                dayTasks.slice(0, 3).forEach(task => {
                    const taskDot = document.createElement('div');
                    taskDot.className = `task-dot priority-${task.priority.toLowerCase()}`;
                    taskDot.title = task.title;
                    taskContainer.appendChild(taskDot);
                });
            }
        });
    }
    
    /**
     * Get the primary date for a task (start date, end date, or deadline)
     * @param {Object} task - Task object
     * @returns {string} Formatted date string
     */
    getTaskDate(task) {
        // Priority: StartTime > EndTime > Deadline
        const startDate = task.startTime ? new Date(task.startTime) : null;
        const endDate = task.endTime ? new Date(task.endTime) : null;
        const deadline = task.deadline ? new Date(task.deadline) : null;
        
        const taskDate = startDate || endDate || deadline;
        return taskDate ? this.formatDate(taskDate) : null;
    }
    
    /**
     * Navigation methods
     */
    previousMonth() {
        this.currentMonth--;
        if (this.currentMonth < 1) {
            this.currentMonth = 12;
            this.currentYear--;
        }
        this.render();
    }
    
    nextMonth() {
        this.currentMonth++;
        if (this.currentMonth > 12) {
            this.currentMonth = 1;
            this.currentYear++;
        }
        this.render();
    }
    
    goToToday() {
        this.currentYear = this.today.getFullYear();
        this.currentMonth = this.today.getMonth() + 1;
        this.render();
    }
    
    changeView(view) {
        // Handle view switching (Month/Week/Day)
        console.log('Switching to view:', view);
        // Implementation for other views would go here
    }
    
    /**
     * Handle day cell clicks
     * @param {Date} date - Clicked date
     */
    onDayClick(date) {
        console.log('Day clicked:', date);
        // Could open task creation modal or day view
        this.showDayTasks(date);
    }
    
    /**
     * Show tasks for a specific day
     * @param {Date} date - Date to show tasks for
     */
    async showDayTasks(date) {
        try {
            const formData = new FormData();
            formData.append('action', 'get_day_tasks');
            formData.append('date', this.formatDate(date));
            
            const response = await fetch('php/calendarData.php', {
                method: 'POST',
                body: formData
            });
            
            const data = await response.json();
            
            if (data.success) {
                this.displayDayTasksModal(date, data.tasks || []);
            }
        } catch (error) {
            console.error('Error loading day tasks:', error);
        }
    }
    
    /**
     * Display day tasks in a modal
     * @param {Date} date - Date
     * @param {Array} tasks - Tasks for the date
     */
    displayDayTasksModal(date, tasks) {
        // Create or update modal
        let modal = document.getElementById('dayTasksModal');
        if (!modal) {
            modal = this.createDayTasksModal();
        }
        
        const modalTitle = modal.querySelector('.modal-title');
        const modalContent = modal.querySelector('.modal-content');
        
        modalTitle.textContent = `Tasks for ${date.toLocaleDateString()}`;
        
        if (tasks.length === 0) {
            modalContent.innerHTML = '<p>No tasks for this day.</p>';
        } else {
            modalContent.innerHTML = tasks.map(task => `
                <div class="task-item">
                    <h4>${this.escapeHtml(task.title)}</h4>
                    <p>${this.escapeHtml(task.description || '')}</p>
                    <div class="task-meta">
                        <span class="priority priority-${task.priority.toLowerCase()}">${task.priority}</span>
                        <span class="status status-${task.status.toLowerCase()}">${task.status}</span>
                        <span class="workspace">${this.escapeHtml(task.workspaceName)}</span>
                    </div>
                </div>
            `).join('');
        }
        
        modal.style.display = 'flex';
    }
    
    /**
     * Create day tasks modal
     * @returns {HTMLElement} Modal element
     */
    createDayTasksModal() {
        const modal = document.createElement('div');
        modal.id = 'dayTasksModal';
        modal.className = 'modal';
        modal.innerHTML = `
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title"></h3>
                    <button class="modal-close">&times;</button>
                </div>
                <div class="modal-body"></div>
            </div>
        `;
        
        document.body.appendChild(modal);
        
        // Close modal handlers
        modal.querySelector('.modal-close').addEventListener('click', () => {
            modal.style.display = 'none';
        });
        
        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                modal.style.display = 'none';
            }
        });
        
        return modal;
    }
    
    /**
     * Utility methods
     */
    formatDate(date) {
        return date.toISOString().split('T')[0]; // YYYY-MM-DD
    }
    
    isToday(date) {
        return date.toDateString() === this.today.toDateString();
    }
    
    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
}

// Initialize calendar when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    window.calendar = new Calendar();
});
