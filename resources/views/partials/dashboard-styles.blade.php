<style>
    .db2 {
        --db2-bg: #f8fafc;
        --db2-surface: #ffffff;
        --db2-surface-soft: #f8fafc;
        --db2-border: #e2e8f0;
        --db2-text: #0f172a;
        --db2-muted: #64748b;
        --db2-faint: #94a3b8;
        --db2-primary: #5b42f3;
        --db2-primary-soft: #ede9fe;
        --db2-green: #16a34a;
        --db2-green-soft: #dcfce7;
        --db2-red: #e11d48;
        --db2-red-soft: #ffe4e6;
        --db2-amber: #d97706;
        --db2-amber-soft: #fef3c7;
        --db2-shadow: 0 14px 34px rgba(15, 23, 42, .07);
        width: 100%;
        max-width: 1320px;
        margin: 0 auto;
        color: var(--db2-text);
    }

    .dark .db2 {
        --db2-bg: #0f172a;
        --db2-surface: #111c31;
        --db2-surface-soft: #172338;
        --db2-border: #27364f;
        --db2-text: #e5edf9;
        --db2-muted: #9fb0c8;
        --db2-faint: #70819b;
        --db2-primary: #a78bfa;
        --db2-primary-soft: rgba(139, 92, 246, .15);
        --db2-green: #4ade80;
        --db2-green-soft: rgba(34, 197, 94, .13);
        --db2-red: #fb7185;
        --db2-red-soft: rgba(244, 63, 94, .13);
        --db2-amber: #fbbf24;
        --db2-amber-soft: rgba(245, 158, 11, .13);
        --db2-shadow: 0 18px 42px rgba(0, 0, 0, .22);
    }

    .db2,
    .db2 * {
        box-sizing: border-box;
    }

    .db2 button,
    .db2 a {
        -webkit-tap-highlight-color: transparent;
    }

    .db2-welcome {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 1rem;
        margin-bottom: 1.5rem;
    }

    .db2-eyebrow {
        margin: 0 0 .4rem;
        color: var(--db2-primary);
        font-size: .69rem;
        font-weight: 800;
        letter-spacing: .14em;
        line-height: 1;
    }

    .db2-welcome__title,
    .db2-card__title {
        margin: 0;
        color: var(--db2-text);
        letter-spacing: -.035em;
    }

    .db2-welcome__title {
        font-size: clamp(1.7rem, 3vw, 2.25rem);
        font-weight: 850;
        line-height: 1.15;
    }

    .db2-welcome__time,
    .db2-card__subtitle {
        margin: .45rem 0 0;
        color: var(--db2-muted);
        font-size: .86rem;
        line-height: 1.5;
    }

    .db2-live-chip,
    .db2-period-chip,
    .db2-health {
        display: inline-flex;
        align-items: center;
        gap: .45rem;
        border-radius: 999px;
        font-size: .72rem;
        font-weight: 800;
        line-height: 1;
        white-space: nowrap;
    }

    .db2-live-chip {
        padding: .66rem .84rem;
        border: 1px solid var(--db2-border);
        background: var(--db2-surface);
        color: var(--db2-muted);
        box-shadow: 0 4px 12px rgba(15, 23, 42, .04);
    }

    .db2-live-chip__dot {
        width: .5rem;
        height: .5rem;
        border-radius: 50%;
        background: var(--db2-green);
        box-shadow: 0 0 0 4px var(--db2-green-soft);
    }

    .db2-grid {
        display: grid;
        gap: 1.25rem;
    }

    .db2-grid--top {
        grid-template-columns: minmax(0, 1.25fr) minmax(340px, .9fr);
        align-items: stretch;
    }

    .db2-card {
        min-width: 0;
        margin-bottom: 1.25rem;
        padding: 1.4rem;
        overflow: hidden;
        border: 1px solid var(--db2-border);
        border-radius: 1.25rem;
        background: var(--db2-surface);
        box-shadow: var(--db2-shadow);
    }

    .db2-card__header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 1rem;
        margin-bottom: 1.35rem;
    }

    .db2-card__title {
        font-size: 1.12rem;
        font-weight: 800;
        line-height: 1.25;
    }

    .db2-health {
        padding: .55rem .7rem;
    }

    .db2-health--good {
        background: var(--db2-green-soft);
        color: var(--db2-green);
    }

    .db2-health--warn {
        background: var(--db2-amber-soft);
        color: var(--db2-amber);
    }

    .db2-health--bad {
        background: var(--db2-red-soft);
        color: var(--db2-red);
    }

    .db2-period-chip {
        padding: .54rem .7rem;
        background: var(--db2-primary-soft);
        color: var(--db2-primary);
    }

    .db2-network__content {
        display: grid;
        grid-template-columns: minmax(180px, .78fr) minmax(230px, 1.22fr);
        align-items: center;
        gap: 1.35rem;
    }

    .db2-donut-wrap {
        display: grid;
        justify-items: center;
        gap: .7rem;
    }

    .db2-donut {
        display: grid;
        width: 11.25rem;
        aspect-ratio: 1;
        place-items: center;
        padding: .72rem;
        border-radius: 50%;
        box-shadow: inset 0 0 0 1px rgba(15, 23, 42, .04);
    }

    .db2-donut__inner {
        display: flex;
        width: 100%;
        height: 100%;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        background: var(--db2-surface);
        text-align: center;
    }

    .db2-donut__icon {
        color: var(--db2-primary);
        font-size: 1.15rem;
        font-weight: 900;
        line-height: 1;
    }

    .db2-donut__inner strong {
        margin-top: .35rem;
        color: var(--db2-text);
        font-size: 1.9rem;
        font-weight: 850;
        letter-spacing: -.06em;
        line-height: 1;
    }

    .db2-donut__inner span:last-child,
    .db2-donut-caption {
        color: var(--db2-muted);
        font-size: .72rem;
        font-weight: 700;
    }

    .db2-donut__inner span:last-child {
        margin-top: .35rem;
    }

    .db2-donut-caption {
        margin: 0;
    }

    .db2-donut-caption strong {
        color: var(--db2-green);
    }

    .db2-status-list {
        display: grid;
        gap: .65rem;
    }

    .db2-status {
        display: grid;
        grid-template-columns: auto minmax(0, 1fr) auto;
        align-items: center;
        gap: .75rem;
        width: 100%;
        padding: .85rem .9rem;
        border: 1px solid var(--db2-border);
        border-radius: .95rem;
        background: var(--db2-surface-soft);
        color: var(--db2-text);
        cursor: pointer;
        text-align: left;
        transition: border-color .18s ease, transform .18s ease, background .18s ease;
    }

    .db2-status:hover {
        transform: translateY(-1px);
        border-color: var(--db2-primary);
        background: var(--db2-surface);
    }

    .db2-status:focus-visible,
    .db2-link:focus-visible,
    .customer-status-modal__close:focus-visible,
    .customer-status-modal__button:focus-visible {
        outline: 3px solid rgba(139, 92, 246, .42);
        outline-offset: 3px;
    }

    .db2-status__mark {
        display: grid;
        width: .78rem;
        height: .78rem;
        place-items: center;
        border-radius: 50%;
        background: var(--db2-faint);
        color: #fff;
        font-size: .64rem;
        font-weight: 900;
    }

    .db2-status--online .db2-status__mark {
        background: var(--db2-green);
        box-shadow: 0 0 0 4px var(--db2-green-soft);
    }

    .db2-status--offline .db2-status__mark {
        background: var(--db2-red);
        box-shadow: 0 0 0 4px var(--db2-red-soft);
    }

    .db2-status--isolated .db2-status__mark {
        background: var(--db2-amber);
        box-shadow: 0 0 0 4px var(--db2-amber-soft);
    }

    .db2-status__copy,
    .db2-status__value {
        display: flex;
        min-width: 0;
        flex-direction: column;
        gap: .16rem;
    }

    .db2-status__copy strong,
    .db2-status__value {
        font-size: .84rem;
        font-weight: 800;
    }

    .db2-status__copy small,
    .db2-status__value small {
        overflow: hidden;
        color: var(--db2-muted);
        font-size: .69rem;
        font-weight: 600;
        line-height: 1.2;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .db2-status__value {
        align-items: flex-end;
        color: var(--db2-text);
        font-size: 1rem;
    }

    .db2-status__value small {
        color: var(--db2-primary);
    }

    .db2-router {
        display: grid;
        grid-template-columns: minmax(0, 1.3fr) repeat(2, minmax(0, .7fr));
        gap: .75rem;
        margin-top: 1.25rem;
        padding-top: 1rem;
        border-top: 1px solid var(--db2-border);
    }

    .db2-router__item {
        display: flex;
        min-width: 0;
        flex-direction: column;
        gap: .24rem;
    }

    .db2-router__label {
        color: var(--db2-faint);
        font-size: .66rem;
        font-weight: 800;
        letter-spacing: .06em;
        text-transform: uppercase;
    }

    .db2-router__item strong {
        overflow: hidden;
        color: var(--db2-text);
        font-size: .78rem;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .db2-router__dot {
        display: inline-block;
        width: .5rem;
        height: .5rem;
        margin-right: .28rem;
        border-radius: 50%;
        background: var(--db2-red);
    }

    .db2-router__dot.is-connected {
        background: var(--db2-green);
        box-shadow: 0 0 0 3px var(--db2-green-soft);
    }

    .db2-profit {
        display: flex;
        flex-direction: column;
    }

    .db2-profit__hero {
        margin-bottom: 1rem;
        padding: 1.15rem;
        border: 1px solid rgba(91, 66, 243, .16);
        border-radius: 1rem;
        background: linear-gradient(135deg, var(--db2-primary-soft), transparent 90%);
    }

    .db2-profit--negative .db2-profit__hero {
        border-color: rgba(225, 29, 72, .18);
        background: linear-gradient(135deg, var(--db2-red-soft), transparent 90%);
    }

    .db2-profit__label,
    .db2-flow__label {
        display: block;
        color: var(--db2-muted);
        font-size: .68rem;
        font-weight: 800;
        letter-spacing: .08em;
        text-transform: uppercase;
    }

    .db2-profit__amount {
        display: block;
        margin: .42rem 0;
        color: var(--db2-text);
        font-size: clamp(1.35rem, 2.1vw, 1.9rem);
        font-weight: 850;
        letter-spacing: -.045em;
        line-height: 1.15;
    }

    .db2-profit__hero p {
        margin: 0;
        color: var(--db2-muted);
        font-size: .75rem;
        font-weight: 600;
        line-height: 1.45;
    }

    .db2-flow-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: .8rem;
    }

    .db2-flow {
        min-width: 0;
        padding: .9rem;
        border: 1px solid var(--db2-border);
        border-radius: .95rem;
        background: var(--db2-surface-soft);
    }

    .db2-flow strong {
        display: block;
        overflow: hidden;
        margin: .42rem 0 .25rem;
        color: var(--db2-text);
        font-size: .98rem;
        font-weight: 800;
        letter-spacing: -.025em;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .db2-flow__meta {
        display: block;
        overflow: hidden;
        color: var(--db2-muted);
        font-size: .68rem;
        font-weight: 600;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .db2-progress {
        display: block;
        height: .35rem;
        margin-top: .75rem;
        overflow: hidden;
        border-radius: 999px;
        background: var(--db2-border);
    }

    .db2-progress i {
        display: block;
        height: 100%;
        border-radius: inherit;
    }

    .db2-flow--income .db2-progress i {
        background: var(--db2-green);
    }

    .db2-flow--expense .db2-progress i {
        background: var(--db2-red);
    }

    .db2-finance-notes {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: .8rem;
        margin-top: auto;
        padding-top: 1rem;
    }

    .db2-finance-notes > div {
        min-width: 0;
        padding: .82rem .9rem;
        border-radius: .9rem;
        background: var(--db2-surface-soft);
    }

    .db2-finance-notes span,
    .db2-finance-notes small {
        display: block;
        color: var(--db2-muted);
        font-size: .67rem;
        font-weight: 700;
        line-height: 1.35;
    }

    .db2-finance-notes strong {
        display: block;
        overflow: hidden;
        margin-top: .28rem;
        color: var(--db2-text);
        font-size: .85rem;
        font-weight: 800;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .db2-finance-notes small {
        margin-top: .22rem;
    }

    .db2-link {
        display: inline-flex;
        align-items: center;
        gap: .35rem;
        color: var(--db2-primary);
        font-size: .76rem;
        font-weight: 800;
        text-decoration: none;
        white-space: nowrap;
    }

    .db2-link:hover {
        text-decoration: underline;
    }

    .db2-activity-list {
        display: grid;
    }

    .db2-activity-row {
        display: grid;
        grid-template-columns: auto minmax(0, 1fr) auto;
        align-items: center;
        gap: .8rem;
        padding: .9rem 0;
        border-top: 1px solid var(--db2-border);
    }

    .db2-activity-row:first-child {
        padding-top: 0;
        border-top: 0;
    }

    .db2-activity-row:last-child {
        padding-bottom: 0;
    }

    .db2-activity-row__icon {
        display: grid;
        width: 2rem;
        height: 2rem;
        place-items: center;
        border-radius: .7rem;
        font-size: 1rem;
        font-weight: 900;
    }

    .db2-activity-row__icon.is-income {
        background: var(--db2-green-soft);
        color: var(--db2-green);
    }

    .db2-activity-row__icon.is-expense {
        background: var(--db2-red-soft);
        color: var(--db2-red);
    }

    .db2-activity-row__copy {
        display: flex;
        min-width: 0;
        flex-direction: column;
        gap: .25rem;
    }

    .db2-activity-row__copy strong {
        overflow: hidden;
        color: var(--db2-text);
        font-size: .84rem;
        font-weight: 800;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .db2-activity-row__copy span {
        overflow: hidden;
        color: var(--db2-muted);
        font-size: .71rem;
        font-weight: 600;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .db2-activity-row__amount {
        color: var(--db2-text);
        font-size: .82rem;
        font-weight: 850;
        text-align: right;
        white-space: nowrap;
    }

    .db2-activity-row__amount.is-income,
    .db2-table .is-income {
        color: var(--db2-green);
    }

    .db2-activity-row__amount.is-expense,
    .db2-table .is-expense {
        color: var(--db2-red);
    }

    .db2-empty {
        padding: 1.5rem;
        border: 1px dashed var(--db2-border);
        border-radius: .9rem;
        color: var(--db2-muted);
        font-size: .84rem;
        font-weight: 600;
        text-align: center;
    }

    .db2-table-scroll {
        overflow-x: auto;
        margin: 0 -.25rem -.25rem;
        padding: 0 .25rem .25rem;
    }

    .db2-table {
        width: 100%;
        min-width: 760px;
        border-collapse: collapse;
        color: var(--db2-text);
        font-size: .79rem;
    }

    .db2-table th,
    .db2-table td {
        padding: .88rem .82rem;
        border-bottom: 1px solid var(--db2-border);
        text-align: right;
        vertical-align: middle;
        white-space: nowrap;
    }

    .db2-table th {
        color: var(--db2-faint);
        font-size: .65rem;
        font-weight: 800;
        letter-spacing: .055em;
        text-transform: uppercase;
    }

    .db2-table th:first-child,
    .db2-table td:first-child {
        text-align: left;
    }

    .db2-table tbody tr:last-child td {
        border-bottom: 0;
    }

    .db2-table td {
        color: var(--db2-muted);
        font-weight: 700;
    }

    .db2-table td strong {
        display: block;
        color: var(--db2-text);
        font-weight: 800;
    }

    .db2-table td small {
        display: block;
        margin-top: .2rem;
        color: var(--db2-faint);
        font-size: .67rem;
        font-weight: 700;
    }

    .customer-status-modal {
        position: fixed;
        z-index: 80;
        inset: 0;
        display: none;
        padding: 1rem;
    }

    .customer-status-modal.is-open {
        display: grid;
        place-items: center;
    }

    .customer-status-modal__backdrop {
        position: absolute;
        inset: 0;
        background: rgba(15, 23, 42, .62);
        backdrop-filter: blur(4px);
    }

    .customer-status-modal__panel {
        position: relative;
        z-index: 1;
        display: flex;
        width: min(100%, 38rem);
        max-height: min(44rem, calc(100vh - 2rem));
        flex-direction: column;
        overflow: hidden;
        border: 1px solid #e2e8f0;
        border-radius: 1.15rem;
        background: #fff;
        box-shadow: 0 26px 70px rgba(2, 6, 23, .36);
    }

    .dark .customer-status-modal__panel {
        border-color: #334155;
        background: #111c31;
    }

    .customer-status-modal__header,
    .customer-status-modal__footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        padding: 1.1rem 1.2rem;
    }

    .customer-status-modal__header {
        border-bottom: 1px solid #e2e8f0;
    }

    .dark .customer-status-modal__header {
        border-color: #334155;
    }

    .customer-status-modal__kicker {
        margin: 0 0 .35rem;
        color: #6d4aff;
        font-size: .67rem;
        font-weight: 850;
        letter-spacing: .12em;
    }

    .customer-status-modal__title {
        margin: 0;
        color: #0f172a;
        font-size: 1.15rem;
        font-weight: 850;
        letter-spacing: -.03em;
    }

    .dark .customer-status-modal__title {
        color: #e5edf9;
    }

    .customer-status-modal__count {
        margin: .28rem 0 0;
        color: #64748b;
        font-size: .76rem;
        font-weight: 650;
    }

    .dark .customer-status-modal__count {
        color: #9fb0c8;
    }

    .customer-status-modal__close {
        display: grid;
        width: 2.15rem;
        height: 2.15rem;
        flex: 0 0 auto;
        place-items: center;
        border: 1px solid #e2e8f0;
        border-radius: .7rem;
        background: #f8fafc;
        color: #475569;
        cursor: pointer;
        font-size: 1.45rem;
        line-height: 1;
    }

    .dark .customer-status-modal__close {
        border-color: #334155;
        background: #1e293b;
        color: #cbd5e1;
    }

    .customer-status-modal__list {
        display: grid;
        gap: .25rem;
        overflow-y: auto;
        padding: .65rem 1.2rem;
    }

    .customer-status-modal__customer {
        display: grid;
        grid-template-columns: auto minmax(0, 1fr);
        gap: .8rem;
        align-items: center;
        padding: .78rem 0;
        border-bottom: 1px solid #edf2f7;
    }

    .customer-status-modal__customer:last-child {
        border-bottom: 0;
    }

    .dark .customer-status-modal__customer {
        border-color: #26354d;
    }

    .customer-status-modal__avatar {
        display: grid;
        width: 2.4rem;
        height: 2.4rem;
        place-items: center;
        border-radius: .78rem;
        background: #ede9fe;
        color: #6d4aff;
        font-size: .9rem;
        font-weight: 900;
    }

    .dark .customer-status-modal__avatar {
        background: rgba(139, 92, 246, .18);
        color: #c4b5fd;
    }

    .customer-status-modal__customer-info {
        min-width: 0;
    }

    .customer-status-modal__customer-info h3 {
        overflow: hidden;
        margin: 0;
        color: #1e293b;
        font-size: .84rem;
        font-weight: 800;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .dark .customer-status-modal__customer-info h3 {
        color: #e5edf9;
    }

    .customer-status-modal__meta {
        display: flex;
        flex-wrap: wrap;
        gap: .22rem .55rem;
        margin-top: .28rem;
        color: #64748b;
        font-size: .69rem;
        font-weight: 600;
    }

    .dark .customer-status-modal__meta {
        color: #9fb0c8;
    }

    .customer-status-modal__empty {
        padding: 2.2rem 1rem;
        color: #64748b;
        font-size: .84rem;
        font-weight: 650;
        text-align: center;
    }

    .customer-status-modal__footer {
        justify-content: flex-end;
        border-top: 1px solid #e2e8f0;
    }

    .dark .customer-status-modal__footer {
        border-color: #334155;
    }

    .customer-status-modal__button {
        border: 0;
        border-radius: .75rem;
        background: #5b42f3;
        color: #fff;
        cursor: pointer;
        padding: .7rem 1rem;
        font-size: .78rem;
        font-weight: 800;
    }

    @media (max-width: 1024px) {
        .db2-grid--top {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 640px) {
        .db2-welcome {
            flex-direction: column;
            gap: .85rem;
        }

        .db2-live-chip {
            align-self: flex-start;
        }

        .db2-card {
            padding: 1.05rem;
            border-radius: 1rem;
        }

        .db2-card__header {
            gap: .75rem;
            margin-bottom: 1.05rem;
        }

        .db2-card__title {
            font-size: 1.03rem;
        }

        .db2-network__content {
            grid-template-columns: 1fr;
        }

        .db2-donut {
            width: 10.2rem;
        }

        .db2-flow-grid,
        .db2-finance-notes {
            grid-template-columns: 1fr;
        }

        .db2-router {
            grid-template-columns: 1fr 1fr;
        }

        .db2-router__item:first-child {
            grid-column: 1 / -1;
        }

        .db2-activity-row {
            grid-template-columns: auto minmax(0, 1fr);
        }

        .db2-activity-row__amount {
            grid-column: 2;
            justify-self: start;
            text-align: left;
        }

        .customer-status-modal {
            padding: .65rem;
        }

        .customer-status-modal__header,
        .customer-status-modal__footer {
            padding: 1rem;
        }

        .customer-status-modal__list {
            padding: .55rem 1rem;
        }
    }

    .db2-area-chips {
        display: flex;
        flex-wrap: wrap;
        gap: .45rem;
        margin-top: .7rem;
    }

    .db2-area-chip {
        display: inline-flex;
        align-items: center;
        gap: .35rem;
        max-width: 100%;
        padding: .42rem .65rem;
        overflow: hidden;
        border: 1px solid var(--db2-border);
        border-radius: 999px;
        background: var(--db2-primary-soft);
        color: var(--db2-primary);
        font-size: .69rem;
        font-weight: 800;
        line-height: 1;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .db2-area-chip::before {
        width: .38rem;
        height: .38rem;
        flex: 0 0 auto;
        border-radius: 50%;
        background: currentColor;
        content: "";
        opacity: .72;
    }


    .pending-invoice-modal {
        position: fixed;
        z-index: 1200;
        display: none;
        inset: 0;
        padding: 1rem;
    }
    .pending-invoice-modal.is-open {
        display: grid;
        place-items: center;
    }
    .pending-invoice-modal__backdrop {
        position: absolute;
        inset: 0;
        background: rgba(15, 23, 42, .56);
        backdrop-filter: blur(3px);
    }
    .pending-invoice-modal__panel {
        position: relative;
        z-index: 1;
        display: flex;
        width: min(43rem, 100%);
        max-height: min(43rem, calc(100vh - 2rem));
        flex-direction: column;
        overflow: hidden;
        border: 1px solid #dbeafe;
        border-radius: 1.2rem;
        background: #ffffff;
        box-shadow: 0 28px 80px rgba(15, 23, 42, .36);
    }
    .pending-invoice-modal__header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 1rem;
        padding: 1.2rem 1.25rem 1rem;
        border-bottom: 1px solid #e0e7ff;
        background: linear-gradient(115deg, #fff7ed, #ffffff);
    }
    .pending-invoice-modal__kicker {
        margin: 0;
        color: #c2410c;
        font-size: .64rem;
        font-weight: 850;
        letter-spacing: .11em;
    }
    .pending-invoice-modal__title {
        margin: .32rem 0 0;
        color: #0f172a;
        font-size: 1.15rem;
        font-weight: 850;
        letter-spacing: -.03em;
    }
    .pending-invoice-modal__summary {
        margin: .3rem 0 0;
        color: #64748b;
        font-size: .75rem;
        font-weight: 650;
    }
    .pending-invoice-modal__close {
        display: grid;
        width: 2.15rem;
        height: 2.15rem;
        flex: 0 0 auto;
        place-items: center;
        border: 1px solid #fed7aa;
        border-radius: .7rem;
        background: #fff7ed;
        color: #c2410c;
        cursor: pointer;
        font-size: 1.45rem;
        line-height: 1;
    }
    .pending-invoice-modal__list {
        overflow-y: auto;
        padding: .45rem 1.2rem;
    }
    .pending-invoice-modal__item {
        display: grid;
        grid-template-columns: auto minmax(0, 1fr) auto;
        align-items: center;
        gap: .8rem;
        padding: .85rem 0;
        border-bottom: 1px solid #edf2f7;
    }
    .pending-invoice-modal__item:last-child {
        border-bottom: 0;
    }
    .pending-invoice-modal__avatar {
        display: grid;
        width: 2.45rem;
        height: 2.45rem;
        place-items: center;
        border-radius: .78rem;
        background: linear-gradient(135deg, #f59e0b, #f97316);
        color: #ffffff;
        font-size: .78rem;
        font-weight: 900;
    }
    .pending-invoice-modal__info {
        min-width: 0;
    }
    .pending-invoice-modal__info h3 {
        overflow: hidden;
        margin: 0;
        color: #1e293b;
        font-size: .84rem;
        font-weight: 800;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    .pending-invoice-modal__meta {
        display: flex;
        flex-wrap: wrap;
        gap: .22rem .55rem;
        margin-top: .28rem;
        color: #64748b;
        font-size: .67rem;
        font-weight: 600;
    }
    .pending-invoice-modal__finance {
        display: flex;
        align-items: flex-end;
        flex-direction: column;
        gap: .25rem;
        text-align: right;
    }
    .pending-invoice-modal__finance > strong {
        color: #c2410c;
        font-size: .82rem;
        font-weight: 850;
        white-space: nowrap;
    }
    .pending-invoice-modal__status {
        display: inline-flex;
        padding: .25rem .42rem;
        border-radius: 999px;
        background: #fff7ed;
        color: #c2410c;
        font-size: .58rem;
        font-weight: 850;
        letter-spacing: .03em;
        text-transform: uppercase;
        white-space: nowrap;
    }
    .pending-invoice-modal__status.is-isolated {
        background: #ffedd5;
        color: #9a3412;
    }
    .pending-invoice-modal__item a {
        color: #4338ca;
        font-size: .68rem;
        font-weight: 850;
        text-decoration: none;
        white-space: nowrap;
    }
    .pending-invoice-modal__item a:hover {
        text-decoration: underline;
    }
    .pending-invoice-modal__empty {
        padding: 2.4rem 1rem;
        color: #64748b;
        font-size: .82rem;
        font-weight: 650;
        text-align: center;
    }
    .pending-invoice-modal__footer {
        padding: .85rem 1.25rem;
        border-top: 1px solid #e0e7ff;
        background: #fafcff;
        color: #64748b;
        font-size: .7rem;
        font-weight: 650;
    }
    body.pending-invoice-modal-open {
        overflow: hidden;
    }


    /* Finance activity page */
    .fa2 {
        --fa2-surface: #ffffff;
        --fa2-surface-soft: #f8fafc;
        --fa2-border: #e2e8f0;
        --fa2-text: #0f172a;
        --fa2-muted: #64748b;
        --fa2-faint: #94a3b8;
        --fa2-primary: #5b42f3;
        --fa2-primary-soft: #ede9fe;
        --fa2-green: #16a34a;
        --fa2-green-soft: #dcfce7;
        --fa2-red: #e11d48;
        --fa2-red-soft: #ffe4e6;
        --fa2-shadow: 0 14px 34px rgba(15, 23, 42, .07);
        width: 100%;
        max-width: 1320px;
        margin: 0 auto;
        color: var(--fa2-text);
    }

    .dark .fa2 {
        --fa2-surface: #111c31;
        --fa2-surface-soft: #172338;
        --fa2-border: #27364f;
        --fa2-text: #e5edf9;
        --fa2-muted: #9fb0c8;
        --fa2-faint: #70819b;
        --fa2-primary: #a78bfa;
        --fa2-primary-soft: rgba(139, 92, 246, .15);
        --fa2-green: #4ade80;
        --fa2-green-soft: rgba(34, 197, 94, .13);
        --fa2-red: #fb7185;
        --fa2-red-soft: rgba(244, 63, 94, .13);
        --fa2-shadow: 0 18px 42px rgba(0, 0, 0, .22);
    }

    .fa2,
    .fa2 * {
        box-sizing: border-box;
    }

    .fa2-hero {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 1rem;
        margin-bottom: 1.4rem;
    }

    .fa2-eyebrow {
        margin: 0 0 .42rem;
        color: var(--fa2-primary);
        font-size: .69rem;
        font-weight: 850;
        letter-spacing: .14em;
        line-height: 1;
    }

    .fa2-hero__title,
    .fa2-card__title {
        margin: 0;
        color: var(--fa2-text);
        letter-spacing: -.035em;
    }

    .fa2-hero__title {
        font-size: clamp(1.65rem, 3vw, 2.25rem);
        font-weight: 850;
        line-height: 1.15;
    }

    .fa2-hero__subtitle,
    .fa2-card__subtitle {
        margin: .5rem 0 0;
        color: var(--fa2-muted);
        font-size: .86rem;
        font-weight: 600;
        line-height: 1.5;
    }

    .fa2-back-link {
        display: inline-flex;
        align-items: center;
        gap: .5rem;
        flex: 0 0 auto;
        padding: .7rem .9rem;
        border: 1px solid var(--fa2-border);
        border-radius: .8rem;
        background: var(--fa2-surface);
        color: var(--fa2-primary);
        box-shadow: 0 4px 12px rgba(15, 23, 42, .04);
        font-size: .76rem;
        font-weight: 800;
        text-decoration: none;
        white-space: nowrap;
        transition: transform .18s ease, border-color .18s ease;
    }

    .fa2-back-link:hover {
        transform: translateY(-1px);
        border-color: var(--fa2-primary);
    }

    .fa2-summary {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 1rem;
        margin-bottom: 1.25rem;
    }

    .fa2-summary-card,
    .fa2-card {
        border: 1px solid var(--fa2-border);
        background: var(--fa2-surface);
        box-shadow: var(--fa2-shadow);
    }

    .fa2-summary-card {
        min-width: 0;
        padding: 1.08rem;
        border-radius: 1rem;
    }

    .fa2-summary-card--income {
        background: linear-gradient(135deg, var(--fa2-green-soft), var(--fa2-surface) 72%);
    }

    .fa2-summary-card--expense {
        background: linear-gradient(135deg, var(--fa2-red-soft), var(--fa2-surface) 72%);
    }

    .fa2-summary-card__label {
        display: block;
        color: var(--fa2-muted);
        font-size: .68rem;
        font-weight: 800;
        letter-spacing: .07em;
        text-transform: uppercase;
    }

    .fa2-summary-card__value {
        display: block;
        overflow: hidden;
        margin: .48rem 0 .28rem;
        color: var(--fa2-text);
        font-size: clamp(1.03rem, 1.8vw, 1.32rem);
        font-weight: 850;
        letter-spacing: -.035em;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .fa2-summary-card--income .fa2-summary-card__value {
        color: var(--fa2-green);
    }

    .fa2-summary-card--expense .fa2-summary-card__value {
        color: var(--fa2-red);
    }

    .fa2-summary-card__meta {
        display: block;
        color: var(--fa2-muted);
        font-size: .72rem;
        font-weight: 600;
    }

    .fa2-card {
        overflow: hidden;
        border-radius: 1.2rem;
    }

    .fa2-card__header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 1rem;
        padding: 1.35rem 1.4rem 1.15rem;
    }

    .fa2-card__title {
        font-size: 1.12rem;
        font-weight: 850;
        line-height: 1.25;
    }

    .fa2-page-badge {
        display: inline-flex;
        align-items: center;
        gap: .45rem;
        flex: 0 0 auto;
        padding: .54rem .7rem;
        border-radius: 999px;
        background: var(--fa2-primary-soft);
        color: var(--fa2-primary);
        font-size: .7rem;
        font-weight: 800;
        white-space: nowrap;
    }

    .fa2-page-badge i {
        width: .42rem;
        height: .42rem;
        border-radius: 50%;
        background: currentColor;
        opacity: .75;
    }

    .fa2-table-scroll {
        overflow-x: auto;
        border-top: 1px solid var(--fa2-border);
    }

    .fa2-table {
        width: 100%;
        min-width: 880px;
        border-collapse: collapse;
        color: var(--fa2-text);
        font-size: .79rem;
    }

    .fa2-table th,
    .fa2-table td {
        padding: .92rem 1.05rem;
        border-bottom: 1px solid var(--fa2-border);
        text-align: left;
        vertical-align: middle;
    }

    .fa2-table th {
        color: var(--fa2-faint);
        font-size: .64rem;
        font-weight: 850;
        letter-spacing: .07em;
        text-transform: uppercase;
        white-space: nowrap;
    }

    .fa2-table tbody tr {
        transition: background .16s ease;
    }

    .fa2-table tbody tr:hover {
        background: var(--fa2-surface-soft);
    }

    .fa2-table tbody tr:last-child td {
        border-bottom: 0;
    }

    .fa2-table__date strong,
    .fa2-table__date span {
        display: block;
        white-space: nowrap;
    }

    .fa2-table__date strong,
    .fa2-table__description strong {
        color: var(--fa2-text);
        font-weight: 800;
    }

    .fa2-table__date span {
        margin-top: .2rem;
        color: var(--fa2-muted);
        font-size: .69rem;
        font-weight: 650;
    }

    .fa2-table__description {
        min-width: 190px;
    }

    .fa2-table__reference {
        max-width: 180px;
        overflow: hidden;
        color: var(--fa2-muted);
        font-weight: 650;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .fa2-type {
        display: inline-flex;
        align-items: center;
        gap: .4rem;
        padding: .38rem .58rem;
        border-radius: 999px;
        font-size: .68rem;
        font-weight: 800;
        white-space: nowrap;
    }

    .fa2-type i {
        display: grid;
        width: 1rem;
        height: 1rem;
        place-items: center;
        border-radius: 50%;
        background: currentColor;
        color: #fff;
        font-size: .72rem;
        font-style: normal;
        font-weight: 900;
        line-height: 1;
    }

    .fa2-type--income {
        background: var(--fa2-green-soft);
        color: var(--fa2-green);
    }

    .fa2-type--expense {
        background: var(--fa2-red-soft);
        color: var(--fa2-red);
    }

    .fa2-method {
        display: inline-block;
        max-width: 145px;
        overflow: hidden;
        padding: .32rem .48rem;
        border: 1px solid var(--fa2-border);
        border-radius: .48rem;
        background: var(--fa2-surface-soft);
        color: var(--fa2-muted);
        font-size: .68rem;
        font-weight: 750;
        text-overflow: ellipsis;
        vertical-align: middle;
        white-space: nowrap;
    }

    .fa2-table__amount {
        color: var(--fa2-text);
        font-size: .82rem;
        font-weight: 850;
        text-align: right !important;
        white-space: nowrap;
    }

    .fa2-table__amount.is-income {
        color: var(--fa2-green);
    }

    .fa2-table__amount.is-expense {
        color: var(--fa2-red);
    }

    .fa2-empty {
        display: grid;
        justify-items: center;
        gap: .45rem;
        padding: 3rem 1rem;
        color: var(--fa2-muted);
        text-align: center;
    }

    .fa2-empty__icon {
        display: grid;
        width: 2.5rem;
        height: 2.5rem;
        place-items: center;
        border-radius: .8rem;
        background: var(--fa2-primary-soft);
        color: var(--fa2-primary);
        font-size: 1.2rem;
        font-weight: 900;
    }

    .fa2-empty strong {
        color: var(--fa2-text);
        font-size: .92rem;
        font-weight: 850;
    }

    .fa2-empty p {
        max-width: 28rem;
        margin: 0;
        font-size: .78rem;
        font-weight: 600;
        line-height: 1.5;
    }

    .fa2-pagination {
        padding: 1.1rem 1.4rem;
        border-top: 1px solid var(--fa2-border);
    }

    .fa2-pagination nav {
        display: flex;
        justify-content: flex-end;
    }

    .fa2-pagination nav > div:first-child {
        display: none;
    }

    .fa2-pagination nav > div:last-child {
        display: flex;
        align-items: center;
        gap: .25rem;
    }

    .fa2-pagination a,
    .fa2-pagination span {
        display: inline-flex;
        min-width: 2.15rem;
        height: 2.15rem;
        align-items: center;
        justify-content: center;
        border: 1px solid var(--fa2-border);
        border-radius: .65rem;
        background: var(--fa2-surface);
        color: var(--fa2-muted);
        font-size: .75rem;
        font-weight: 800;
        text-decoration: none;
    }

    .fa2-pagination a:hover {
        border-color: var(--fa2-primary);
        color: var(--fa2-primary);
    }

    .fa2-pagination [aria-current="page"] span,
    .fa2-pagination span[aria-current="page"] {
        border-color: var(--fa2-primary);
        background: var(--fa2-primary);
        color: #fff;
    }

    @media (max-width: 760px) {
        .fa2-hero {
            flex-direction: column;
            gap: .85rem;
        }

        .fa2-back-link {
            align-self: flex-start;
        }

        .fa2-summary {
            grid-template-columns: 1fr;
            gap: .75rem;
        }

        .fa2-card__header {
            flex-direction: column;
            gap: .8rem;
            padding: 1.1rem 1rem .95rem;
        }

        .fa2-pagination {
            padding: 1rem;
        }

        .fa2-pagination nav {
            justify-content: flex-start;
            overflow-x: auto;
            padding-bottom: .15rem;
        }
    }
    /* Ringkasan aktivitas keuangan: satu card */
    .fa2-summary--compact {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 0;
        overflow: hidden;
        margin-bottom: 1.25rem;
        border: 1px solid var(--fa2-border);
        border-radius: 1.1rem;
        background: var(--fa2-surface);
        box-shadow: var(--fa2-shadow);
    }

    .fa2-summary--compact .fa2-summary__item {
        min-width: 0;
        padding: 1.05rem 1.15rem;
        border-right: 1px solid var(--fa2-border);
    }

    .fa2-summary--compact .fa2-summary__item:last-child {
        border-right: 0;
    }

    .fa2-summary__label {
        display: block;
        color: var(--fa2-muted);
        font-size: .66rem;
        font-weight: 800;
        letter-spacing: .07em;
        text-transform: uppercase;
    }

    .fa2-summary__value {
        display: block;
        overflow: hidden;
        margin: .45rem 0 .25rem;
        color: var(--fa2-text);
        font-size: clamp(1rem, 1.7vw, 1.28rem);
        font-weight: 850;
        letter-spacing: -.035em;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .fa2-summary__meta {
        display: block;
        overflow: hidden;
        color: var(--fa2-muted);
        font-size: .71rem;
        font-weight: 600;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .fa2-summary__item--income {
        background: linear-gradient(135deg, var(--fa2-green-soft), transparent 78%);
    }

    .fa2-summary__item--income .fa2-summary__value {
        color: var(--fa2-green);
    }

    .fa2-summary__item--expense {
        background: linear-gradient(135deg, var(--fa2-red-soft), transparent 78%);
    }

    .fa2-summary__item--expense .fa2-summary__value {
        color: var(--fa2-red);
    }

    @media (max-width: 760px) {
        .fa2-summary--compact {
            grid-template-columns: 1fr;
        }

        .fa2-summary--compact .fa2-summary__item {
            border-right: 0;
            border-bottom: 1px solid var(--fa2-border);
        }

        .fa2-summary--compact .fa2-summary__item:last-child {
            border-bottom: 0;
        }
    }
    /* Refinement: compact finance summary, softer and simpler */
    .fa2-summary--compact {
        gap: 0;
        margin-bottom: 1rem;
        border-color: color-mix(in srgb, var(--fa2-border) 82%, transparent);
        border-radius: .9rem;
        background: var(--fa2-surface-soft);
        box-shadow: none;
    }

    .fa2-summary--compact .fa2-summary__item {
        padding: .85rem 1rem;
        border-right-color: color-mix(in srgb, var(--fa2-border) 78%, transparent);
        background: transparent;
    }

    .fa2-summary__label {
        font-size: .62rem;
        font-weight: 750;
        letter-spacing: .055em;
    }

    .fa2-summary__value {
        margin: .32rem 0 .12rem;
        font-size: clamp(.95rem, 1.5vw, 1.12rem);
        letter-spacing: -.025em;
    }

    .fa2-summary__meta {
        font-size: .67rem;
        font-weight: 550;
    }

    .fa2-summary__item--income,
    .fa2-summary__item--expense {
        background: transparent;
    }

    .fa2-summary__item--income .fa2-summary__value {
        color: var(--fa2-green);
    }

    .fa2-summary__item--expense .fa2-summary__value {
        color: var(--fa2-red);
    }

    @media (max-width: 760px) {
        .fa2-summary--compact {
            margin-bottom: .9rem;
        }

        .fa2-summary--compact .fa2-summary__item {
            padding: .78rem .9rem;
            border-bottom-color: color-mix(in srgb, var(--fa2-border) 78%, transparent);
        }
    }
    /* Finance activity: minimal information hierarchy */
    .fa2-hero--minimal {
        align-items: center;
        margin-bottom: 1rem;
    }

    .fa2-hero--minimal .fa2-hero__title {
        font-size: clamp(1.3rem, 2.2vw, 1.65rem);
    }

    .fa2-summary--minimal {
        grid-template-columns: repeat(2, minmax(0, 1fr));
        margin-bottom: 1rem;
    }

    .fa2-summary--minimal .fa2-summary__item {
        padding: .9rem 1.05rem;
    }

    .fa2-summary--minimal .fa2-summary__value {
        margin: 0 0 .3rem;
        font-size: clamp(1.05rem, 1.8vw, 1.25rem);
    }

    .fa2-summary--minimal .fa2-summary__label {
        font-size: .67rem;
        letter-spacing: .03em;
        text-transform: none;
    }

    .fa2-summary--minimal .fa2-summary__item--income,
    .fa2-summary--minimal .fa2-summary__item--expense {
        background: transparent;
    }

    @media (max-width: 560px) {
        .fa2-summary--minimal {
            grid-template-columns: 1fr;
        }
    }




</style>
