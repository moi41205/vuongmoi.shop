<?php
// Nhận thông tin sản phẩm từ URL (nếu có)
$mahang = isset($_GET['mahang']) ? $_GET['mahang'] : '';
$tenhang = isset($_GET['tenhang']) ? $_GET['tenhang'] : '';
$dongia = isset($_GET['dongia']) ? $_GET['dongia'] : 0;
$soluong = isset($_GET['soluong']) ? $_GET['soluong'] : 1;
$anh = isset($_GET['anh']) ? $_GET['anh'] : ''; // Nhận đường dẫn ảnh
$tongtien = $dongia * $soluong;

// Nếu có thông tin sản phẩm, thêm vào giỏ hàng
if ($mahang && $tenhang && $dongia) {
    echo "
    <script>
        let cart = JSON.parse(localStorage.getItem('cart')) || [];
        let found = false;

        cart.forEach(function(item) {
            if (item.mahang === '$mahang') {
                item.quantity += $soluong; // Tăng số lượng nếu sản phẩm đã có trong giỏ hàng
                found = true;
            }
        });

        if (!found) {
            cart.push({
                mahang: '$mahang',
                name: '$tenhang',
                price: $dongia,
                quantity: $soluong,
                image: '$anh' // Cập nhật đường dẫn ảnh
            });
        }

        localStorage.setItem('cart', JSON.stringify(cart));
    </script>";
}
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giỏ Hàng</title>
    <link rel="stylesheet" href="../css/giohang.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    
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
        <a href="../trangchuxong.php">Đăng xuất</a>
        
    </div>
</nav>

    <!-- Link tới Font Awesome cho biểu tượng -->
    

    <div class="container">
        <h1>Giỏ Hàng</h1>
        <div class="cart-header">
            <input type="checkbox" id="select-all" onclick="toggleSelectAll()" /> 
            <span>Sản Phẩm</span>
            <span>Đơn Giá</span>
            <span>Số Lượng</span>
            <span>Số Tiền</span>
            <span>Thao Tác</span>
        </div>
        <div id="cart-items">
            <!-- Các sản phẩm trong giỏ hàng sẽ được hiển thị ở đây -->
            <p>Giỏ hàng của bạn hiện tại trống.</p>
        </div>
        <div class="remove-selected">
            <button onclick="removeSelected()" >Xóa tất cả những sản phẩm đã tích</button>
            <div id="cart-total" class="cart-total">
                <p>Tổng tiền: 0₫</p>
            </div>
        </div>
        <button class="checkout-btn" onclick="checkout()">Thanh Toán</button>
    </div>

    <script>
        // Hàm định dạng số tiền
        function formatCurrency(value) {
    // Kiểm tra giá trị trước khi gọi toFixed()
    if (typeof value === 'undefined' || value === null) {
        return '0'; // Hoặc giá trị mặc định khác
    }
    return value.toFixed(0).replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}
        // Hàm tải giỏ hàng từ localStorage và hiển thị các sản phẩm
        function loadCart() {
            const cartItemsDiv = document.getElementById('cart-items');
            let cart = JSON.parse(localStorage.getItem('cart')) || [];
            let total = 0;

            // Kiểm tra xem giỏ hàng có sản phẩm nào không
            if (cart.length === 0) {
                cartItemsDiv.innerHTML = "<p>Giỏ hàng của bạn hiện tại trống.</p>";
            } else {
                cartItemsDiv.innerHTML = ""; // Xóa nội dung trước đó
                cart.forEach((item, index) => {
                    // Tính tổng tiền cho từng sản phẩm
                    const itemTotal = item.price * item.quantity;

                    // Thêm sản phẩm vào phần hiển thị giỏ hàng với hình ảnh
                    cartItemsDiv.innerHTML += `
    <div class="cart-container">
        <div class="cart-item">
            <input type="checkbox" class="select-item" onchange="updateTotal()">
            <div class="cart-item-info">
                <img src="../${item.image}" alt="${item.name}">
                <span class="cart-item-name">${item.name}</span>
            </div>
            <span class="cart-item-price">${formatCurrency(item.price)}</span>
            <div class="cart-controls">
                <button onclick="decrementQuantity(${index})">-</button>
                <input type="text" value="${item.quantity}" min="1" id="quantity-${index}" onchange="updateQuantity(${index}, this.value)">
                <button onclick="incrementQuantity(${index})">+</button>
            </div>
            <span class="cart-item-total">${formatCurrency(itemTotal)}</span>
            <button onclick="removeItem(${index})" class="cart-remove-btn">Xóa</button>
        </div>
    </div>
`;
                });
            }

            // Cập nhật tổng tiền
            updateTotal();
        }


       
function updateQuantity(index, newQuantity) {
    let cart = JSON.parse(localStorage.getItem('cart')) || [];
    
    // Chuyển đổi giá trị mới sang số nguyên, nếu giá trị là null, trống hoặc không hợp lệ, đặt thành 1
    newQuantity = parseInt(newQuantity, 10);
    
    if (isNaN(newQuantity) || newQuantity < 1) {
        newQuantity = 1;
    }

    // Cập nhật số lượng trong giỏ hàng
    cart[index].quantity = newQuantity;
    
    // Lưu giỏ hàng lại vào localStorage
    localStorage.setItem('cart', JSON.stringify(cart));
    
    // Cập nhật lại hiển thị giỏ hàng
    loadCart();
}// Đợi 500ms sau khi người dùng ngừng nhập


        // Hàm cập nhật tổng tiền
        function updateTotal() {
            let cart = JSON.parse(localStorage.getItem('cart')) || [];
            let total = 0;
            const checkboxes = document.querySelectorAll('.select-item');
            checkboxes.forEach((checkbox, index) => {
                if (checkbox.checked) {
                    total += cart[index].price * cart[index].quantity;
                }
            });
            const cartTotalDiv = document.getElementById('cart-total');
            cartTotalDiv.innerHTML = `<p>Tổng tiền: ${formatCurrency(total)}</p>`;
        }

        // Hàm tăng số lượng
function incrementQuantity(index) {
    let cart = JSON.parse(localStorage.getItem('cart')) || [];
    cart[index].quantity++;
    localStorage.setItem('cart', JSON.stringify(cart));
    document.getElementById(`quantity-${index}`).value = cart[index].quantity;
    loadCart(); // Cập nhật lại giỏ hàng hiển thị
}

// Hàm giảm số lượng
function decrementQuantity(index) {
    let cart = JSON.parse(localStorage.getItem('cart')) || [];
    if (cart[index].quantity > 1) {
        cart[index].quantity--;
    }
    localStorage.setItem('cart', JSON.stringify(cart));
    document.getElementById(`quantity-${index}`).value = cart[index].quantity;
    loadCart(); // Cập nhật lại giỏ hàng hiển thị
}

// Hàm cập nhật số lượng khi người dùng nhập trực tiếp
function updateQuantity(index, newQuantity) {
    let cart = JSON.parse(localStorage.getItem('cart')) || [];
    newQuantity = parseInt(newQuantity, 10);

    if (newQuantity > 0) {
        cart[index].quantity = newQuantity;
    } else {
        cart.splice(index, 1); // Xóa sản phẩm nếu số lượng là 0
    }

    // Cập nhật lại localStorage
    localStorage.setItem('cart', JSON.stringify(cart));
    loadCart(); // Cập nhật lại giỏ hàng hiển thị
}

        // Hàm xóa sản phẩm khỏi giỏ hàng
        function removeItem(index) {
            let cart = JSON.parse(localStorage.getItem('cart')) || [];
            cart.splice(index, 1);

            // Cập nhật lại localStorage
            localStorage.setItem('cart', JSON.stringify(cart));
            loadCart(); // Cập nhật lại giỏ hàng hiển thị
        }

        // Hàm xóa tất cả những sản phẩm đã tích
        function removeSelected() {
            let cart = JSON.parse(localStorage.getItem('cart')) || [];
            const checkboxes = document.querySelectorAll('.select-item');
            for (let i = checkboxes.length - 1; i >= 0; i--) {
                if (checkboxes[i].checked) {
                    cart.splice(i, 1);
                }
            }

            // Cập nhật lại localStorage
            localStorage.setItem('cart', JSON.stringify(cart));
            loadCart(); // Cập nhật lại giỏ hàng hiển thị
        }

        // Hàm chọn tất cả các sản phẩm
        function toggleSelectAll() {
            const selectAllCheckbox = document.getElementById('select-all');
            const checkboxes = document.querySelectorAll('.select-item');
            checkboxes.forEach(checkbox => {
                checkbox.checked = selectAllCheckbox.checked;
            });
            updateTotal();
        }

        // Gọi hàm loadCart khi trang được tải
        window.onload = loadCart;

        // Hàm kiểm tra đơn hàng
        function checkout() {
            alert("Bạn đã thanh toán thành công!");
        }
        
    </script>
</body>

</html>