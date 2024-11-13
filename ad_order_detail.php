<iframe src="admindonhang.php" style="width: 100%; height: 100%; border: none;"></iframe>
<?php
// Kết nối cơ sở dữ liệu
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

// Lấy OrderID từ URL
$order_id = isset($_GET['order_id']) ? $_GET['order_id'] : '';

function getOrderDetails($conn, $order_id) {
    // Sử dụng dấu nháy đơn ngược để tránh xung đột từ khóa
    $query = "SELECT 
                p.ProductName,
                p.ProductImage,
                o.NameOder,
                o.PhoneOder,
                o.AddressOder,
                o.CreatTime,
                o.LastUpdate,
                o.Note AS OrderNote,
                od.NoteCart,
                od.Quantity,
                od.PriceP,
                o.Total,
                o.Status,
                DATE_FORMAT(o.LastUpdate, '%d/%m/%Y %H:%i:%s') AS formatted_last_update
              FROM `order` o
              JOIN `orderdetails` od ON o.OrderID = od.OrderID
              JOIN `product` p ON od.ProductID = p.ProductID
              WHERE o.OrderID = ?";

    // Chuẩn bị câu lệnh
    $stmt = $conn->prepare($query);

    // Kiểm tra nếu chuẩn bị thất bại
    if ($stmt === false) {
        die("Lỗi truy vấn SQL: " . $conn->error);
    }

    // Bind tham số vào câu truy vấn
    $stmt->bind_param("s", $order_id);
    $stmt->execute();

    // Lấy kết quả
    $result = $stmt->get_result();

    // Kiểm tra nếu có kết quả
    if ($result && $result->num_rows > 0) {
        return $result->fetch_all(MYSQLI_ASSOC); // Trả về tất cả sản phẩm
    }
    return null;
}


// Lấy chi tiết đơn hàng
$order_details = $order_id ? getOrderDetails($conn, $order_id) : null;

// Cập nhật trạng thái khi form được gửi
if (isset($_POST['update_status'])) {
    if ($order_details && count($order_details) > 0) {
        // Đổi trạng thái (sử dụng phần tử đầu tiên của mảng)
        $current_status = $order_details[0]['Status']; 
        $new_status = $current_status == 1 ? 'Chưa xử lý' : 'Đã xử lý'; // Đổi trạng thái giữa 0 (Chưa xử lý) và 1 (Đã xử lý)

        // Cập nhật trạng thái trong cơ sở dữ liệu
        $sql_update_status = "UPDATE `order` SET Status = ?, LastUpdate = NOW() WHERE OrderID = ?";
        $stmt = $conn->prepare($sql_update_status);
        if ($stmt) {
            $stmt->bind_param("is", $new_status, $order_id);
            $stmt->execute();
            $stmt->close();
        } else {
            die("Lỗi chuẩn bị truy vấn: " . $conn->error);
        }

        // Cập nhật lại dữ liệu sau khi thay đổi
        $order_details = getOrderDetails($conn, $order_id);
    }
} else {
    // Nếu không gửi form, bạn cần đảm bảo mặc định trạng thái được hiển thị đúng
    if ($order_details && count($order_details) > 0) {
        $current_status = $order_details[0]['Status']; 
        $new_status = $current_status == 1 ? 'Chưa xử lý' : 'Đã xử lý';
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
    <link href='./assets/img/logo.png' rel='icon' type='image/x-icon' />
    <link rel="stylesheet" href="assets/css/admin.css">
    <link rel="stylesheet" href="./assets/css/toast-message.css">
    <link href="./assets/font/font-awesome-pro-v6-6.2.0/css/all.min.css" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="./assets/css/admin-responsive.css">
    <title>Chi tiết đơn hàng</title>
</head>
<body>
<?php if ($order_details): ?>
    <div class="modal detail-order open">
        <div class="modal-container">
            <h3 class="modal-container-title">CHI TIẾT ĐƠN HÀNG</h3>
            <form action="admindonhang.php" method="POST">
                <button class="modal-close"><i class="fa-regular fa-xmark"></i></button>
            </form>
            <div class="modal-detail-order">
                <div class="modal-detail-left">
                    <!-- Duyệt qua tất cả các sản phẩm trong đơn hàng -->
                    <?php foreach ($order_details as $item): ?>
                        <div class="order-item-group">
                            <div class="order-product">
                                <div class="order-product-left">
                                    <img src="<?php echo $item['ProductImage']; ?>" alt="">
                                    <div class="order-product-info">
                                        <h4><?php echo htmlspecialchars($item['ProductName']); ?></h4>
                                        <p class="order-product-note"><i class="fa-light fa-pen"></i> 
                                            <?php echo htmlspecialchars($item['NoteCart'] ? $item['NoteCart'] : 'Không có ghi chú'); ?>
                                        </p>
                                        <p class="order-product-quantity">SL: <?php echo $item['Quantity']; ?></p>
                                    </div>
                                </div>
                                <div class="order-product-right">
                                    <div class="order-product-price">
                                        <span class="order-product-current-price">
                                            <?php echo number_format($item['PriceP']); ?>&nbsp;₫
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <div class="modal-detail-right">
                    <ul class="detail-order-group">
                        <!-- Lấy thông tin từ sản phẩm đầu tiên (cùng đơn hàng) để hiển thị thông tin chung -->
                        <?php $first_item = $order_details[0]; ?>
                        <li class="detail-order-item">
                            <span class="detail-order-item-left"><i class="fa-light fa-calendar-days"></i> Ngày đặt hàng</span>
                            <span class="detail-order-item-right"><?php echo date('d/m/Y', $first_item['CreatTime']); ?></span>
                        </li>
                        <li class="detail-order-item">
                            <span class="detail-order-item-left"><i class="fa-light fa-truck"></i> Hình thức giao</span>
                            <span class="detail-order-item-right">Giao tận nơi</span>
                        </li>
                        <li class="detail-order-item">
                            <span class="detail-order-item-left"><i class="fa-thin fa-person"></i> Người nhận</span>
                            <span class="detail-order-item-right"><?php echo htmlspecialchars($first_item['NameOder']); ?></span>
                        </li>
                        <li class="detail-order-item">
                            <span class="detail-order-item-left"><i class="fa-light fa-phone"></i> Số điện thoại</span>
                            <span class="detail-order-item-right"><?php echo htmlspecialchars($first_item['PhoneOder']); ?></span>
                        </li>
                        <li class="detail-order-item tb">
                            <span class="detail-order-item-left"><i class="fa-light fa-clock"></i> Thời gian đặt</span>
                            <span class="detail-order-item-b"><?php echo date("d/m/Y H:i:s", $first_item['CreatTime']); ?></span>
                        </li>
                        <li class="detail-order-item tb">
                            <span class="detail-order-item-left"><i class="fa-light fa-clock"></i> Thời gian nhận</span>
                            <?php if ($first_item['Status'] == 1): ?>
                                <span class="detail-order-item-b"></span>
                            <?php else: ?>
                                <span class="detail-order-item-b"><?php echo date("d/m/Y H:i:s", $first_item['formatted_last_update']); ?></span>
                            <?php endif; ?>
                        </li>
                        <li class="detail-order-item tb">
                            <span class="detail-order-item-left"><i class="fa-light fa-location-dot"></i> Địa chỉ nhận</span>
                            <p class="detail-order-item-b"><?php echo htmlspecialchars($first_item['AddressOder']); ?></p>
                        </li>
                        <li class="detail-order-item tb">
                            <span class="detail-order-item-t"><i class="fa-light fa-note-sticky"></i> Ghi chú</span>
                            <p class="detail-order-item-b"><?php echo htmlspecialchars($first_item['OrderNote']); ?></p>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="modal-detail-bottom">
                <div class="modal-detail-bottom-left">
                    <div class="price-total">
                        <span class="thanhtien">Thành tiền</span>
                        <span class="price"><?php echo number_format($first_item['Total']); ?>&nbsp;₫</span>
                    </div>
                </div>
                <div class="modal-detail-bottom-right">
                    <form method="POST">
                        <button 
                            name="update_status" 
                            class="<?php echo $first_item['Status'] == 1 ? 'modal-detail-btn btn-chuaxuly' : 'modal-detail-btn btn-daxuly'; ?>">
                            <?php echo $first_item['Status'] == 1 ? 'Chưa xử lý' : 'Đã xử lý'; ?>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>
</body>
</html>