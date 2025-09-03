// Example dummy data for demo
const dummyResults = [
    { id: 1, type: 'task', name: 'UI Design', link: 'task.html?id=1' },
    { id: 2, type: 'goal', name: 'Complete Website', link: 'goal.html?id=2' },
    { id: 3, type: 'workspace', name: 'Frontend Team', link: 'workspace.html?id=3' },
    { id: 4, type: 'task', name: 'Write Documentation', link: 'task.html?id=4' },
    { id: 5, type: 'goal', name: 'Release MVP', link: 'goal.html?id=5' }
];

const searchBar = document.getElementById('search-bar');
const resultsDiv = document.getElementById('search-results');
const promptDiv = document.getElementById('search-prompt');

searchBar.addEventListener('input', function() {
    const q = searchBar.value.trim().toLowerCase();
    // In real implementation, make AJAX call to backend here
    if (!q) {
        resultsDiv.innerHTML = '';
        promptDiv.style.display = 'block';
        return;
    }
    promptDiv.style.display = 'none';
    // Filter results by name
    const results = dummyResults.filter(r => r.name.toLowerCase().includes(q));
    if (results.length === 0) {
        resultsDiv.innerHTML = '<div class="no-result-card">No results found</div>';
    } else {
        resultsDiv.innerHTML = '';
        results.forEach(r => {
            const card = document.createElement('div');
            card.className = 'result-card';
            card.innerHTML = `<span class="result-type">${r.type}</span>: <span class="result-name">${r.name}</span>`;
            card.onclick = () => {
                // Redirect to the relative page
                window.location.href = r.link;
            };
            resultsDiv.appendChild(card);
        });
    }
});