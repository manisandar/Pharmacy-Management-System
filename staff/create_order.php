<?php
require_once "../config/db.php";
require_once "../includes/auth_check.php";

if (empty($_POST["order_items"])) {
    header("Location: pending_orders.php?error=no_items");
    exit;
}


$staff_id = $_SESSION["user_id"];

$customer_name = $_POST["customer_name"];
$contact = $_POST["contact_number"];
$address = $_POST["address"];
$prescription_note = $_POST["prescription_note"];
$order_items = json_decode($_POST["order_items"], true);

/* 1️⃣ CREATE CUSTOMER (always new for now – safe) */
$useExisting = $_POST["use_existing_customer"] ?? 0;

if (!empty($_POST["customer_id"])) {
    $customer_id = (int)$_POST["customer_id"];
} else {
    $stmt = $conn->prepare(
        "INSERT INTO customers (customer_name, contact_number, address)
         VALUES (?, ?, ?)"
    );
    $stmt->bind_param("sss", $customer_name, $contact, $address);
    $stmt->execute();
    $customer_id = $stmt->insert_id;
}


/* 2️⃣ CREATE ORDER */
$stmt = $conn->prepare(
    "INSERT INTO orders (customer_id, staff_id, prescription_note)
     VALUES (?, ?, ?)"
);
$stmt->bind_param("iis", $customer_id, $staff_id, $prescription_note);
$stmt->execute();
$order_id = $stmt->insert_id;

$totalAmount = 0;

/* 3️⃣ ORDER ITEMS */
foreach ($order_items as $item) {


    $medicine_id = (int)$item["id"];
    $qty = (int)$item["qty"];
    $price = (float)$item["price"];

    // 1️⃣ Check current stock
    $check = $conn->query(
        "SELECT quantity FROM medicines WHERE medicine_id = $medicine_id"
    )->fetch_assoc();

    if (!$check || $qty > $check["quantity"]) {
        header("Location: pending_orders.php?error=stock");
        exit;
    }

    // 2️⃣ Insert order item
    $stmt = $conn->prepare(
        "INSERT INTO order_items (order_id, medicine_id, quantity, price)
         VALUES (?, ?, ?, ?)"
    );
    $stmt->bind_param(
        "iiid",
        $order_id,
        $medicine_id,
        $qty,
        $price
    );
    $stmt->execute();   
    $subtotal = $qty * $price;
    $totalAmount += $subtotal;

    // 3️⃣ 🔥 DEDUCT STOCK (THIS IS THE KEY)
    $conn->query(
        "UPDATE medicines
         SET quantity = quantity - $qty
         WHERE medicine_id = $medicine_id"
    );
}
/* ✅ UPDATE TOTAL ONCE (CORRECT PLACE) */
$conn->query(
    "UPDATE orders
     SET total_amount = $totalAmount
     WHERE order_id = $order_id"
);

/* DONE */
header("Location: pending_orders.php?success=order_created");
exit;
