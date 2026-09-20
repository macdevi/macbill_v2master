<style>
    /* FINANCE-DASHBOARD-FINAL */
    .db2-finance-v5 {
        overflow: hidden;
        border-color: #e2e8f0;
        background: #ffffff;
        box-shadow: 0 16px 30px -16px rgba(15, 23, 42, .16), 0 5px 12px -8px rgba(15, 23, 42, .07);
    }

    .db2-finance-v5 .db2-finance-v6__header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        margin: 0 0 1.15rem;
        padding: 0;
        border: 0;
        background: transparent;
    }

    .db2-finance-v5 .db2-finance-v6__title {
        margin: 0;
        color: #1e293b;
        font-size: .95rem;
        font-weight: 850;
        letter-spacing: -.02em;
        line-height: 1.2;
    }

    .db2-finance-v5 .db2-finance-v6__date {
        display: inline-flex;
        align-items: center;
        gap: .38rem;
        flex: 0 0 auto;
        color: #64748b;
        font-size: .69rem;
        font-weight: 750;
        white-space: nowrap;
    }

    .db2-finance-v5 .db2-finance-v6__date img {
        width: 1rem;
        height: 1rem;
        object-fit: contain;
    }

    .db2-finance-v5 .db2-finance-v6__profit-hero {
        position: relative;
        isolation: isolate;
        display: block;
        overflow: hidden;
        min-height: 9.75rem;
        height: auto;
        margin: 0 0 .9rem;
        padding: 1.15rem 1.2rem 1rem;
        border: 1px solid rgba(191, 219, 254, .4);
        border-radius: 1.1rem;
        color: #ffffff;
        background: linear-gradient(128deg, #1d4ed8 0%, #2563eb 53%, #3b82f6 100%);
        box-shadow: 0 15px 24px -13px rgba(29, 78, 216, .7);
    }

    .db2-finance-v5 .db2-finance-v6__profit-hero::before,
    .db2-finance-v5 .db2-finance-v6__profit-hero::after {
        position: absolute;
        z-index: -1;
        border: 1.7rem solid rgba(219, 234, 254, .14);
        border-radius: 50%;
        content: "";
    }

    .db2-finance-v5 .db2-finance-v6__profit-hero::before {
        top: -5.8rem;
        right: -3.25rem;
        width: 11.5rem;
        height: 11.5rem;
    }

    .db2-finance-v5 .db2-finance-v6__profit-hero::after {
        bottom: -6.6rem;
        left: 47%;
        width: 10.8rem;
        height: 10.8rem;
        border-color: rgba(147, 197, 253, .13);
    }

    .db2-finance-v5 .db2-finance-v6__profit-top,
    .db2-finance-v5 .db2-finance-v6__profit-main {
        position: relative;
        z-index: 1;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: .75rem;
    }

    .db2-finance-v5 .db2-finance-v6__profit-main {
        align-items: flex-end;
        margin-top: .72rem;
    }

    .db2-finance-v5 .db2-finance-v6__profit-label {
        display: inline-flex;
        align-items: center;
        gap: .38rem;
        color: #dbeafe;
        font-size: .69rem;
        font-weight: 750;
    }

    .db2-finance-v5 .db2-finance-v6__profit-label img {
        width: 1.1rem;
        height: 1.1rem;
        object-fit: contain;
        filter: brightness(0) invert(1);
        opacity: .94;
    }

    .db2-finance-v5 .db2-finance-v6__period-chip {
        display: inline-flex;
        align-items: center;
        gap: .28rem;
        padding: .32rem .48rem;
        border: 1px solid rgba(219, 234, 254, .25);
        border-radius: 999px;
        background: rgba(30, 64, 175, .22);
        color: #dbeafe;
        font-size: .55rem;
        font-weight: 750;
        white-space: nowrap;
    }

    .db2-finance-v5 .db2-finance-v6__period-chip img {
        width: .74rem;
        height: .74rem;
        filter: brightness(0) invert(1);
        opacity: .88;
    }

    .db2-finance-v5 .db2-finance-v6__profit-amount {
        display: block;
        width: auto;
        max-width: none;
        margin: 0;
        padding: 0;
        border: 0;
        background: transparent;
        color: #ffffff;
        font-size: clamp(1.35rem, 3.3vw, 1.72rem);
        font-weight: 850;
        letter-spacing: -.055em;
        line-height: 1.08;
        text-align: left;
    }

    .db2-finance-v5 .db2-finance-v6__profit-copy {
        display: block;
        width: auto;
        max-width: none;
        margin: .3rem 0 0;
        padding: 0;
        border: 0;
        background: transparent;
        color: #bfdbfe;
        font-size: .6rem;
        font-weight: 650;
        line-height: 1.35;
        text-align: left;
    }

    .db2-finance-v5 .db2-finance-v6__profit-status {
        display: inline-flex;
        align-items: center;
        gap: .22rem;
        align-self: flex-end;
        margin-bottom: .06rem;
        padding: .34rem .45rem;
        border: 1px solid rgba(255, 255, 255, .15);
        border-radius: .52rem;
        background: rgba(15, 23, 42, .13);
        color: #dcfce7;
        font-size: .56rem;
        font-weight: 800;
        white-space: nowrap;
    }

    .db2-finance-v5 .db2-finance-v6__profit-status img {
        width: .82rem;
        height: .82rem;
        object-fit: contain;
    }

    .db2-finance-v5 .db2-finance-v6__profit-status.is-negative {
        color: #fee2e2;
    }

    .db2-finance-v5 .db2-finance-v6__profit-status.is-neutral {
        color: #e0e7ff;
    }

    .db2-finance-v5 .db2-finance-v6__chart {
        position: relative;
        z-index: 1;
        display: block;
        width: 100%;
        height: 2.5rem;
        margin-top: .66rem;
        padding-top: .12rem;
        border-top: 1px solid rgba(219, 234, 254, .18);
    }

    .db2-finance-v5 .db2-finance-v6__chart svg {
        display: block;
        width: 100%;
        height: 2.6rem;
        overflow: visible;
    }

    .db2-finance-v5 .db2-finance-v6__chart-guide {
        fill: none;
        stroke: rgba(219, 234, 254, .16);
        stroke-width: 1;
        stroke-dasharray: 3 4;
    }

    .db2-finance-v5 .db2-finance-v6__chart-bar {
        fill: #93c5fd;
        opacity: .6;
    }

    .db2-finance-v5 .db2-finance-v6__chart-bar.is-strong {
        fill: #bfdbfe;
        opacity: .92;
    }

    .db2-finance-v5 .db2-finance-v6__chart-line {
        fill: none;
        stroke: #ffffff;
        stroke-width: 2.2;
        stroke-linecap: round;
        stroke-linejoin: round;
    }

    .db2-finance-v5 .db2-finance-v6__chart-dot {
        fill: #ffffff;
        stroke: #60a5fa;
        stroke-width: 2;
    }

    .db2-finance-v5 .db2-finance-v6__flow-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: .75rem;
        margin: 0 0 .9rem;
        padding: 0;
    }

    .db2-finance-v5 .db2-finance-v6__flow-grid .db2-flow {
        display: block;
        min-width: 0;
        min-height: 0;
        margin: 0;
        padding: .82rem;
        border-radius: .9rem;
    }

    .db2-finance-v5 .db2-flow--income {
        border-color: #dcfce7;
        background: #f0fdf4;
    }

    .db2-finance-v5 .db2-flow--expense {
        border-color: #fee2e2;
        background: #fef2f2;
    }

    .db2-finance-v5 .db2-finance-v6__flow-grid .db2-flow__top {
        display: flex;
        align-items: center;
        justify-content: flex-start;
        gap: .42rem;
        margin-bottom: .45rem;
    }

    .db2-finance-v5 .db2-flow__icon {
        display: grid;
        width: 1.55rem;
        height: 1.55rem;
        place-items: center;
        border: 0;
        border-radius: .42rem;
        background: transparent;
        box-shadow: none;
    }

    .db2-finance-v5 .db2-flow__icon img {
        display: block;
        width: 1.25rem;
        height: 1.25rem;
        max-width: none;
        max-height: none;
        object-fit: contain;
    }

    .db2-finance-v5 .db2-flow__label {
        font-size: .58rem;
        font-weight: 850;
        letter-spacing: .04em;
        text-transform: uppercase;
    }

    .db2-finance-v5 .db2-flow--income .db2-flow__label {
        color: #166534;
    }

    .db2-finance-v5 .db2-flow--expense .db2-flow__label {
        color: #991b1b;
    }

    .db2-finance-v5 .db2-finance-v6__flow-grid .db2-flow > strong {
        display: block;
        overflow: hidden;
        width: 100%;
        margin: 0;
        padding: 0;
        font-size: .83rem;
        font-weight: 850;
        letter-spacing: -.035em;
        line-height: 1.15;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .db2-finance-v5 .db2-flow--income > strong {
        color: #15803d;
    }

    .db2-finance-v5 .db2-flow--expense > strong {
        color: #b91c1c;
    }

    .db2-finance-v5 .db2-flow__meta,
    .db2-finance-v5 .db2-progress {
        display: none;
    }

    .db2-finance-v5 .db2-finance-v6__connected-list {
        display: block;
        width: 100%;
        margin: 0;
        padding: 0;
        overflow: hidden;
        border: 1px solid #edf2f7;
        border-radius: .92rem;
        background: #ffffff;
    }

    .db2-finance-v5 .db2-finance-v6__connected-list .db2-finance-note,
    .db2-finance-v5 .db2-finance-v6__connected-list .db2-finance-note--action,
    .db2-finance-v5 .db2-finance-v6__connected-list .db2-finance-note--pending,
    .db2-finance-v5 .db2-finance-v6__connected-list .db2-finance-note--estimate {
        position: static;
        display: grid;
        grid-template-columns: 2.1rem minmax(0, 1fr) auto;
        align-items: center;
        justify-content: normal;
        min-height: 4.05rem;
        width: 100%;
        gap: .66rem;
        margin: 0;
        padding: .74rem .82rem;
        border: 0;
        border-radius: 0;
        background: #ffffff;
        box-shadow: none;
        text-align: left;
        transform: none;
    }

    .db2-finance-v5 .db2-finance-v6__connected-list .db2-finance-note + .db2-finance-note {
        border-top: 1px solid #f1f5f9;
    }

    .db2-finance-v5 .db2-finance-v6__connected-list .db2-finance-note--action {
        grid-template-columns: 2.1rem minmax(0, 1fr) auto auto;
        cursor: pointer;
    }

    .db2-finance-v5 .db2-finance-v6__connected-list .db2-finance-note--action:hover {
        background: #fafcff;
    }

    .db2-finance-v5 .db2-finance-v6__connected-list .db2-finance-note__icon {
        display: grid;
        width: 2.1rem;
        height: 2.1rem;
        min-width: 2.1rem;
        place-items: center;
        margin: 0;
        padding: 0;
        border: 0;
        border-radius: 50%;
        background: #fff7e6;
        box-shadow: none;
        transform: none;
    }

    .db2-finance-v5 .db2-finance-v6__connected-list .db2-finance-note__icon img {
        display: block;
        width: 1.15rem;
        height: 1.15rem;
        max-width: none;
        max-height: none;
        object-fit: contain;
    }

    .db2-finance-v5 .db2-finance-v6__connected-list .db2-finance-note--pending .db2-finance-note__icon {
        background: #fff3e8;
    }

    .db2-finance-v5 .db2-finance-v6__connected-list .db2-finance-note--estimate .db2-finance-note__icon {
        background: #eaf2ff;
    }

    .db2-finance-v5 .db2-finance-v6__connected-list .db2-finance-note__copy {
        display: block;
        min-width: 0;
        margin: 0;
        padding: 0;
        line-height: 1.2;
    }

    .db2-finance-v5 .db2-finance-v6__connected-list .db2-finance-note__copy strong,
    .db2-finance-v5 .db2-finance-v6__connected-list .db2-finance-note__copy small {
        display: block;
        overflow: hidden;
        margin: 0;
        padding: 0;
        color: #475569;
        font-size: .7rem;
        font-weight: 800;
        letter-spacing: 0;
        line-height: 1.25;
        text-align: left;
        text-overflow: ellipsis;
        text-transform: none;
        white-space: nowrap;
    }

    .db2-finance-v5 .db2-finance-v6__connected-list .db2-finance-note__value {
        position: static;
        display: block;
        align-self: center;
        justify-self: end;
        margin: 0;
        padding: 0;
        color: #d97706;
        font-size: .78rem;
        font-weight: 850;
        letter-spacing: -.03em;
        line-height: 1.1;
        white-space: nowrap;
    }

    .db2-finance-v5 .db2-finance-v6__connected-list .db2-finance-note--estimate .db2-finance-note__value {
        color: #1d4ed8;
    }

    .db2-finance-v5 .db2-finance-v6__connected-list .db2-finance-note--pending,
    .db2-finance-v5 .db2-finance-v6__connected-list .db2-finance-note--estimate {
        grid-template-columns: 2.1rem minmax(0, 1fr) max-content;
        grid-template-rows: minmax(2.1rem, auto);
    }

    .db2-finance-v5 .db2-finance-v6__connected-list .db2-finance-note--pending .db2-finance-note__icon,
    .db2-finance-v5 .db2-finance-v6__connected-list .db2-finance-note--estimate .db2-finance-note__icon {
        grid-column: 1;
        grid-row: 1;
    }

    .db2-finance-v5 .db2-finance-v6__connected-list .db2-finance-note--pending .db2-finance-note__copy,
    .db2-finance-v5 .db2-finance-v6__connected-list .db2-finance-note--estimate .db2-finance-note__copy {
        grid-column: 2;
        grid-row: 1;
    }

    .db2-finance-v5 .db2-finance-v6__connected-list .db2-finance-note--pending .db2-finance-note__value,
    .db2-finance-v5 .db2-finance-v6__connected-list .db2-finance-note--estimate .db2-finance-note__value {
        grid-column: 3;
        grid-row: 1;
        width: auto;
        min-width: 0;
        max-width: none;
        text-align: right;
        font-variant-numeric: tabular-nums;
    }

    .db2-finance-v5 .db2-finance-v6__connected-list .db2-finance-note__invoice-summary {
        display: block;
        min-width: 2.1rem;
        margin: 0;
        padding: 0;
        text-align: right;
        line-height: 1;
    }

    .db2-finance-v5 .db2-finance-v6__connected-list .db2-finance-note__invoice-summary strong {
        display: block;
        margin: 0;
        color: #f59e0b;
        font-size: .9rem;
        font-weight: 850;
        letter-spacing: -.03em;
        line-height: 1;
    }

    .db2-finance-v5 .db2-finance-v6__connected-list .db2-finance-note__invoice-summary small {
        display: block;
        margin: .18rem 0 0;
        padding: 0;
        color: #94a3b8;
        font-size: .5rem;
        font-weight: 700;
        line-height: 1;
        text-align: right;
        text-transform: none;
    }

    .db2-finance-v5 .db2-finance-v6__connected-list .db2-finance-note__action-label {
        display: inline-flex;
        align-items: center;
        gap: .12rem;
        margin: 0;
        padding: 0;
        color: #2563eb;
        font-size: .61rem;
        font-weight: 850;
        line-height: 1;
        white-space: nowrap;
    }

    .dark .db2-finance-v5 {
        border-color: #263654;
        background: #111c31;
    }

    .dark .db2-finance-v5 .db2-finance-v6__title {
        color: #e5edf9;
    }

    .dark .db2-finance-v5 .db2-finance-v6__date {
        color: #9fb0c8;
    }

    .dark .db2-finance-v5 .db2-flow--income {
        border-color: rgba(74, 222, 128, .26);
        background: rgba(34, 197, 94, .12);
    }

    .dark .db2-finance-v5 .db2-flow--expense {
        border-color: rgba(251, 113, 133, .26);
        background: rgba(244, 63, 94, .12);
    }

    .dark .db2-finance-v5 .db2-flow--income .db2-flow__label,
    .dark .db2-finance-v5 .db2-flow--income > strong {
        color: #86efac;
    }

    .dark .db2-finance-v5 .db2-flow--expense .db2-flow__label,
    .dark .db2-finance-v5 .db2-flow--expense > strong {
        color: #fda4af;
    }

    .dark .db2-finance-v5 .db2-finance-v6__connected-list {
        border-color: #263654;
        background: #172338;
    }

    .dark .db2-finance-v5 .db2-finance-v6__connected-list .db2-finance-note {
        background: #172338;
    }

    .dark .db2-finance-v5 .db2-finance-v6__connected-list .db2-finance-note + .db2-finance-note {
        border-top-color: #263654;
    }

    .dark .db2-finance-v5 .db2-finance-v6__connected-list .db2-finance-note--action:hover {
        background: #1b2a43;
    }

    .dark .db2-finance-v5 .db2-finance-v6__connected-list .db2-finance-note__copy strong {
        color: #dce7f5;
    }

    .dark .db2-finance-v5 .db2-finance-v6__connected-list .db2-finance-note--pending .db2-finance-note__value {
        color: #fdba74;
    }

    .dark .db2-finance-v5 .db2-finance-v6__connected-list .db2-finance-note--estimate .db2-finance-note__value {
        color: #93c5fd;
    }

    .dark .db2-finance-v5 .db2-finance-v6__connected-list .db2-finance-note__invoice-summary strong {
        color: #fbbf24;
    }

    .dark .db2-finance-v5 .db2-finance-v6__connected-list .db2-finance-note__invoice-summary small {
        color: #9fb0c8;
    }

    @media (max-width: 640px) {
        .db2-finance-v5 .db2-finance-v6__header {
            align-items: flex-start;
            margin-bottom: 1rem;
        }

        .db2-finance-v5 .db2-finance-v6__profit-hero {
            min-height: 9.45rem;
            padding: 1rem;
        }

        .db2-finance-v5 .db2-finance-v6__connected-list .db2-finance-note,
        .db2-finance-v5 .db2-finance-v6__connected-list .db2-finance-note--action,
        .db2-finance-v5 .db2-finance-v6__connected-list .db2-finance-note--pending,
        .db2-finance-v5 .db2-finance-v6__connected-list .db2-finance-note--estimate {
            min-height: 3.85rem;
            gap: .55rem;
            padding: .68rem .72rem;
        }

        .db2-finance-v5 .db2-finance-v6__connected-list .db2-finance-note__icon {
            width: 1.95rem;
            height: 1.95rem;
            min-width: 1.95rem;
        }

        .db2-finance-v5 .db2-finance-v6__connected-list .db2-finance-note__value {
            font-size: .73rem;
        }

        .db2-finance-v5 .db2-finance-v6__connected-list .db2-finance-note__invoice-summary strong {
            font-size: .84rem;
        }

        .db2-finance-v5 .db2-finance-v6__connected-list .db2-finance-note--pending,
        .db2-finance-v5 .db2-finance-v6__connected-list .db2-finance-note--estimate {
            grid-template-columns: 1.95rem minmax(0, 1fr) max-content;
            grid-template-rows: minmax(1.95rem, auto);
        }

        .db2-finance-v5 .db2-finance-v6__connected-list .db2-finance-note--pending .db2-finance-note__value,
        .db2-finance-v5 .db2-finance-v6__connected-list .db2-finance-note--estimate .db2-finance-note__value {
            grid-column: 3;
            grid-row: 1;
            width: auto;
            min-width: 0;
            font-size: .73rem;
        }
    }
</style>
