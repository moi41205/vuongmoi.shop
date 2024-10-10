<?php
// qlnhanvien.php

session_start();
$loggedIn = isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'];

// Kiểm tra quyền truy cập trang quản lý nhân viên
$quyen = isset($_SESSION['quyen']) ? $_SESSION['quyen'] : [];  // Lấy quyền từ session, mặc định là mảng trống nếu không có
if (!in_array('nhanvien', $quyen)) {
    echo "Bạn không có quyền truy cập trang này.";
    header("Location: loginADMIN.php");
    exit;
}
include '../db_connect.php';

// Xử lý phần chỉnh sửa
$editMode = false;
$editID = null;
$editData = null;

if (isset($_GET['id'])) {
    $editMode = true;
    $editID = $_GET['id'];
    
    // Lấy thông tin tài khoản cần chỉnh sửa
    $sql = "SELECT * FROM admin WHERE id = ?";
    $stmt = $con->prepare($sql);
    if ($stmt) {
        $stmt->bind_param("i", $editID);
        $stmt->execute();
        $result_edit = $stmt->get_result();
        $editData = $result_edit->fetch_assoc();
    } else {
        echo "Lỗi khi chuẩn bị câu truy vấn: " . $con->error;
    }
}

// Xóa tài khoản nhân viên
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $delete_sql = "DELETE FROM admin WHERE id = ?";
    $stmt = $con->prepare($delete_sql);
    if ($stmt) {
        $stmt->bind_param("i", $delete_id);
        $stmt->execute();
        $_SESSION['success'] = "Xóa tài khoản thành công.";
    } else {
        $_SESSION['error'] = "Lỗi khi chuẩn bị câu truy vấn: " . $con->error;
    }
    header("Location: qlnhanvien.php");
    exit();
}

// Thêm hoặc cập nhật tài khoản nhân viên
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $tendangnhap = $_POST['tendangnhap'];
    $quyen = $_POST['quyen'];
    $ten_taikhoan = $_POST['ten_taikhoan'];

    // Nếu đang ở chế độ chỉnh sửa
    if (isset($_POST['id']) && $_POST['id'] != "") {
        $id = $_POST['id'];
        $matkhau = isset($_POST['matkhau']) && !empty($_POST['matkhau']) ? $_POST['matkhau'] : null;
        
        // Cập nhật thông tin tài khoản
        if ($matkhau) {
            // Cập nhật cả mật khẩu
            $sql_update = "UPDATE admin SET tendangnhap = ?, matkhau = ?, quyen = ?, ten_taikhoan = ? WHERE id = ?";
            $stmt = $con->prepare($sql_update);
            if ($stmt) {
                $stmt->bind_param("ssssi", $tendangnhap, $matkhau, $quyen, $ten_taikhoan, $id);
                if ($stmt->execute()) {
                    $_SESSION['success'] = "Cập nhật tài khoản thành công.";
                } else {
                    $_SESSION['error'] = "Cập nhật tài khoản thất bại: " . $stmt->error;
                }
            } else {
                $_SESSION['error'] = "Lỗi khi chuẩn bị câu truy vấn: " . $con->error;
            }
        } else {
            // Chỉ cập nhật thông tin tài khoản, không thay đổi mật khẩu
            $sql_update = "UPDATE admin SET tendangnhap = ?, quyen = ?, ten_taikhoan = ? WHERE id = ?";
            $stmt = $con->prepare($sql_update);
            if ($stmt) {
                $stmt->bind_param("sssi", $tendangnhap, $quyen, $ten_taikhoan, $id);
                if ($stmt->execute()) {
                    $_SESSION['success'] = "Cập nhật tài khoản thành công.";
                } else {
                    $_SESSION['error'] = "Cập nhật tài khoản thất bại: " . $stmt->error;
                }
            } else {
                $_SESSION['error'] = "Lỗi khi chuẩn bị câu truy vấn: " . $con->error;
            }
        }
    } else {
        // Thêm tài khoản mới
        $matkhau = $_POST['matkhau'];

        // Kiểm tra tên đăng nhập đã tồn tại hay chưa
        $check_sql = "SELECT * FROM admin WHERE tendangnhap = ?";
        $stmt = $con->prepare($check_sql);
        if ($stmt) {
            $stmt->bind_param("s", $tendangnhap);
            $stmt->execute();
            $result_check = $stmt->get_result();

            if ($result_check->num_rows > 0) {
                $_SESSION['error'] = "Tên đăng nhập đã tồn tại.";
                header("Location: qlnhanvien.php");
                exit();
            }
        } else {
            $_SESSION['error'] = "Lỗi khi chuẩn bị câu truy vấn: " . $con->error;
            header("Location: qlnhanvien.php");
            exit();
        }

        // Thêm tài khoản mới
        $sql_insert = "INSERT INTO admin (tendangnhap, matkhau, quyen, ten_taikhoan) VALUES (?, ?, ?, ?)";
        $stmt = $con->prepare($sql_insert);
        if ($stmt) {
            $stmt->bind_param("ssss", $tendangnhap, $matkhau, $quyen, $ten_taikhoan);
            if ($stmt->execute()) {
                $_SESSION['success'] = "Thêm tài khoản thành công.";
            } else {
                $_SESSION['error'] = "Thêm tài khoản thất bại: " . $stmt->error;
            }
        } else {
            $_SESSION['error'] = "Lỗi khi chuẩn bị câu truy vấn: " . $con->error;
        }
    }

    header("Location: qlnhanvien.php");
    exit();
}

// Lấy danh sách tất cả nhân viên
$sql = "SELECT * FROM admin";
$result = $con->query($sql);
?>


<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý nhân viên</title>
    <link rel="stylesheet" href="css/qlnhanvien.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <header class="admin-header">
        <div class="header-container">
            <h1 class="admin-title" style="color: #3399ff;">Bảng Điều Khiển Admin</h1>
            <ul class="user-actions">
                <?php if ($loggedIn): ?>
                    <li><a href="logout.php"><i class="fa fa-user"></i> <?php echo $_SESSION['admin_username']; ?></a></li>
                <?php else: ?>
                    <li><a href="loginADMIN.php" class="dangnhap">Đăng Nhập</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </header>

    <nav>
        <div class="tabs">
            <a href="trangchuadmin.php" class="tab-button"><i class="fa fa-home"></i> Trang chủ</a>   
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
                <a href="qlnhanvien.php" class="tab-button" style="background-color: #858382;"><i class="fa fa-user-tie"></i> Nhân viên</a>
            <?php endif; ?>
        </div>
    </nav>

    <div class="container mt-5">
        <h1 style="text-align: center;">Quản lý nhân viên</h1>
        <?php
// Hiển thị thông báo nếu có trong session
if (isset($_SESSION['success'])) {
    echo '<div id="success" class="success">' . $_SESSION['success'] . '</div>';
    // Xóa thông báo sau khi hiển thị
    unset($_SESSION['success']);
}
?>
        <div class="employee-management">
            <div class="form-section">
                <div class="card mb-4">
                    <div class="card-header">
                        <?php echo $editMode ? 'Chỉnh sửa nhân viên' : 'Thêm nhân viên mới'; ?>
                    </div>
                    <div class="card-body">
                        <form action="qlnhanvien.php" method="POST">
                            <?php if ($editMode): ?>
                                <input type="hidden" name="id" value="<?php echo $editData['id']; ?>">
                            <?php endif; ?>
                            
                            <div class="form-group">
                                <label for="tendangnhap">Tên đăng nhập:</label>
                                <input type="text" name="tendangnhap" class="form-control" value="<?php echo $editMode ? $editData['tendangnhap'] : ''; ?>" required>
                            </div>
                            <?php if ($editMode): ?>
                                <!-- Chỉ hiển thị khi chỉnh sửa -->
                                <div class="form-group">
                                    <label for="matkhau">Mật khẩu mới (bỏ trống nếu không thay đổi):</label>
                                    <input type="password" name="matkhau" class="form-control">
                                </div>
                            <?php else: ?>
                                <!-- Thêm tài khoản mới -->
                                <div class="form-group">
                                    <label for="matkhau">Mật khẩu:</label>
                                    <input type="password" name="matkhau" class="form-control" required>
                                </div>
                            <?php endif; ?>
                            <div class="form-group">
                                <label for="quyen">Quyền (phân cách bởi dấu phẩy):</label>
                                <input type="text" name="quyen" class="form-control" value="<?php echo $editMode ? $editData['quyen'] : ''; ?>" placeholder="sanpham,danhmuc,taikhoan">
                            </div>
                            <div class="form-group">
                                <label for="ten_taikhoan">Tên nhân viên:</label>
                                <input type="text" name="ten_taikhoan" class="form-control" value="<?php echo $editMode ? $editData['ten_taikhoan'] : ''; ?>">
                            </div>
                            <button type="submit" class="btn btn-primary">
                                <?php echo $editMode ? 'Cập nhật' : 'Thêm'; ?>
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Table Section -->
            <div class="table-section">
                <!-- Bảng danh sách nhân viên -->
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Tên đăng nhập</th>
                            <th>Mật khẩu</th>
                            <th>Quyền</th>
                            <th>Tên nhân viên</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $row['id']; ?></td>
                                <td><?php echo $row['tendangnhap']; ?></td>
                                <td>
                                    <div class="input-group">
                                        <input type="password" id="password_<?php echo $row['id']; ?>" value="<?php echo $row['matkhau']; ?>" class="form-control" style="max-width: 50%" readonly>
                                        <div class="input-group-append">
                                            <!-- Icon con mắt -->
                                            <span class="input-group-text" onclick="togglePassword(<?php echo $row['id']; ?>)" style="cursor: pointer;">
                                                <i id="eyeIcon_<?php echo $row['id']; ?>" class="fas fa-eye"></i>
                                            </span>
                                        </div>
                                    </div>
                                </td>
                                <td><?php echo $row['quyen']; ?></td>
                                <td><?php echo $row['ten_taikhoan']; ?></td>
                                <td>
                                    <a href="qlnhanvien.php?id=<?php echo $row['id']; ?>" class="btn btn-warning btn-sm">Sửa</a> |
                                    <a href="qlnhanvien.php?delete_id=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Bạn có chắc chắn muốn xóa?');">Xóa</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        function togglePassword(id) {
            var passwordField = document.getElementById("password_" + id);
            var eyeIcon = document.getElementById("eyeIcon_" + id);

            if (passwordField.type === "password") {
                passwordField.type = "text"; // Hiển thị mật khẩu
                eyeIcon.classList.remove("fa-eye"); // Thay đổi biểu tượng con mắt
                eyeIcon.classList.add("fa-eye-slash"); // Biểu tượng con mắt bị gạch
            } else {
                passwordField.type = "password"; // Ẩn mật khẩu
                eyeIcon.classList.remove("fa-eye-slash"); // Thay đổi lại biểu tượng con mắt
                eyeIcon.classList.add("fa-eye"); // Biểu tượng con mắt bình thường
            }
        }
    </script>
            <script>
    // Tự động ẩn thông báo sau 3 giây (3000 milliseconds)
    setTimeout(function() {
        var successDiv = document.getElementById("success");
        var successText = document.getElementById("error");
        if (successDiv) {
            successDiv.style.display = "none";
        }
    }, 3000); // 3 giây
</script>
</body>

