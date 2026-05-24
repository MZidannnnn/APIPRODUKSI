    <!-- Bootstrap core JavaScript-->
    <script src="{{ asset('sbadmin2/vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('sbadmin2/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>

    <!-- Core plugin JavaScript-->
    <script src="{{ asset('sbadmin2/vendor/jquery-easing/jquery.easing.min.js') }}"></script>

    <!-- Custom scripts for all pages-->
    <script src="{{ asset('sbadmin2/js/sb-admin-2.min.js') }}"></script>

    <!-- Page level plugins -->
    <script src="{{ asset('sbadmin2/vendor/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('sbadmin2/vendor/datatables/dataTables.bootstrap4.min.js') }}"></script>

    <!-- Page level custom scripts -->
    <script src="{{ asset('sbadmin2/js/demo/datatables-demo.js') }}"></script>

    <!-- Sweet Alert -->
    <script src="{{ asset('sweetalert2/dist/sweetalert2.all.min.js')}}"></script>

    <!-- Session sukses -->
    @session('success')
        <script>
            Swal.fire({
            title: "Sukses",
            text: "{{ session('success')}}",
            icon: "success"
            });
        </script>
    @endsession

    <!-- Session gagal -->
    @session('error')
        <script>
            Swal.fire({
            title: "Gagal",
            text: "{{ session('error') }}",
            icon: "error"
            });
        </script>
    @endsession

    <!-- Session Hapus -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Mendengarkan semua klik di halaman secara global
            document.body.addEventListener('click', function (e) {
                // Jika yang diklik adalah tombol yang memiliki class 'btn-hapus'
                if (e.target.classList.contains('btn-hapus') || e.target.closest('.btn-hapus')) {
                    e.preventDefault(); // Tahan submit form asli
                    
                    // Cari form terdekat dari tombol yang diklik
                    const form = e.target.closest('form'); 
                    
                    Swal.fire({
                        title: "Yakin ingin menghapus data ini?",
                        text: "Data yang dihapus tidak dapat dikembalikan!",
                        icon: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#3085d6",
                        cancelButtonColor: "#d33",
                        confirmButtonText: "Ya, hapus!",
                        cancelButtonText: "Batal"
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Jika klik Ya, jalankan submit form ke controller
                            if (form) form.submit();
                        }
                    });
                }
            });
        });
    </script>
    
</body>

</html>