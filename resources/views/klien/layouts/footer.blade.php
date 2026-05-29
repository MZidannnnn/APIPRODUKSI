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

 @stack('scripts')
 
</body>
</html>