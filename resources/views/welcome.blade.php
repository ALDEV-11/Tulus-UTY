<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'AldiMotor') }} - Servis Motor</title>
    <meta name="description" content="Reservasi servis motor online dengan cepat dan mudah.">
    <link rel="icon" type="image/png" href="{{ asset('images/logo-favicon.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('dist-dashboard/assets/vendor/fonts/iconify-icons.css') }}">
    <style>
        :root {
            --suzuki-blue: #0033A0;
            --navy: #0A1F44;
            --sky: #4C7DF0;
            --mist: #E8EEF9;
            --suzuki-red: #D6001C;
            --red-dark: #A80016;
            --white: #FFFFFF;
            --cloud: #F4F6FA;
            --ink: #0F172A;
            --slate: #5B6578;
        }

        * { box-sizing: border-box; }

        html, body { margin: 0; min-height: 100%; }

        body {
            color: #fff;
            font-family: 'Plus Jakarta Sans', Arial, sans-serif;
            background: var(--navy);
        }

        .services {
            padding: 120px 0 128px;
            color: var(--ink);
            background: var(--white);
        }

        .services-inner {
            width: min(1120px, calc(100% - 48px));
            margin: 0 auto;
            display: grid;
            grid-template-columns: minmax(280px, .82fr) minmax(0, 1.18fr);
            align-items: center;
            gap: 85px;
        }

        .section-kicker {
            display: inline-block;
            margin-bottom: 20px;
            padding-bottom: 8px;
            border-bottom: 2px solid var(--suzuki-red);
            color: var(--ink);
            font-size: 1.6rem;
            font-weight: 800;
            letter-spacing: -.05em;
        }

        .services h2 {
            max-width: 500px;
            margin: 0 0 18px;
            color: var(--ink);
            font-size: clamp(2.4rem, 4vw, 3.65rem);
            line-height: 1.1;
            letter-spacing: -.07em;
        }

        .services h2 strong { color: var(--suzuki-blue); }

        .services-intro {
            max-width: 430px;
            margin: 0 0 35px;
            color: var(--slate);
            font-size: .96rem;
            font-weight: 600;
            line-height: 1.65;
        }

        .services-cta {
            color: #fff;
            background: linear-gradient(100deg, var(--navy), var(--suzuki-blue));
        }

        .service-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 30px 42px;
        }

        .service-card {
            min-height: 230px;
            padding: 25px 20px 20px;
            border-radius: 17px;
            border: 1px solid var(--mist);
            background: var(--white);
        }

        .service-icon {
            width: 42px;
            height: 42px;
            margin-bottom: 15px;
            color: var(--suzuki-blue);
            font-size: 2.45rem;
            line-height: 42px;
        }

        .service-icon .bx {
            width: 42px;
            height: 42px;
        }

        .service-card h3 {
            margin: 0 0 12px;
            color: var(--navy);
            font-size: 1.22rem;
            letter-spacing: -.04em;
        }

        .service-card p {
            min-height: 42px;
            margin: 0 0 13px;
            color: var(--slate);
            font-size: .78rem;
            line-height: 1.5;
        }

        .service-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 5px 11px;
            padding-top: 12px;
            border-top: 2px solid var(--sky);
            color: var(--slate);
            font-size: .7rem;
            font-weight: 700;
        }

        .service-meta span { white-space: nowrap; }

        .service-empty {
            grid-column: 1 / -1;
            color: var(--slate);
        }

        .hero {
            isolation: isolate;
            min-height: 100vh;
            position: relative;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            background:
                linear-gradient(90deg, rgba(7, 25, 52, .66) 0%, rgba(7, 25, 52, .25) 48%, rgba(7, 25, 52, .28) 100%),
                linear-gradient(180deg, rgba(8, 24, 49, .32), rgba(8, 24, 49, .08) 58%, rgba(5, 14, 27, .88)),
                url('https://images.unsplash.com/photo-1558981806-ec527fa84c39?auto=format&fit=crop&w=2200&q=85') center / cover;
        }

        .hero::after {
            content: '';
            position: absolute;
            inset: 0;
            z-index: -1;
            background: radial-gradient(circle at 54% 50%, transparent 20%, rgba(3, 12, 27, .18) 72%, rgba(3, 12, 27, .45));
            pointer-events: none;
        }

        .navbar {
            width: min(1120px, calc(100% - 48px));
            margin: 0 auto;
            padding: 28px 0;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .brand {
            display: inline-flex;
            align-items: center;
            gap: 11px;
            min-height: 44px;
            color: #fff;
            font-size: 1.2rem;
            font-weight: 800;
            letter-spacing: -.04em;
            text-decoration: none;
        }

        .brand-logo {
            position: relative;
            width: 40px;
            height: 40px;
            flex: none;
        }

        .brand-logo::before {
            content: '';
            position: absolute;
            inset: 0;
            background: #fff;
            -webkit-mask: url('{{ asset('images/logo.png') }}') center / contain no-repeat;
            mask: url('{{ asset('images/logo.png') }}') center / contain no-repeat;
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 34px;
        }

        .nav-links a {
            color: rgba(255, 255, 255, .9);
            font-size: .82rem;
            font-weight: 600;
            text-decoration: none;
        }

        .nav-login {
            display: inline-flex !important;
            align-items: center;
            justify-content: center;
            min-height: 40px;
            padding: 0 18px;
            color: #fff !important;
            border-radius: 999px;
            background: transparent;
            font-size: .84rem !important;
            font-weight: 800 !important;
        }

        .hero-content {
            width: min(1120px, calc(100% - 48px));
            flex: 1;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 70px 0 145px;
        }

        .hero-copy { max-width: 860px; }

        .eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 14px;
            padding: 7px 14px;
            color: rgba(255, 255, 255, .95);
            background: rgba(8, 23, 45, .48);
            border: 1px solid rgba(255, 255, 255, .2);
            border-radius: 999px;
            font-size: .79rem;
            font-weight: 600;
            backdrop-filter: blur(8px);
        }

        .eyebrow::before {
            content: '◷';
            font-size: 1rem;
            line-height: 1;
        }

        h1 {
            max-width: 830px;
            margin: 0 auto 16px;
            font-size: clamp(2.55rem, 5.2vw, 4.65rem);
            line-height: 1.08;
            letter-spacing: -.06em;
        }

        .hero-copy p {
            margin: 0 auto 26px;
            color: rgba(255, 255, 255, .88);
            font-size: clamp(.9rem, 1.5vw, 1.05rem);
            line-height: 1.6;
        }

        .actions {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 12px;
        }

        .button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 13px;
            min-height: 50px;
            padding: 0 25px;
            border-radius: 999px;
            font-size: .85rem;
            font-weight: 700;
            text-decoration: none;
        }

        .button-primary { color: var(--white); background: var(--suzuki-red); }
        .button-primary:hover { background: var(--red-dark); }
        .button-outline { color: #fff; border: 1px solid rgba(255, 255, 255, .72); background: rgba(7, 24, 46, .22); }
        .arrow { font-size: 1.3rem; line-height: 0; }

        .benefits {
            position: absolute;
            bottom: 30px;
            left: 50%;
            width: min(840px, calc(100% - 48px));
            transform: translateX(-50%);
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 10px;
        }

        .benefit {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 6px 14px;
            color: rgba(255, 255, 255, .92);
            background: rgba(48, 56, 68, .58);
            border: 1px solid rgba(255, 255, 255, .13);
            border-radius: 999px;
            font-size: .73rem;
            font-weight: 600;
            backdrop-filter: blur(7px);
        }

        .check { color: #cbd6df; font-size: .95rem; }

        .feature-story {
            padding: 90px 0 110px;
            color: var(--ink);
            background: var(--white);
        }

        .feature-story-inner {
            width: min(1010px, calc(100% - 48px));
            margin: 0 auto;
        }

        .feature-row {
            min-height: 390px;
            display: grid;
            grid-template-columns: minmax(0, 1fr) minmax(280px, 1fr);
            align-items: center;
            gap: 80px;
        }

        .feature-row + .feature-row { margin-top: 40px; }
        .feature-row-reverse .feature-copy { order: 2; }
        .feature-row-reverse .feature-art { order: 1; }

        .feature-copy { max-width: 500px; }

        .feature-label {
            display: inline-block;
            margin-bottom: 16px;
            padding: 7px 14px;
            border-radius: 999px;
            color: var(--navy);
            background: var(--mist);
            font-size: .78rem;
            font-weight: 800;
        }

        .feature-copy h2 {
            margin: 0 0 17px;
            color: var(--ink);
            font-size: clamp(2rem, 3.4vw, 2.85rem);
            line-height: .98;
            letter-spacing: -.075em;
        }

        .feature-copy p {
            max-width: 480px;
            margin: 0 0 20px;
            color: var(--slate);
            font-size: .95rem;
            line-height: 1.65;
        }

        .feature-list {
            display: grid;
            gap: 6px;
            margin: 0 0 22px;
            padding: 0;
            list-style: none;
            color: var(--slate);
            font-size: .9rem;
            font-weight: 600;
        }

        .feature-list li::before {
            content: '✓';
            display: inline-grid;
            width: 17px;
            height: 17px;
            margin-right: 8px;
            place-items: center;
            border-radius: 50%;
            color: var(--white);
            background: var(--suzuki-red);
            font-size: .7rem;
            font-weight: 800;
        }

        .feature-button {
            min-height: 53px;
            padding: 0 26px;
            color: var(--suzuki-blue);
            border: 1.5px solid var(--suzuki-blue);
            background: var(--white);
        }

        .feature-art {
            min-height: 330px;
            display: grid;
            place-items: center;
            position: relative;
            overflow: hidden;
            border-radius: 50%;
            background: var(--mist);
        }

        .feature-art::before {
            content: '';
            position: absolute;
            width: 63%;
            height: 63%;
            border-radius: 50%;
            background: #D6E1FA;
        }

        .feature-art svg {
            z-index: 1;
            width: min(72%, 250px);
            height: auto;
        }

        .feature-art-phone { width: 132px !important; }
        .feature-art-toolbox { width: min(76%, 265px) !important; }

        .booking-steps {
            padding: 105px 0 125px;
            color: var(--navy);
            background: var(--white);
        }

        .booking-steps-inner {
            width: min(1120px, calc(100% - 48px));
            margin: 0 auto;
        }

        .booking-heading {
            max-width: 700px;
            margin: 0 auto 68px;
            text-align: center;
        }

        .booking-heading h2 {
            display: inline-block;
            margin: 0 0 18px;
            padding-bottom: 7px;
            border-bottom: 2px solid var(--suzuki-red);
            font-size: clamp(2rem, 3.8vw, 2.7rem);
            line-height: 1.1;
            letter-spacing: -.06em;
        }

        .booking-heading p {
            max-width: 640px;
            margin: 0 auto;
            color: var(--slate);
            font-size: 1.05rem;
            line-height: 1.45;
        }

        .booking-step-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 24px;
        }

        .booking-step {
            position: relative;
            padding-top: 106px;
        }

        .booking-step:not(:last-child)::after {
            content: '';
            position: absolute;
            z-index: 0;
            top: 40px;
            left: 50%;
            width: calc(100% + 24px);
            border-top: 2px dashed var(--sky);
        }

        .booking-number {
            position: absolute;
            z-index: 1;
            top: 0;
            left: 50%;
            width: 82px;
            height: 82px;
            transform: translateX(-50%);
            display: grid;
            place-items: center;
            border-radius: 50%;
            color: #fff;
            background: linear-gradient(155deg, var(--navy), var(--suzuki-blue));
            font-size: 2rem;
            font-weight: 800;
            letter-spacing: -.06em;
        }

        .booking-card {
            min-height: 207px;
            padding: 29px 28px 25px;
            border-radius: 17px;
            color: #fff;
            background: linear-gradient(155deg, var(--suzuki-blue) 0%, var(--navy) 100%);
        }

        .booking-card h3 {
            margin: 0 0 19px;
            font-size: 1.7rem;
            letter-spacing: -.06em;
        }

        .booking-card p {
            max-width: 315px;
            margin: 0;
            color: rgba(255, 255, 255, .92);
            font-size: .99rem;
            line-height: 1.35;
        }

        .final-cta {
            padding: 82px 0 86px;
            background: var(--white);
        }

        .final-cta-inner {
            width: min(1120px, calc(100% - 48px));
            min-height: 440px;
            margin: 0 auto;
            padding: 72px 56px;
            display: flex;
            align-items: flex-start;
            border-radius: 22px;
            color: #fff;
            background:
                linear-gradient(90deg, rgba(5, 20, 43, .9), rgba(5, 20, 43, .58)),
                url('https://images.unsplash.com/photo-1558981806-ec527fa84c39?auto=format&fit=crop&w=1800&q=85') center / cover;
        }

        .final-cta-content { max-width: 650px; }

        .final-cta h2 {
            margin: 0 0 50px;
            font-size: clamp(2.4rem, 5vw, 3.8rem);
            line-height: 1.08;
            letter-spacing: -.075em;
        }

        .final-cta h2 strong { color: var(--suzuki-red); }

        .final-cta-list {
            display: grid;
            gap: 8px;
            margin: 0;
            padding: 0;
            list-style: none;
            color: rgba(255, 255, 255, .95);
            font-size: 1rem;
            line-height: 1.45;
        }

        .final-cta-list li::before {
            content: '✓';
            display: inline-grid;
            width: 19px;
            height: 19px;
            margin-right: 9px;
            place-items: center;
            border-radius: 50%;
            color: var(--white);
            background: var(--suzuki-red);
            font-size: .72rem;
            font-weight: 800;
        }

        .site-footer {
            padding: 68px 0 28px;
            color: #fff;
            background: var(--navy);
        }

        .site-footer-inner {
            width: min(1120px, calc(100% - 48px));
            margin: 0 auto;
        }

        .footer-grid {
            display: grid;
            grid-template-columns: 1.45fr repeat(3, 1fr);
            gap: 42px;
            padding: 44px 0 70px;
            border-top: 1px solid rgba(255, 255, 255, .08);
            border-bottom: 1px solid rgba(255, 255, 255, .08);
        }

        .footer-column h3 {
            margin: 0 0 16px;
            color: #fff;
            font-size: 1.05rem;
            letter-spacing: -.04em;
        }

        .footer-column h3:not(:first-child) { margin-top: 35px; }

        .footer-column p,
        .footer-column a {
            display: block;
            margin: 0 0 10px;
            color: rgba(255, 255, 255, .65);
            font-size: .88rem;
            line-height: 1.5;
            text-decoration: none;
        }

        .footer-brand {
            display: inline-flex !important;
            align-items: center;
            gap: 11px;
            min-height: 44px;
            margin-bottom: 22px !important;
            font-size: 1.2rem !important;
            font-weight: 800;
            color: #fff !important;
        }

        .footer-description {
            max-width: 315px;
            margin-bottom: 0 !important;
        }

        .footer-newsletter p { margin-bottom: 15px; }

        .newsletter-form {
            display: flex;
            align-items: center;
            min-height: 52px;
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, .2);
            border-radius: 14px;
            background: rgba(255, 255, 255, .07);
        }

        .newsletter-form input {
            min-width: 0;
            flex: 1;
            padding: 0 16px;
            color: #fff;
            border: 0;
            outline: 0;
            background: transparent;
            font: inherit;
            font-size: .82rem;
        }

        .newsletter-form input::placeholder { color: rgba(255, 255, 255, .48); }

        .newsletter-form button {
            height: 100%;
            padding: 0 16px;
            color: #fff;
            border: 0;
            background: transparent;
            font: inherit;
            font-size: .82rem;
            font-weight: 800;
            cursor: pointer;
        }

        .footer-bottom {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
            padding-top: 25px;
            color: rgba(255, 255, 255, .52);
            font-size: .78rem;
        }

        .footer-legal {
            display: flex;
            gap: 28px;
        }

        .footer-legal a {
            color: inherit;
            text-decoration: none;
        }

        @media (max-width: 640px) {
            .navbar, .hero-content { width: min(100% - 32px, 1120px); }
            .navbar { padding-top: 21px; }
            .nav-links { gap: 15px; }
            .nav-links a:not(.nav-login) { display: none; }
            .hero-content { padding-bottom: 170px; }
            .benefits { bottom: 20px; width: calc(100% - 24px); }
            .benefit { font-size: .68rem; padding: 5px 10px; }
            .services { padding: 76px 0 84px; }
            .services-inner { width: min(100% - 32px, 1120px); grid-template-columns: 1fr; gap: 50px; }
            .section-kicker { font-size: 1.35rem; }
            .service-grid { gap: 18px; }
            .service-card { min-height: 0; padding: 20px 16px 16px; }
            .service-card h3 { font-size: 1rem; }
            .service-card p { font-size: .72rem; }
            .feature-story { padding: 68px 0 78px; }
            .feature-story-inner { width: min(100% - 32px, 1010px); }
            .feature-row,
            .feature-row-reverse {
                min-height: 0;
                grid-template-columns: 1fr;
                gap: 28px;
            }
            .feature-row + .feature-row { margin-top: 72px; }
            .feature-row-reverse .feature-copy,
            .feature-row-reverse .feature-art { order: initial; }
            .feature-copy { max-width: none; }
            .feature-copy h2 { font-size: 2rem; }
            .feature-copy p { font-size: .88rem; }
            .feature-art { min-height: 280px; }
            .booking-steps { padding: 72px 0 84px; }
            .booking-steps-inner { width: min(100% - 32px, 1120px); }
            .booking-heading { margin-bottom: 52px; }
            .booking-heading p { font-size: .9rem; }
            .booking-step-grid { grid-template-columns: 1fr; gap: 42px; }
            .booking-step { padding-top: 38px; }
            .booking-step:not(:last-child)::after {
                top: auto;
                right: auto;
                bottom: -27px;
                left: 50%;
                width: 0;
                height: 18px;
                border-top: 0;
                border-left: 2px dashed var(--sky);
            }
            .booking-number { width: 76px; height: 76px; font-size: 1.8rem; }
            .booking-card { min-height: 0; padding: 54px 24px 24px; }
            .booking-card h3 { font-size: 1.45rem; }
            .booking-card p { font-size: .88rem; }
            .final-cta { padding: 55px 0 60px; }
            .final-cta-inner {
                width: min(100% - 32px, 1120px);
                min-height: 390px;
                padding: 45px 27px;
                border-radius: 17px;
            }
            .final-cta h2 { margin-bottom: 38px; font-size: 2.45rem; }
            .final-cta-list { font-size: .86rem; }
            .site-footer { padding: 52px 0 28px; }
            .site-footer-inner { width: min(100% - 32px, 1120px); }
            .footer-grid { grid-template-columns: repeat(2, 1fr); gap: 35px 20px; padding: 35px 0 42px; }
            .footer-column:first-child { grid-column: 1 / -1; }
            .footer-column h3:not(:first-child) { margin-top: 27px; }
            .footer-bottom { align-items: flex-start; flex-direction: column; gap: 12px; }
            .footer-legal { gap: 18px; }
        }

        @media (min-width: 641px) and (max-width: 900px) {
            .services-inner { grid-template-columns: 1fr; gap: 55px; }
        }
    </style>
</head>
<body>
    <main class="hero">
        <nav class="navbar" aria-label="Navigasi utama">
            <a class="brand" href="{{ route('welcome') }}" aria-label="{{ config('app.name', 'AldiMotor') }} - Beranda">
                <span class="brand-logo" aria-hidden="true"></span>
                <span>{{ config('app.name', 'AldiMotor') }}</span>
            </a>
            <div class="nav-links">
                <a href="{{ route('welcome') }}">Beranda</a>
                <a href="#tentang">Tentang</a>
                <a href="#layanan">Layanan</a>
                @auth
                    <a class="nav-login" href="{{ route('home') }}">Dashboard</a>
                @else
                    <a class="nav-login" href="{{ route('login') }}">Masuk</a>
                @endauth
            </div>
        </nav>

        <section class="hero-content" aria-labelledby="hero-title">
            <div class="hero-copy">
                <div class="eyebrow">Reservasi Online 24 jam</div>
                <h1 id="hero-title">Servis Tanpa Antri, Cukup Booking Dan Tunggu.</h1>
                <p>Pilih layanan, tentukan jadwal, lalu datang sesuai jam Anda.</p>
                <div class="actions">
                    @auth
                        <a class="button button-primary" href="{{ route('booking-saya') }}">Reservasi sekarang <span class="arrow">→</span></a>
                    @else
                        <a class="button button-primary" href="{{ route('login') }}">Reservasi sekarang <span class="arrow">→</span></a>
                    @endauth
                    <a class="button button-outline" href="{{ route('login') }}">Lihat layanan</a>
                </div>
            </div>
        </section>

        <div class="benefits" id="tentang" aria-label="Keunggulan layanan">
            <span class="benefit"><span class="check">✓</span> Mekanik bersertifikat</span>
            <span class="benefit"><span class="check">✓</span> Sparepart asli</span>
            <span class="benefit"><span class="check">✓</span> Bayar online</span>
            <span class="benefit"><span class="check">✓</span> Garansi 7 hari</span>
        </div>
    </main>

    <section class="feature-story" aria-label="Keunggulan AldiMotor">
        <div class="feature-story-inner">
            <article class="feature-row">
                <div class="feature-copy">
                    <span class="feature-label">Booking online</span>
                    <h2>Pilih jadwal dari rumah,<br>datang tepat waktu</h2>
                    <p>Lihat jam yang masih kosong, pilih layanan, dan dapatkan kode booking dalam dua menit.</p>
                    <ul class="feature-list">
                        <li>Pilih jadwal kedatangan</li>
                        <li>Cek layanan yang tersedia</li>
                        <li>Dapatkan kode booking</li>
                    </ul>
                    <a class="button feature-button" href="{{ route('login') }}">Pilih paket <span class="arrow">→</span></a>
                </div>
                <div class="feature-art" aria-hidden="true">
                    <svg class="feature-art-phone" viewBox="0 0 170 280" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <rect x="25" y="12" width="120" height="256" rx="22" fill="#0033A0"/>
                        <rect x="34" y="31" width="102" height="218" rx="15" fill="#fff"/>
                        <rect x="34" y="31" width="102" height="43" rx="15" fill="#0033A0"/>
                        <rect x="49" y="93" width="17" height="17" rx="5" fill="#E8EEF9"/>
                        <rect x="76" y="93" width="17" height="17" rx="5" fill="#E8EEF9"/>
                        <rect x="103" y="93" width="17" height="17" rx="5" fill="#E8EEF9"/>
                        <rect x="49" y="121" width="17" height="17" rx="5" fill="#E8EEF9"/>
                        <rect x="76" y="121" width="17" height="17" rx="5" fill="#E8EEF9"/>
                        <rect x="103" y="121" width="17" height="17" rx="5" fill="#E8EEF9"/>
                        <rect x="49" y="149" width="17" height="17" rx="5" fill="#E8EEF9"/>
                        <rect x="76" y="149" width="17" height="17" rx="5" fill="#E8EEF9"/>
                        <rect x="103" y="149" width="17" height="17" rx="5" fill="#E8EEF9"/>
                        <rect x="49" y="183" width="53" height="14" rx="7" fill="#0033A0"/>
                        <rect x="49" y="207" width="71" height="14" rx="7" fill="#E8EEF9"/>
                        <circle cx="142" cy="45" r="25" fill="#D6001C"/>
                        <path d="m130 45 8 8 16-18" stroke="#fff" stroke-width="6" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
            </article>

            <article class="feature-row feature-row-reverse">
                <div class="feature-copy">
                    <span class="feature-label">Dikerjakan langsung</span>
                    <h2>Mekanik bersertifikat dengan<br>sparepart asli</h2>
                    <p>Setiap motor ditangani mekanik berpengalaman. Biaya dan sparepart dijelaskan sebelum dikerjakan.</p>
                    <ul class="feature-list">
                        <li>Mekanik berpengalaman</li>
                        <li>Estimasi biaya transparan</li>
                        <li>Sparepart berkualitas</li>
                    </ul>
                    <a class="button feature-button" href="{{ route('login') }}">Pilih paket <span class="arrow">→</span></a>
                </div>
                <div class="feature-art" aria-hidden="true">
                    <svg class="feature-art-toolbox" viewBox="0 0 280 230" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="m57 55 38 52" stroke="#0033A0" stroke-width="13" stroke-linecap="round"/>
                        <path d="m51 49 17 2-12 15-12-10 7-7Z" fill="#0033A0"/>
                        <circle cx="212" cy="54" r="26" fill="#D6001C"/>
                        <path d="M212 36v36M194 54h36M200 42l24 24M224 42l-24 24" stroke="#fff" stroke-width="6"/>
                        <rect x="50" y="91" width="180" height="111" rx="18" fill="#4C7DF0"/>
                        <rect x="39" y="76" width="202" height="45" rx="14" fill="#0033A0"/>
                        <path d="M111 76V61c0-10 8-18 18-18h43c10 0 18 8 18 18v15" stroke="#0033A0" stroke-width="13"/>
                        <rect x="119" y="113" width="50" height="25" rx="7" fill="#0033A0"/>
                        <path d="M125 162h30M140 147v30" stroke="#fff" stroke-width="13" stroke-linecap="round"/>
                        <path d="m229 153 38 14v33c0 13-9 24-19 29-10-5-19-16-19-29v-33Z" fill="#0033A0" stroke="#fff" stroke-width="5"/>
                        <path d="m239 184 7 7 15-17" stroke="#fff" stroke-width="6" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
            </article>

            <article class="feature-row">
                <div class="feature-copy">
                    <span class="feature-label">Pantau status</span>
                    <h2>Tahu kapan motor selesai<br>tanpa bolak-balik bertanya</h2>
                    <p>Setiap perubahan status dikirim ke WhatsApp. Atau cek sendiri di halaman riwayat.</p>
                    <ul class="feature-list">
                        <li>Notifikasi progres servis</li>
                        <li>Riwayat servis tersimpan</li>
                        <li>Status selesai mudah dipantau</li>
                    </ul>
                    <a class="button feature-button" href="{{ route('login') }}">Pilih paket <span class="arrow">→</span></a>
                </div>
                <div class="feature-art" aria-hidden="true">
                    <svg class="feature-art-phone" viewBox="0 0 170 280" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <rect x="25" y="12" width="120" height="256" rx="22" fill="#0033A0"/>
                        <rect x="34" y="31" width="102" height="218" rx="15" fill="#fff"/>
                        <rect x="34" y="31" width="102" height="43" rx="15" fill="#0033A0"/>
                        <circle cx="51" cy="52" r="8" fill="#D6001C"/>
                        <rect x="65" y="47" width="48" height="10" rx="5" fill="#fff"/>
                        <rect x="47" y="92" width="69" height="38" rx="10" fill="#E8EEF9"/>
                        <rect x="57" y="103" width="35" height="6" rx="3" fill="#B7C7E8"/>
                        <rect x="57" y="114" width="25" height="6" rx="3" fill="#B7C7E8"/>
                        <rect x="47" y="141" width="69" height="38" rx="10" fill="#E8EEF9"/>
                        <rect x="57" y="152" width="35" height="6" rx="3" fill="#B7C7E8"/>
                        <rect x="57" y="163" width="25" height="6" rx="3" fill="#B7C7E8"/>
                        <rect x="52" y="190" width="70" height="37" rx="11" fill="#4C7DF0"/>
                        <path d="M63 204h39M63 214h25" stroke="#fff" stroke-width="6" stroke-linecap="round"/>
                        <circle cx="142" cy="44" r="25" fill="#D92C43"/>
                        <path d="M142 31v21M135 56h14M137 28c0-7 10-7 10 0" stroke="#fff" stroke-width="5" stroke-linecap="round"/>
                    </svg>
                </div>
            </article>
        </div>
    </section>

    <section class="services" id="layanan" aria-labelledby="services-title">
        <div class="services-inner">
            <div class="services-copy">
                <span class="section-kicker">Layanan Kami</span>
                <h2 id="services-title">Servis Bergaransi,<br><strong>Kualitas Terbukti</strong></h2>
                <p class="services-intro">Pilih layanan yang kamu butuhkan, lihat detail pengerjaannya, lalu serahkan motormu untuk ditangani dengan teliti.</p>
                <a class="button services-cta" href="{{ route('login') }}">Semua layanan <span class="arrow">→</span></a>
            </div>

            <div class="service-grid">
                @forelse ($layanans as $layanan)
                    @php
                        $namaLayanan = strtolower($layanan->nama);
                        $ikonLayanan = str_contains($namaLayanan, 'oli') ? 'bx-droplet'
                            : (str_contains($namaLayanan, 'rem') ? 'bx-wrench'
                            : (str_contains($namaLayanan, 'tune') ? 'bx-refresh' : 'bx-cog'));
                    @endphp
                    <article class="service-card">
                        <div class="service-icon" aria-hidden="true"><i class="icon-base bx {{ $ikonLayanan }}"></i></div>
                        <h3>{{ $layanan->nama }}</h3>
                        <p>{{ $layanan->deskripsi ?: 'Perawatan motor dengan pengerjaan rapi dan teliti.' }}</p>
                        <div class="service-meta">
                            <span>{{ $layanan->durasi_menit }} menit</span>
                            @if ($layanan->interval_servis_km)
                                <span>± {{ number_format($layanan->interval_servis_km, 0, ',', '.') }} km</span>
                            @endif
                            @if ($layanan->interval_servis_hari)
                                <span>{{ $layanan->interval_servis_hari }} hari</span>
                            @endif
                        </div>
                    </article>
                @empty
                    <p class="service-empty">Layanan servis sedang dipersiapkan.</p>
                @endforelse
            </div>
        </div>
    </section>

    <section class="booking-steps" aria-labelledby="booking-steps-title">
        <div class="booking-steps-inner">
            <header class="booking-heading">
                <h2 id="booking-steps-title">Cara Reservasi</h2>
                <p>Hanya butuh beberapa menit. Yuk, atur jadwal servis motor Anda melalui langkah-langkah di bawah.</p>
            </header>

            <div class="booking-step-grid">
                <article class="booking-step">
                    <span class="booking-number">01</span>
                    <div class="booking-card">
                        <h3>Pilih Layanan</h3>
                        <p>Temukan paket servis ideal untuk motor Anda, mulai dari ganti oli hingga tune-up lengkap.</p>
                    </div>
                </article>
                <article class="booking-step">
                    <span class="booking-number">02</span>
                    <div class="booking-card">
                        <h3>Pilih Jadwal</h3>
                        <p>Pilih tanggal dan jam yang paling sesuai, lalu isi data motor Anda dengan lengkap.</p>
                    </div>
                </article>
                <article class="booking-step">
                    <span class="booking-number">03</span>
                    <div class="booking-card">
                        <h3>Datang &amp; Servis</h3>
                        <p>Datang sesuai jadwal dan nikmati proses servis yang rapi, cepat, dan transparan.</p>
                    </div>
                </article>
            </div>
        </div>
    </section>

    <section class="final-cta" aria-labelledby="final-cta-title">
        <div class="final-cta-inner">
            <div class="final-cta-content">
                <h2 id="final-cta-title">Siap servis <strong>tanpa antre?</strong></h2>
                <ul class="final-cta-list">
                    <li>Pilih jadwal dari rumah</li>
                    <li>Batal gratis sampai 24 jam sebelum jadwal</li>
                    <li>Bayar dibengkel setelah servis selesai</li>
                </ul>
            </div>
        </div>
    </section>

    <footer class="site-footer">
        <div class="site-footer-inner">
            <div class="footer-grid">
                <div class="footer-column">
                    <a class="footer-brand" href="{{ route('welcome') }}" aria-label="{{ config('app.name', 'AldiMotor') }} - Beranda">
                        <span class="brand-logo" aria-hidden="true"></span>
                        <span>{{ config('app.name', 'AldiMotor') }}</span>
                    </a>
                    <p class="footer-description">Platform booking servis kendaraan yang membantu Anda menemukan jadwal servis tanpa antre dan mendapatkan informasi layanan servis ringan dengan lebih cepat.</p>
                    <h3>Alamat</h3>
                    <p></p>
                </div>
                <div class="footer-column">
                    <h3>Layanan</h3>
                    <a href="{{ route('login') }}">Servis ringan</a>
                    <a href="{{ route('login') }}">Servis berkala</a>
                    <a href="{{ route('login') }}">Perawatan rutin</a>
                    <a href="{{ route('login') }}">Konsultasi servis</a>
                    <h3>Jam operasional</h3>
                    <p>{{ $jamLayanan }}</p>
                </div>
                <div class="footer-column">
                    <h3>Bantuan</h3>
                    <a href="#booking-steps-title">Cara booking</a>
                    <a href="#tentang">Syarat &amp; ketentuan</a>
                    <a href="#tentang">FAQ</a>
                    <a href="#tentang">Hubungi kami</a>
                    <h3>Media sosial</h3>
                    <a href="#tentang">Instagram</a>
                    <a href="#tentang">Facebook</a>
                    <a href="#tentang">WhatsApp</a>
                </div>
                <div class="footer-column footer-newsletter">
                    <h3>Kontak</h3>
                    @if ($kontakBengkel?->no_hp)
                        <p>{{ $kontakBengkel->no_hp }}</p>
                    @endif
                    @if ($kontakBengkel?->email)
                        <p>{{ $kontakBengkel->email }}</p>
                    @endif
                    @if (!$kontakBengkel?->no_hp && !$kontakBengkel?->email)
                        <p>Kontak belum tersedia.</p>
                    @endif
                    <h3>Newsletter</h3>
                    <p>Dapatkan informasi promo, jadwal servis, dan tips perawatan kendaraan.</p>
                    <div class="newsletter-form">
                        <input type="email" placeholder="Email Anda" aria-label="Email Anda">
                        <button type="button">Daftar</button>
                    </div>
                </div>
            </div>
            <div class="footer-bottom">
                <span>&copy; {{ date('Y') }} {{ config('app.name', 'AldiMotor') }}. All rights reserved.</span>
                <div class="footer-legal">
                    <a href="#tentang">Made By Team TULUS UTY</a>
                </div>
            </div>
        </div>
    </footer>
    <script>
        document.addEventListener('click', (event) => {
            const link = event.target.closest('a[href^="#"]');

            if (!link) {
                return;
            }

            const targetId = link.getAttribute('href').slice(1);
            const target = document.getElementById(targetId);

            if (!target) {
                return;
            }

            event.preventDefault();
            target.scrollIntoView({
                behavior: window.matchMedia('(prefers-reduced-motion: reduce)').matches
                    ? 'auto'
                    : 'smooth',
                block: 'start',
            });
            window.history.pushState(null, '', `#${targetId}`);
        });
    </script>
</body>
</html>
