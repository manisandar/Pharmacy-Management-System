<!-- ADD SUPPLIER MODAL -->
<div id="addSupplierModal" class="modal-overlay">
    <div class="modal-box">
        <div class="modal-header">
            <h3>Add Supplier</h3>
            <button class="modal-close" onclick="closeAddSupplier()">×</button>
        </div>

        <div class="modal-body">
            <form method="POST" action="supplier_add.php">
                <label>Supplier Name *</label>
                <input type="text" name="supplier_name" required>

                <label>Contact Number</label>
                <input type="text" name="contact_number">

                <button class="btn-primary full-width">Save Supplier</button>
            </form>
        </div>
    </div>
</div>

<!-- EDIT SUPPLIER MODAL -->
<div id="editSupplierModal" class="modal-overlay">
    <div class="modal-box">
        <div class="modal-header">
            <h3>Edit Supplier</h3>
            <button class="modal-close" onclick="closeEditSupplier()">×</button>
        </div>

        <div class="modal-body">
            <form method="POST" action="supplier_edit.php">
                <input type="hidden" name="supplier_id" id="edit_supplier_id">

                <label>Supplier Name *</label>
                <input type="text" name="supplier_name" id="edit_supplier_name" required>

                <label>Contact Number</label>
                <input type="text" name="contact_number" id="edit_contact">

                <button class="btn-primary full-width">Update Supplier</button>
            </form>
        </div>
    </div>
</div>
