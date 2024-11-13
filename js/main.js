// Ngăn để không ẩn bộ lọc
document.addEventListener('DOMContentLoaded', function () {
    const form = document.querySelector('.advanced-search');
    const searchParams = new URLSearchParams(window.location.search);
    
    // Kiểm tra nếu có action trong URL
    const hasAction = searchParams.has('action');

    // Kiểm tra nếu có bất kỳ tham số nào trong URL không phải là trống và không phải là tham số phân trang
    const hasNonPagingParams = Array.from(searchParams.keys()).some(key => {
        const value = searchParams.get(key).trim();
        return value !== '' && key !== 'per_page' && key !== 'page';
    });

    // Mở form nếu có tham số khác phân trang và không có action
    if (hasNonPagingParams && !hasAction) {
        form.classList.add('open');
    }

    // Đảm bảo nút đóng hoạt động
    document.querySelector('.advanced-search-control button[onclick*="remove"]').addEventListener('click', function () {
        form.classList.remove('open');
    });
});

function keepAdvancedSearchOpen(event) {
    // Giữ form mở khi thực hiện submit
    document.querySelector('.advanced-search').classList.add('open');
    return true;
}



// Pop-up detail.php
document.addEventListener('DOMContentLoaded', function () {
    const productLinks = document.querySelectorAll('.card-image-link, .card-title-link, .product-buy, .header-middle-right-item open');
    const modalContainer = document.createElement('div');
    modalContainer.id = 'popup-container';
    modalContainer.classList.add('modal'); // Thêm class modal cho container
    document.body.appendChild(modalContainer);

    // Hàm kiểm tra đăng nhập và thêm sản phẩm vào giỏ hàng
    window.handleAddToCart = function(isLoggedIn) {
        if (isLoggedIn) {
            // Nếu người dùng đã đăng nhập, gửi biểu mẫu
            const addToCartForm = document.getElementById('add-to-cart-form');
            if (addToCartForm) {
                addToCartForm.submit();
            }
        } else {
            // Nếu chưa đăng nhập, hiển thị thông báo yêu cầu đăng nhập
            toast({ title: 'Warning', message: 'Chưa đăng nhập tài khoản !', type: 'warning', duration: 3000 });
        }
    };

    productLinks.forEach(link => {
        link.addEventListener('click', function (event) {
            event.preventDefault(); // Ngăn không cho chuyển trang

            let productID;
            if (this.classList.contains('product-buy')) {
                productID = this.getAttribute('data-product-id');
            } else {
                productID = new URL(this.href).searchParams.get('ProductID');
            }

            // Gọi Ajax để tải chi tiết sản phẩm
            fetch(`detail.php?ProductID=${productID}`)
                .then(response => response.text())
                .then(html => {
                    modalContainer.innerHTML = html;
                    modalContainer.classList.add('open'); // Mở modal

                    // Thêm sự kiện đóng popup
                    const closeButton = modalContainer.querySelector('.close-popup');
                    if (closeButton) {
                        closeButton.addEventListener('click', function () {
                            closeModal();
                        });
                    }
                    // Gọi hàm để gán sự kiện cho nút cộng và trừ
                    setupQuantityButtons();
                })
                .catch(error => {
                    console.error('Lỗi khi tải popup chi tiết:', error);
                });
        });
    });

    // Đóng popup khi nhấn vào khoảng trống bên ngoài modal
    document.addEventListener('click', function (event) {
        if (modalContainer.classList.contains('open') && !event.target.closest('.modal-container')) {
            closeModal();
        }
    });

    function closeModal() {
        modalContainer.classList.remove('open'); // Đóng modal
        modalContainer.innerHTML = ''; // Xóa nội dung để giải phóng bộ nhớ
    }
    function setupQuantityButtons() {
        const buttonsAdded = modalContainer.querySelectorAll('.buttons_added');
        buttonsAdded.forEach(buttons => {
            const minusButton = buttons.querySelector('.minus');
            const plusButton = buttons.querySelector('.plus');
            const inputQty = buttons.querySelector('.input-qty');

            // Sự kiện cho nút trừ
            if (minusButton) {
                minusButton.addEventListener('click', function () {
                    let currentValue = parseInt(inputQty.value);
                    if (currentValue > parseInt(inputQty.min)) {
                        inputQty.value = currentValue - 1;
                    }
                });
            }

            // Sự kiện cho nút cộng
            if (plusButton) {
                plusButton.addEventListener('click', function () {
                    let currentValue = parseInt(inputQty.value);
                    if (currentValue < parseInt(inputQty.max)) {
                        inputQty.value = currentValue + 1;
                    }
                });
            }
        });
    }
});


// Tìm tất cả các nút cộng và trừ trong tài liệu
document.querySelectorAll('.buttons_added').forEach(buttons => {
    const minusButton = buttons.querySelector('.minus');
    const plusButton = buttons.querySelector('.plus');
    const inputQty = buttons.querySelector('.input-qty');

    // Sự kiện cho nút trừ
    minusButton.addEventListener('click', function () {
        let currentValue = parseInt(inputQty.value);
        if (currentValue > parseInt(inputQty.min)) {
            inputQty.value = currentValue - 1;
        }
    });

    // Sự kiện cho nút cộng
    plusButton.addEventListener('click', function () {
        let currentValue = parseInt(inputQty.value);
        if (currentValue < parseInt(inputQty.max)) {
            inputQty.value = currentValue + 1;
        }
    });
});
// Auto hide header on scroll
const headerNav = document.querySelector(".header-bottom");
let lastScrollY = window.scrollY;

window.addEventListener("scroll", () => {
    if(lastScrollY < window.scrollY) {
        headerNav.classList.add("hide")
    } else {
        headerNav.classList.remove("hide")
    }
    lastScrollY = window.scrollY;
})



// Open & Close Cart
function openCart() {

    document.querySelector('.modal-cart').classList.add('open');
    body.style.overflow = "hidden";
}

function closeCart() {
    document.querySelector('.modal-cart').classList.remove('open');
    body.style.overflow = "auto";

}



// Kiểm tra URL để xác định xem có action nào không
function checkURLForAction() {
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('action')) {
        openCart(); // Gọi hàm mở giỏ hàng nếu có action
    }
}

// Gọi hàm kiểm tra khi trang được tải
window.onload = checkURLForAction;

function uploadImage(input) {
    const preview = document.getElementById('image-preview');
    const file = input.files[0];

    if (file) {
        const reader = new FileReader();
        reader.onload = function (e) {
            preview.src = e.target.result;
        }
        reader.readAsDataURL(file);
    } else {
        preview.src = './assets/img/blank-image.png';
    }
}


