<div class="page-nav">
    <ul class="page-nav-list" id="pagination">
    <?php
    if ($totalPages > 1) {
        // Tạo chuỗi tham số
        $param = "";
        if ($search) {
            $param .= "name=" . urlencode($search) . "&";
        }
        if ($category) {
            $param .= "category=" . urlencode($category) . "&";
        }
        if ($min_price !== null) {
            $param .= "min_price=" . $min_price . "&";
        }
        if ($max_price !== null) {
            $param .= "max_price=" . $max_price . "&";
        }
        if ($sort_order) {
            $param .= "sort_order=" . $sort_order . "&";
        }

        // Phân trang
        if ($current_page > 3) {
            $first_page = 1;
            ?>
            <li class="page-nav-item">
                <a href="?<?=$param?>per_page=<?= $item_per_page ?>&page=<?= $first_page ?>">F</a>
            </li>
            <?php
        }

        if ($current_page > 2) {
            $prev_page = $current_page - 1;
            ?>
            <li class="page-nav-item">
                <a href="?<?=$param?>per_page=<?= $item_per_page ?>&page=<?= $prev_page ?>">P</a>
            </li>
            <?php
        }

        for ($num = 1; $num <= $totalPages; $num++) {
            if ($num != $current_page) {
                if ($num > $current_page - 3 && $num < $current_page + 3) { ?>
                    <li class="page-nav-item">
                        <a href="?<?=$param?>per_page=<?= $item_per_page ?>&page=<?= $num ?>"><?= $num ?></a>
                    </li>
                <?php }
            } else { ?>
                <li class="page-nav-item active">
                    <a><?= $num ?></a>
                </li>
            <?php }
        }

        if ($current_page < $totalPages - 1) {
            $next_page = $current_page + 1;
            ?>
            <li class="page-nav-item">
                <a href="?<?=$param?>per_page=<?= $item_per_page ?>&page=<?= $next_page ?>">N</a>
            </li>
            <?php
        }

        if ($current_page < $totalPages - 3) {
            $end_page = $totalPages;
            ?>
            <li class="page-nav-item">
                <a href="?<?=$param?>per_page=<?= $item_per_page ?>&page=<?= $end_page ?>">L</a>
            </li>
            <?php
        }
    }
    ?>
    </ul>
</div>
