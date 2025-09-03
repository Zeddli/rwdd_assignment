// Dummy notification data - in practice, you'd fetch this from backend
    const notifications = [
        // Example notification structure
        // { id: 1, text: 'Task "Design UI" is due tomorrow', date: '2025-09-01 11:22' },
        // ... add up to N notifications for demo
    ];
    // Demo: create dummy notifications, newest first
    for(let i=300; i>=1; i--) {
        notifications.push({
            id: i,
            text: `Notification #${i}: Task or Goal update/reminder`,
            date: `2025-08-${Math.floor(i/2)+1} 08:${(i*3)%60}` // random date for demo
        });
    }

// Paging logic
const perPage = 20;
let currentPage = 1;

function renderNotifications() {
    const list = document.getElementById('notification-list');
    const pagelist = document.getElementById('pagination-controls');
    list.innerHTML = '';
    pagelist.innerHTML = '';

    if (notifications.length === 0) {
        list.innerHTML = '<div class="no-notification">No Notifications</div>';
        return;
    }

    // Order by date descending (latest first)
    const ordered = [...notifications].sort((a, b) => new Date(b.date) - new Date(a.date));
    const totalPage = Math.ceil(ordered.length / perPage);
    const start = (currentPage-1)*perPage;
    const pageItems = ordered.slice(start, start+perPage);

    // Render notification cards
    pageItems.forEach(n => {
        const card = document.createElement('div');
        card.className = 'notification-card';
        card.innerHTML = `
            <div class="notification-text">${n.text}</div>
            <div class="notification-date">${n.date}</div>
        `;
        list.appendChild(card);
    });

    // Pagination arrows/buttons logic
    if (totalPage > 1) {
        // Leftmost arrow
        const leftmost = document.createElement('button');
        leftmost.className = 'page-btn arrow-btn';
        leftmost.innerHTML = '&laquo;';
        leftmost.disabled = currentPage === 1;
        leftmost.onclick = () => {
            if (currentPage !== 1) {
                currentPage = 1;
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
                currentPage--;
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
                currentPage = i;
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
                currentPage = totalPage;
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
                currentPage = totalPage;
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
                currentPage++;
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
                currentPage = totalPage;
                renderNotifications();
                window.scrollTo(0,0);
            }
        };
        pagelist.appendChild(rightmost);
    }
}
renderNotifications();