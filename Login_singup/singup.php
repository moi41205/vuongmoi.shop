<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng ký</title>
    <link rel="stylesheet" href="../css/login.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css" integrity="anonymous" />
    <style>
        /* CSS cho thông báo */
        .alert {
            display: none; /* Ẩn thông báo mặc định */
           
            padding: 15px;
            color: white;
            border-radius: 5px;
            text-align: center;
        }
        .dangky {
            background-image: linear-gradient(120deg, #00aaff85 0%, #00aaff85 100%);
            background-size: 200% 200%;
            width: 100%;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            transition: all 0.3s ease;
        }
        .dangky:hover {
            background-position: 100% 0;
            box-shadow: 0 0 10px #00aaff85;
        }
        h2 {
            margin-bottom: 10px !important;
    margin-top: 10px !important;

        }
    </style>
</head>
<body background="../img/login/backro.png">
    <div class="auth-wrapper">
        <div class="auth-container">
            <div class="auth-action-left">
                <div class="auth-form-outer">
                    <h2 class="auth-form-title">Tạo tài khoản</h2>

                    <!-- Bắt đầu session -->
                    <?php
                    session_start();
                    if (isset($_SESSION['thongbao'])):
                        $thongbao = $_SESSION['thongbao'];
                        unset($_SESSION['thongbao']); // Xóa thông báo sau khi hiển thị
                    ?>
                        <div class="alert <?php echo strpos($thongbao, 'thành công') !== false ? 'success' : ''; ?>" id="notification">
                            <?php echo $thongbao; ?>
                        </div>
                        <?php if (strpos($thongbao, 'thành công') !== false): ?>
        <audio id="successAudio" autoplay>
            <source src="../img/tc.mp3" type="audio/mpeg">
        </audio>
    <?php endif; ?>
                    <?php endif; ?>

                    <!-- Form đăng ký -->
                    <form class="login-form" method="POST" action="logic_singup.php">
                        <input type="text" name="ten" class="auth-form-input" placeholder="Tên" required>
                        <input type="text" name="username" class="auth-form-input" placeholder="Tên đăng nhập" required>
                        
                        <!-- New input fields for Dienthoai and Diachi -->
                        <input type="text" name="dienthoai" class="auth-form-input" placeholder="Số điện thoại" required>
                        <input type="text" name="diachi" class="auth-form-input" placeholder="Địa chỉ" required>

                        <div class="input-icon">
                            <input type="password" name="matkhau" class="auth-form-input" placeholder="Mật Khẩu" required>
                            <i class="fa fa-eye show-password"></i>
                        </div>
                        <input type="password" name="matkhau_xacnhan" class="auth-form-input" placeholder="Nhập lại mật Khẩu" required>

                        <div class="footer-h">
                            <button type="submit" class="dangky">Đăng ký</button>
                        </div>
                    </form>

                    <p class="p">Đã có tài khoản? <a href="login.php">Đăng nhập</a></p>
                </div>
            </div>
            <div class="auth-action-right">
                <div class="auth-image">
                    <img src="../img/login/login.jpg" alt="login">
                </div>
            </div>
        </div>
    </div>
    <audio id="myAudio" autoplay>
  <source src="../img/dk.mp3" type="audio/mpeg">
</audio>
    <script>
        // Hiển thị thông báo nếu có
        <?php if (isset($thongbao)): ?>
            document.getElementById("notification").style.display = "block";
        <?php endif; ?>
    </script>
    <script>
        window.addEventListener("load", function () {
            const loginForm = document.querySelector(".login-form");
            
            // Xử lý việc hiển thị/ẩn mật khẩu
            const showPasswordIcon = loginForm && loginForm.querySelector(".show-password");
            const inputPassword = loginForm && loginForm.querySelector('input[type="password"]');
            
            if (showPasswordIcon) {
                showPasswordIcon.addEventListener("click", function () {
                    const inputPasswordType = inputPassword.getAttribute("type");
                    inputPasswordType === "password"
                        ? inputPassword.setAttribute("type", "text")
                        : inputPassword.setAttribute("type", "password");
                });
            }
                        // Nếu có thông báo thành công, phát tc.mp3 và dừng dk.mp3
                        const notification = document.getElementById("notification");
            if (notification && notification.classList.contains('success')) {
                const audioSuccess = document.getElementById("successAudio");
                const audioDk = document.getElementById("myAudio");
                
                if (audioSuccess) {
                    audioDk.pause(); // Dừng âm thanh dk.mp3
                    audioSuccess.play(); // Phát tc.mp3
                }
            }
        });
    </script>


</body>

</html>
