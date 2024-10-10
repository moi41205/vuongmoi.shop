<?php
include '../ketnoi.php'; // File kết nối cơ sở dữ liệu

// Lấy id của sản phẩm từ URL
$id = isset($_GET['id']) ? $_GET['id'] : 0;

$sql = "SELECT h.*, cs.Thongso , cs.baohanh , cs.voicher , cs.giagoc
        FROM hang h 
        LEFT JOIN chitiet_sanpham cs ON h.Mahang = cs.Mahang 
        WHERE h.Mahang = '$id'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $product = $result->fetch_assoc();
} else {
    echo "Không tìm thấy sản phẩm"; 
    exit;
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chi tiết Sản phẩm</title>
    <link rel="stylesheet" href="../css/sanpham.css">
    
    <script src="https://unpkg.com/unlazy@0.11.3/dist/unlazy.with-hashing.iife.js" defer init></script>
    <link rel="shortcut icon" href="../img/logo1.png" type="image/x-icon">
</head>

<body>
<nav class="navbar">
    <a href="trangchu.php">
        <div class="logo">
            <img src="../img/logo.png" alt="Logo" style="border-radius: 50%;">
            <span class="shop-name">Vương Moi Shop</span>
        </div>
    </a>
    <div class="nav-links">
        <a href="giohangtoi.php"><img src="../img/giohang.png" alt="giohang" class="giohang"></a>
        <a href="../trangchuxong.php">Đăng xuất</a>
    </div>
</nav>

    <!-- Nội dung sản phẩm -->
    <div class="sanpham-container">
        <div class="container">
            <div class="product">
                <div class="product-images">
                    <img id="mainImage" src="../img/sản_phẩm/<?php echo $product['anh']; ?>" alt="Ảnh sản phẩm">
                </div>
                <div class="product-details">
                    <h1><?php echo $product['Tenhang']; ?></h1>
                    <div class="rating">5.0 ★★★★★ | Đánh giá: 0</div>
                    <div class="price">
                        <?php echo number_format($product['Dongia'], 0, ',', '.'); ?>đ
                        <span style="text-decoration: line-through; color: #888; margin-left: 10px;font-size: 20px;">
                        <?php echo number_format($product['giagoc'], 0, ',', '.'); ?>đ</span>
                        <span class="discount"><?php echo $product['voicher']; ?> giảm</span>
                    </div>
                    <div class="ghichu">
                        <li>Tình Trạng: <?php echo $product['Soluongton'] > 0 ? 'Còn hàng' : 'Hết hàng'; ?></li>
                        <li>Bảo hành: <?php echo $product['baohanh']; ?></li>
                        <li>Combo mua 4 được giảm 0%</li>
                    </div>
                    <!-- Phần chọn số lượng -->
                    <div class="quantity-selector">
                        <label for="quantity">Số Lượng</label>
                        <div class="quantity-input">
                            <button class="quantity-btn" onclick="decreaseQuantity()">-</button>
                            <input type="text" id="quantity" value="1" min="1">
                            <button class="quantity-btn" onclick="increaseQuantity()">+</button>
                        </div>
                    </div>
                    <div style="display: flex;" class="buttons">
                        <button style="border: 1px solid black;" 
                            onclick="orderNow('<?php echo $product['Mahang']; ?>', '<?php echo addslashes($product['Tenhang']); ?>', <?php echo $product['Dongia']; ?>, '<?php echo addslashes($product['anh']); ?>',<?php echo $product['Soluongton']; ?>)" 
                            class="bg-primary text-primary-foreground hover:bg-primary/80 px-4 py-2 rounded-lg block mt-4">
                            Mua ngay
                        </button>

                        <button style="border: 1px solid black;" 
                            onclick="addToCart('<?php echo $product['Mahang']; ?>', '<?php echo addslashes($product['Tenhang']); ?>', <?php echo $product['Dongia']; ?>, '../img/sản_phẩm/<?php echo addslashes($product['anh']); ?>', <?php echo $product['Soluongton']; ?>)"
                            class="bg-accent text-accent-foreground hover:bg-accent/80 px-4 py-2 rounded-lg block mt-4">
                            Thêm vào giỏ hàng
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Thông tin chi tiết sản phẩm -->
        <div class="container">
            <div style="background-color: #fafafa; padding: 10px 20px;">
                <h1 style="font-size: 24px; margin-bottom: 10px;">Chi tiết sản phẩm</h1>
            </div>
            <p>Tên sản phẩm: <?php echo $product['Tenhang']; ?></p>
            <p><?php echo nl2br(str_replace(',', '<br>', $product['Thongso'])); ?></p>
        </div>
    </div>
<script>
// Hàm giảm số lượng
function decreaseQuantity() {
    var quantityInput = document.getElementById('quantity');
    var currentValue = parseInt(quantityInput.value);
    if (currentValue > 1) {
        quantityInput.value = currentValue - 1;
    }
}

// Hàm tăng số lượng
function increaseQuantity() {
    var quantityInput = document.getElementById('quantity');
    var currentValue = parseInt(quantityInput.value);
    quantityInput.value = currentValue + 1;
}

// Hàm xử lý khi nhấn nút "Mua ngay"
function orderNow(mahang, tenhang, dongia, anh, soluongton) {
    var quantity = document.getElementById('quantity').value;

    // Kiểm tra nếu sản phẩm tạm thời hết hàng (soluongton = 0)
    if (soluongton == 0) {
        alert("Sản phẩm tạm thời hết hàng!");
        return; // Không thực hiện tiếp nếu sản phẩm hết hàng
    }

    // Điều hướng đến trang thanh toán nếu sản phẩm còn hàng
    window.location.href = 'thanhtoan.php?mahang=' + mahang + 
        '&tenhang=' + encodeURIComponent(tenhang) + 
        '&dongia=' + dongia + 
        '&soluong=' + quantity +
        '&soluongton=' + soluongton +  // Thêm số lượng tồn kho
        '&anh=' + encodeURIComponent(anh);  // Thêm thông tin ảnh
}

// Hàm xử lý khi nhấn nút "Thêm vào giỏ hàng"
function addToCart(mahang, tenhang, dongia, anh, soluongton) {
    var quantity = document.getElementById('quantity').value;

    // Kiểm tra nếu sản phẩm tạm thời hết hàng (soluongton = 0)
    if (soluongton == 0) {
        alert("Sản phẩm tạm thời hết hàng!");
        return; // Không thực hiện tiếp nếu sản phẩm hết hàng
    }

    // Kiểm tra số lượng sản phẩm người dùng nhập có vượt quá số lượng tồn kho hay không
    if (quantity > soluongton) {
        alert("Số lượng không đủ, sản phẩm tồn kho chỉ còn " + soluongton + " sản phẩm.");
        return; // Không thực hiện tiếp nếu số lượng vượt quá tồn kho
    }

    // Điều hướng đến trang giỏ hàng và truyền đầy đủ thông tin sản phẩm
    window.location.href = 'giohangtoi.php?mahang=' + mahang + 
        '&tenhang=' + encodeURIComponent(tenhang) + 
        '&dongia=' + dongia + 
        '&soluong=' + quantity + 
        '&soluongton=' + soluongton +  // Thêm số lượng tồn kho
        '&anh=' + encodeURIComponent(anh);
}
</script>

</body>
</html>
