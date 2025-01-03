<?php
$filepath = realpath(dirname(__FILE__));
require_once($filepath . "../../db_connect.php");
$db = new DB_CONNECT();
$db_dir = $db->connect();
session_start();
$email = $_POST['username'];
$password = $_POST['password'];
$check_login = mysqli_query($db_dir, "SELECT *FROM user WHERE `email` = '$email' AND `password` = '$password' ");
if (mysqli_num_rows($check_login) >= 1) {
    $_SESSION['uid'] = 1;
    while ($retrieve_user_info = mysqli_fetch_array($check_login)) {
        $_SESSION['user_id'] = $retrieve_user_info['user_id'];
    }
    header('Location: '.$_POST['callback']);
} else {
    ?>
    <h1>Invalid login</h1>
<?php
}