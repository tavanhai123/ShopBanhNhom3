<iframe src="adminsp.php" style="width: 100%; height: 100%; border: none;"></iframe>
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


$cate = mysqli_query($conn,"SELECT * FROM category");

if (isset($_POST['ten-mon'])) {
    // Lấy dữ liệu từ form
    $tenMon = $_POST['ten-mon'];
    $category = $_POST['category'];
    $giaMoi = $_POST['gia-moi'];
    $moTa = $_POST['mo-ta'];

    $file_name = "";  // Khởi tạo biến lưu tên tệp hình ảnh

    // Kiểm tra nếu có file được tải lên
    if (isset($_FILES['up-hinh-anh']) && $_FILES['up-hinh-anh']['error'] == 0) {
        $uploadDir = 'assets/img/products/';
        $file_name = basename($_FILES['up-hinh-anh']['name']);
        $uploadFile = $uploadDir . $file_name;

        // Di chuyển file vào thư mục
        if (!move_uploaded_file($_FILES['up-hinh-anh']['tmp_name'], $uploadFile)) {
            echo "Có lỗi khi tải lên hình ảnh.";
            $file_name = "";  // Đảm bảo $file_name là rỗng nếu tải lên thất bại
        }
    
    

   /* if (isset($_FILES['up-hinh-anh'])){
       
        $file = $_FILES['up-hinh-anh'];
        $file_name = $file['name'];
        move_uploaded_file($file['tmp_name'],'assets/img/products/'.$file_name);  */
    }
        

    $sql = "INSERT INTO product (`ProductName`, `Description`, `CateID`, `ProductImage`, `Price`) VALUES ('$tenMon', '$moTa', '$category', '$uploadFile', '$giaMoi')";
    $query = mysqli_query($conn,$sql);

    if ($query) {
        header("Location: adminsp.php");
        exit();
    } else {
        echo "Có lỗi xảy ra khi thêm sản phẩm.";
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
    <div class="modal-container" bis_skin_checked="1">
        <h3 class="modal-container-title">THÊM MỚI SẢN PHẨM</h3>
        <button class="modal-close product-form" onclick="history.back()"><i class="fa-regular fa-xmark"></i></button>
        <div class="modal-content" bis_skin_checked="1">
            <form action="" enctype="multipart/form-data" method="POST" class="add-product-form">
                <div class="modal-content-left" bis_skin_checked="1">
                    <img src="./assets/img/blank-image.png" alt="" class="upload-image-preview" id="image-preview">
                    <div class="form-group file" bis_skin_checked="1">
                        <label for="up-hinh-anh" class="form-label-file">
                            <i class="fa-regular fa-cloud-arrow-up"></i>Chọn hình ảnh
                        </label>
                        <input accept="image/jpeg, image/png, image/jpg" type="file" id="up-hinh-anh" name="up-hinh-anh"  class="form-control" onchange="uploadImage(this)">
                    </div>
                </div>
                <div class="modal-content-right" bis_skin_checked="1">
                    <div class="form-group" bis_skin_checked="1">
                        <label for="ten-mon" class="form-label">Tên món</label>
                        <input id="ten-mon" name="ten-mon" type="text" placeholder="Nhập tên món" class="form-control">
                        <span class="form-message"></span>
                    </div>
                    <div class="form-group" bis_skin_checked="1">
                        <label for="category" class="form-label">Chọn món</label>
                        <select name="category" id="chon-mon">
                            <option value="">---Danh mục---</option>
                            <?php foreach ($cate as $key => $value) {?>
                                <option value="<?php echo $value['CateID']?>"><?php echo $value['CateName']?></option>
                            <?php } ?>
                        </select>
                        <span class="form-message"></span>
                    </div>
                    <div class="form-group" bis_skin_checked="1">
                        <label for="gia-moi" class="form-label">Giá bán</label>
                        <input id="gia-moi" name="gia-moi" type="text" placeholder="Nhập giá bán" class="form-control">
                        <span class="form-message"></span>
                    </div>
                    <div class="form-group" bis_skin_checked="1">
                        <label for="mo-ta" class="form-label">Mô tả</label>
                        <textarea class="product-desc" name="mo-ta" id="mo-ta" placeholder="Nhập mô tả món ăn..."></textarea>
                        <span class="form-message"></span>
                    </div>
                    <button class="form-submit btn-add-product-form" id="add-product-button">
                        <i class="fa-regular fa-plus"></i>
                        <span>THÊM MÓN</span>
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