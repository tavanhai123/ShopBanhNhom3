<iframe src="adminkhachhang.php" style="width: 100%; height: 100%; border: none;"></iframe>
<?php
session_start();

// Kết nối đến cơ sở dữ liệu
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "shopbanh";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

$conn->set_charset("utf8");

// Kiểm tra nếu chưa đăng nhập hoặc không phải admin
if (!isset($_SESSION['username']) || $_SESSION['userrole'] != 1) {
    header("Location: dnhap.php"); // Điều hướng về trang đăng nhập nếu chưa đăng nhập hoặc không phải admin
    exit();
}

// Lấy thông tin người dùng từ URL
if (isset($_GET['id'])) {
    $userid = intval($_GET['id']);

    // Truy vấn để lấy thông tin người dùng
    $stmt = $conn->prepare("SELECT fullname, phone, email FROM user WHERE userid = ?");
    $stmt->bind_param("i", $userid);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $fullname = $user['fullname'];
        $phone = $user['phone'];
        $email = $user['email'];
    } else {
        echo "Không tìm thấy người dùng!";
        exit();
    }

    $stmt->close();
} else {
    echo "Không có ID người dùng!";
    exit();
}

// Cập nhật thông tin
if (isset($_POST['update_user'])) {
    $userid = $_POST['userid'];
    $fullname = $_POST['fullname'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];

    // Truy vấn cập nhật thông tin người dùng
    $update_sql = "UPDATE user SET fullname = ?, phone = ?, email = ? WHERE userid = ?";
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param("sssi", $fullname, $phone, $email, $userid);

    if ($stmt->execute()) {
        // Lưu thông báo vào session để hiển thị pop-up
        $_SESSION['message'] = 'Cập nhật thông tin thành công!';
        // Giữ lại thông tin người dùng và hiển thị popup
        header("Location: {$_SERVER['PHP_SELF']}?id=" . $userid);  // Reload lại trang mà không làm mất dữ liệu
        exit();
    } else {
        echo "<div class='error-message'>Lỗi cập nhật thông tin: " . $conn->error . "</div>";
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
    <link href='./assets/img/logo.png' rel='icon' type='image/x-icon' />
    <link rel="stylesheet" href="assets/css/admin.css">
    <link rel="stylesheet" href="./assets/css/toast-message.css">
    <link href="./assets/font/font-awesome-pro-v6-6.2.0/css/all.min.css" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="./assets/css/admin-responsive.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> <!-- Thêm thư viện SweetAlert2 -->
    <title>Quản lý cửa hàng</title>
</head>
<body>
<div class="modal signup open">
    <div class="modal-container">
        <h3 class="modal-container-title edit-account-e" style="display: block;">CHỈNH SỬA THÔNG TIN</h3>
        <form action="adminkhachhang.php" method="POST">
            <button class="modal-close" fdprocessedid="zbp7q3"><i class="fa-regular fa-xmark"></i></button>
        </form>
        <div class="form-content sign-up">
            <form action="" method="POST" class="signup-form">
                <input type="hidden" name="userid" value="<?php echo $userid; ?>">
                <div class="form-group">
                    <label for="fullname" class="form-label">Tên đầy đủ</label>
                    <input type="text" id="fullname" name="fullname" class="form-control" fdprocessedid="8s7aee" value="<?php echo htmlspecialchars($fullname); ?>" required>
                    <span class="form-message-name form-message"></span>
                </div>
                <div class="form-group">
                    <label for="phone" class="form-label">Số điện thoại</label>
                    <input type="text" id="phone" name="phone" class="form-control" fdprocessedid="hawcfa" value="0<?php echo htmlspecialchars($phone); ?>" required>
                    <span class="form-message-phone form-message"></span>
                </div>
                <div class="form-group">
                    <label for="password" class="form-label">Email</label>
                    <input type="email" id="email" name="email" class="form-control" fdprocessedid="w1aezn" value="<?php echo htmlspecialchars($email); ?>" required>
                    <span class="form-message-password form-message"></span>
                </div>   
                <button type="submit" name="update_user" class="form-submit edit-account-e" id="btn-update-button" fdprocessedid="x31ogg" style="display: block;"><i class="fa-regular fa-floppy-disk"></i> Lưu thông tin</button>
            </form>
        </div>
    </div>
</div>

<!-- Hiển thị thông báo pop-up nếu có thông báo từ session -->
<?php if (isset($_SESSION['message'])): ?>
    <script>
        Swal.fire({
            icon: 'success',
            title: '<?php echo $_SESSION['message']; ?>',
            showConfirmButton: false,
            timer: 1500
        });
    </script>
    <?php unset($_SESSION['message']); ?> <!-- Xóa thông báo sau khi đã hiển thị -->
<?php endif; ?>

</body>
</html>
