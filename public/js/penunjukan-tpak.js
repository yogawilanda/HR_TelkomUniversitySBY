document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.querySelector('input[name="search"]');
    const tableBody = document.querySelector('tbody');
    const searchForm = searchInput.closest('form');
    const loader = document.createElement('tr');
    loader.innerHTML = '<td colspan="4" class="text-center py-4"><i class="fas fa-spinner fa-spin"></i> Loading...</td>';

    if (!searchInput || !tableBody) return;

    // Prevent form submit
    searchForm.addEventListener('submit', e => e.preventDefault());

    let timeout;
    searchInput.addEventListener('input', function() {
        clearTimeout(timeout);
        timeout = setTimeout(async () => {
            const query = this.value;
            tableBody.parentNode.replaceChild(loader.cloneNode(true), tableBody);
            
            try {
                const url = new URL(searchForm.action);
                url.searchParams.set('search', query);
                const response = await fetch(url, { headers: {'X-Requested-With': 'XMLHttpRequest'} });
                if (response.ok) {
                    const html = await response.text();
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    const newTableBody = doc.querySelector('tbody');
                    const newPagination = doc.querySelector('.pagination-container') || doc.querySelector('[role="navigation"]');
                    if (newTableBody) tableBody.parentNode.replaceChild(newTableBody, tableBody);
                    if (newPagination) {
                        const paginationContainer = document.querySelector('.pagination-container') || document.querySelector('[role="navigation"]');
                        if (paginationContainer) paginationContainer.replaceWith(newPagination);
                    }
                }
            } catch (e) {
                console.error('Search error:', e);
            }
        }, 300);
    });
});
