const searchBar = document.getElementById('search-bar');
const resultsDiv = document.getElementById('search-results');
const promptDiv = document.getElementById('search-prompt');

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
                        // Only show workspace name, styled
                        card.innerHTML = `<span class="result-type" style="color:#1565c0; font-weight:600;">Workspace</span>: <span class="result-name">${r.name}</span>`;
                    } else {
                        // For task/goal, show workspace name in bottom line
                        card.innerHTML = `<span class="result-type">${r.type}</span>: <span class="result-name">${r.name}</span>
                        <div class="result-workspace" style="margin-top:2px; font-weight:500; color:#1565c0; font-size:0.98em;">Workspace: ${r.workspace}</div>`;
                    }
                    card.onclick = () => {
                        window.location.href = r.link;
                    };
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