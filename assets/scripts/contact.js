const input = document.getElementById("subject"),
    div = document.getElementById("select_input");
input.addEventListener("change", (() => {
    "devis-buy" === input.value || "devis-shop" === input.value
        ? div.innerHTML = '<label for="product-name">Nom du / des produit(s) concernés</label><input type="text" name="product-name" id="product-name"></input>'
        : "candidature" === input.value ? div.innerHTML = '<div class="form_input"><label for="cv">Votre CV <span class="required">*</span></label><input type="file" accept=".pdf,.doc,.docx,.txt,image/*" name="cv" id="cv" required></input></div><div class="form_input"><label for="motivation">Votre lettre de motivation</label><input type="file" accept=".pdf,.doc,.docx,.txt,image/*" name="motivation" id="motivation"></input></div>'
            : "autre" === input.value ? div.innerHTML = '<label for="other">Précisez votre demande <span class="required">*</span></label><input type="text" name="other" id="other" required></input>'
                : div.innerHTML = ""
}));