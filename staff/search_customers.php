<?php
require_once "../config/db.php";

$q = trim($_GET["q"] ?? "");
if ($q === "") {
    echo json_encode([]);
    exit;
}

$stmt = $conn->prepare(
    "SELECT customer_id, customer_name, contact_number
     FROM customers
     WHERE customer_name LIKE ?
     ORDER BY customer_name
     LIMIT 10"
);

$like = "%$q%";
$stmt->bind_param("s", $like);
$stmt->execute();

$res = $stmt->get_result();
$data = [];

while ($row = $res->fetch_assoc()) {
    $data[] = $row;
}

echo json_encode($data);
