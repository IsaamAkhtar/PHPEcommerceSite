<?php
// Include database connection details
require_once(__DIR__ . '/../config.php');

// Connect to MySQL server
$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE);

// Check connection
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

$orders = [];

// Get all orders
$sql = "SELECT tbl_order.*, GROUP_CONCAT(pd_name SEPARATOR ', ') AS products
        FROM tbl_order
        INNER JOIN tbl_order_item ON tbl_order.od_id = tbl_order_item.od_id
        INNER JOIN tbl_product ON tbl_product.pd_id = tbl_order_item.pd_id
        GROUP BY tbl_order.od_id";

if ($result = $mysqli->query($sql)) {
    while ($row = $result->fetch_object()) {
        $orders[] = $row;
    }
    $result->free();
} else {
    die("Query failed: " . $mysqli->error);
}

// Close MySQL connection
$mysqli->close();
?>