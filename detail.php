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
<?php
  session_start();
  include './connect_db.php';
  $result = mysqli_query($con, "SELECT * FROM `product` WHERE ProductID = ".$_GET['ProductID']);
  $detail = mysqli_fetch_assoc($result);
  $isLoggedIn = isset($_SESSION['username']);
?>
<div id="toast"></div>
    <div class="modal product-detail open">
        <button class="modal-close close-popup"><i class="fa-thin fa-xmark"></i></button>
        <div class="modal-container mdl-cnt" id="product-detail-content">
            <div class="modal-header">
                <img class="product-image" src="./<?= $detail['ProductImage'] ?>" alt="">
            </div>
            <form id="add-to-cart-form" action="index.php?action=add" method="POST">
                <div class="modal-body">
                    <h2 class="product-title"><?= $detail['ProductName'] ?></h2>
                    <div class="product-control">
                        <div class="priceBox">
                            <span class="current-price"><?= number_format($detail['Price'], 0, ",", ".") ?> VND</span>
                        </div>
                        <div class="buttons_added">
                            <input class="minus is-form" type="button" value="-">
                            <input class="input-qty" max="100" min="1" name="quantity[<?=$detail['ProductID']?>]" type="number" value="1">
                            <input class="plus is-form" type="button" value="+" >
                        </div>
                    </div>
                    <p class="product-description"><?= $detail['Description'] ?></p>
                </div>
                <div class="notebox">
                    <p class="notebox-title">Ghi chú</p>
                    <textarea class="text-note" id="popup-detail-note" name="note" placeholder="Nhập thông tin cần lưu ý..."></textarea>
                </div>
                <div class="modal-footer">
                    <div class="modal-footer-control">
                        <button class="button-dat" id="add-cart" type="button" onclick="handleAddToCart(<?= json_encode($isLoggedIn) ?>)"><i class="fa-light fa-basket-shopping"></i> Thêm vào giỏ hàng</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
<script src="./js/toast-message.js"></script>
<script src="./js/main.js"></script>
</body>
</html>
