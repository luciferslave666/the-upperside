<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Menu - KafeAnda</title>
    
    <script type="text/javascript"
            src="{{ config('midtrans.is_production') ? 'https://app.midtrans.com/snap/snap.js' : 'https://app.sandbox.midtrans.com/snap/snap.js' }}"
            data-client-key="{{ config('midtrans.client_key') }}"></script>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { 
            font-family: 'Inter', sans-serif;
            -webkit-tap-highlight-color: transparent;
        }
        
        html {
            scroll-behavior: smooth;
        }
        
        .product-card {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        
        @media (hover: hover) {
            .product-card:active {
                transform: scale(0.98);
            }
        }
        
        .header-scrolled {
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        
        @keyframes slideInDown {
            from {
                transform: translateY(-100px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }
        
        @keyframes slideOutUp {
            from {
                transform: translateY(0);
                opacity: 1;
            }
            to {
                transform: translateY(-100px);
                opacity: 0;
            }
        }
        
        @keyframes bounce {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.2); }
        }
        
        @keyframes slideUp {
            from {
                transform: translateY(100%);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }
        
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        
        .ajax-notification {
            animation: slideInDown 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55);
        }
        
        .cart-badge-bounce {
            animation: bounce 0.5s ease;
        }
        
        .floating-cart {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 50;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
        }
        
        .category-nav {
            scrollbar-width: none;
            -ms-overflow-style: none;
        }
        .category-nav::-webkit-scrollbar {
            display: none;
        }
        
        /* Cart Modal Styles */
        .cart-modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(4px);
            z-index: 100;
            display: none;
            animation: fadeIn 0.3s ease;
        }
        
        .cart-modal-overlay.active {
            display: flex;
            align-items: flex-end;
            justify-content: center;
        }
        
        @media (min-width: 640px) {
            .cart-modal-overlay.active {
                align-items: center;
                padding: 1rem;
            }
        }
        
        .cart-modal {
            background: white;
            width: 100%;
            max-width: 42rem;
            max-height: 90vh;
            display: flex;
            flex-direction: column;
            border-radius: 1.5rem 1.5rem 0 0;
            box-shadow: 0 -4px 20px rgba(0, 0, 0, 0.2);
            animation: slideUp 0.3s cubic-bezier(0.68, -0.55, 0.265, 1.55);
        }
        
        @media (min-width: 640px) {
            .cart-modal {
                border-radius: 1.5rem;
            }
        }
        
        .cart-items-container {
            flex: 1;
            overflow-y: auto;
            padding: 1.5rem;
        }
        
        .spinner {
            border: 3px solid rgba(255, 255, 255, .3);
            width: 20px;
            height: 20px;
            border-radius: 50%;
            border-left-color: #fff;
            animation: spin 1s ease infinite;
        }
        
        .spinner-dark {
            border: 3px solid rgba(0, 0, 0, .1);
            width: 24px;
            height: 24px;
            border-radius: 50%;
            border-left-color: #111827;
            animation: spin 1s ease infinite;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body class="bg-gray-50">

    <!-- Header -->
    <header id="main-header" class="bg-white border-b border-gray-200 sticky top-0 z-40 transition-shadow">
        <div class="px-4 py-3">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="font-bold text-xl text-gray-900">
                        Kafe<span class="text-gray-600">Anda</span>
                    </h1>
                    <p class="text-xs text-gray-500 mt-0.5">
                        {{ App\Models\Table::find(session('order_details.table_id'))->name ?? 'Meja' }} • 
                        {{ session('order_details.number_of_people') }} orang
                    </p>
                </div>
                
                <!-- Cart Button Desktop -->
                <button id="cart-btn-desktop" class="hidden sm:flex relative group">
                    <div class="p-2.5 bg-gray-900 text-white rounded-xl transition hover:bg-gray-800">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4z" />
                        </svg>
                    </div>
                    <span id="cart-badge-desktop" class="absolute -top-1 -right-1 bg-red-500 text-white text-xs font-bold rounded-full h-5 w-5 flex items-center justify-center {{ $cartTotalQuantity > 0 ? '' : 'hidden' }}">
                        {{ $cartTotalQuantity }}
                    </span>
                </button>
            </div>
        </div>
        
        <!-- Category Navigation -->
        <div class="px-4 pb-2 overflow-x-auto category-nav">
            <div class="flex space-x-2">
                @foreach ($categories as $category)
                    <a href="#category-{{ $category->id }}" 
                       class="category-tab inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-full whitespace-nowrap hover:bg-gray-200 transition">
                        {{ $category->name }}
                    </a>
                @endforeach
            </div>
        </div>
    </header>

    <main class="pb-24">
        
        <!-- Customer Info Card -->
        <div class="px-4 pt-4 pb-2">
            <div class="bg-gradient-to-r from-gray-800 to-gray-900 text-white rounded-2xl p-4 shadow-lg">
                <div class="flex items-center space-x-3">
                    <div class="p-2 bg-white/10 rounded-lg backdrop-blur">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm opacity-90">Memesan untuk:</p>
                        <p class="font-semibold text-lg">{{ session('order_details.customer_name') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Menu Categories -->
        <div class="px-4 pt-4">
            @foreach ($categories as $category)
                <div id="category-{{ $category->id }}" class="mb-8 scroll-mt-32">
                    <h3 class="text-xl font-bold text-gray-900 mb-4 flex items-center">
                        <span class="bg-gray-900 w-1 h-6 rounded mr-3"></span>
                        {{ $category->name }}
                    </h3>
                    
                    <div class="space-y-3">
                        @forelse ($category->products as $product)
                            @if ($product->is_available)
                                <div class="product-card bg-white rounded-xl shadow-sm overflow-hidden border border-gray-100">
                                    <div class="flex">
                                        <div class="w-24 h-24 sm:w-28 sm:h-28 flex-shrink-0 bg-gray-100">
                                            <img src="{{ $product->image ? asset('storage/' . $product->image) : 'https://via.placeholder.com/200x200.png?text=Menu' }}" 
                                                 alt="{{ $product->name }}" 
                                                 class="w-full h-full object-cover">
                                        </div>
                                        
                                        <div class="flex-1 p-3 flex flex-col justify-between">
                                            <div>
                                                <h4 class="font-semibold text-gray-900 text-sm sm:text-base leading-tight mb-1">
                                                    {{ $product->name }}
                                                </h4>
                                                @if($product->description)
                                                    <p class="text-xs text-gray-500 line-clamp-1">
                                                        {{ $product->description }}
                                                    </p>
                                                @endif
                                            </div>
                                            
                                            <div class="flex items-center justify-between mt-2">
                                                <p class="font-bold text-gray-900 text-base sm:text-lg">
                                                    Rp {{ number_format($product->price, 0, ',', '.') }}
                                                </p>
                                                
                                                <form action="{{ route('cart.add') }}" method="POST" class="add-to-cart-form">
                                                    @csrf
                                                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                                                    <input type="hidden" name="quantity" value="1">
                                                    
                                                    <button type="submit" class="add-to-cart-btn flex items-center space-x-1.5 px-3 py-1.5 sm:px-4 sm:py-2 bg-gray-900 text-white text-sm font-medium rounded-lg hover:bg-gray-800 active:scale-95 transition">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                                        </svg>
                                                        <span>Tambah</span>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @empty
                            <div class="text-center py-12 bg-white rounded-xl">
                                <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                                </svg>
                                <p class="text-gray-500 text-sm">Belum ada menu di kategori ini</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            @endforeach
        </div>
    </main>

    <!-- Floating Cart Button Mobile -->
    <button id="cart-btn-mobile" class="sm:hidden floating-cart">
        <div class="relative">
            <div class="bg-gray-900 text-white p-4 rounded-full shadow-2xl hover:bg-gray-800 transition">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4z" />
                </svg>
            </div>
            <span id="cart-badge-mobile" class="absolute -top-1 -right-1 bg-red-500 text-white text-xs font-bold rounded-full min-w-[20px] h-5 px-1.5 flex items-center justify-center {{ $cartTotalQuantity > 0 ? '' : 'hidden' }}">
                {{ $cartTotalQuantity }}
            </span>
        </div>
    </button>

    <!-- Cart Modal -->
    <div id="cart-modal" class="cart-modal-overlay">
        <div class="cart-modal">
            <!-- Header -->
            <div class="flex justify-between items-center p-6 border-b border-gray-200">
                <div class="flex items-center space-x-3">
                    <div class="bg-gray-900 p-2 rounded-lg">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4z"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-gray-900">Keranjang Belanja</h2>
                        <p class="text-sm text-gray-500"><span id="modal-total-items">0</span> item</p>
                    </div>
                </div>
                <button id="close-cart-modal" class="p-2 hover:bg-gray-100 rounded-lg transition">
                    <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <!-- Cart Items -->
            <div id="cart-items-container" class="cart-items-container">
                <!-- Will be populated by JavaScript -->
            </div>

            <!-- Summary -->
            <div id="cart-summary" class="border-t border-gray-200 p-6 space-y-4 bg-gray-50">
                <!-- Will be populated by JavaScript -->
            </div>
        </div>
    </div>

    <!-- Confirmation Modal for Delete -->
    <div id="delete-confirmation-modal" class="cart-modal-overlay">
        <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full mx-4 overflow-hidden" style="animation: slideUp 0.3s cubic-bezier(0.68, -0.55, 0.265, 1.55);">
            <!-- Icon Header -->
            <div class="bg-red-50 p-6 text-center">
                <div class="bg-red-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">Hapus Item?</h3>
                <p class="text-gray-600 text-sm">Item ini akan dihapus dari keranjang Anda</p>
            </div>

            <!-- Item Info -->
            <div class="p-6 border-b border-gray-200">
                <div class="flex items-center gap-4">
                    <img id="delete-item-image" src="" alt="" class="w-16 h-16 rounded-lg object-cover">
                    <div class="flex-1">
                        <h4 id="delete-item-name" class="font-semibold text-gray-900 mb-1"></h4>
                        <p id="delete-item-quantity" class="text-sm text-gray-500"></p>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="p-6 bg-gray-50 flex gap-3">
                <button id="cancel-delete-btn" class="flex-1 px-4 py-3 bg-white border-2 border-gray-300 hover:border-gray-400 text-gray-700 font-semibold rounded-xl transition active:scale-95">
                    Batal
                </button>
                <button id="confirm-delete-btn" class="flex-1 px-4 py-3 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-xl transition active:scale-95 flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                    Hapus
                </button>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const cartModal = document.getElementById('cart-modal');
            const cartBtnMobile = document.getElementById('cart-btn-mobile');
            const cartBtnDesktop = document.getElementById('cart-btn-desktop');
            const closeCartModal = document.getElementById('close-cart-modal');
            
            // URL templates untuk update dan remove cart
            const updateCartUrl = "{{ route('cart.update', ':id') }}";
            const removeCartUrl = "{{ route('cart.remove', ':id') }}";
            
            // Header scroll effect
            const header = document.getElementById('main-header');
            window.addEventListener('scroll', () => {
                if (window.pageYOffset > 10) {
                    header.classList.add('header-scrolled');
                } else {
                    header.classList.remove('header-scrolled');
                }
            });
            
            // Category tab active state
            const categoryTabs = document.querySelectorAll('.category-tab');
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const categoryId = entry.target.id;
                        categoryTabs.forEach(tab => {
                            if (tab.getAttribute('href') === `#${categoryId}`) {
                                tab.classList.remove('bg-gray-100', 'text-gray-700');
                                tab.classList.add('bg-gray-900', 'text-white');
                            } else {
                                tab.classList.remove('bg-gray-900', 'text-white');
                                tab.classList.add('bg-gray-100', 'text-gray-700');
                            }
                        });
                    }
                });
            }, { threshold: 0.5, rootMargin: '-100px 0px -50% 0px' });
            
            document.querySelectorAll('[id^="category-"]').forEach(section => {
                observer.observe(section);
            });
            
            // Open cart modal
            function openCartModal() {
                loadCart();
                cartModal.classList.add('active');
                document.body.style.overflow = 'hidden';
            }
            
            // Close cart modal
            function closeCartModalFunc() {
                cartModal.classList.remove('active');
                document.body.style.overflow = '';
            }
            
            cartBtnMobile.addEventListener('click', openCartModal);
            cartBtnDesktop.addEventListener('click', openCartModal);
            closeCartModal.addEventListener('click', closeCartModalFunc);
            
            // Close on overlay click
            cartModal.addEventListener('click', (e) => {
                if (e.target === cartModal) {
                    closeCartModalFunc();
                }
            });
            
            // Delete Confirmation Modal
            const deleteModal = document.getElementById('delete-confirmation-modal');
            const cancelDeleteBtn = document.getElementById('cancel-delete-btn');
            const confirmDeleteBtn = document.getElementById('confirm-delete-btn');
            let itemToDelete = null;
            
            function openDeleteModal(cartId, itemName, itemImage, itemQuantity) {
                itemToDelete = cartId;
                document.getElementById('delete-item-name').textContent = itemName;
                document.getElementById('delete-item-image').src = itemImage || 'https://via.placeholder.com/80x80.png?text=Menu';
                document.getElementById('delete-item-quantity').textContent = `${itemQuantity} item`;
                deleteModal.classList.add('active');
                document.body.style.overflow = 'hidden';
            }
            
            function closeDeleteModal() {
                deleteModal.classList.remove('active');
                document.body.style.overflow = cartModal.classList.contains('active') ? 'hidden' : '';
                itemToDelete = null;
            }
            
            cancelDeleteBtn.addEventListener('click', closeDeleteModal);
            
            deleteModal.addEventListener('click', (e) => {
                if (e.target === deleteModal) {
                    closeDeleteModal();
                }
            });
            
            confirmDeleteBtn.addEventListener('click', () => {
                if (itemToDelete) {
                    executeRemoveFromCart(itemToDelete);
                    closeDeleteModal();
                }
            });
            
            // Load cart data
            function loadCart() {
                const container = document.getElementById('cart-items-container');
                container.innerHTML = '<div class="text-center py-8"><div class="spinner-dark mx-auto"></div></div>';
                
                fetch('{{ route("cart.get") }}', {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    renderCart(data);
                })
                .catch(error => {
                    console.error('Error:', error);
                    container.innerHTML = '<div class="text-center py-8 text-red-500">Gagal memuat keranjang</div>';
                });
            }
            
            // Render cart
            function renderCart(data) {
                const container = document.getElementById('cart-items-container');
                const summary = document.getElementById('cart-summary');
                const modalTotalItems = document.getElementById('modal-total-items');
                
                modalTotalItems.textContent = data.total_quantity;
                
                if (data.items.length === 0) {
                    container.innerHTML = `
                        <div class="text-center py-12">
                            <div class="bg-gray-100 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4">
                                <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4z"/>
                                </svg>
                            </div>
                            <p class="text-gray-500 font-medium">Keranjang Anda kosong</p>
                            <p class="text-gray-400 text-sm mt-1">Tambahkan menu untuk mulai memesan</p>
                        </div>
                    `;
                    summary.innerHTML = '';
                    return;
                }
                
                // Render items
                let itemsHTML = '<div class="space-y-4">';
                data.items.forEach(item => {
                    const imageUrl = item.image ? `{{ asset('storage/') }}/${item.image}` : 'https://via.placeholder.com/80x80.png?text=Menu';
                    itemsHTML += `
                        <div class="bg-gray-50 rounded-xl p-4 hover:bg-gray-100 transition" data-cart-id="${item.cart_id}">
                            <div class="flex gap-4">
                                <img src="${imageUrl}" 
                                     alt="${item.name}"
                                     data-item-image
                                     class="w-20 h-20 rounded-lg object-cover flex-shrink-0">
                                
                                <div class="flex-1 min-w-0">
                                    <div class="flex justify-between items-start mb-2">
                                        <h3 class="font-semibold text-gray-900 truncate pr-2" data-item-name>${item.name}</h3>
                                        <button type="button" class="text-red-500 hover:bg-red-50 p-1 rounded transition flex-shrink-0" onclick="openDeleteConfirmation('${item.cart_id}', '${item.name.replace(/'/g, "\\'")}', '${imageUrl}', ${item.quantity})">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </div>
                                    
                                    <p class="font-bold text-gray-900 mb-3">Rp ${formatRupiah(item.price)}</p>
                                    
                                    <div class="flex items-center gap-3">
                                        <button type="button" class="bg-white border-2 border-gray-300 hover:border-gray-900 p-1 rounded-lg transition active:scale-95" onclick="updateQuantity('${item.cart_id}', parseInt('${item.quantity}') - 1)">
                                            <svg class="w-4 h-4 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                                            </svg>
                                        </button>
                                        
                                        <span class="font-semibold text-gray-900 w-8 text-center" data-item-quantity>${item.quantity}</span>
                                        
                                        <button type="button" class="bg-gray-900 hover:bg-gray-800 p-1 rounded-lg transition active:scale-95" onclick="updateQuantity('${item.cart_id}', parseInt('${item.quantity}') + 1)">
                                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                            </svg>
                                        </button>
                                        
                                        <span class="ml-auto font-bold text-gray-900">Rp ${formatRupiah(item.subtotal)}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                });
                itemsHTML += '</div>';
                container.innerHTML = itemsHTML;
                
                // Render summary
                summary.innerHTML = `
                    <div class="space-y-2">
                        <div class="flex justify-between text-gray-700">
                            <span>Subtotal (${data.total_quantity} item)</span>
                            <span class="font-medium">Rp ${formatRupiah(data.subtotal)}</span>
                        </div>
                        
                        <div class="flex justify-between text-gray-700">
                            <span>Biaya Layanan (${data.service_percent}%)</span>
                            <span class="font-medium">Rp ${formatRupiah(data.service_fee)}</span>
                        </div>
                        
                        <div class="flex justify-between text-gray-700">
                            <span>Pajak (${data.tax_percent}%)</span>
                            <span class="font-medium">Rp ${formatRupiah(data.tax)}</span>
                        </div>
                        
                        <div class="border-t border-gray-300 pt-2 mt-2">
                            <div class="flex justify-between items-center">
                                <span class="text-lg font-bold text-gray-900">Total Pembayaran</span>
                                <span class="text-2xl font-extrabold text-gray-900">Rp ${formatRupiah(data.grand_total)}</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2">
                        <form action="{{ route('order.place.counter') }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full flex items-center justify-center gap-2 bg-white border-2 border-gray-300 hover:border-gray-900 text-gray-900 font-semibold py-3 px-4 rounded-xl transition active:scale-95">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                                Bayar di Kasir
                            </button>
                        </form>
                        
                        <button id="pay-online-btn" class="flex items-center justify-center gap-2 bg-gray-900 hover:bg-gray-800 text-white font-semibold py-3 px-4 rounded-xl transition active:scale-95 shadow-lg">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                            </svg>
                            Bayar Sekarang
                        </button>
                    </div>
                `;
                
                // Attach online payment handler
                const payOnlineBtn = document.getElementById('pay-online-btn');
                if (payOnlineBtn) {
                    payOnlineBtn.addEventListener('click', handleOnlinePayment);
                }
            }
            
            // Format Rupiah
            function formatRupiah(number) {
                return new Intl.NumberFormat('id-ID').format(number);
            }
            
            // Update PERBAIKAN BUG
            window.updateQuantity = function(cartId, newQuantity) {
                // Validasi input harus number
                newQuantity = parseInt(newQuantity, 10);
                
                // Jika quantity 1 dan dikurangi, tampilkan modal hapus
                if (newQuantity < 1) {
                    // PERBAIKAN: Dapatkan data item terlebih dahulu sebelum buka modal
                    const cartItem = document.querySelector(`[data-cart-id="${cartId}"]`);
                    if (cartItem) {
                        const itemName = cartItem.querySelector('[data-item-name]')?.textContent || 'Item';
                        const itemImage = cartItem.querySelector('[data-item-image]')?.src || 'https://via.placeholder.com/80x80.png?text=Menu';
                        const itemQuantity = parseInt(cartItem.querySelector('[data-item-quantity]')?.textContent || '1');
                        openDeleteConfirmation(cartId, itemName, itemImage, itemQuantity);
                    }
                    return;
                }
                
                const url = updateCartUrl.replace(':id', cartId);
                
                fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        quantity: newQuantity
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        loadCart();
                        updateCartBadge(data.cart_count);
                        showNotification('Jumlah diperbarui', 'success');
                    } else {
                        showNotification(data.message || 'Gagal memperbarui', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showNotification('Terjadi kesalahan', 'error');
                });
            }
            
            // Remove from cart
            window.removeFromCart = function(cartId) {
                executeRemoveFromCart(cartId);
            }
            
            // Open delete confirmation
            window.openDeleteConfirmation = function(cartId, itemName, itemImage, itemQuantity) {
                openDeleteModal(cartId, itemName, itemImage, itemQuantity);
            }
            
            // Execute remove from cart
            function executeRemoveFromCart(cartId) {
                const url = removeCartUrl.replace(':id', cartId);
                
                fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        loadCart();
                        updateCartBadge(data.cart_count);
                        showNotification('Item dihapus', 'success');
                    } else {
                        showNotification(data.message || 'Gagal menghapus', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showNotification('Terjadi kesalahan', 'error');
                });
            }
            
            // Handle online payment
            function handleOnlinePayment(e) {
                e.preventDefault();
                const btn = e.currentTarget;
                const originalHTML = btn.innerHTML;
                
                btn.disabled = true;
                btn.innerHTML = '<div class="spinner mx-auto"></div>';
                
                fetch('{{ route("order.place.online") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        alert(data.error);
                        btn.disabled = false;
                        btn.innerHTML = originalHTML;
                    } else {
                        window.snap.pay(data.snapToken, {
                            onSuccess: function(result) {
                                window.location.href = '{{ route("order.success", ["order" => ":id"]) }}'.replace(':id', data.orderId);
                            },
                            onPending: function(result) {
                                alert("Menunggu pembayaran Anda...");
                                btn.disabled = false;
                                btn.innerHTML = originalHTML;
                            },
                            onError: function(result) {
                                alert("Pembayaran gagal!");
                                btn.disabled = false;
                                btn.innerHTML = originalHTML;
                            },
                            onClose: function() {
                                alert('Anda menutup pop-up pembayaran. Silakan coba lagi.');
                                btn.disabled = false;
                                btn.innerHTML = originalHTML;
                            }
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan. Silakan coba lagi.');
                    btn.disabled = false;
                    btn.innerHTML = originalHTML;
                });
            }
            
            // Add to cart forms
            const addToCartForms = document.querySelectorAll('.add-to-cart-form');
            
            addToCartForms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    
                    const formData = new FormData(this);
                    const submitButton = this.querySelector('.add-to-cart-btn');
                    const originalButtonHTML = submitButton.innerHTML;
                    
                    submitButton.disabled = true;
                    submitButton.innerHTML = `
                        <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    `;
                    
                    fetch(this.action, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            showNotification(data.message || 'Item ditambahkan!', 'success');
                            updateCartBadge(data.cartTotalQuantity || data.cart_count);
                        } else {
                            showNotification(data.error || data.message, 'error');
                            if (data.redirect) {
                                setTimeout(() => {
                                    window.location.href = data.redirect;
                                }, 1500);
                            }
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showNotification('Terjadi kesalahan. Silakan coba lagi.', 'error');
                    })
                    .finally(() => {
                        submitButton.disabled = false;
                        submitButton.innerHTML = originalButtonHTML;
                    });
                });
            });
        });
        
        // Show notification
        function showNotification(message, type = 'success') {
            const existingNotif = document.querySelector('.ajax-notification');
            if (existingNotif) {
                existingNotif.remove();
            }
            
            const bgColor = type === 'success' ? 'bg-green-500' : 'bg-red-500';
            const icon = type === 'success' 
                ? '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>'
                : '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>';
            
            const notification = document.createElement('div');
            notification.className = `ajax-notification fixed top-4 left-4 right-4 sm:left-auto sm:right-4 sm:max-w-sm z-[100] ${bgColor} text-white p-4 rounded-xl shadow-2xl`;
            
            notification.innerHTML = `
                <div class="flex items-center space-x-3">
                    <div class="flex-shrink-0">
                        ${icon}
                    </div>
                    <p class="flex-1 font-medium text-sm">${message}</p>
                    <button onclick="this.parentElement.parentElement.remove()" class="flex-shrink-0 text-white hover:opacity-70">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                        </svg>
                    </button>
                </div>
            `;
            
            document.body.appendChild(notification);
            
            setTimeout(() => {
                notification.style.animation = 'slideOutUp 0.3s ease-out';
                setTimeout(() => notification.remove(), 300);
            }, 3000);
        }
        
        // Update cart badge
        function updateCartBadge(count) {
            const cartBadgeMobile = document.getElementById('cart-badge-mobile');
            const cartBadgeDesktop = document.getElementById('cart-badge-desktop');
            
            [cartBadgeMobile, cartBadgeDesktop].forEach(badge => {
                if (badge) {
                    badge.textContent = count;
                    
                    if (count > 0) {
                        badge.classList.remove('hidden');
                        badge.classList.add('cart-badge-bounce');
                        setTimeout(() => {
                            badge.classList.remove('cart-badge-bounce');
                        }, 500);
                    } else {
                        badge.classList.add('hidden');
                    }
                }
            });
        }
    </script>

</body>
</html>