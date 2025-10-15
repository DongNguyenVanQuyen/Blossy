<link rel="stylesheet" href="<?= BASE_URL ?>Public/Assets/Css/Admin/Detail-Order.css">
<div id="admin_detail">
    <?php
    include_once __DIR__ . '/../Layouts/Sidebar.php';
    ?>

    <div class="admin-main">
    <?php include_once __DIR__ . '/../Layouts/Header.php'; ?>

    <div class="order-content">
        <h2 class="order-title">📄 Chi tiết đơn hàng #<?= htmlspecialchars($order['id'] ?? 'N/A') ?></h2>

        <!-- 🔹 TÓM TẮT ĐƠN HÀNG -->
        <div class="order-summary">
        <div><strong>Khách hàng:</strong> <?= htmlspecialchars($order['first_name'] . ' ' . $order['last_name']) ?></div>
        <div><strong>Phương thức thanh toán:</strong> <?= strtoupper($order['payment_method'] ?? 'COD') ?></div>
        <div><strong>Trạng thái thanh toán:</strong> 
            <?= ($order['payment_status'] === 'da_thanh_toan') ? 'Đã thanh toán' : 'Chưa thanh toán' ?>
        </div>
        <div><strong>Ngày giao dự kiến:</strong> <?= date('d/m/Y', strtotime($order['delivery_date'] ?? '+3 days')) ?></div>
        <div><strong>Trạng thái đơn hàng:</strong>
            <?php
            $status = strtolower($order['status'] ?? 'cho_xac_nhan');
            echo match($status) {
                'cho_xac_nhan' => '🕒 Chờ xác nhận',
                'dang_giao'    => '🚚 Đang giao hàng',
                'thanh_cong'   => '✅ Hoàn thành',
                'da_huy'       => '❌ Đã hủy',
                default        => 'Không xác định'
            };
            ?>
        </div>

        <hr>

        <div><strong>Tổng phụ:</strong> <?= number_format($order['subtotal'] ?? 0, 0, ',', '.') ?>đ</div>
        <div><strong>Giảm giá từ Phiếu Giảm Giá:</strong> 
            <?= !empty($order['discount_total']) ? '-' . number_format($order['discount_total'], 0, ',', '.') . 'đ' : '0đ' ?>
        </div>
        <div><strong>Phí vận chuyển:</strong> <?= number_format($order['shipping_fee'] ?? 0, 0, ',', '.') ?>đ</div>
        <div><strong><span style="color:#b4662a;">Tổng cộng sau giảm:</span></strong> 
            <span style="font-weight:bold;"><?= number_format($order['grand_total'] ?? 0, 0, ',', '.') ?>đ</span>
        </div>
        </div>

        <!-- 🔹 DANH SÁCH SẢN PHẨM -->
        <div class="order-details">
        <h3>Danh sách sản phẩm</h3>

        <?php if (!empty($items)): ?>
            <table class="order-detail-table">
            <thead>
                <tr>
                <th>Ảnh</th>
                <th>Tên sản phẩm</th>
                <th>Giá gốc</th>
                <th>Giảm giá</th>
                <th>Giá sau giảm</th>
                <th>Số lượng</th>
                <th>Tổng</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $item): ?>
                <?php
                    $price = $item['old_price'] ?? 0;
                    $discount = $item['old_price'] - $item['price'] ;
                    $priceAfter = $price - $discount;
                    $qty = $item['quantity'] ?? 1;
                ?>
                <tr>
                    <td><img src="<?= htmlspecialchars($item['image_url'] ?? BASE_URL . 'Public/Assets/Image/no_image.png') ?>" 
                            alt="<?= htmlspecialchars($item['name']) ?>" class="order-detail-img"></td>
                    <td><?= htmlspecialchars($item['name']) ?></td>
                    <td><?= number_format($price, 0, ',', '.') ?>đ</td>
                    <td><?= $discount > 0 ? '-' . number_format($discount, 0, ',', '.') . 'đ' : '0đ' ?></td>
                    <td><?= number_format($priceAfter, 0, ',', '.') ?>đ</td>
                    <td><?= (int)$qty ?></td>
                    <td><strong><?= number_format($priceAfter * $qty, 0, ',', '.') ?>đ</strong></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
            </table>
        <?php else: ?>
            <p class="order-empty">Không có sản phẩm trong đơn hàng này.</p>
        <?php endif; ?>
        </div>

        <!-- 🔹 NÚT QUAY LẠI -->
        <div class="order-actions">
        <a href="index.php?controller=adminorder&action=index" class="btn-back">⬅ Quay lại quản lý đơn hàng</a>
        </div>
    </div>
    </div>
</div>