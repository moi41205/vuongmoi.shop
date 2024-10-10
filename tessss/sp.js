// Hàm thêm vào giỏ hàng
function addToCart(id, name, price, image) {
    let cart = JSON.parse(localStorage.getItem('cart')) || [];
    
    // Kiểm tra xem sản phẩm đã có trong giỏ chưa
    let existingProduct = cart.find(item => item.id === id);
    if (existingProduct) {
        existingProduct.quantity += 1; // Nếu có rồi thì tăng số lượng
    } else {
        // Nếu chưa có thì thêm mới vào giỏ
        cart.push({ id, name, price, image, quantity: 1 });
    }

    // Lưu lại giỏ hàng vào localStorage
    localStorage.setItem('cart', JSON.stringify(cart));
    alert('Sản phẩm đã được thêm vào giỏ hàng!');
}

// Hàm xử lý khi bấm nút "Mua ngay"
function orderNow(id, name, price, image) {
    const quantityInput = document.getElementById('quantity');
    const quantity = parseInt(quantityInput.value) || 1; // Lấy số lượng từ input
    
    let cart = JSON.parse(localStorage.getItem('cart')) || [];
    
    // Kiểm tra xem sản phẩm đã có trong giỏ chưa
    let existingProduct = cart.find(item => item.id === id);
    if (existingProduct) {
        existingProduct.quantity += quantity; // Tăng số lượng
    } else {
        // Nếu chưa có thì thêm mới vào giỏ
        cart.push({ id, name, price, image, quantity });
    }

    // Lưu lại giỏ hàng vào localStorage
    localStorage.setItem('cart', JSON.stringify(cart));
    
    // Chuyển đến trang thanh toán
    window.location.href = 'checkout.html'; 
}

// Hàm giảm số lượng
function decreaseQuantity() {
    let quantityInput = document.getElementById('quantity');
    let quantity = parseInt(quantityInput.value);
    if (quantity > 1) {
        quantityInput.value = quantity - 1;
    }
}

// Hàm tăng số lượng
function increaseQuantity() {
    let quantityInput = document.getElementById('quantity');
    let quantity = parseInt(quantityInput.value);
    quantityInput.value = quantity + 1;
}
