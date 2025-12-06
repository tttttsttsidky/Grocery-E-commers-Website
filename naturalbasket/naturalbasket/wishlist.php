<?php include 'header.php'; ?>
<main class="container mx-auto px-4 py-10">
    <h1 class="text-2xl font-bold mb-6 border-b pb-4">আমার পছন্দের তালিকা (Wishlist)</h1>
    <div id="wishlist-container" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6">
        <div class="col-span-full text-center py-20"><div class="loader mx-auto"></div></div>
    </div>
</main>
<script>
async function loadWishlist() {
    const wishlistIds = JSON.parse(localStorage.getItem('wishlist') || '[]');
    if (wishlistIds.length === 0) {
        document.getElementById('wishlist-container').innerHTML = '<div class="col-span-full text-center py-20 text-gray-400"><i class="fa-regular fa-heart text-6xl mb-4 opacity-20"></i><p class="text-lg">আপনার উইশলিস্ট খালি।</p><a href="shop.php" class="text-green-600 font-bold mt-2 inline-block hover:underline">পণ্য দেখুন</a></div>';
        return;
    }
    
    const products = await (await fetch('api.php?action=get_products')).json();
    const wishItems = products.filter(p => wishlistIds.includes(p.id));
    
    document.getElementById('wishlist-container').innerHTML = wishItems.map(p => `
        <div class="bg-white rounded-xl shadow-sm hover:shadow-lg transition p-4 group relative border border-gray-100">
            <button onclick="toggleWishlist(${p.id}); loadWishlist()" class="absolute top-3 right-3 text-red-500 bg-white w-8 h-8 rounded-full shadow flex items-center justify-center hover:bg-red-50 z-10"><i class="fa-solid fa-trash"></i></button>
            <div class="h-48 flex items-center justify-center mb-4 bg-gray-50 rounded-lg overflow-hidden">
                <img src="${p.image}" class="h-full object-contain group-hover:scale-110 transition duration-500">
            </div>
            <div class="text-left">
                <p class="text-xs text-green-600 font-bold uppercase mb-1">${p.category}</p>
                <h3 class="font-bold truncate text-gray-800 mb-2"><a href="product.php?id=${p.id}">${p.name}</a></h3>
                <div class="flex justify-between items-center">
                    <span class="font-bold text-lg text-green-700">৳ ${p.price}</span>
                    <button onclick='addToCart(${JSON.stringify(p)})' class="bg-green-100 text-green-600 w-9 h-9 rounded-full flex items-center justify-center hover:bg-green-600 hover:text-white transition shadow-sm">
                        <i class="fa-solid fa-plus"></i>
                    </button>
                </div>
            </div>
        </div>
    `).join('');
}
loadWishlist();
</script>
<?php include 'footer.php'; ?>