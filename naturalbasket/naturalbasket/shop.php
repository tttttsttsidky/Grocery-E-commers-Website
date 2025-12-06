<?php include 'header.php'; ?>
<main class="container mx-auto px-4 py-10">
    <h1 class="text-3xl font-bold mb-6">আমাদের শপ</h1>
    <div id="product-list" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6">Loading...</div>
</main>
<script>
fetch('api.php?action=get_products').then(r=>r.json()).then(products=>{
    document.getElementById('product-list').innerHTML = products.map(p => `
        <div class="bg-white rounded shadow p-4 group hover:shadow-lg transition">
            <a href="product.php?id=${p.id}"><img src="${p.image}" class="h-48 w-full object-contain mb-4"></a>
            <p class="text-xs text-green-600 uppercase font-bold">${p.category}</p>
            <h3 class="font-bold text-lg mb-2 truncate"><a href="product.php?id=${p.id}">${p.name}</a></h3>
            <div class="flex justify-between items-center">
                <span class="font-bold text-xl text-green-700">৳ ${p.price}</span>
                <button onclick='addToCart(${JSON.stringify(p)})' class="bg-green-600 text-white px-4 py-1 rounded text-sm">Add</button>
            </div>
        </div>
    `).join('');
});
</script>
<?php include 'footer.php'; ?>