<script>
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
    </script>

<script>
    const galleryTrack = document.getElementById('galleryTrack');
    const prevButton = document.querySelector('.prev-btn');
    const nextButton = document.querySelector('.next-btn');

    let currentSlide = 0;

    function updateGallery() {
        const itemWidth = document.querySelector('.gallery-item').offsetWidth + 50;
        galleryTrack.style.transform = `translateX(-${currentSlide * itemWidth}px)`;
    }

    nextButton.addEventListener('click', function () {
        const totalItems = document.querySelectorAll('.gallery-item').length;
        const visibleItems = window.innerWidth <= 768 ? 1 : 3;

        if (currentSlide < totalItems - visibleItems) {
            currentSlide++;
            updateGallery();
        }
    });

    prevButton.addEventListener('click', function () {
        if (currentSlide > 0) {
            currentSlide--;
            updateGallery();
        }
    });

    window.addEventListener('resize', updateGallery);

    const minusQty = document.getElementById('minusQty');
    const plusQty = document.getElementById('plusQty');
    const qtyInput = document.getElementById('qtyInput');

    minusQty.addEventListener('click', function () {
        let qty = parseInt(qtyInput.value);

        if (qty > 1) {
            qtyInput.value = qty - 1;
        }
    });

    plusQty.addEventListener('click', function () {
        let qty = parseInt(qtyInput.value);
        qtyInput.value = qty + 1;
    });
</script>
 @stack('scripts')
</body>
</html>