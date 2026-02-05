<?php
$alertMessage = "";
$alertType = "";

/* SUCCESS MESSAGES */
if (isset($_GET["success"])) {
    switch ($_GET["success"]) {

        /* MEDICINE */
        case "medicine_added":
            $alertMessage = "Medicine added successfully";
            break;

        case "medicine_updated":
            $alertMessage = "Medicine updated successfully";
            break;

        case "medicine_deleted":
            $alertMessage = "Medicine deleted successfully";
            break;

        /* SUPPLIER */
        case "supplier_added":
            $alertMessage = "Supplier added successfully";
            break;

        case "supplier_updated":
            $alertMessage = "Supplier updated successfully";
            break;

        case "supplier_deleted":
            $alertMessage = "Supplier deleted successfully";
            break;
        
        case "user_added":
            $alertMessage = "User added successfully";
             break;
        case "user_updated":
            $alertMessage = "User updated successfully";
            break;

        case "user_deleted":
            $alertMessage = "User deleted successfully";
            break;

        case "profile_updated":
            $alertMessage = "Profile updated successfully";
            break;


    }

    if ($alertMessage !== "") {
        $alertType = "success";
    }
}

/* ERROR MESSAGES */
if (isset($_GET["error"])) {
    switch ($_GET["error"]) {

        case "supplier_in_use":
            $alertMessage = "Cannot delete supplier. Supplier is used by medicines.";
            break;

        case "username_taken":
            $alertMessage = "That username is already taken. Please choose another.";
            break;

        case "missing_fields":
            $alertMessage = "Please fill in all required fields.";
            break;

        case "update_failed":
            $alertMessage = "Update failed. Please try again.";
            break;

        default:
            $alertMessage = "Something went wrong";
            break;
    }

    $alertType = "error";
}
?>

<?php if ($alertMessage): ?>
<div class="alert <?= $alertType ?>" id="alertBox" style="opacity:1;">
    <?= htmlspecialchars($alertMessage) ?>
</div>
<script>
    // Auto-dismiss alert after 3 seconds for success, 5 seconds for error
    (function() {
        const alertBox = document.getElementById('alertBox');
        if (alertBox) {
            const isSuccess = alertBox.classList.contains('success');
            const dismissTime = isSuccess ? 3000 : 5000;
            
            setTimeout(function() {
                alertBox.style.transition = 'opacity 0.5s ease-out';
                alertBox.style.opacity = '0';
                setTimeout(function() {
                    alertBox.style.display = 'none';
                }, 500);
            }, dismissTime);
        }
    })();
</script>
<?php endif; ?>
