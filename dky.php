<?php 
// Kết nối đến cơ sở dữ liệu
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "shopbanh";

// Tạo kết nối
$conn = new mysqli($servername, $username, $password, $dbname);

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

// Đặt mã hóa ký tự
$conn->set_charset("utf8");

// Xử lý khi form được submit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullname = trim($_POST['fullname']);
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $password = $_POST['password'];
    $password_confirmation = $_POST['password_confirmation'];
    $created_time = date("Y-m-d H:i:s");

    // Kiểm tra mật khẩu xác nhận
    if ($password !== $password_confirmation) {
        echo "<p style='color:red;'>Mật khẩu không khớp.</p>";
    } else {
        // Kiểm tra tên đăng nhập có tồn tại không
        $check_username = $conn->prepare("SELECT * FROM user WHERE Username = ?");
        $check_username->bind_param("s", $username);
        $check_username->execute();
        $result = $check_username->get_result();

        if ($result->num_rows > 0) {
            // Hiển thị thông báo lỗi trong form
            $error_message = "Tên đăng nhập đã được dùng.";
        } else {
            // Mã hóa mật khẩu trước khi lưu vào CSDL
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Chuẩn bị câu lệnh SQL để chèn dữ liệu bao gồm thời gian tạo và gán userrole = 2
            $sql = "INSERT INTO user (Fullname, Username, Email, Phone, Password, userrole, created_time) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssssss", $fullname, $username, $email, $phone, $hashed_password, $userrole, $created_time);


            // Gán giá trị cho userrole
            $userrole = 2;  // Gán vai trò người dùng là 2 (người dùng)

            // Thực hiện bind các giá trị cần thiết cho câu lệnh SQL
            $stmt->bind_param("sssssss", htmlspecialchars($fullname), htmlspecialchars($username), htmlspecialchars($email), htmlspecialchars($phone), $hashed_password, $userrole, $created_time);

            // Thực thi câu lệnh SQL
            if ($stmt->execute()) {
                // Chuyển hướng về trang đăng nhập mà không in ra văn bản trước đó
                header("Location: dnhap.php");
                exit();
            } else {
                echo "<p style='color:red;'>Lỗi: " . $stmt->error . "</p>";
            }

            $stmt->close();
        }

        $check_username->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sweet Cake</title>
    <link href='./assets/img/logo.png' rel='icon' type='image/x-icon' />
    <link rel="stylesheet" href="./assets/css/main.css">
</head>
<body>
<div class="form-content sign-up">
    <h3 class="form-title">Đăng ký tài khoản</h3>
    <p class="form-description">Đăng ký thành viên để mua hàng và nhận những ưu đãi đặc biệt từ chúng tôi.</p>
    <form action="" method="POST" class="signup-form">
        <div class="form-group">
            <label for="fullname" class="form-label">Tên đầy đủ</label>
            <input id="fullname" name="fullname" type="text" placeholder="VD: Văn Hải" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="username" class="form-label">Tên đăng nhập</label>
            <input id="username" name="username" type="text" placeholder="Nhập tên đăng nhập" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="email" class="form-label">Email</label>
            <input id="email" name="email" type="email" placeholder="Nhập email" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="phone" class="form-label">Số điện thoại</label>
            <input id="phone" name="phone" type="text" placeholder="Nhập số điện thoại" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="password" class="form-label">Mật khẩu</label>
            <input id="password" name="password" type="password" placeholder="Nhập mật khẩu" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="password_confirmation" class="form-label">Nhập lại mật khẩu</label>
            <input id="password_confirmation" name="password_confirmation" placeholder="Nhập lại mật khẩu" type="password" class="form-control" required>
        </div>
        <div class="form-group">
            <input class="checkbox" name="checkbox" required="" type="checkbox" id="checkbox-signup">
            <label for="checkbox-signup" class="form-checkbox">Tôi đồng ý với <a href="#" title="chính sách trang web" target="_blank">chính sách trang web</a></label>
        </div>

        <?php if (isset($error_message)) { echo "<p style='color:red;'>$error_message</p>"; } ?>

        <button class="form-submit" id="signup-button" type="submit">Đăng ký</button>
    </form>
    <p class="change-login">Bạn đã có tài khoản? <a href="dnhap.php" class="login-link">Đăng nhập ngay</a></p>
</div>
</body>
</html>
