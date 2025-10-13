<?php
/**
 * Monthly Calendar View Component
 * Displays the monthly calendar grid with navigation and task indicators
 */
?>

<!-- Calendar Navigation Bar -->
<div class="calendar-navigation" id="calendarNavBar">
    <div class="calendar-nav-section">
        <button class="nav-btn" id="prevMonthBtn" title="Previous Month">
            <svg width="20" height="20" viewBox="0 0 24 24">
                <path d="M15.41 7.41L14 6l-6 6 6 6 1.41-1.41L10.83 12z"/>
            </svg>
        </button>
        
        <h2 class="month-year-display" id="monthYearDisplay">November 2025</h2>
        
        <button class="nav-btn" id="nextMonthBtn" title="Next Month">
            <svg width="20" height="20" viewBox="0 0 24 24">
                <path d="M10 6L8.59 7.41 13.17 12l-4.58 4.59L10 18l6-6z"/>
            </svg>
        </button>
    </div>
    
    <div class="calendar-nav-section">
        <select class="view-selector" id="viewSelector">
            <option value="month" selected>Month</option>
            <option value="week">Week</option>
            <option value="day">Day</option>
        </select>
        
        <button class="today-btn" id="todayBtn">Today</button>
    </div>
</div>

<!-- Calendar Grid Container -->
<div class="calendar-container" id="calendarContainer">
    <div class="calendar-grid" id="calendarGrid">
        <!-- Calendar grid will be rendered here by JavaScript -->
    </div>
</div>

<!-- Loading indicator -->
<div class="calendar-loading" id="calendarLoading" style="display: none;">
    <div class="loading-spinner"></div>
    <p>Loading calendar...</p>
</div>