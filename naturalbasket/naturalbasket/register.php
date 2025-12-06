<?php include 'header.php'; ?>
<div class="flex justify-center items-center min-h-[60vh] bg-green-50">
    <div class="bg-white p-8 rounded shadow-lg w-full max-w-md">
        <h2 class="text-2xl font-bold mb-6 text-center">রেজিস্ট্রেশন</h2>
        <form onsubmit="doReg(event)">
            <input name="name" placeholder="নাম" class="border p-3 w-full mb-4 rounded" required>
            <input name="email" placeholder="ইমেইল" class="border p-3 w-full mb-4 rounded" required>
            <input name="phone" placeholder="ফোন" class="border p-3 w-full mb-4 rounded" required>
            <input type="password" name="password" placeholder="পাসওয়ার্ড" class="border p-3 w-full mb-6 rounded" required>
            <button class="bg-green-600 text-white w-full py-3 rounded font-bold">রেজিস্টার</button>
        </form>
    </div>
</div>
<script>
async function doReg(e) {
    e.preventDefault();
    const r = await fetch('api.php?action=register', {method:'POST', body:JSON.stringify(Object.fromEntries(new FormData(e.target)))});
    const d = await r.json();
    if(d.status==='success') { alert('সফল! লগিন করুন'); window.location.href='login.php'; } else alert(d.message);
}
</script>
<?php include 'footer.php'; ?>