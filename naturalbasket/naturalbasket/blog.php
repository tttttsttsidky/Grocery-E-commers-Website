<?php include 'header.php'; ?>
<main class="container mx-auto px-4 py-10">
    <div class="text-center mb-10">
        <h1 class="text-3xl font-bold text-gray-800 mb-3">আমাদের ব্লগ</h1>
        <p class="text-gray-500">স্বাস্থ্য টিপস এবং অর্গানিক লাইফস্টাইল</p>
    </div>
    <div id="blog-list" class="grid md:grid-cols-3 gap-8">Loading...</div>
</main>
<script>
fetch('api.php?action=get_blogs').then(r=>r.json()).then(blogs => {
    document.getElementById('blog-list').innerHTML = blogs.map(b => `
        <div class="bg-white rounded-xl shadow-sm overflow-hidden hover:shadow-lg transition border border-gray-100 group h-full flex flex-col">
            <div class="h-48 overflow-hidden">
                <img src="${b.image}" class="w-full h-full object-cover group-hover:scale-110 transition duration-500">
            </div>
            <div class="p-6 flex-1 flex flex-col">
                <div class="text-xs text-green-600 font-bold mb-2 uppercase tracking-wide">স্বাস্থ্য কথা</div>
                <h3 class="font-bold text-xl mb-3 text-gray-800 group-hover:text-green-700 transition">${b.title}</h3>
                <p class="text-sm text-gray-600 line-clamp-3 leading-relaxed mb-4 flex-1">${b.content}</p>
                <div class="pt-4 border-t flex justify-between text-xs text-gray-400">
                    <span><i class="fa-solid fa-user mr-1"></i> ${b.author}</span>
                    <span><i class="fa-solid fa-calendar mr-1"></i> ${new Date(b.created_at).toLocaleDateString()}</span>
                </div>
            </div>
        </div>
    `).join('');
});
</script>
<?php include 'footer.php'; ?>