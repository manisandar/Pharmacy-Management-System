<?php
require_once "../config/db.php";

$order_id = (int)$_GET["order_id"];

$order = $conn->query(
    "SELECT o.*, c.customer_name, c.contact_number
     FROM orders o
     JOIN customers c ON o.customer_id = c.customer_id
     WHERE o.order_id = $order_id"
)->fetch_assoc();

$items = $conn->query(
    "SELECT m.medicine_name, oi.quantity, oi.price
     FROM order_items oi
     JOIN medicines m ON oi.medicine_id = m.medicine_id
     WHERE oi.order_id = $order_id"
);
?>

<div class="bill-container">
    <h2 style="text-align:center;">PharmFlow Pharmacy</h2>
    <p style="text-align:center;">123, Abac Street, Bangbo</p>
    <hr>

    <p><b>Order ID:</b> <?= $order_id ?></p>
    <p><b>Name:</b> <?= htmlspecialchars($order["customer_name"]) ?></p>
    <p><b>Contact:</b> <?= htmlspecialchars($order["contact_number"]) ?></p>
    <p><b>Date:</b> <?= $order["order_date"] ?></p>

    <hr>

    <table width="100%">
        <tr>
            <th align="left">Medicine</th>
            <th align="right">Amount</th>
        </tr>

        <?php $total = 0; ?>
        <?php while ($i = $items->fetch_assoc()): ?>
            <?php $sub = $i["quantity"] * $i["price"]; ?>
            <?php $total += $sub; ?>
            <tr>
                <td><?= $i["medicine_name"] ?> (<?= $i["quantity"] ?> × <?= $i["price"] ?>)</td>
                <th align="right" class="amount-cell"><?= number_format($sub, 2) ?>฿</th>
            </tr>
        <?php endwhile; ?>
    </table>

    <hr>

    <h3>Total: <?= number_format($total, 2) ?>฿</h3>
    
    
    <p><b>Allergy Check:</b><br>
        <?= nl2br(htmlspecialchars($order["allergy_notes"])) ?>
    </p>
    <p><b>Pharmacist Instructions:</b><br>
        <?= nl2br(htmlspecialchars($order["pharmacist_instructions"])) ?>
    </p>
</div>
