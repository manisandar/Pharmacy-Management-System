<!-- ADD USER MODAL -->
<div id="addUserModal" class="modal-overlay">
    <div class="modal-box">
        <div class="modal-header">
            <h3>Add User</h3>
            <button class="modal-close" onclick="closeAddUser()">×</button>
        </div>

        <div class="modal-body">
            <form method="POST" action="user_add.php">

                <div class="form-group">
                    <label>Username *</label>
                    <input type="text" name="username" required>
                </div>

                <div class="form-group">
                    <label>Password (leave blank to keep current)</label>
                    <input type="password" name="password" required>
                </div>

                <div class="form-group">
                    <label>Role *</label>
                    <select name="role" required>
                        <option value="">Select role</option>
                        <option value="admin">Admin</option>
                        <option value="staff">Staff</option>
                        <option value="pharmacist">Pharmacist</option>
                    </select>
                </div>

                <button type="submit" class="btn-primary full-width">
                    Create User
                </button>

            </form>
        </div>
    </div>
</div>

<!-- EDIT USER MODAL -->
<div id="editUserModal" class="modal-overlay">
    <div class="modal-box">
        <div class="modal-header">
            <h3>Edit User</h3>
            <button class="modal-close" onclick="closeEditUser()">×</button>
        </div>

        <div class="modal-body">
            <form method="POST" action="user_edit.php">

                <input type="hidden" name="user_id" id="edit_user_id">

                <div class="form-group">
                    <label>Username *</label>
                    <input type="text" name="username" id="edit_username" required>
                </div>

                <div class="form-group">
                    <label>Password (leave blank to keep current)</label>
                    <input type="password" name="password" id="edit_password" placeholder="Enter new password">
                </div>

                <div class="form-group">
                    <label>Role *</label>
                    <select name="role" id="edit_role" required>
                        <option value="admin">Admin</option>
                        <option value="staff">Staff</option>
                        <option value="pharmacist">Pharmacist</option>
                    </select>
                </div>

                <button type="submit" class="btn-primary full-width">
                    Update User
                </button>

            </form>
        </div>
    </div>
</div>

