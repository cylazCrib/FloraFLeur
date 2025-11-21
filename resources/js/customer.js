/**
 * Flora Fleur Customer Dashboard Logic
 * Handles Navigation, Cart, Favorites, and Mock Backend Logic
 */

window.app = {
    state: {
        cart: [],
        favorites: [],
        orders: [], 
        addresses: [],
        products: [
            // --- Bouquets ---
            { 
                id: 1, 
                name: 'Red Roses Bouquet', 
                price: 1500, 
                category: 'bouquet', 
                occasion: 'valentines', 
                image: 'https://i.pinimg.com/736x/20/d9/75/20d975ae5d4f33d0caed1a0dad56b4a1.jpg',
                description: 'A timeless classic. This bouquet features 12 premium long-stemmed red roses, hand-tied with satin ribbon and accented with fresh baby’s breath and eucalyptus.'
            },
            {
                id: 11,
                name: 'Elegant White Roses',
                price: 2500,
                category: 'bouquet',
                occasion: 'wedding',
                image: 'https://i.pinimg.com/736x/fb/b2/ff/fbb2ff7675391e0ec79d8b1e94f9a364.jpg',
                description: 'Pure and pristine white roses hand-tied for the classic bride. Symbolizes innocence and new beginnings.'
            },
            {
                id: 12,
                name: 'Classic Bridal Bouquet',
                price: 2800,
                category: 'bouquet',
                occasion: 'wedding',
                image: 'https://i.pinimg.com/736x/8d/ec/ea/8decea6c3bfa62dc7abfc85d25f8fab7.jpg',
                description: 'A sophisticated mix of white roses, peonies, and soft greenery. The quintessential accessory for a timeless wedding look.'
            },
            {
                id: 17,
                name: 'Deep Red Romance',
                price: 1800,
                category: 'bouquet',
                occasion: 'valentines',
                image: 'https://i.pinimg.com/736x/2b/ac/5f/2bac5f79e5d7a87a2e4c0a072998ffc7.jpg',
                description: 'Intense crimson roses wrapped in premium black paper. A dramatic declaration of deep love and passion.'
            },
            {
                id: 18,
                name: 'Pink & White Delight',
                price: 1600,
                category: 'bouquet',
                occasion: 'valentines',
                image: 'https://i.pinimg.com/1200x/66/56/5f/66565f8a5b2b1e9ccccf7e33fe6027ea.jpg',
                description: 'A sweet combination of soft pink and creamy white blooms. Expresses admiration and gentle affection.'
            },
            {
                id: 19,
                name: 'Sunflower Surprise',
                price: 1400,
                category: 'bouquet',
                occasion: 'valentines',
                image: 'https://i.pinimg.com/1200x/d4/a7/7c/d4a77c27bf85182d423fa18b06e47784.jpg',
                description: 'Bright sunflowers mixed with red berries. A cheerful alternative to traditional roses for a love that radiates happiness.'
            },
            {
                id: 20,
                name: 'Lavender Dreams',
                price: 1700,
                category: 'bouquet',
                occasion: 'valentines',
                image: 'https://i.pinimg.com/736x/93/70/e3/9370e3f359041ff4e3ae470e15e8a4cd.jpg',
                description: 'A fragrant bouquet dominated by purple hues, featuring lavender, lilac roses, and statice. Mystical and enchanting.'
            },

            // --- Standees (Grand Opening & Wedding) ---
            { 
                id: 2, 
                name: 'Grand Opening Standee', 
                price: 3500, 
                category: 'standee', 
                occasion: 'grand-opening', 
                image: 'https://i.pinimg.com/1200x/43/b3/4f/43b34f35e4fe86163f3a80e32213212c.jpg',
                description: 'Make a bold statement for any inauguration. This 6ft standee features vibrant anthuriums, yellow lilies, and tropical foliage to symbolize success and prosperity.'
            },
            { 
                id: 6, 
                name: 'Wedding Arch', 
                price: 15000, 
                category: 'standee', 
                occasion: 'wedding', 
                image: 'https://i.pinimg.com/1200x/85/db/85/85db85f46627b46a16c2cd697a72cc21.jpg',
                description: 'Create a magical backdrop for your special day. This custom floral arch features cascading white roses, lush greenery, and seasonal blooms.'
            },
            {
                id: 16,
                name: 'Boho Wedding Backdrop',
                price: 8000,
                category: 'standee',
                occasion: 'wedding',
                image: 'https://i.pinimg.com/1200x/cf/23/7d/cf237d3df5e0ba7b0bcf0025a4ac1f9b.jpg',
                description: 'A free-spirited backdrop using dried palms, pampas grass, and terracotta roses. Ideal for bohemian-themed nuptials.'
            },
            {
                id: 26,
                name: 'Radiant Inaugural',
                price: 3800,
                category: 'standee',
                occasion: 'grand-opening',
                image: 'https://i.pinimg.com/1200x/f5/fc/b9/f5fcb970da51d5a2ed9b64514d232080.jpg',
                description: 'A striking display of bright orange and red heliconias, symbolizing growth and success. Perfect for business openings.'
            },
            {
                id: 27,
                name: 'Golden Success',
                price: 4200,
                category: 'standee',
                occasion: 'grand-opening',
                image: 'https://i.pinimg.com/736x/14/3b/ca/143bca432537a2b32dbcc41fa57a4e2c.jpg',
                description: 'Tall yellow mums and golden palms create a beacon of prosperity. A luxurious choice for corporate events.'
            },
            {
                id: 28,
                name: 'Modern Chic Standee',
                price: 3500,
                category: 'standee',
                occasion: 'grand-opening',
                image: 'https://i.pinimg.com/1200x/a2/6e/68/a26e68dfb26d0aaa83eea06f6114b558.jpg',
                description: 'A contemporary arrangement with architectural lines, featuring bird of paradise and monstera leaves.'
            },
            {
                id: 29,
                name: 'Classic Congratulatory',
                price: 3000,
                category: 'standee',
                occasion: 'grand-opening',
                image: 'https://i.pinimg.com/736x/e7/73/35/e7733529f76975ae6e3eedde5aa229dc.jpg',
                description: 'Traditional red gerberas and roses on a bamboo tripod. A time-honored way to send your best wishes.'
            },
            {
                id: 30,
                name: 'Vibrant Victory',
                price: 3600,
                category: 'standee',
                occasion: 'grand-opening',
                image: 'https://i.pinimg.com/736x/53/e0/1b/53e01b75b570bf237edfbcfd0089ce0e.jpg',
                description: 'An explosion of color with multi-colored mums and carnations. Celebrates joy and new beginnings.'
            },
            {
                id: 31,
                name: 'Elegant Opening',
                price: 4000,
                category: 'standee',
                occasion: 'grand-opening',
                image: 'https://i.pinimg.com/736x/e9/df/b2/e9dfb27f2d71a28621145c692ae1ecd7.jpg',
                description: 'Sophisticated pink and white lilies arranged with height and grace. Adds a touch of class to any storefront.'
            },

            // --- Baskets & Plants ---
            { 
                id: 3, 
                name: 'Indoor Fern Basket', 
                price: 850, 
                category: 'basket', 
                occasion: 'all', 
                image: 'https://i.pinimg.com/736x/0e/24/00/0e2400b844843a0164813e65ced223d8.jpg',
                description: 'Bring the outdoors in with this lush Boston Fern in a woven seagrass basket. Perfect for air purification and adding a touch of serenity to your living space.'
            },
            
            // --- Potted Plants ---
            { 
                id: 7, 
                name: 'Exotic Caladium', 
                price: 1100, 
                category: 'potted', 
                occasion: 'all', 
                image: 'https://images.unsplash.com/photo-1599598425947-320a20639026?q=80&w=800&auto=format&fit=crop',
                description: 'Known for its heart-shaped leaves with striking pink and green patterns. A stunning centerpiece that adds an artistic touch to any room.'
            },
            { 
                id: 8, 
                name: 'Red Anthurium Pot', 
                price: 1450, 
                category: 'potted', 
                occasion: 'all', 
                image: 'https://i.pinimg.com/736x/3d/ec/b3/3decb3de528c52bb19d8dceb271ff4df.jpg',
                description: 'Bright, waxy red blooms that last for months. This low-maintenance tropical plant represents hospitality and abundance.'
            },
            { 
                id: 9, 
                name: 'Peace Lily Elegance', 
                price: 1300, 
                category: 'potted', 
                occasion: 'all', 
                image: 'https://i.pinimg.com/1200x/cb/58/91/cb5891bd9d2140345723840b7fa9908f.jpg',
                description: 'An elegant air-purifying plant with glossy dark green leaves and graceful white spathes. A symbol of peace, prosperity, and sympathy.'
            },
            { 
                id: 10, 
                name: 'Burgundy Rubber Plant', 
                price: 1850, 
                category: 'potted', 
                occasion: 'all', 
                image: 'https://i.pinimg.com/736x/bc/8d/ca/bc8dcab126ff40e3f95964141633a7d1.jpg',
                description: 'A robust indoor tree with thick, glossy burgundy-green leaves. Adds magnificent height and drama to modern interiors.'
            },
            {
                id: 32,
                name: 'Monstera Deliciosa',
                price: 1600,
                category: 'potted',
                occasion: 'all',
                image: 'https://i.pinimg.com/1200x/7e/0b/34/7e0b34bd83b6be958b31f58d7e9cb91c.jpg',
                description: 'The iconic Swiss Cheese Plant with massive, glossy, split leaves. A true jungle giant that makes a spectacular focal point in any bright room.'
            },
            {
                id: 33,
                name: 'Trailing Golden Pothos',
                price: 950,
                category: 'potted',
                occasion: 'all',
                image: 'https://i.pinimg.com/736x/79/3c/2e/793c2e15c54f92d287e16910763e1913.jpg',
                description: 'A resilient and fast-growing vine with heart-shaped variegated leaves. Perfect for hanging baskets or cascading from high shelves.'
            },

            // --- Flower Boxes ---
            {
                id: 21,
                name: 'Peach Hatbox',
                price: 2100,
                category: 'box',
                occasion: 'all',
                image: 'https://i.pinimg.com/1200x/85/77/1c/85771c431d647833304aa1099abe2f92.jpg',
                description: 'Warm peach roses arranged in a chic cylindrical box. A modern and stylish gift suitable for any celebration.'
            },
            {
                id: 22,
                name: 'Blue Hydrangea Box',
                price: 2300,
                category: 'box',
                occasion: 'all',
                image: 'https://i.pinimg.com/736x/9f/f3/66/9ff3669cb70928b46566894308fbc109.jpg',
                description: 'Voluminous blue hydrangeas that evoke a sense of calm and ocean breeze. Presented in a matching luxury box.'
            },
            {
                id: 23,
                name: 'Mixed Spring Box',
                price: 1950,
                category: 'box',
                occasion: 'all',
                image: 'https://i.pinimg.com/1200x/d6/f5/15/d6f5154991d9d7b9864241758107b22d.jpg',
                description: 'A vibrant explosion of colors featuring yellow, pink, and orange blooms. Brings the energy of spring into any room.'
            },
            {
                id: 24,
                name: 'Elegant Black Box',
                price: 2600,
                category: 'box',
                occasion: 'all',
                image: 'https://i.pinimg.com/1200x/57/f9/6c/57f96c70d16614419bd08b2dd2ff930e.jpg',
                description: 'Premium red roses set against a matte black box with gold trim. The ultimate luxury floral experience.'
            },
            {
                id: 25,
                name: 'Heart Shaped Rose Box',
                price: 2900,
                category: 'box',
                occasion: 'valentines',
                image: 'https://i.pinimg.com/1200x/bf/30/88/bf3088d7dbf1210d450bc9502b5be6a7.jpg',
                description: 'Red roses meticulously arranged in a heart-shaped box. A romantic gesture that speaks louder than words.'
            }
        ],
        currentProduct: null,
        currentView: {
            filter: 'all',
            title: 'Collection'
        }
    },

    init() {
        // Load data from localStorage if available
        const storedCart = localStorage.getItem('flora_cart');
        const storedFavs = localStorage.getItem('flora_favs');
        const storedOrders = localStorage.getItem('flora_orders');
        const storedAddresses = localStorage.getItem('flora_addresses');

        if (storedCart) this.state.cart = JSON.parse(storedCart);
        if (storedFavs) this.state.favorites = JSON.parse(storedFavs);
        if (storedOrders) this.state.orders = JSON.parse(storedOrders);
        if (storedAddresses) this.state.addresses = JSON.parse(storedAddresses);

        this.updateBadges();
        this.nav.init();
        this.account.renderAddresses();
        
        // Setup Modal Listeners
        document.querySelectorAll('[data-modal-close]').forEach(btn => {
            btn.addEventListener('click', () => {
                document.getElementById('product-modal').style.display = 'none';
                document.getElementById('payment-modal').style.display = 'none';
                document.getElementById('thank-you-modal').style.display = 'none';
            });
        });

        // Quantity buttons in modal
        const minusBtn = document.querySelector('.quantity-btn-minus');
        const plusBtn = document.querySelector('.quantity-btn-plus');
        
        if(minusBtn) minusBtn.addEventListener('click', () => this.modal.adjustQuantity(-1));
        if(plusBtn) plusBtn.addEventListener('click', () => this.modal.adjustQuantity(1));
        
        // Modal Action Buttons
        const modalCheckout = document.querySelector('#product-modal .btn-checkout');
        if(modalCheckout) {
            modalCheckout.addEventListener('click', () => {
                 this.cart.add(this.state.currentProduct, parseInt(document.getElementById('modal-quantity-value').innerText));
                 document.getElementById('product-modal').style.display = 'none';
                 this.nav.showCart();
            });
        }
        
        // Add to cart button inside modal
        const modalAddToCart = document.querySelector('#product-modal button:first-of-type');
        if(modalAddToCart) {
            modalAddToCart.addEventListener('click', () => {
                 this.cart.add(this.state.currentProduct, parseInt(document.getElementById('modal-quantity-value').innerText));
                 document.getElementById('product-modal').style.display = 'none';
                 this.toast.show('Added to Cart');
            });
        }

        // Payment Modal
        const placeOrderBtn = document.querySelector('.btn-place-order');
        if(placeOrderBtn) {
            placeOrderBtn.addEventListener('click', () => {
                 this.cart.processPayment();
            });
        }
        
        const backMainBtn = document.querySelector('.btn-back-main');
        if(backMainBtn) {
            backMainBtn.addEventListener('click', () => {
                 document.getElementById('thank-you-modal').style.display = 'none';
                 this.nav.showDashboard();
            });
        }
    },

    save() {
        localStorage.setItem('flora_cart', JSON.stringify(this.state.cart));
        localStorage.setItem('flora_favs', JSON.stringify(this.state.favorites));
        localStorage.setItem('flora_orders', JSON.stringify(this.state.orders));
        localStorage.setItem('flora_addresses', JSON.stringify(this.state.addresses));
        this.updateBadges();
    },

    updateBadges() {
        const cartCount = this.state.cart.reduce((acc, item) => acc + item.qty, 0);
        const favCount = this.state.favorites.length;

        const cartBadge = document.getElementById('cart-badge');
        const favBadge = document.getElementById('fav-badge');

        if(cartBadge) {
            cartBadge.innerText = cartCount;
            cartBadge.style.opacity = cartCount > 0 ? '1' : '0';
        }
        
        if(favBadge) {
            favBadge.innerText = favCount;
            favBadge.style.opacity = favCount > 0 ? '1' : '0';
        }
    },

    toast: {
        show(message, type = 'success') {
            const toast = document.getElementById('order-toast');
            if(!toast) return;
            toast.querySelector('span').innerText = type === 'success' ? 'Success' : 'Note';
            toast.querySelector('p').innerText = message;
            toast.classList.remove('translate-x-[120%]');
            setTimeout(() => {
                toast.classList.add('translate-x-[120%]');
            }, 3000);
        }
    },

    nav: {
        init() {
            // Ensure correct initial state
            this.hideAll();
            const dash = document.getElementById('view-dashboard');
            if(dash) {
                dash.classList.remove('hidden');
                setTimeout(() => dash.classList.add('active'), 50);
                const bg = document.getElementById('bg-image-container');
                if (bg) bg.style.opacity = '1';
                if (bg) bg.classList.remove('bg-faded'); 
            }
        },

        hideAll() {
            document.querySelectorAll('.view-section').forEach(el => {
                el.classList.add('hidden');
                el.classList.remove('active'); // Ensure animation resets
            });
            window.scrollTo(0, 0); 
            
            const bg = document.getElementById('bg-image-container');
            if (bg) bg.classList.add('bg-faded'); 
            document.getElementById('app-container-dashboard').classList.remove('bg-[#F5F5F0]'); 
        },

        showDashboard(e) {
            if(e) e.preventDefault();
            this.hideAll();
            const dash = document.getElementById('view-dashboard');
            dash.classList.remove('hidden');
            setTimeout(() => dash.classList.add('active'), 50);
            
            // Restore full background
            const bg = document.getElementById('bg-image-container');
            if (bg) bg.classList.remove('bg-faded'); 
        },

        showRequest(e) {
            if(e) e.preventDefault();
            this.hideAll();
            const view = document.getElementById('view-request');
            view.classList.remove('hidden');
            setTimeout(() => view.classList.add('active'), 50); // FIXED: ADDED ACTIVE
            document.getElementById('app-container-dashboard').classList.add('bg-[#F5F5F0]');
        },

        filterProducts(filter, title) {
            window.app.state.currentView.filter = filter;
            window.app.state.currentView.title = title;

            this.hideAll();
            const view = document.getElementById('view-products');
            view.classList.remove('hidden');
            setTimeout(() => view.classList.add('active'), 50); // FIXED: ADDED ACTIVE
            document.getElementById('app-container-dashboard').classList.add('bg-[#F5F5F0]');
            
            document.getElementById('product-category-title').innerText = title;

            const grid = document.getElementById('products-grid');
            grid.innerHTML = '';

            const products = filter === 'all' 
                ? window.app.state.products 
                : window.app.state.products.filter(p => p.category === filter || p.occasion === filter);

            products.forEach(p => {
                const isFav = window.app.favorites.has(p.id);
                const card = document.createElement('div');
                card.className = 'bg-white rounded-lg shadow-md overflow-hidden group hover:shadow-xl transition-all duration-300';
                card.innerHTML = `
                    <div class="relative h-64 overflow-hidden cursor-pointer" onclick="window.app.modal.open(${p.id})">
                        <img src="${p.image}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                        <div id="fav-btn-${p.id}" class="absolute bottom-2 right-2 bg-white/90 p-2 rounded-full shadow hover:bg-[#86A873] hover:text-white transition-colors cursor-pointer" onclick="event.stopPropagation(); window.app.favorites.toggle(${p.id})">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ${isFav ? 'text-red-500 fill-current' : 'text-gray-600'}" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd" />
                            </svg>
                        </div>
                    </div>
                    <div class="p-4">
                        <h3 class="font-rosarivo text-lg text-[#4A4A3A]">${p.name}</h3>
                        <p class="text-gray-500 text-sm mb-3">₱${p.price.toLocaleString()}</p>
                        <button onclick="window.app.cart.addById(${p.id})" class="w-full bg-[#4A4A3A] text-white text-xs py-2 rounded hover:bg-[#86A873] transition-colors uppercase font-bold tracking-wider cursor-pointer">
                            Add to Cart
                        </button>
                    </div>
                `;
                grid.appendChild(card);
            });
        },

        showCart(e) {
            if(e) e.preventDefault();
            this.hideAll();
            const view = document.getElementById('view-cart');
            view.classList.remove('hidden');
            setTimeout(() => view.classList.add('active'), 50); // FIXED: ADDED ACTIVE
            document.getElementById('app-container-dashboard').classList.add('bg-[#F5F5F0]');
            window.app.cart.render();
        },

        showFavorites(e) {
            if(e) e.preventDefault();
            this.hideAll();
            const view = document.getElementById('view-favorites');
            view.classList.remove('hidden');
            setTimeout(() => view.classList.add('active'), 50); // FIXED: ADDED ACTIVE
            document.getElementById('app-container-dashboard').classList.add('bg-[#F5F5F0]');
            window.app.favorites.render();
        },

        showPurchases(tab = 'to-ship') {
            this.hideAll();
            const view = document.getElementById('view-purchases');
            view.classList.remove('hidden');
            setTimeout(() => view.classList.add('active'), 50); // FIXED: ADDED ACTIVE
            document.getElementById('app-container-dashboard').classList.add('bg-[#F5F5F0]');
            this.switchPurchaseTab(tab);
        },
        
        switchPurchaseTab(tab) {
            document.querySelectorAll('.purchase-tab').forEach(t => {
                t.classList.remove('active', 'border-[#86A873]', 'text-[#4A4A3A]');
                t.classList.add('border-transparent', 'text-gray-500');
                if(t.dataset.tab === tab) {
                    t.classList.add('active', 'border-[#86A873]', 'text-[#4A4A3A]');
                    t.classList.remove('border-transparent', 'text-gray-500');
                }
            });

            const container = document.getElementById('purchases-list');
            container.innerHTML = '';
            
            const filteredOrders = window.app.state.orders.filter(o => o.status === tab);
            
            if(filteredOrders.length === 0) {
                container.innerHTML = `<div class="text-center py-10 text-gray-400 italic bg-white/80 backdrop-blur-sm rounded-lg">No orders in this category.</div>`;
                return;
            }

            filteredOrders.forEach(order => {
                const div = document.createElement('div');
                div.className = 'bg-white p-6 rounded-lg shadow border border-gray-100';
                div.innerHTML = `
                    <div class="flex justify-between mb-4 border-b border-gray-100 pb-2">
                        <span class="font-bold text-[#4A4A3A]">Order #${order.id}</span>
                        <span class="text-sm text-gray-500">${order.date}</span>
                    </div>
                    <div class="space-y-2 mb-4">
                        ${order.items.map(item => `
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">${item.qty}x ${item.name}</span>
                                <span class="font-medium">₱${(item.price * item.qty).toLocaleString()}</span>
                            </div>
                        `).join('')}
                    </div>
                    <div class="flex justify-between items-center pt-2">
                        <span class="text-sm font-bold text-[#86A873] uppercase tracking-wide">${order.status.replace('-', ' ')}</span>
                        <span class="font-rosarivo text-xl">Total: ₱${order.total.toLocaleString()}</span>
                    </div>
                `;
                container.appendChild(div);
            });
        },

        showAbout(e) {
            if(e) e.preventDefault();
            this.hideAll();
            const view = document.getElementById('view-about');
            view.classList.remove('hidden');
            setTimeout(() => view.classList.add('active'), 50); // FIXED: ADDED ACTIVE
            document.getElementById('app-container-dashboard').classList.add('bg-[#F5F5F0]');
        },

        showAccount(e) {
             if(e) e.preventDefault();
            this.hideAll();
            const view = document.getElementById('view-account');
            view.classList.remove('hidden');
            setTimeout(() => view.classList.add('active'), 50); // FIXED: ADDED ACTIVE
            document.getElementById('app-container-dashboard').classList.add('bg-[#F5F5F0]');
        }
    },

    // ... (modal, cart, favorites logic remain the same) ...
    modal: {
        open(productId) {
            const p = window.app.state.products.find(x => x.id === productId);
            if(!p) return;
            
            window.app.state.currentProduct = p;
            
            document.getElementById('modal-product-image').src = p.image;
            document.getElementById('modal-product-title').innerText = p.name;
            // Inject description here
            const descEl = document.getElementById('modal-product-description');
            if(descEl) descEl.innerText = p.description || 'No description available.';
            
            document.getElementById('modal-product-price').innerText = '₱' + p.price.toLocaleString();
            document.getElementById('modal-quantity-value').innerText = '1';
            document.getElementById('product-modal').style.display = 'flex';
        },
        adjustQuantity(delta) {
            const el = document.getElementById('modal-quantity-value');
            let val = parseInt(el.innerText) + delta;
            if(val < 1) val = 1;
            el.innerText = val;
        }
    },

    cart: {
        addById(id) {
            const p = window.app.state.products.find(x => x.id === id);
            if(p) this.add(p, 1);
        },
        add(product, qty) {
            const existing = window.app.state.cart.find(i => i.id === product.id);
            if(existing) {
                existing.qty += qty;
            } else {
                window.app.state.cart.push({ ...product, qty: qty });
            }
            window.app.save();
            window.app.toast.show(`${product.name} added to cart!`);
        },
        remove(id) {
            window.app.state.cart = window.app.state.cart.filter(i => i.id !== id);
            window.app.save();
            this.render();
        },
        render() {
            const tbody = document.getElementById('cart-items-container');
            tbody.innerHTML = '';
            let total = 0;

            if(window.app.state.cart.length === 0) {
                tbody.innerHTML = `<tr><td colspan="5" class="text-center py-8 text-gray-400">Your cart is empty.</td></tr>`;
            }

            window.app.state.cart.forEach(item => {
                const lineTotal = item.price * item.qty;
                total += lineTotal;
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td class="px-6 py-4 flex items-center gap-3">
                        <img src="${item.image}" class="w-10 h-10 rounded object-cover">
                        <span class="font-medium text-gray-800">${item.name}</span>
                    </td>
                    <td class="px-6 py-4 text-gray-600">₱${item.price.toLocaleString()}</td>
                    <td class="px-6 py-4 text-gray-600">${item.qty}</td>
                    <td class="px-6 py-4 font-bold text-[#4A4A3A]">₱${lineTotal.toLocaleString()}</td>
                    <td class="px-6 py-4 text-right">
                        <button onclick="window.app.cart.remove(${item.id})" class="text-red-400 hover:text-red-600 text-xs font-bold uppercase cursor-pointer">Remove</button>
                    </td>
                `;
                tbody.appendChild(tr);
            });

            document.getElementById('cart-total').innerText = '₱' + total.toLocaleString();
        },
        processPayment() {
            if(window.app.state.cart.length === 0) return;
            
            const total = window.app.state.cart.reduce((acc, item) => acc + (item.price * item.qty), 0);
            
            const newOrder = {
                id: 'ORD-' + Math.floor(Math.random() * 10000),
                status: 'to-ship',
                items: [...window.app.state.cart],
                total: total,
                date: new Date().toLocaleDateString()
            };
            
            window.app.state.orders.push(newOrder);
            window.app.state.cart = []; // Clear cart
            window.app.save();
            
            document.getElementById('payment-modal').style.display = 'none';
            document.getElementById('thank-you-modal').style.display = 'flex';
        },
        checkout() {
            if(window.app.state.cart.length === 0) {
                window.app.toast.show('Cart is empty', 'error');
                return;
            }
            const total = window.app.state.cart.reduce((acc, item) => acc + (item.price * item.qty), 0);
            document.getElementById('payment-total-price').innerText = '₱' + total.toLocaleString();
            document.getElementById('payment-modal').style.display = 'flex';
        }
    },

    favorites: {
        has(id) {
            return window.app.state.favorites.some(i => i.id === id);
        },
        toggle(id) {
            // 1. Update State
            if(this.has(id)) {
                window.app.state.favorites = window.app.state.favorites.filter(i => i.id !== id);
                window.app.toast.show('Removed from Favorites');
            } else {
                const p = window.app.state.products.find(x => x.id === id);
                if(p) {
                    window.app.state.favorites.push(p);
                    window.app.toast.show('Added to Favorites');
                }
            }
            window.app.save();

            // 2. Handle UI Logic
            // If we are on the Favorites page, we MUST re-render to remove the item
            const favView = document.getElementById('view-favorites');
            if (!favView.classList.contains('hidden')) {
                this.render();
                return; 
            }

            // If we are on the Products Grid page, we do NOT want to re-render the whole grid
            // because that causes the "flash" and reset to 'all' that you disliked.
            // Instead, we just find the heart icon and change its color.
            const btn = document.getElementById(`fav-btn-${id}`);
            if (btn) {
                const svg = btn.querySelector('svg');
                if (svg) {
                    if (this.has(id)) {
                        // Make it red
                        svg.classList.remove('text-gray-600');
                        svg.classList.add('text-red-500', 'fill-current');
                    } else {
                        // Make it gray
                        svg.classList.remove('text-red-500', 'fill-current');
                        svg.classList.add('text-gray-600');
                    }
                }
            }
        },
        render() {
            const grid = document.getElementById('favorites-grid');
            grid.innerHTML = '';
            const msg = document.getElementById('no-favorites-msg');

            if(window.app.state.favorites.length === 0) {
                msg.classList.remove('hidden');
            } else {
                msg.classList.add('hidden');
                window.app.state.favorites.forEach(p => {
                    const card = document.createElement('div');
                    card.className = 'bg-white rounded-lg shadow-md overflow-hidden group relative';
                    card.innerHTML = `
                        <div class="h-64 overflow-hidden cursor-pointer" onclick="window.app.modal.open(${p.id})">
                            <img src="${p.image}" class="w-full h-full object-cover">
                        </div>
                        <div class="p-4">
                            <h3 class="font-rosarivo text-lg text-[#4A4A3A]">${p.name}</h3>
                            <p class="text-gray-500 text-sm mb-3">₱${p.price.toLocaleString()}</p>
                            <div class="flex gap-2">
                                <button onclick="window.app.cart.addById(${p.id})" class="flex-1 bg-[#4A4A3A] text-white text-xs py-2 rounded hover:bg-[#86A873] transition-colors uppercase font-bold tracking-wider cursor-pointer">
                                    Add to Cart
                                </button>
                                <button onclick="window.app.favorites.toggle(${p.id})" class="px-3 border border-gray-200 rounded hover:bg-red-50 text-red-500 cursor-pointer">
                                    ✕
                                </button>
                            </div>
                        </div>
                    `;
                    grid.appendChild(card);
                });
            }
        }
    },

    account: {
        toggleEdit() {
            const display = document.getElementById('account-display');
            const edit = document.getElementById('account-edit');
            
            if (display.classList.contains('hidden')) {
                display.classList.remove('hidden');
                edit.classList.add('hidden');
            } else {
                display.classList.add('hidden');
                edit.classList.remove('hidden');
            }
        },
        save() {
            const name = document.getElementById('edit-name').value;
            const email = document.getElementById('edit-email').value;
            
            if(name) document.getElementById('display-name').innerText = name;
            if(email) document.getElementById('display-email').innerText = email;
            
            // In a real app, you would send an AJAX request here.
            window.app.toast.show('Profile updated successfully!');
            this.toggleEdit();
        },
        
        // Address Logic
        showAddAddress() {
            document.getElementById('address-form').classList.remove('hidden');
            document.getElementById('address-form-title').innerText = 'New Address';
            // Clear fields
            document.getElementById('addr-id').value = '';
            document.getElementById('addr-label').value = '';
            document.getElementById('addr-street').value = '';
            document.getElementById('addr-city').value = '';
            document.getElementById('addr-zip').value = '';
        },
        cancelAddress() {
            document.getElementById('address-form').classList.add('hidden');
        },
        saveAddress() {
            const id = document.getElementById('addr-id').value;
            const label = document.getElementById('addr-label').value;
            const street = document.getElementById('addr-street').value;
            const city = document.getElementById('addr-city').value;
            const zip = document.getElementById('addr-zip').value;

            if (!label || !street || !city) {
                alert('Please fill required fields');
                return;
            }

            const newAddr = {
                id: id || Date.now(),
                label,
                street,
                city,
                zip
            };

            if (id) {
                // Edit existing
                const index = window.app.state.addresses.findIndex(a => a.id == id);
                if (index !== -1) {
                    window.app.state.addresses[index] = newAddr;
                }
            } else {
                // Add new
                window.app.state.addresses.push(newAddr);
            }

            window.app.save();
            this.renderAddresses();
            this.cancelAddress();
            window.app.toast.show('Address saved successfully!');
        },
        deleteAddress(id) {
            if(confirm('Delete this address?')) {
                window.app.state.addresses = window.app.state.addresses.filter(a => a.id != id);
                window.app.save();
                this.renderAddresses();
            }
        },
        editAddress(id) {
            const addr = window.app.state.addresses.find(a => a.id == id);
            if(!addr) return;

            document.getElementById('addr-id').value = addr.id;
            document.getElementById('addr-label').value = addr.label;
            document.getElementById('addr-street').value = addr.street;
            document.getElementById('addr-city').value = addr.city;
            document.getElementById('addr-zip').value = addr.zip;

            document.getElementById('address-form').classList.remove('hidden');
            document.getElementById('address-form-title').innerText = 'Edit Address';
        },
        renderAddresses() {
            const container = document.getElementById('address-list');
            if(!container) return;
            
            container.innerHTML = '';
            
            if (window.app.state.addresses.length === 0) {
                container.innerHTML = `<p class="text-gray-400 italic text-sm text-center py-4">No addresses saved yet.</p>`;
                return;
            }

            window.app.state.addresses.forEach(addr => {
                const div = document.createElement('div');
                div.className = 'border border-gray-200 rounded-lg p-4 flex justify-between items-center bg-gray-50';
                div.innerHTML = `
                    <div>
                        <span class="text-xs font-bold text-[#86A873] uppercase tracking-wide block mb-1">${addr.label}</span>
                        <p class="text-sm text-gray-700">${addr.street}, ${addr.city} ${addr.zip}</p>
                    </div>
                    <div class="flex gap-3">
                        <button onclick="app.account.editAddress(${addr.id})" class="text-gray-400 hover:text-[#4A4A3A]">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                        </button>
                        <button onclick="app.account.deleteAddress(${addr.id})" class="text-gray-400 hover:text-red-500">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                        </button>
                    </div>
                `;
                container.appendChild(div);
            });
        }
    },

    request: {
        submit() {
            const desc = document.getElementById('request-description').value;
            if(!desc.trim()) {
                alert('Please describe your request.');
                return;
            }
            // Simulate sending
            document.getElementById('request-description').value = '';
            window.app.toast.show('Request sent to florist!');
            setTimeout(() => window.app.nav.showDashboard(), 1500);
        },
        call() {
            window.location.href = 'tel:+1234567890';
        }
    }
};

// Start App
window.addEventListener('DOMContentLoaded', () => {
    window.app.init();
});