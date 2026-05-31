<script>
    document.addEventListener('DOMContentLoaded', function () {

    // ==========================
    // PROFILE DROPDOWN
    // ==========================
    const profileButton = document.getElementById('profileButton');
    const profileDropdown = document.getElementById('profileDropdown');

    if (profileButton && profileDropdown) {
        profileButton.addEventListener('click', function (event) {
            event.stopPropagation();
            profileDropdown.classList.toggle('show');
        });

        profileDropdown.addEventListener('click', function (event) {
            event.stopPropagation();
        });

        document.addEventListener('click', function () {
            profileDropdown.classList.remove('show');
        });
    }

    // ==========================
    // REALTIME NOTIFIKASI CHAT
    // ==========================
    function updateChatHeaderBadge() {
        fetch("{{ route('chat.unread') }}")
            .then(response => response.json())
            .then(data => {
                const badge = document.getElementById('chatHeaderBadge');

                if (!badge) return;

                if (data.count > 0) {
                    badge.textContent = data.count > 99 ? '99+' : data.count;
                    badge.classList.remove('d-none');
                } else {
                    badge.textContent = '0';
                    badge.classList.add('d-none');
                }
            })
            .catch(error => console.log(error));
    }

    updateChatHeaderBadge();
    setInterval(updateChatHeaderBadge, 3000);

    // ==========================
    // SEARCH PRODUK
    // ==========================
    const searchInput = document.getElementById('searchInput');
    const searchDropdown = document.getElementById('searchDropdown');

    if (searchInput && searchDropdown) {
        let timeoutId;

        searchInput.addEventListener('input', function () {
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

                                li.addEventListener('click', function () {
                                    window.location.href = `/pesanan/detail/${item.id_item_produksi}`;
                                });

                                searchDropdown.appendChild(li);
                            });

                            searchDropdown.style.display = 'block';
                        } else {
                            searchDropdown.style.display = 'none';
                        }
                    });
            }, 300);
        });
    }

    });
</script>

 @stack('scripts')
 
</body>
</html>