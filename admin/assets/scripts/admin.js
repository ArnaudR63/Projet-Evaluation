function parts() {
    fetch("./load_parts.php")
        .then(response => response.text())
        .then(data => {
            document.getElementById("admin_container").innerHTML = data;
        })
        .catch(error => console.error("Erreur lors du chargement des parties admin :", error));
}
parts();

function dropImage() {
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
}

function collapses() {
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
}

function modifyProduct(button) {
    const id = button.getAttribute('data-id'),
        imgSrc = button.parentElement.parentElement.querySelector('img').src,
        title = button.parentElement.parentElement.querySelector('h3').innerText,
        description = button.getAttribute('data-desc'),
        initialCategory = button.parentElement.parentElement.getAttribute("data-cat"),
        categories = button.parentElement.parentElement.getAttribute("data-list").split(","),
        categoriesName = button.parentElement.parentElement.getAttribute("data-listName").split(",");
    let categoriesHtml = "";
    if (categories.length === categoriesName.length) {
        console.log(categories, categoriesName);
        for (let i = 0; i <= categories.length - 1; i++) {
            if (categories[i] === initialCategory) {
                categoriesHtml += `<option value="${categories[i]}" selected="selected">${categoriesName[i]}</option>`;
            } else {
                categoriesHtml += `<option value="${categories[i]}">${categoriesName[i]}</option>`;
            }
        }
    } else {
        console.error('Nombre de catégories différents');
        return;
    }

    let html = `<div class="background-blur"></div>
        <div class="popup_update">
            <div class="name">
                <input type="text" value="${title}">
            </div>
              <div class="category">
                <select>
                ${categoriesHtml}
                </select>
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
                <input type="hidden" name="catId" value="">
                <input type="hidden" name="productName" value="">
                <input type="hidden" name="productDescription" value="">
                <input type="hidden" name="productImage" value="">
            </form>
        </div>`;

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
            const catId = popup.querySelector('.category select').value;
            const catDescription = popup.querySelector('.description textarea').value;
            let catImage = popup.querySelector('.image img').src;

            if (!catImage.startsWith('data:image')) {
                toDataURL(catImage, function (dataUrl) {
                    catImage = dataUrl;
                    form.querySelector('input[name="productName"]').value = catName;
                    form.querySelector('input[name="catId"]').value = catId;
                    form.querySelector('input[name="productDescription"]').value = catDescription;
                    form.querySelector('input[name="productImage"]').value = catImage;
                    form.submit();
                });
            } else {
                form.querySelector('input[name="productName"]').value = catName;
                form.querySelector('input[name="catId"]').value = catId;
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

function modifyPost(button) {
    const id = button.getAttribute('data-id'),
        imgSrc = button.parentElement.parentElement.querySelector('img').src,
        title = button.parentElement.parentElement.querySelector('h3').innerText,
        description = button.getAttribute('data-desc'),
        initialCategory = button.parentElement.parentElement.getAttribute("data-cat"),
        categories = button.parentElement.parentElement.getAttribute("data-list").split(","),
        categoriesName = button.parentElement.parentElement.getAttribute("data-listName").split(",");
    let categoriesHtml = "";
    if (categories.length === categoriesName.length) {
        for (let i = 0; i <= categories.length - 1; i++) {
            if (categories[i] === initialCategory) {
                categoriesHtml += `<option value="${categories[i]}" selected="selected">${categoriesName[i]}</option>`;
            } else {
                categoriesHtml += `<option value="${categories[i]}">${categoriesName[i]}</option>`;
            }
        }
    } else {
        console.error('Nombre de catégories différents');
        return;
    }

    let html = `<div class="background-blur"></div>
        <div class="popup_update">
            <div class="name">
                <input type="text" value="${title}">
            </div>
              <div class="category" >
                <select value="${initialCategory}">
                ${categoriesHtml}
                </select>
            </div>
            <div class="description">
                <textarea>${description}</textarea> <!-- Champ pour la description -->
            </div>
            <div class="image">
                <img src="${imgSrc}" alt="${title}">
            </div>
            <button class="button send">Modifier</button>
            <p class="close">Annuler</p>
            <form method="POST" action="./post.php?action=update&id=${id}">
                <input type="hidden" name="id" value="${id}">
                <input type="hidden" name="catId" value="">
                <input type="hidden" name="postTitle" value="">
                <input type="hidden" name="postDescription" value="">
                <input type="hidden" name="postImage" value="">
            </form>
        </div>`;

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
            const catId = popup.querySelector('.category select').value;
            const catDescription = popup.querySelector('.description textarea').value;
            let catImage = popup.querySelector('.image img').src;

            if (!catImage.startsWith('data:image')) {
                toDataURL(catImage, function (dataUrl) {
                    catImage = dataUrl;
                    form.querySelector('input[name="postTitle"]').value = catName;
                    form.querySelector('input[name="catId"]').value = catId;
                    form.querySelector('input[name="postDescription"]').value = catDescription;
                    form.querySelector('input[name="postImage"]').value = catImage;
                    form.submit();
                });
            } else {
                form.querySelector('input[name="postTitle"]').value = catName;
                form.querySelector('input[name="catId"]').value = catId;
                form.querySelector('input[name="postDescription"]').value = catDescription;
                form.querySelector('input[name="postImage"]').value = catImage;
                form.submit();
            }
        });
    }

    const imageElement = div.querySelector('.image img');
    changeImage(imageElement);
}

function deletePost(button) {
    const id = button.getAttribute('data-id');
    let resp = confirm(`Souhaitez-vous supprimer le post ${id} ?`);
    if (resp) {
        window.location.replace(`./post.php?action=delete&id=${id}`);
    }
}

function initializePopups() {
    const popupButtons = document.querySelectorAll('.button_popup'),
        popupCloseCrosses = document.querySelectorAll('.close'),
        buttonsUpdateP = document.getElementsByClassName('button updateP'),
        buttonsDeleteP = document.getElementsByClassName('button deleteP'),
        buttonsUpdatePost = document.getElementsByClassName('button updatePost'),
        buttonsDeletePost = document.getElementsByClassName('button deletePost');

    popupButtons.forEach(button => {
        button.addEventListener('click', function () {
            const popupContent = this.nextElementSibling.nextElementSibling;
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

    for (let i = buttonsUpdatePost.length - 1; i >= 0; i--) {
        buttonsUpdatePost.item(i).addEventListener('click', function (button) {
            modifyPost(button.target);
            closePopup();
        });
    }

    for (let i = buttonsDeletePost.length - 1; i >= 0; i--) {
        buttonsDeletePost.item(i).addEventListener('click', function (button) {
            deletePost(button.target);
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

function modifyCat(button) {
    const id = button.getAttribute('data-id'),
        imgSrc = button.parentElement.parentElement.querySelector('img').src,
        title = button.parentElement.parentElement.querySelector('h3').innerText.replace(/ - ID: ([0-9]+)/i, ''),
        catType = button.parentElement.parentElement.querySelector('.item');
    if (catType) {
        catAction = catType.classList.contains('product') ? 'category' : 'postCategory';
    } else {
        catAction = button.parentElement.parentElement.parentElement.querySelector('.item').classList.contains('product') ? 'category' : 'postCategory';
    }
    let html =
        `  <div class="background-blur"></div>
            <div class="popup_update">
                <div class="name">
                    <input type="text" value="${title}">
                </div>
                <div class="image">
                    <img src="${imgSrc}" alt="${title}">
                </div>
                <button class="button send">Modifier</button>
                <p class="close">Annuler</p>
                <form method="POST" action="./${catAction}.php?action=update&id=${id}">
                    <input type="hidden" name="id" value="${id}">
                    <input type="hidden" name="catName" value="">
                    <input type="hidden" name="catImage" value="">
                </form>
            </div>`;

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

function deleteProductCat(button) {
    const id = button.getAttribute('data-id');
    let resp = confirm(`Souhatiez-vous supprimer la catégorie ${id} et tous les produits liés ?`);
    if (resp) {
        window.location.replace(`./category.php?action=delete&id=${id}`);
    }
}

function deletePostCat(button) {
    const id = button.getAttribute('data-id');
    let resp = confirm(`Souhatiez-vous supprimer la catégorie ${id} et tous les posts liés ?`);
    if (resp) {
        window.location.replace(`./postCategory.php?action=delete&id=${id}`);
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

function initAdminCarousel() {
    const container = document.getElementById('admin_container');
    const wrapper = document.getElementById('carousel_container');
    const elements = wrapper.querySelectorAll('.carousel_element');
    let currentIndex = 0;

    function nextSlide() {
        elements[currentIndex].classList.remove('active');
        currentIndex = (currentIndex + 1) % elements.length;
        elements[currentIndex].classList.add('active');
    }

    function prevSlide() {
        elements[currentIndex].classList.remove('active');
        currentIndex = (currentIndex - 1 + elements.length) % elements.length;
        elements[currentIndex].classList.add('active');
    }

    elements[currentIndex].classList.add('active');
    container.querySelector('.carousel-next').addEventListener('click', nextSlide);
    container.querySelector('.carousel-prev').addEventListener('click', prevSlide);
}

setTimeout(() => {
    const buttonsUpdate = document.querySelectorAll('.button.update'),
        buttonsDelete = document.querySelectorAll('.button.delete');

    for (let i = buttonsUpdate.length - 1; i >= 0; i--) {
        buttonsUpdate.item(i).addEventListener('click', function (button) { modifyCat(button.target) });
    }

    for (let i = buttonsDelete.length - 1; i >= 0; i--) {
        buttonsDelete.item(i).addEventListener('click', function (button) { deleteCat(button.target) });
    }
    initAdminCarousel();
    initializePopups();
    collapses();
    dropImage();
}, 400);

const msg = document.getElementById('msg');
if (msg) {
    setTimeout(() => {
        msg.classList.add('hide');
    }, 5000);
}