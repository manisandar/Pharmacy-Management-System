function openAddSupplier() {
    document.getElementById("addSupplierModal").style.display = "flex";
}

function closeAddSupplier() {
    document.getElementById("addSupplierModal").style.display = "none";
}

function openEditSupplier(data) {
    document.getElementById("edit_supplier_id").value = data.supplier_id;
    document.getElementById("edit_supplier_name").value = data.supplier_name;
    document.getElementById("edit_contact").value = data.contact_number || "";
    document.getElementById("editSupplierModal").style.display = "flex";
}

function closeEditSupplier() {
    document.getElementById("editSupplierModal").style.display = "none";
}

const alertBox = document.getElementById("alertBox");

    if (alertBox) {
        setTimeout(() => {
            alertBox.style.opacity = "0";
            alertBox.style.transition = "opacity 0.5s ease";
        }, 2500);

        setTimeout(() => {
            alertBox.remove();
        }, 2200);
}
