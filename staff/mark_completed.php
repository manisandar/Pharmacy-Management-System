<?php
require_once "../config/db.php";

$order_id = (int)$_POST["order_id"];

$conn->query(
    "UPDATE orders
     SET order_status = 'completed'
     WHERE order_id = $order_id"
);
