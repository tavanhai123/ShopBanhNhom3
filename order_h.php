<?php
include './connect_db.php';
session_start();
$user_id = $_SESSION['userid']; // Giả sử bạn đã lưu user_id sau khi đăng nhập

$sql_orders = "SELECT o.OrderID, o.Total, o.Status, o.CreatTime, od.ProductID, od.Quantity, od.PriceP, od.NoteCart, 
                      p.ProductName, p.ProductImage 
               FROM `order` o
               JOIN orderdetails od ON o.OrderID = od.OrderID 
               JOIN product p ON od.ProductID = p.ProductID 
               WHERE o.UserID = $user_id
               ORDER BY o.OrderID DESC";
$result_orders = $con->query($sql_orders);

$orders = [];

// Lưu trữ dữ liệu các sản phẩm trong từng đơn hàng vào mảng $orders
if ($result_orders->num_rows > 0) {
    while ($row = $result_orders->fetch_assoc()) {
        $order_id = $row['OrderID'];
        
        if (!isset($orders[$order_id])) {
            // Nếu đơn hàng chưa tồn tại trong mảng, tạo một phần tử mới với các thông tin tổng quát của đơn hàng
            $orders[$order_id] = [
                'Total' => $row['Total'],
                'Status' => $row['Status'],
                'CreatTime' => $row['CreatTime'],
                'products' => [] // Mảng con chứa các sản phẩm của đơn hàng
            ];
        }
        
        // Thêm sản phẩm vào mảng products của đơn hàng hiện tại
        $orders[$order_id]['products'][] = [
            'ProductName' => $row['ProductName'],
            'ProductImage' => $row['ProductImage'],
            'Quantity' => $row['Quantity'],
            'PriceP' => $row['PriceP'],
            'NoteCart' => $row['NoteCart']
        ];
    }
}
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
    <link rel="stylesheet" href="./assets/css/toast-message.css">
    <link rel="stylesheet" href="./assets/font/font-awesome-pro-v6-6.2.0/css/all.min.css"/>
</head>
<body>
    <header>
        <div class="header-middle">
            <div class="container">
                <div class="header-middle-left">
                    <div class="header-logo">
                        <a href="index.php">
                            <img src="./assets/img/logo.png" alt="" class="header-logo-img">
                        </a>
                    </div>
                </div>
                <div class="header-middle-center">
                    <form action="index.php" method="GET" class="form-search">
                        <span class="search-btn"><i class="fa-light fa-magnifying-glass"></i></span>
                        <input type="text" class="form-search-input" placeholder="Tìm kiếm món ăn..." autocomplete="on" required value="<?=isset($_GET['name']) ? $_GET['name'] : ""?>" name="name" />
                        <button type="button" class="filter-btn" onclick="document.querySelector('.advanced-search').classList.toggle('open');"><i class="fa-light fa-filter-list" ></i><span>Lọc</span></button>
                    </form>
                </div>
                <div class="header-middle-right">
                    <ul class="header-middle-right-list">
                        <li class="header-middle-right-item dropdown open">
                            <i class="fa-light fa-user"></i>
                            <div class="auth-container">
                            <?php if (isset($_SESSION['username'])): ?>
                                <span class="text-dndk">Tài khoản</span>
                                <a href="account.php" class="text-tk" style="color: black;">
                                    <?php echo htmlspecialchars($_SESSION['fullname']); ?>
                                    <i class="fa-sharp fa-solid fa-caret-down"></i>
                                </a>
                            <?php else: ?>
                                <span class="text-dndk">Đăng nhập / Đăng ký</span>
                                <span class="text-tk" style="color: black;" >Tài khoản <i class="fa-sharp fa-solid fa-caret-down"></i></span>
                            <?php endif; ?>
                        </div>
                        <ul class="header-middle-right-menu">
                            <?php if (isset($_SESSION['username'])): ?>
                                <?php if (!empty($_SESSION['userrole']) && $_SESSION['userrole'] == 1): ?>
                                    <!-- Hiển thị 4 mục cho admin -->
                                    <li><a href="admin.php"><i class="fa-light fa-gear"></i> Quản lý cửa hàng</a></li>
                                    <li><a href="account.php"><i class="fa-light fa-user-pen"></i> Tài khoản của tôi</a></li>
                                    <li><a href="order_h.php"><i class="fa-regular fa-bags-shopping"></i> Đơn hàng đã mua</a></li>
                                    <li><a href="dxuat.php"><i class="fa-light fa-right-from-bracket"></i> Thoát tài khoản</a></li>
                                <?php else: ?>
                                    <!-- Hiển thị 3 mục cho user không phải admin -->
                                    <li><a href="account.php"><i class="fa-light fa-user-pen"></i>Tài khoản của tôi</a></li>
                                    <li><a href="orderhistory.php"><i class="fa-regular fa-bags-shopping"></i> Đơn hàng đã mua</a></li>
                                    <li><a href="dxuat.php"><i class="fa-light fa-right-from-bracket"></i> Thoát tài khoản</a></li>
                                <?php endif; ?>
                            <?php else: ?>
                                <li><a href="dnhap.php"><i class="fa-light fa-right-to-bracket"></i> Đăng nhập</a></li>
                                <li><a href="dky.php"><i class="fa-light fa-right-to-bracket"></i> Đăng ký</a></li>
                            <?php endif; ?>
                        </ul>
                    </li>
                
                        <li class="header-middle-right-item open" onclick="window.location.href='cart.php';">
                            <div class="cart-icon-menu">
                                    <i class="fa-light fa-basket-shopping"></i>
                            </div>
                            <span>Giỏ hàng</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </header>
    <div class="modal-cart">
        <div class="cart-container">
            <h2 class="gio-hang-trong" style="display: none;">Giỏ hàng trống</h2>  <ul class="cart-list"></ul>
            <div class="cart-total-container">
                <p class="cart-total-title">Tổng cộng:</p>
                <p class="cart-total-amount">0 ₫</p>  </div>
            <button class="thanh-toan disabled">Thanh toán</button> </div>
    </div>
    <?php
    include './connect_db.php'; // Đảm bảo đã kết nối với cơ sở dữ liệu

    // Truy vấn để lấy tất cả các danh mục từ bảng category
    $categoryQuery = "SELECT CateID, CateName FROM category";
    $categoryResult = mysqli_query($con, $categoryQuery);
    ?>
    <nav class="header-bottom">
        <div class="container">
            <ul class="menu-list">
                <li class="menu-list-item"><a href="index.php" class="menu-link">Trang chủ</a></li>
                <?php
                // Kiểm tra và lặp qua các kết quả để tạo danh sách động
                if ($categoryResult && mysqli_num_rows($categoryResult) > 0) {
                    while ($row = mysqli_fetch_assoc($categoryResult)) {
                        $cateID = htmlspecialchars($row['CateID']);
                        $cateName = htmlspecialchars($row['CateName']);
                        echo "<li class='menu-list-item'><a href='index.php?category=$cateID' class='menu-link'>$cateName</a></li>";
                    }
                }
                ?>
            </ul>
        </div>
    </nav>
    
    <?php
    include './connect_db.php'; // Đảm bảo đã kết nối với cơ sở dữ liệu

    // Truy vấn để lấy tất cả các danh mục từ bảng category
    $categoryQuery = "SELECT CateID, CateName FROM category";
    $categoryResult = mysqli_query($con, $categoryQuery);
    ?>
    <form action="index.php" method="GET" class="advanced-search" onsubmit="return keepAdvancedSearchOpen(event);">
        <input type="hidden" name="name" value="<?= isset($_GET['name']) ? htmlspecialchars($_GET['name']) : '' ?>">
        <div class="container">
            <div class="advanced-search-category">
                <span>Phân loại</span>
                <select name="category" id="advanced-search-category-select">
                    <option value="all">Tất cả</option>
                    <?php
                    // Lặp qua kết quả truy vấn để tạo các tùy chọn
                    if ($categoryResult && mysqli_num_rows($categoryResult) > 0) {
                        while ($row = mysqli_fetch_assoc($categoryResult)) {
                            $selected = isset($_GET['category']) && $_GET['category'] == $row['CateID'] ? "selected" : "";
                            echo "<option value='" . htmlspecialchars($row['CateID']) . "' $selected>" . htmlspecialchars($row['CateName']) . "</option>";
                        }
                    }
                    ?>
                </select>
            </div>
            <div class="advanced-search-price">
                <span>Giá từ</span>
                <input type="number" name="min_price" placeholder="tối thiểu" id="min-price" value="<?= isset($_GET['min_price']) ? $_GET['min_price'] : "" ?>">
                <span>đến</span>
                <input type="number" name="max_price" placeholder="tối đa" id="max-price" value="<?= isset($_GET['max_price']) ? $_GET['max_price'] : "" ?>">
                <button type="submit" id="advanced-search-price-btn"><i class="fa-light fa-magnifying-glass-dollar"></i></button>
            </div>
            <div class="advanced-search-control">
                <button type="submit" name="sort_order" value="asc" id="sort-ascending"><i class="fa-regular fa-arrow-up-short-wide"></i></button>
                <button type="submit" name="sort_order" value="desc" id="sort-descending"><i class="fa-regular fa-arrow-down-wide-short"></i></button>
                <button type="reset" id="reset-search" onclick="window.location.href='index.php';"><i class="fa-light fa-arrow-rotate-right"></i></button>
                <button type="button" onclick="document.querySelector('.advanced-search').classList.remove('open');"><i class="fa-light fa-xmark"></i></button>
            </div>
        </div>
    </form>

    <main class="main-wrapper">
        <div class="container" id="order-history">
            <div class="main-account">
                <div class="main-account-header">
                    <h3>Quản lý đơn hàng của bạn</h3>
                    <p>Xem chi tiết, trạng thái của những đơn hàng đã đặt.</p>
                </div>
                <div class="main-account-body">
                    <div class="order-history-section">
                        <?php if (!empty($orders)) {
                            foreach ($orders as $order_id => $order) { ?>
                                <div class="order-history-group">
                                    <?php foreach ($order['products'] as $product) { ?>
                                        <div class="order-history">
                                            <div class="order-history-left">
                                                <img src="<?php echo $product['ProductImage']; ?>" alt="">
                                                <div class="order-history-info">
                                                    <h4><?php echo $product['ProductName']; ?></h4>
                                                    <p class="order-history-note"><i class="fa-light fa-pen"></i> <?php echo $product['NoteCart']; ?></p>
                                                    <p class="order-history-quantity">x<?php echo $product['Quantity']; ?></p>
                                                </div>
                                            </div>
                                            <div class="order-history-right">
                                                <div class="order-history-price">
                                                    <span class="order-history-current-price"><?php echo number_format($product['PriceP'], 0, ',', '.'); ?>&nbsp;₫</span>
                                                </div>
                                            </div>
                                        </div>
                                    <?php } ?>
                                    <div class="order-history-control">
                                        <div class="order-history-status">
                                            <?php if ($order['Status'] == 1): ?>
                                                <span class="order-history-status-sp no-complete">Đang xử lý</span>
                                            <?php else: ?>
                                                <span class="order-history-status-sp complete">Đã xử lý</span>
                                            <?php endif; ?>
                                            <button id="order-history-detail" onclick="window.location.href='order_d.php?order_id=<?php echo $order_id; ?>'">
                                                <i class="fa-regular fa-eye"></i> Xem chi tiết
                                            </button>
                                        </div>
                                        <div class="order-history-total">
                                            <span class="order-history-total-desc">Tổng tiền: </span>
                                            <span class="order-history-toltal-price"><?php echo number_format($order['Total'], 0, ',', '.'); ?>&nbsp;₫</span>
                                        </div>
                                    </div>
                                </div>
                            <?php }
                        } else {
                            echo "<div class='empty-order-section'><img src='./assets/img/empty-order.jpg' alt='' class='empty-order-img'><p>Chưa có đơn hàng nào</p></div>";
                        } ?>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <footer class="footer">
            <div class="container">
                <div class="footer-top">
                    <div class="footer-top-content">
                        <div class="footer-top-img">
                            <img src="./assets/img/Thiết kế chưa có tên (2).png" alt="">
                        </div>
                        <div class="footer-top-subbox">
                            <div class="footer-top-subs">
                                <h2 class="footer-top-subs-title">Đăng ký nhận tin</h2>
                                <p class="footer-top-subs-text">Nhận thông tin mới nhất từ chúng tôi</p>
                            </div>
                            <form class="form-ground">
                                <input type="email" class="form-ground-input" placeholder="Nhập email của bạn">
                                <button class="form-ground-btn">
                                    <span>ĐĂNG KÝ</span>
                                    <i class="fa-solid fa-arrow-right"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="widget-area">
                <div class="container">
                    <div class="widget-row">
                        <div class="widget-row-col-1">
                            <h3 class="widget-title">Về chúng tôi</h3>
                            <div class="widget-row-col-content">
                                <p>Sweet Cake là thương hiệu được thành lập vào năm 2024 với tiêu chí đặt chất lượng sản phẩm lên hàng đầu.</p>
                            </div>
                            <div class="widget-social">
                                <div class="widget-social-item">
                                    <a href="">
                                        <i class="fab fa-facebook-f"></i>
                                    </a>
                                </div>
                                <div class="widget-social-item">
                                    <a href="">
                                        <i class="fab fa-twitter"></i>
                                    </a>
                                </div>
                                <div class="widget-social-item">
                                    <a href="">
                                        <i class="fab fa-linkedin-in"></i>
                                    </a>
                                </div>
                                <div class="widget-social-item">
                                    <a href="">
                                        <i class="fab fa-whatsapp"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="widget-row-col">
                            <h3 class="widget-title">Liên kết</h3>
                            <ul class="widget-contact">
                                <li class="widget-contact-item">
                                    <a href="">
                                        <i class="fa-regular fa-arrow-right"></i>
                                        <span>Về chúng tôi</span>
                                    </a>
                                </li>
                                <li class="widget-contact-item">
                                    <a href="">
                                        <i class="fa-regular fa-arrow-right"></i>
                                        <span>Thực đơn</span>
                                    </a>
                                </li>
                                <li class="widget-contact-item">
                                    <a href="">
                                        <i class="fa-regular fa-arrow-right"></i>
                                        <span>Điều khoản</span>
                                    </a>
                                </li>
                                <li class="widget-contact-item">
                                    <a href="">
                                        <i class="fa-regular fa-arrow-right"></i>
                                        <span>Liên hệ</span>
                                    </a>
                                </li>
                                <li class="widget-contact-item">
                                    <a href="">
                                        <i class="fa-regular fa-arrow-right"></i>
                                        <span>Tin tức</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                        <div class="widget-row-col">
                            <h3 class="widget-title">Thực đơn</h3>
                            <ul class="widget-contact">
                                <li class="widget-contact-item">
                                    <a href="">
                                        <i class="fa-regular fa-arrow-right"></i>
                                        <span>Cupcake</span>
                                    </a>
                                </li>
                                <li class="widget-contact-item">
                                    <a href="">
                                        <i class="fa-regular fa-arrow-right"></i>
                                        <span>Bentocake</span>
                                    </a>
                                </li>
                                <li class="widget-contact-item">
                                    <a href="">
                                        <i class="fa-regular fa-arrow-right"></i>
                                        <span>Chessecake</span>
                                    </a>
                                </li>
                                <li class="widget-contact-item">
                                    <a href="">
                                        <i class="fa-regular fa-arrow-right"></i>
                                        <span>Layercake</span>
                                    </a>
                                </li>
                                <li class="widget-contact-item">
                                    <a href="">
                                        <i class="fa-regular fa-arrow-right"></i>
                                        <span>Specialcake</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                        <div class="widget-row-col-1">
                            <h3 class="widget-title">Liên hệ</h3>
                            <div class="contact">
                                <div class="contact-item">
                                    <div class="contact-item-icon">
                                        <i class="fa-regular fa-location-dot"></i>
                                    </div>
                                    <div class="contact-content">
                                        <span>79 Hồ Tùng Mậu</span>
                                    </div>
                                </div>
                                <div class="contact-item">
                                    <div class="contact-item-icon">
                                        <i class="fa-regular fa-phone"></i>
                                    </div>
                                    <div class="contact-content contact-item-phone">
                                        <span>0123 456 789</span>
                                        <br>
                                        <span>0987 654 321</span>
                                    </div>
                                </div>
                                <div class="contact-item">
                                    <div class="contact-item-icon">
                                        <i class="fa-regular fa-envelope"></i>
                                    </div>
                                    <div class="contact-content conatct-item-email">
                                        <span>abc@cake.com</span><br />
                                        <span>infoabc@cake.com</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </footer>
        <div class="copyright-wrap">
            <div class="container">
                <div class="copyright-content">
                    <p>Sweet Cake. All Rights Reserved.</p>
                </div>
            </div>
        </div>
        <div class="back-to-top">
            <a href="#"><i class="fa-regular fa-arrow-up"></i></a>
        </div> 
    </div>
    <script src="./js/toast-message.js"></script>
    <script src="./js/main.js"></script> 
</body>
</html>
