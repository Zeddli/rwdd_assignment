# Calendar Page - FullCalendar Integration

This calendar page has been updated to use FullCalendar, a powerful and feature-rich calendar library that replaces the previous custom calendar implementation.

## Features

- **Multiple Views**: Month, Week, Day, and List views
- **Task Integration**: Displays tasks as calendar events with priority-based colors
- **Interactive**: Click on dates to create tasks, click on events to view details
- **Responsive**: Works well on desktop and mobile devices
- **Real-time Updates**: Calendar refreshes when tasks are created, updated, or deleted

## Files

### Core Files
- `calendar.php` - Main calendar page with FullCalendar integration
- `js/fullcalendar-integration.js` - JavaScript integration with FullCalendar
- `php/calendarData.php` - Backend API for calendar data
- `css/calendar.css` - Styling for FullCalendar components

### Removed Files
The following files were removed as they were part of the old custom calendar implementation:
- `calendarView.php` - Old calendar view component
- `js/monthlyCalendar.js` - Old custom calendar JavaScript
- `createTaskPopUp.php` - Old task creation popup
- `css/createTaskPopUp.css` - Old popup styling

## Integration

The calendar integrates with the existing task management system:
- Tasks are fetched from the database and displayed as calendar events
- Priority levels determine event colors (High=Red, Medium=Yellow, Low=Green, Urgent=Purple)
- Events show task titles, descriptions, priorities, status, and workspace information
- Clicking on events opens a detailed modal with task information
- Clicking on dates opens the task creation modal

## Dependencies

- FullCalendar v6.1.19 (loaded via CDN)
- Existing task management backend
- To-do sidebar component (preserved)

## Browser Support

FullCalendar supports all modern browsers including:
- Chrome 60+
- Firefox 55+
- Safari 12+
- Edge 79+

## Usage

1. Navigate to the calendar page
2. Use the toolbar to switch between different views (Month, Week, Day, List)
3. Click on any date to create a new task
4. Click on any event to view task details
5. Use navigation buttons to move between months/weeks
6. Click "Today" to return to the current date

## Customization

The calendar styling can be customized by modifying the CSS classes in `css/calendar.css`. The FullCalendar integration can be extended by modifying `js/fullcalendar-integration.js`.

