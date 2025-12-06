<?php include 'header.php'; $id = $_GET['id'] ?? 0; ?>
<main class="container mx-auto px-4 py-10" id="detail-container">
    <div class="text-center py-10">লোডিং...</div>
</main>
<script>
const pid = <?php echo $id; ?>;
if(pid) {
    fetch('api.php?action=get_products').then(r=>r.json()).then(all => {
        const p = all.find(x => x.id == pid);
        if(!p) { document.getElementById('detail-container').innerHTML = 'পণ্য পাওয়া যায়নি'; return; }
        document.getElementById('detail-container').innerHTML = `
            <div class="grid md:grid-cols-2 gap-8 bg-white p-8 rounded shadow">
                <div class="flex justify-center"><img src="${p.image}" class="max-h-96"></div>
                <div>
                    <h1 class="text-3xl font-bold mb-2">${p.name}</h1>
                    <p class="text-green-600 text-2xl font-bold mb-4">৳ ${p.price}</p>
                    <p class="text-gray-600 mb-6">${p.description}</p>
                    <button onclick='addToCart(${JSON.stringify(p)})' class="bg-green-600 text-white px-8 py-3 rounded font-bold shadow">কার্টে যোগ করুন</button>
                </div>
            </div>
        `;
    });
}
</script>
<?php include 'footer.php'; ?>