const searchBar = document.getElementById('search-bar');
const resultsDiv = document.getElementById('search-results');
const promptDiv = document.getElementById('search-prompt');

// Session-based navigation
function navigateSearchResult(r) {
    if (r.type === 'task') {
        fetch('../Navbar/navbar_api.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `action=set_task_session&task_id=${r.id}`
        }).then(res => res.json())
          .then(data => {
            if (data.success) window.location.href = '../TaskPage/Task.php';
            else alert('Failed to open task: ' + (data.message || 'Unknown error'));
          })
          .catch(() => alert('Error opening task. Please try again.'));
    } else if (r.type === 'goal') {
        fetch('../Navbar/navbar_api.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `action=set_goal_session&goal_id=${r.id}&workspace_id=${r.workspaceid}`
        }).then(res => res.json())
          .then(data => {
            if (data.success) window.location.href = '../GoalPage/GoalPage.php';
            else alert('Failed to open goal: ' + (data.message || 'Unknown error'));
          })
          .catch(() => alert('Error opening goal. Please try again.'));
    } else if (r.type === 'workspace') {
        fetch('../Navbar/navbar_api.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `action=set_workspace_session&workspace_id=${r.id}`
        }).then(res => res.json())
          .then(data => {
            if (data.success) window.location.href = '../WorkspacePage/Workspace.php';
            else alert('Failed to open workspace: ' + (data.message || 'Unknown error'));
          })
          .catch(() => alert('Error opening workspace. Please try again.'));
    } else {
        window.location.href = r.link;
    }
}

searchBar.addEventListener('input', function() {
    const q = searchBar.value.trim();
    if (!q) {
        resultsDiv.innerHTML = '';
        promptDiv.style.display = 'block';
        promptDiv.textContent = 'Type to search for tasks, goals, or workspace...';
        return;
    }
    promptDiv.style.display = 'none';

    fetch(`search_api.php?q=${encodeURIComponent(q)}`)
        .then(res => res.json())
        .then(results => {
            if (results.length === 0) {
                resultsDiv.innerHTML = '<div class="no-result-card">No results found</div>';
            } else {
                resultsDiv.innerHTML = '';
                results.forEach(r => {
                    const card = document.createElement('div');
                    card.className = 'result-card';

                    if (r.type === 'workspace') {
                        card.innerHTML = `<span class="result-type" style="color:#1565c0; font-weight:600;">Workspace</span>: <span class="result-name">${r.name}</span>`;
                    } else {
                        card.innerHTML = `<span class="result-type">${r.type}</span>: <span class="result-name">${r.name}</span>
                        <div class="result-workspace" style="margin-top:2px; font-weight:500; color:#1565c0; font-size:0.98em;">Workspace: ${r.workspace}</div>`;
                    }
                    card.onclick = () => navigateSearchResult(r);
                    resultsDiv.appendChild(card);
                });
            }
        })
        .catch(() => {
            resultsDiv.innerHTML = '<div class="no-result-card">Search error</div>';
        });
});
promptDiv.textContent = 'Type to search for tasks, goals, or workspaces...';
promptDiv.style.display = 'block';