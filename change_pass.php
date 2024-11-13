<?php
$servername = "localhost"; // Địa chỉ máy chủ
$db_username = "root"; // Tên người dùng cơ sở dữ liệu
$db_password = ""; // Mật khẩu của cơ sở dữ liệu
$dbname = "shopbanh"; // Tên cơ sở dữ liệu

// Tạo kết nối
$conn = new mysqli($servername, $db_username, $db_password, $dbname);
$conn->set_charset("utf8");

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

session_start();

// Kiểm tra nếu người dùng đã đăng nhập
if (!isset($_SESSION['username'])) {
    header('Location: index.php'); // Chuyển hướng nếu chưa đăng nhập
    exit;
}

// Lấy tên người dùng từ session
$username = $_SESSION['username'];

// Xử lý form khi được gửi
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Kiểm tra xem mật khẩu mới và xác nhận mật khẩu mới có khớp hay không
    if ($new_password !== $confirm_password) {
        $_SESSION['message'] = "Mật khẩu mới và xác nhận mật khẩu không khớp.";
        header('Location: account.php');
        exit;
    }

    // Lấy mật khẩu hiện tại từ cơ sở dữ liệu
    $sql = "SELECT password FROM user WHERE username='$username'";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $stored_password = $row['password'];

        // Kiểm tra mật khẩu hiện tại
        if ($current_password !== $stored_password) {
            $_SESSION['message'] = "Mật khẩu hiện tại không đúng.";
            header('Location: account.php');
            exit;
        }

        // Cập nhật mật khẩu mới vào cơ sở dữ liệu
        $sql = "UPDATE user SET password='$new_password' WHERE username='$username'";
        if ($conn->query($sql) === TRUE) {
            $_SESSION['message'] = "Đổi mật khẩu thành công!";
        } else {
            $_SESSION['message'] = "Lỗi: " . $conn->error;
        }
    } else {
        $_SESSION['message'] = "Không tìm thấy người dùng.";
    }

    // Chuyển hướng về form đổi mật khẩu
    header('Location: account.php');
    exit;
}



$conn->close();
?>
