<div id="orderModal" class="modal-overlay" style="display:none;">
    <div class="modal-box large">

        <div class="modal-header">
            <h3>Create New Order</h3>
            <button class="modal-close" onclick="closeOrderModal()">×</button>
        </div>

        <form method="POST" action="create_order.php">

            <div class="form-row">
                <div class="form-group full">
                    <label>Customer</label>
                    <select name="customer_id" required>
                        <option value="">Select customer</option>
                        <?php
                        $customers = $conn->query(
                            "SELECT customer_id, customer_name
                             FROM customers
                             ORDER BY customer_name"
                        );
                        while ($c = $customers->fetch_assoc()) {
                            echo "<option value='{$c['customer_id']}'>
                                    {$c['customer_name']}
                                  </option>";
                        }
                        ?>
                    </select>
                </div>
            </div>

            <!-- You can extend medicine logic later -->

            <button type="submit" class="btn-primary full-width">
                Create Order
            </button>
        </form>

    </div>
</div>
