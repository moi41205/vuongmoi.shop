
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/trangview.css">
    <link rel="stylesheet" href="../css/banner.css">
    <link rel="stylesheet" href="../css/danhmuc.css">
    <script src="https://cdn.tailwindcss.com?plugins=forms,typography"></script>
    <script src="https://unpkg.com/unlazy@0.11.3/dist/unlazy.with-hashing.iife.js" defer init></script>
    
    <title>Vương Moi Shop</title>
    <link rel="shortcut icon" href="../img/logo1.png" type="image/x-icon">
    <style>
        .out-of-stock {
            
    position: absolute !important;
    top: 15% !important; /* Điều chỉnh vị trí của hình ảnh */
    left: 0% !important; /* Điều chỉnh vị trí của hình ảnh */
    width: 100% !important; /* Kích thước hình ảnh "hết hàng" */
    height: auto !important; /* Tự động điều chỉnh chiều cao theo chiều rộng */
    pointer-events: none !important; /* Không cho phép tương tác với hình ảnh "hết hàng" */
    z-index: 10 !important; /* Đặt thứ tự z để đảm bảo hình ảnh "hết hàng" nằm trên cùng */
}

.w-96{
    width: 40rem !important;
}
    </style>
</head>
<body>
    <div class="bg-background text-foreground min-h-screen">
        <nav class="bg-primary text-primary-foreground p-4">
            <div class="container mx-auto flex justify-between items-center">
                <a class="text-xl font-bold flex items-center" href="trangchu.php">
                    <img src="../img/logo.png" alt="Logo" class="h-12 w-auto mr-2 rounded-full" />
                    <span>Vương Moi Shop</span>
                </a>
                <!-- thanh tìm kiếm  -->
                <div class="relative">
                    <form action="trangchu.php" method="GET" class="flex">
                        <input type="text" name="search" placeholder="100% Hàng Thật" value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>" class="border rounded-2xl px-4 py-2 w-96 focus:border-primary-foreground focus:ring-1 focus:ring-primary-foreground text-black" />
                        <button type="submit" class="absolute right-0 top-0 mt-2 mr-2">🔍</button>
                    </form>
                </div>
                <ul class="flex space-x-4">
                    <li><a href="giohang1.php" class="hover:underline"><img src="../img/giohang.png" alt="giohang" class="h-10 w-10 mr-2 "></a></li>
                    <li><a href="trangchuxong.php" class="dangnhap">Đăng xuất</a></li>
                    <li></li>
                
                </ul> 
            </div>
        </nav>

        <div class="price">
            <header>
            <div class="banner-container">
            <!-- Banner Chính -->
            <div class="carousel-container">
                <button class="prev" onclick="prevSlide()">&#10094;</button>
                <?php include '../Kết_nối_banner.php'; ?>
                <div class="carousel-slide">
                    <?php
                    // Hiển thị các hình ảnh của banner chính
                    for ($i = 1; $i <= 4; $i++) {
                        $key = "main_banner_$i";
                        if (!empty($banner_images[$key])) {
                            echo "<img src='../{$banner_images[$key]}' alt='Banner Main $i'>";
                        }
                    }
                    ?>
                </div>
                <button class="next" onclick="nextSlide()">&#10095;</button>
            </div>

            <!-- Banner Phụ -->
            <div class="banner-side">
                <div class="side-banner">
                    <?php if (!empty($banner_images['side_banner_1'])): ?>
                        <img  src="../<?= $banner_images['side_banner_1'] ?>" alt="Banner Phụ 1">
                    <?php endif; ?>
                </div>
                <div class="side-banner">
                    <?php if (!empty($banner_images['side_banner_2'])): ?>
                        <img src="../<?= $banner_images['side_banner_2'] ?>" alt="Banner Phụ 2">
                    <?php endif; ?>
                </div>
            </div>
        </div>
            </header>
        </div>

        <div class="dm"><h1>DANH MỤC SẢN PHẨM</h1></div>
<div class="category-container">
    <?php
        // Include database connection
        include '../db_connect.php';

        // Check for connection error
        if (!$con) {
            die("Connection failed: " . mysqli_connect_error());
        }

        // SQL query để lấy các danh mục sản phẩm
        $sql = "SELECT Maloaihang, Tenloaihang, anh FROM loaihang";
        $result = mysqli_query($con, $sql);

        // Kiểm tra và hiển thị các danh mục
        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $Maloaihang = $row['Maloaihang'];
                $Tenloaihang = $row['Tenloaihang'];
                $anh = $row['anh'];

                echo "
                <div class='category-item'>
                    <a href='?category=$Maloaihang'>
                        <img src='../img/danh_mục/$anh' alt='$Tenloaihang'>
                        <p>$Tenloaihang</p>
                    </a>
                </div>";
            }
        } else {
            echo "<p class='text-center'>Không có danh mục sản phẩm.</p>";
        }

        // Đóng kết nối
        mysqli_close($con);
    ?>
</div>

        <!-- Main Product Grid -->
<div class="container mx-auto grid grid-cols-1 sm:grid-cols-2 md:grid-cols-5 gap-4 py-8">
<?php
                // Include database connection
                include '../db_connect.php';

                // Check for connection error
                if (!$con) {
                    die("Connection failed: " . mysqli_connect_error());
                }

                // Lấy giá trị category từ URL nếu có
                $category = isset($_GET['category']) ? $_GET['category'] : '';

                // Lấy giá trị tìm kiếm từ URL nếu có
                $search = isset($_GET['search']) ? $_GET['search'] : '';

                // SQL query để lấy các sản phẩm
                $sql = "SELECT hang.Mahang, hang.Tenhang, hang.Mota, loaihang.Tenloaihang, hang.anh, hang.Dongia, hang.Soluongton 
                        FROM hang 
                        INNER JOIN loaihang ON hang.Maloaihang = loaihang.Maloaihang";

                // Nếu có danh mục được chọn, thêm điều kiện lọc
                if (!empty($category)) {
                    $sql .= " WHERE hang.Maloaihang = '$category'";
                }

                // Nếu có từ khóa tìm kiếm, thêm điều kiện lọc
                if (!empty($search)) {
                    if (!empty($category)) {
                        $sql .= " AND";
                    } else {
                        $sql .= " WHERE";
                    }
                    $sql .= " hang.Tenhang LIKE '%$search%'";
                }
 
                $result = mysqli_query($con, $sql);

                // Kiểm tra và hiển thị các sản phẩm
                if (mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        $Tenhang = $row['Tenhang'];
                        $Mota = $row['Mota'];
                        $anh = $row['anh'];
                        $don_gia = number_format($row['Dongia'], 0, '.', '.');
                        $soluongton = $row['Soluongton'];
                        $Mahang = $row['Mahang'];

                        // Đường dẫn đến hình ảnh "hết hàng"
                        $outOfStockImage = '../img/sold_out.png';

                        echo "
                        <a href='sp.php?id=$Mahang'>
                            <div class='bg-card text-card-foreground p-4 rounded-lg shadow-md relative'>
                                <img src='../img/sản_phẩm/$anh' alt='$Tenhang' class='w-full h-81 object-cover rounded-lg mb-4' />
                                ";

                        // Hiển thị hình ảnh "hết hàng" nếu số lượng tồn bằng 0
                        if ($soluongton == 0) {
                            echo "
                                <img src='$outOfStockImage' alt='Hết hàng' class='out-of-stock opacity-90' />
                            ";
                        }

                        echo "
                                <h2 class='text-lg font-bold mb-2'>$Tenhang</h2>
                                <p class='text-sm text-muted-foreground mb-4'>$Mota</p>
                                <span class='text-lg font-bold text-black'>$don_gia VNĐ</span>
                            </div>
                        </a>";
                    }
                } else {
                    echo "<p class='text-center'>Không tìm thấy sản phẩm.</p>";
                }

                // Đóng kết nối
                mysqli_close($con);
            ?>
</div>

        <!-- Footer -->
        <footer class="bg-primary text-primary-foreground py-4 text-center">
            <p>&copy; 2024 Vương Moi Shop</p>
        </footer>
    </div>

    <script src="../script/trangchu.js"></script>
    <script src="../script/banner.js"></script>
</body>
</html>
