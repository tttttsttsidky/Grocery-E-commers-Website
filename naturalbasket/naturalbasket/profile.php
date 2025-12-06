<?php include 'header.php'; ?>
<main class="container mx-auto px-4 py-10 max-w-4xl">
    <div id="profile-content" class="fade-in">
        <div class="text-center py-20"><div class="loader mx-auto"></div></div>
    </div>
</main>
<script>
async function loadProfile() {
    try {
        const r = await fetch('api.php?action=check_session');
        const d = await r.json();
        if (d.status !== 'logged_in') { window.location.href = 'login.php'; return; }
        
        const orders = await (await fetch('api.php?action=get_orders')).json();
        const user = d.user;

        document.getElementById('profile-content').innerHTML = `
            <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100 text-center mb-10">
                <div class="w-24 h-24 bg-green-100 rounded-full flex items-center justify-center text-4xl font-bold text-green-700 mx-auto mb-4 border-4 border-white shadow-sm">${user.name[0]}</div>
                <h2 class="text-2xl font-bold text-gray-800">${user.name}</h2>
                <p class="text-gray-500">${user.role === 'admin' ? 'Administrator' : 'Premium Member'}</p>
                <button onclick="logout()" class="mt-4 text-red-500 text-sm font-bold border border-red-200 px-4 py-1.5 rounded-full hover:bg-red-50">লগআউট</button>
            </div>

            <h3 class="font-bold text-xl mb-6 flex items-center gap-2 border-l-4 border-green-600 pl-3 text-gray-800">আমার অর্ডার সমূহ (${orders.length})</h3>
            <div class="space-y-4">
                ${orders.length > 0 ? orders.map(o => `
                    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition group">
                        <div class="flex justify-between items-start mb-4">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 bg-green-50 rounded-full flex items-center justify-center text-green-600 text-lg"><i class="fa-solid fa-box-open"></i></div>
                                <div>
                                    <span class="font-bold text-gray-800 block text-lg">অর্ডার #${o.id}</span>
                                    <span class="text-xs text-gray-500 flex items-center gap-1"><i class="fa-regular fa-clock"></i> ${o.order_date}</span>
                                </div>
                            </div>
                            <span class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider ${o.status==='Delivered'?'bg-green-100 text-green-800':'bg-orange-100 text-orange-800'}">${o.status}</span>
                        </div>
                        <div class="border-t pt-4 flex justify-between items-center">
                            <span class="text-sm text-gray-500 font-medium bg-gray-50 px-2 py-1 rounded">${o.payment_method==='cod'?'Cash Payment':'Digital Payment'}</span>
                            <span class="font-bold text-xl text-green-700">৳ ${o.total_amount}</span>
                        </div>
                    </div>
                `).join('') : '<div class="text-center py-16 bg-white rounded-xl border border-dashed"><p class="text-gray-400 text-lg mb-2">আপনার কোনো অর্ডার নেই।</p><a href="shop.php" class="text-green-600 font-bold hover:underline">শপিং শুরু করুন</a></div>'}
            </div>
        `;
    } catch (e) { console.error(e); }
}
loadProfile();
</script>
<?php include 'footer.php'; ?>