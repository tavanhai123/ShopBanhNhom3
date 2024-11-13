<?php
// Bắt đầu session
session_start();

// Kết nối đến cơ sở dữ liệu
$servername = "localhost";
$db_username = "root";
$db_password = "";
$dbname = "shopbanh";

// Tạo kết nối
$conn = new mysqli($servername, $db_username, $db_password, $dbname);
$conn->set_charset("utf8");

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

// Xử lý khi form được submit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Lấy thông tin từ database
    $sql = "SELECT * FROM user WHERE Username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    // Kiểm tra kết quả
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        
     // Kiểm tra mật khẩu
     if (password_verify($password, $user['Password'])) { 
        // Kiểm tra mật khẩu đã mã hóa
        $_SESSION['userid'] = $user['UserID'];
        $_SESSION['username'] = $username;
        $_SESSION['fullname'] = $user['Fullname'];
        $_SESSION['userrole'] = (string)$user['UserRole']; // Lưu UserRole vào session như chuỗi
    
        header("Location: index.php");
        exit();

        } else {
            $error_message = "Mật khẩu không đúng.";
        }
    } else {
        $error_message = "Đăng nhập thất bại. Vui lòng kiểm tra lại tên đăng nhập.";
    }    

    $stmt->close();
}

$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sweet Cake</title>
    <link href='./assets/img/logo.png' rel='icon' type='image/x-icon' />
    <link rel="stylesheet" href="./assets/css/main.css">
</head>
<body>
<div class="form-content login">
    <h3 class="form-title">Đăng nhập tài khoản</h3>
    <form action="" method="POST" class="login-form">
        <div class="form-group">
            <label for="username" class="form-label">Tên đăng nhập</label>
            <input id="username" name="username" type="text" placeholder="Tên đăng nhập" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="password" class="form-label">Mật khẩu</label>
            <input id="password" name="password" type="password" placeholder="Mật khẩu" class="form-control" required>
        </div>

        <?php if (isset($error_message)) { echo "<p style='color:red;'>$error_message</p>"; } ?>

        <button class="form-submit" type="submit">Đăng nhập</button>
        <p class="change-login">Chưa có tài khoản? <a href="dky.php" class="login-link">Đăng ký ngay</a></p>
    </form>
</div>
</body>
</html>

