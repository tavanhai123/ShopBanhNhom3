<?php
session_start();

$servername = "localhost"; 
$db_username = "root"; 
$db_password = ""; 
$dbname = "shopbanh"; 

$conn = new mysqli($servername, $db_username, $db_password, $dbname);
$conn->set_charset("utf8"); 

if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

// Kiểm tra nếu người dùng đã đăng nhập và có quyền admin (UserRole = 1)
if (!isset($_SESSION['username']) || $_SESSION['userrole'] != '1') {
    header("Location: dnhap.php"); // Chuyển hướng đến trang đăng nhập nếu không phải admin
    exit();
}

// Lấy thông tin admin từ cơ sở dữ liệu
$username = $_SESSION['username']; // Lấy tên đăng nhập từ session

// Kiểm tra và chuẩn bị câu lệnh SQL
$stmt = $conn->prepare("SELECT FullName FROM user WHERE Username = ?");


if ($stmt === false) {
    die('Lỗi câu lệnh SQL: ' . $conn->error); // Nếu prepare thất bại, in ra lỗi
}
// Count total users
$total_user_result = $conn->query("SELECT COUNT(*) AS total FROM user WHERE UserRole = 2");
$total_user = ($total_user_result->num_rows > 0) ? $total_user_result->fetch_assoc()['total'] : 0;

// Count total products
$total_products_result = $conn->query("SELECT COUNT(*) AS total FROM product");
$total_products = ($total_products_result->num_rows > 0) ? $total_products_result->fetch_assoc()['total'] : 0;

$total_revenue_result = $conn->query("SELECT SUM(Total) AS total_revenue FROM `order`");
$total_revenue = ($total_revenue_result->num_rows > 0) ? $total_revenue_result->fetch_assoc()['total_revenue'] : 0;



// Liên kết tham số và thực thi câu lệnh
$stmt->bind_param("s", $username); // Truy vấn theo tên đăng nhập
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    $fullname = $user['FullName']; // Lấy tên đầy đủ admin
} else {
    $fullname = "Admin"; // Nếu không tìm thấy, có thể trả về "Admin" mặc định
}

$stmt->close();
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
                    <li class="sidebar-list-item tab-content active">
                        <a href="admin.php" class="sidebar-link">
                            <div class="sidebar-icon"><i class="fa-light fa-house"></i></div>
                            <div class="hidden-sidebar">Trang tổng quan</div>
                        </a>
                    </li>
                    <li class="sidebar-list-item tab-content">
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
                            <div class="hidden-sidebar" id="name-acc"><?php echo $fullname; ?></div> <!-- Hiển thị tên đầy đủ của admin -->
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
            <div class="section active">
                <h1 class="page-title">Trang tổng quát của cửa hàng Sweet Cake</h1>
                <div class="cards">
                    <div class="card-single">
                        <div class="box">
                            <h2 id="amount-user"><?php echo $total_user; ?></h2>
                            <div class="on-box">
                                <img src="assets/img/admin/s1.png" alt="" style=" width: 200px;">
                                <h3>Khách hàng</h3>
                                <p>Khách hàng mục tiêu là một nhóm đối tượng khách hàng trong phân khúc thị trường mục
                                    tiêu mà doanh nghiệp bạn đang hướng tới. </p>
                            </div>

                        </div>
                    </div>
                    <div class="card-single">
                        <div class="box">
                            <div class="on-box">
                                <img src="assets/img/admin/s2.png" alt="" style=" width: 200px;">
                                <h2 id="amount-product"><?php echo $total_products?></h2>
                                <h3>Sản phẩm</h3>
                                    <p>Sản phẩm là bất cứ cái gì có thể đưa vào thị trường để tạo sự chú ý, mua sắm, sử dụng
                                    hay tiêu dùng nhằm thỏa mãn một nhu cầu hay ước muốn. Nó có thể là những vật thể,
                                    dịch vụ, con người, địa điểm, tổ chức hoặc một ý tưởng.</p>
                            </div>
                        </div>
                    </div>
                    <div class="card-single">
                        <div class="box">
                            <h2 id="doanh-thu"><?= number_format($total_revenue, 0, ",", ".") ?> VND</h2>
                            <div class="on-box">
                                <img src="assets/img/admin/s3.png" alt="" style=" width: 200px;">
                                <h3>Doanh thu</h3>
                                <p>Doanh thu của doanh nghiệp là toàn bộ số tiền sẽ thu được do tiêu thụ sản phẩm, cung
                                    cấp dịch vụ với sản lượng.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
