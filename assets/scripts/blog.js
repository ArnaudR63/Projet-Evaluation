const searchInput = document.getElementById('keyword'),
    searchButton = document.getElementById('search'),
    filterList = document.querySelectorAll('#filters_cat input[type="checkbox"]'),
    resetFilters = document.getElementById('reset'),
    htmlContainer = document.getElementById('posts_container');

if (htmlContainer) {
    function fetchFilteredProducts() {
        const selectedFilters = filterList.length > 0
            ? Array.from(filterList).filter(filter => filter.checked).map(filter => filter.name)
            : [];

        const keyword = searchInput ? searchInput.value.trim() : '';

        sendAjaxRequest({
            keywords: keyword ? [keyword] : [],
            categories: selectedFilters
        });
    }

    if (searchButton && searchInput) {
        searchInput.value = localStorage.getItem('keyword') || '';

        searchButton.addEventListener('click', () => {
            localStorage.setItem('keyword', searchInput.value.trim());
            fetchFilteredProducts();
        });
    }

    if (resetFilters) {
        resetFilters.addEventListener('click', () => {
            localStorage.removeItem('keyword');
            if (searchInput) searchInput.value = '';
            if (filterList.length > 0) {
                filterList.forEach(filter => (filter.checked = false));
            }
            fetchFilteredProducts();
        });
    }

    if (filterList.length > 0) {
        filterList.forEach(filter => {
            filter.addEventListener('change', fetchFilteredProducts);
        });
    }
} else {
    console.error('Impossible de récupérer le conteneur HTML');
}

function sendAjaxRequest(data) {
    const formData = new FormData();
    formData.append('data', JSON.stringify(data));

    fetch('filter_post.php', {
        method: 'POST',
        body: formData
    })
        .then(response => response.text())
        .then(text => {
            console.log('Réponse brute du serveur:', text);
            return JSON.parse(text);
        })
        .then(data => {
            if (data.html) {
                htmlContainer.innerHTML = data.html;
            } else {
                console.error('Réponse inattendue du serveur :', data);
            }
        })
        .catch(error => console.error('Erreur AJAX :', error));

}
