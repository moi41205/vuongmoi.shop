<?php
session_start();
$loggedIn = isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'];

// Kiểm tra quyền truy cập trang quản lý sản phẩm
$quyen = isset($_SESSION['quyen']) ? $_SESSION['quyen'] : [];  // Lấy quyền từ session, mặc định là mảng trống nếu không có
if (!in_array('sanpham', $quyen)) {
    echo "Bạn không có quyền truy cập trang này.";
    header("Location: loginADMIN.php");
    exit;
}
include '../db_connect.php';

if (!$con) {
    die("Kết nối thất bại: " . mysqli_connect_error());
}

// Khởi tạo các biến với giá trị mặc định
$Mahang = $Tenhang = $Donvido = $Mota = $Maloaihang = $Soluongton = $Dongia = $anh = $thongso = $baohanh = $giagoc = $voicher = '';
$isEditing = false;

// Kiểm tra hành động chỉnh sửa sản phẩm
if (isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_GET['Mahang'])) {
    $Mahang = $_GET['Mahang'];
    $isEditing = true;

    $sql_edit = "SELECT h.*, ct.Thongso, ct.baohanh, ct.giagoc, ct.voicher FROM hang h LEFT JOIN chitiet_sanpham ct ON h.Mahang = ct.Mahang WHERE h.Mahang = ?";
    $stmt_edit = mysqli_prepare($con, $sql_edit);
    mysqli_stmt_bind_param($stmt_edit, "s", $Mahang);
    mysqli_stmt_execute($stmt_edit);
    $result_edit = mysqli_stmt_get_result($stmt_edit);
    $product = mysqli_fetch_assoc($result_edit);

    if ($product) {
        $Tenhang = $product['Tenhang'];
        $Donvido = $product['Donvido'];
        $Mota = $product['Mota'];
        $Maloaihang = $product['Maloaihang'];
        $Soluongton = $product['Soluongton'];
        $Dongia = $product['Dongia'];
        $anh = $product['anh'];
        $thongso = $product['Thongso'];
        $baohanh = $product['baohanh'];
        $giagoc = $product['giagoc'];
        $voicher = $product['voicher'];
    }
}

// Kiểm tra hành động xóa sản phẩm
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['Mahang'])) {
    $Mahang = $_GET['Mahang'];

    // Bắt đầu giao dịch để đảm bảo an toàn
    mysqli_begin_transaction($con);

    try {
        // Xóa chi tiết sản phẩm trước
        $sql_delete_details = "DELETE FROM chitiet_sanpham WHERE Mahang = ?";
        $stmt_delete_details = mysqli_prepare($con, $sql_delete_details);
        mysqli_stmt_bind_param($stmt_delete_details, "s", $Mahang);
        mysqli_stmt_execute($stmt_delete_details);

        // Kiểm tra lỗi khi xóa chi tiết sản phẩm
        if (mysqli_stmt_affected_rows($stmt_delete_details) === -1) {
            throw new Exception("Lỗi khi xóa chi tiết sản phẩm: " . mysqli_stmt_error($stmt_delete_details));
        }

        // Xóa sản phẩm chính
        $sql_delete = "DELETE FROM hang WHERE Mahang = ?";
        $stmt_delete = mysqli_prepare($con, $sql_delete);
        mysqli_stmt_bind_param($stmt_delete, "s", $Mahang);
        mysqli_stmt_execute($stmt_delete);

        // Kiểm tra lỗi khi xóa sản phẩm
        if (mysqli_stmt_affected_rows($stmt_delete) === -1) {
            throw new Exception("Lỗi khi xóa sản phẩm: " . mysqli_stmt_error($stmt_delete));
        }

        // Hoàn tất giao dịch nếu không có lỗi
        mysqli_commit($con);

        // Thông báo xóa thành công
        $_SESSION['thongbao'] = array(
            'type' => 'success',
            'message' => "Sản phẩm và chi tiết sản phẩm đã được xóa thành công!"
        );
    } catch (Exception $e) {
        // Hủy giao dịch nếu có lỗi
        mysqli_rollback($con);

        // Thông báo lỗi
        $_SESSION['thongbao'] = array(
            'type' => 'error',
            'message' => $e->getMessage()
        );
    }

    // Quay lại trang nhập sản phẩm
    header("Location: Nhập_SP.php");
    exit();
}



?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý Sản Phẩm</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/nhap_sp.css">
    <style>

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
        <a href="trangchuadmin.php" class="tab-button"><i class="fa fa-home"></i> Trang chủ</a>   
        <!-- Sử dụng in_array để kiểm tra quyền trong mảng -->
        <?php if (in_array('sanpham', $_SESSION['quyen'])): ?>
            <a href="Nhập_SP.php" class="tab-button" style="background-color: #858382 ;"><i class="fa fa-product-hunt"></i> Sản phẩm</a>
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
</body>
<main>
<?php
if (isset($_SESSION['thongbao'])) {
    echo "<p id='thongbao' class='" . $_SESSION['thongbao']['type'] . "'>" . $_SESSION['thongbao']['message'] . "</p>";
    unset($_SESSION['thongbao']);
}   
?>
<div class="container">
    <!-- Form nhập liệu -->
    <form action="xu_ly_nhap_sp.php<?php echo $isEditing ? '?action=edit&Mahang='.$Mahang : ''; ?>" method="POST" enctype="multipart/form-data" class="product-form" style="
    width: 40%;
    
    margin: 0 auto 40px;
    background: #fff;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        display: flex;
    flex-direction: column;
">
        <h2>Quản lý sản phẩm</h2>

        <!--  -->
        
        <label for="Tenhang">Tên hàng:</label>
        <input type="text" name="Tenhang" id="Tenhang" value="<?php echo htmlspecialchars($Tenhang); ?>" required class="form-input">
        
        <label for="Donvido">Đơn vị đo:</label>
        <input type="text" name="Donvido" id="Donvido" value="<?php echo htmlspecialchars($Donvido); ?>" class="form-input">
        
        <label for="Mota">Mô tả:</label>
        <textarea name="Mota" id="Mota" class="form-input"><?php echo htmlspecialchars($Mota); ?></textarea>
        
        <label for="Maloaihang">Mã loại hàng:</label>
        <select name="Maloaihang" id="Maloaihang" class="form-input">
            <?php
            $sql_loaihang = "SELECT * FROM loaihang";
            $result_loaihang = mysqli_query($con, $sql_loaihang);
            while ($row = mysqli_fetch_assoc($result_loaihang)) {
                $selected = ($row['Maloaihang'] == $Maloaihang) ? 'selected' : '';
                echo "<option value='" . htmlspecialchars($row['Maloaihang']) . "' $selected>" . htmlspecialchars($row['Tenloaihang']) . "</option>";
            }
            ?>
        </select>
        
        <label for="Soluongton">Số lượng tồn:</label>
        <input type="number" name="Soluongton" id="Soluongton" value="<?php echo htmlspecialchars($Soluongton); ?>" required class="form-input">
        
        <label for="Dongia">Đơn giá:</label>
        <input type="number" step="0.01" name="Dongia" id="Dongia" value="<?php echo htmlspecialchars($Dongia); ?>" required class="form-input">
        
        <label for="anh">Ảnh sản phẩm:</label>
        <input type="file" name="anh" id="anh">
        <?php if ($anh) { ?>
            <img src="../img/sản_phẩm/<?php echo htmlspecialchars($anh); ?>" alt="Ảnh sản phẩm" class="product-image">
        <?php } ?>

        <label for="thongso">Chi tiết sản phẩm:</label>
        <textarea name="thongso" id="thongso" class="form-input"><?php echo htmlspecialchars($thongso); ?></textarea>

        <label for="baohanh">Bảo hành:</label>
        <input type="text" name="baohanh" id="baohanh" value="<?php echo htmlspecialchars($baohanh); ?>" class="form-input">

        <label for="giagoc">Giá gốc:</label>
        <input type="number" step="0.01" name="giagoc" id="giagoc" value="<?php echo htmlspecialchars($giagoc); ?>" class="form-input">

        <input type="submit" value="<?php echo $isEditing ? 'Cập nhật sản phẩm' : 'Thêm sản phẩm'; ?>" class="form-submit">
    </form>

    <!-- Danh sách sản phẩm -->
    <div class="product-table">
        <table>
            <tr>
                <th>Mã hàng</th>
                <th>Tên hàng</th>
                <th>Đơn vị</th>
                <th>Mô tả</th>
                <th>Số lượng tồn</th>
                <th>Đơn giá</th>
                <th>Ảnh</th>
                <th>Hành động</th>
            </tr>
            <?php
            $sql = "SELECT * FROM hang";
            $result = mysqli_query($con, $sql);
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<tr>
                        <td>" . htmlspecialchars($row['Mahang']) . "</td>
                        <td>" . htmlspecialchars($row['Tenhang']) . "</td>
                        <td>" . htmlspecialchars($row['Donvido']) . "</td>
                        <td>" . htmlspecialchars($row['Mota']) . "</td>
                        <td>" . htmlspecialchars($row['Soluongton']) . "</td>
                        <td>" . htmlspecialchars(number_format($row['Dongia'])) . "</td>
                        <td><img src='../img/sản_phẩm/" . htmlspecialchars($row['anh']) . "' class='product-image'></td>
                        <td>
                            <a href='?action=edit&Mahang=" . htmlspecialchars($row['Mahang']) . "' class='edit-btn'>Sửa</a>
                            <a href='?action=delete&Mahang=" . htmlspecialchars($row['Mahang']) . "' class='delete-btn' onclick='return confirm(\"Bạn có chắc chắn muốn xóa sản phẩm này không?\")'>Xóa</a>
                        </td>
                    </tr>";
            }
            ?>
        </table>
    </div>
</div>

</main>
<script>
    // Tự động ẩn thông báo sau 5 giây (5000 milliseconds)
    setTimeout(function() {
        var messageDiv = document.getElementById("thongbao");
        if (messageDiv) {
            messageDiv.style.display = "none";
        }
    }, 3000); // 5 giây
</script>
</html>
