<?php include 'header.php'; ?>

<main class="container mx-auto px-4 py-10 min-h-[60vh]">
    <!-- Header -->
    <div class="flex items-center gap-2 mb-6 border-b pb-4">
        <h1 class="text-2xl font-bold text-gray-800">অনুসন্ধান ফলাফল:</h1>
        <span id="search-term" class="text-2xl font-bold text-green-600">"..."</span>
    </div>
    
    <!-- Results Grid -->
    <div id="search-results" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
        <div class="col-span-full text-center py-20">
            <div class="loader mx-auto mb-4"></div>
            <p class="text-gray-500 animate-pulse">খোঁজা হচ্ছে...</p>
        </div>
    </div>
</main>

<script>
    const urlParams = new URLSearchParams(window.location.search);
    const query = urlParams.get('q') || '';
    
    document.getElementById('search-term').innerText = `"${query}"`;

    if (!query) {
        document.getElementById('search-results').innerHTML = `
            <div class="col-span-full text-center py-20">
                <i class="fa-solid fa-magnifying-glass text-6xl text-gray-200 mb-4"></i>
                <p class="text-gray-500 text-lg">আপনি কিছু লিখেননি।</p>
            </div>`;
    } else {
        fetch('api.php?action=get_products')
            .then(r => r.json())
            .then(products => {
                // Set global variable for main.js
                window.allProducts = products;

                const q = query.toLowerCase();
                const results = products.filter(p => 
                    p.name.toLowerCase().includes(q) || 
                    p.category.toLowerCase().includes(q) ||
                    (p.description && p.description.toLowerCase().includes(q))
                );

                const container = document.getElementById('search-results');
                
                if (results.length === 0) {
                    container.innerHTML = `
                        <div class="col-span-full text-center py-20 bg-white rounded-xl border border-dashed">
                            <i class="fa-solid fa-box-open text-6xl text-gray-300 mb-4"></i>
                            <p class="text-xl text-gray-600 font-bold">দুঃখিত, "${query}" দিয়ে কোনো পণ্য পাওয়া যায়নি।</p>
                            <p class="text-gray-400 mt-2 mb-6">বানান ঠিক আছে কিনা যাচাই করুন অথবা অন্য শব্দ দিয়ে খুঁজুন।</p>
                            <a href="shop.php" class="bg-green-600 text-white px-6 py-2 rounded-full font-bold hover:bg-green-700 transition">সকল পণ্য দেখুন</a>
                        </div>
                    `;
                } else {
                    container.innerHTML = results.map(p => `
                        <div class="bg-white rounded-xl shadow-sm hover:shadow-lg transition duration-300 group border border-gray-100 overflow-hidden flex flex-col relative">
                            ${wishlist.includes(p.id) ? '<div class="absolute top-3 right-3 text-red-500 z-10 drop-shadow-sm bg-white w-8 h-8 rounded-full flex items-center justify-center"><i class="fa-solid fa-heart"></i></div>' : ''}
                            
                            <div class="relative h-52 bg-gray-50 p-6 flex items-center justify-center cursor-pointer overflow-hidden" onclick="window.location.href='product.php?id=${p.id}'">
                                <img src="${p.image}" class="h-full object-contain mix-blend-multiply group-hover:scale-110 transition duration-500" onerror="this.src='https://placehold.co/400x400?text=No+Image'">
                            </div>
                            
                            <div class="p-5 flex-1 flex flex-col">
                                <div class="text-[10px] text-green-600 font-bold uppercase tracking-wider mb-1">${p.category}</div>
                                <h3 class="font-bold text-gray-800 text-base mb-1 truncate cursor-pointer hover:text-green-600 transition" onclick="window.location.href='product.php?id=${p.id}'">${p.name}</h3>
                                
                                <div class="mt-auto pt-3 flex justify-between items-end">
                                    <div>
                                        <div class="text-xs text-gray-400 line-through">৳ ${Math.round(p.price * 1.1)}</div>
                                        <div class="font-bold text-lg text-green-700">৳ ${p.price}</div>
                                    </div>
                                    <button onclick="addToCart(${p.id})" class="w-10 h-10 rounded-full bg-green-50 text-green-600 hover:bg-green-600 hover:text-white flex items-center justify-center transition shadow-sm">
                                        <i class="fa-solid fa-plus"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    `).join('');
                }
            })
            .catch(err => {
                console.error(err);
                document.getElementById('search-results').innerHTML = '<p class="text-red-500 text-center col-span-full">সার্ভার থেকে ডাটা লোড করতে সমস্যা হয়েছে।</p>';
            });
    }
</script>
<?php include 'footer.php'; ?>