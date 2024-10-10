<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng Nhập</title>
    <link rel="stylesheet" href="css/stye.css">
</head>
<body>
    <div class="login-wrapper">
        <div class="login-container">
            <h2>Đăng Nhập</h2>
            <form id="login-form" method="POST">
                <div class="login-input-field">
                    <label for="username">Tên Đăng Nhập</label>
                    <input type="text" name="username" id="username" required>
                </div>
                <div class="login-input-field">
                    <label for="password">Mật Khẩu</label>
                    <input type="password" name="password" id="password" required>
                </div>
                <button type="submit" class="login-button">Đăng Nhập</button>
                <p id="error-message" style="color: red;"></p>

            </form>
            <p>Chưa có tài khoản? <a href="đăngký.php">đăng ký</a></p>
        </div>
    </div>

    <script>
        document.getElementById('login-form').addEventListener('submit', function(event) {
            event.preventDefault(); // Ngăn chặn hành vi mặc định của form

            var formData = new FormData(this);

            fetch('process_login.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Chuyển hướng đến trang tương ứng nếu đăng nhập thành công
                    window.location.href = data.redirect;
                } else {
                    // Hiển thị thông báo lỗi
                    document.getElementById('error-message').innerText = data.message;
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
        });
    </script>
</body>
</html>
