<?php
/*
 * This script handles category management operations:
 * 1. Connects to the MySQL database.
 * 2. Retrieves all categories along with their product counts.
 * 3. Handles form submissions to add new categories.
 * 4. Handles delete requests for categories.
 * 5. Manages error and success messages using session variables.
 */
if (!isset($_SESSION)) session_start();

// Include database connection details
require_once(__DIR__ . '/../config.php');

// Connect to MySQL server
$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE);

// Check connection
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Get all the categories
$categories = [];
$sql = "SELECT COUNT(tbl_product.cat_id) AS product_count, tbl_category.*
        FROM tbl_category
        LEFT JOIN tbl_product ON tbl_product.cat_id = tbl_category.cat_id
        GROUP BY tbl_category.cat_id";
$result = $mysqli->query($sql);

if ($result) {
    while ($row = $result->fetch_object()) {
        $categories[] = $row;
    }
    $result->free();
} else {
    die("Query failed: " . $mysqli->error);
}

// Handle new category request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $catname = $_POST['catname'] ?? '';
    $catdesc = htmlspecialchars($_POST['catdesc'] ?? '');

    $errmsg_arr = [];
    $errflag = false;

    if (empty($catname)) {
        $errmsg_arr[] = 'Category name missing';
        $errflag = true;
    }

    if ($errflag) {
        $_SESSION['ERRMSG_ARR'] = $errmsg_arr;
        header("Location: index.php");
        exit();
    }

    // Create INSERT query
    $stmt = $mysqli->prepare("INSERT INTO tbl_category (cat_name, cat_description) VALUES (?, ?)");
    $stmt->bind_param('ss', $catname, $catdesc);
    
    if ($stmt->execute()) {
        $_SESSION['MSGS'] = ['<strong>Wola!</strong> Changes were successful.'];
        $stmt->close();
        header("Location: index.php");
        exit();
    } else {
        die("Query failed: " . $stmt->error);
    }
}

// Handle delete request
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['delete'])) {
    $catid = intval($_GET['delete']);

    // Create DELETE query
    $stmt = $mysqli->prepare("DELETE FROM tbl_category WHERE cat_id = ?");
    $stmt->bind_param('i', $catid);

    if ($stmt->execute()) {
        $_SESSION['MSGS'] = ['<strong>Wola!</strong> Changes were successful.'];
        $stmt->close();
        header("Location: index.php");
        exit();
    } else {
        $_SESSION['ERRMSG_ARR'] = ['<strong>Oh no!</strong> Changes didn\'t happen, make sure your database is up.'];
        $stmt->close();
        header("Location: index.php");
        exit();
    }
}

// Close MySQL connection
$mysqli->close();
?>