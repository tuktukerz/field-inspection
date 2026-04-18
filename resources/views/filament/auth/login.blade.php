<div class="fi-custom-login-wrapper">
    <style>
        body {
            background-color: #ffffff !important;
            background-image: radial-gradient(#e5e7eb 1px, transparent 1px) !important;
            background-size: 20px 20px !important;
        }
        .dark body {
            background-color: #0a0a0a !important;
            background-image: radial-gradient(#27272a 1px, transparent 1px) !important;
        }
        .fi-simple-layout {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
        }
        .fi-simple-main-ctn {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            background: transparent !important;
        }
        .fi-simple-main {
            width: 100%;
            max-width: 420px;
            background: #ffffff;
            border-radius: 1rem;
            box-shadow: 0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1);
            padding: 2.25rem 2rem;
            border: 1px solid #f1f5f9;
        }
        .dark .fi-simple-main {
            background: #18181b;
            border-color: #27272a;
            box-shadow: 0 20px 25px -5px rgb(0 0 0 / 0.4), 0 8px 10px -6px rgb(0 0 0 / 0.3);
        }
        .fi-simple-page-header,
        .fi-logo {
            display: none !important;
        }
        .fi-custom-login-brand {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 1.5rem;
        }
        .fi-custom-login-logo {
            width: 64px;
            height: 64px;
            border-radius: 0.875rem;
            background: linear-gradient(135deg, #0ea5e9 0%, #0369a1 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 8px 16px -4px rgb(14 165 233 / 0.4);
        }
        .fi-custom-login-title {
            font-family: 'Plus Jakarta Sans', ui-sans-serif, system-ui, sans-serif;
            font-size: 1.375rem;
            font-weight: 700;
            letter-spacing: -0.02em;
            color: #0f172a;
            margin: 0;
        }
        .dark .fi-custom-login-title {
            color: #f4f4f5;
        }
        .fi-custom-login-divider {
            height: 1px;
            background: #f1f5f9;
            margin-bottom: 1.5rem;
        }
        .dark .fi-custom-login-divider {
            background: #27272a;
        }
    </style>

    <div class="fi-custom-login-brand">
        <div class="fi-custom-login-logo">
            <svg width="36" height="36" viewBox="0 0 36 36" fill="none" xmlns="http://www.w3.org/2000/svg">
                <circle cx="18" cy="5" r="2" fill="#ffffff"/>
                <line x1="18" y1="7" x2="18" y2="32" stroke="#ffffff" stroke-width="1.5" stroke-linecap="round"/>
                <line x1="9" y1="32" x2="18" y2="8" stroke="#ffffff" stroke-width="1.75" stroke-linecap="round"/>
                <line x1="27" y1="32" x2="18" y2="8" stroke="#ffffff" stroke-width="1.75" stroke-linecap="round"/>
                <line x1="13" y1="20" x2="23" y2="20" stroke="#ffffff" stroke-width="1.25" stroke-linecap="round"/>
                <line x1="11" y1="26" x2="25" y2="26" stroke="#ffffff" stroke-width="1.25" stroke-linecap="round"/>
                <line x1="15" y1="14" x2="21" y2="14" stroke="#ffffff" stroke-width="1.25" stroke-linecap="round"/>
                <line x1="7" y1="32" x2="29" y2="32" stroke="#ffffff" stroke-width="1.5" stroke-linecap="round"/>
            </svg>
        </div>
        <h1 class="fi-custom-login-title">Inspeksi Lapangan</h1>
    </div>

    <div class="fi-custom-login-divider"></div>

    {{ $this->content }}
</div>
