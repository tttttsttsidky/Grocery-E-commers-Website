<?php 
// সেশন স্টার্ট চেক
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include 'header.php'; 

// সিকিউরিটি চেক: ইউজার লগিন না থাকলে বা এডমিন না হলে লগিন পেজে পাঠাবে
if(!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo "<script>window.location.href='login.php';</script>"; 
    exit;
}
?>

<div class="flex min-h-screen bg-gray-50 font-sans text-gray-800">
    
    <aside class="w-64 bg-green-900 text-white hidden md:flex flex-col fixed h-full z-50 transition-all duration-300 shadow-2xl" id="sidebar">
        <div class="p-6 border-b border-green-800 flex justify-between items-center">
            <div>
                <h2 class="text-2xl font-bold flex items-center gap-2"><i class="fa-solid fa-shield-cat"></i> এডমিন</h2>
                <p class="text-xs text-green-300 mt-1">ন্যাচারাল বাস্কেট</p>
            </div>
            <button onclick="toggleSidebar()" class="md:hidden text-white hover:text-red-400 transition"><i class="fa-solid fa-times text-xl"></i></button>
        </div>
        
        <nav class="flex-1 p-4 space-y-2 overflow-y-auto custom-scrollbar">
            <button onclick="showSection('dashboard')" class="w-full text-left px-4 py-3 rounded hover:bg-green-800 transition flex items-center gap-3 focus:bg-green-800 section-btn active-btn bg-green-800">
                <i class="fa-solid fa-chart-pie w-5"></i> ড্যাশবোর্ড
            </button>
            <button onclick="showSection('orders')" class="w-full text-left px-4 py-3 rounded hover:bg-green-800 transition flex items-center gap-3 focus:bg-green-800 section-btn">
                <i class="fa-solid fa-box w-5"></i> অর্ডারসমূহ
            </button>
            <button onclick="showSection('products')" class="w-full text-left px-4 py-3 rounded hover:bg-green-800 transition flex items-center gap-3 focus:bg-green-800 section-btn">
                <i class="fa-solid fa-tags w-5"></i> পণ্য ম্যানেজমেন্ট
            </button>
            <button onclick="showSection('customers')" class="w-full text-left px-4 py-3 rounded hover:bg-green-800 transition flex items-center gap-3 focus:bg-green-800 section-btn">
                <i class="fa-solid fa-users w-5"></i> কাস্টমার লিস্ট
            </button>
            <button onclick="showSection('blogs')" class="w-full text-left px-4 py-3 rounded hover:bg-green-800 transition flex items-center gap-3 focus:bg-green-800 section-btn">
                <i class="fa-solid fa-newspaper w-5"></i> ব্লগ ম্যানেজমেন্ট
            </button>
        </nav>
        
        <div class="p-4 border-t border-green-800">
            <a href="index.php" class="block text-center bg-green-700 py-2 rounded hover:bg-green-600 text-sm font-bold shadow-lg transition transform hover:scale-105">
                <i class="fa-solid fa-globe"></i> ওয়েবসাইটে যান
            </a>
        </div>
    </aside>

    <div class="md:hidden fixed w-full bg-green-900 text-white p-4 z-40 flex justify-between items-center shadow-md top-0 left-0">
        <span class="font-bold text-lg flex items-center gap-2"><i class="fa-solid fa-leaf"></i> প্যানেল</span>
        <button onclick="toggleSidebar()" class="text-2xl focus:outline-none"><i class="fa-solid fa-bars"></i></button>
    </div>

    <main class="flex-1 md:ml-64 p-4 md:p-8 pt-24 md:pt-8 transition-all min-h-screen">
        
        <div id="sec-dashboard" class="section-content fade-in">
            <h2 class="text-2xl font-bold text-gray-800 mb-6 border-b pb-2">ওভারভিউ</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8" id="stats-container">
                <div class="bg-white p-6 rounded-xl shadow-sm border animate-pulse h-32"></div>
                <div class="bg-white p-6 rounded-xl shadow-sm border animate-pulse h-32"></div>
                <div class="bg-white p-6 rounded-xl shadow-sm border animate-pulse h-32"></div>
            </div>
        </div>

        <div id="sec-orders" class="section-content hidden fade-in">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold text-gray-800">অর্ডার তালিকা</h2>
                <button onclick="loadAdminData()" class="text-sm text-blue-600 hover:text-blue-800 bg-white border border-blue-200 px-4 py-2 rounded-full transition shadow-sm flex items-center gap-2">
                    <i class="fa-solid fa-rotate"></i> রিফ্রেশ
                </button>
            </div>
            <div class="bg-white rounded-xl shadow-sm border overflow-hidden">
                <div class="overflow-x-auto" id="orders-table">
                    <div class="p-10 text-center text-gray-400">লোড হচ্ছে...</div>
                </div>
            </div>
        </div>

        <div id="sec-products" class="section-content hidden fade-in">
            <h2 class="text-2xl font-bold text-gray-800 mb-6">পণ্য ম্যানেজমেন্ট</h2>
            
            <div class="bg-white p-6 rounded-xl shadow-sm border mb-8">
                <h3 class="font-bold mb-4 text-gray-700 border-b pb-2 flex items-center gap-2"><i class="fa-solid fa-plus-circle text-green-600"></i> নতুন পণ্য যুক্ত করুন</h3>
                <form onsubmit="addProduct(event)" class="grid md:grid-cols-2 gap-6" enctype="multipart/form-data">
                    <div class="space-y-4">
                        <input name="name" placeholder="পণ্যের নাম" class="border p-3 w-full rounded text-sm focus:border-green-500 outline-none transition" required>
                        <div class="grid grid-cols-2 gap-3">
                            <input name="category" placeholder="ক্যাটাগরি" class="border p-3 w-full rounded text-sm" required>
                            <input name="price" type="number" placeholder="দাম (৳)" class="border p-3 w-full rounded text-sm" required>
                        </div>
                        <input name="weight" placeholder="ওজন (e.g. 1kg)" class="border p-3 w-full rounded text-sm" required>
                        <div class="grid grid-cols-2 gap-3">
                             <input name="farmer_name" placeholder="কৃষকের নাম (Optional)" class="border p-3 w-full rounded text-sm">
                             <input name="farmer_loc" placeholder="এলাকা (Optional)" class="border p-3 w-full rounded text-sm">
                        </div>
                    </div>
                    <div class="space-y-4">
                        <textarea name="description" placeholder="বিস্তারিত বিবরণ..." class="border p-3 w-full rounded text-sm h-24 focus:border-green-500 outline-none"></textarea>
                        <div class="border p-3 rounded bg-gray-50 text-sm border-dashed border-gray-300 hover:bg-gray-100 transition relative">
                            <span class="text-xs text-gray-500 block mb-2 font-bold text-center"><i class="fa-solid fa-cloud-arrow-up text-2xl mb-1"></i><br>ছবি আপলোড করুন</span>
                            <input type="file" name="image" accept="image/*" class="absolute inset-0 opacity-0 cursor-pointer w-full h-full">
                        </div>
                        <button class="bg-green-700 text-white w-full py-3 rounded font-bold text-sm hover:bg-green-800 transition shadow-lg flex justify-center gap-2">
                            <i class="fa-solid fa-save"></i> পণ্য সেভ করুন
                        </button>
                    </div>
                </form>
            </div>

            <div class="bg-white rounded-xl shadow-sm border overflow-hidden">
                <div class="px-6 py-4 bg-gray-50 border-b font-bold text-gray-700 uppercase text-xs flex justify-between items-center">
                    <span>সব পণ্যের তালিকা</span>
                    <button class="text-green-600 hover:text-green-800" onclick="loadAdminData()"><i class="fa-solid fa-rotate"></i></button>
                </div>
                <div id="products-table" class="overflow-x-auto">
                    <div class="p-10 text-center text-gray-400">লোড হচ্ছে...</div>
                </div>
            </div>
        </div>

        <div id="sec-customers" class="section-content hidden fade-in">
            <h2 class="text-2xl font-bold text-gray-800 mb-6">নিবন্ধিত কাস্টমার</h2>
            <div class="bg-white rounded-xl shadow-sm border overflow-hidden">
                <div id="customers-table" class="overflow-x-auto">
                    <div class="p-10 text-center text-gray-400">লোড হচ্ছে...</div>
                </div>
            </div>
        </div>

        <div id="sec-blogs" class="section-content hidden fade-in">
            <h2 class="text-2xl font-bold text-gray-800 mb-6">ব্লগ ম্যানেজমেন্ট</h2>
            
            <div class="bg-white p-6 rounded-xl shadow-sm border mb-8">
                <h3 class="font-bold mb-4 text-gray-700 border-b pb-2 flex items-center gap-2"><i class="fa-solid fa-pen-nib text-purple-600"></i> নতুন ব্লগ লিখুন</h3>
                <form onsubmit="addBlog(event)" class="grid gap-4" enctype="multipart/form-data">
                    <div class="grid md:grid-cols-2 gap-4">
                        <input name="title" placeholder="ব্লগের শিরোনাম" class="border p-3 w-full rounded text-sm focus:border-purple-500 outline-none" required>
                        <input name="author" placeholder="লেখকের নাম" class="border p-3 w-full rounded text-sm" required>
                    </div>
                    <textarea name="content" placeholder="বিস্তারিত লিখুন..." class="border p-3 w-full rounded text-sm h-32 focus:border-purple-500 outline-none" required></textarea>
                    <div class="border p-3 rounded bg-gray-50 text-sm border-dashed border-gray-300 relative hover:bg-purple-50 transition">
                        <span class="text-xs text-gray-500 block mb-2 font-bold text-center"><i class="fa-solid fa-image text-xl"></i> কভার ছবি আপলোড</span>
                        <input type="file" name="image" accept="image/*" class="absolute inset-0 opacity-0 cursor-pointer w-full h-full">
                    </div>
                    <button class="bg-purple-700 text-white w-full py-3 rounded font-bold text-sm hover:bg-purple-800 transition shadow-lg">পোস্ট করুন</button>
                </form>
            </div>

            <div class="bg-white rounded-xl shadow-sm border overflow-hidden">
                <div class="px-6 py-4 bg-gray-50 border-b font-bold text-gray-700 uppercase text-xs flex justify-between items-center">
                    <span>প্রকাশিত ব্লগসমূহ</span>
                    <button class="text-purple-600 hover:text-purple-800" onclick="loadBlogs()"><i class="fa-solid fa-rotate"></i></button>
                </div>
                <div id="blogs-table" class="overflow-x-auto">
                    <div class="p-10 text-center text-gray-400">লোড হচ্ছে...</div>
                </div>
            </div>
        </div>

    </main>
</div>

<div id="order-modal" class="fixed inset-0 bg-black bg-opacity-60 hidden z-[100] flex items-center justify-center px-4 backdrop-blur-sm transition-opacity">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-2xl overflow-hidden">
        <div class="bg-green-800 text-white p-4 flex justify-between items-center">
            <h3 class="font-bold text-lg flex items-center gap-2"><i class="fa-solid fa-receipt"></i> অর্ডার ডিটেইলস</h3>
            <button onclick="closeModal('order-modal')" class="text-white hover:text-red-200 text-2xl transition">&times;</button>
        </div>
        <div id="order-modal-content" class="p-6 max-h-[80vh] overflow-y-auto bg-gray-50"></div>
    </div>
</div>

<div id="edit-product-modal" class="fixed inset-0 bg-black bg-opacity-60 hidden z-[100] flex items-center justify-center px-4 backdrop-blur-sm transition-opacity">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg overflow-hidden">
        <div class="bg-blue-700 text-white p-4 flex justify-between items-center">
            <h3 class="font-bold text-lg flex items-center gap-2"><i class="fa-solid fa-pen-to-square"></i> পণ্য এডিট করুন</h3>
            <button onclick="closeModal('edit-product-modal')" class="text-white hover:text-red-200 text-2xl">&times;</button>
        </div>
        <div class="p-6 max-h-[80vh] overflow-y-auto">
            <form onsubmit="submitEditProduct(event)" id="edit-form" class="space-y-4">
                <input type="hidden" name="id" id="edit-id">
                <div>
                    <label class="text-xs font-bold text-gray-600 block mb-1">পণ্যের নাম</label>
                    <input name="name" id="edit-name" class="border p-2 w-full rounded text-sm border-gray-300" required>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div><label class="text-xs font-bold text-gray-600 block mb-1">ক্যাটাগরি</label><input name="category" id="edit-category" class="border p-2 w-full rounded text-sm border-gray-300" required></div>
                    <div><label class="text-xs font-bold text-gray-600 block mb-1">দাম (৳)</label><input name="price" type="number" id="edit-price" class="border p-2 w-full rounded text-sm border-gray-300" required></div>
                </div>
                <div><label class="text-xs font-bold text-gray-600 block mb-1">ওজন</label><input name="weight" id="edit-weight" class="border p-2 w-full rounded text-sm border-gray-300" required></div>
                <div><label class="text-xs font-bold text-gray-600 block mb-1">বিবরণ</label><textarea name="description" id="edit-desc" class="border p-2 w-full rounded text-sm h-20 border-gray-300"></textarea></div>
                <div class="border p-3 rounded bg-gray-50 text-sm"><span class="text-xs text-gray-500 block mb-1 font-bold">নতুন ছবি (ঐচ্ছিক):</span><input type="file" name="image" accept="image/*" class="w-full text-xs"></div>
                <button class="bg-blue-600 text-white w-full py-3 rounded font-bold text-sm hover:bg-blue-700 transition shadow">আপডেট করুন</button>
            </form>
        </div>
    </div>
</div>

<div id="edit-blog-modal" class="fixed inset-0 bg-black bg-opacity-60 hidden z-[100] flex items-center justify-center px-4 backdrop-blur-sm transition-opacity">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg overflow-hidden">
        <div class="bg-purple-700 text-white p-4 flex justify-between items-center">
            <h3 class="font-bold text-lg flex items-center gap-2"><i class="fa-solid fa-pen-nib"></i> ব্লগ এডিট করুন</h3>
            <button onclick="closeModal('edit-blog-modal')" class="text-white hover:text-red-200 text-2xl">&times;</button>
        </div>
        <div class="p-6 max-h-[80vh] overflow-y-auto">
            <form onsubmit="submitEditBlog(event)" id="edit-blog-form" class="space-y-4">
                <input type="hidden" name="id" id="edit-blog-id">
                <div><label class="text-xs font-bold text-gray-600 block mb-1">শিরোনাম</label><input name="title" id="edit-blog-title" class="border p-2 w-full rounded text-sm border-gray-300" required></div>
                <div><label class="text-xs font-bold text-gray-600 block mb-1">লেখক</label><input name="author" id="edit-blog-author" class="border p-2 w-full rounded text-sm border-gray-300" required></div>
                <div><label class="text-xs font-bold text-gray-600 block mb-1">বিবরণ</label><textarea name="content" id="edit-blog-content" class="border p-2 w-full rounded text-sm h-32 border-gray-300"></textarea></div>
                <div class="border p-3 rounded bg-gray-50 text-sm"><span class="text-xs text-gray-500 block mb-1 font-bold">নতুন ছবি (ঐচ্ছিক):</span><input type="file" name="image" accept="image/*" class="w-full text-xs"></div>
                <button class="bg-purple-600 text-white w-full py-3 rounded font-bold text-sm hover:bg-purple-700 transition shadow">আপডেট করুন</button>
            </form>
        </div>
    </div>
</div>

<script>
    // --- ইউটিলিটি ফাংশন ---
    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        sidebar.classList.toggle('hidden');
        sidebar.classList.toggle('flex');
    }

    function closeModal(id) { document.getElementById(id).classList.add('hidden'); }
    
    // স্ট্যাটাস কালার সেট করা
    function getStatusColor(status) {
        if(status === 'Pending') return 'bg-orange-100 text-orange-800';
        if(status === 'Processing') return 'bg-blue-100 text-blue-800';
        if(status === 'Shipped') return 'bg-purple-100 text-purple-800';
        if(status === 'Delivered') return 'bg-green-100 text-green-800';
        return 'bg-gray-100 text-gray-800';
    }

    // --- পেজ লোড হলে ---
    document.addEventListener('DOMContentLoaded', () => {
        loadAdminData();
        if(window.innerWidth < 768) {
            document.getElementById('sidebar').classList.add('hidden');
            document.getElementById('sidebar').classList.remove('flex');
        }
    });

    // --- নেভিগেশন ---
    function showSection(id) {
        document.querySelectorAll('.section-content').forEach(el => el.classList.add('hidden'));
        document.getElementById(`sec-${id}`).classList.remove('hidden');
        
        // বাটন একটিভ স্টাইল
        document.querySelectorAll('.section-btn').forEach(btn => {
            btn.classList.remove('bg-green-800', 'active-btn');
        });
        event.currentTarget.classList.add('bg-green-800', 'active-btn');
        
        // মোবাইল মেনু বন্ধ
        if(window.innerWidth < 768) toggleSidebar();
        
        // ডাটা রিফ্রেশ
        if(id === 'blogs') loadBlogs();
        if(['orders', 'products', 'customers', 'dashboard'].includes(id)) loadAdminData();
    }

    // --- মেইন ডাটা লোডার (Dashboard, Orders, Products, Users) ---
    async function loadAdminData() {
        try {
            const [ordersRes, productsRes, usersRes] = await Promise.all([
                fetch('api.php?action=get_orders'),
                fetch('api.php?action=get_products'),
                fetch('api.php?action=get_users')
            ]);

            const orders = await ordersRes.json();
            const products = await productsRes.json();
            const users = await usersRes.json();

            // Dashboard Stats
            const totalSales = orders.reduce((acc, o) => acc + parseFloat(o.total_amount), 0);
            const pending = orders.filter(o => o.status === 'Pending').length;

            document.getElementById('stats-container').innerHTML = `
                <div class="bg-white p-6 rounded-xl shadow-sm border-l-4 border-blue-500 flex items-center justify-between transform hover:scale-105 transition duration-300">
                    <div><h3 class="text-gray-500 text-xs font-bold uppercase tracking-wider">মোট অর্ডার</h3><p class="text-3xl font-bold mt-1 text-gray-800">${orders.length}</p><span class="text-xs text-orange-500 font-medium bg-orange-100 px-2 py-0.5 rounded">${pending} পেনন্ডিং</span></div>
                    <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 shadow-sm"><i class="fa-solid fa-cart-shopping text-xl"></i></div>
                </div>
                <div class="bg-white p-6 rounded-xl shadow-sm border-l-4 border-green-500 flex items-center justify-between transform hover:scale-105 transition duration-300">
                    <div><h3 class="text-gray-500 text-xs font-bold uppercase tracking-wider">মোট বিক্রি</h3><p class="text-3xl font-bold mt-1 text-gray-800">৳ ${totalSales.toLocaleString()}</p></div>
                    <div class="w-12 h-12 rounded-full bg-green-100 flex items-center justify-center text-green-600 shadow-sm"><i class="fa-solid fa-chart-line text-xl"></i></div>
                </div>
                <div class="bg-white p-6 rounded-xl shadow-sm border-l-4 border-yellow-500 flex items-center justify-between transform hover:scale-105 transition duration-300">
                    <div><h3 class="text-gray-500 text-xs font-bold uppercase tracking-wider">কাস্টমার</h3><p class="text-3xl font-bold mt-1 text-gray-800">${users.length || 0}</p></div>
                    <div class="w-12 h-12 rounded-full bg-yellow-100 flex items-center justify-center text-yellow-600 shadow-sm"><i class="fa-solid fa-users text-xl"></i></div>
                </div>`;

            // Orders Table
            document.getElementById('orders-table').innerHTML = `
                <table class="w-full text-left text-sm whitespace-nowrap">
                    <thead class="bg-gray-100 text-xs uppercase text-gray-600 sticky top-0"><tr><th class="p-4">অর্ডার ID</th><th class="p-4">কাস্টমার</th><th class="p-4">পরিমাণ</th><th class="p-4">স্ট্যাটাস</th><th class="p-4 text-right">অ্যাকশন</th></tr></thead>
                    <tbody class="divide-y divide-gray-100">${orders.length ? orders.map(o => `
                        <tr class="hover:bg-gray-50 transition">
                            <td class="p-4 font-mono text-gray-500">#${o.id}</td>
                            <td class="p-4"><div class="font-bold text-gray-800">${o.customer_name}</div><div class="text-xs text-gray-500">${o.phone}</div></td>
                            <td class="p-4 font-bold text-green-700">৳${o.total_amount}</td>
                            <td class="p-4"><span class="px-2.5 py-1 rounded-full text-xs font-bold ${getStatusColor(o.status)}">${o.status}</span></td>
                            <td class="p-4 text-right flex justify-end gap-2">
                                <button onclick="viewOrder(${o.id})" class="text-blue-600 bg-blue-50 w-8 h-8 rounded-full hover:bg-blue-100 flex items-center justify-center transition shadow-sm" title="বিস্তারিত"><i class="fa-solid fa-eye"></i></button>
                                <button onclick="deleteOrder(${o.id})" class="text-red-600 bg-red-50 w-8 h-8 rounded-full hover:bg-red-100 flex items-center justify-center transition shadow-sm" title="ডিলিট"><i class="fa-solid fa-trash"></i></button>
                            </td>
                        </tr>`).join('') : '<tr><td colspan="5" class="p-6 text-center text-gray-500">কোনো অর্ডার নেই</td></tr>'}</tbody>
                </table>`;

            // Products Table
            document.getElementById('products-table').innerHTML = `
                <table class="w-full text-left text-sm">
                    <thead class="bg-gray-100 text-xs uppercase text-gray-600"><tr><th class="p-4">ছবি</th><th class="p-4">নাম</th><th class="p-4">দাম</th><th class="p-4 text-right">অ্যাকশন</th></tr></thead>
                    <tbody class="divide-y divide-gray-100">${products.length ? products.map(p => `
                        <tr class="hover:bg-gray-50 transition">
                            <td class="p-4"><img src="${p.image}" class="w-12 h-12 object-cover rounded-lg border bg-white shadow-sm" onerror="this.src='https://placehold.co/50'"></td>
                            <td class="p-4"><div class="font-bold text-gray-800">${p.name}</div><div class="text-xs text-gray-500 bg-gray-100 inline-block px-1.5 rounded mt-1">${p.category}</div></td>
                            <td class="p-4 font-bold text-green-700">৳${p.price}</td>
                            <td class="p-4 text-right">
                                <button onclick='openEditProduct(${JSON.stringify(p).replace(/'/g, "&#39;")})' class="text-blue-600 hover:text-blue-800 bg-blue-50 px-3 py-1.5 rounded text-xs font-bold mr-2 transition"><i class="fa-solid fa-pen"></i> এডিট</button>
                                <button onclick="deleteProduct(${p.id})" class="text-red-600 hover:text-red-800 bg-red-50 px-3 py-1.5 rounded text-xs font-bold transition"><i class="fa-solid fa-trash"></i> ডিলিট</button>
                            </td>
                        </tr>`).join('') : '<tr><td colspan="4" class="p-6 text-center text-gray-500">কোনো পণ্য নেই</td></tr>'}</tbody>
                </table>`;

            // Customers Table
            document.getElementById('customers-table').innerHTML = `
                <table class="w-full text-left text-sm whitespace-nowrap">
                    <thead class="bg-gray-100 text-xs uppercase text-gray-600"><tr><th class="p-4">নাম</th><th class="p-4">ইমেইল</th><th class="p-4">ফোন</th><th class="p-4">রোল</th><th class="p-4 text-right">অ্যাকশন</th></tr></thead>
                    <tbody class="divide-y divide-gray-100">${users.length ? users.map(u => `
                        <tr class="hover:bg-gray-50 transition">
                            <td class="p-4 font-bold text-gray-800">${u.name}</td>
                            <td class="p-4 text-gray-600">${u.email}</td>
                            <td class="p-4 text-gray-600">${u.phone || '-'}</td>
                            <td class="p-4"><span class="px-2 py-1 rounded text-xs font-bold ${u.role==='admin'?'bg-purple-100 text-purple-800':'bg-green-100 text-green-800'}">${u.role}</span></td>
                            <td class="p-4 text-right">
                                ${u.role !== 'admin' ? `<button onclick="deleteUser(${u.id})" class="text-red-600 bg-red-50 px-3 py-1 rounded text-xs font-bold hover:bg-red-100 transition"><i class="fa-solid fa-ban"></i> ডিলিট</button>` : ''}
                            </td>
                        </tr>`).join('') : '<tr><td colspan="5" class="p-6 text-center text-gray-500">কোনো ইউজার নেই</td></tr>'}</tbody>
                </table>`;

        } catch (e) { console.error(e); }
    }

    // --- PRODUCT FUNCTIONS ---
    async function addProduct(e) {
        e.preventDefault(); if(!confirm('পণ্যটি যুক্ত করতে চান?')) return;
        const btn = e.target.querySelector('button'); btn.disabled = true; btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> সেভ হচ্ছে...';
        try {
            const r = await fetch('api.php?action=add_product', { method: 'POST', body: new FormData(e.target) });
            if((await r.json()).status === 'success') { alert('সফল!'); e.target.reset(); loadAdminData(); }
        } catch(err) { alert('Error'); } finally { btn.disabled = false; btn.innerHTML = '<i class="fa-solid fa-save"></i> পণ্য সেভ করুন'; }
    }

    async function deleteProduct(id) {
        if(!confirm('সতর্কতা: পণ্যটি ডিলিট করবেন?')) return;
        await fetch('api.php?action=delete_product', { method: 'POST', body: JSON.stringify({ id }) });
        loadAdminData();
    }

    function openEditProduct(p) {
        document.getElementById('edit-product-modal').classList.remove('hidden');
        ['id','name','category','price','weight','desc'].forEach(k => {
            const el = document.getElementById(`edit-${k}`);
            if(el) el.value = p[k.replace('desc','description')] || p[k] || '';
        });
    }

    async function submitEditProduct(e) {
        e.preventDefault();
        const btn = e.target.querySelector('button'); btn.innerText = "আপডেট হচ্ছে..."; btn.disabled = true;
        try {
            const r = await fetch('api.php?action=update_product', { method: 'POST', body: new FormData(e.target) });
            if((await r.json()).status === 'success') { alert('আপডেট সফল!'); closeModal('edit-product-modal'); loadAdminData(); }
        } catch(e) { alert('Error'); } finally { btn.innerText = "আপডেট করুন"; btn.disabled = false; }
    }

    // --- ORDER FUNCTIONS ---
    async function deleteOrder(id) {
        if(!confirm('অর্ডারটি ডিলিট করবেন?')) return;
        await fetch('api.php?action=delete_order', { method: 'POST', body: JSON.stringify({ id }) });
        loadAdminData();
    }

    async function viewOrder(id) {
        document.getElementById('order-modal').classList.remove('hidden');
        const content = document.getElementById('order-modal-content');
        content.innerHTML = '<div class="text-center py-10"><div class="loader mx-auto mb-2"></div>লোডিং...</div>';
        try {
            const order = await (await fetch(`api.php?action=get_order_details&id=${id}`)).json();
            content.innerHTML = `
                <div class="grid md:grid-cols-2 gap-6 mb-6">
                    <div class="bg-white p-4 rounded border border-gray-200 shadow-sm">
                        <h4 class="font-bold text-gray-700 border-b pb-2 mb-2"><i class="fa-solid fa-user"></i> কাস্টমার</h4>
                        <p><strong>নাম:</strong> ${order.customer_name}</p>
                        <p><strong>ফোন:</strong> ${order.phone}</p>
                        <p><strong>ঠিকানা:</strong> ${order.address}</p>
                    </div>
                    <div class="bg-white p-4 rounded border border-gray-200 shadow-sm">
                        <h4 class="font-bold text-gray-700 border-b pb-2 mb-2"><i class="fa-solid fa-tasks"></i> স্ট্যাটাস</h4>
                        <div class="flex gap-2 mt-2">
                            <select id="status-${id}" class="border p-2 rounded w-full text-sm bg-gray-50">
                                <option value="Pending" ${order.status==='Pending'?'selected':''}>Pending</option>
                                <option value="Processing" ${order.status==='Processing'?'selected':''}>Processing</option>
                                <option value="Shipped" ${order.status==='Shipped'?'selected':''}>Shipped</option>
                                <option value="Delivered" ${order.status==='Delivered'?'selected':''}>Delivered</option>
                            </select>
                            <button onclick="updStatus(${id})" class="bg-green-700 text-white px-4 rounded font-bold text-sm hover:bg-green-800">Save</button>
                        </div>
                    </div>
                </div>
                <h4 class="font-bold mb-2 text-sm text-gray-600 uppercase">আইটেমসমূহ</h4>
                <div class="border rounded overflow-hidden">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-gray-100"><tr><th class="p-2">পণ্য</th><th class="p-2 text-center">পরিমাণ</th><th class="p-2 text-right">দাম</th></tr></thead>
                        <tbody>${order.items.map(i=>`<tr><td class="p-2 border-b">${i.product_name}</td><td class="p-2 border-b text-center">x${i.quantity}</td><td class="p-2 border-b text-right">৳${i.price*i.quantity}</td></tr>`).join('')}</tbody>
                        <tfoot class="bg-green-50 font-bold"><tr><td colspan="2" class="p-2 text-right">মোট:</td><td class="p-2 text-right text-green-700">৳${order.total_amount}</td></tr></tfoot>
                    </table>
                </div>`;
        } catch(e) { content.innerHTML = '<p class="text-red-500 text-center">ডাটা পাওয়া যায়নি</p>'; }
    }

    async function updStatus(id) {
        await fetch('api.php?action=update_status', { method: 'POST', body: JSON.stringify({ id, status: document.getElementById(`status-${id}`).value }) });
        closeModal('order-modal'); loadAdminData(); alert("স্ট্যাটাস আপডেট হয়েছে!");
    }

    // --- USER ACTIONS ---
    async function deleteUser(id) {
        if(!confirm('সতর্কতা: এই ইউজারকে ডিলিট করবেন?')) return;
        await fetch('api.php?action=delete_user', { method: 'POST', body: JSON.stringify({ id }) });
        loadAdminData();
    }

    // --- BLOG FUNCTIONS ---
    async function loadBlogs() {
        const c = document.getElementById('blogs-table'); 
        c.innerHTML = '<div class="p-10 text-center text-gray-400">লোড হচ্ছে...</div>';
        try {
            const blogs = await (await fetch('api.php?action=get_blogs')).json();
            c.innerHTML = `<table class="w-full text-left text-sm"><thead class="bg-gray-100 text-xs uppercase text-gray-600"><tr><th class="p-4">ছবি</th><th class="p-4">শিরোনাম</th><th class="p-4">লেখক</th><th class="p-4 text-right">অ্যাকশন</th></tr></thead><tbody class="divide-y divide-gray-100">${blogs.length ? blogs.map(b=>`
                <tr class="hover:bg-gray-50 transition">
                    <td class="p-4"><img src="${b.image}" class="w-16 h-10 object-cover rounded shadow-sm" onerror="this.src='https://placehold.co/50'"></td>
                    <td class="p-4 font-bold text-gray-800">${b.title}</td>
                    <td class="p-4 text-gray-600">${b.author}</td>
                    <td class="p-4 text-right">
                        <button onclick='openEditBlog(${JSON.stringify(b).replace(/'/g, "&#39;")})' class="text-purple-600 hover:text-purple-800 bg-purple-50 px-3 py-1.5 rounded text-xs font-bold mr-2 transition"><i class="fa-solid fa-pen"></i> এডিট</button>
                        <button onclick="deleteBlog(${b.id})" class="text-red-600 hover:text-red-800 bg-red-50 px-3 py-1.5 rounded text-xs font-bold transition"><i class="fa-solid fa-trash"></i></button>
                    </td>
                </tr>`).join('') : '<tr><td colspan="4" class="p-6 text-center text-gray-500">কোনো ব্লগ নেই</td></tr>'}</tbody></table>`;
        } catch(e) { c.innerHTML = '<div class="p-10 text-center text-red-500">এরর হয়েছে</div>'; }
    }

    async function addBlog(e) {
        e.preventDefault(); if(!confirm('ব্লগটি পোস্ট করতে চান?')) return;
        const btn = e.target.querySelector('button'); btn.innerText = "পোস্ট হচ্ছে..."; btn.disabled = true;
        try {
            const r = await fetch('api.php?action=add_blog', { method: 'POST', body: new FormData(e.target) });
            if((await r.json()).status === 'success') { alert('ব্লগ পোস্ট হয়েছে!'); e.target.reset(); loadBlogs(); }
        } catch(err) { alert('Error'); } finally { btn.innerText = "পোস্ট করুন"; btn.disabled = false; }
    }

    async function deleteBlog(id) { if(confirm('ডিলিট করবেন?')) { await fetch('api.php?action=delete_blog', { method: 'POST', body: JSON.stringify({ id }) }); loadBlogs(); } }

    function openEditBlog(b) {
        document.getElementById('edit-blog-modal').classList.remove('hidden');
        document.getElementById('edit-blog-id').value = b.id;
        document.getElementById('edit-blog-title').value = b.title;
        document.getElementById('edit-blog-author').value = b.author;
        document.getElementById('edit-blog-content').value = b.content;
    }

    async function submitEditBlog(e) {
        e.preventDefault();
        const btn = e.target.querySelector('button'); btn.innerText = "আপডেট হচ্ছে..."; btn.disabled = true;
        try {
            const r = await fetch('api.php?action=update_blog', { method: 'POST', body: new FormData(e.target) });
            if((await r.json()).status === 'success') { alert('ব্লগ আপডেট হয়েছে!'); closeModal('edit-blog-modal'); loadBlogs(); }
        } catch(e) { alert('Error'); } finally { btn.innerText = "আপডেট করুন"; btn.disabled = false; }
    }
</script>