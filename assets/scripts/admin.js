const msg = document.getElementById('msg');
if (msg) {
    setTimeout(() => {
        msg.classList.add('hide');
    }, 5000);
}

const dropContainer = document.getElementById("dropcontainer"),
    fileInput = document.getElementById("cat_image");
if (fileInput && dropContainer) {
    dropContainer.addEventListener("dragover", (e) => {
        e.preventDefault();
    }, false);

    dropContainer.addEventListener("dragenter", () => {
        dropContainer.classList.add("drag-active");
    });

    dropContainer.addEventListener("dragleave", () => {
        dropContainer.classList.remove("drag-active");
    });

    dropContainer.addEventListener("drop", (e) => {
        e.preventDefault();
        dropContainer.classList.remove("drag-active");
        fileInput.files = e.dataTransfer.files;
    });
}

const collapses = document.querySelectorAll('.collapse');
if (collapses) {
    collapses.forEach(collapse => {
        const title = collapse.querySelector('.title'),
            content = collapse.querySelector('.content');

        content.style.display = 'none';
        title.addEventListener('click', () => {
            const isOpen = content.style.display === 'block';
            content.style.display = isOpen ? 'none' : 'block';
            collapse.classList.toggle('open', !isOpen);
        });
    });
}

function modifyProduct(button) {
    const id = button.getAttribute('data-id'),
        imgSrc = button.parentElement.parentElement.querySelector('img').src,
        title = button.parentElement.parentElement.querySelector('h3').innerText,
        description = button.getAttribute('data-desc');

    let html = `
        <div class="background-blur"></div>
        <div class="popup_update">
            <div class="name">
                <input type="text" value="${title}">
            </div>
            <div class="description">
                <textarea>${description}</textarea> <!-- Champ pour la description -->
            </div>
            <div class="image">
                <img src="${imgSrc}" alt="${title}">
            </div>
            <button class="button send">Modifier</button>
            <p class="close">Annuler</p>
            <form method="POST" action="./products.php?action=update&id=${id}">
                <input type="hidden" name="id" value="${id}">
                <input type="hidden" name="productName" value="">
                <input type="hidden" name="productDescription" value=""> <!-- Champ caché pour la description -->
                <input type="hidden" name="productImage" value="">
            </form>
        </div>
    `;

    let div = document.createElement('div');
    div.classList.add('popup_container', 'open');
    div.innerHTML = html;
    document.body.prepend(div);

    const popup = document.querySelector('.popup_container');
    if (popup) {
        popup.querySelector('.close').addEventListener('click', function () {
            popup.remove();
        });

        popup.querySelector('.button.send').addEventListener('click', function () {
            const form = popup.querySelector('form');

            const catName = popup.querySelector('.name input').value;
            const catDescription = popup.querySelector('.description textarea').value;
            let catImage = popup.querySelector('.image img').src;

            if (!catImage.startsWith('data:image')) {
                toDataURL(catImage, function (dataUrl) {
                    catImage = dataUrl;
                    form.querySelector('input[name="productName"]').value = catName;
                    form.querySelector('input[name="productDescription"]').value = catDescription;
                    form.querySelector('input[name="productImage"]').value = catImage;
                    form.submit();
                });
            } else {
                form.querySelector('input[name="productName"]').value = catName;
                form.querySelector('input[name="productDescription"]').value = catDescription;
                form.querySelector('input[name="productImage"]').value = catImage;
                form.submit();
            }
        });
    }

    const imageElement = div.querySelector('.image img');
    changeImage(imageElement);
}

function toDataURL(url, callback) {
    var xhr = new XMLHttpRequest();
    xhr.onload = function () {
        var reader = new FileReader();
        reader.onloadend = function () {
            callback(reader.result);
        }
        reader.readAsDataURL(xhr.response);
    };
    xhr.open('GET', url);
    xhr.responseType = 'blob';
    xhr.send();
}

function deleteProduct(button) {
    const id = button.getAttribute('data-id');
    let resp = confirm(`Souhaitez-vous supprimer le produit ${id} ?`);
    if (resp) {
        window.location.replace(`./products.php?action=delete&id=${id}`);
    }
}

function initializePopups() {
    const popupButtons = document.querySelectorAll('.button_popup'),
        popupCloseCrosses = document.querySelectorAll('.close'),
        buttonsUpdateP = document.getElementsByClassName('button updateP'),
        buttonsDeleteP = document.getElementsByClassName('button deleteP');

    popupButtons.forEach(button => {
        button.addEventListener('click', function () {
            const popupContent = this.nextElementSibling;
            if (popupContent) {
                togglePopup(popupContent);
            }
        });
    });

    popupCloseCrosses.forEach(close => {
        close.addEventListener('click', function () {
            const popupContent = this.parentElement.parentElement;
            if (popupContent) {
                togglePopup(popupContent);
            }
        })
    })

    for (let i = buttonsUpdateP.length - 1; i >= 0; i--) {
        buttonsUpdateP.item(i).addEventListener('click', function (button) {
            modifyProduct(button.target);
            closePopup();
        });
    }

    for (let i = buttonsDeleteP.length - 1; i >= 0; i--) {
        buttonsDeleteP.item(i).addEventListener('click', function (button) {
            deleteProduct(button.target);
            closePopup();
        });
    }
}

function closePopup() {
    document.querySelector('.open.category').classList.remove('open');
}

function togglePopup(popupContent) {
    popupContent.parentElement.classList.toggle('open');
}

initializePopups();

const buttonsUpdate = document.getElementsByClassName('button update'),
    buttonsDelete = document.getElementsByClassName('button delete');

function modifyCat(button) {
    const id = button.getAttribute('data-id'),
        imgSrc = button.parentElement.parentElement.querySelector('img').src,
        title = button.parentElement.parentElement.querySelector('h2').innerText.replace(/ - ID: ([0-9]+)/i, '');

    let html = `
            <div class="background-blur"></div>
            <div class="popup_update">
                <div class="name">
                    <input type="text" value="${title}">
                </div>
                <div class="image">
                    <img src="${imgSrc}" alt="${title}">
                </div>
                <button class="button send">Modifier</button>
                <p class="close">Annuler</p>
                <form method="POST" action="./category.php?action=update&id=${id}">
                    <input type="hidden" name="id" value="${id}">
                    <input type="hidden" name="catName" value="">
                    <input type="hidden" name="catImage" value="">
                </form>
            </div>
        `;

    let div = document.createElement('div');
    div.classList.add('popup_container', 'open');
    div.innerHTML = html;
    document.body.prepend(div);

    const popup = document.querySelector('.popup_container');
    if (popup) {
        popup.querySelector('.close').addEventListener('click', function () {
            popup.remove();
        });

        popup.querySelector('.button.send').addEventListener('click', function () {
            const form = popup.querySelector('form');

            const catName = popup.querySelector('.name input').value;
            let catImage = popup.querySelector('.image img').src;

            if (!catImage.startsWith('data:image')) {
                toDataURL(catImage, function (dataUrl) {
                    catImage = dataUrl;
                    form.querySelector('input[name="catName"]').value = catName;
                    form.querySelector('input[name="catImage"]').value = catImage;
                    form.submit();
                });
            } else {
                form.querySelector('input[name="catName"]').value = catName;
                form.querySelector('input[name="catImage"]').value = catImage;
                form.submit();
            }
        });
    }

    const imageElement = div.querySelector('.image img');
    changeImage(imageElement);
}


function deleteCat(button) {
    const id = button.getAttribute('data-id');
    let resp = confirm(`Souhaitez-vous supprimer la catégorie ${id} et tous les produits liés ?`);
    if (resp) {
        window.location.replace(`./category.php?action=delete&id=${id}`);
    }
}

function changeImage(imageElement) {
    imageElement.addEventListener('click', () => {
        const fileInput = document.createElement('input');
        fileInput.type = 'file';
        fileInput.accept = 'image/*';

        fileInput.addEventListener('change', (e) => {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = () => {
                    imageElement.src = reader.result;
                    imageElement.alt = file.name;
                };
                reader.readAsDataURL(file);
            }
        });

        fileInput.click();
    });

    const container = imageElement.parentElement;
    container.addEventListener('dragover', (e) => {
        e.preventDefault();
        e.stopPropagation();
        container.classList.add('dragging');
    });

    container.addEventListener('dragleave', (e) => {
        e.preventDefault();
        e.stopPropagation();
        container.classList.remove('dragging');
    });

    container.addEventListener('drop', (e) => {
        e.preventDefault();
        e.stopPropagation();
        container.classList.remove('dragging');

        const file = e.dataTransfer.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = () => {
                imageElement.src = reader.result;
                imageElement.alt = file.name;
            };
            reader.readAsDataURL(file);
        }
    });
}

for (let i = buttonsUpdate.length - 1; i >= 0; i--) {
    buttonsUpdate.item(i).addEventListener('click', function (button) { modifyCat(button.target) });
}

for (let i = buttonsDelete.length - 1; i >= 0; i--) {
    buttonsDelete.item(i).addEventListener('click', function (button) { deleteCat(button.target) });
}

if (document.getElementById('cross')) {
    document.getElementById('#cross').addEventListener('click', function () {
        document.getElementById('add_cat').classList.toggle('open');
        if (!document.getElementById('add_cat').classList.contains('open')) {
            document.querySelectorAll('.collapse').forEach((element) => {
                element.classList.remove('open');
            })
        }
    })
}