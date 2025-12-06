// ==========================================
// 1. Global State & Initialization
// ==========================================
let cart = JSON.parse(localStorage.getItem('cart')) || [];
let wishlist = JSON.parse(localStorage.getItem('wishlist')) || [];
let currentUser = null;

// পেজ লোড হওয়ার সাথে সাথে এই ফাংশনগুলো রান করবে
document.addEventListener('DOMContentLoaded', async () => {
    await checkSession(); // সেশন চেক
    updateCartCount();    // কার্ট কাউন্ট আপডেট
    updateWishlistCount();// উইশলিস্ট কাউন্ট আপডেট
    renderCartItems();    // কার্ট সাইডবার রেন্ডার
});

// ==========================================
// 2. Authentication Functions
// ==========================================

// সার্ভার থেকে চেক করা ইউজার লগইন অবস্থায় আছে কিনা
async function checkSession() {
    try {
        const response = await fetch('api.php?action=check_session');
        const data = await response.json();
        currentUser = (data.status === 'logged_in') ? data.user : null;
    } catch (e) {
        console.error("Session check failed:", e);
        currentUser = null;
    }
    updateAuthUI();
}

// লগইন অবস্থার ওপর ভিত্তি করে হেডার আপডেট করা
function updateAuthUI() {
    const desk = document.getElementById('auth-section');
    const mob = document.getElementById('mobile-auth');

    let html = '';

    if (currentUser) {
        // যদি ইউজার লগইন থাকে
        html = `
            <div class="flex items-center gap-3">
                <a href="#" class="font-bold text-green-800 hover:underline flex items-center gap-1 bg-green-50 px-3 py-1 rounded-full border border-green-100 transition hover:bg-green-100">
                    <i class="fa-regular fa-user"></i> ${currentUser.name}
                </a>
                ${currentUser.role === 'admin' ? 
                    `<a href="admin.php" class="text-white bg-blue-600 w-7 h-7 rounded-full flex items-center justify-center hover:bg-blue-700 transition" title="Admin Panel">
                        <i class="fa-solid fa-shield-halved text-xs"></i>
                    </a>` : ''
                } 
                <button onclick="logout()" class="text-red-500 border border-red-200 px-3 py-1 rounded-full text-xs font-bold hover:bg-red-50 transition">লগআউট</button>
            </div>
        `;
    } else {
        // যদি ইউজার লগআউট থাকে
        html = `
            <div class="flex gap-2">
                <a href="login.php" class="px-3 py-1.5 text-sm font-bold text-gray-600 hover:text-green-700 transition">লগিন</a>
                <a href="register.php" class="px-4 py-1.5 text-sm font-bold text-white bg-green-600 rounded-full shadow hover:bg-green-700 transition transform hover:scale-105">রেজিস্টার</a>
            </div>
        `;
    }

    if (desk) desk.innerHTML = html;
    if (mob) mob.innerHTML = html;
}

// লগআউট ফাংশন
async function logout() {
    try {
        await fetch('api.php?action=logout');
        window.location.href = 'index.php'; // হোমপেজে রিডাইরেক্ট
    } catch (e) {
        console.error("Logout failed:", e);
    }
}

// ==========================================
// 3. Cart Logic
// ==========================================

function addToCart(product) {
    // বাটন এনিমেশন
    const cartBtn = document.querySelector('button[onclick="toggleCart()"]');
    if(cartBtn) {
        cartBtn.classList.add('scale-125', 'text-green-600');
        setTimeout(() => cartBtn.classList.remove('scale-125', 'text-green-600'), 200);
    }

    // কার্টে পণ্য চেক করা
    const exist = cart.find(x => x.id == product.id);
    if (exist) {
        exist.qty++;
    } else {
        cart.push({...product, qty: 1});
    }
    
    saveCart();
    showToast('পণ্যটি কার্টে যুক্ত হয়েছে! 🛒');
    toggleCart(); // অপশনাল: কার্ট ওপেন হবে
}

function saveCart() {
    localStorage.setItem('cart', JSON.stringify(cart));
    updateCartCount();
    renderCartItems();
}

function updateCartCount() { 
    const els = document.querySelectorAll('#cart-count');
    els.forEach(el => {
        const count = cart.reduce((a, b) => a + b.qty, 0);
        el.innerText = count;
        if (count > 0) el.classList.remove('hidden');
        else el.classList.add('hidden');
    });
}

function renderCartItems() {
    const el = document.getElementById('cart-items');
    const totalEl = document.getElementById('cart-total');
    if (!el) return;

    if (cart.length === 0) {
        el.innerHTML = `
            <div class="flex flex-col items-center justify-center h-64 text-gray-400">
                <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                    <i class="fa-solid fa-basket-shopping text-4xl opacity-30"></i>
                </div>
                <p class="font-medium">আপনার ঝুড়ি খালি</p>
                <a href="shop.php" onclick="toggleCart()" class="text-green-600 text-sm mt-2 font-bold hover:underline">কেনাকাটা শুরু করুন</a>
            </div>`;
        if (totalEl) totalEl.innerText = '৳ ০';
        return;
    }

    el.innerHTML = cart.map(i => `
        <div class="flex gap-3 items-center bg-white border border-gray-100 p-3 rounded-lg hover:shadow-sm transition">
            <div class="w-14 h-14 bg-gray-50 rounded-md flex items-center justify-center shrink-0">
                <img src="${i.image}" class="h-full object-contain" onerror="this.src='https://placehold.co/50'">
            </div>
            <div class="flex-1 min-w-0">
                <h4 class="font-bold text-sm text-gray-800 truncate">${i.name}</h4>
                <div class="flex items-center gap-2 mt-1">
                    <span class="text-xs text-gray-500 bg-gray-100 px-2 py-0.5 rounded">${i.qty} x ৳${i.price}</span>
                </div>
            </div>
            <div class="text-right">
                <p class="font-bold text-green-700 text-sm">৳${i.price * i.qty}</p>
                <button onclick="removeFromCart(${i.id})" class="text-red-400 hover:text-red-600 text-xs mt-1 p-1"><i class="fa-solid fa-trash"></i></button>
            </div>
        </div>
    `).join('');
    
    if (totalEl) totalEl.innerText = '৳ ' + cart.reduce((a, b) => a + b.price * b.qty, 0);
}

function removeFromCart(id) {
    cart = cart.filter(x => x.id !== id);
    saveCart();
}

function toggleCart() {
    const sb = document.getElementById('cart-sidebar');
    const ov = document.getElementById('cart-overlay');
    if(sb && ov) {
        sb.classList.toggle('translate-x-full');
        ov.classList.toggle('hidden');
    }
}

// ==========================================
// 4. Other Utilities (Search, Wishlist, Toast)
// ==========================================

// মোবাইল মেনু টগল
function toggleMobileMenu() {
    const mm = document.getElementById('mobile-menu');
    const mo = document.getElementById('menu-overlay');
    if(mm && mo) {
        mm.classList.toggle('-translate-x-full');
        mo.classList.toggle('hidden');
    }
}

// সার্চ হ্যান্ডলার
function handleSearch(q) {
    if (q.trim()) {
        window.location.href = `shop.php?search=${encodeURIComponent(q.trim())}`;
    }
}

// উইশলিস্ট টগল
function toggleWishlist(id) {
    if (wishlist.includes(id)) {
        wishlist = wishlist.filter(x => x !== id);
        showToast('উইশলিস্ট থেকে সরানো হয়েছে 💔');
    } else {
        wishlist.push(id);
        showToast('উইশলিস্টে যুক্ত হয়েছে ❤️');
    }
    localStorage.setItem('wishlist', JSON.stringify(wishlist));
    updateWishlistCount();
    
    // যদি উইশলিস্ট পেজে থাকে তবে রিলোড দিবে
    if (window.location.pathname.includes('wishlist.php')) location.reload();
}

function updateWishlistCount() {
    const el = document.getElementById('wishlist-count');
    if(el) el.innerText = wishlist.length;
}

// টোস্ট নোটিফিকেশন
function showToast(msg, err = false) {
    const d = document.createElement('div');
    d.className = `fixed bottom-10 left-1/2 -translate-x-1/2 px-6 py-3 rounded-full shadow-2xl text-white text-sm font-bold z-[100] flex items-center gap-3 transition-all duration-300 transform translate-y-10 opacity-0 ${err ? 'bg-red-600' : 'bg-gray-800'}`;
    d.innerHTML = `<i class="fa-solid ${err ? 'fa-circle-exclamation' : 'fa-circle-check'} text-lg"></i> ${msg}`;
    document.body.appendChild(d);
    
    // এনিমেশন
    requestAnimationFrame(() => {
        d.classList.remove('translate-y-10', 'opacity-0');
    });

    setTimeout(() => {
        d.classList.add('translate-y-10', 'opacity-0');
        setTimeout(() => d.remove(), 300);
    }, 2500);
}
