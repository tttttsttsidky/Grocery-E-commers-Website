<?php include 'header.php'; ?>
<div class="container mx-auto px-4 py-20 max-w-md text-center">
    <h2 class="text-2xl font-bold mb-4">অর্ডার ট্র্যাক করুন</h2>
    <input id="tid" placeholder="Order ID" class="border p-3 w-full rounded mb-2">
    <button onclick="doTrack()" class="bg-green-600 text-white px-6 py-2 rounded">Check</button>
    <div id="res" class="mt-4 font-bold"></div>
</div>
<script>
async function doTrack() {
    const id = document.getElementById('tid').value;
    const r = await (await fetch('api.php?action=get_orders')).json(); // In real app use specific endpoint
    const o = r.find(x => x.id == id);
    document.getElementById('res').innerText = o ? `Status: ${o.status}` : 'Not Found';
}
</script>
<?php include 'footer.php'; ?>