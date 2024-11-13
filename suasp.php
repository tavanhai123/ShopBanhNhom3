<iframe src="adminsp.php" style="width: 100%; height: 100%; border: none;"></iframe>
<?php
session_start();

// Kết nối cơ sở dữ liệu
$conn = new mysqli("localhost", "root", "", "shopbanh");
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}
$conn->set_charset("utf8");

// Lấy danh mục sản phẩm
$categories = mysqli_query($conn, "SELECT * FROM category");

// Kiểm tra và lấy thông tin sản phẩm nếu có id
$product = [];
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = (int)$_GET['id'];
    $query = "SELECT * FROM product WHERE ProductId = ?";
    if ($stmt = $conn->prepare($query)) {
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $product = $result->fetch_assoc();
        $stmt->close();
    }
}

// Xử lý khi gửi form
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $tenMon = $_POST['ten-mon'] ?? $product['ProductName'];
    $category = $_POST['category'] ?? $product['CateID'];
    $giaMoi = $_POST['gia-moi'] ?? $product['Price'];
    $moTa = $_POST['mo-ta'] ?? $product['Description'];

    $uploadFile = $product['ProductImage'];  // Giữ lại tên ảnh cũ nếu không tải ảnh mới

    // Kiểm tra nếu có file được tải lên
    if (isset($_FILES['up-hinh-anh']) && $_FILES['up-hinh-anh']['error'] == 0) {
        $uploadDir = 'assets/img/products/';
        $file_name = basename($_FILES['up-hinh-anh']['name']);
        $uploadFile = $uploadDir . $file_name;

        if (!move_uploaded_file($_FILES['up-hinh-anh']['tmp_name'], $uploadFile)) {
            echo "Có lỗi khi tải lên hình ảnh.";
            $uploadFile = $product['ProductImage'];  // Đảm bảo giữ lại ảnh cũ nếu tải lên thất bại
        }
    }

    // Cập nhật sản phẩm vào cơ sở dữ liệu
    $sql = "UPDATE product SET ProductName = ?, Description = ?, CateID = ?, ProductImage = ?, Price = ? WHERE ProductID = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("ssisii", $tenMon, $moTa, $category, $uploadFile, $giaMoi, $id);
        if ($stmt->execute()) {
            header("Location: adminsp.php");
            exit();
        } else {
            echo "Có lỗi xảy ra khi cập nhật sản phẩm.";
        }
        $stmt->close();
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
<div class="modal add-product open" >
    <div class="modal-container">
        <h3 class="modal-container-title edit-product-e">CHỈNH SỬA SẢN PHẨM</h3>
        <button class="modal-close product-form" onclick="history.back()"><i class="fa-regular fa-xmark"></i></button>
        <div class="modal-content">
            <form action="" method="POST" enctype="multipart/form-data" class="add-product-form">
                <input type="hidden" name="id" value="<?php echo htmlspecialchars($product['ProductID']); ?>">
                <div class="modal-content-left">
                    <img src="<?php echo htmlspecialchars($product['ProductImage']); ?>" alt="" class="upload-image-preview" id="image-preview">
                    <div class="form-group file">
                        <label for="up-hinh-anh" class="form-label-file"><i class="fa-regular fa-cloud-arrow-up"></i> Chọn hình ảnh</label>
                        <input accept="image/jpeg, image/png, image/jpg" id="up-hinh-anh" name="up-hinh-anh" type="file" class="form-control" onchange="uploadImage(this)">
                    </div>
                </div>
                <div class="modal-content-right">
                    <div class="form-group">
                        <label for="ten-mon" class="form-label">Tên món</label>
                        <input id="ten-mon" name="ten-mon" type="text" value="<?php echo htmlspecialchars($product['ProductName']); ?>" placeholder="Nhập tên món" class="form-control">
                        <span class="form-message"></span>
                    </div>
                    <div class="form-group">
                        <label for="category" class="form-label">Chọn món</label>
                        <select name="category" id="chon-mon">
                            <?php while ($category = mysqli_fetch_assoc($categories)): ?>
                                <option value="<?php echo $category['CateID']; ?>" 
                                    <?php echo $product['CateID'] == $category['CateID'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($category['CateName']); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                        <span class="form-message"></span>
                    </div>
                    <div class="form-group">
                        <label for="gia-moi" class="form-label">Giá bán</label>
                        <input id="gia-moi" name="gia-moi" type="text" value="<?php echo htmlspecialchars($product['Price']); ?>" placeholder="Nhập giá bán" class="form-control">
                        <span class="form-message"></span>
                    </div>
                    <div class="form-group">
                        <label for="mo-ta" class="form-label">Mô tả</label>
                        <textarea class="product-desc" id="mo-ta" name="mo-ta" placeholder="Nhập mô tả món ăn..."><?php echo htmlspecialchars($product['Description']); ?></textarea>
                        <span class="form-message"></span>
                    </div>
                    <button class="form-submit btn-update-product-form edit-product-e" id="update-product-button" style="display: block;">
                        <i class="fa-light fa-pencil"></i>
                        <span>LƯU THAY ĐỔI</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<script src="./js/main.js"></script> 
</body>
</html>
<?php $conn->close(); ?>