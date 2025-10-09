// Get the user's timezone
// Get the timezone string, e.g., "Asia/Kuala_Lumpur"
var timezone = Intl.DateTimeFormat().resolvedOptions().timeZone;

// Paging logic
const perPage = 20;
let currentPage = 1;

function renderNotifications() {
    const list = document.getElementById('notification-list');
    const pagelist = document.getElementById('pagination-controls');
    list.innerHTML = '';
    pagelist.innerHTML = '';

    if (!notifications || notifications.length === 0) {
        list.innerHTML = '<div class="no-notification">No Notifications</div>';
        renderPaginationControls(1, 1, pagelist);
        return;
    }

    const ordered = [...notifications].sort((a, b) => new Date(b.CreatedAt) - new Date(a.CreatedAt));
    const totalPage = Math.ceil(ordered.length / perPage);
    const start = (currentPage-1)*perPage;
    const pageItems = ordered.slice(start, start+perPage);;

    pageItems.forEach(n => {
        const card = document.createElement('div');
        card.className = 'notification-card';

        card.innerHTML = `
            <div class="notification-title"><strong>${n.Title}</strong></div>
            <div class="notification-desc">${n.Description}</div>
            <div class="notification-date">${n.CreatedAt}</div>
        `;
        card.onclick = () => navigateNotification(n);
        list.appendChild(card);
    });

    // Always show pagination controls even if only one page
    renderPaginationControls(totalPage, currentPage, pagelist);
}

function renderPaginationControls(totalPage, currentPage, pagelist) {
    // Leftmost arrow
    const leftmost = document.createElement('button');
    leftmost.className = 'page-btn arrow-btn';
    leftmost.innerHTML = '&laquo;';
    leftmost.disabled = currentPage === 1;
    leftmost.onclick = () => {
        if (currentPage !== 1) {
            window.currentPage = 1;
            renderNotifications();
            window.scrollTo(0,0);
        }
    };
    pagelist.appendChild(leftmost);

    // Left arrow
    const left = document.createElement('button');
    left.className = 'page-btn arrow-btn';
    left.innerHTML = '&lt;';
    left.disabled = currentPage === 1;
    left.onclick = () => {
        if (currentPage > 1) {
            window.currentPage--;
            renderNotifications();
            window.scrollTo(0,0);
        }
    };
    pagelist.appendChild(left);

    let firstPageBtn = 1;
    let lastPageBtn = totalPage;

    // Logic for showing max 10 page buttons
    if (totalPage > 10) {
        if (currentPage <= 6) {
            firstPageBtn = 1;
            lastPageBtn = 10;
        } else if (currentPage > totalPage - 5) {
            firstPageBtn = totalPage - 9;
            lastPageBtn = totalPage;
        } else {
            firstPageBtn = currentPage - 5;
            lastPageBtn = currentPage + 4;
        }
    }

    for(let i=firstPageBtn; i<=lastPageBtn; i++) {
        const btn = document.createElement('button');
        btn.className = 'page-btn' + (i===currentPage ? ' active' : '');
        btn.innerText = i;
        btn.onclick = () => {
            window.currentPage = i;
            renderNotifications();
            window.scrollTo(0,0);
        };
        pagelist.appendChild(btn);
    }

    // Only show ... and last page button if there's a gap
    if (totalPage > 10 && lastPageBtn < totalPage - 1) {
        const dots = document.createElement('span');
        dots.className = 'page-ellipsis';
        dots.innerText = '...';
        pagelist.appendChild(dots);

        // Last page button
        const lastBtn = document.createElement('button');
        lastBtn.className = 'page-btn' + (totalPage === currentPage ? ' active' : '');
        lastBtn.innerText = totalPage;
        lastBtn.onclick = () => {
            window.currentPage = totalPage;
            renderNotifications();
            window.scrollTo(0,0);
        };
        pagelist.appendChild(lastBtn);
    } else if (totalPage > 10 && lastPageBtn === totalPage - 1) {
        // Show last page button directly if only one is skipped
        const lastBtn = document.createElement('button');
        lastBtn.className = 'page-btn' + (totalPage === currentPage ? ' active' : '');
        lastBtn.innerText = totalPage;
        lastBtn.onclick = () => {
            window.currentPage = totalPage;
            renderNotifications();
            window.scrollTo(0,0);
        };
        pagelist.appendChild(lastBtn);
    }

    // Right arrow
    const right = document.createElement('button');
    right.className = 'page-btn arrow-btn';
    right.innerHTML = '&gt;';
    right.disabled = currentPage === totalPage;
    right.onclick = () => {
        if (currentPage < totalPage) {
            window.currentPage++;
            renderNotifications();
            window.scrollTo(0,0);
        }
    };
    pagelist.appendChild(right);

    // Rightmost arrow
    const rightmost = document.createElement('button');
    rightmost.className = 'page-btn arrow-btn';
    rightmost.innerHTML = '&raquo;';
    rightmost.disabled = currentPage === totalPage;
    rightmost.onclick = () => {
        if (currentPage !== totalPage) {
            window.currentPage = totalPage;
            renderNotifications();
            window.scrollTo(0,0);
        }
    };
    pagelist.appendChild(rightmost);
}

// Handle reminder card clicks for navigation
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('reminder-list').addEventListener('click', function(e) {
        const card = e.target.closest('.reminder-card');
        if (card && card.dataset.taskid) {
            const taskID = card.dataset.taskid;

            fetch('../Navbar/navbar_api.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=set_task_session&task_id=${taskID}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.href = '../TaskPage/Task.php';
                } else {
                    alert('Failed to open task: ' + (data.message || 'Unknown error'));
                }
            })
            .catch(err => {
                alert('Error opening task. Please try again.');
            });
        }
    });
});

function navigateNotification(notif) {
    if (notif.RelatedTable === 'task') {
        // Set task session and redirect
        fetch('../Navbar/navbar_api.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `action=set_task_session&task_id=${notif.RelatedID}`
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) window.location.href = '../TaskPage/Task.php';
            else alert('Failed to open task: ' + (data.message || 'Unknown error'));
        });
    } else if (notif.RelatedTable === 'goal') {
        // Set goal and workspace session and redirect
        const goalID = notif.RelatedID;
        const workspaceID = notif.WorkspaceID;
        if (!workspaceID) {
            alert('Workspace for this goal not found!');
            return;
        }
        fetch('../Navbar/navbar_api.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `action=set_goal_session&goal_id=${goalID}&workspace_id=${workspaceID}`
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) window.location.href = '../GoalPage/GoalPage.php';
            else alert('Failed to open goal: ' + (data.message || 'Unknown error'));
        });
    } else if (notif.RelatedTable === 'workspace') {
        // Set workspace session and redirect
        const workspaceID = notif.RelatedID;
        fetch('../Navbar/navbar_api.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `action=set_workspace_session&workspace_id=${workspaceID}`
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) window.location.href = '../WorkspacePage/Workspace.php';
            else alert('Failed to open workspace: ' + (data.message || 'Unknown error'));
        });
    } else {
        // Unknown type: fallback
        alert('Unknown notification type');
    }
}

renderNotifications();