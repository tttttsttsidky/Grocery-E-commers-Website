<?php
// সেশন স্টার্ট করা জরুরি, না হলে $_SESSION কাজ করবে না এবং লগিন কাজ করবে না
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ন্যাচারাল বাস্কেট</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <link href="https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <script src="script.js" defer></script> 

    <style>
        body { font-family: 'Hind Siliguri', sans-serif; background-color: #f8fafc; overflow-x: hidden; }
        
        /* Smooth Scroll & Selection */
        html { scroll-behavior: smooth; }
        ::selection { background: #166534; color: white; }
        
        /* Glass Navigation */
        .glass-nav { background: rgba(255, 255, 255, 0.9); backdrop-filter: blur(12px); border-bottom: 1px solid rgba(0,0,0,0.05); }
        
        /* Active Link Style */
        .nav-link { position: relative; color: #4b5563; transition: all 0.3s ease; }
        .nav-link:hover, .nav-link.active { color: #15803d; font-weight: 600; }
        .nav-link::after { content: ''; position: absolute; width: 0; height: 2px; bottom: -4px; left: 0; background-color: #15803d; transition: width 0.3s; }
        .nav-link:hover::after, .nav-link.active::after { width: 100%; }

        /* Animations */
        .fade-in { animation: fadeIn 0.5s ease-out forwards; }
        .slide-in-right { animation: slideInRight 0.3s ease-out forwards; }
        
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes slideInRight { from { transform: translateX(100%); } to { transform: translateX(0); } }

        /* Custom Scrollbar */
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: #f1f5f9; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }

        /* Loader */
        .loader { border: 3px solid #f3f3f3; border-top: 3px solid #166534; border-radius: 50%; width: 30px; height: 30px; animation: spin 0.8s linear infinite; }
        @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
    </style>
</head>
<body class="text-gray-800 flex flex-col min-h-screen">

    <div class="bg-green-900 text-white text-[11px] md:text-xs py-2 text-center font-medium tracking-wide z-50 relative">
        🌿 প্রথম অর্ডারে ১০% ছাড়! কোড: <span class="bg-white/20 px-1 rounded">SAVE10</span> | হেল্পলাইন: ০১৭১১-০০০০০০
    </div>

    <nav class="glass-nav sticky top-0 z-40 transition-all duration-300" id="navbar">
        <div class="container mx-auto px-4 py-3">
            <div class="flex justify-between items-center gap-4">
                
                <div class="flex items-center gap-4">
                    <button onclick="toggleMobileMenu()" class="md:hidden text-2xl text-gray-600 hover:text-green-700 focus:outline-none transition">
                        <i class="fa-solid fa-bars"></i>
                    </button>
                    <a href="index.php" class="flex items-center gap-2 group">
                        <div class="w-9 h-9 bg-green-100 rounded-full flex items-center justify-center text-green-600 text-lg group-hover:bg-green-600 group-hover:text-white transition duration-300">
                            <i class="fa-solid fa-leaf"></i>
                        </div>
                        <div>
                            <h1 class="text-2xl font-bold text-green-800 leading-none group-hover:text-green-600 transition">ন্যাচারাল</h1>
                        </div>
                    </a>
                </div>

                <div class="hidden md:flex flex-1 max-w-lg relative group">
                    <input type="text" id="deskSearch" placeholder="পণ্য খুঁজুন..." 
                           class="w-full border border-gray-300 rounded-full py-2.5 px-5 bg-gray-50 focus:bg-white focus:outline-none focus:border-green-600 focus:ring-2 focus:ring-green-100 transition text-sm"
                           onkeyup="if(event.key === 'Enter') handleSearch(this.value)">
                    <button onclick="handleSearch(document.getElementById('deskSearch').value)" 
                            class="absolute right-1.5 top-1.5 bg-green-600 text-white w-8 h-8 rounded-full flex items-center justify-center hover:bg-green-700 transition shadow-md transform group-hover:scale-105">
                        <i class="fa-solid fa-search text-xs"></i>
                    </button>
                </div>

                <div class="flex items-center gap-5">
                    <div id="auth-section" class="hidden md:block text-sm font-medium"></div>
                    
                    <a href="wishlist.php" class="relative text-gray-500 hover:text-red-500 transition transform hover:scale-110" title="Wishlist">
                        <i class="fa-regular fa-heart text-2xl"></i>
                        <span id="wishlist-count" class="absolute -top-1.5 -right-2 bg-red-500 text-white text-[10px] font-bold rounded-full h-4 w-4 flex items-center justify-center shadow-sm border-2 border-white">0</span>
                    </a>
                    
                    <button onclick="toggleCart()" class="relative text-gray-500 hover:text-green-700 transition transform hover:scale-110" title="Cart">
                        <i class="fa-solid fa-basket-shopping text-2xl"></i>
                        <span id="cart-count" class="absolute -top-1.5 -right-2 bg-green-600 text-white text-[10px] font-bold rounded-full h-4 w-4 flex items-center justify-center shadow-sm border-2 border-white">0</span>
                    </button>
                </div>
            </div>

            <div class="md:hidden mt-3 relative">
                <input type="text" id="mobSearch" placeholder="কি খুঁজছেন?..." 
                       class="w-full border rounded-lg py-2 px-4 text-sm bg-gray-50 focus:outline-none focus:border-green-500"
                       onkeyup="if(event.key === 'Enter') handleSearch(this.value)">
                <i class="fa-solid fa-search absolute right-3 top-2.5 text-gray-400"></i>
            </div>
        </div>

        <div class="hidden md:block border-t border-gray-100 bg-white/50">
            <div class="container mx-auto px-4">
                <div class="flex justify-center space-x-8 text-sm font-medium py-3">
                    <?php 
                    $page = basename($_SERVER['PHP_SELF']); 
                    $menu = [
                        'index.php' => 'হোম',
                        'shop.php' => 'শপ',
                        'farmers.php' => 'কৃষক',
                        'blog.php' => 'ব্লগ',
                        'track.php' => 'অর্ডার ট্র্যাক',
                        'contact.php' => 'যোগাযোগ'
                    ];

                    // --- এডমিন চেক লজিক ---
                    // সেশন রোল যদি 'admin' হয়, তাহলে মেনুতে অপশন যোগ হবে
                    if(isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
                        $menu['admin.php'] = 'অ্যাডমিন প্যানেল';
                    }
                    // -----------------------

                    foreach($menu as $link => $name){
                        $active = ($page == $link) ? 'active' : '';
                        // এডমিন লিংককে হাইলাইট করার জন্য আলাদা ক্লাস
                        $style = ($link == 'admin.php') ? 'text-red-600 font-bold' : '';
                        
                        echo "<a href='$link' class='nav-link $active $style'>$name</a>";
                    }
                    ?>
                </div>
            </div>
        </div>
    </nav>

    <div id="mobile-menu" class="fixed inset-y-0 left-0 w-72 bg-white shadow-2xl transform -translate-x-full transition-transform duration-300 z-[60] flex flex-col h-full">
        <div class="p-5 bg-green-700 text-white flex justify-between items-center">
            <span class="font-bold text-lg flex items-center gap-2"><i class="fa-solid fa-bars"></i> মেনু</span>
            <button onclick="toggleMobileMenu()" class="w-8 h-8 bg-white/20 rounded-full flex items-center justify-center hover:bg-white/30 transition"><i class="fa-solid fa-times"></i></button>
        </div>
        <div class="flex-1 overflow-y-auto p-4 space-y-2 text-sm font-medium text-gray-700">
            <div id="mobile-auth" class="mb-6 pb-4 border-b border-gray-100"></div>
            
            <a href="index.php" class="block py-3 px-4 rounded-lg hover:bg-green-50 hover:text-green-700 transition flex items-center gap-3"><i class="fa-solid fa-house opacity-60"></i> হোম</a>
            <a href="shop.php" class="block py-3 px-4 rounded-lg hover:bg-green-50 hover:text-green-700 transition flex items-center gap-3"><i class="fa-solid fa-store opacity-60"></i> শপ</a>
            <a href="farmers.php" class="block py-3 px-4 rounded-lg hover:bg-green-50 hover:text-green-700 transition flex items-center gap-3"><i class="fa-solid fa-user-group opacity-60"></i> কৃষক</a>
            <a href="blog.php" class="block py-3 px-4 rounded-lg hover:bg-green-50 hover:text-green-700 transition flex items-center gap-3"><i class="fa-solid fa-newspaper opacity-60"></i> ব্লগ</a>
            <a href="track.php" class="block py-3 px-4 rounded-lg hover:bg-green-50 hover:text-green-700 transition flex items-center gap-3"><i class="fa-solid fa-truck-fast opacity-60"></i> অর্ডার ট্র্যাক</a>
            <a href="contact.php" class="block py-3 px-4 rounded-lg hover:bg-green-50 hover:text-green-700 transition flex items-center gap-3"><i class="fa-solid fa-envelope opacity-60"></i> যোগাযোগ</a>

            <?php if(isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                <a href="admin.php" class="block py-3 px-4 rounded-lg bg-red-50 text-red-600 hover:bg-red-100 transition flex items-center gap-3 font-bold"><i class="fa-solid fa-shield-halved"></i> অ্যাডমিন প্যানেল</a>
            <?php endif; ?>

        </div>
    </div>
    <div id="menu-overlay" onclick="toggleMobileMenu()" class="fixed inset-0 bg-black/50 hidden z-[55] backdrop-blur-sm transition-opacity duration-300"></div>

    <div id="cart-overlay" onclick="toggleCart()" class="fixed inset-0 bg-black/50 hidden z-[60] backdrop-blur-sm"></div>
    <div id="cart-sidebar" class="fixed top-0 right-0 h-full w-80 sm:w-96 bg-white shadow-2xl transform translate-x-full transition-transform duration-300 z-[70] flex flex-col">
        <div class="p-5 border-b flex justify-between items-center bg-green-50">
            <h3 class="font-bold text-green-800 text-lg flex items-center gap-2"><i class="fa-solid fa-bag-shopping"></i> আপনার ঝুড়ি</h3>
            <button onclick="toggleCart()" class="text-gray-400 hover:text-red-500 transition w-8 h-8 rounded-full hover:bg-white flex items-center justify-center"><i class="fa-solid fa-times text-lg"></i></button>
        </div>
        <div id="cart-items" class="flex-1 overflow-y-auto p-5 space-y-4">
            </div>
        <div class="p-5 border-t bg-white shadow-[0_-5px_15px_rgba(0,0,0,0.05)]">
            <div class="flex justify-between font-bold text-lg mb-4 text-gray-800"><span>মোট:</span> <span id="cart-total">৳ ০</span></div>
            <button onclick="window.location.href='checkout.php'" class="block w-full bg-green-600 text-white py-3.5 rounded-lg font-bold text-center hover:bg-green-700 shadow-lg transition transform hover:-translate-y-1 flex items-center justify-center gap-2">
                চেকআউট করুন <i class="fa-solid fa-arrow-right"></i>
            </button>
        </div>
    </div>