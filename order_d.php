<iframe src="order_h.php" style="width: 100%; height: 100%; border: none;"></iframe>

<?php
include './connect_db.php';
$order_id = $_GET['order_id']; // Lấy OrderID từ URL

// Lấy chi tiết đơn hàng từ bảng `order` và các bảng liên quan
$sql_order_details = "SELECT o.CreatTime, o.LastUpdate, o.Note, o.AddressOder, o.NameOder, o.PhoneOder, o.Status
                      FROM `order` o 
                      WHERE o.OrderID = $order_id";
$result_details = $con->query($sql_order_details);

if ($result_details->num_rows > 0) {
    $order = $result_details->fetch_assoc();
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
    <div class="modal detail-order open">
        <div class="modal-container mdl-cnt">
            <h3 class="modal-container-title">Thông tin đơn hàng</h3>
            <button class="form-close" onclick="window.history.back();"><i class="fa-regular fa-xmark"></i></button>
            <div class="detail-order-content">
                <ul class="detail-order-group">
                    <li class="detail-order-item">
                        <span class="detail-order-item-left"><i class="fa-light fa-calendar-days"></i> Ngày đặt hàng</span>
                        <span class="detail-order-item-right"><?php echo date("d/m/Y H:i:s", $order['CreatTime']); ?></span>
                    </li>
                    <li class="detail-order-item">
                        <span class="detail-order-item-left"><i class="fa-light fa-pencil"></i> Ghi chú đơn hàng</span>
                        <span class="detail-order-item-right"><?php echo $order['Note']; ?></span>
                    </li>
                    <li class="detail-order-item">
                        <span class="detail-order-item-left"><i class="fa-light fa-truck"></i> Hình thức giao</span>
                        <span class="detail-order-item-right">Giao tận nơi</span>
                    </li>
                    <li class="detail-order-item">
                        <span class="detail-order-item-left"><i class="fa-light fa-clock"></i> Ngày nhận hàng</span>
                        <?php if ($order['Status'] == 1): ?>
                            <span class="detail-order-item-right"><?php echo date("d/m/Y", $order['LastUpdate']); ?></span>
                        <?php else: ?>
                            <span class="detail-order-item-right"><?php echo date("d/m/Y H:i:s", $order['LastUpdate']); ?></span>
                        <?php endif; ?>
                        
                    </li>
                    <li class="detail-order-item">
                        <span class="detail-order-item-left"><i class="fa-light fa-location-dot"></i> Địa điểm nhận</span>
                        <span class="detail-order-item-right"><?php echo $order['AddressOder']; ?></span>
                    </li>
                    <li class="detail-order-item">
                        <span class="detail-order-item-left"><i class="fa-thin fa-person"></i> Người nhận</span>
                        <span class="detail-order-item-right"><?php echo $order['NameOder']; ?></span>
                    </li>
                    <li class="detail-order-item">
                        <span class="detail-order-item-left"><i class="fa-light fa-phone"></i> Số điện thoại nhận</span>
                        <span class="detail-order-item-right"><?php echo $order['PhoneOder']; ?></span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
<?php } else {
    echo "<p>Không tìm thấy chi tiết đơn hàng.</p>";
} ?>
</body>
</html>
