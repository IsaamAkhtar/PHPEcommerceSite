<?php
// Start session
session_start();

// Include database connection details
require_once(__DIR__ . '/../config.php');

// Array to store validation errors
$errmsg_arr = array();

// Validation error flag
$errflag = false;

// Connect to MySQL server
$link = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE);
if (!$link) {
    die('Failed to connect to server: ' . mysqli_connect_error());
}

// Function to sanitize values received from the form. Prevents SQL injection
function clean($str, $link) {
    $str = trim($str);
    return mysqli_real_escape_string($link, $str);
}

// Sanitize the POST values
$username = clean($_POST['username'], $link);
$password = clean($_POST['password'], $link);

// Input Validations
if ($username == '') {
    $errmsg_arr[] = 'Please provide a username.';
    $errflag = true;
}
if ($password == '') {
    $errmsg_arr[] = 'Please enter the password.';
    $errflag = true;
}

// If there are input validations, redirect back to the login form
if ($errflag) {
    $_SESSION['ERRMSG_ARR'] = $errmsg_arr;
    session_write_close();
    header("location: ../customerLogin.php");
    exit();
}

// Create query
$qry = "SELECT * FROM tbl_user WHERE user_name='$username' AND password='" . md5($_POST['password']) . "'";
$result = mysqli_query($link, $qry);

// Check whether the query was successful or not
if ($result) {
    if (mysqli_num_rows($result) == 1) {
        // Login Successful
        session_regenerate_id();
        $member = mysqli_fetch_assoc($result);
        $_SESSION['SESS_USER_ID'] = $member['user_id'];
        $_SESSION['SESS_USERNAME'] = $member['user_name'];
        $_SESSION['SESS_IS_ADMIN'] = $member['user_is_admin'];
        session_write_close();
        header("location: ../index.php");
        exit();
    } else {
        // Login failed
        $_SESSION['ERRMSG_ARR'] = array('<b>Oh no!</b> Incorrect username or password. Please try again.');
        session_write_close();
        header("location: ../customerLogin.php");
        exit();
    }
} else {
    die("Query failed: " . mysqli_error($link));
}
?>