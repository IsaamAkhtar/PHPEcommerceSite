<?php
if (!isset($_SESSION)) {
    session_start();
}

// Include database connection details
require_once(__DIR__ . '/../config.php');

// Connect to MySQL server
$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE);

// Check connection
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

$products = [];

// Get all the products
$sql = "SELECT tbl_product.*, tbl_category.cat_name
        FROM tbl_product
        INNER JOIN tbl_category ON tbl_product.cat_id = tbl_category.cat_id";

if ($result = $mysqli->query($sql)) {
    while ($row = $result->fetch_object()) {
        $products[] = $row;
    }
    $result->free();
} else {
    die("Query failed: " . $mysqli->error);
}

// Handle new product request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['proname'])) {
    $proname = $mysqli->real_escape_string($_POST['proname']);
    $prodesc = htmlspecialchars($_POST['prodesc']);
    $category = intval($_POST['category']);
    $price = floatval($_POST['price']);
    $quantity = intval($_POST['quantity']);
    $proimage = $_FILES['proimage'];

    $errmsg_arr = [];
    $errflag = false;

    // Validate form inputs
    if (empty($proname)) {
        $errmsg_arr[] = 'Product name missing';
        $errflag = true;
    }
    if (empty($category)) {
        $errmsg_arr[] = 'Category missing';
        $errflag = true;
    }
    if (empty($price)) {
        $errmsg_arr[] = 'Price missing';
        $errflag = true;
    }
    if (empty($quantity)) {
        $errmsg_arr[] = 'Quantity missing';
        $errflag = true;
    }
    if ($proimage['error'] !== UPLOAD_ERR_OK) {
        $errmsg_arr[] = 'Please upload an image';
        $errflag = true;
    }

    // Function to validate image type
    function valid($ptype)
    {
        $valid_types = ['image/jpg', 'image/jpeg', 'image/png', 'image/gif'];
        return in_array($ptype, $valid_types);
    }

    if (!$errflag && !valid($proimage['type'])) {
        $errmsg_arr[] = 'You must upload a JPEG, JPG, or PNG image.';
        $errflag = true;
    }

    if ($errflag) {
        $_SESSION['ERRMSG_ARR'] = $errmsg_arr;
        header("Location: index.php");
        exit();
    }

    // Build the target path
    $targetPath = __DIR__ . '/../img/uploads/' . basename($proimage['name']);

    // Attempt to move the file
    if (move_uploaded_file($proimage['tmp_name'], $targetPath)) {
        // Create INSERT query
        $stmt = $mysqli->prepare("INSERT INTO tbl_product (cat_id, pd_name, pd_description, pd_price, pd_qty, pd_image) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param('issdis', $category, $proname, $prodesc, $price, $quantity, $proimage['name']);
        
        if ($stmt->execute()) {
            $_SESSION['MSGS'] = ['<strong>Success!</strong> Product added successfully.'];
            $stmt->close();
            header("Location: index.php");
            exit();
        } else {
            $stmt->close();
            die("Query failed: " . $mysqli->error);
        }
    } else {
        $_SESSION['ERRMSG_ARR'] = ['Could not upload file. Check read/write permissions on the directory.'];
        header("Location: index.php");
        exit();
    }
}

// Handle delete request
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['delete'])) {
    $pd_id = intval($_GET['delete']);

    // Create DELETE query
    $stmt = $mysqli->prepare("DELETE FROM tbl_product WHERE pd_id = ?");
    $stmt->bind_param('i', $pd_id);

    if ($stmt->execute()) {
        $_SESSION['MSGS'] = ['<strong>Success!</strong> Product deleted successfully.'];
        $stmt->close();
        header("Location: index.php");
        exit();
    } else {
        $stmt->close();
        $_SESSION['ERRMSG_ARR'] = ['<strong>Error!</strong> Could not delete product.'];
        header("Location: index.php");
        exit();
    }
}

// Close MySQL connection
$mysqli->close();
?>