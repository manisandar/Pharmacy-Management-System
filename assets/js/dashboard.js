function openModal() {
    document.getElementById("modalOverlay").style.display = "flex";
}

function closeModal() {
    document.getElementById("modalOverlay").style.display = "none";
}

// Close when clicking outside modal
document.getElementById("modalOverlay").addEventListener("click", function (e) {
    if (e.target === this) {
        closeModal();
    }
});

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

function openEditModal(data) {
    document.getElementById("edit_medicine_id").value = data.medicine_id;
    document.getElementById("edit_medicine_name").value = data.medicine_name;
    document.getElementById("edit_chemical_name").value = data.chemical_name || "";
    document.getElementById("edit_dosage_form").value = data.dosage_form;
    document.getElementById("edit_price").value = data.price_per_unit;
    document.getElementById("edit_quantity").value = data.quantity;
    document.getElementById("edit_reorder").value = data.reorder_level;
    document.getElementById("edit_expiry").value = data.expiry_date;
    document.getElementById("edit_supplier").value = data.supplier_id;

    document.getElementById("editModalOverlay").style.display = "flex";
}

function closeEditModal() {
    document.getElementById("editModalOverlay").style.display = "none";
}


const toggleBtn = document.getElementById("themeToggle");
const body = document.body;

/* Load saved theme */
if (localStorage.getItem("theme") === "dark") {
    body.classList.add("dark-mode");
    toggleBtn.textContent = "☀️";
}

/* Toggle */
toggleBtn.addEventListener("click", () => {
    body.classList.toggle("dark-mode");

    if (body.classList.contains("dark-mode")) {
        localStorage.setItem("theme", "dark");
        toggleBtn.textContent = "☀️";
    } else {
        localStorage.setItem("theme", "light");
        toggleBtn.textContent = "🌙";
    }
});