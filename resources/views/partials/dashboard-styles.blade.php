<style>
        .dashboard-page {
            width: 100%;
            max-width: 1240px;
            margin: 0 auto;
        }

        .dashboard-header {
            margin-bottom: 1.5rem;
        }

        .dashboard-header__title {
            margin: 0;
            color: #191923;
            font-size: clamp(1.55rem, 2.2vw, 2.12rem);
            font-weight: 900;
            letter-spacing: -0.045em;
            line-height: 1.15;
        }

        .dashboard-header__time {
            display: flex;
            align-items: center;
            gap: 0.45rem;
            margin: 0.52rem 0 0;
            color: #64748b;
            font-size: 0.82rem;
            font-weight: 700;
            line-height: 1.4;
        }

        .dashboard-header__time span {
            color: #cbd5e1;
        }

        .customer-summary-card {
            position: relative;
            overflow: hidden;
            border: 1px solid #dbe5f2;
            border-radius: 22px;
            background:
                radial-gradient(circle at 100% 0%, rgba(59, 130, 246, 0.11), transparent 32%),
                linear-gradient(145deg, #ffffff 0%, #f8fbff 100%);
            box-shadow:
                0 18px 46px rgba(15, 23, 42, 0.09),
                0 2px 7px rgba(15, 23, 42, 0.04);
            transition: transform 180ms ease, box-shadow 180ms ease;
        }

        .customer-summary-card::before {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(90deg, #2563eb 0%, #38bdf8 48%, #22c55e 100%);
            content: "";
        }

        .customer-summary-card:hover {
            transform: translateY(-2px);
            box-shadow:
                0 23px 52px rgba(15, 23, 42, 0.12),
                0 4px 12px rgba(37, 99, 235, 0.08);
        }

        .customer-summary-card__top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1.25rem;
            padding: 1.65rem 1.7rem 1.35rem;
        }

        .customer-summary-card__title {
            display: flex;
            align-items: center;
            gap: 0.62rem;
            margin: 0;
            color: #191923;
            font-size: 1.22rem;
            font-weight: 900;
            letter-spacing: -0.028em;
        }

        .customer-summary-card__title::before {
            display: block;
            width: 10px;
            height: 10px;
            flex: 0 0 auto;
            border-radius: 50%;
            background: #2563eb;
            box-shadow: 0 0 0 5px rgba(37, 99, 235, 0.12);
            content: "";
        }

        .customer-summary-card__description {
            display: none;
        }

        .customer-summary-card__total {
            position: relative;
            min-width: 142px;
            overflow: hidden;
            padding: 0.82rem 1rem;
            border: 1px solid rgba(147, 197, 253, 0.62);
            border-radius: 15px;
            background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
            text-align: right;
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.85);
        }

        .customer-summary-card__total::after {
            position: absolute;
            right: -17px;
            bottom: -24px;
            width: 70px;
            height: 70px;
            border-radius: 50%;
            background: rgba(37, 99, 235, 0.1);
            content: "";
        }

        .customer-summary-card__total span {
            position: relative;
            z-index: 1;
            display: block;
            color: #3b5c91;
            font-size: 0.67rem;
            font-weight: 850;
            letter-spacing: 0.02em;
        }

        .customer-summary-card__total strong {
            position: relative;
            z-index: 1;
            display: block;
            margin-top: 0.28rem;
            color: #4318e4;
            font-size: 1.85rem;
            font-weight: 950;
            letter-spacing: -0.06em;
            line-height: 1;
        }

        .customer-summary-card__divider {
            height: 1px;
            margin: 0 1.7rem;
            background: linear-gradient(90deg, transparent, #dbe5f2 10%, #dbe5f2 90%, transparent);
        }

        .customer-summary-card__stats {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 1rem;
            padding: 1.3rem 1.7rem 1.15rem;
        }

        .customer-status {
            position: relative;
            overflow: hidden;
            min-height: 145px;
            padding: 1rem 1.1rem;
            border: 1px solid transparent;
            border-radius: 16px;
            transition: transform 170ms ease, box-shadow 170ms ease;
        }

        .customer-status:hover {
            transform: translateY(-2px);
        }

        .customer-status::after {
            position: absolute;
            right: -20px;
            bottom: -35px;
            width: 105px;
            height: 105px;
            border-radius: 50%;
            content: "";
        }

        .customer-status--online {
            border-color: #bbf7d0;
            background: linear-gradient(135deg, #f0fdf4 0%, #ecfdf5 100%);
            box-shadow: 0 7px 18px rgba(22, 163, 74, 0.07);
        }

        .customer-status--online::after {
            background: rgba(34, 197, 94, 0.1);
        }

        .customer-status--offline {
            border-color: #fecaca;
            background: linear-gradient(135deg, #fff7f7 0%, #fff1f2 100%);
            box-shadow: 0 7px 18px rgba(239, 68, 68, 0.065);
        }

        .customer-status--offline::after {
            background: rgba(239, 68, 68, 0.09);
        }

        .customer-status__header,
        .customer-status__value,
        .customer-status__caption,
        .customer-status__isolated {
            position: relative;
            z-index: 1;
        }

        .customer-status__header {
            display: flex;
            align-items: center;
            gap: 0.52rem;
        }

        .customer-status__indicator {
            width: 9px;
            height: 9px;
            flex: 0 0 auto;
            border-radius: 999px;
        }

        .customer-status__indicator--online {
            background: #22c55e;
            box-shadow: 0 0 0 5px rgba(34, 197, 94, 0.15);
        }

        .customer-status__indicator--offline {
            background: #ef4444;
            box-shadow: 0 0 0 5px rgba(239, 68, 68, 0.13);
        }

        .customer-status__label {
            color: #475569;
            font-size: 0.77rem;
            font-weight: 900;
            letter-spacing: 0.01em;
        }

        .customer-status__value {
            display: block;
            margin-top: 0.85rem;
            color: #191923;
            font-size: clamp(2.15rem, 4vw, 2.8rem);
            font-weight: 950;
            letter-spacing: -0.07em;
            line-height: 0.92;
        }

        .customer-status__caption {
            display: block;
            margin-top: 0.65rem;
            color: #64748b;
            font-size: 0.71rem;
            font-weight: 750;
            line-height: 1.35;
        }

        .customer-status__isolated {
            display: inline-flex;
            align-items: center;
            margin-top: 0.6rem;
            padding: 0.32rem 0.56rem;
            border: 1px solid #fed7aa;
            border-radius: 999px;
            background: #fff7ed;
            color: #c2410c;
            font-size: 0.66rem;
            font-weight: 850;
            line-height: 1;
            box-shadow: 0 1px 1px rgba(194, 65, 12, 0.05);
        }

        .customer-summary-card__progress-wrap {
            padding: 0.15rem 1.7rem 1.55rem;
        }

        .customer-summary-card__progress {
            display: flex;
            width: 100%;
            height: 14px;
            overflow: hidden;
            border: 1px solid rgba(148, 163, 184, 0.16);
            border-radius: 999px;
            background: #e5e7eb;
            box-shadow: inset 0 1px 2px rgba(15, 23, 42, 0.08);
        }

        .customer-summary-card__progress-online {
            display: block;
            min-width: 0;
            height: 100%;
            background: linear-gradient(90deg, #16a34a 0%, #4ade80 100%);
            box-shadow: inset -1px 0 0 rgba(255, 255, 255, 0.4);
            transition: width 300ms ease;
        }

        .customer-summary-card__progress-offline {
            display: block;
            min-width: 0;
            height: 100%;
            background: linear-gradient(90deg, #fb7185 0%, #ef4444 100%);
            transition: width 300ms ease;
        }

        .dark .dashboard-header__title {
            color: #f8fafc;
        }

        .dark .dashboard-header__time {
            color: #94a3b8;
        }

        .dark .dashboard-header__time span {
            color: #475569;
        }

        .dark .customer-summary-card {
            border-color: rgba(71, 85, 105, 0.72);
            background:
                radial-gradient(circle at 100% 0%, rgba(59, 130, 246, 0.16), transparent 33%),
                linear-gradient(145deg, #232235 0%, #1d1b2a 100%);
            box-shadow: 0 18px 46px rgba(0, 0, 0, 0.24);
        }

        .dark .customer-summary-card__title,
        .dark .customer-status__value {
            color: #f8fafc;
        }

        .dark .customer-summary-card__total {
            border-color: rgba(96, 165, 250, 0.25);
            background: linear-gradient(135deg, rgba(30, 64, 175, 0.35), rgba(30, 58, 138, 0.26));
        }

        .dark .customer-summary-card__total span {
            color: #bfdbfe;
        }

        .dark .customer-summary-card__total strong {
            color: #93c5fd;
        }

        .dark .customer-summary-card__divider {
            background: linear-gradient(90deg, transparent, #334155 10%, #334155 90%, transparent);
        }

        .dark .customer-status--online {
            border-color: rgba(34, 197, 94, 0.24);
            background: linear-gradient(135deg, rgba(20, 83, 45, 0.42), rgba(6, 78, 59, 0.24));
        }

        .dark .customer-status--offline {
            border-color: rgba(248, 113, 113, 0.24);
            background: linear-gradient(135deg, rgba(127, 29, 29, 0.32), rgba(136, 19, 55, 0.2));
        }

        .dark .customer-status__label {
            color: #cbd5e1;
        }

        .dark .customer-status__caption {
            color: #94a3b8;
        }

        .dark .customer-status__isolated {
            border-color: rgba(251, 146, 60, 0.27);
            background: rgba(154, 52, 18, 0.24);
            color: #fdba74;
        }

        .dark .customer-summary-card__progress {
            border-color: rgba(71, 85, 105, 0.65);
            background: #334155;
        }

        @media (max-width: 720px) {
            .dashboard-header {
                margin-bottom: 1.15rem;
            }

            .customer-summary-card {
                border-radius: 17px;
            }

            .customer-summary-card__top {
                align-items: flex-start;
                flex-direction: column;
                gap: 1rem;
                padding: 1.25rem 1.12rem 1.05rem;
            }

            .customer-summary-card__total {
                width: 100%;
                text-align: left;
            }

            .customer-summary-card__divider {
                margin: 0 1.12rem;
            }

            .customer-summary-card__stats {
                grid-template-columns: 1fr;
                gap: 0.75rem;
                padding: 1rem 1.12rem;
            }

            .customer-status {
                min-height: 130px;
                padding: 0.92rem 1rem;
            }

            .customer-summary-card__progress-wrap {
                padding: 0.1rem 1.12rem 1.2rem;
            }
        }

        /* Concept D — empat metrik dengan aksen vertikal */
        .customer-summary-card {
            display: grid;
            grid-template-columns: minmax(0, 1fr);
            border: 1px solid #e2e8f0;
            border-radius: 18px;
            background: #ffffff;
            box-shadow: 0 14px 34px rgba(15, 23, 42, 0.07);
        }

        .customer-summary-card::before,
        .customer-summary-card::after {
            display: none;
        }

        .customer-summary-card__accent {
            grid-row: 1 / span 2;
            width: 7px;
            background: linear-gradient(180deg, #2563eb 0%, #38bdf8 48%, #22c55e 100%);
        }

        .customer-summary-card__heading {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            padding: 1.35rem 1.45rem 1rem;
        }

        .customer-summary-card__eyebrow {
            margin: 0 0 0.28rem;
            color: #64748b;
            font-size: 0.65rem;
            font-weight: 850;
            letter-spacing: 0.12em;
        }

        .customer-summary-card__title {
            margin: 0;
            color: #191923;
            font-size: 1.15rem;
            font-weight: 900;
            letter-spacing: -0.03em;
        }

        .customer-summary-card__title::before {
            display: none;
        }

        .customer-summary-card__live {
            display: inline-flex;
            align-items: center;
            gap: 0.42rem;
            padding: 0.42rem 0.62rem;
            border: 1px solid #bbf7d0;
            border-radius: 999px;
            background: #f0fdf4;
            color: #15803d;
            font-size: 0.67rem;
            font-weight: 850;
        }

        .customer-summary-card__live i {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: #22c55e;
            box-shadow: 0 0 0 4px rgba(34, 197, 94, 0.12);
        }

        .customer-summary-card__metrics {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            border-top: 1px solid #eef2f7;
        }

        .customer-metric {
            position: relative;
            min-width: 0;
            padding: 1.1rem 1.45rem 1.25rem;
        }

        .customer-metric + .customer-metric {
            border-left: 1px solid #eef2f7;
        }

        .customer-metric__label {
            display: flex;
            align-items: center;
            gap: 0.4rem;
            color: #64748b;
            font-size: 0.72rem;
            font-weight: 800;
            white-space: nowrap;
        }

        .customer-metric__dot {
            width: 8px;
            height: 8px;
            flex: 0 0 auto;
            border-radius: 50%;
            background: currentColor;
        }

        .customer-metric__value {
            display: block;
            margin-top: 0.58rem;
            color: #191923;
            font-size: clamp(1.8rem, 3.1vw, 2.45rem);
            font-weight: 950;
            letter-spacing: -0.065em;
            line-height: 1;
        }

        .customer-metric__detail {
            display: block;
            margin-top: 0.48rem;
            color: #94a3b8;
            font-size: 0.67rem;
            font-weight: 700;
            line-height: 1.3;
        }

        .customer-metric--total {
            background: linear-gradient(180deg, rgba(239, 246, 255, 0.78), rgba(255, 255, 255, 0));
        }

        .customer-metric--total .customer-metric__value {
            color: #2563eb;
        }

        .customer-metric--online .customer-metric__label {
            color: #16a34a;
        }

        .customer-metric--online .customer-metric__value {
            color: #15803d;
        }

        .customer-metric--offline .customer-metric__label {
            color: #dc2626;
        }

        .customer-metric--offline .customer-metric__value {
            color: #b91c1c;
        }

        .customer-metric--isolated .customer-metric__label {
            color: #d97706;
        }

        .customer-metric--isolated .customer-metric__value {
            color: #b45309;
        }

        .dark .customer-summary-card {
            border-color: #334155;
            background: #1d1b2a;
            box-shadow: 0 14px 34px rgba(0, 0, 0, 0.24);
        }

        .dark .customer-summary-card__heading,
        .dark .customer-metric__value {
            color: #f8fafc;
        }

        .dark .customer-summary-card__eyebrow,
        .dark .customer-metric__detail {
            color: #94a3b8;
        }

        .dark .customer-summary-card__title {
            color: #f8fafc;
        }

        .dark .customer-summary-card__metrics,
        .dark .customer-metric + .customer-metric {
            border-color: #334155;
        }

        .dark .customer-metric--total {
            background: linear-gradient(180deg, rgba(30, 64, 175, 0.2), rgba(17, 24, 39, 0));
        }

        .dark .customer-metric--total .customer-metric__value {
            color: #93c5fd;
        }

        .dark .customer-metric--online .customer-metric__value {
            color: #86efac;
        }

        .dark .customer-metric--offline .customer-metric__value {
            color: #fca5a5;
        }

        .dark .customer-metric--isolated .customer-metric__value {
            color: #fcd34d;
        }

        @media (max-width: 920px) {
            .customer-summary-card__metrics {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .customer-metric:nth-child(3) {
                border-left: 0;
                border-top: 1px solid #eef2f7;
            }

            .customer-metric:nth-child(4) {
                border-top: 1px solid #eef2f7;
            }

            .dark .customer-metric:nth-child(3),
            .dark .customer-metric:nth-child(4) {
                border-top-color: #334155;
            }
        }

        @media (max-width: 560px) {
            .customer-summary-card {
                grid-template-columns: 5px minmax(0, 1fr);
                border-radius: 15px;
            }

            .customer-summary-card__accent {
                width: 5px;
            }

            .customer-summary-card__heading {
                align-items: flex-start;
                flex-direction: column;
                padding: 1.1rem 1rem 0.9rem;
            }

            .customer-summary-card__metrics {
                grid-template-columns: 1fr;
            }

            .customer-metric {
                padding: 0.9rem 1rem;
            }

            .customer-metric + .customer-metric,
            .customer-metric:nth-child(3),
            .customer-metric:nth-child(4) {
                border-top: 1px solid #eef2f7;
                border-left: 0;
            }

            .dark .customer-metric + .customer-metric,
            .dark .customer-metric:nth-child(3),
            .dark .customer-metric:nth-child(4) {
                border-top-color: #334155;
            }
        }


        /* Mobile: empat metrik tetap horizontal dan dapat digeser bila layar sempit */
        @media (max-width: 920px) {
            .customer-summary-card__metrics {
                display: flex;
                overflow-x: auto;
                grid-template-columns: none;
                -webkit-overflow-scrolling: touch;
                scrollbar-width: thin;
                scrollbar-color: #cbd5e1 transparent;
            }

            .customer-summary-card__metrics::-webkit-scrollbar {
                height: 5px;
            }

            .customer-summary-card__metrics::-webkit-scrollbar-thumb {
                border-radius: 999px;
                background: #cbd5e1;
            }

            .customer-metric {
                min-width: 145px;
                flex: 1 0 145px;
            }

            .customer-metric + .customer-metric,
            .customer-metric:nth-child(3),
            .customer-metric:nth-child(4) {
                border-top: 0;
                border-left: 1px solid #eef2f7;
            }

            .dark .customer-metric + .customer-metric,
            .dark .customer-metric:nth-child(3),
            .dark .customer-metric:nth-child(4) {
                border-top-color: transparent;
                border-left-color: #334155;
            }
        }

        @media (max-width: 560px) {
            .customer-summary-card__metrics {
                grid-template-columns: none;
            }

            .customer-metric {
                min-width: 132px;
                flex-basis: 132px;
                padding: 0.9rem 0.85rem 1rem;
            }

            .customer-metric__label {
                font-size: 0.66rem;
            }

            .customer-metric__value {
                font-size: 1.95rem;
            }

            .customer-metric__detail {
                font-size: 0.61rem;
            }

            .customer-metric + .customer-metric,
            .customer-metric:nth-child(3),
            .customer-metric:nth-child(4) {
                border-top: 0;
                border-left: 1px solid #eef2f7;
            }

            .dark .customer-metric + .customer-metric,
            .dark .customer-metric:nth-child(3),
            .dark .customer-metric:nth-child(4) {
                border-top-color: transparent;
                border-left-color: #334155;
            }
        }


        /* Total pelanggan di kanan header, khusus tampilan mobile */
        .customer-summary-card__heading-main {
            min-width: 0;
        }

        .customer-summary-card__mobile-total {
            display: none;
        }

        @media (max-width: 560px) {
            .customer-summary-card__heading {
                align-items: flex-start;
                flex-direction: row;
                justify-content: space-between;
                gap: 0.75rem;
            }

            .customer-summary-card__live {
                margin-top: 0.65rem;
            }

            .customer-summary-card__mobile-total {
                display: flex;
                min-width: 96px;
                min-height: 56px;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                padding: 0.45rem 0.65rem;
                border: 1px solid #bfdbfe;
                border-radius: 11px;
                background: #eff6ff;
                text-align: center;
            }

            .customer-summary-card__mobile-total span {
                display: block;
                color: #3b5c91;
                font-size: 0.58rem;
                font-weight: 850;
                line-height: 1.2;
                white-space: nowrap;
            }

            .customer-summary-card__mobile-total strong {
                display: block;
                margin-top: 0.18rem;
                color: #4318e4;
                font-size: 1.45rem;
                font-weight: 950;
                letter-spacing: -0.06em;
                line-height: 1;
            }

            .customer-metric--total {
                display: none;
            }

            .customer-summary-card__metrics {
                grid-template-columns: none;
            }

            .dark .customer-summary-card__mobile-total {
                border-color: rgba(96, 165, 250, 0.28);
                background: rgba(30, 64, 175, 0.28);
            }

            .dark .customer-summary-card__mobile-total span {
                color: #bfdbfe;
            }

            .dark .customer-summary-card__mobile-total strong {
                color: #93c5fd;
            }
        }




        /* Border hijau halus: aman, tanpa pseudo-element dan tanpa mask */
        .customer-summary-card {
            border: 2px solid transparent;
            background:
                linear-gradient(#ffffff, #ffffff) padding-box,
                linear-gradient(
                    90deg,
                    #16a34a 0%,
                    #86efac 20%,
                    #dcfce7 40%,
                    #22c55e 55%,
                    #15803d 75%,
                    #86efac 100%
                ) border-box;
            background-size: 100% 100%, 300% 100%;
            background-position: 0 0, 0 0;
            animation: customer-card-green-flow 5s linear infinite;
        }

        @keyframes customer-card-green-flow {
            from {
                background-position: 0 0, 0 0;
            }
            to {
                background-position: 0 0, 300% 0;
            }
        }

        @media (prefers-reduced-motion: reduce) {
            .customer-summary-card {
                animation: none;
            }
        }



        /* customer-card-layout-repair:
           Hapus sisa layout aksen kiri agar isi card kembali sejajar */
        .customer-summary-card {
            grid-template-columns: minmax(0, 1fr) !important;
        }

        .customer-summary-card__accent {
            display: none !important;
        }

        /* Header desktop/tablet: konten kiri dan status tetap rapi */
        .customer-summary-card__heading {
            grid-column: 1 !important;
        }

        .customer-summary-card__metrics {
            grid-column: 1 !important;
        }

        /* Mobile: judul/status di kiri, total pelanggan di kanan */
        @media (max-width: 560px) {
            .customer-summary-card {
                grid-template-columns: minmax(0, 1fr) !important;
            }

            .customer-summary-card__heading {
                display: flex;
                flex-direction: row;
                align-items: flex-start;
                justify-content: space-between;
                gap: 0.75rem;
                padding: 1.1rem 1rem 0.9rem;
            }

            .customer-summary-card__heading-main {
                flex: 1 1 auto;
                min-width: 0;
            }

            .customer-summary-card__mobile-total {
                flex: 0 0 96px;
                min-width: 96px;
                margin-left: auto;
            }

            .customer-summary-card__metrics {
                display: flex;
                grid-template-columns: none;
                overflow-x: auto;
            }
        }


        /* Card status dapat diklik untuk membuka daftar pelanggan */
        .customer-metric--clickable {
            position: relative;
            cursor: pointer;
            transition: background-color 180ms ease, transform 180ms ease;
            -webkit-tap-highlight-color: transparent;
        }

        .customer-metric--clickable:hover {
            background-color: rgba(248, 250, 252, 0.92);
        }

        .customer-metric--clickable:focus-visible {
            z-index: 1;
            outline: 3px solid rgba(34, 197, 94, 0.4);
            outline-offset: -3px;
        }

        .customer-metric--clickable:active {
            transform: scale(0.985);
        }

        .customer-metric__action {
            display: block;
            margin-top: 0.55rem;
            color: #2563eb;
            font-size: 0.64rem;
            font-weight: 850;
            letter-spacing: 0.01em;
        }

        .customer-metric--online .customer-metric__action {
            color: #15803d;
        }

        .customer-metric--offline .customer-metric__action {
            color: #b91c1c;
        }

        .customer-metric--isolated .customer-metric__action {
            color: #b45309;
        }

        .dark .customer-metric--clickable:hover {
            background-color: rgba(30, 41, 59, 0.72);
        }

        /* Modal pelanggan */
        .customer-status-modal {
            position: fixed;
            z-index: 9999;
            inset: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
            visibility: hidden;
            opacity: 0;
            transition: opacity 180ms ease, visibility 180ms ease;
        }

        .customer-status-modal.is-open {
            visibility: visible;
            opacity: 1;
        }

        .customer-status-modal__backdrop {
            position: absolute;
            inset: 0;
            background: rgba(15, 23, 42, 0.58);
            backdrop-filter: blur(3px);
        }

        .customer-status-modal__panel {
            position: relative;
            z-index: 1;
            display: flex;
            width: min(100%, 620px);
            max-height: min(80vh, 680px);
            flex-direction: column;
            overflow: hidden;
            border: 1px solid #dbeafe;
            border-radius: 18px;
            background: #ffffff;
            box-shadow: 0 24px 70px rgba(15, 23, 42, 0.34);
            transform: translateY(10px) scale(0.98);
            transition: transform 180ms ease;
        }

        .customer-status-modal.is-open .customer-status-modal__panel {
            transform: translateY(0) scale(1);
        }

        .customer-status-modal__header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 1rem;
            padding: 1.2rem 1.25rem 1rem;
            border-bottom: 1px solid #e2e8f0;
        }

        .customer-status-modal__kicker {
            margin: 0 0 0.28rem;
            color: #16a34a;
            font-size: 0.66rem;
            font-weight: 900;
            letter-spacing: 0.11em;
        }

        .customer-status-modal__title {
            margin: 0;
            color: #191923;
            font-size: 1.2rem;
            font-weight: 900;
            line-height: 1.2;
        }

        .customer-status-modal__count {
            margin: 0.35rem 0 0;
            color: #64748b;
            font-size: 0.78rem;
            font-weight: 700;
        }

        .customer-status-modal__close {
            display: inline-flex;
            width: 2.2rem;
            height: 2.2rem;
            flex: 0 0 auto;
            align-items: center;
            justify-content: center;
            border: 0;
            border-radius: 10px;
            background: #f1f5f9;
            color: #334155;
            cursor: pointer;
            font-size: 1.55rem;
            line-height: 1;
        }

        .customer-status-modal__close:hover {
            background: #e2e8f0;
        }

        .customer-status-modal__list {
            min-height: 110px;
            overflow-y: auto;
            padding: 0.75rem 1.25rem;
        }

        .customer-status-modal__customer {
            display: flex;
            align-items: flex-start;
            gap: 0.85rem;
            padding: 0.9rem 0;
            border-bottom: 1px solid #eef2f7;
        }

        .customer-status-modal__customer:last-child {
            border-bottom: 0;
        }

        .customer-status-modal__avatar {
            display: flex;
            width: 2.45rem;
            height: 2.45rem;
            flex: 0 0 auto;
            align-items: center;
            justify-content: center;
            border-radius: 999px;
            background: linear-gradient(135deg, #dcfce7, #bbf7d0);
            color: #15803d;
            font-size: 0.95rem;
            font-weight: 900;
        }

        .customer-status-modal__customer-info {
            min-width: 0;
        }

        .customer-status-modal__customer-info h3 {
            margin: 0;
            overflow: hidden;
            color: #191923;
            font-size: 0.91rem;
            font-weight: 850;
            line-height: 1.35;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .customer-status-modal__meta {
            display: flex;
            flex-wrap: wrap;
            gap: 0.28rem 0.55rem;
            margin-top: 0.33rem;
            color: #64748b;
            font-size: 0.72rem;
            font-weight: 650;
            line-height: 1.45;
        }

        .customer-status-modal__meta span:not(:last-child)::after {
            margin-left: 0.55rem;
            color: #cbd5e1;
            content: "•";
        }

        .customer-status-modal__empty {
            display: grid;
            min-height: 150px;
            place-items: center;
            padding: 1rem;
            color: #64748b;
            font-size: 0.84rem;
            font-weight: 700;
            text-align: center;
        }

        .customer-status-modal__footer {
            display: flex;
            justify-content: flex-end;
            padding: 0.9rem 1.25rem;
            border-top: 1px solid #e2e8f0;
            background: #f8fafc;
        }

        .customer-status-modal__button {
            border: 0;
            border-radius: 9px;
            padding: 0.6rem 1rem;
            background: #16a34a;
            color: #ffffff;
            cursor: pointer;
            font-size: 0.78rem;
            font-weight: 850;
        }

        .customer-status-modal__button:hover {
            background: #15803d;
        }

        .customer-status-modal-open {
            overflow: hidden;
        }

        .dark .customer-status-modal__panel {
            border-color: #334155;
            background: #1d1b2a;
        }

        .dark .customer-status-modal__header,
        .dark .customer-status-modal__footer,
        .dark .customer-status-modal__customer {
            border-color: #334155;
        }

        .dark .customer-status-modal__footer {
            background: #191923;
        }

        .dark .customer-status-modal__title,
        .dark .customer-status-modal__customer-info h3 {
            color: #f8fafc;
        }

        .dark .customer-status-modal__count,
        .dark .customer-status-modal__meta,
        .dark .customer-status-modal__empty {
            color: #94a3b8;
        }

        .dark .customer-status-modal__close {
            background: #302e42;
            color: #e2e8f0;
        }

        @media (max-width: 560px) {
            .customer-status-modal {
                align-items: flex-end;
                padding: 0;
            }

            .customer-status-modal__panel {
                width: 100%;
                max-height: 82vh;
                border-right: 0;
                border-bottom: 0;
                border-left: 0;
                border-radius: 18px 18px 0 0;
            }

            .customer-status-modal__header {
                padding: 1rem;
            }

            .customer-status-modal__list {
                padding: 0.55rem 1rem;
            }

            .customer-status-modal__footer {
                padding: 0.8rem 1rem;
            }

            .customer-status-modal__customer {
                gap: 0.72rem;
                padding: 0.82rem 0;
            }
        }

        @media (prefers-reduced-motion: reduce) {
            .customer-metric--clickable,
            .customer-status-modal,
            .customer-status-modal__panel {
                transition: none;
            }
        }



        /* customer-card-dark-green-border-fix:
           Dark mode tetap memakai dua layer background agar border hijau hidup terlihat */
        .dark .customer-summary-card {
            border: 2px solid transparent;
            background:
                linear-gradient(#1d1b2a, #1d1b2a) padding-box,
                linear-gradient(
                    90deg,
                    #16a34a 0%,
                    #86efac 20%,
                    #14532d 40%,
                    #22c55e 55%,
                    #15803d 75%,
                    #86efac 100%
                ) border-box;
            background-size: 100% 100%, 300% 100%;
            background-position: 0 0, 0 0;
            animation: customer-card-green-flow 5s linear infinite;
        }



        /* dashboard-greeting-cute-icon: ikon sapaan setelah nama login */
        #dashboard-greeting {
            display: inline-flex;
            align-items: center;
            gap: 0.42rem;
        }

        #dashboard-greeting::after {
            display: inline-flex;
            width: 1.55rem;
            height: 1.55rem;
            flex: 0 0 1.55rem;
            align-items: center;
            justify-content: center;
            border-radius: 999px;
            background: linear-gradient(135deg, #fef3c7, #fde68a);
            box-shadow: 0 4px 10px rgba(245, 158, 11, 0.18);
            content: "👋";
            font-size: 1rem;
            line-height: 1;
            animation: dashboard-greeting-wave 2.6s ease-in-out infinite;
            transform-origin: 72% 72%;
        }

        @keyframes dashboard-greeting-wave {
            0%, 58%, 100% {
                transform: rotate(0deg);
            }
            63% {
                transform: rotate(14deg);
            }
            68% {
                transform: rotate(-11deg);
            }
            73% {
                transform: rotate(12deg);
            }
            78% {
                transform: rotate(-7deg);
            }
            83% {
                transform: rotate(0deg);
            }
        }

        .dark #dashboard-greeting::after {
            background: linear-gradient(135deg, #78350f, #a16207);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.26);
        }

        @media (prefers-reduced-motion: reduce) {
            #dashboard-greeting::after {
                animation: none;
            }
        }



        /* Ringkasan Keuangan — Model A: Fokus Laba Bersih */
        .financial-summary-card {
            margin-top: 1.25rem;
            overflow: hidden;
            border: 1px solid #dbeafe;
            border-radius: 18px;
            background: #ffffff;
            box-shadow: 0 12px 30px rgba(15, 23, 42, 0.07);
        }

        .financial-summary-card__header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 1rem;
            padding: 1.15rem 1.25rem 0.95rem;
            border-bottom: 1px solid #eaf0f7;
            background: linear-gradient(115deg, rgba(239, 246, 255, 0.86), rgba(255, 255, 255, 0));
        }

        .financial-summary-card__eyebrow {
            margin: 0;
            color: #2563eb;
            font-size: 0.67rem;
            font-weight: 900;
            letter-spacing: 0.12em;
            line-height: 1.2;
        }

        .financial-summary-card__title {
            margin: 0.3rem 0 0;
            color: #191923;
            font-size: 1.15rem;
            font-weight: 900;
            line-height: 1.2;
        }

        .financial-summary-card__period {
            margin: 0.35rem 0 0;
            color: #64748b;
            font-size: 0.75rem;
            font-weight: 700;
        }

        .financial-summary-card__badge {
            display: inline-flex;
            flex: 0 0 auto;
            align-items: center;
            gap: 0.4rem;
            border: 1px solid #bbf7d0;
            border-radius: 999px;
            padding: 0.38rem 0.65rem;
            background: #f0fdf4;
            color: #15803d;
            font-size: 0.66rem;
            font-weight: 850;
            white-space: nowrap;
        }

        .financial-summary-card__badge-dot {
            display: block;
            width: 0.45rem;
            height: 0.45rem;
            border-radius: 999px;
            background: #22c55e;
            box-shadow: 0 0 0 4px rgba(34, 197, 94, 0.12);
        }

        .financial-summary-card__body {
            padding: 1.1rem 1.25rem 1.25rem;
        }

        /* Fokus utama: laba bersih */
        .finance-profit-hero {
            padding: 1.15rem 1.2rem;
            border: 1px solid #bbf7d0;
            border-radius: 15px;
            background:
                radial-gradient(circle at 100% 0%, rgba(134, 239, 172, 0.46), transparent 42%),
                linear-gradient(135deg, #f0fdf4, #ecfdf5);
        }

        .finance-profit-hero--negative {
            border-color: #fecaca;
            background:
                radial-gradient(circle at 100% 0%, rgba(252, 165, 165, 0.42), transparent 42%),
                linear-gradient(135deg, #fff1f2, #fef2f2);
        }

        .finance-profit-hero__top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
        }

        .finance-profit-hero__label {
            color: #15803d;
            font-size: 0.66rem;
            font-weight: 950;
            letter-spacing: 0.1em;
            line-height: 1.2;
        }

        .finance-profit-hero--negative .finance-profit-hero__label {
            color: #b91c1c;
        }

        .finance-profit-hero__icon {
            display: inline-flex;
            width: 2rem;
            height: 2rem;
            flex: 0 0 auto;
            align-items: center;
            justify-content: center;
            border-radius: 999px;
            background: #16a34a;
            color: #ffffff;
            font-size: 1.1rem;
            font-weight: 950;
        }

        .finance-profit-hero--negative .finance-profit-hero__icon {
            background: #dc2626;
        }

        .finance-profit-hero__value {
            display: block;
            margin-top: 0.45rem;
            color: #047857;
            font-size: clamp(1.55rem, 5.4vw, 2.1rem);
            font-weight: 950;
            letter-spacing: -0.06em;
            line-height: 1.1;
        }

        .finance-profit-hero--negative .finance-profit-hero__value {
            color: #b91c1c;
        }

        .finance-profit-hero__note {
            margin: 0.45rem 0 0;
            color: #15803d;
            font-size: 0.74rem;
            font-weight: 750;
            line-height: 1.35;
        }

        .finance-profit-hero--negative .finance-profit-hero__note {
            color: #b91c1c;
        }

        /* Perbandingan pemasukan dengan pengeluaran */
        .finance-flow-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 0.85rem;
            margin-top: 0.95rem;
        }

        .finance-flow {
            min-width: 0;
            padding: 0.95rem;
            border: 1px solid #e2e8f0;
            border-radius: 13px;
            background: #ffffff;
        }

        .finance-flow__heading {
            display: flex;
            align-items: center;
            gap: 0.42rem;
        }

        .finance-flow__icon {
            display: inline-flex;
            width: 1.55rem;
            height: 1.55rem;
            align-items: center;
            justify-content: center;
            border-radius: 7px;
            font-size: 0.9rem;
            font-weight: 950;
        }

        .finance-flow__label {
            color: #475569;
            font-size: 0.69rem;
            font-weight: 900;
            line-height: 1.2;
        }

        .finance-flow__value {
            display: block;
            margin-top: 0.68rem;
            overflow: hidden;
            font-size: clamp(0.98rem, 3.2vw, 1.22rem);
            font-weight: 950;
            letter-spacing: -0.045em;
            line-height: 1.15;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .finance-flow__detail {
            display: block;
            min-height: 1.9em;
            margin-top: 0.34rem;
            color: #94a3b8;
            font-size: 0.62rem;
            font-weight: 700;
            line-height: 1.35;
        }

        .finance-flow__bar {
            position: relative;
            height: 0.42rem;
            margin-top: 0.72rem;
            overflow: hidden;
            border-radius: 999px;
            background: #e2e8f0;
        }

        .finance-flow__bar::after {
            display: block;
            width: var(--finance-bar-width);
            height: 100%;
            border-radius: inherit;
            content: "";
            transition: width 420ms ease;
        }

        .finance-flow--income .finance-flow__icon {
            background: #dcfce7;
            color: #15803d;
        }

        .finance-flow--income .finance-flow__value {
            color: #15803d;
        }

        .finance-flow__bar--income::after {
            background: linear-gradient(90deg, #22c55e, #86efac);
        }

        .finance-flow--expense .finance-flow__icon {
            background: #fee2e2;
            color: #dc2626;
        }

        .finance-flow--expense .finance-flow__value {
            color: #b91c1c;
        }

        .finance-flow__bar--expense::after {
            background: linear-gradient(90deg, #ef4444, #fca5a5);
        }

        /* Informasi pendukung: estimasi dan tagihan tertunda */
        .finance-support-list {
            margin-top: 1rem;
            overflow: hidden;
            border: 1px solid #e2e8f0;
            border-radius: 13px;
            background: #ffffff;
        }

        .finance-support-row {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.82rem 0.9rem;
        }

        .finance-support-row + .finance-support-row {
            border-top: 1px solid #eef2f7;
        }

        .finance-support-row__icon {
            display: inline-flex;
            width: 1.85rem;
            height: 1.85rem;
            flex: 0 0 auto;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            font-size: 0.95rem;
            font-weight: 950;
        }

        .finance-support-row__content {
            min-width: 0;
            flex: 1 1 auto;
        }

        .finance-support-row__label,
        .finance-support-row__detail {
            display: block;
        }

        .finance-support-row__label {
            color: #334155;
            font-size: 0.72rem;
            font-weight: 900;
            line-height: 1.25;
        }

        .finance-support-row__detail {
            margin-top: 0.18rem;
            overflow: hidden;
            color: #94a3b8;
            font-size: 0.61rem;
            font-weight: 700;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .finance-support-row__value {
            flex: 0 0 auto;
            color: #191923;
            font-size: clamp(0.78rem, 2.7vw, 0.98rem);
            font-weight: 950;
            letter-spacing: -0.04em;
            text-align: right;
            white-space: nowrap;
        }

        .finance-support-row--estimate .finance-support-row__icon {
            background: #dbeafe;
            color: #2563eb;
        }

        .finance-support-row--estimate .finance-support-row__value {
            color: #4318e4;
        }

        .finance-support-row--pending .finance-support-row__icon {
            background: #fef3c7;
            color: #b45309;
        }

        .finance-support-row--pending .finance-support-row__value {
            color: #b45309;
        }

        /* Dark mode */
        .dark .financial-summary-card {
            border-color: #334155;
            background: #1d1b2a;
            box-shadow: 0 14px 34px rgba(0, 0, 0, 0.24);
        }

        .dark .financial-summary-card__header {
            border-color: #334155;
            background: linear-gradient(115deg, rgba(30, 64, 175, 0.16), rgba(17, 24, 39, 0));
        }

        .dark .financial-summary-card__title {
            color: #f8fafc;
        }

        .dark .financial-summary-card__period {
            color: #94a3b8;
        }

        .dark .finance-profit-hero {
            border-color: rgba(34, 197, 94, 0.36);
            background:
                radial-gradient(circle at 100% 0%, rgba(22, 163, 74, 0.22), transparent 44%),
                linear-gradient(135deg, rgba(20, 83, 45, 0.55), rgba(17, 24, 39, 0.92));
        }

        .dark .finance-profit-hero--negative {
            border-color: rgba(248, 113, 113, 0.35);
            background:
                radial-gradient(circle at 100% 0%, rgba(220, 38, 38, 0.22), transparent 44%),
                linear-gradient(135deg, rgba(127, 29, 29, 0.52), rgba(17, 24, 39, 0.92));
        }

        .dark .finance-profit-hero__label,
        .dark .finance-profit-hero__note {
            color: #bbf7d0;
        }

        .dark .finance-profit-hero__value {
            color: #86efac;
        }

        .dark .finance-profit-hero--negative .finance-profit-hero__label,
        .dark .finance-profit-hero--negative .finance-profit-hero__note {
            color: #fecaca;
        }

        .dark .finance-profit-hero--negative .finance-profit-hero__value {
            color: #fca5a5;
        }

        .dark .finance-flow,
        .dark .finance-support-list {
            border-color: #334155;
            background: #191923;
        }

        .dark .finance-flow__label,
        .dark .finance-support-row__label {
            color: #e2e8f0;
        }

        .dark .finance-flow__detail,
        .dark .finance-support-row__detail {
            color: #94a3b8;
        }

        .dark .finance-flow--income .finance-flow__icon {
            background: rgba(20, 83, 45, 0.48);
            color: #86efac;
        }

        .dark .finance-flow--income .finance-flow__value {
            color: #86efac;
        }

        .dark .finance-flow--expense .finance-flow__icon {
            background: rgba(127, 29, 29, 0.44);
            color: #fca5a5;
        }

        .dark .finance-flow--expense .finance-flow__value {
            color: #fca5a5;
        }

        .dark .finance-flow__bar {
            background: #334155;
        }

        .dark .finance-support-row + .finance-support-row {
            border-color: #334155;
        }

        .dark .finance-support-row--estimate .finance-support-row__icon {
            background: rgba(30, 64, 175, 0.34);
            color: #93c5fd;
        }

        .dark .finance-support-row--estimate .finance-support-row__value {
            color: #93c5fd;
        }

        .dark .finance-support-row--pending .finance-support-row__icon {
            background: rgba(120, 53, 15, 0.45);
            color: #fcd34d;
        }

        .dark .finance-support-row--pending .finance-support-row__value {
            color: #fcd34d;
        }

        @media (max-width: 560px) {
            .financial-summary-card {
                margin-top: 1rem;
                border-radius: 15px;
            }

            .financial-summary-card__header {
                padding: 1.05rem 1rem 0.88rem;
            }

            .financial-summary-card__title {
                font-size: 1.03rem;
            }

            .financial-summary-card__badge {
                padding: 0.34rem 0.55rem;
                font-size: 0.6rem;
            }

            .financial-summary-card__body {
                padding: 0.9rem 1rem 1rem;
            }

            .finance-profit-hero {
                padding: 1rem;
                border-radius: 13px;
            }

            .finance-flow-grid {
                gap: 0.65rem;
                margin-top: 0.75rem;
            }

            .finance-flow {
                padding: 0.8rem;
                border-radius: 11px;
            }

            .finance-flow__value {
                font-size: 0.92rem;
            }

            .finance-support-list {
                margin-top: 0.8rem;
                border-radius: 11px;
            }

            .finance-support-row {
                gap: 0.6rem;
                padding: 0.75rem;
            }

            .finance-support-row__value {
                font-size: 0.76rem;
            }
        }

        @media (max-width: 380px) {
            .financial-summary-card__badge {
                display: none;
            }

            .finance-flow-grid {
                grid-template-columns: 1fr;
            }

            .finance-flow__detail {
                min-height: auto;
            }
        }

        @media (prefers-reduced-motion: reduce) {
            .finance-flow__bar::after {
                transition: none;
            }
        }



        /* finance-color-distinction-palette:
           Keuangan memakai identitas indigo; warna status tetap semantik. */

        /* Header finansial: indigo agar berbeda dari monitoring jaringan */
        .financial-summary-card {
            border-color: #ddd6fe;
            box-shadow: 0 14px 32px rgba(79, 70, 229, 0.09);
        }

        .financial-summary-card__header {
            border-bottom-color: #e9e7ff;
            background:
                radial-gradient(circle at 100% 0%, rgba(196, 181, 253, 0.38), transparent 40%),
                linear-gradient(120deg, #f5f3ff, #ffffff 72%);
        }

        .financial-summary-card__eyebrow {
            color: #6d28d9;
        }

        .financial-summary-card__badge {
            border-color: #c4b5fd;
            background: #f5f3ff;
            color: #6d28d9;
        }

        .financial-summary-card__badge-dot {
            background: #8b5cf6;
            box-shadow: 0 0 0 4px rgba(139, 92, 246, 0.14);
        }

        /* Laba bersih: indigo sebagai wadah, hijau hanya untuk hasil positif */
        .finance-profit-hero {
            border-color: #c7d2fe;
            background:
                radial-gradient(circle at 100% 0%, rgba(167, 139, 250, 0.48), transparent 43%),
                linear-gradient(135deg, #eef2ff, #f5f3ff);
        }

        .finance-profit-hero__label {
            color: #4338ca;
        }

        .finance-profit-hero__icon {
            background: #4f46e5;
            box-shadow: 0 7px 16px rgba(79, 70, 229, 0.26);
        }

        .finance-profit-hero__value {
            color: #047857;
        }

        .finance-profit-hero__note {
            color: #475569;
        }

        /* Kondisi laba negatif tetap jelas memakai merah */
        .finance-profit-hero--negative {
            border-color: #fecaca;
            background:
                radial-gradient(circle at 100% 0%, rgba(252, 165, 165, 0.42), transparent 43%),
                linear-gradient(135deg, #fff1f2, #fef2f2);
        }

        .finance-profit-hero--negative .finance-profit-hero__label,
        .finance-profit-hero--negative .finance-profit-hero__note,
        .finance-profit-hero--negative .finance-profit-hero__value {
            color: #b91c1c;
        }

        .finance-profit-hero--negative .finance-profit-hero__icon {
            background: #dc2626;
            box-shadow: 0 7px 16px rgba(220, 38, 38, 0.22);
        }

        /* Pendapatan: teal, supaya tidak menyatu dengan hijau monitoring */
        .finance-flow--income .finance-flow__icon {
            background: #ccfbf1;
            color: #0f766e;
        }

        .finance-flow--income .finance-flow__value {
            color: #0f766e;
        }

        .finance-flow__bar--income::after {
            background: linear-gradient(90deg, #0d9488, #5eead4);
        }

        /* Pengeluaran: coral/merah lembut */
        .finance-flow--expense .finance-flow__icon {
            background: #ffe4e6;
            color: #e11d48;
        }

        .finance-flow--expense .finance-flow__value {
            color: #be123c;
        }

        .finance-flow__bar--expense::after {
            background: linear-gradient(90deg, #e11d48, #fda4af);
        }

        /* Estimasi: biru informasi */
        .finance-support-row--estimate .finance-support-row__icon {
            background: #dbeafe;
            color: #2563eb;
        }

        .finance-support-row--estimate .finance-support-row__value {
            color: #4318e4;
        }

        /* Tertunda: amber sebagai tindakan yang perlu diperhatikan */
        .finance-support-row--pending .finance-support-row__icon {
            background: #fef3c7;
            color: #b45309;
        }

        .finance-support-row--pending .finance-support-row__value {
            color: #b45309;
        }

        /* Dark mode: indigo slate tetap menjadi identitas keuangan */
        .dark .financial-summary-card {
            border-color: #4338ca;
            background: #1d1b2a;
            box-shadow: 0 16px 38px rgba(49, 46, 129, 0.28);
        }

        .dark .financial-summary-card__header {
            border-color: #3730a3;
            background:
                radial-gradient(circle at 100% 0%, rgba(109, 40, 217, 0.28), transparent 42%),
                linear-gradient(120deg, rgba(49, 46, 129, 0.34), #1d1b2a 72%);
        }

        .dark .financial-summary-card__eyebrow {
            color: #c4b5fd;
        }

        .dark .financial-summary-card__badge {
            border-color: rgba(167, 139, 250, 0.45);
            background: rgba(76, 29, 149, 0.28);
            color: #ddd6fe;
        }

        .dark .financial-summary-card__badge-dot {
            background: #a78bfa;
            box-shadow: 0 0 0 4px rgba(167, 139, 250, 0.16);
        }

        .dark .finance-profit-hero {
            border-color: rgba(129, 140, 248, 0.44);
            background:
                radial-gradient(circle at 100% 0%, rgba(109, 40, 217, 0.32), transparent 44%),
                linear-gradient(135deg, rgba(49, 46, 129, 0.58), rgba(30, 27, 75, 0.54));
        }

        .dark .finance-profit-hero__label {
            color: #c7d2fe;
        }

        .dark .finance-profit-hero__icon {
            background: #6366f1;
            box-shadow: 0 7px 17px rgba(99, 102, 241, 0.3);
        }

        .dark .finance-profit-hero__value {
            color: #6ee7b7;
        }

        .dark .finance-profit-hero__note {
            color: #cbd5e1;
        }

        .dark .finance-profit-hero--negative {
            border-color: rgba(248, 113, 113, 0.38);
            background:
                radial-gradient(circle at 100% 0%, rgba(220, 38, 38, 0.24), transparent 44%),
                linear-gradient(135deg, rgba(127, 29, 29, 0.5), rgba(69, 10, 10, 0.38));
        }

        .dark .finance-profit-hero--negative .finance-profit-hero__label,
        .dark .finance-profit-hero--negative .finance-profit-hero__note,
        .dark .finance-profit-hero--negative .finance-profit-hero__value {
            color: #fecaca;
        }

        .dark .finance-flow--income .finance-flow__icon {
            background: rgba(15, 118, 110, 0.36);
            color: #5eead4;
        }

        .dark .finance-flow--income .finance-flow__value {
            color: #5eead4;
        }

        .dark .finance-flow--expense .finance-flow__icon {
            background: rgba(159, 18, 57, 0.38);
            color: #fda4af;
        }

        .dark .finance-flow--expense .finance-flow__value {
            color: #fda4af;
        }

        .dark .finance-support-row--estimate .finance-support-row__icon {
            background: rgba(30, 64, 175, 0.36);
            color: #93c5fd;
        }

        .dark .finance-support-row--estimate .finance-support-row__value {
            color: #93c5fd;
        }

        .dark .finance-support-row--pending .finance-support-row__icon {
            background: rgba(120, 53, 15, 0.48);
            color: #fcd34d;
        }

        .dark .finance-support-row--pending .finance-support-row__value {
            color: #fcd34d;
        }

        /* Area financial dashboard table */
        .financial-summary-card--area {
            margin-top: 1.5rem;
        }

        .financial-summary-card--area .financial-summary-card__body {
            padding: 0;
        }

        .area-financial-table-wrapper {
            width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            scrollbar-color: #cbd5e1 transparent;
            scrollbar-width: thin;
        }

        .area-financial-table {
            width: 100%;
            min-width: 940px;
            border-collapse: separate;
            border-spacing: 0;
            color: #334155;
            font-size: 0.875rem;
        }

        .area-financial-table th,
        .area-financial-table td {
            border-bottom: 1px solid #e2e8f0;
            padding: 0.95rem 1rem;
            vertical-align: middle;
        }

        .area-financial-table th {
            position: sticky;
            top: 0;
            z-index: 1;
            background: #f8fafc;
            color: #64748b;
            font-size: 0.7rem;
            font-weight: 800;
            letter-spacing: 0.06em;
            line-height: 1.2;
            text-transform: uppercase;
            white-space: nowrap;
        }

        .area-financial-table td:first-child {
            min-width: 220px;
        }

        .area-financial-table tbody tr {
            transition: background-color 160ms ease;
        }

        .area-financial-table tbody tr:hover {
            background: #f8fafc;
        }

        .area-financial-table tbody tr:last-child td {
            border-bottom: 0;
        }

        .area-financial-table td strong {
            display: block;
            color: #302e42;
            font-size: 0.875rem;
            font-weight: 750;
            line-height: 1.35;
        }

        .area-financial-table__number {
            font-variant-numeric: tabular-nums;
            text-align: right;
            white-space: nowrap;
        }

        .area-financial-table__code,
        .area-financial-table__invoice-count {
            display: block;
            margin-top: 0.22rem;
            color: #94a3b8;
            font-size: 0.72rem;
            font-weight: 600;
            line-height: 1.25;
        }

        .area-financial-table__income,
        .area-financial-table__profit {
            color: #15803d;
            font-weight: 750;
        }

        .area-financial-table__expense {
            color: #be123c;
            font-weight: 750;
        }

        .area-financial-table__pending {
            color: #b45309;
            font-weight: 750;
        }

        .area-financial-table__negative {
            color: #dc2626;
            font-weight: 800;
        }

        .dark .financial-summary-card--area .area-financial-table {
            color: #cbd5e1;
        }

        .dark .financial-summary-card--area .area-financial-table th {
            border-color: #312e81;
            background: #232235;
            color: #a5b4fc;
        }

        .dark .financial-summary-card--area .area-financial-table td {
            border-color: rgba(99, 102, 241, 0.22);
        }

        .dark .financial-summary-card--area .area-financial-table tbody tr:hover {
            background: rgba(49, 46, 129, 0.24);
        }

        .dark .financial-summary-card--area .area-financial-table td strong {
            color: #f1f5f9;
        }

        .dark .financial-summary-card--area .area-financial-table__code,
        .dark .financial-summary-card--area .area-financial-table__invoice-count {
            color: #94a3b8;
        }

        .dark .financial-summary-card--area .area-financial-table__income,
        .dark .financial-summary-card--area .area-financial-table__profit {
            color: #6ee7b7;
        }

        .dark .financial-summary-card--area .area-financial-table__expense {
            color: #fda4af;
        }

        .dark .financial-summary-card--area .area-financial-table__pending {
            color: #fcd34d;
        }

        .dark .financial-summary-card--area .area-financial-table__negative {
            color: #fca5a5;
        }

        .financial-summary-card--area .area-financial-table__scroll-hint {
            display: none;
        }
        @media (max-width: 767px) {
                        .financial-summary-card--area .financial-summary-card__body {
                padding: 0;
            }

            .financial-summary-card--area .area-financial-table-wrapper {
                padding: 0.55rem;
            }

            .financial-summary-card--area .area-financial-table {
                min-width: 850px;
                border-radius: 12px;
            }

            .financial-summary-card--area .area-financial-table th,
            .financial-summary-card--area .area-financial-table td {
                padding: 0.82rem 0.7rem;
            }

            .financial-summary-card--area .area-financial-table th:first-child,
            .financial-summary-card--area .area-financial-table td:first-child {
                padding-left: 0.9rem;
            }
        }


        /* Customer page dark-mode consistency */
        .dark #customer-livefind {
            background-color: #302e42;
            border-color: #334155;
            color: #f1f5f9;
        }

        .dark #customer-livefind:focus {
            background-color: #191923;
            border-color: #7e57ff;
        }

        .dark .space-y-3.bg-slate-50 {
            background-color: rgba(2, 6, 23, 0.72);
        }

        .dark article.border-slate-200.bg-white {
            background-color: #191923;
            border-color: #302e42;
        }

        .dark article.border-slate-200.bg-slate-100\/70 {
            background-color: rgba(30, 41, 59, 0.72);
            border-color: #334155;
        }

        .dark article.border-rose-200.bg-rose-50\/30 {
            background-color: rgba(136, 19, 55, 0.16);
            border-color: rgba(251, 113, 133, 0.28);
        }

        .dark article.border-slate-200.bg-white:hover {
            background-color: #232235;
            border-color: rgba(34, 211, 238, 0.55);
        }

        .dark article.border-slate-200.bg-slate-100\/70:hover {
            background-color: rgba(51, 65, 85, 0.78);
            border-color: #475569;
        }

        .dark article.border-rose-200.bg-rose-50\/30:hover {
            background-color: rgba(136, 19, 55, 0.24);
            border-color: rgba(251, 113, 133, 0.44);
        }

        .dark .space-y-3.bg-slate-50 > article {
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.16);
        }


        /* Customer page outer card dark-mode repair */
        .dark .space-y-5 > section.relative.overflow-visible.rounded-2xl {
            background-color: #191923 !important;
            border-color: #302e42 !important;
            box-shadow: 0 14px 30px rgba(0, 0, 0, 0.20);
        }

        .dark .space-y-5 > section.relative.overflow-visible.rounded-2xl > div.border-b {
            background-color: #191923 !important;
            border-color: #302e42 !important;
        }

        .dark .space-y-5 > section.relative.overflow-visible.rounded-2xl > div.space-y-3 {
            background-color: #12111a !important;
        }

        .dark .space-y-5 > section.relative.overflow-visible.rounded-2xl > div.space-y-3 > article {
            background-color: #242238;
        }

        .dark .space-y-5 > section.relative.overflow-visible.rounded-2xl > div.space-y-3 > article:hover {
            background-color: #2b2940;
        }


        /* Customers page: explicit dark theme layers */
        .dark .customers-page {
            color: #e2e8f0;
        }

        .dark .customers-page > section {
            background: #191923 !important;
            border-color: #334155 !important;
            box-shadow: 0 16px 32px rgba(0, 0, 0, 0.24) !important;
        }

        .dark .customers-page > section > div:first-child {
            background: #191923 !important;
            border-color: #334155 !important;
        }

        .dark .customers-page > section > div.space-y-3 {
            background: #12111a !important;
        }

        .dark .customers-page > section > div.space-y-3 > article {
            background: #242238 !important;
            border-color: #334155 !important;
        }

        .dark .customers-page > section > div.space-y-3 > article:hover {
            background: #29273d !important;
            border-color: rgba(34, 211, 238, 0.55) !important;
        }

        .dark .customers-page > section > div.space-y-3 > article[class*="rose"] {
            background: rgba(136, 19, 55, 0.24) !important;
            border-color: rgba(251, 113, 133, 0.32) !important;
        }

        .dark .customers-page > section > div.space-y-3 > article[class*="slate-100"] {
            background: #302e42 !important;
            border-color: #475569 !important;
        }

        .dark .customers-page details > div {
            background: #191923 !important;
            border-color: #334155 !important;
        }

@media (max-width: 767px){.financial-summary-card--area .area-financial-table__scroll-hint{display:block;margin:0 .55rem .45rem;color:#64748b;font-size:.72rem;font-weight:700;letter-spacing:.01em}.dark .financial-summary-card--area .area-financial-table__scroll-hint{color:#a5b4fc}}
        /* MACBILL PREMIUM PURPLE — CUSTOMER CARD POLISH */
        .customer-summary-card {
            overflow: hidden;
            border: 1px solid #e5e2f7;
            border-radius: 24px;
            background:
                radial-gradient(circle at 100% 0%, rgba(126, 87, 255, .12), transparent 31%),
                linear-gradient(145deg, #fff 0%, #faf9ff 100%);
            box-shadow: 0 18px 42px rgba(55, 35, 133, .08);
        }

        .customer-summary-card__accent {
            grid-row: auto;
            width: 100%;
            height: 5px;
            background: linear-gradient(90deg, #4318e4 0%, #7e57ff 54%, #b39cff 100%);
        }

        .customer-summary-card__heading {
            align-items: flex-start;
            padding: 1.45rem 1.55rem 1.1rem;
        }

        .customer-summary-card__eyebrow {
            color: #7c6adf;
            font-size: .66rem;
            font-weight: 700;
            letter-spacing: .14em;
            line-height: 1.2;
        }

        .customer-summary-card__title {
            color: #191923;
            font-size: clamp(1.2rem, 2vw, 1.45rem);
            font-weight: 700;
            letter-spacing: -.035em;
            line-height: 1.12;
        }

        .customer-summary-card__live {
            flex: 0 0 auto;
            padding: .46rem .68rem;
            border-color: #bbf7d0;
            background: #f0fdf4;
            color: #15803d;
            box-shadow: 0 4px 12px rgba(21, 128, 61, .08);
        }

        .customer-summary-card__metrics {
            border-top: 1px solid #ebeef9;
        }

        .customer-metric {
            min-height: 152px;
            padding: 1.25rem 1.55rem 1.35rem;
            transition: background-color 180ms ease;
        }

        .customer-metric + .customer-metric {
            border-left-color: #ebeef9;
        }

        .customer-metric--clickable {
            cursor: pointer;
        }

        .customer-metric--clickable:hover {
            background: rgba(126, 87, 255, .055);
        }

        .customer-metric--clickable:focus-visible {
            z-index: 1;
            outline: 3px solid rgba(67, 24, 228, .28);
            outline-offset: -3px;
        }

        .customer-metric__label {
            gap: .48rem;
            color: #64748b;
            font-size: .7rem;
            font-weight: 700;
            letter-spacing: .015em;
        }

        .customer-metric__dot {
            width: 9px;
            height: 9px;
            box-shadow: 0 0 0 4px currentColor;
            opacity: .82;
        }

        .customer-metric__value {
            margin-top: .72rem;
            color: #191923;
            font-size: clamp(2rem, 3.25vw, 2.75rem);
            font-weight: 700;
            letter-spacing: -.055em;
            line-height: .94;
            font-variant-numeric: tabular-nums;
        }

        .customer-metric__detail {
            margin-top: .55rem;
            color: #64748b;
            font-size: .69rem;
            font-weight: 600;
            line-height: 1.35;
        }

        .customer-metric__action {
            display: inline-flex;
            margin-top: .5rem;
            color: #6d5add;
            font-size: .67rem;
            font-weight: 750;
        }

        .customer-metric--total {
            background: linear-gradient(145deg, rgba(240, 237, 255, .9), rgba(255, 255, 255, 0));
        }

        .customer-metric--total .customer-metric__label,
        .customer-metric--total .customer-metric__value {
            color: #4318e4;
        }

        .customer-metric--online .customer-metric__label,
        .customer-metric--online .customer-metric__value,
        .customer-metric--online .customer-metric__action {
            color: #15803d;
        }

        .customer-metric--offline .customer-metric__label,
        .customer-metric--offline .customer-metric__value,
        .customer-metric--offline .customer-metric__action {
            color: #be123c;
        }

        .customer-metric--isolated .customer-metric__label,
        .customer-metric--isolated .customer-metric__value,
        .customer-metric--isolated .customer-metric__action {
            color: #b45309;
        }

        /* MACBILL PREMIUM PURPLE — LIVE NETWORK SHIMMER */
        .customer-summary-card {
            position: relative;
            isolation: isolate;
            overflow: hidden;
            border: 1px solid #e5e2f7;
            box-shadow: 0 18px 42px rgba(55, 35, 133, .08);
        }

        .customer-summary-card::before {
            display: none;
        }

        .customer-summary-card > * {
            position: relative;
            z-index: 1;
        }

        .customer-summary-card__live {
            position: relative;
            overflow: hidden;
            border-color: #bbf7d0;
            background: #f0fdf4;
            color: #15803d;
        }

        .customer-summary-card__live::after {
            content: "";
            position: absolute;
            inset: -35% auto -35% -45%;
            width: 34%;
            background: linear-gradient(105deg, transparent 0%, rgba(255,255,255,0) 25%, rgba(255,255,255,.9) 50%, rgba(255,255,255,0) 75%, transparent 100%);
            transform: skewX(-18deg);
            animation: macbill-live-badge-shimmer 3.8s ease-in-out infinite;
            pointer-events: none;
        }

        .customer-summary-card__live i {
            position: relative;
            z-index: 1;
            animation: macbill-live-beacon 1.8s ease-in-out infinite;
        }

        .customer-summary-card__progress {
            position: relative;
            overflow: hidden;
            height: 13px;
            border-color: rgba(126, 87, 255, .16);
            background: #e9e7f2;
            box-shadow: inset 0 1px 2px rgba(25, 25, 35, .08);
        }

        .customer-summary-card__progress-online {
            position: relative;
            overflow: hidden;
            background: linear-gradient(90deg, #16a34a 0%, #4ade80 100%);
        }

        .customer-summary-card__progress-online::after {
            content: "";
            position: absolute;
            inset: 0 auto 0 -45%;
            width: 30%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,.20), rgba(255,255,255,.82), rgba(255,255,255,.20), transparent);
            transform: skewX(-18deg);
            animation: macbill-network-shimmer 2.9s ease-in-out infinite;
            pointer-events: none;
        }

        .customer-metric--clickable {
            transition: transform 180ms ease, box-shadow 180ms ease, background-color 180ms ease;
        }

        .customer-metric--clickable:hover,
        .customer-metric--clickable:active {
            transform: translateY(-2px);
            background: rgba(126, 87, 255, .06);
            box-shadow: inset 0 0 0 1px rgba(126, 87, 255, .12);
        }

        .customer-metric--online:hover,
        .customer-metric--online:active {
            box-shadow: inset 0 0 0 1px rgba(34, 197, 94, .26);
        }

        .customer-metric--offline:hover,
        .customer-metric--offline:active {
            box-shadow: inset 0 0 0 1px rgba(244, 63, 94, .24);
        }

        .customer-metric--isolated:hover,
        .customer-metric--isolated:active {
            box-shadow: inset 0 0 0 1px rgba(245, 158, 11, .28);
        }

        @keyframes macbill-live-beacon {
            0%, 100% { box-shadow: 0 0 0 4px rgba(34, 197, 94, .15); transform: scale(1); }
            50% { box-shadow: 0 0 0 8px rgba(34, 197, 94, 0); transform: scale(1.08); }
        }

        @keyframes macbill-live-badge-shimmer {
            0%, 55% { left: -45%; }
            82%, 100% { left: 125%; }
        }

        @keyframes macbill-network-shimmer {
            0%, 35% { left: -45%; }
            75%, 100% { left: 125%; }
        }

        .dark .customer-summary-card {
            border-color: rgba(167, 139, 250, .20);
            box-shadow: 0 18px 42px rgba(0, 0, 0, .28);
        }

        .dark .customer-summary-card__live {
            border-color: rgba(74, 222, 128, .28);
            background: rgba(20, 83, 45, .32);
            color: #bbf7d0;
        }

        .dark .customer-summary-card__progress {
            border-color: rgba(167, 139, 250, .22);
            background: #302e42;
        }

        .dark .customer-summary-card__progress-online::after {
            background: linear-gradient(90deg, transparent, rgba(255,255,255,.12), rgba(187,247,208,.82), rgba(255,255,255,.12), transparent);
        }

        @media (prefers-reduced-motion: reduce) {
            .customer-summary-card__live::after,
            .customer-summary-card__live i,
            .customer-summary-card__progress-online::after {
                animation: none;
            }

            .customer-metric--clickable {
                transition: none;
            }
        }
        /* MACBILL — NETWORK CARD LIGHT MODE FIX */
        .customer-summary-card {
            background: #f7f6fc;
            border-color: #cfc6eb;
        }

        /* Aktifkan kembali garis hijau pada tepi card Monitoring Jaringan */
        .customer-summary-card::before {
            content: "";
            display: block;
            position: absolute;
            inset: 0;
            z-index: 0;
            pointer-events: none;
            border: 2px solid #22c55e;
            border-radius: inherit;
            opacity: 1;
        }

        /* Background section metric sedikit lebih gelap agar terpisah */
        .customer-summary-card__metrics {
            background: #f1f0f8;
            border-top: 1px solid #d8d1ee;
        }

        /* Progress area juga dibuat lebih tegas */
        .customer-summary-card__progress {
            background: #d7d3e3;
            border-color: #bbb1d7;
        }

        /* Dark mode tetap menggunakan treatment dark, bukan outline hijau terang */
        .dark .customer-summary-card {
            background: #211f30;
            border-color: rgba(167, 139, 250, .28);
        }

        .dark .customer-summary-card::before {
            border-color: rgba(74, 222, 128, .48);
        }

        .dark .customer-summary-card__metrics {
            background: #29263a;
            border-top-color: rgba(167, 139, 250, .20);
        }

        .dark .customer-summary-card__progress {
            background: #353248;
            border-color: rgba(167, 139, 250, .26);
        }


/* NETWORK MONITORING FINAL START */
/* NETWORK_FINAL */
.network-monitoring-card--reference{position:relative;overflow:hidden;background:rgba(255,255,255,.90);border:1px solid #cbd5e1;border-radius:20px;box-shadow:0 18px 50px rgba(20,35,60,.08);color:#172033}
.network-monitoring-card--reference:before{content:"";position:absolute;inset:0;pointer-events:none;background:radial-gradient(circle at 18% 55%,rgba(32,116,255,.055),transparent 34%)}
.network-monitoring-card--reference .network-monitoring-card__header,.network-monitoring-card--reference .network-monitoring-card__body,.network-monitoring-card--reference .network-monitoring-card__footer{position:relative;z-index:1}
.network-monitoring-card--reference .network-monitoring-card__header{display:flex;align-items:center;justify-content:space-between;gap:16px;min-height:72px;padding:0 24px;border-bottom:1px solid #dbe3ed}
.network-monitoring-card--reference .network-monitoring-card__header-left{display:flex;align-items:center;gap:12px;min-width:0}
.network-monitoring-card--reference .network-monitoring-card__icon{display:inline-flex;align-items:center;justify-content:center;flex:0 0 40px;width:40px;height:40px;border-radius:11px;background:rgba(38,120,255,.09);color:#287cff;font-size:20px}
.network-monitoring-card--reference .network-monitoring-card__title{margin:0 0 3px;color:#172033;font-size:16px;font-weight:800;line-height:1.2}
.network-monitoring-card--reference .network-monitoring-card__subtitle{margin:0;color:#8a95a7;font-size:11px;line-height:1.35}
.network-monitoring-card--reference .network-monitoring-card__live,.network-monitoring-card--reference .network-monitoring-card__footer-live{display:inline-flex;align-items:center;gap:7px;color:#16ad6e;font-size:10px;font-weight:800;letter-spacing:1px;text-transform:uppercase;white-space:nowrap}
.network-monitoring-card--reference .network-monitoring-card__live i,.network-monitoring-card--reference .network-monitoring-card__footer-live i{display:inline-block;width:7px;height:7px;border-radius:50%;background:#19d17e;box-shadow:0 0 5px #19d17e,0 0 13px rgba(25,209,126,.75);animation:netlive 1.5s ease-in-out infinite}
.network-monitoring-card--reference .network-monitoring-card__body{display:grid;grid-template-columns:330px minmax(300px,1fr);align-items:center;gap:38px;min-height:305px;padding:25px}
.network-monitoring-card--reference .network-monitoring-card__donut-area{position:relative;display:flex;align-items:center;justify-content:center;justify-self:center;width:275px;height:275px}
.network-monitoring-card--reference .network-monitoring-card__donut-area:before{content:"";position:absolute;width:205px;height:205px;border-radius:50%;background:radial-gradient(circle,rgba(26,210,126,.20),rgba(39,124,255,.15) 32%,transparent 72%);filter:blur(4px);animation:netglow 2.4s ease-in-out infinite}
.network-monitoring-card--reference .network-monitoring-card__signal-ring{position:absolute;width:254px;height:254px;border:1px solid rgba(42,132,255,.38);border-radius:50%;box-shadow:0 0 12px rgba(42,132,255,.13);animation:netspin 9s linear infinite}
.network-monitoring-card--reference .network-monitoring-card__signal-ring:before{content:"";position:absolute;inset:-6px;border:2px dashed rgba(42,132,255,.38);border-radius:inherit;filter:drop-shadow(0 0 4px rgba(42,132,255,.5));animation:netspinback 13s linear infinite}
.network-monitoring-card--reference .network-monitoring-card__pulse-ring{position:absolute;width:160px;height:160px;border:1px solid rgba(28,216,128,.45);border-radius:50%;box-shadow:0 0 8px rgba(28,216,128,.18);opacity:0;animation:netpulse 2.2s ease-out infinite}
.network-monitoring-card--reference .network-monitoring-card__pulse-ring--two{animation-delay:.73s}
.network-monitoring-card--reference .network-monitoring-card__pulse-ring--three{animation-delay:1.46s}
.network-monitoring-card--reference .network-monitoring-card__donut:before,.network-monitoring-card--reference .network-monitoring-card__donut:after{content:none!important;display:none!important;animation:none!important}
.network-monitoring-card--reference .network-monitoring-card__donut{position:relative;z-index:2;display:flex;align-items:center;justify-content:center;width:232px;height:232px;border-radius:50%;box-shadow:0 0 0 5px rgba(255,255,255,.58),0 0 18px rgba(35,126,255,.13);animation:netenter .85s cubic-bezier(.16,1,.3,1) both}
.network-monitoring-card--reference .network-monitoring-card__donut-center{position:relative;z-index:2;display:flex;flex-direction:column;align-items:center;justify-content:center;width:162px;height:162px;padding:10px;border-radius:50%;background:rgba(255,255,255,.97);box-shadow:inset 0 1px 4px rgba(15,23,42,.09);animation:netcenter .9s cubic-bezier(.16,1,.3,1) .12s both}
.network-monitoring-card--reference .network-monitoring-card__wifi{display:inline-flex;align-items:center;justify-content:center;width:33px;height:33px;margin-bottom:7px;border-radius:50%;background:rgba(42,130,255,.10);box-shadow:0 0 15px rgba(42,130,255,.12);color:#3c91ff;font-size:17px;animation:netwifi 1.5s ease-in-out infinite}
.network-monitoring-card--reference .network-monitoring-card__donut-center>strong{color:#172033;font-size:38px;font-weight:800;letter-spacing:-.05em;line-height:1}
.network-monitoring-card--reference .network-monitoring-card__donut-label{margin-top:5px;color:#8a95a7;font-size:11px;line-height:1.2}
.network-monitoring-card--reference .network-monitoring-card__health{margin-top:8px;padding:4px 7px;border-radius:999px;font-size:8px;font-weight:850;letter-spacing:.08em;line-height:1}
.network-monitoring-card--reference .network-monitoring-card__health--healthy{background:#d1fae5;color:#047857}
.network-monitoring-card--reference .network-monitoring-card__health--warning{background:#fef3c7;color:#b45309}
.network-monitoring-card--reference .network-monitoring-card__health--critical{background:#ffe4e6;color:#be123c}
.network-monitoring-card--reference .network-monitoring-card__status-area{display:flex;flex-direction:column;align-self:center;min-width:0;gap:11px}
.network-monitoring-card--reference .network-monitoring-card__status-heading{margin:0 0 3px;color:#8a95a7;font-size:11px}
.network-monitoring-card--reference .network-monitoring-card__status{display:flex;align-items:center;justify-content:space-between;width:100%;min-height:68px;padding:11px 14px;border:1px solid #cdd6e2;border-radius:13px;background:rgba(248,250,252,.70);color:#172033;cursor:pointer;font-family:inherit;text-align:left;transition:background .22s,box-shadow .22s,transform .22s}
.network-monitoring-card--reference .network-monitoring-card__status:hover{background:#fff;box-shadow:0 7px 20px rgba(30,50,80,.07);transform:translateX(5px)}
.network-monitoring-card--reference .network-monitoring-card__status-left{display:inline-flex;align-items:center;gap:11px;min-width:0}
.network-monitoring-card--reference .network-monitoring-card__status-right{display:grid;align-items:center;flex:0 0 auto;gap:4px;margin-left:8px;text-align:right}
.network-monitoring-card--reference .network-monitoring-card__status-icon{display:inline-flex;align-items:center;justify-content:center;flex:0 0 34px;width:34px;height:34px;border-radius:9px;font-size:14px}
.network-monitoring-card--reference .network-monitoring-card__status-text{display:grid;min-width:0;gap:2px}
.network-monitoring-card--reference .network-monitoring-card__status-text strong{color:#253047;font-size:13px;font-weight:800;line-height:1.15}
.network-monitoring-card--reference .network-monitoring-card__status-text small{color:#8994a7;font-size:10px;line-height:1.2}
.network-monitoring-card--reference .network-monitoring-card__status-right strong{font-size:19px;font-weight:800;letter-spacing:-.03em;line-height:1}
.network-monitoring-card--reference .network-monitoring-card__view-text{display:inline-block;color:#287cff;font-size:11px;font-weight:700;line-height:1.2;white-space:nowrap}
.network-monitoring-card--reference .network-monitoring-card__status--online .network-monitoring-card__status-icon{background:rgba(24,207,126,.10);color:#19d17e}
.network-monitoring-card--reference .network-monitoring-card__status--online .network-monitoring-card__status-right strong{color:#13b977}
.network-monitoring-card--reference .network-monitoring-card__status--offline .network-monitoring-card__status-icon{background:rgba(239,83,80,.09);color:#e65355}
.network-monitoring-card--reference .network-monitoring-card__status--offline .network-monitoring-card__status-right strong{color:#e55355}
.network-monitoring-card--reference .network-monitoring-card__status--isolated .network-monitoring-card__status-icon{background:rgba(231,161,36,.10);color:#d99820;font-size:17px;font-weight:850}
.network-monitoring-card--reference .network-monitoring-card__status--isolated .network-monitoring-card__status-right strong{color:#dc961d}
.network-monitoring-card--reference .network-monitoring-card__footer{display:flex;align-items:center;justify-content:space-between;gap:12px;padding:13px 24px;border-top:1px solid #dbe3ed;color:#8a95a7;font-size:10px}
.network-monitoring-card--reference .network-monitoring-card__footer-live{letter-spacing:0;text-transform:none}
.network-monitoring-card--reference .network-monitoring-card__footer-live b{color:#18b878;font-weight:800}
@keyframes netlive{0%,100%{transform:scale(1)}50%{transform:scale(1.35)}}@keyframes netglow{0%,100%{opacity:.65;transform:scale(.88)}50%{opacity:1;transform:scale(1.13)}}@keyframes netspin{to{transform:rotate(360deg)}}@keyframes netspinback{from{transform:rotate(360deg)}to{transform:rotate(0)}}@keyframes netpulse{0%{opacity:.75;transform:scale(.65)}100%{opacity:0;transform:scale(1.7)}}@keyframes netenter{from{opacity:0;transform:scale(.76) rotate(-18deg)}to{opacity:1;transform:scale(1) rotate(0)}}@keyframes netcenter{from{opacity:0;transform:scale(.68)}to{opacity:1;transform:scale(1)}}@keyframes netwifi{0%,100%{transform:scale(1)}50%{transform:scale(1.14)}}
.dark .network-monitoring-card--reference{background:rgba(13,18,28,.92);border-color:rgba(53,65,85,.72);box-shadow:0 25px 65px rgba(0,0,0,.38);color:#edf3ff}
.dark .network-monitoring-card--reference .network-monitoring-card__header,.dark .network-monitoring-card--reference .network-monitoring-card__footer{border-color:rgba(55,67,87,.58)}
.dark .network-monitoring-card--reference .network-monitoring-card__title,.dark .network-monitoring-card--reference .network-monitoring-card__donut-center>strong,.dark .network-monitoring-card--reference .network-monitoring-card__status-text strong{color:#edf3ff}
.dark .network-monitoring-card--reference .network-monitoring-card__subtitle,.dark .network-monitoring-card--reference .network-monitoring-card__donut-label,.dark .network-monitoring-card--reference .network-monitoring-card__status-heading,.dark .network-monitoring-card--reference .network-monitoring-card__status-text small,.dark .network-monitoring-card--reference .network-monitoring-card__footer{color:#98a6bb}
.dark .network-monitoring-card--reference .network-monitoring-card__donut{box-shadow:0 0 0 5px rgba(13,18,28,.78),0 0 22px rgba(35,126,255,.19)}
.dark .network-monitoring-card--reference .network-monitoring-card__donut-center{background:rgba(13,18,28,.97);box-shadow:inset 0 1px 5px rgba(0,0,0,.42)}
.dark .network-monitoring-card--reference .network-monitoring-card__status{background:rgba(22,29,42,.78);border-color:rgba(51,64,85,.72)}
.dark .network-monitoring-card--reference .network-monitoring-card__status:hover{background:rgba(27,37,53,.96);box-shadow:0 7px 22px rgba(0,0,0,.24)}
.dark .network-monitoring-card--reference .network-monitoring-card__health--healthy{background:rgba(6,78,59,.72);color:#a7f3d0}.dark .network-monitoring-card--reference .network-monitoring-card__health--warning{background:rgba(120,53,15,.72);color:#fde68a}.dark .network-monitoring-card--reference .network-monitoring-card__health--critical{background:rgba(136,19,55,.70);color:#fecdd3}
@media(max-width:760px){.network-monitoring-card--reference .network-monitoring-card__header{padding:0 16px}.network-monitoring-card--reference .network-monitoring-card__body{grid-template-columns:minmax(8.9rem,.88fr) minmax(0,1.12fr);align-items:center;column-gap:.8rem;min-height:0;padding:1rem}.network-monitoring-card--reference .network-monitoring-card__donut-area{grid-column:1;grid-row:1;width:9.8rem;height:9.8rem}.network-monitoring-card--reference .network-monitoring-card__donut-area:before{width:7.2rem;height:7.2rem}.network-monitoring-card--reference .network-monitoring-card__signal-ring{width:9rem;height:9rem}.network-monitoring-card--reference .network-monitoring-card__pulse-ring{width:5.8rem;height:5.8rem}.network-monitoring-card--reference .network-monitoring-card__donut{width:8.15rem;height:8.15rem}.network-monitoring-card--reference .network-monitoring-card__donut-center{width:5.7rem;height:5.7rem;padding:.35rem}.network-monitoring-card--reference .network-monitoring-card__wifi{width:1.55rem;height:1.55rem;margin-bottom:.22rem;font-size:.78rem}.network-monitoring-card--reference .network-monitoring-card__donut-center>strong{font-size:1.45rem}.network-monitoring-card--reference .network-monitoring-card__donut-label{margin-top:.18rem;font-size:.52rem}.network-monitoring-card--reference .network-monitoring-card__health{margin-top:.3rem;padding:.18rem .3rem;font-size:.38rem}.network-monitoring-card--reference .network-monitoring-card__status-area{grid-column:2;grid-row:1;gap:.4rem;width:100%}.network-monitoring-card--reference .network-monitoring-card__status-heading{margin:0 0 .05rem;font-size:.58rem}.network-monitoring-card--reference .network-monitoring-card__status{min-height:3.5rem;padding:.38rem .45rem;border-radius:.55rem}.network-monitoring-card--reference .network-monitoring-card__status-left{gap:.35rem}.network-monitoring-card--reference .network-monitoring-card__status-icon{flex-basis:1.45rem;width:1.45rem;height:1.45rem;border-radius:.38rem;font-size:.62rem}.network-monitoring-card--reference .network-monitoring-card__status--isolated .network-monitoring-card__status-icon{font-size:.78rem}.network-monitoring-card--reference .network-monitoring-card__status-text{gap:0}.network-monitoring-card--reference .network-monitoring-card__status-text strong{overflow:hidden;font-size:.64rem;text-overflow:ellipsis;white-space:nowrap}.network-monitoring-card--reference .network-monitoring-card__status-text small{display:none}.network-monitoring-card--reference .network-monitoring-card__status-right{align-items:flex-end;gap:.12rem;margin-left:.2rem}.network-monitoring-card--reference .network-monitoring-card__status-right strong{font-size:.88rem}.network-monitoring-card--reference .network-monitoring-card__view-text{display:inline-block;font-size:.55rem;font-weight:750;white-space:nowrap}.network-monitoring-card--reference .network-monitoring-card__status:hover{transform:translateX(2px)}.network-monitoring-card--reference .network-monitoring-card__footer{padding:13px 16px}}
@media(max-width:380px){.network-monitoring-card--reference .network-monitoring-card__body{grid-template-columns:minmax(7.8rem,.86fr) minmax(0,1.14fr);column-gap:.45rem;padding:.75rem}.network-monitoring-card--reference .network-monitoring-card__donut-area{width:8.6rem;height:8.6rem}.network-monitoring-card--reference .network-monitoring-card__signal-ring{width:7.9rem;height:7.9rem}.network-monitoring-card--reference .network-monitoring-card__pulse-ring{width:5rem;height:5rem}.network-monitoring-card--reference .network-monitoring-card__donut{width:7.15rem;height:7.15rem}.network-monitoring-card--reference .network-monitoring-card__donut-center{width:5rem;height:5rem}.network-monitoring-card--reference .network-monitoring-card__donut-center>strong{font-size:1.2rem}.network-monitoring-card--reference .network-monitoring-card__wifi,.network-monitoring-card--reference .network-monitoring-card__status-icon{display:none}.network-monitoring-card--reference .network-monitoring-card__donut-label{font-size:.47rem}.network-monitoring-card--reference .network-monitoring-card__health{font-size:.34rem}.network-monitoring-card--reference .network-monitoring-card__status{min-height:3.15rem;padding:.3rem .35rem}.network-monitoring-card--reference .network-monitoring-card__status-text strong{font-size:.59rem}.network-monitoring-card--reference .network-monitoring-card__status-right strong{font-size:.78rem}.network-monitoring-card--reference .network-monitoring-card__view-text{font-size:.48rem}}
@media(prefers-reduced-motion:reduce){.network-monitoring-card--reference *,.network-monitoring-card--reference *:before,.network-monitoring-card--reference *:after{animation:none!important;transition:none!important}}



/* STAFF-FINANCE-COLOURFUL-V4-START */
/* Tema Colourful V4 untuk kartu Keuangan Wilayah Admin Staff. */
.financial-summary-card--model-a {
    position: relative;
    overflow: hidden;
    border-color: #dbeafe;
    background:
        radial-gradient(circle at 100% 0%, rgba(99, 102, 241, .16), transparent 31%),
        radial-gradient(circle at 0% 100%, rgba(20, 184, 166, .10), transparent 28%),
        linear-gradient(145deg, #ffffff 0%, #f7faff 100%);
    box-shadow: 0 18px 42px rgba(30, 64, 175, .11), 0 2px 8px rgba(15, 23, 42, .05);
}
.financial-summary-card--model-a .financial-summary-card__header {
    border-bottom-color: #dbeafe;
    background: linear-gradient(110deg, rgba(238, 242, 255, .98), rgba(255, 255, 255, .78));
}
.financial-summary-card--model-a .financial-summary-card__eyebrow { color: #4f46e5; }
.financial-summary-card--model-a .financial-summary-card__title { color: #1e1b4b; }
.financial-summary-card--model-a .financial-summary-card__badge {
    border-color: #c7d2fe;
    background: #eef2ff;
    color: #4338ca;
    box-shadow: 0 6px 14px rgba(79, 70, 229, .10);
}
.financial-summary-card--model-a .financial-summary-card__badge-dot {
    background: #6366f1;
    box-shadow: 0 0 0 4px rgba(99, 102, 241, .14);
}

.financial-summary-card--model-a .finance-profit-hero {
    position: relative;
    overflow: hidden;
    border: 0;
    border-radius: 18px;
    background: linear-gradient(135deg, #3730a3 0%, #6366f1 53%, #a855f7 100%);
    box-shadow: 0 15px 30px rgba(79, 70, 229, .28);
}
.financial-summary-card--model-a .finance-profit-hero::before {
    position: absolute;
    top: -4.7rem;
    right: -2.9rem;
    width: 11rem;
    height: 11rem;
    border-radius: 999px;
    background: rgba(255, 255, 255, .14);
    content: "";
}
.financial-summary-card--model-a .finance-profit-hero::after {
    position: absolute;
    right: 1.2rem;
    bottom: -2.8rem;
    width: 6rem;
    height: 6rem;
    border: 1px solid rgba(255, 255, 255, .24);
    border-radius: 999px;
    content: "";
}
.financial-summary-card--model-a .finance-profit-hero__top,
.financial-summary-card--model-a .finance-profit-hero__value,
.financial-summary-card--model-a .finance-profit-hero__note {
    position: relative;
    z-index: 1;
}
.financial-summary-card--model-a .finance-profit-hero__label,
.financial-summary-card--model-a .finance-profit-hero__value,
.financial-summary-card--model-a .finance-profit-hero__note { color: #fff; }
.financial-summary-card--model-a .finance-profit-hero__label { opacity: .84; }
.financial-summary-card--model-a .finance-profit-hero__note { opacity: .88; }
.financial-summary-card--model-a .finance-profit-hero__icon {
    position: relative;
    z-index: 1;
    background: rgba(255, 255, 255, .20);
    color: #fff;
    box-shadow: none;
    backdrop-filter: blur(5px);
}
.financial-summary-card--model-a .finance-profit-hero--negative {
    background: linear-gradient(135deg, #9f1239 0%, #e11d48 56%, #fb7185 100%);
    box-shadow: 0 15px 30px rgba(225, 29, 72, .26);
}

.financial-summary-card--model-a .finance-flow {
    position: relative;
    overflow: hidden;
    border-radius: 16px;
    box-shadow: 0 8px 18px rgba(15, 23, 42, .055);
}
.financial-summary-card--model-a .finance-flow--income {
    border-color: #99f6e4;
    background: linear-gradient(145deg, #ecfdf5, #ecfeff);
}
.financial-summary-card--model-a .finance-flow--expense {
    border-color: #fecdd3;
    background: linear-gradient(145deg, #fff1f2, #fff7ed);
}
.financial-summary-card--model-a .finance-flow--income .finance-flow__icon {
    background: linear-gradient(135deg, #0f766e, #14b8a6);
    color: #fff;
    box-shadow: 0 6px 14px rgba(13, 148, 136, .24);
}
.financial-summary-card--model-a .finance-flow--expense .finance-flow__icon {
    background: linear-gradient(135deg, #e11d48, #fb7185);
    color: #fff;
    box-shadow: 0 6px 14px rgba(225, 29, 72, .23);
}
.financial-summary-card--model-a .finance-flow--income .finance-flow__label,
.financial-summary-card--model-a .finance-flow--income .finance-flow__value { color: #0f766e; }
.financial-summary-card--model-a .finance-flow--expense .finance-flow__label,
.financial-summary-card--model-a .finance-flow--expense .finance-flow__value { color: #be123c; }
.financial-summary-card--model-a .finance-flow__bar { background: rgba(148, 163, 184, .22); }
.financial-summary-card--model-a .finance-flow__bar--income::after {
    background: linear-gradient(90deg, #14b8a6, #2dd4bf);
}
.financial-summary-card--model-a .finance-flow__bar--expense::after {
    background: linear-gradient(90deg, #f43f5e, #fb7185);
}

.financial-summary-card--model-a .finance-support-list {
    border-color: #e0e7ff;
    border-radius: 16px;
    background: #fff;
    box-shadow: 0 8px 18px rgba(15, 23, 42, .045);
}
.financial-summary-card--model-a .finance-support-row { transition: background 180ms ease, transform 180ms ease; }
.financial-summary-card--model-a .finance-support-row:hover {
    background: #f8fafc;
    transform: translateX(2px);
}
.financial-summary-card--model-a .finance-support-row--estimate .finance-support-row__icon {
    background: linear-gradient(135deg, #2563eb, #6366f1);
    color: #fff;
    box-shadow: 0 6px 14px rgba(37, 99, 235, .20);
}
.financial-summary-card--model-a .finance-support-row--estimate .finance-support-row__value { color: #2563eb; }
.financial-summary-card--model-a .finance-support-row--pending .finance-support-row__icon {
    background: linear-gradient(135deg, #f59e0b, #f97316);
    color: #fff;
    box-shadow: 0 6px 14px rgba(245, 158, 11, .20);
}
.financial-summary-card--model-a .finance-support-row--pending .finance-support-row__value { color: #c2410c; }

[data-recent-payment-activity] {
    position: relative;
    overflow: hidden;
    border-color: #c7d2fe !important;
    background:
        radial-gradient(circle at 100% 0%, rgba(45, 212, 191, .15), transparent 31%),
        linear-gradient(145deg, #fff 0%, #f8faff 100%);
    box-shadow: 0 14px 30px rgba(30, 64, 175, .08);
}
[data-recent-payment-activity] > div:first-child {
    padding: .85rem 1rem;
    margin: -1.25rem -1.25rem 1rem;
    border-bottom: 1px solid #dbeafe;
    background: linear-gradient(110deg, #eff6ff, #f0fdfa);
}
[data-recent-payment-activity] article {
    border-radius: .8rem;
    transition: background 180ms ease, transform 180ms ease;
}
[data-recent-payment-activity] article:hover {
    background: #f0fdfa;
    transform: translateX(2px);
}
[data-recent-payment-activity] strong { color: #059669 !important; }

.dark .financial-summary-card--model-a {
    border-color: #3730a3;
    background: radial-gradient(circle at 100% 0%, rgba(99, 102, 241, .25), transparent 31%), linear-gradient(145deg, #17162a, #1e1b3a);
}
.dark .financial-summary-card--model-a .financial-summary-card__header {
    border-bottom-color: #3730a3;
    background: linear-gradient(110deg, rgba(67, 56, 202, .30), rgba(30, 27, 58, .18));
}
.dark .financial-summary-card--model-a .financial-summary-card__title { color: #e0e7ff; }
.dark .financial-summary-card--model-a .finance-flow--income {
    border-color: rgba(45, 212, 191, .34);
    background: rgba(15, 118, 110, .17);
}
.dark .financial-summary-card--model-a .finance-flow--expense {
    border-color: rgba(251, 113, 133, .34);
    background: rgba(190, 24, 93, .14);
}
.dark .financial-summary-card--model-a .finance-support-list {
    border-color: #3730a3;
    background: #1d1b2a;
}
.dark .financial-summary-card--model-a .finance-support-row:hover { background: rgba(67, 56, 202, .18); }
.dark [data-recent-payment-activity] {
    border-color: #3730a3 !important;
    background: radial-gradient(circle at 100% 0%, rgba(45, 212, 191, .18), transparent 31%), linear-gradient(145deg, #17162a, #1e1b3a);
}
.dark [data-recent-payment-activity] > div:first-child {
    border-bottom-color: #3730a3;
    background: linear-gradient(110deg, rgba(30, 64, 175, .27), rgba(15, 118, 110, .18));
}
.dark [data-recent-payment-activity] article:hover { background: rgba(15, 118, 110, .16); }
/* STAFF-FINANCE-COLOURFUL-V4-END */

</style>
