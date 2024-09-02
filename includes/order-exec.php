<?php
// Start session
session_start();

// Include database connection details
require_once(__DIR__.'/../config.php');

// Connect to MySQL server
$link = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE);
if (!$link) {
    die("Cannot access db: " . mysqli_connect_error());
}

// Get user and order details from the session and POST data
$user_id = $_SESSION['SESS_USER_ID'];
$od_date = date('Y-m-d');
$od_name = $_POST['name'];
$od_address = $_POST['address'];
$od_city = $_POST['city'];
$od_postal_code = $_POST['postal_code'];
$od_cost = $_SESSION['total'];

// Insert order into `tbl_order`
$qry = "INSERT INTO `tbl_order` (`user_id`, `od_date`, `od_status`, `od_name`, `od_address`, `od_city`, `od_postal_code`, `od_cost`)
        VALUES (?, ?, 'New', ?, ?, ?, ?, ?)";
$stmt = mysqli_prepare($link, $qry);
mysqli_stmt_bind_param($stmt, 'isssssi', $user_id, $od_date, $od_name, $od_address, $od_city, $od_postal_code, $od_cost);
mysqli_stmt_execute($stmt);

// Get the last inserted order ID
$od_id = mysqli_insert_id($link);

// Insert each cart item into `tbl_order_item` and update product quantity in `tbl_product`
foreach ($_SESSION['CART'] as $cart_item_ID => $cart_item) {
    $pd_id = $cart_item['pd_id'];
    
    // Insert order item
    $qry = "INSERT INTO `tbl_order_item` (`od_id`, `pd_id`, `od_qty`) VALUES (?, ?, 1)";
    $stmt = mysqli_prepare($link, $qry);
    mysqli_stmt_bind_param($stmt, 'ii', $od_id, $pd_id);
    mysqli_stmt_execute($stmt);
    
    // Update product quantity
    $qry = "UPDATE `tbl_product` SET `pd_qty` = `pd_qty` - 1 WHERE `pd_id` = ?";
    $stmt = mysqli_prepare($link, $qry);
    mysqli_stmt_bind_param($stmt, 'i', $pd_id);
    mysqli_stmt_execute($stmt);
}

// Check whether the queries were successful
if (mysqli_stmt_affected_rows($stmt) > 0) {
    // Clear the cart and redirect with success message
    unset($_SESSION['CART']);
    $_SESSION['MSGS'] = array('<strong>Wohu!</strong> Your order has been placed.');
    session_write_close();
    header("location: ../profile.php");
    exit();
} else {
    die("Query failed: " . mysqli_error($link));
}

// Close the MySQL connection
mysqli_close($link);
?>
