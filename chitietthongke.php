<?php
include 'adminthongke.php'
?>
    
    
    <div class="modal detail-order-product open">
    <div class="modal-container">
        <a href="adminthongke.php">
            <button class="modal-close"><i class="fa-regular fa-xmark"></i></button>
        </a>
        <div class="table">
            <table width="100%">
                <thead>
                    <tr>
                        <td>Mã đơn</td>
                        <td>Số lượng</td>
                        <td>Đơn giá</td>
                        <td>Ngày đặt</td>
                    </tr>
                </thead>
                <tbody id="show-product-order-detail">
                    <?php
                    // Kiểm tra có tham số product_id không
                    $product_id = isset($_GET['product_id']) ? $_GET['product_id'] : null;
                    $product_details = [];

                        // Nếu có product_id, lấy thông tin chi tiết đơn hàng
                    if ($product_id) {
                        $stmt = $conn->prepare("SELECT o.OrderID, oi.Quantity, oi.PriceP, o.CreatTime
                                                FROM orderdetails oi 
                                                JOIN `order` o ON oi.OrderID = o.OrderID 
                                                WHERE oi.ProductID = ?");
                        if ($stmt === false) {
                            die('Lỗi câu lệnh SQL: ' . $conn->error); // Hiển thị thông báo lỗi nếu câu lệnh không hợp lệ
                        }                        
                        $stmt->bind_param("i", $product_id);
                        $stmt->execute();
                        $result = $stmt->get_result();
                            
                         while ($row = $result->fetch_assoc()) {
                            $product_details[] = $row;
                        }
                    }
                    // Hiển thị chi tiết đơn hàng của sản phẩm
                    if ($product_id && !empty($product_details)) {
                        foreach ($product_details as $detail) {
                            echo "<tr>
                                    <td>{$detail['OrderID']}</td>
                                    <td>{$detail['Quantity']}</td>
                                    <td>" . number_format($detail['PriceP'], 0, ',', '.') . " VND</td>
                                    <td>" . date("d/m/Y H:i:s", strtotime($detail['CreatTime'])) . "</td>
                                  </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='4'>Không có dữ liệu.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
    
    
    
</body></html>
