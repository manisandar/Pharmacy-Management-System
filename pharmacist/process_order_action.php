<?php
require_once "../config/db.php";
require_once "../includes/auth_check.php";

if ($_SESSION["role"] !== "pharmacist") {
    die("Unauthorized");
}

if (!isset($_POST["order_id"], $_POST["action"])) {
    die("Invalid request");
}

$order_id = (int)$_POST["order_id"];
$action = $_POST["action"];
$pharmacist_id = $_SESSION["user_id"];

$allergy_notes = $_POST["allergy_notes"] ?? null;
$instructions = $_POST["pharmacist_instructions"] ?? null;
$removed_items = $_POST["removed_items"] ?? [];

/* =========================
   START TRANSACTION
========================= */
$conn->begin_transaction();

try {

    /* ===== 1️⃣ HANDLE REMOVED MEDICINES (APPROVE CASE) ===== */
    if ($action === "approve" && !empty($removed_items)) {

        // removed_items now contains medicine_id values (NOT order_item_id)
        foreach ($removed_items as $medicine_id_raw) {
            $medicine_id = (int)$medicine_id_raw;

            // Get qty for this (order_id, medicine_id) pair
            $stmtItem = $conn->prepare(
                "SELECT quantity FROM order_items WHERE order_id = ? AND medicine_id = ? LIMIT 1"
            );
            $stmtItem->bind_param("ii", $order_id, $medicine_id);
            $stmtItem->execute();
            $res = $stmtItem->get_result();
            $item = $res->fetch_assoc();
            $stmtItem->close();

            if ($item) {
                $qty = (int)$item['quantity'];

                // Restore stock
                $stmtUpdate = $conn->prepare(
                    "UPDATE medicines SET quantity = quantity + ? WHERE medicine_id = ?"
                );
                $stmtUpdate->bind_param("ii", $qty, $medicine_id);
                $stmtUpdate->execute();
                $stmtUpdate->close();

                // Remove item from order using (order_id, medicine_id)
                $stmtDel = $conn->prepare(
                    "DELETE FROM order_items WHERE order_id = ? AND medicine_id = ?"
                );
                $stmtDel->bind_param("ii", $order_id, $medicine_id);
                $stmtDel->execute();
                $stmtDel->close();
            }
        }
    }

    /* ===== 2️⃣ IF REJECT → RESTORE ALL STOCK ===== */
    if ($action === "reject") {

        $items = $conn->query(
            "SELECT medicine_id, quantity
             FROM order_items
             WHERE order_id = $order_id"
        );

        while ($i = $items->fetch_assoc()) {
            $conn->query(
                "UPDATE medicines
                 SET quantity = quantity + {$i['quantity']}
                 WHERE medicine_id = {$i['medicine_id']}"
            );
        }

        // Optional but clean
        $conn->query(
            "DELETE FROM order_items WHERE order_id = $order_id"
        );

    }
    /* ===== 🔁 RECALCULATE TOTAL AMOUNT ===== */
    $totalResult = $conn->query(
        "SELECT COALESCE(SUM(quantity * price), 0) AS total
        FROM order_items
        WHERE order_id = $order_id"
    );

    $totalRow = $totalResult->fetch_assoc();
    $new_total = $totalRow['total'];

    /* ===== UPDATE ORDER TOTAL ===== */
    $conn->query(
        "UPDATE orders
        SET total_amount = $new_total
        WHERE order_id = $order_id"
    );

    if ($action === "approve" && $new_total <= 0) {
        throw new Exception("Order cannot be approved with no medicines.");
    }
    
    /* ===== 3️⃣ UPDATE ORDER ===== */
    $newStatus = ($action === "approve") ? "approved" : "rejected";

    $stmt = $conn->prepare(
        "UPDATE orders
         SET 
            order_status = ?,
            pharmacist_id = ?,
            allergy_notes = ?,
            pharmacist_instructions = ?
         WHERE order_id = ?"
    );

    $stmt->bind_param(
        "sissi",
        $newStatus,
        $pharmacist_id,
        $allergy_notes,
        $instructions,
        $order_id
    );

    $stmt->execute();

    /* ===== 4️⃣ COMMIT ===== */
    $conn->commit();

    header("Location: dashboard.php?success=processed");
    exit;

} catch (Exception $e) {

    $conn->rollback();
    die("Error processing order: " . $e->getMessage());
}
?>