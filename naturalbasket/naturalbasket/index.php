<?php include 'header.php'; ?>

<main class="flex-grow">
    <!-- Hero Section -->
    <section class="bg-green-900 text-white py-24 text-center relative overflow-hidden">
        <div class="absolute inset-0 bg-black opacity-20"></div>
        <div class="relative z-10 px-4">
            <span class="bg-green-700 px-3 py-1 rounded-full text-xs font-bold mb-4 inline-block">১০০% অর্গানিক</span>
            <h1 class="text-4xl md:text-6xl font-bold mb-4">সুস্থ জীবনের জন্য <br> বিশুদ্ধ খাবার</h1>
            <p class="text-green-100 mb-8 text-lg">আমরা সরাসরি কৃষকের মাঠ থেকে সেরা পণ্য সংগ্রহ করি।</p>
            <a href="shop.php" class="inline-block bg-white text-green-800 px-8 py-3 rounded-full font-bold shadow-lg hover:bg-green-50 transition transform hover:scale-105">
                শপিং করুন <i class="fa-solid fa-arrow-right ml-2"></i>
            </a>
        </div>
    </section>
    
    <!-- Features Section -->
    <section class="py-12 bg-green-50 border-b border-green-100">
        <div class="container mx-auto px-4 grid grid-cols-2 md:grid-cols-4 gap-6 text-center">
            <div class="p-6 bg-white rounded shadow hover:shadow-lg transition">
                <i class="fa-solid fa-leaf text-3xl text-green-600 mb-2"></i>
                <h3 class="font-bold">১০০% প্রাকৃতিক</h3>
            </div>
            <div class="p-6 bg-white rounded shadow hover:shadow-lg transition">
                <i class="fa-solid fa-truck-fast text-3xl text-green-600 mb-2"></i>
                <h3 class="font-bold">দ্রুত ডেলিভারি</h3>
            </div>
            <div class="p-6 bg-white rounded shadow hover:shadow-lg transition">
                <i class="fa-solid fa-check-circle text-3xl text-green-600 mb-2"></i>
                <h3 class="font-bold">বিশুদ্ধতার গ্যারান্টি</h3>
            </div>
            <div class="p-6 bg-white rounded shadow hover:shadow-lg transition">
                <i class="fa-solid fa-headset text-3xl text-green-600 mb-2"></i>
                <h3 class="font-bold">২৪/৭ সাপোর্ট</h3>
            </div>
        </div>
    </section>

    <!-- Featured Products Section -->
    <section class="container mx-auto px-4 py-16">
        <h2 class="text-3xl font-bold mb-8 text-center text-gray-800">জনপ্রিয় পণ্য</h2>
        <div id="featured-products" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6 text-center">
            <div class="col-span-full text-center py-10"><div class="loader mx-auto"></div></div>
        </div>
        <div class="text-center mt-10">
            <a href="shop.php" class="text-green-700 font-bold hover:underline text-lg">সকল পণ্য দেখুন &rarr;</a>
        </div>
    </section>
    
    <!-- Blog Teaser -->
    <section class="bg-green-50 py-16">
        <div class="container mx-auto px-4 text-center">
            <h2 class="text-3xl font-bold mb-4 text-green-900">আমাদের ব্লগে চোখ রাখুন</h2>
            <p class="text-green-800 mb-8">স্বাস্থ্য টিপস এবং অর্গানিক লাইফস্টাইল সম্পর্কে জানুন।</p>
            <a href="blog.php" class="bg-green-700 text-white px-6 py-2 rounded-lg hover:bg-green-800 transition">ব্লগ পড়ুন</a>
        </div>
    </section>
</main>

<script>
// হোমপেজে শুধুমাত্র প্রথম ৪টি পণ্য দেখানোর জন্য স্ক্রিপ্ট
fetch('api.php?action=get_products')
    .then(r => r.json())
    .then(products => {
        const html = products.slice(0, 4).map(p => `
            <div class="bg-white rounded-xl shadow-sm hover:shadow-lg transition p-4 group relative border border-gray-100">
                <div class="h-48 flex items-center justify-center mb-4 bg-gray-50 rounded-lg overflow-hidden">
                    <img src="${p.image}" class="h-full object-contain group-hover:scale-110 transition duration-500" onerror="this.src='https://placehold.co/400x400?text=No+Image'">
                </div>
                <div class="text-left">
                    <p class="text-xs text-green-600 font-bold uppercase mb-1">${p.category}</p>
                    <h3 class="font-bold truncate text-gray-800 mb-2"><a href="product.php?id=${p.id}" class="hover:text-green-600">${p.name}</a></h3>
                    <div class="flex justify-between items-center">
                        <span class="font-bold text-lg text-green-700">৳ ${p.price}</span>
                        <button onclick='addToCart(${JSON.stringify(p)})' class="bg-green-100 text-green-600 w-9 h-9 rounded-full flex items-center justify-center hover:bg-green-600 hover:text-white transition shadow-sm">
                            <i class="fa-solid fa-plus"></i>
                        </button>
                    </div>
                </div>
            </div>
        `).join('');
        document.getElementById('featured-products').innerHTML = html;
    })
    .catch(err => {
        document.getElementById('featured-products').innerHTML = '<p class="text-red-500 col-span-full">পণ্য লোড করতে সমস্যা হয়েছে।</p>';
    });
</script>

<?php include 'footer.php'; ?>