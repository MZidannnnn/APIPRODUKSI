<script>
    document.addEventListener('DOMContentLoaded', function () {

    // ==========================
    // PROFILE DROPDOWN
    // ==========================
    const profileButton = document.getElementById('profileButton');
    const profileDropdown = document.getElementById('profileDropdown');

    if (profileButton) {
        profileButton.addEventListener('click', function () {
            profileDropdown.classList.toggle('show');
        });

        document.addEventListener('click', function (event) {
            if (!event.target.closest('.profile-wrapper')) {
                profileDropdown.classList.remove('show');
            }
        });
    }

    // ==========================
    // SEARCH PRODUK
    // ==========================
    const searchInput = document.getElementById('searchInput');
    const searchDropdown = document.getElementById('searchDropdown');
    
    let timeoutId;
    searchInput.addEventListener('input', function() {
        clearTimeout(timeoutId);
        const query = this.value.trim();

        if (query.length < 2) { 
            searchDropdown.style.display = 'none';
            return;
        }

        timeoutId = setTimeout(() => {
            fetch(`/search/live?q=${query}`)
                .then(res => res.json())
                .then(data => {
                    searchDropdown.innerHTML = ''; 
                    if (data.length > 0) {
                        data.forEach(item => {
                            const li = document.createElement('li');
                            li.textContent = item.nama_item; 
                            li.style.cssText = "padding: 10px; cursor: pointer; border-bottom: 1px solid #eee;";
                            
                            // JIKA DIKLIK, ARAHKAN KE HALAMAN DETAIL PRODUK
                            li.addEventListener('click', function() {
                                window.location.href = `/pesanan/detail/${item.id_item_produksi}`;
                            });
                            
                            searchDropdown.appendChild(li);
                        });
                        searchDropdown.style.display = 'block';
                    }
                });
        }, 300);
    });
});
</script>

 @stack('scripts')
 
</body>
</html>