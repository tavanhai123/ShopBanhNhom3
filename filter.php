<?php
    include './connect_db.php';

    // Nhận dữ liệu từ form gửi qua phương thức GET
    $search = isset($_GET['name']) ? $_GET['name'] : "";
    $category = isset($_GET['category']) ? $_GET['category'] : "";
    $min_price = isset($_GET['min_price']) ? (float)$_GET['min_price'] : 0;
    $max_price = isset($_GET['max_price']) ? (float)$_GET['max_price'] : 0;
    $sort_order = isset($_GET['sort_order']) ? $_GET['sort_order'] : "";

    $conditions = [];

    // Thêm điều kiện tìm kiếm theo tên nếu có
    if ($search) {
        $conditions[] = "`ProductName` LIKE '%" . mysqli_real_escape_string($con, $search) . "%'";
    }

    // Thêm điều kiện lọc theo danh mục nếu có
    if ($category && $category !== "Tất cả") {
        $conditions[] = "`CateID` = '" . mysqli_real_escape_string($con, $category) . "'";
    }

    // Thêm điều kiện lọc theo giá nếu có
    if ($min_price > 0) {
        $conditions[] = "`Price` >= " . $min_price;
    }
    if ($max_price > 0) {
        $conditions[] = "`Price` <= " . $max_price;
    }

    // Xây dựng câu truy vấn SQL với các điều kiện
    $sql = "SELECT * FROM `product`";
    if (count($conditions) > 0) {
        $sql .= " WHERE " . implode(" AND ", $conditions);
    }

    // Sắp xếp sản phẩm nếu có yêu cầu
    if ($sort_order === "asc") {
        $sql .= " ORDER BY `Price` ASC";
    } elseif ($sort_order === "desc") {
        $sql .= " ORDER BY `Price` DESC";
    } else {
        $sql .= " ORDER BY `ProductID` ASC";
    }

    // Thực hiện truy vấn
    $result = mysqli_query($con, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Filter Results</title>
    <link rel="stylesheet" href="./assets/css/main.css">
</head>
<body>
    <div class="container">
        <h2>Kết quả lọc</h2>
        <div class="home-products">
            <?php if ($result && mysqli_num_rows($result) > 0) { ?>
                <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                    <div class="col-product">
                        <article class="card-product">
                            <div class="card-header">
                                <a href="detail.php?ProductID=<?= $row['ProductID'] ?>" class="card-image-link">
                                    <img class="card-image" src="./<?= $row['ProductImage'] ?>" alt="<?= $row['ProductName'] ?>">
                                </a>
                            </div>
                            <div class="food-info">
                                <div class="card-content">
                                    <div class="card-title">
                                        <a href="detail.php?ProductID=<?= $row['ProductID'] ?>" class="card-title-link"><?= $row['ProductName'] ?></a>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <div class="product-price">
                                        <span class="current-price"><?= number_format($row['Price'], 0, ",", ".") ?> VND</span>
                                    </div>
                                </div>
                            </div>
                        </article>
                    </div>
                <?php } ?>
            <?php } else { ?>
                <p>Không tìm thấy sản phẩm nào.</p>
            <?php } ?>
        </div>
    </div>
</body>
</html>
