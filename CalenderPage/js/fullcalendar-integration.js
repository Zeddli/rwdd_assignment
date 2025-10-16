/**
 * FullCalendar Integration
 * Integrates FullCalendar with existing task management system
 */

class FullCalendarIntegration {
    constructor() {
        this.calendar = null;
        this.currentEvents = [];
        this.init();
    }

    /**
     * Initialize FullCalendar
     */
    init() {
        // Wait for DOM to be ready
        document.addEventListener('DOMContentLoaded', () => {
            this.initializeCalendar();
            this.bindEvents();
        });
    }

    /**
     * Initialize FullCalendar instance
     */
    initializeCalendar() {
        const calendarEl = document.getElementById('calendar');
        
        if (!calendarEl) {
            console.error('Calendar element not found');
            return;
        }

        this.calendar = new FullCalendar.Calendar(calendarEl, {
            // Basic configuration
            initialView: 'dayGridMonth',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
            },
            
            // Theme and styling
            themeSystem: 'standard',
            
            // Event handling
            events: (info, successCallback, failureCallback) => {
                this.loadEvents(info.start, info.end, successCallback, failureCallback);
            },
            
            // Event interactions
            eventClick: (info) => {
                this.handleEventClick(info);
            },
            
            dateClick: (info) => {
                this.handleDateClick(info);
            },
            
            // Event rendering
            eventDidMount: (info) => {
                this.customizeEventDisplay(info);
            },
            
            // Height and responsiveness
            height: 'auto',
            aspectRatio: 1.8,
            
            // Locale and timezone
            locale: 'en',
            timeZone: 'local',
            
            // Navigation
            navLinks: true,
            
            // Event display options
            eventDisplay: 'block',
            dayMaxEvents: 3,
            moreLinkClick: 'popover',
            
            // Event time display
            displayEventTime: true,
            displayEventEnd: true
        });

        // Render the calendar
        this.calendar.render();
        console.log('FullCalendar initialized successfully');
    }

    /**
     * Load events from the server
     * @param {Date} start - Start date for the view
     * @param {Date} end - End date for the view
     * @param {Function} successCallback - Callback for successful data load
     * @param {Function} failureCallback - Callback for failed data load
     */
    async loadEvents(start, end, successCallback, failureCallback) {
        try {
            const formData = new FormData();
            formData.append('action', 'get_events');
            formData.append('start', start.toISOString().split('T')[0]);
            formData.append('end', end.toISOString().split('T')[0]);

            const response = await fetch('php/calendarData.php', {
                method: 'POST',
                body: formData
            });

            const data = await response.json();

            if (data.success && data.events) {
                // Transform task data to FullCalendar event format
                const events = data.events.map(task => this.transformTaskToEvent(task));
                this.currentEvents = events;
                successCallback(events);
            } else {
                console.error('Failed to load events:', data.message);
                failureCallback(data.message);
            }
        } catch (error) {
            console.error('Error loading events:', error);
            failureCallback(error.message);
        }
    }

    /**
     * Transform task data to FullCalendar event format
     * @param {Object} task - Task object from server
     * @returns {Object} FullCalendar event object
     */
    transformTaskToEvent(task) {
        // Determine event date (prioritize startTime, then deadline)
        const eventDate = task.startTime ? new Date(task.startTime) : 
                         task.deadline ? new Date(task.deadline) : 
                         new Date();

        // Determine end date
        const endDate = task.endTime ? new Date(task.endTime) : 
                       new Date(eventDate.getTime() + (60 * 60 * 1000)); // 1 hour default

        return {
            id: task.id,
            title: task.title,
            start: eventDate.toISOString(),
            end: endDate.toISOString(),
            allDay: this.isAllDayTask(task),
            backgroundColor: this.getPriorityColor(task.priority),
            borderColor: this.getPriorityColor(task.priority),
            textColor: '#ffffff',
            extendedProps: {
                description: task.description,
                priority: task.priority,
                status: task.status,
                workspaceName: task.workspaceName,
                userRole: task.userRole,
                deadline: task.deadline,
                startTime: task.startTime,
                endTime: task.endTime
            }
        };
    }

    /**
     * Check if task is all-day event
     * @param {Object} task - Task object
     * @returns {boolean} True if all-day event
     */
    isAllDayTask(task) {
        // If no specific times are set, treat as all-day
        if (!task.startTime && !task.endTime) {
            return true;
        }
        
        // If start and end are on the same day and no specific times
        if (task.startTime && task.endTime) {
            const start = new Date(task.startTime);
            const end = new Date(task.endTime);
            return start.toDateString() === end.toDateString() && 
                   start.getHours() === 0 && 
                   end.getHours() === 23;
        }
        
        return false;
    }

    /**
     * Get color based on task priority
     * @param {string} priority - Task priority
     * @returns {string} Color code
     */
    getPriorityColor(priority) {
        const colors = {
            'high': '#dc3545',     // Red
            'medium': '#ffc107',   // Yellow
            'low': '#28a745',      // Green
            'urgent': '#6f42c1'    // Purple
        };
        return colors[priority.toLowerCase()] || '#007bff';
    }

    /**
     * Handle event click
     * @param {Object} info - Event click info
     */
    handleEventClick(info) {
        const event = info.event;
        const taskData = event.extendedProps;
        
        console.log('Event clicked:', event.title);
        
        // Show task details in modal or navigate to task page
        if (typeof window.showTaskDetailWindow === 'function') {
            // If we have a task detail modal, we could show it here
            // For now, just log the task details
            console.log('Task details:', {
                id: event.id,
                title: event.title,
                description: taskData.description,
                priority: taskData.priority,
                status: taskData.status,
                workspace: taskData.workspaceName
            });
        }
        
        // You can implement task editing here
        // For example, open a modal with task details
        this.showTaskDetails(event);
    }

    /**
     * Handle date click (create new task)
     * @param {Object} info - Date click info
     */
    handleDateClick(info) {
        console.log('Date clicked:', info.dateStr);
        
        // Open task creation modal for the clicked date
        if (typeof window.showTaskDetailWindow === 'function') {
            const workspaceId = window.currentWorkspaceId || 1;
            // Show workspace selection when creating task from calendar date click
            window.showTaskDetailWindow({
                workspaceId: workspaceId,
                showWorkspaceSelection: true,
                date: info.dateStr
            });
            
            // Ensure modal close functionality is properly initialized
            this.ensureModalCloseFunctionality();
        }
    }

    /**
     * Customize event display
     * @param {Object} info - Event mount info
     */
    customizeEventDisplay(info) {
        const event = info.event;
        const taskData = event.extendedProps;
        
        // Add priority indicator
        if (taskData.priority) {
            info.el.title = `${event.title}\nPriority: ${taskData.priority}\nStatus: ${taskData.status}`;
        }
        
        // Add workspace indicator
        if (taskData.workspaceName) {
            const workspaceEl = document.createElement('span');
            workspaceEl.className = 'workspace-indicator';
            workspaceEl.textContent = taskData.workspaceName;
            workspaceEl.style.cssText = 'font-size: 0.7em; opacity: 0.8; margin-left: 4px;';
            info.el.appendChild(workspaceEl);
        }
    }

    /**
     * Show task details in a simple modal
     * @param {Object} event - FullCalendar event
     */
    showTaskDetails(event) {
        const taskData = event.extendedProps;
        
        // Create a simple modal for task details
        const modal = document.createElement('div');
        modal.className = 'modal';
        modal.style.cssText = `
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1000;
        `;
        
        const modalContent = document.createElement('div');
        modalContent.style.cssText = `
            background: white;
            padding: 20px;
            border-radius: 8px;
            max-width: 500px;
            width: 90%;
            max-height: 80vh;
            overflow-y: auto;
        `;
        
        modalContent.innerHTML = `
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                <h3 style="margin: 0;">Task Details</h3>
                <button onclick="this.closest('.modal').remove()" style="background: none; border: none; font-size: 24px; cursor: pointer;">&times;</button>
            </div>
            <div>
                <p><strong>Title:</strong> ${event.title}</p>
                <p><strong>Description:</strong> ${taskData.description || 'No description'}</p>
                <p><strong>Priority:</strong> <span style="background: ${this.getPriorityColor(taskData.priority)}; color: white; padding: 2px 8px; border-radius: 12px; font-size: 12px;">${taskData.priority}</span></p>
                <p><strong>Status:</strong> ${taskData.status}</p>
                <p><strong>Workspace:</strong> ${taskData.workspaceName}</p>
                <p><strong>Start:</strong> ${event.start ? event.start.toLocaleString() : 'Not set'}</p>
                <p><strong>End:</strong> ${event.end ? event.end.toLocaleString() : 'Not set'}</p>
                ${taskData.deadline ? `<p><strong>Deadline:</strong> ${new Date(taskData.deadline).toLocaleString()}</p>` : ''}
            </div>
        `;
        
        modal.appendChild(modalContent);
        document.body.appendChild(modal);
        
        // Close modal when clicking outside
        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                modal.remove();
            }
        });
    }

    /**
     * Refresh calendar events
     */
    async refreshEvents() {
        if (this.calendar) {
            this.calendar.refetchEvents();
        }
    }

    /**
     * Add new event to calendar
     * @param {Object} task - New task data
     */
    addEvent(task) {
        if (this.calendar) {
            const event = this.transformTaskToEvent(task);
            this.calendar.addEvent(event);
        }
    }

    /**
     * Update existing event
     * @param {Object} task - Updated task data
     */
    updateEvent(task) {
        if (this.calendar) {
            const existingEvent = this.calendar.getEventById(task.id);
            if (existingEvent) {
                const eventData = this.transformTaskToEvent(task);
                existingEvent.setProp('title', eventData.title);
                existingEvent.setStart(eventData.start);
                existingEvent.setEnd(eventData.end);
                existingEvent.setProp('backgroundColor', eventData.backgroundColor);
                existingEvent.setProp('extendedProps', eventData.extendedProps);
            }
        }
    }

    /**
     * Remove event from calendar
     * @param {string|number} taskId - Task ID to remove
     */
    removeEvent(taskId) {
        if (this.calendar) {
            const event = this.calendar.getEventById(taskId);
            if (event) {
                event.remove();
            }
        }
    }

    /**
     * Ensure modal close functionality is properly set up
     */
    ensureModalCloseFunctionality() {
        // Wait a bit for the modal to be rendered
        setTimeout(() => {
            const modal = document.getElementById('taskDetailModal');
            const closeBtn = document.getElementById('closeTaskDetailModal');
            const cancelBtn = document.getElementById('cancelTaskBtn');
            
            if (modal) {
                // Re-ensure close button functionality
                if (closeBtn && !closeBtn.hasAttribute('data-listener-added')) {
                    closeBtn.addEventListener('click', () => {
                        console.log('Close button clicked');
                        if (typeof window.hideTaskDetailWindow === 'function') {
                            window.hideTaskDetailWindow();
                        } else {
                            modal.style.display = 'none';
                        }
                    });
                    closeBtn.setAttribute('data-listener-added', 'true');
                }
                
                // Re-ensure cancel button functionality
                if (cancelBtn && !cancelBtn.hasAttribute('data-listener-added')) {
                    cancelBtn.addEventListener('click', () => {
                        console.log('Cancel button clicked');
                        if (typeof window.hideTaskDetailWindow === 'function') {
                            window.hideTaskDetailWindow();
                        } else {
                            modal.style.display = 'none';
                        }
                    });
                    cancelBtn.setAttribute('data-listener-added', 'true');
                }
                
                // Re-ensure click outside to close functionality
                if (!modal.hasAttribute('data-listener-added')) {
                    modal.addEventListener('click', (e) => {
                        if (e.target === modal) {
                            console.log('Modal backdrop clicked');
                            if (typeof window.hideTaskDetailWindow === 'function') {
                                window.hideTaskDetailWindow();
                            } else {
                                modal.style.display = 'none';
                            }
                        }
                    });
                    modal.setAttribute('data-listener-added', 'true');
                }
            }
        }, 100);
    }

    /**
     * Bind global events
     */
    bindEvents() {
        // Listen for task updates from other components
        document.addEventListener('taskUpdated', (event) => {
            this.updateEvent(event.detail);
        });
        
        document.addEventListener('taskCreated', (event) => {
            this.addEvent(event.detail);
        });
        
        document.addEventListener('taskDeleted', (event) => {
            this.removeEvent(event.detail.id);
        });
        
        // Refresh events when tasks are updated via modal
        if (typeof window.refreshTasksAfterModal === 'function') {
            const originalRefresh = window.refreshTasksAfterModal;
            window.refreshTasksAfterModal = () => {
                originalRefresh();
                this.refreshEvents();
            };
        }
    }
}

// Initialize FullCalendar integration
window.fullCalendarIntegration = new FullCalendarIntegration();

// Export for global access
window.FullCalendarIntegration = FullCalendarIntegration;
