document.addEventListener('DOMContentLoaded', function() {
    const filterForm = document.querySelector('.filters');
    if (!filterForm) return;

    const mainContent = document.querySelector('.main-content');
    let debounceTimer;

    // Intercepter les changements dans le formulaire
    filterForm.addEventListener('input', function(e) {
        if (e.target.tagName === 'INPUT' || e.target.tagName === 'SELECT') {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => {
                updateResults();
            }, 300);
        }
    });

    // Intercepter la soumission pour éviter le rechargement
    filterForm.addEventListener('submit', function(e) {
        e.preventDefault();
        updateResults();
    });

    // Délégation d'événements pour la pagination
    mainContent.addEventListener('click', function(e) {
        if (e.target.classList.contains('page-link')) {
            e.preventDefault();
            const url = new URL(e.target.href);
            const params = new URLSearchParams(url.search);
            updateResults(params.get('page'));
        }
    });

    function updateResults(page = 1) {
        const formData = new FormData(filterForm);
        const params = new URLSearchParams(formData);
        params.set('ajax', '1');
        params.set('page', page);

        // Mettre à jour l'URL sans recharger
        const newUrl = window.location.pathname + '?' + params.toString().replace('&ajax=1', '');
        window.history.pushState({ path: newUrl }, '', newUrl);

        // Afficher un indicateur de chargement léger
        const tableContainer = document.querySelector('.table-container');
        tableContainer.style.opacity = '0.5';
        tableContainer.style.pointerEvents = 'none';

        fetch(window.location.pathname + '?' + params.toString())
            .then(response => response.text())
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                
                // Mettre à jour le tableau
                const newTable = doc.querySelector('.table-container');
                if (newTable) {
                    tableContainer.innerHTML = newTable.innerHTML;
                }

                // Mettre à jour la pagination
                const pagination = document.querySelector('.pagination');
                const newPagination = doc.querySelector('.pagination');
                
                if (pagination && newPagination) {
                    pagination.innerHTML = newPagination.innerHTML;
                } else if (!pagination && newPagination) {
                    const pagDiv = document.createElement('div');
                    pagDiv.className = 'pagination';
                    pagDiv.innerHTML = newPagination.innerHTML;
                    tableContainer.after(pagDiv);
                } else if (pagination && !newPagination) {
                    pagination.remove();
                }

                // Mettre à jour le compteur dans le header (optionnel)
                const headerP = document.querySelector('.page-header p');
                const newHeaderP = doc.querySelector('.page-header p');
                if (headerP && newHeaderP) {
                    headerP.textContent = newHeaderP.textContent;
                }

                tableContainer.style.opacity = '1';
                tableContainer.style.pointerEvents = 'auto';
            })
            .catch(error => {
                console.error('Error fetching filters:', error);
                tableContainer.style.opacity = '1';
                tableContainer.style.pointerEvents = 'auto';
            });
    }
});
