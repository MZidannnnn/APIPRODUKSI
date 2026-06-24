@if (session('success') || session('status') || session('error'))
    <div id="toast-notification" class="toast-notification
        {{ session('error') ? 'toast-error' : 'toast-success' }}">

        {{ session('success') ?? session('status') ?? session('error') }}

    </div>
@endif

<style>
    .toast-notification {
        position: fixed;
        top: 24px;
        right: 24px;
        z-index: 9999;
        min-width: 260px;
        max-width: 360px;
        padding: 14px 18px;
        border-radius: 12px;
        color: #fff;
        font-size: 14px;
        font-weight: 500;
        box-shadow: 0 10px 30px rgba(0,0,0,0.16);
        animation: slideToast 0.35s ease, hideToast 0.35s ease 3s forwards;
    }

    .toast-success {
        background: #00994d;
    }

    .toast-error {
        background: #dc3545;
    }

    @keyframes slideToast {
        from {
            opacity: 0;
            transform: translateX(40px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

    @keyframes hideToast {
        to {
            opacity: 0;
            transform: translateX(40px);
            pointer-events: none;
        }
    }

    @media (max-width: 576px) {
        .toast-notification {
            top: 18px;
            left: 16px;
            right: 16px;
            max-width: none;
            min-width: auto;
        }
    }
</style>