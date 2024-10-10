
<?php
session_start();
$loggedIn = isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'];
if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    header("Location: loginADMIN.php");
    exit;
}



include '../db_connect.php';

if (!$con) {
    die("Kết nối thất bại: " . mysqli_connect_error());
}


// Truy vấn Sản phẩm bán chạy
$bestSellingQuery = "SELECT h.Tenhang, SUM(ct.Soluong) AS TotalSold 
                     FROM hang h
                     JOIN chitiethd ct ON h.Mahang = ct.Mahang
                     JOIN hoadon hd ON ct.SohieuHD = hd.SohieuHD
                     WHERE hd.Trangthai = 'Giao hàng thành công'
                     GROUP BY h.Mahang
                     ORDER BY TotalSold DESC LIMIT 5";
$bestSellingResult = $con->query($bestSellingQuery);

// Truy vấn Sản phẩm bán chậm
$slowSellingQuery = "SELECT h.Tenhang, SUM(ct.Soluong) AS TotalSold 
                     FROM hang h
                     JOIN chitiethd ct ON h.Mahang = ct.Mahang
                     JOIN hoadon hd ON ct.SohieuHD = hd.SohieuHD
                     WHERE hd.Trangthai = 'Giao hàng thành công'
                     GROUP BY h.Mahang
                     ORDER BY TotalSold ASC LIMIT 5";
$slowSellingResult = $con->query($slowSellingQuery);

// Truy vấn Khách hàng mua nhiều
$frequentBuyersQuery = "SELECT k.Tenkhach, COUNT(hd.SohieuHD) AS TotalOrders
                        FROM khach k
                        JOIN hoadon hd ON k.id = hd.id
                        WHERE hd.Trangthai = 'Giao hàng thành công'
                        GROUP BY k.id
                        ORDER BY TotalOrders DESC LIMIT 5";
$frequentBuyersResult = $con->query($frequentBuyersQuery);

// Truy vấn Doanh số và Doanh thu trong tháng
$monthlySalesQuery = "SELECT SUM(hd.Tongtien) AS TotalRevenue, COUNT(hd.SohieuHD) AS TotalSales
                      FROM hoadon hd
                      WHERE hd.Trangthai = 'Giao hàng thành công'
                      AND MONTH(hd.NgayBH) = MONTH(CURRENT_DATE())
                      AND YEAR(hd.NgayBH) = YEAR(CURRENT_DATE())";
$monthlySalesResult = $con->query($monthlySalesQuery);
$monthlySalesData = $monthlySalesResult->fetch_assoc();

// Truy vấn Doanh số và Doanh thu trong quý
$quarterlySalesQuery = "SELECT SUM(hd.Tongtien) AS TotalRevenue, COUNT(hd.SohieuHD) AS TotalSales
                        FROM hoadon hd
                        WHERE hd.Trangthai = 'Giao hàng thành công'
                        AND QUARTER(hd.NgayBH) = QUARTER(CURRENT_DATE())
                        AND YEAR(hd.NgayBH) = YEAR(CURRENT_DATE())";
$quarterlySalesResult = $con->query($quarterlySalesQuery);
$quarterlySalesData = $quarterlySalesResult->fetch_assoc();

// Truy vấn Doanh số và Doanh thu trong năm
$yearlySalesQuery = "SELECT SUM(hd.Tongtien) AS TotalRevenue, COUNT(hd.SohieuHD) AS TotalSales
                     FROM hoadon hd
                     WHERE hd.Trangthai = 'Giao hàng thành công'
                     AND YEAR(hd.NgayBH) = YEAR(CURRENT_DATE())";
$yearlySalesResult = $con->query($yearlySalesQuery);
$yearlySalesData = $yearlySalesResult->fetch_assoc();


?>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
   
    
    <title>Trang chủ ADMIN</title>
    <style>
header {
    background-color: #333; /* Màu nền cho header */
    color: #fff; /* Màu chữ trong header */
    padding: 20px;
    text-align: center;
}

.tabs {
    display: flex;
    justify-content: center;
    background-color: #444; /* Màu nền cho menu */
}

.tab-button {
    text-decoration: none;
    background-color: transparent; /* Nền trong suốt cho các nút tab */
    border: none;
    color: #fff; /* Màu chữ cho nút tab */
    padding: 15px 20px;
    cursor: pointer; /* Con trỏ khi di chuột vào nút */
    font-size: 16px;
    transition: background-color 0.3s; /* Hiệu ứng chuyển màu nền */
}

.tab-button:hover {
    background-color: #555; /* Màu nền khi di chuột */
}

.tab-button.active {
    background-color: #007bff; /* Màu nền cho tab đang hoạt động */
    color: #fff; /* Màu chữ cho tab đang hoạt động */
    border-radius: 5px; /* Bo góc cho tab đang hoạt động */
}

.tab-content {
    display: none; /* Ẩn tất cả các tab nội dung */
    padding: 20px;
    background-color: #fff; /* Màu nền cho nội dung tab */
    border: 1px solid #ccc; /* Viền cho nội dung tab */
    border-radius: 5px; /* Bo góc cho nội dung tab */
    margin: 20px; /* Khoảng cách cho nội dung tab */
}

.tab-content.active {
    display: block; /* Hiển thị tab nội dung đang hoạt động */
}
.admin-header .header-container {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px;
       
    }

    .admin-header .admin-title {
        margin-top: 0;
        margin-bottom: 0;
        margin-left: 540px;
    }

    .admin-header .user-actions {
        list-style: none;
        margin: 0;
        padding: 0;
    }

    .admin-header .user-actions li {
        display: inline-block;
        margin-left: 20px;
    }

    .admin-header .user-actions a {
        text-decoration: none;
        color: #fff;
        
        padding: 8px 16px;
        border-radius: 5px;
        transition: background-color 0.3s ease;
    }
    /* Đặt font cho toàn bộ trang */
body {
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

.container {
    width: 80%;
    margin: 0 auto;
    padding: 20px;
}

h1 {
    text-align: center;
    margin-bottom: 40px;
}

h2 {
    font-size: 24px;
    margin-bottom: 20px;
}

.row {
    display: flex;
    justify-content: space-between;
    margin-bottom: 25px;
}

.col-md-6 {
    width: 48%;
}

.list-group {
    list-style-type: none;
    padding: 0;
    max-height: 300px;
    overflow-y: auto;
}

.list-group-item {
    background-color: #f9f9f9;
    padding: 10px 15px;
    margin-bottom: 10px;
    border: 1px solid #ddd;
    border-radius: 4px;
}

/* Styling cho phần doanh thu */
p {
    font-size: 18px;
    margin-bottom: 10px;
}



/* Khoảng cách giữa các nhóm */
.my-4 {
    
    margin-bottom: 2rem;
}

/* Responsive - cho màn hình nhỏ hơn */
@media (max-width: 768px) {
    .row {
        flex-direction: column;
    }
    
    .col-md-6 {
        width: 100%;
    }
}
    </style>
</head>
<body style="font-family: Arial, sans-serif ; margin: 0;">
    

<header class="admin-header">
    <div class="header-container">
        <h1 class="admin-title" style="color: #3399ff;">Bảng Điều Khiển Admin</h1>
        <ul class="user-actions">
            <?php if ($loggedIn): ?>
                
                <li><a href="logout.php"><i class="fa fa-user"></i> <?php echo $_SESSION['admin_username'];; ?></a></li>
            <?php else: ?>
                <li><a href="loginADMIN.php" class="dangnhap">Đăng Nhập</a></li>
            <?php endif; ?>
        </ul>
    </div>
</header>

<nav>
    <div class="tabs">
        <a href="trangchuadmin.php" class="tab-button" style="background-color: #858382;"><i class="fa fa-home"></i> Trang chủ</a>   
        
        <!-- Sử dụng in_array để kiểm tra quyền trong mảng -->
        <?php if (in_array('sanpham', $_SESSION['quyen'])): ?>
            <a href="Nhập_SP.php" class="tab-button"><i class="fa fa-product-hunt"></i> Sản phẩm</a>
        <?php endif; ?>

        <?php if (in_array('danhmuc', $_SESSION['quyen'])): ?>
            <a href="Nhập_DM.php" class="tab-button"><i class="fa fa-list"></i> Danh mục</a>
        <?php endif; ?>

        <?php if (in_array('banner', $_SESSION['quyen'])): ?>
            <a href="Nhập_Banner.php" class="tab-button"><i class="fa fa-image"></i> Banner</a>
        <?php endif; ?>

        <?php if (in_array('taikhoan', $_SESSION['quyen'])): ?>
            <a href="qltaikhaon.php" class="tab-button"><i class="fa fa-user"></i> Tài khoản</a>
        <?php endif; ?>

        <?php if (in_array('donhang', $_SESSION['quyen'])): ?>
            <a href="quanlydonhang.php" class="tab-button"><i class="fa fa-credit-card"></i> Đơn hàng</a>
        <?php endif; ?>

        <?php if (in_array('hoadon', $_SESSION['quyen'])): ?>
            <a href="xemhoadon.php" class="tab-button"><i class="fa fa-clipboard-list"></i> Hóa đơn</a>
        <?php endif; ?>
        <?php if (in_array('nhanvien', $_SESSION['quyen'])): ?>
            <a href="qlnhanvien.php" class="tab-button"><i class="fa fa-user-tie"></i> Nhân viên</a>
        <?php endif; ?>
    </div>
</nav>
<body>
<div class="container">
    

    <div class="row">
        <div class="col-md-6">
            <h2>Sản phẩm bán chạy</h2>
            <ul class="list-group">
                <?php while ($row = $bestSellingResult->fetch_assoc()) { ?>
                    <li class="list-group-item"><?php echo $row['Tenhang'] . " - " . $row['TotalSold'] . " sản phẩm"; ?></li>
                <?php } ?>
            </ul>
        </div>
        <div class="col-md-6">
            <h2>Sản phẩm bán chậm</h2>
            <ul class="list-group">
                <?php while ($row = $slowSellingResult->fetch_assoc()) { ?>
                    <li class="list-group-item"><?php echo $row['Tenhang'] . " - " . $row['TotalSold'] . " sản phẩm"; ?></li>
                <?php } ?>
            </ul>
        </div>
    </div>

    <div class="row my-4">
        <div class="col-md-6">
            <h2>Khách hàng mua nhiều</h2>
            <ul class="list-group">
                <?php while ($row = $frequentBuyersResult->fetch_assoc()) { ?>
                    <li class="list-group-item"><?php echo $row['Tenkhach'] . " - " . $row['TotalOrders'] . " đơn hàng"; ?></li>
                <?php } ?>
            </ul>
        </div>
        <div class="col-md-6">
            <h2>Doanh số & Doanh thu trong tháng</h2>
            <p>Tổng số đơn hàng: <?php echo $monthlySalesData['TotalSales']; ?></p>
            <p>Tổng doanh thu: <?php echo number_format( $quarterlySalesData['TotalRevenue']); ?> VND</p>
        </div>
    </div>

    <div class="row my-4">
        <div class="col-md-6">
            <h2>Doanh số & Doanh thu trong quý</h2>
            <p>Tổng số đơn hàng: <?php echo $quarterlySalesData['TotalSales']; ?></p>
            <p>Tổng doanh thu: <?php echo number_format( $quarterlySalesData['TotalRevenue']); ?> VND</p>
        </div>
        <div class="col-md-6">
            <h2>Doanh số & Doanh thu trong năm</h2>
            <p>Tổng số đơn hàng: <?php echo $yearlySalesData['TotalSales']; ?></p>
            <p>Tổng doanh thu: <?php echo number_format( $quarterlySalesData['TotalRevenue']); ?> VND</p>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
