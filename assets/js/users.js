function openAddUser() {
    document.getElementById("addUserModal").style.display = "flex";
}

function closeAddUser() {
    document.getElementById("addUserModal").style.display = "none";
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

function openEditUser(data) {
    document.getElementById("edit_user_id").value = data.user_id;
    document.getElementById("edit_username").value = data.username;
    document.getElementById("edit_password").value = "";
    document.getElementById("edit_role").value = data.role;
    document.getElementById("editUserModal").style.display = "flex";
}

function closeEditUser() {
    document.getElementById("editUserModal").style.display = "none";
}
