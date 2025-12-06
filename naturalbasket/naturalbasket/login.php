<?php include 'header.php'; ?>
<div class="flex justify-center items-center min-h-[60vh] bg-green-50">
    <div class="bg-white p-8 rounded shadow-lg w-full max-w-md">
        <h2 class="text-2xl font-bold mb-6 text-center">লগিন</h2>
        <form onsubmit="doLogin(event)">
            <input name="email" placeholder="ইমেইল" class="border p-3 w-full mb-4 rounded" required>
            <input type="password" name="password" placeholder="পাসওয়ার্ড" class="border p-3 w-full mb-6 rounded" required>
            <button class="bg-green-600 text-white w-full py-3 rounded font-bold">লগিন করুন</button>
        </form>
        <p class="mt-4 text-center text-sm">অ্যাকাউন্ট নেই? <a href="register.php" class="text-green-600 font-bold">রেজিস্টার</a></p>
    </div>
</div>
<script>
async function doLogin(e) {
    e.preventDefault();
    const r = await fetch('api.php?action=login', {method:'POST', body:JSON.stringify(Object.fromEntries(new FormData(e.target)))});
    const d = await r.json();
    if(d.status==='success') window.location.href = 'index.php'; else alert(d.message);
}
</script>
<?php include 'footer.php'; ?>