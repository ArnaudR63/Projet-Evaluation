function toggleMenu() {
    document.getElementById('toggle-menu').classList.toggle('open');
    menu = document.getElementById('menu').classList.toggle('open');
}

function toggleAdmin() {
    document.querySelector('#account>a').parentElement.classList.toggle('open');
}