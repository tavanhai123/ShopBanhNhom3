<?php
session_start();
include './connect_db.php';


// Khởi tạo các biến cho dữ liệu đơn hàng
$orderItems = [];
$totalQuantity = 0;
$totalPrice = 0;
$shippingFee = 30000;
$finalTotal = 0;

// Kiểm tra xem giỏ hàng có trống không
if (!empty($_SESSION["cart"])) {
    $productIds = implode(",", array_keys($_SESSION["cart"]));
    $products = mysqli_query($con, "SELECT * FROM `product` WHERE `ProductID` IN ($productIds)");

    while ($product = mysqli_fetch_assoc($products)) {
        $productId = $product['ProductID'];
        $quantity = $_SESSION["cart"][$productId];
        $productName = $product['ProductName'];
        $productPrice = $product['Price'];
        
        $itemTotal = $quantity * $productPrice;
        
        $orderItems[] = [
            'id' => $productId,
            'name' => $productName,
            'quantity' => $quantity,
            'price' => $productPrice,
            'total' => $itemTotal,
        ];

        $totalQuantity += $quantity;
        $totalPrice += $itemTotal;
    }

    $finalTotal = $totalPrice + $shippingFee;
}

// Xử lý khi bấm nút "Đặt hàng"
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    if (!isset($_SESSION['userid'])) {
        echo "<script>alert('Bạn cần đăng nhập để tiếp tục'); window.location.href = 'dnhap.php';</script>";
        exit();
    }
    $userID = $_SESSION['userid'];
    $nameOder = $_POST['tennguoinhan'];
    $phoneOder = $_POST['sdtnhan'];
    $addressOder = $_POST['diachinhan'];
    $noteOrder = $_POST['note_order'] ?? null;
    $status = 1;
    
    // Kiểm tra các thông tin bắt buộc
    if (empty($_POST['tennguoinhan']) || empty($_POST['sdtnhan']) || empty($_POST['diachinhan'])) {
        echo "<script>alert('Bạn cần nhập đủ thông tin'); window.history.back();</script>";
        exit();
    }
    // Thêm đơn hàng vào bảng `order`
    $sqlOrder = "INSERT INTO `order` (OrderID, UserID, NameOder, PhoneOder, AddressOder, Note, Total, CreatTime, LastUpdate, Status) 
                 VALUES (NULL,'$userID', '$nameOder', '$phoneOder', '$addressOder', '$noteOrder', '$finalTotal', '".time()."', '".time()."', '$status')";
    
    if (mysqli_query($con, $sqlOrder)) {
        $orderID = mysqli_insert_id($con);

        // Thêm chi tiết đơn hàng vào bảng `orderdetails`
        foreach ($orderItems as $item) {
            $productID = $item['id'];
            $quantity = $item['quantity'];
            $price = $item['price'];
            $noteCart = $_SESSION["notes"][$productID] ?? "Không có ghi chú";

            $sqlOrderDetails = "INSERT INTO `orderdetails` (DetailsID, OrderID, ProductID, Quantity, PriceP, CreatTime, LastUpdate, NoteCart)
                                VALUES (NULL, '$orderID', '$productID', '$quantity', '$price', '".time()."', '".time()."', '$noteCart')";

            mysqli_query($con, $sqlOrderDetails);
        }

        // Xóa giỏ hàng sau khi đặt hàng thành công
        unset($_SESSION["cart"]);
        unset($_SESSION["notes"]);
        echo "<script>alert('Đơn hàng của bạn đã được đặt thành công!'); window.location.href = 'index.php';</script>";
    } else {
        echo "<script>alert('Đặt hàng không thành công. Vui lòng thử lại!');</script>";
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<form method="POST" action="pay.php">
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
<div class="checkout-page">
    <div class="checkout-header">
        <div class="checkout-return">
        <a href="index.php"><i class="fa-regular fa-chevron-left"></i></a>
        </div>
        <h2 class="checkout-title">Thanh toán</h2>
    </div>
    <main class="checkout-section container">
        <div class="checkout-col-left">
            <div class="checkout-row">
                <div class="checkout-col-title">
                    Thông tin đơn hàng
                </div>
                <div class="checkout-col-content">
                    <div class="content-group">
                        <p class="checkout-content-label">Hình thức giao nhận</p>
                        <div class="checkout-type-order">
                            <button class="type-order-btn active" id="giaotannoi">
                                <i class="fa-duotone fa-moped"
                                    style="--fa-secondary-opacity: 1.0; --fa-primary-color: dodgerblue; --fa-secondary-color: #ffb100;"></i>
                                Giao tận nơi
                            </button>
                        </div>
                    </div>
                    <div class="content-group">
                        <p class="checkout-content-label">Ghi chú đơn hàng</p>
                        <textarea name="note_order" type="text" class="note-order" placeholder="Nhập ghi chú"></textarea>
                    </div>
                </div>
            </div>
            <div class="checkout-row">
                <div class="checkout-col-title">
                    Thông tin người nhận
                </div>
                <div class="checkout-col-content">
                    <div class="content-group">
                        <form action="" class="info-nhan-hang">
                            <div class="form-group">
                                <input id="tennguoinhan" name="tennguoinhan" type="text"
                                    placeholder="Tên người nhận" class="form-control">
                                <span class="form-message"></span>
                            </div>
                            <div class="form-group">
                                <input id="sdtnhan" name="sdtnhan" type="text" placeholder="Số điện thoại nhận hàng"
                                    class="form-control">
                                <span class="form-message"></span>
                            </div>
                            <div class="form-group">
                                <input id="diachinhan" name="diachinhan" type="text" placeholder="Địa chỉ nhận hàng"
                                    class="form-control chk-ship">
                                <span class="form-message"></span>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="checkout-col-right">
            <p class="checkout-content-label">Đơn hàng</p>
            <div class="bill-total" id="list-order-checkout">
                <?php foreach ($orderItems as $item) { ?>
                    <div class="food-total">
                        <div class="count"><?= $item['quantity'] ?>x</div>
                        <div class="info-food">
                            <div class="name-food"><?= $item['name'] ?></div>
                        </div>
                        <div class="price-detail">
                            <?= number_format($item['total'], 0, ",", ".") ?> ₫
                        </div>
                    </div>
                <?php } ?>
            </div>
            <div class="bill-payment">
                <div class="total-bill-order">
                    <div class="priceFlx">
                        <div class="text">
                            Tiền hàng 
                            <span class="count"><?= $totalQuantity ?> món</span>
                        </div>
                        <div class="price-detail">
                            <span id="checkout-cart-total"><?= number_format($totalPrice, 0, ",", ".") ?> ₫</span>
                        </div>
                    </div>
                    <div class="priceFlx chk-ship">
                        <div class="text">Phí vận chuyển</div>
                        <div class="price-detail chk-free-ship">
                            <span><?= number_format($shippingFee, 0, ",", ".") ?> ₫</span>
                        </div>
                    </div>
                </div>
                <div class="policy-note">
                    Bằng việc bấm vào nút “Đặt hàng”, tôi đồng ý với
                    <a href="#" target="_blank">chính sách hoạt động</a>
                    của chúng tôi.
                </div>
            </div>
            <div class="total-checkout">
                <div class="text">Tổng tiền</div>
                <div class="price-bill">
                    <div class="price-final" id="checkout-cart-price-final"><?= number_format($finalTotal, 0, ",", ".") ?> ₫</div>
                </div>
            </div>
            <button type="submit" class="complete-checkout-btn">Đặt hàng</button>
        </div>    
    </main>
</div>
</body>
</form>
</html>
