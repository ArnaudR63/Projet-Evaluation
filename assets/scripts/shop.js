const filters = document.querySelectorAll(".filter input"),
    productsContainer = document.getElementById("products_container"),
    filterTitle = document.querySelector('#filters_list>h2');

function fetchFilteredProducts() {
    const selectedCategories = Array.from(filters)
        .filter(input => input.checked)
        .map(input => input.name);

    const formData = new FormData();
    formData.append("categories", JSON.stringify(selectedCategories));

    fetch("filter_products.php", {
        method: "POST",
        body: formData,
    })
        .then(response => response.text())
        .then(html => {
            productsContainer.innerHTML = html;
        })
        .catch(error => console.error("Erreur lors de la récupération des produits :", error));
}

filters.forEach(filter => {
    filter.addEventListener("change", fetchFilteredProducts);
});
document.getElementById('reset').addEventListener('click', () => {
    let i = 0
    filters.forEach((filter) => {
        if (filter.checked === true) {
            i++;
        }
        filter.checked = false;
    });

    if (i > 0) {
        fetchFilteredProducts();
    }
});

if (window.innerWidth <= 980 && filterTitle) {
    filterTitle.addEventListener('click', function () {
        filterTitle.parentElement.classList.toggle('open');
    });
}