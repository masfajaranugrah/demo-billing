@php
$configData = Helper::appClasses();
$customizerHidden = 'customizer-hide';
@endphp
@php
use Illuminate\Support\Facades\Auth;
// Cek apakah customer sudah login
if (Auth::guard('customer')->check()) {
    // Redirect langsung ke route dashboard customer
    header("Location: " . route('customer.tagihan.home'));
    exit;
}
@endphp
@extends('layouts/layoutMaster')

@section('title', 'Login Member')

@section('vendor-style')
@vite([
  'resources/assets/vendor/libs/@form-validation/form-validation.scss'
])
@endsection

@section('page-style')
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
<style>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: 'Inter', sans-serif;
    background: #0f172a;
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 20px;
    position: relative;
    overflow: hidden;
}

body::before {
    content: '';
    position: absolute;
    width: 500px;
    height: 500px;
    background: radial-gradient(circle, rgba(59, 130, 246, 0.15) 0%, transparent 70%);
    top: -200px;
    right: -200px;
    border-radius: 50%;
    animation: float 8s ease-in-out infinite;
}

body::after {
    content: '';
    position: absolute;
    width: 400px;
    height: 400px;
    background: radial-gradient(circle, rgba(139, 92, 246, 0.15) 0%, transparent 70%);
    bottom: -150px;
    left: -150px;
    border-radius: 50%;
    animation: float 10s ease-in-out infinite reverse;
}

@keyframes float {
    0%, 100% { transform: translate(0, 0); }
    50% { transform: translate(30px, 30px); }
}

.auth-container {
    width: 100%;
    max-width: 420px;
    position: relative;
    z-index: 1;
}

/* Card */
.auth-card {
    background: rgba(255, 255, 255, 0.98);
    backdrop-filter: blur(10px);
    border-radius: 24px;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
    overflow: hidden;
    animation: slideUp 0.6s ease;
}

@keyframes slideUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Logo Section */
.logo-section {
    text-align: center;
    padding: 48px 32px 32px;
    background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
    border-bottom: 1px solid #f1f5f9;
}

.logo-circle {
    width: 80px;
    height: 80px;
    background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
    border-radius: 20px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 20px;
    box-shadow: 0 8px 24px rgba(15, 23, 42, 0.2);
}

.logo-text {
    font-size: 2rem;
    font-weight: 800;
    color: #ffffff;
    letter-spacing: -0.02em;
}

.company-name {
    font-size: 1.125rem;
    font-weight: 700;
    color: #0f172a;
    margin-bottom: 4px;
    letter-spacing: -0.01em;
}

.company-tagline {
    font-size: 0.875rem;
    color: #64748b;
}

/* Form Section */
.form-section {
    padding: 40px 32px;
}

.form-header {
    text-align: center;
    margin-bottom: 32px;
}

.form-title {
    font-size: 1.5rem;
    font-weight: 700;
    color: #0f172a;
    margin-bottom: 8px;
    letter-spacing: -0.02em;
}

.form-subtitle {
    font-size: 0.9375rem;
    color: #64748b;
    line-height: 1.5;
}

/* Alert Messages */
.alert {
    padding: 14px 16px;
    border-radius: 12px;
    margin-bottom: 24px;
    display: flex;
    align-items: flex-start;
    gap: 12px;
    animation: slideDown 0.3s ease;
}

@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.alert-error {
    background: #fef2f2;
    border: 1px solid #fecaca;
    color: #991b1b;
}

.alert-icon {
    font-size: 1.25rem;
    flex-shrink: 0;
    margin-top: 2px;
}

.alert-content {
    flex: 1;
    font-size: 0.875rem;
    font-weight: 500;
    line-height: 1.5;
}

/* Form Group */
.form-group {
    margin-bottom: 24px;
}

.form-label {
    display: block;
    font-size: 0.875rem;
    font-weight: 600;
    color: #0f172a;
    margin-bottom: 8px;
}

.input-wrapper {
    position: relative;
}

.input-icon {
    position: absolute;
    left: 16px;
    top: 50%;
    transform: translateY(-50%);
    color: #94a3b8;
    font-size: 1.125rem;
    pointer-events: none;
    transition: color 0.2s ease;
}

.form-input {
    width: 100%;
    padding: 14px 16px 14px 48px;
    font-size: 1rem;
    color: #0f172a;
    background: #f8fafc;
    border: 2px solid #e2e8f0;
    border-radius: 12px;
    transition: all 0.2s ease;
    font-family: 'Inter', sans-serif;
}

.form-input:focus {
    outline: none;
    background: #ffffff;
    border-color: #0f172a;
    box-shadow: 0 0 0 4px rgba(15, 23, 42, 0.1);
}

.form-input:focus + .input-icon {
    color: #0f172a;
}

.form-input::placeholder {
    color: #cbd5e1;
}

/* Submit Button */
.btn-submit {
    width: 100%;
    padding: 16px 24px;
    font-size: 1rem;
    font-weight: 600;
    color: #ffffff;
    background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
    border: none;
    border-radius: 12px;
    cursor: pointer;
    transition: all 0.3s ease;
    font-family: 'Inter', sans-serif;
    box-shadow: 0 4px 16px rgba(15, 23, 42, 0.2);
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
}

.btn-submit:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 24px rgba(15, 23, 42, 0.3);
}

.btn-submit:active {
    transform: translateY(0);
}

.btn-submit:disabled {
    background: #cbd5e1;
    cursor: not-allowed;
    transform: none;
    box-shadow: none;
}

.btn-icon {
    font-size: 1.125rem;
}

/* Loading Spinner */
.spinner {
    width: 18px;
    height: 18px;
    border: 2px solid rgba(255, 255, 255, 0.3);
    border-radius: 50%;
    border-top-color: #ffffff;
    animation: spin 0.6s linear infinite;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}

/* Help Box */
.help-box {
    margin-top: 24px;
    padding: 16px;
    background: #f0f9ff;
    border: 1px solid #bae6fd;
    border-radius: 12px;
    display: flex;
    gap: 12px;
}

.help-icon {
    width: 32px;
    height: 32px;
    background: #0284c7;
    color: white;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    font-size: 1rem;
}

.help-content {
    flex: 1;
}

.help-title {
    font-size: 0.875rem;
    font-weight: 600;
    color: #075985;
    margin-bottom: 4px;
}

.help-text {
    font-size: 0.8125rem;
    color: #0369a1;
    line-height: 1.4;
}

/* Footer */
.auth-footer {
    padding: 24px 32px;
    text-align: center;
    background: #f8fafc;
    border-top: 1px solid #f1f5f9;
}

.footer-text {
    font-size: 0.875rem;
    color: #64748b;
    margin-bottom: 8px;
}

.footer-link {
    color: #0f172a;
    font-weight: 600;
    text-decoration: none;
    transition: color 0.2s ease;
}

.footer-link:hover {
    color: #334155;
}

.footer-links {
    display: flex;
    justify-content: center;
    gap: 16px;
    margin-top: 12px;
}

.footer-links a {
    font-size: 0.8125rem;
    color: #94a3b8;
    text-decoration: none;
    transition: color 0.2s ease;
}

.footer-links a:hover {
    color: #64748b;
}

/* Responsive */
@media (max-width: 576px) {
    body {
        padding: 16px;
    }

    .logo-section {
        padding: 40px 24px 24px;
    }

    .logo-circle {
        width: 72px;
        height: 72px;
    }

    .logo-text {
        font-size: 1.75rem;
    }

    .form-section {
        padding: 32px 24px;
    }

    .form-title {
        font-size: 1.375rem;
    }

    .auth-footer {
        padding: 20px 24px;
    }
}
</style>
@endsection

@section('vendor-script')
@vite([
  'resources/assets/vendor/libs/@form-validation/popular.js',
  'resources/assets/vendor/libs/@form-validation/bootstrap5.js',
  'resources/assets/vendor/libs/@form-validation/auto-focus.js'
])
@endsection


@section('page-script')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('formAuthentication');
    const submitBtn = form.querySelector('.btn-submit');
    const input = document.getElementById('no_whatsapp');
    const rememberCheckbox = document.getElementById('remember');

    // Prefill dari localStorage kalau sebelumnya disimpan
    const savedPhone = localStorage.getItem('customer_phone');
    const savedRemember = localStorage.getItem('remember_whatsapp');

    if (savedRemember === 'true' && savedPhone) {
        input.value = savedPhone;
        rememberCheckbox.checked = true;
    }

    // Auto focus
    input.focus();

    // Form submission
    form.addEventListener('submit', function(e) {
        const whatsapp = input.value.trim();

        if (!whatsapp) {
            e.preventDefault();
            showAlert('Nomor WhatsApp tidak boleh kosong');
            return;
        }

        // Simpan / hapus ke/dari localStorage sesuai checkbox
        if (rememberCheckbox.checked) {
            localStorage.setItem('customer_phone', whatsapp);
            localStorage.setItem('remember_whatsapp', 'true');
        } else {
            localStorage.removeItem('customer_phone');
            localStorage.removeItem('remember_whatsapp');
        }

        // Show loading state
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner"></span> Memproses...';
    });

    // Remove alert on input
    input.addEventListener('input', function() {
        const alert = document.querySelector('.alert-error');
        if (alert) {
            alert.remove();
        }
    });

    function showAlert(message) {
        const existingAlert = document.querySelector('.alert-error');
        if (existingAlert) existingAlert.remove();

        const alertDiv = document.createElement('div');
        alertDiv.className = 'alert alert-error';
        alertDiv.innerHTML = `
            <i class="bi bi-exclamation-circle alert-icon"></i>
            <div class="alert-content">${message}</div>
        `;

        const formSection = document.querySelector('.form-section form');
        formSection.insertBefore(alertDiv, formSection.firstChild);
    }

    // Debug (opsional - bisa dihapus di production)
    console.log('?? Saved phone:', savedPhone);
    console.log('? Remember:', savedRemember);
});
</script>
@endsection


@section('content')
<div class="auth-container">
    <div class="auth-card">


        <!-- Form Section -->
        <div class="form-section">
            <div class="form-header">
                <h1 class="form-title">Selamat Datang</h1>
                <p class="form-subtitle">Masuk ke akun Anda untuk mengelola tagihan</p>
            </div>

            <form id="formAuthentication" action="{{ route('login.member.post') }}" method="POST">
                @csrf

                @if(session('error'))
                <div class="alert alert-error">
                    <i class="bi bi-exclamation-circle alert-icon"></i>
                    <div class="alert-content">{{ session('error') }}</div>
                </div>
                @endif

                <div class="form-group">
                    <label for="no_whatsapp" class="form-label">Nomor WhatsApp</label>
                    <div class="input-wrapper">
                        <input
                            type="text"
                            class="form-input"
                            id="no_whatsapp"
                            name="no_whatsapp"
                            placeholder="08123456789"
                            autofocus
                            required>
                        <i class="bi bi-phone input-icon"></i>
                    </div>
                </div>
   <!-- Checkbox Remember Me -->
    <div class="form-check mb-3">
        <input type="checkbox" class="form-check-input" id="remember" name="remember">
        <label class="form-check-label" for="remember">
            Ingat Saya
        </label>
    </div>
                    <button class="btn-submit" type="submit">
                    <i class="bi bi-box-arrow-in-right btn-icon"></i>
                    Masuk
                </button>

                <div class="help-box">
                    <div class="help-icon">
                        <i class="bi bi-lightbulb"></i>
                    </div>
                    <div class="help-content">
                        <div class="help-title">Tips Login</div>
                        <div class="help-text">Gunakan nomor WhatsApp yang terdaftar pada sistem kami tanpa tanda (+) atau spasi</div>
                    </div>
                </div>
            </form>
        </div>


    </div>
</div>


@endsection
