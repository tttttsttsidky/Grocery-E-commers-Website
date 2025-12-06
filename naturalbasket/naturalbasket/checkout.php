<?php include 'header.php'; ?>
<main class="container mx-auto px-4 py-10 max-w-2xl">
    <h1 class="text-2xl font-bold mb-6">চেকআউট</h1>
    <form id="checkout-form" class="bg-white p-6 rounded shadow space-y-4">
        <input name="name" placeholder="আপনার নাম" class="border p-3 w-full rounded" required>
        <input name="phone" placeholder="মোবাইল নাম্বার" class="border p-3 w-full rounded" required>
        <textarea name="address" placeholder="ঠিকানা" class="border p-3 w-full rounded" required></textarea>
        <div>
            <label class="mr-4"><input type="radio" name="payment" value="cod" checked> ক্যাশ অন ডেলিভারি</label>
            <label><input type="radio" name="payment" value="bkash"> বিকাশ</label>
        </div>
        <div class="font-bold text-xl border-t pt-4">মোট: <span id="pay-total">0</span></div>
        <button type="submit" class="bg-green-600 text-white w-full py-3 rounded font-bold">অর্ডার প্লেস করুন</button>
    </form>
</main>
<script>
    const cartTotal = JSON.parse(localStorage.getItem('cart')||'[]').reduce((s,i)=>s+(i.price*i.qty),0);
    document.getElementById('pay-total').innerText = '৳ ' + (cartTotal + 60); // +Shipping
    
    document.getElementById('checkout-form').onsubmit = async (e) => {
        e.preventDefault();
        const d = Object.fromEntries(new FormData(e.target));
        d.items = JSON.parse(localStorage.getItem('cart'));
        d.total = cartTotal + 60;
        
        const r = await fetch('api.php?action=place_order', {method:'POST', body:JSON.stringify(d)});
        const res = await r.json();
        if(res.status === 'success') {
            localStorage.removeItem('cart');
            alert('অর্ডার সফল! ID: #' + res.order_id);
            window.location.href = 'index.php';
        } else {
            alert(res.message || 'লগিন করুন');
            window.location.href = 'login.php';
        }
    };
</script>
<?php include 'footer.php'; ?>