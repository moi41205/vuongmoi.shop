<?php
// Bắt đầu session
session_start();

// Kết nối cơ sở dữ liệu
include '../db.php';

// Kiểm tra xem phương thức yêu cầu có phải là POST không
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $ten = $_POST['ten']; 
    $username = $_POST['username']; 
    $matkhau = $_POST['matkhau']; 
    $matkhau_xacnhan = $_POST['matkhau_xacnhan'];

    // Lấy thêm dữ liệu từ các ô mới
    $dienthoai = $_POST['dienthoai'];
    $diachi = $_POST['diachi'];

    $thongbao = ""; // Biến để lưu thông báo

    // Kiểm tra nếu mật khẩu và nhập lại mật khẩu khớp nhau
    if ($matkhau != $matkhau_xacnhan) {
        $thongbao = "Mật khẩu không khớp!";
    } else {
        // Kiểm tra xem tên đăng nhập đã tồn tại chưa
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user) {
            $thongbao = "Tên đăng nhập đã tồn tại!";
        } else {
            try {
                // Bắt đầu một giao dịch (transaction)
                $pdo->beginTransaction();

                // Thêm tài khoản mới vào bảng users
                $sql = "INSERT INTO users (Tên, username, password, `Ngày tạo tk`) VALUES (?, ?, ?, CURDATE())";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$ten, $username, $matkhau]);

                // Lấy ID của người dùng vừa được thêm vào
                $user_id = $pdo->lastInsertId();

                // Thêm bản ghi tương ứng vào bảng khach với thông tin mới
                $sql_khach = "INSERT INTO khach (id, Tenkhach, Dienthoai, Diachi) VALUES (?, ?, ?, ?)";
                $stmt_khach = $pdo->prepare($sql_khach);
                $stmt_khach->execute([$user_id, $ten, $dienthoai, $diachi]);

                // Xác nhận giao dịch thành công
                $pdo->commit();

                $thongbao = "Đăng ký thành công!";
            } catch (Exception $e) {
                // Nếu có lỗi, hủy bỏ giao dịch
                $pdo->rollBack();
                $thongbao = "Đã xảy ra lỗi trong quá trình đăng ký: " . $e->getMessage();
            }
        }
    }

    // Gửi thông báo trở lại trang đăng ký
    $_SESSION['thongbao'] = $thongbao;

    // Chuyển hướng lại file đăng ký
    header("Location: singup.php");
    exit();
}
?>
