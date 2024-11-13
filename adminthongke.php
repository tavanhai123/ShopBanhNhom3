<?php
session_start();
$servername = "localhost";
$username = "root";
$password = ""; 
$dbname = "shopbanh"; 

$conn = new mysqli($servername, $username, $password, $dbname);

// Kiểm tra kết nối
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
$search_keyword = isset($_GET['search']) ? $_GET['search'] : '';

// Tìm kiếm sản phẩm theo tên
$search_keyword = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';

// Truy vấn sản phẩm dựa trên từ khóa tìm kiếm
$sql = "SELECT p.ProductImage, p.ProductName, p.Price, p.ProductId, c.CateName 
        FROM product p 
        JOIN category c ON p.CateId = c.CateId";
if (!empty($search_keyword)) {
    $sql .= " WHERE p.ProductName LIKE '%$search_keyword%'";
}

// Thực hiện truy vấn
$result = $conn->query($sql);
if ($result === false) {
    die("Lỗi truy vấn: " . $conn->error);
}


// Truy vấn để lấy các thống kê
$total_products_sold = 0;
$total_orders = 0;
$total_revenue = 0;

// Lấy tổng số lượng sản phẩm đã bán
$result = $conn->query("SELECT SUM(Quantity) AS total_products_sold FROM orderdetails");
if ($result && $row = $result->fetch_assoc()) {
    $total_products_sold = $row['total_products_sold'];
}

// Lấy tổng số đơn hàng
$result = $conn->query("SELECT COUNT(OrderID) AS total_orders FROM `order`");
if ($result && $row = $result->fetch_assoc()) {
    $total_orders = $row['total_orders'];
}

// Lấy tổng doanh thu
$result = $conn->query("SELECT SUM(Total) AS total_revenue FROM `order`");
if ($result && $row = $result->fetch_assoc()) {
    $total_revenue = $row['total_revenue'];
}
// Nếu chưa lấy fullname từ session, truy vấn để lấy nó từ CSDL
if (empty($fullname)) {
    // Truy vấn dữ liệu của admin từ CSDL
    $stmt = $conn->prepare("SELECT FullName FROM user WHERE Username = ? AND userrole = 1");

    if ($stmt === false) {
        die('Lỗi câu lệnh SQL: ' . $conn->error); 
    }

    // Liên kết tham số và thực thi câu lệnh
    $stmt->bind_param("s", $username); 
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $fullname = $user['FullName'];  
        $_SESSION['fullname'] = $fullname;  // Lưu fullname vào session để sử dụng sau
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
        <div class="div-left">
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
                        <li class="sidebar-list-item tab-content active">
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
        </div>
        <main class="content">
            <div class="section active">
            <div class="admin-control">
                <div class="admin-control-center">
                    <form action="" method="GET" class="form-search">
                        <span class="search-btn" onclick="document.getElementById('form-search-product').form.submit()"><i class="fa-light fa-magnifying-glass"></i></span>
                        <input id="form-search-product" type="text" name="search" class="form-search-input" placeholder="Tìm kiếm tên món..." value="<?php echo htmlspecialchars($search_keyword); ?>">
                    </form>
                </div>
                <!-- <div class="admin-control-right">
                    <form action="" class="fillter-date">
                        <div>
                            <label for="time-start">Từ</label>
                            <input type="date" class="form-control-date" id="time-start-tk">
                        </div>
                        <div>
                            <label for="time-end">Đến</label>
                            <input type="date" class="form-control-date" id="time-end-tk">
                        </div>
                    </form> 
                    <button class="btn-reset-order"><i class="fa-regular fa-arrow-up-short-wide"></i></button>
                    <button class="btn-reset-order"><i class="fa-regular fa-arrow-down-wide-short"></i></button>
                    <button class="btn-reset-order"><i class="fa-light fa-arrow-rotate-right"></i></button>                    
                </div> -->
            </div>
            <div class="order-statistical" id="order-statistical">
                <div class="order-statistical-item">
                    <div class="order-statistical-item-content">
                        <p class="order-statistical-item-content-desc">Số đơn hàng</p>
                        <h4 class="order-statistical-item-content-h" id="quantity-product"><?php echo $total_orders; ?></h4>
                    </div>
                    <div class="order-statistical-item-icon">
                        <i class="fa-light fa-salad"></i>
                    </div>
                </div>
                <div class="order-statistical-item">
                    <div class="order-statistical-item-content">
                        <p class="order-statistical-item-content-desc">Số lượng bán ra</p>
                        <h4 class="order-statistical-item-content-h" id="quantity-order"><?php echo $total_products_sold; ?></h4>
                    </div>
                    <div class="order-statistical-item-icon">
                        <i class="fa-light fa-file-lines"></i>
                    </div>
                </div>
                <div class="order-statistical-item">
                    <div class="order-statistical-item-content">
                        <p class="order-statistical-item-content-desc">Doanh thu</p>
                        <h4 class="order-statistical-item-content-h" id="quantity-sale"><?php echo number_format($total_revenue, 0, ',', '.') . ' VND'; ?></h4>
                    </div>
                    <div class="order-statistical-item-icon">
                        <i class="fa-light fa-dollar-sign"></i>
                    </div>
                </div>
            </div>
            <div class="table">
                <table width="100%">
                    <thead>
                        <tr>
                            <td>STT</td>
                            <td>Tên món</td>
                            <td>Số lượng bán</td>
                            <td>Doanh thu</td>
                            <td></td>
                        </tr>
                    </thead>
                    <tbody id="showTk">
                    
                    <?php
                         // Lấy danh sách sản phẩm bán ra cùng số lượng và doanh thu, có điều kiện tìm kiếm nếu có từ khóa
                         $query = "SELECT p.ProductID, p.ProductName, p.ProductImage, SUM(oi.Quantity) AS TotalSold, SUM(oi.Quantity * oi.PriceP) AS Revenue 
                         FROM orderdetails oi 
                         JOIN product p ON oi.ProductID = p.ProductID ";
                        if (!empty($search_keyword)) {
                            $query .= "WHERE p.ProductName LIKE '%$search_keyword%' ";
                        }
                        $query .= "GROUP BY oi.ProductID";

                        $stmt = $conn->query($query);
                        $index = 1;
                        while ($row = $stmt->fetch_assoc()) {
                            echo "<tr>
                                    <td>{$index}</td>
                                    <td>
                                        <div class='prod-img-title'>
                                            <img class='prd-img-tbl' src='" . htmlspecialchars($row['ProductImage']) . "' alt='" . htmlspecialchars($row['ProductName']) . "'>
                                            <p>" . htmlspecialchars($row['ProductName']) . "</p>
                                        </div>
                                    </td>
                                    <td>{$row['TotalSold']}</td>
                                    <td>" . number_format($row['Revenue'], 0, ',', '.') . " VND</td>
                                    <td>
                                    <button class='btn-detail product-order-detail' data-id='{$row['ProductID']}' onclick='window.location.href=\"chitietthongke.php?product_id={$row['ProductID']}\"'><i class='fa-regular fa-eye'></i> Chi tiết</button>
                                    </td>
                                    </tr>";
                            $index++;
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
    
