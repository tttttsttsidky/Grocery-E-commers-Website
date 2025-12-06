<?php include 'header.php'; ?>
<main class="container mx-auto px-4 py-10">
    <div class="text-center mb-10">
        <h1 class="text-3xl font-bold text-gray-800 mb-2">আমাদের গর্বিত কৃষক</h1>
        <p class="text-gray-500">যাদের পরিশ্রমে আমরা পাই বিশুদ্ধ খাবার</p>
    </div>
    <div id="farmers-list" class="grid grid-cols-2 md:grid-cols-4 gap-6">Loading...</div>
</main>
<script>
fetch('api.php?action=get_products').then(r=>r.json()).then(products => {
    const uniqueFarmers = [...new Map(products.map(item => [item['farmer_name'], item])).values()];
    
    document.getElementById('farmers-list').innerHTML = uniqueFarmers.map(p => `
        <div class="bg-white p-8 text-center rounded-2xl shadow-sm border border-gray-100 hover:shadow-lg transition group cursor-pointer">
            <div class="w-24 h-24 bg-green-50 rounded-full mx-auto mb-4 flex items-center justify-center text-4xl text-green-600 group-hover:bg-green-600 group-hover:text-white transition duration-300 border-4 border-white shadow-sm">
                <i class="fa-solid fa-user-nurse"></i> <!-- Using generic icon as placeholder -->
            </div>
            <h3 class="font-bold text-gray-800 text-lg">${p.farmer_name || 'অজানা কৃষক'}</h3>
            <p class="text-xs text-green-600 font-bold uppercase tracking-widest mt-1 mb-2">সার্টিফাইড ফার্মার</p>
            <p class="text-sm text-gray-500"><i class="fa-solid fa-location-dot mr-1"></i> ${p.farmer_loc || 'বাংলাদেশ'}</p>
        </div>
    `).join('');
});
</script>
<?php include 'footer.php'; ?>