<?php
session_start();
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
    header("Location: dnhap.php"); 
    exit();
}

// Kiểm tra và lấy thông tin người dùng từ session
$fullname = isset($_SESSION['fullname']) ? $_SESSION['fullname'] : '';
$username = $_SESSION['username'];
$userrole = $_SESSION['userrole'];

// Nếu chưa lấy fullname từ session, truy vấn để lấy nó từ CSDL
if (empty($fullname)) {
    $stmt = $conn->prepare("SELECT FullName FROM user WHERE Username = ? AND userrole = 1");

    if ($stmt === false) {
        die('Lỗi câu lệnh SQL: ' . $conn->error);
    }

    // Liên kết tham số và thực thi câu lệnh
    $stmt->bind_param("s", $username);  // Truy vấn theo tên đăng nhập
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $fullname = $user['FullName']; 
        $_SESSION['fullname'] = $fullname; 
    } else {
        $fullname = "Admin";
    }

    $stmt->close();
}
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
    <title>Quản lý cửa hàng</title>
</head>
<body>
    <header class="header">
        <button class="menu-icon-btn">
            <div class="menu-icon">
                <i class="fa-regular fa-bars"></i>
            </div>
        </button>
    </header>
    <div class="container">
        <aside class="sidebar open">
            <div class="top-sidebar">
                <a href="#" class="channel-logo"><img src="./assets/img/logo.png" alt="Channel Logo"></a>
            </div>
            <div class="middle-sidebar">
                <ul class="sidebar-list">
                    <li class="sidebar-list-item tab-content">
                        <a href="admin.php" class="sidebar-link">
                            <div class="sidebar-icon"><i class="fa-light fa-house"></i></div>
                            <div class="hidden-sidebar">Trang tổng quan</div>
                        </a>
                    </li>
                    <li class="sidebar-list-item tab-content active">
                        <a href="adminsp.php" class="sidebar-link">
                            <div class="sidebar-icon"><i class="fa-light fa-pot-food"></i></div>
                            <div class="hidden-sidebar">Sản phẩm</div>
                        </a>
                    </li>
                    <li class="sidebar-list-item tab-content">
                        <a href="adminkhachhang.php" class="sidebar-link">
                            <div class="sidebar-icon"><i class="fa-light fa-users"></i></div>
                            <div class="hidden-sidebar">Khách hàng</div>
                        </a>
                    </li>
                    <li class="sidebar-list-item tab-content">
                        <a href="admindonhang.php" class="sidebar-link">
                            <div class="sidebar-icon"><i class="fa-light fa-basket-shopping"></i></div>
                            <div class="hidden-sidebar">Đơn hàng</div>
                        </a>
                    </li>
                    <li class="sidebar-list-item tab-content">
                        <a href="adminthongke.php" class="sidebar-link">
                            <div class="sidebar-icon"><i class="fa-light fa-chart-simple"></i></div>
                            <div class="hidden-sidebar">Thống kê</div>
                        </a>
                    </li>
                </ul>
            </div>
            <div class="bottom-sidebar">
                <ul class="sidebar-list">
                    <li class="sidebar-list-item user-logout">
                        <a href="index.php" class="sidebar-link">
                            <div class="sidebar-icon"><i class="fa-thin fa-circle-chevron-left"></i></div>
                            <div class="hidden-sidebar">Trang chủ</div>
                        </a>
                    </li>
                    <li class="sidebar-list-item user-logout">
                        <a href="account.php" class="sidebar-link">
                            <div class="sidebar-icon"><i class="fa-light fa-circle-user"></i></div>
                            <div class="hidden-sidebar" id="name-acc"><?php echo htmlspecialchars($fullname); ?></div>
                        </a>
                    </li>
                    <li class="sidebar-list-item user-logout">
                        <a href="dxuat.php" class="sidebar-link" id="logout-acc">
                            <div class="sidebar-icon"><i class="fa-light fa-arrow-right-from-bracket"></i></div>
                            <div class="hidden-sidebar">Đăng xuất</div>
                        </a>
                    </li>
                </ul>
            </div>
        </aside>
        <main class="content">
            <!-- Product Section -->
            <div class="section product-all active">
                <div class="admin-control">
                    <div class="admin-control-left">
                        <select name="the-loai" id="the-loai">
                            <option>Tất cả</option>
                            <option>Cupcake</option>
                            <option>Bentocake</option>
                            <option>Chessecake</option>
                            <option>Layercake</option>
                            <option>Mochicake</option>
                            <option>Tiramisu</option>
                            <option>Đã xóa</option>
                        </select>
                    </div>
                    <div class="admin-control-center">
                        <form action="" class="form-search">
                            <span class="search-btn"><i class="fa-light fa-magnifying-glass"></i></span>
                            <input id="form-search-product" type="text" class="form-search-input" placeholder="Tìm kiếm tên món...">
                        </form>
                    </div>
                    <div class="admin-control-right">
                        <button class="btn-control-large" id="btn-cancel-product"><i class="fa-light fa-rotate-right"></i> Làm mới</button>
                        <button class="btn-control-large" id="btn-add-product"><i class="fa-light fa-plus"></i> Thêm món mới</button>
                    </div>
                </div>
                <div id="show-product"></div>
                <div class="page-nav">
                    <ul class="page-nav-list">
                        <li class="page-nav-item active"><a href="#">1</a></li>
                        <li class="page-nav-item"><a href="#">2</a></li>
                        <li class="page-nav-item"><a href="#">3</a></li>
                        <li class="page-nav-item"><a href="#">4</a></li>
                        <li class="page-nav-item"><a href="#">5</a></li>
                    </ul>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
 