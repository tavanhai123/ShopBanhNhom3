<?php
$servername = "localhost"; // Địa chỉ máy chủ
$username = "root"; // Tên người dùng cơ sở dữ liệu
$password = ""; // Mật khẩu của cơ sở dữ liệu
$dbname = "shopbanh"; // Tên cơ sở dữ liệu

// Tạo kết nối
$conn = new mysqli($servername, $username, $password, $dbname);
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

// Lấy thông tin người dùng từ cơ sở dữ liệu
$sql = "SELECT fullname, email, phone FROM user WHERE username='$username'";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $fullname = $row['fullname'];
    $email = $row['email'];
    $phone = $row['phone'];
} else {
    $fullname = $email = $phone = ''; // Gán giá trị rỗng nếu không có dữ liệu
}

$message = ""; // Khởi tạo thông báo trống
$error = false; // Biến để kiểm tra loại thông báo

// Xử lý form khi được gửi
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Kiểm tra nếu là form cập nhật thông tin
    if (isset($_POST['update_info'])) {
        // Lấy dữ liệu từ form
        $fullname = isset($_POST['fullname']) ? $_POST['fullname'] : '';
        $email = isset($_POST['email']) ? $_POST['email'] : '';
        $phone = isset($_POST['phone']) ? $_POST['phone'] : '';

        // Kiểm tra xem các trường không để trống
        if (!empty($fullname) && !empty($email) && !empty($phone)) {
            // Câu truy vấn để cập nhật thông tin người dùng
            $sql = "UPDATE user SET fullname='$fullname', email='$email', phone='$phone' WHERE username='$username'";

            if ($conn->query($sql) === TRUE) {
                $_SESSION['message'] = "Cập nhật thông tin thành công!";
            } else {
                $_SESSION['message'] = "Lỗi: " . $conn->error;
                $_SESSION['error'] = true; // Đánh dấu là lỗi
            }
        } else {
            $_SESSION['message'] = "Vui lòng điền đầy đủ thông tin.";
            $_SESSION['error'] = true; // Đánh dấu là lỗi
        }
    }
    // Kiểm tra nếu là form thay đổi mật khẩu
    elseif (isset($_POST['change_pass'])) {
        $current_password = $_POST['current_password'];
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];

        // Kiểm tra mật khẩu cũ và xác nhận mật khẩu mới
        if ($new_password === $confirm_password) {
            // Câu truy vấn để kiểm tra mật khẩu cũ
            $sql = "SELECT password FROM user WHERE username='$username'";
            $result = $conn->query($sql);
            $row = $result->fetch_assoc();

            if ($row && $row['password'] === $current_password) {
                // Cập nhật mật khẩu mới
                $sql = "UPDATE user SET password='$new_password' WHERE username='$username'";
                if ($conn->query($sql) === TRUE) {
                    $_SESSION['message'] = "Đổi mật khẩu thành công!";
                } else {
                    $_SESSION['message'] = "Lỗi: " . $conn->error;
                    $_SESSION['error'] = true; // Đánh dấu là lỗi
                }
            } else {
                $_SESSION['message'] = "Mật khẩu hiện tại không đúng.";
                $_SESSION['error'] = true; // Đánh dấu là lỗi
            }
        } else {
            $_SESSION['message'] = "Mật khẩu mới và xác nhận không khớp.";
            $_SESSION['error'] = true; // Đánh dấu là lỗi
        }
    }
}

// Đóng kết nối
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
    <link rel="stylesheet" href="./assets/css/home-responsive.css">
    <link rel="stylesheet" href="./assets/font/font-awesome-pro-v6-6.2.0/css/all.min.css"/>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
<main class="main-wrapper">
    <div class="container" id="account-user">
        <div class="main-account">
            <div class="main-account-header">
                <h3>Thông tin tài khoản của bạn</h3>
                <p>Quản lý thông tin để bảo mật tài khoản</p>
            </div>
            <div class="main-account-body">
                <div class="main-account-body-col">
                    <form action="account.php" method="POST" class="info-user">
                        <div class="form-group">
                            <label for="fullname" class="form-label">Họ và tên</label>
                            <input class="form-control" type="text" name="fullname" id="fullname" placeholder="Nhập họ và tên của bạn" value="<?php echo htmlspecialchars($fullname); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="username" class="form-label">Tên đăng nhập</label>
                            <input class="form-control" type="text" name="username" id="username" value="<?php echo htmlspecialchars($username); ?>" disabled="true" placeholder="">
                        </div>
                        <div class="form-group">
                            <label for="phone" class="form-label">Số điện thoại</label>
                            <input class="form-control" type="text" name="phone" id="phone" placeholder="Nhập số điện thoại" value="<?php echo htmlspecialchars($phone); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="email" class="form-label">Email</label>
                            <input class="form-control" type="email" name="email" id="email" placeholder="Nhập email của bạn" value="<?php echo htmlspecialchars($email); ?>" required>
                        </div>
                        <button type="submit" name="update_info" id="save-info-user"><i class="fa-regular fa-floppy-disk"></i> Lưu thay đổi</button>
                    </form>
                </div>

                <!-- Form thay đổi mật khẩu -->
                <div class="main-account-body-col">
                    <form action="account.php" method="POST" class="change-password">
                        <div class="form-group">
                            <label for="current_password" class="form-label">Mật khẩu hiện tại</label>
                            <input class="form-control" type="password" name="current_password" id="current_password" placeholder="Nhập mật khẩu hiện tại" required>
                        </div>
                        <div class="form-group">
                            <label for="new_password" class="form-label">Mật khẩu mới</label>
                            <input class="form-control" type="password" name="new_password" id="new_password" placeholder="Nhập mật khẩu mới" required>
                        </div>
                        <div class="form-group">
                            <label for="confirm_password" class="form-label">Xác nhận mật khẩu mới</label>
                            <input class="form-control" type="password" name="confirm_password" id="confirm_password" placeholder="Nhập lại mật khẩu mới" required>
                        </div>
                        <button type="submit" name="change_pass" id="save-password"><i class="fa-regular fa-key"></i> Đổi mật khẩu</button>
                    </form>
                </div>

            </div>
        </div>
    </div>
</main>

<?php if (isset($_SESSION['message'])): ?>
    <script>
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: '<?php echo isset($_SESSION['error']) && $_SESSION['error'] === true ? 'error' : 'success'; ?>',
            title: '<?php echo isset($_SESSION['error']) && $_SESSION['error'] === true ? 'Error' : 'Success'; ?>',
            text: '<?php echo $_SESSION['message']; ?>',
            showConfirmButton: false,
            timer: 2000,
            timerProgressBar: true,
            customClass: {
                popup: 'swal2-no-animation'
            },
            didOpen: (toast) => {
                const progressBar = toast.querySelector('.swal2-timer-progress-bar');
                
                // Đổi màu thanh tiến trình dựa trên trạng thái
                if (progressBar) {
                    const iconType = '<?php echo isset($_SESSION['error']) && $_SESSION['error'] === true ? 'error' : 'success'; ?>';
                    progressBar.style.backgroundColor = iconType === 'error' ? '#f44336' : '#4caf50'; // Đỏ cho lỗi, xanh cho thành công
                }
            }
        });
    </script>
    <?php unset($_SESSION['message'], $_SESSION['error']); ?> <!-- Xóa thông báo sau khi hiển thị -->
<?php endif; ?>


</body>

</html>

