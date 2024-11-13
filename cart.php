<?php
include './connect_db.php';
if (!isset($_SESSION["cart"])) {
    $_SESSION["cart"] = array();
}
if (!isset($_SESSION["notes"])) {
    $_SESSION["notes"] = array(); // Khởi tạo mảng lưu ghi chú cho mỗi sản phẩm
}

$error = false;
$success = false;

if (isset($_GET['action'])) {
    function update_cart($add = false) {
        // Kiểm tra tồn tại mảng quantity và note trước khi sử dụng
        if (isset($_POST['quantity']) && is_array($_POST['quantity'])) {
            foreach ($_POST['quantity'] as $id => $quantity) {
                if ($quantity == 0 || empty($quantity)) {
                    unset($_SESSION["cart"][$id]);
                    unset($_SESSION["notes"][$id]);
                } else {
                    if ($add) {
                        $_SESSION["cart"][$id] = (isset($_SESSION["cart"][$id]) ? $_SESSION["cart"][$id] : 0) + $quantity;
                    } else {
                        $_SESSION["cart"][$id] = $quantity;
                    }
                }
          
                // Kiểm tra và lưu ghi chú mới nếu có
                if (isset($_POST['note']) && !empty(trim($_POST['note']))) {
                    $_SESSION["notes"][$id] = trim($_POST['note']);
                    // Nếu chưa có ghi chú cũ và không có ghi chú mới, đặt mặc định là "Không có ghi chú"
                } elseif (!isset($_SESSION["notes"])) {
                    $_SESSION["notes"][$id] = "Không có ghi chú";
                }
            }
        }
    }

    switch ($_GET['action']) {
        case "add":
            update_cart(true);
            break;
        case "delete":
            if (isset($_GET['id'])) {
                unset($_SESSION["cart"][$_GET['id']]);
                unset($_SESSION["notes"][$_GET['id']]);
            }
            break;
        case "submit":
            if (isset($_POST['update_click'])) { // Cập nhật số lượng sản phẩm
                update_cart();
            }
            break;
    }
}

// Truy vấn sản phẩm có trong giỏ hàng
if (!empty($_SESSION["cart"])) {
    $product_ids = implode(",", array_map('intval', array_keys($_SESSION["cart"])));
    $products = mysqli_query($con, "SELECT * FROM product WHERE ProductID IN ($product_ids)");
}
?>

    <div class="modal-cart" id="model-cart">
        <div class="cart-container">
            <div class="cart-header">
                <h3 class="cart-header-title">
                    <i class="fa-regular fa-basket-shopping-simple"></i> Giỏ hàng
                </h3>
                <button class="cart-close" onclick="closeCart();" >
                    <i class="fa-sharp fa-solid fa-xmark"></i>
                </button>
            </div>
            <form id="cart-form" action="?action=submit" method="POST">
                <div class="cart-body">
                    <ul class="cart-list">
                        <?php
                        if (!empty($products)) {
                            $total = 0;
                            $num = 1;
                            while ($row = mysqli_fetch_array($products)) {
                                $total += $row['Price'] * $_SESSION["cart"][$row['ProductID']];
                                ?>
                                <li class="cart-item" data-id="<?= $row['ProductID'] ?>">
                                    <div class="cart-item-info">
                                        <div class="card-header">
                                            <img class="card-image" src="<?= $row['ProductImage'] ?>">
                                        </div>
                                        <p class="cart-item-title"><?= $row['ProductName'] ?></p>
                                        <span class="cart-item-price price" data-price="<?= $row['Price'] ?>">
                                            <?= number_format($row['Price'], 0, ",", ".") ?> ₫
                                        </span>
                                    </div>
                                    <p class="product-note">
                                        <i class="fa-light fa-pencil"></i>
                                        <span>
                                            <?php
                                            // Hiển thị ghi chú nếu có, nếu không thì ghi là "Không có ghi chú"
                                            echo isset($_SESSION["notes"][$row['ProductID']]) ? $_SESSION["notes"][$row['ProductID']] : "Không có ghi chú";
                                            ?>
                                        </span>
                                    </p>
                                    <div class="cart-item-control">
                                        <a href="?action=delete&id=<?= $row['ProductID'] ?>" class="cart-item-delete">Xóa</a>
                                        <div class="buttons_added">
                                            <input class="minus is-form" type="button" value="-" ">
                                            <input class="input-qty" max="100" min="1" name="quantity[<?= $row['ProductID'] ?>]" type="number" value="<?= $_SESSION["cart"][$row['ProductID']] ?>">
                                            <input class="plus is-form" type="button" value="+" ">
                                        </div>
                                    </div>
                                </li>
                                <?php
                                $num++;
                            }
                        } else {
                            echo '<div class="gio-hang-trong"><i class="fa-thin fa-cart-xmark"></i><p>Không có sản phẩm nào trong giỏ hàng của bạn</p></div>';
                        }
                        ?>
                    </ul>
                </div>
                <div class="cart-footer">
                    <div class="cart-total-price">
                        <p class="text-tt">Tổng tiền:</p>
                        <p class="text-price">
                            <?php if (!empty($products)) { ?>
                                <?= number_format($total, 0, ",", ".") ?>₫
                            <?php } else { ?>
                                0₫
                            <?php } ?>
                        </p>
                    </div>
                    <div class="cart-footer-payment">
                        <?php if (!empty($products)) { ?>
                            <button class="them-mon" type="submit" name="update_click" value="Cập nhật"><i class="fa-light fa-arrow-rotate-right"></i> Cập nhật</button>
                            <a href="pay.php" class="thanh-toan">Thanh toán</a>
                        <?php } else { ?>
                            <button class="them-mon" disabled><i class="fa-light fa-arrow-rotate-right"></i> Cập nhật</button>
                            <button class="thanh-toan disabled">Thanh toán</button>
                        <?php } ?>
                    </div>                   
                </div>
            </form>
        </div>
    </div>