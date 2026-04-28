<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>SIP-KTP | {{ $title }}</title>

    <!-- Custom fonts for this template-->
    <link href="{{ asset('sbadmin2/vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="{{ asset('sbadmin2/css/sb-admin-2.min.css') }}" rel="stylesheet">

    <!-- Custom styles for this page -->
    <link href="{{ asset('sbadmin2/vendor/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet">

    <style>
    /* ===== FIX SIDEBAR ===== */
    #accordionSidebar {
        position: fixed;
        top: 0;
        left: 0;
        height: 100vh;
        width: 14rem;
        z-index: 1030;
    }

    /* ===== FIX WRAPPER ===== */
    #wrapper {
        display: flex;
    }

    /* ===== FIX CONTENT ===== */
    #content-wrapper {
        margin-left: 14rem;
        min-height: 100vh;
        display: flex;
        flex-direction: column;
    }

    /* ===== CONTENT GROW ===== */
    #content {
        flex: 1 0 auto;
    }

    /* ===== FOOTER FIX ===== */
    footer.sticky-footer {
        flex-shrink: 0;
    }

    /* ===== RESPONSIVE ===== */
    @media (max-width: 768px) {
        #accordionSidebar {
            position: relative;
            width: 100%;
            height: auto;
        }

        #content-wrapper {
            margin-left: 0;
        }
    }
    </style>

</head>
