<?php
session_start(); // Khởi tạo session

// Kết nối đến CSDL
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "shopbanh";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

$conn->set_charset("utf8");

// Kiểm tra đăng nhập và quyền admin
if (empty($_SESSION['username']) || $_SESSION['userrole'] != 1) {
    header("Location: dnhap.php");
    exit();
}

// Lấy fullname từ session hoặc CSDL
$fullname = $_SESSION['fullname'] ?? '';  
if (!$fullname) {
    $stmt = $conn->prepare("SELECT FullName FROM user WHERE Username = ? AND userrole = 1");
    $stmt->bind_param("s", $_SESSION['username']);
    $stmt->execute();
    $stmt->bind_result($fullname);
    $stmt->fetch();
    $_SESSION['fullname'] = $fullname ?: "Admin";
    $stmt->close();
}

// Tìm kiếm và lọc theo thời gian
$search = $_GET['search'] ?? '';
$start_date = $_GET['time-start'] ?? '';
$end_date = $_GET['time-end'] ?? '';
$sql = "SELECT userid, fullname, phone, email, created_time FROM user WHERE userrole = 2";
$sql .= $search ? " AND fullname LIKE '%$search%'" : '';
$sql .= $start_date && $end_date ? " AND created_time BETWEEN '$start_date' AND '$end_date'" :
       ($start_date ? " AND created_time >= '$start_date'" : 
       ($end_date ? " AND created_time <= '$end_date'" : ''));

// Thực thi câu truy vấn
$result = $conn->query($sql);

// Xử lý xóa người dùng
if ($userid = $_GET['delete_user'] ?? null) {
    $delete_sql = "DELETE FROM user WHERE userid = ?";
    if ($stmt = $conn->prepare($delete_sql)) {
        $stmt->bind_param("i", $userid);
        if ($stmt->execute()) {
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        }
        echo "Lỗi xóa người dùng: " . $conn->error;
    }
}

?>

<!DOCTYPE html>
<html lang="vi">
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
                    <li class="sidebar-list-item tab-content">
                        <a href="adminsp.php" class="sidebar-link">
                            <div class="sidebar-icon"><i class="fa-light fa-pot-food"></i></div>
                            <div class="hidden-sidebar">Sản phẩm</div>
                        </a>
                    </li>
                    <li class="sidebar-list-item tab-content active">
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
        
            <!-- Account  -->
            <div class="section">
                <div class="admin-control">
                    <div class="admin-control-center">
                        <form action="" method="get" class="form-search">
                            <span class="search-btn"><i class="fa-light fa-magnifying-glass"></i></span>
                            <input id="form-search-user" name="search" type="text" class="form-search-input" placeholder="Tìm kiếm khách hàng..." value="<?php echo htmlspecialchars($search); ?>">
                        </form>
                    </div>
                    <div class="admin-control-right">
                        <form action="" method="get" class="fillter-date">
                            <div>
                                <label for="time-start">Từ</label>
                                <input type="date" class="form-control-date" id="time-start-user" name="time-start" value="<?php echo isset($_GET['time-start']) ? $_GET['time-start'] : ''; ?>">
                            </div>
                            <div>
                                <label for="time-end">Đến</label>
                                <input type="date" class="form-control-date" id="time-end-user" name="time-end" value="<?php echo isset($_GET['time-end']) ? $_GET['time-end'] : ''; ?>">
                            </div>
                            <button type="submit">Lọc</button>
                        </form>      
                    </div>
                </div>

                <div class="admin-table">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <td>ID</td>
                                <td>Họ tên</td>
                                <td>SĐT</td>
                                <td>Email</td>
                                <td>Ngày tạo</td>
                                <td>Hành động</td>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    echo "<tr>
                                            <td>" . $row['userid'] . "</td>
                                            <td>" . $row['fullname'] . "</td>
                                            <td>" . $row['phone'] . "</td>
                                            <td>" . $row['email'] . "</td>
                                            <td>" . $row['created_time'] . "</td>
                                            <td>
                                                <a href='admin_editkh.php?id=" . htmlspecialchars($row['userid']) . "' class='btn-edit'><i class='fa-light fa-pen-to-square'></i></a>
                                                <a href='?delete_user=" . $row['userid'] . "' onclick=\"return confirm('Bạn có chắc chắn muốn xóa người dùng này?');\" class='btn-delete'>
                                                    <i class='fa-regular fa-trash's></i></
                                                </a>


                                            </td>
                                          </tr>";
                                }
                            } else {
                                echo "<tr><td colspan='6'>Không có khách hàng nào trong khoảng thời gian này.</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                    
                </div>
            </div>
        </main>
    </div>
</body>
</html>

<?php
$conn->close();
?>