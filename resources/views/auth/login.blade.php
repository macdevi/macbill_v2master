@php
    $businessName = \App\Models\Setting::value('business_name', 'MACBILLING');
    $businessInitial = strtoupper(mb_substr(trim($businessName), 0, 1)) ?: 'M';
@endphp
<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="color-scheme" content="dark">
    <title>Masuk — {{ $businessName }}</title>
    <style>
        :root {
            color-scheme: dark;
            --bg: #020617;
            --surface: rgba(15, 23, 42, .72);
            --surface-solid: #0f172a;
            --surface-muted: #172033;
            --line: rgba(148, 163, 184, .18);
            --line-strong: rgba(34, 211, 238, .32);
            --text: #f8fafc;
            --muted: #94a3b8;
            --cyan: #22d3ee;
            --cyan-strong: #06b6d4;
            --emerald: #34d399;
            --danger: #fb7185;
            --shadow: 0 28px 80px rgba(2, 6, 23, .52);
        }

        * {
            box-sizing: border-box;
        }

        html,
        body {
            min-height: 100%;
            margin: 0;
        }

        body {
            min-height: 100vh;
            overflow-x: hidden;
            background:
                radial-gradient(circle at 10% 12%, rgba(6, 182, 212, .16), transparent 30rem),
                radial-gradient(circle at 90% 84%, rgba(16, 185, 129, .14), transparent 28rem),
                linear-gradient(135deg, #020617 0%, #071426 48%, #06131e 100%);
            color: var(--text);
            font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
        }

        .network-grid {
            position: fixed;
            inset: 0;
            z-index: -3;
            opacity: .28;
            background-image:
                linear-gradient(rgba(56, 189, 248, .08) 1px, transparent 1px),
                linear-gradient(90deg, rgba(56, 189, 248, .08) 1px, transparent 1px);
            background-size: 44px 44px;
            mask-image: linear-gradient(to bottom, rgba(0, 0, 0, .92), transparent);
        }

        .network-map {
            position: fixed;
            inset: 0;
            z-index: -2;
            width: 100%;
            height: 100%;
            pointer-events: none;
        }

        .network-line {
            fill: none;
            stroke: rgba(34, 211, 238, .24);
            stroke-width: 1.3;
            stroke-dasharray: 8 14;
            animation: network-flow 11s linear infinite;
        }

        .network-line.emerald {
            stroke: rgba(52, 211, 153, .22);
            animation-duration: 15s;
            animation-direction: reverse;
        }

        .network-node {
            fill: #22d3ee;
            filter: drop-shadow(0 0 8px rgba(34, 211, 238, .95));
            animation: node-pulse 3.4s ease-in-out infinite;
            transform-origin: center;
        }

        .network-node.emerald {
            fill: #34d399;
            filter: drop-shadow(0 0 8px rgba(52, 211, 153, .92));
            animation-delay: 1.1s;
        }

        @keyframes network-flow {
            to {
                stroke-dashoffset: -180;
            }
        }

        @keyframes node-pulse {
            0%, 100% {
                opacity: .46;
                transform: scale(.85);
            }

            50% {
                opacity: 1;
                transform: scale(1.35);
            }
        }

        .page {
            width: min(1180px, calc(100% - 32px));
            min-height: 100vh;
            margin: 0 auto;
            display: grid;
            grid-template-columns: minmax(0, 1.08fr) minmax(390px, .92fr);
            align-items: center;
            gap: clamp(32px, 7vw, 108px);
            padding: 36px 0;
        }

        .brand-panel {
            position: relative;
            padding: 32px 20px;
        }

        .eyebrow,
        .status-pill,
        .feature-chip {
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .eyebrow {
            color: #a5f3fc;
            font-size: 11px;
            font-weight: 800;
            letter-spacing: .15em;
            text-transform: uppercase;
        }

        .eyebrow::before {
            content: "";
            width: 28px;
            height: 1px;
            background: linear-gradient(90deg, var(--cyan), transparent);
        }

        .brand-title {
            max-width: 670px;
            margin: 18px 0 16px;
            font-size: clamp(38px, 5vw, 66px);
            font-weight: 850;
            line-height: 1.05;
            letter-spacing: -.055em;
        }

        .brand-title span {
            color: var(--cyan);
            text-shadow: 0 0 28px rgba(34, 211, 238, .28);
        }

        .brand-copy {
            max-width: 550px;
            margin: 0;
            color: var(--muted);
            font-size: 16px;
            line-height: 1.75;
        }

        .feature-list {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 28px;
        }

        .feature-chip {
            border: 1px solid var(--line);
            border-radius: 999px;
            background: rgba(15, 23, 42, .52);
            padding: 9px 13px;
            color: #cbd5e1;
            font-size: 12px;
            font-weight: 700;
            backdrop-filter: blur(10px);
        }

        .feature-chip i,
        .status-dot {
            display: inline-block;
            border-radius: 999px;
            background: var(--emerald);
            box-shadow: 0 0 0 4px rgba(52, 211, 153, .11), 0 0 13px rgba(52, 211, 153, .55);
        }

        .feature-chip i {
            width: 7px;
            height: 7px;
        }

        .brand-status {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-top: 44px;
            color: #94a3b8;
            font-size: 12px;
            font-weight: 650;
        }

        .status-dot {
            width: 8px;
            height: 8px;
            animation: node-pulse 2.5s ease-in-out infinite;
        }

        .login-card {
            width: 100%;
            border: 1px solid var(--line);
            border-radius: 28px;
            background:
                linear-gradient(150deg, rgba(30, 41, 59, .76), rgba(15, 23, 42, .88)),
                var(--surface);
            padding: clamp(26px, 4vw, 42px);
            box-shadow: var(--shadow);
            backdrop-filter: blur(20px);
        }

        .login-brand {
            display: flex;
            align-items: center;
            gap: 13px;
            margin-bottom: 32px;
        }

        .brand-mark {
            position: relative;
            width: 48px;
            height: 48px;
            display: grid;
            place-items: center;
            overflow: hidden;
            border: 1px solid rgba(103, 232, 249, .32);
            border-radius: 16px;
            background: linear-gradient(135deg, rgba(34, 211, 238, .25), rgba(16, 185, 129, .19));
            color: white;
            font-size: 20px;
            font-weight: 900;
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, .16), 0 9px 26px rgba(34, 211, 238, .14);
        }

        .brand-mark::after {
            content: "";
            position: absolute;
            width: 66px;
            height: 2px;
            transform: rotate(-45deg);
            background: rgba(255, 255, 255, .26);
        }

        .login-brand-text {
            min-width: 0;
        }

        .login-brand-text strong {
            display: block;
            overflow: hidden;
            color: white;
            font-size: 15px;
            font-weight: 850;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .login-brand-text span {
            display: block;
            margin-top: 3px;
            color: var(--muted);
            font-size: 11px;
            font-weight: 700;
            letter-spacing: .08em;
            text-transform: uppercase;
        }

        .login-card h1 {
            margin: 0;
            color: white;
            font-size: 26px;
            font-weight: 850;
            letter-spacing: -.035em;
        }

        .login-card > p {
            margin: 9px 0 26px;
            color: var(--muted);
            font-size: 14px;
            line-height: 1.6;
        }

        .alert {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            margin-bottom: 20px;
            border: 1px solid rgba(251, 113, 133, .3);
            border-radius: 14px;
            background: rgba(190, 24, 93, .12);
            padding: 12px 13px;
            color: #fecdd3;
            font-size: 13px;
            font-weight: 650;
            line-height: 1.45;
        }

        .alert-icon {
            display: grid;
            flex: 0 0 auto;
            width: 19px;
            height: 19px;
            place-items: center;
            border-radius: 50%;
            background: rgba(251, 113, 133, .18);
            color: #fda4af;
            font-size: 12px;
            font-weight: 900;
        }

        .field {
            margin-top: 17px;
        }

        .field label {
            display: block;
            margin-bottom: 8px;
            color: #cbd5e1;
            font-size: 12px;
            font-weight: 750;
        }

        .input-wrap {
            position: relative;
        }

        .input-icon {
            position: absolute;
            top: 50%;
            left: 14px;
            width: 18px;
            height: 18px;
            transform: translateY(-50%);
            color: #64748b;
            pointer-events: none;
        }

        .field input {
            width: 100%;
            min-height: 49px;
            border: 1px solid rgba(148, 163, 184, .20);
            border-radius: 13px;
            outline: none;
            background: rgba(2, 6, 23, .36);
            padding: 0 46px 0 43px;
            color: #f8fafc;
            font: inherit;
            font-size: 14px;
            transition: border-color .2s ease, background .2s ease, box-shadow .2s ease;
        }

        .field input::placeholder {
            color: #64748b;
        }

        .field input:hover {
            border-color: rgba(148, 163, 184, .35);
        }

        .field input:focus {
            border-color: var(--cyan);
            background: rgba(2, 6, 23, .55);
            box-shadow: 0 0 0 4px rgba(34, 211, 238, .12);
        }

        .field input.is-invalid {
            border-color: rgba(251, 113, 133, .7);
        }

        .field-error {
            margin: 7px 2px 0;
            color: #fda4af;
            font-size: 12px;
            font-weight: 600;
        }

        .password-toggle {
            position: absolute;
            top: 50%;
            right: 10px;
            display: grid;
            width: 31px;
            height: 31px;
            place-items: center;
            transform: translateY(-50%);
            border: 0;
            border-radius: 9px;
            background: transparent;
            color: #94a3b8;
            cursor: pointer;
            transition: background .2s ease, color .2s ease;
        }

        .password-toggle:hover {
            background: rgba(148, 163, 184, .11);
            color: #e2e8f0;
        }

        .submit-button {
            width: 100%;
            min-height: 50px;
            margin-top: 26px;
            border: 0;
            border-radius: 13px;
            background: linear-gradient(135deg, #22d3ee, #14b8a6);
            color: #06202a;
            cursor: pointer;
            font: inherit;
            font-size: 14px;
            font-weight: 900;
            letter-spacing: .01em;
            box-shadow: 0 12px 28px rgba(6, 182, 212, .20);
            transition: transform .2s ease, box-shadow .2s ease, filter .2s ease;
        }

        .submit-button:hover {
            filter: brightness(1.05);
            transform: translateY(-1px);
            box-shadow: 0 15px 31px rgba(6, 182, 212, .29);
        }

        .submit-button:focus-visible,
        .password-toggle:focus-visible {
            outline: 3px solid rgba(34, 211, 238, .42);
            outline-offset: 3px;
        }

        .submit-button:disabled {
            cursor: wait;
            filter: saturate(.5);
            opacity: .78;
            transform: none;
        }

        .login-footer {
            margin: 23px 0 0;
            color: #64748b;
            font-size: 11px;
            line-height: 1.6;
            text-align: center;
        }

        .login-footer strong {
            color: #94a3b8;
        }

        @media (max-width: 850px) {
            .page {
                width: min(520px, calc(100% - 28px));
                grid-template-columns: 1fr;
                padding: 28px 0;
            }

            .brand-panel {
                display: none;
            }

            .login-card {
                padding: 28px 24px;
            }
        }

        @media (max-width: 420px) {
            .login-card {
                border-radius: 23px;
                padding: 24px 19px;
            }

            .login-card h1 {
                font-size: 24px;
            }
        }

        @media (prefers-reduced-motion: reduce) {
            *,
            *::before,
            *::after {
                scroll-behavior: auto !important;
                animation-duration: .01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: .01ms !important;
            }
        }
    </style>
</head>
<body>
    <div class="network-grid" aria-hidden="true"></div>

    <svg class="network-map" viewBox="0 0 1440 900" preserveAspectRatio="none" aria-hidden="true">
        <path class="network-line" d="M-30 155 C170 90 220 260 400 185 S660 92 810 205 S1070 300 1225 145 S1390 108 1480 175" />
        <path class="network-line emerald" d="M-15 680 C155 580 260 752 408 650 S688 550 842 690 S1090 790 1245 625 S1370 590 1465 675" />
        <path class="network-line" d="M85 850 C225 700 310 765 465 635 S780 440 925 590 S1200 470 1360 340" />
        <circle class="network-node" cx="220" cy="205" r="4" />
        <circle class="network-node emerald" cx="400" cy="185" r="4" />
        <circle class="network-node" cx="810" cy="205" r="4" />
        <circle class="network-node emerald" cx="1225" cy="145" r="4" />
        <circle class="network-node emerald" cx="260" cy="752" r="4" />
        <circle class="network-node" cx="842" cy="690" r="4" />
        <circle class="network-node emerald" cx="1245" cy="625" r="4" />
        <circle class="network-node" cx="465" cy="635" r="4" />
        <circle class="network-node emerald" cx="925" cy="590" r="4" />
    </svg>

    <main class="page">
        <section class="brand-panel" aria-label="Informasi sistem">
            <div class="eyebrow">Network Operations Platform</div>

            <h2 class="brand-title">
                Kelola koneksi, billing, dan pelanggan dalam <span>satu sistem.</span>
            </h2>

            <p class="brand-copy">
                {{ $businessName }} membantu operasional layanan internet berjalan lebih teratur,
                responsif, dan terpantau dari satu tempat.
            </p>

            <div class="feature-list" aria-label="Fitur sistem">
                <span class="feature-chip"><i></i> Billing terintegrasi</span>
                <span class="feature-chip"><i></i> Monitoring jaringan</span>
                <span class="feature-chip"><i></i> Manajemen pelanggan</span>
            </div>

            <div class="brand-status">
                <span class="status-dot"></span>
                Akses sistem billing &amp; network management
            </div>
        </section>

        <section class="login-card" aria-labelledby="login-title">
            <div class="login-brand">
                <div class="brand-mark" aria-hidden="true">{{ $businessInitial }}</div>
                <div class="login-brand-text">
                    <strong title="{{ $businessName }}">{{ $businessName }}</strong>
                    <span>Secure access portal</span>
                </div>
            </div>

            <h1 id="login-title">Selamat datang kembali</h1>
            <p>Masuk menggunakan akun operasional Anda untuk melanjutkan ke dashboard.</p>

            @if($errors->any())
                <div class="alert" role="alert">
                    <span class="alert-icon">!</span>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" id="login-form">
                @csrf

                <div class="field">
                    <label for="username">Username</label>
                    <div class="input-wrap">
                        <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                            <path d="M20 21a8 8 0 0 0-16 0"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                        <input
                            id="username"
                            type="text"
                            name="username"
                            value="{{ old('username') }}"
                            placeholder="Masukkan username"
                            autocomplete="username"
                            autofocus
                            required
                            class="@error('username') is-invalid @enderror"
                        >
                    </div>
                    @error('username')
                        <p class="field-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="field">
                    <label for="password">Password</label>
                    <div class="input-wrap">
                        <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                            <rect x="4" y="10" width="16" height="10" rx="2"></rect>
                            <path d="M8 10V7a4 4 0 0 1 8 0v3"></path>
                        </svg>
                        <input
                            id="password"
                            type="password"
                            name="password"
                            placeholder="Masukkan password"
                            autocomplete="current-password"
                            required
                            class="@error('password') is-invalid @enderror"
                        >
                        <button
                            class="password-toggle"
                            type="button"
                            id="toggle-password"
                            aria-label="Tampilkan password"
                            aria-pressed="false"
                        >
                            <svg id="eye-open" viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                <path d="M2 12s3.5-6 10-6 10 6 10 6-3.5 6-10 6S2 12 2 12Z"></path>
                                <circle cx="12" cy="12" r="2.5"></circle>
                            </svg>
                            <svg id="eye-closed" viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true" hidden>
                                <path d="M3 3l18 18"></path>
                                <path d="M10.6 6.2A10.8 10.8 0 0 1 12 6c6.5 0 10 6 10 6a18.5 18.5 0 0 1-3.05 3.75"></path>
                                <path d="M6.1 6.1C3.5 8 2 12 2 12s3.5 6 10 6a10.8 10.8 0 0 0 3.02-.43"></path>
                                <path d="M9.9 9.9a3 3 0 0 0 4.2 4.2"></path>
                            </svg>
                        </button>
                    </div>
                    @error('password')
                        <p class="field-error">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit" class="submit-button" id="login-submit">
                    <span id="submit-label">Masuk ke Dashboard</span>
                </button>
            </form>

            <p class="login-footer">
                Akses terbatas untuk personel <strong>{{ $businessName }}</strong>.<br>
                Pastikan Anda menggunakan akun yang berwenang.
            </p>
        </section>
    </main>

    <script>
        (() => {
            const password = document.getElementById('password');
            const toggle = document.getElementById('toggle-password');
            const eyeOpen = document.getElementById('eye-open');
            const eyeClosed = document.getElementById('eye-closed');
            const form = document.getElementById('login-form');
            const submit = document.getElementById('login-submit');
            const submitLabel = document.getElementById('submit-label');

            toggle?.addEventListener('click', () => {
                const shouldShow = password.type === 'password';

                password.type = shouldShow ? 'text' : 'password';
                toggle.setAttribute('aria-label', shouldShow ? 'Sembunyikan password' : 'Tampilkan password');
                toggle.setAttribute('aria-pressed', String(shouldShow));
                eyeOpen.hidden = shouldShow;
                eyeClosed.hidden = !shouldShow;
                password.focus();
            });

            form?.addEventListener('submit', () => {
                submit.disabled = true;
                submitLabel.textContent = 'Memverifikasi akun...';
            });
        })();
    </script>
</body>
</html>
