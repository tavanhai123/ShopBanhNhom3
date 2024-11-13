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

//tìm kiếm 51-70
// Khởi tạo biến tìm kiếm
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Truy vấn dữ liệu sản phẩm với điều kiện tìm kiếm
$sql = "SELECT p.ProductImage, p.ProductName, p.Description, p.Price, p.ProductId, c.CateName 
        FROM product p 
        JOIN category c ON p.CateId = c.CateId";
if (!empty($search)) {
    $search = $conn->real_escape_string($search); // Bảo vệ khỏi SQL injection
    $sql .= " WHERE p.ProductName LIKE '%$search%'"; // Thêm điều kiện tìm kiếm
}

// Thực hiện truy vấn
$result = $conn->query($sql);

// Kiểm tra xem truy vấn có thành công hay không
if ($result === false) {
    die("Lỗi truy vấn: " . $conn->error);
}

/* Khởi tạo một mảng để chứa dữ liệu sản phẩm
$products = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $products[] = $row; // Thêm từng sản phẩm vào mảng
    }
} */

// Xử lý xóa sản phẩm 
if (isset($_GET['delete_product'])) {
    $ProductId = htmlspecialchars($_GET['delete_product']);
    $delete_sql = "DELETE FROM product WHERE ProductID = '$ProductId' ";

    if ($conn->query($delete_sql) === TRUE) {
       
        // Nếu xóa thành công, chuyển hướng về trang danh sách sản phẩm
        header("Location: adminsp.php");
        
    } else {
        echo "Lỗi xóa sản phẩm: " . $conn->error;  // Nếu có lỗi
    }
       
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
            
                    <div class="admin-control-center">
                        <form action="" method="GET" class="form-search">
                            <span class="search-btn" onclick="document.getElementById('form-search-product').form.submit()"><i class="fa-light fa-magnifying-glass"></i></span>
                            <input id="form-search-product" type="text" name="search" class="form-search-input" placeholder="Tìm kiếm tên món..." value="<?php echo htmlspecialchars($search); ?>">
                        </form>
                    </div>
                    <div class="admin-control-right">
                        <a href="adminsp.php">
                        <button class="btn-control-large" id="btn-cancel-product"><i class="fa-light fa-rotate-right"></i> Làm mới</button>
                        </a>
                        <a href="formthemsp.php">
                            <button class="btn-control-large" id="btn-add-product"><i class="fa-light fa-plus"></i> Thêm món mới</button>
                        </a>
                    </div>
                </div>
                <div id="show-product">
                    
                    <?php if ($result->num_rows == 0): ?>
                        <div class="no-result"><div class="no-result-i"><i class="fa-light fa-face-sad-cry"></i></div><div class="no-result-h">Không có sản phẩm để hiển thị</div></div>
                    <?php else: ?>
                        <?php while ($row = $result->fetch_assoc()): ?>

                            <div class="list">
                                <div class="list-left">
                                    <img src="<?php echo htmlspecialchars($row['ProductImage']); ?>" alt="<?php echo htmlspecialchars($row['ProductName']); ?>">
                                    <div class="list-info">
                                        <h4><?php echo htmlspecialchars($row['ProductName']); ?></h4>
                                        <p class="list-note"><?php echo htmlspecialchars($row['Description']); ?></p>
                                        <span class="list-category"><?php echo htmlspecialchars($row['CateName']); ?></span>
                                    </div>
                                </div>
                                <div class="list-right">
                                    <div class="list-price">
                                        <span class="list-current-price"><?php echo htmlspecialchars(number_format($row['Price'], 0, ',', '.')); ?>&nbsp;₫</span>                   
                                    </div>
                                    <div class="list-control">
                                        <div class="list-tool">
                                            <a href="suasp.php?id=<?php echo ($row['ProductId']);?>">
                                                <button class="btn-edit"><i class="fa-light fa-pen-to-square"></i></button>
                                            </a>
                                            <a href='?delete_product=<?php echo htmlspecialchars($row['ProductId']); ?>'  ">
                                                <button class="btn-delete"><i class="fa-regular fa-trash"></i></button>
                                            </a>
                                        </div>                       
                                    </div>
                                </div> 
                            </div>  
                         <?php endwhile; ?>
                    <?php endif; ?>
                    
                </div>
                
            </div>
        </main>
    </div>

 