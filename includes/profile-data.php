<?php 
// Check if a session is already started before calling session_start()
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include database connection details
require_once(__DIR__.'/../config.php');
$user_id = $_SESSION['SESS_USER_ID'];

// Connect to MySQL server
$link = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE);
if (!$link) {
    die("Cannot access db: " . mysqli_connect_error());
}

// Fetch user information
$res = mysqli_query($link, "SELECT * FROM tbl_user WHERE user_id=".$user_id." LIMIT 1");
$user = mysqli_fetch_assoc($res);

// Fetch orders associated with the user
$ord_res = mysqli_query($link, "SELECT `tbl_order`.*, GROUP_CONCAT(`pd_name` SEPARATOR ', ') as `products`
                                FROM `tbl_order`, `tbl_order_item`, `tbl_product`
                                WHERE `tbl_order`.`od_id` = `tbl_order_item`.`od_id` 
                                AND `tbl_product`.`pd_id` = `tbl_order_item`.`pd_id`
                                AND user_id=".$user_id." GROUP BY `od_id`");
while ($row = mysqli_fetch_object($ord_res)) {
    $orders[] = $row;
}

if(is_array($_POST) && count($_POST) > 0) {
    $password = $_POST['password'];
    $cpassword = $_POST['cpassword'];

    $errmsg_arr = array();
    $errflag = false;

    if($password == '') {
        $errmsg_arr[] = 'Password missing';
        $errflag = true;
    }
    if($cpassword == '') {
        $errmsg_arr[] = 'Confirm password missing';
        $errflag = true;
    }
    if(strcmp($password, $cpassword) != 0) {
        $errmsg_arr[] = 'Passwords do not match';
        $errflag = true;
    }
    if(strlen($password) < 6) {
        $errmsg_arr[] = 'Password is too short.';
        $errflag = true;
    }

    if($errflag) {
        $_SESSION['ERRMSG_ARR'] = $errmsg_arr;
        session_write_close();
        header("location: ./../profile.php");
        exit();
    }

    // Update the password
    $qry = "UPDATE tbl_user SET password='".md5($password)."', updated_at='".date("Y-m-d H:i:s")."' WHERE user_id=$user_id";
    $result = mysqli_query($link, $qry);

    // Check whether the query was successful or not
    if($result) {
        $_SESSION['MSGS'] = array('<strong></strong> Your password was changed successfully.');
        session_write_close();
        header("location: ./../profile.php");
        exit();
    } else {
        die("Query failed: " . mysqli_error($link));
    }
}

// Close the MySQL connection
mysqli_close($link);
?>
