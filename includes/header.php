<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<div class="top-bar">
    <div class="brand">
        <h2>PharmFlow Pharmacy</h2>
        <small>123, Abac Street, Bangbo</small>
    </div>

    <div class="user-info">
        <button id="themeToggle" class="theme-toggle" title="Toggle dark mode">
            🌙
        </button>
        <div class="user-text">
            <span class="username"><?= htmlspecialchars($_SESSION["username"]) ?></span>
            <span class="role"><?= htmlspecialchars($_SESSION["role"]) ?></span>
        </div>
            <?php if (isset($_SESSION['role']) && in_array($_SESSION['role'], ['pharmacist','staff'])): ?>
                <button id="editProfileBtn">Edit Profile</button>
            <?php endif; ?>
            <a href="../auth/logout.php" class="logout-btn">Logout</a>
    </div>
</div>

    <?php /* Edit Profile Modal (available to staff & pharmacist) */ ?>
    <?php if (isset($_SESSION['role']) && in_array($_SESSION['role'], ['pharmacist','staff'])): ?>
    <div id="editProfileModal" class="modal-overlay" style="display:none;">
        <div class="modal-box">
            <div class="modal-header">
                <h3>Edit Profile</h3>
                <button class="modal-close" onclick="closeHeaderEditProfile()">×</button>
            </div>

            <form method="POST" action="../auth/update_profile.php">
                <input type="hidden" name="user_id" value="<?= htmlspecialchars($_SESSION['user_id']) ?>">

                <div id="editProfileError" style="display:none;color:#c0392b;margin-bottom:10px;font-size:14px;"></div>

                <div class="form-group full">
                    <label>Username</label>
                    <input type="text" name="username" value="<?= htmlspecialchars($_SESSION['username']) ?>" required>
                </div>

                <div class="form-group full">
                    <label>New Password (leave blank to keep current)</label>
                    <input type="password" name="password" placeholder="Enter new password">
                </div>

                <div class="modal-actions">
                    <button type="submit" class="btn-primary">Save</button>
                    <button type="button" class="btn" onclick="closeHeaderEditProfile()">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <script>
    function openHeaderEditProfile() {
        const modal = document.getElementById('editProfileModal');
        if (modal) modal.style.display = 'flex';
    }
    function closeHeaderEditProfile() {
        const modal = document.getElementById('editProfileModal');
        if (modal) modal.style.display = 'none';
    }

    const headerEditBtn = document.getElementById('editProfileBtn');
    if (headerEditBtn) {
        headerEditBtn.addEventListener('click', function(e) {
            e.preventDefault();
            openHeaderEditProfile();
        });
    }

    // If redirected back with error=username_taken, open modal and show inline error
    (function() {
        try {
            const params = new URLSearchParams(window.location.search);
            const err = params.get('error');
            if (err === 'username_taken') {
                const modalErr = document.getElementById('editProfileError');
                if (modalErr) {
                    modalErr.textContent = 'That username is already taken. Please choose another.';
                    modalErr.style.display = 'block';
                }
                openHeaderEditProfile();

                // Remove the error param from URL so refresh doesn't reopen modal
                params.delete('error');
                const newUrl = window.location.pathname + (params.toString() ? '?' + params.toString() : '');
                history.replaceState(null, '', newUrl);
            }
        } catch (e) {
            // ignore
        }
    })();
    </script>
    <?php endif; ?>


