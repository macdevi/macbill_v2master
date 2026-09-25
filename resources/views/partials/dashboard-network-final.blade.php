<style>
    /* NETWORK-DASHBOARD-FINAL */
    .db2-network {
        overflow: hidden;
        border-color: #d9e8ed;
        background: #fafdff;
        box-shadow: 0 16px 30px -16px rgba(15, 23, 42, .17), 0 5px 12px -8px rgba(15, 23, 42, .06);
    }

    .db2-network .db2-network-v2__header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: .8rem;
        margin: 0 0 1rem;
        padding: 0;
        border: 0;
        background: transparent;
    }

    .db2-network .db2-network-v2__eyebrow {
        margin: 0;
        color: #64748b;
        font-size: .58rem;
        font-weight: 850;
        letter-spacing: .1em;
        line-height: 1;
    }

    .db2-network .db2-network-v2__title {
        margin: .32rem 0 0;
        color: #172033;
        font-size: .98rem;
        font-weight: 850;
        letter-spacing: -.025em;
        line-height: 1.2;
    }

    .db2-network .db2-network-v2__live {
        display: inline-flex;
        align-items: center;
        gap: .3rem;
        flex: 0 0 auto;
        padding: .32rem .46rem;
        border-radius: 999px;
        background: #ecfdf5;
        color: #15803d;
        font-size: .55rem;
        font-weight: 850;
        line-height: 1;
    }

    .db2-network .db2-network-v2__live i {
        width: .35rem;
        height: .35rem;
        border-radius: 50%;
        background: #22c55e;
        box-shadow: 0 0 0 3px rgba(34, 197, 94, .14);
    }

    .db2-network .db2-network-v2__router {
        display: flex;
        align-items: center;
        gap: .58rem;
        margin: 0 0 .82rem;
    }

    .db2-network .db2-network-v2__router-icon {
        display: grid;
        width: 2.35rem;
        height: 2.35rem;
        flex: 0 0 2.35rem;
        place-items: center;
        padding: .46rem;
        border-radius: 50%;
        background: #e0f2fe;
    }

    .db2-network .db2-network-v2__router-icon img {
        display: block;
        width: 100%;
        height: 100%;
        object-fit: contain;
    }

    .db2-network .db2-network-v2__router-copy {
        min-width: 0;
    }

    .db2-network .db2-network-v2__router-copy strong {
        display: block;
        overflow: hidden;
        color: #1e293b;
        font-size: .72rem;
        font-weight: 850;
        line-height: 1.2;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .db2-network .db2-network-v2__router-copy span {
        display: block;
        overflow: hidden;
        margin-top: .16rem;
        color: #0284c7;
        font-size: .56rem;
        font-weight: 750;
        line-height: 1.2;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .db2-network .db2-network-v2__router-state {
        margin-left: auto;
        color: #16a34a;
        font-size: .58rem;
        font-weight: 850;
        white-space: nowrap;
    }

    .db2-network .db2-network-v2__router-state.is-disconnected {
        color: #e11d48;
    }

    .db2-network .db2-network-v2__telemetry {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        padding: .72rem 0;
        border-top: 1px solid #eaf2f5;
        border-bottom: 1px solid #eaf2f5;
    }

    .db2-network .db2-network-v2__metric {
        min-width: 0;
        padding: 0 .5rem;
        border-left: 1px solid #eaf2f5;
    }

    .db2-network .db2-network-v2__metric:first-child {
        padding-left: 0;
        border-left: 0;
    }

    .db2-network .db2-network-v2__metric:last-child {
        padding-right: 0;
    }

    .db2-network .db2-network-v2__metric-label {
        display: block;
        color: #94a3b8;
        font-size: .5rem;
        font-weight: 850;
        letter-spacing: .07em;
        line-height: 1;
        text-transform: uppercase;
    }

    .db2-network .db2-network-v2__metric-value {
        display: block;
        overflow: hidden;
        margin-top: .3rem;
        color: #1e293b;
        font-size: .78rem;
        font-weight: 850;
        letter-spacing: -.035em;
        line-height: 1.1;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .db2-network .db2-network-v2__metric-value.is-good {
        color: #16a34a;
    }

    .db2-network .db2-network-v2__cpu-bar {
        height: .2rem;
        margin-top: .38rem;
        overflow: hidden;
        border-radius: 999px;
        background: #dbeafe;
    }

    .db2-network .db2-network-v2__cpu-bar i {
        display: block;
        height: 100%;
        border-radius: inherit;
        background: #0ea5e9;
    }

    .db2-network .db2-network-v2__customer-heading {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: .75rem;
        margin: .95rem 0 .65rem;
    }

    .db2-network .db2-network-v2__customer-heading strong {
        color: #334155;
        font-size: .6rem;
        font-weight: 850;
        letter-spacing: .035em;
    }

    .db2-network .db2-network-v2__customer-heading span {
        color: #94a3b8;
        font-size: .53rem;
        font-weight: 700;
        white-space: nowrap;
    }

    .db2-network .db2-network-v2__customers {
        display: grid;
        grid-template-columns: 6.55rem minmax(0, 1fr);
        align-items: center;
        gap: .85rem;
        padding: .72rem;
        border: 1px solid #e7eff3;
        border-radius: .95rem;
        background: #ffffff;
    }

    .db2-network .db2-network-v2__donut {
        position: relative;
        display: grid;
        width: 6.55rem;
        height: 6.55rem;
        place-items: center;
        padding: .42rem;
        border-radius: 50%;
        box-shadow: inset 0 0 0 1px rgba(15, 23, 42, .04);
    }

    .db2-network .db2-network-v2__donut-inner {
        display: flex;
        width: 100%;
        height: 100%;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        background: #ffffff;
        text-align: center;
    }

    .db2-network .db2-network-v2__donut-inner strong {
        color: #1e293b;
        font-size: 1.05rem;
        font-weight: 850;
        letter-spacing: -.06em;
        line-height: 1;
    }

    .db2-network .db2-network-v2__donut-inner span {
        margin-top: .2rem;
        color: #94a3b8;
        font-size: .48rem;
        font-weight: 750;
        line-height: 1.1;
    }

    .db2-network .db2-network-v2__status-list {
        display: grid;
        gap: 0;
    }

    .db2-network .db2-network-v2__status {
        display: grid;
        grid-template-columns: .55rem minmax(0, 1fr) auto auto;
        align-items: center;
        gap: .42rem;
        width: 100%;
        padding: .44rem 0;
        border: 0;
        border-bottom: 1px solid #f1f5f9;
        background: transparent;
        color: inherit;
        cursor: pointer;
        font: inherit;
        text-align: left;
        transition: color .16s ease;
    }

    .db2-network .db2-network-v2__status:last-child {
        border-bottom: 0;
    }

    .db2-network .db2-network-v2__status:hover .db2-network-v2__status-label,
    .db2-network .db2-network-v2__status:hover .db2-network-v2__status-action {
        color: #2563eb;
    }

    .db2-network .db2-network-v2__status:focus-visible {
        outline: 3px solid rgba(14, 165, 233, .28);
        outline-offset: 3px;
        border-radius: .35rem;
    }

    .db2-network .db2-network-v2__status-dot {
        width: .43rem;
        height: .43rem;
        border-radius: 50%;
    }

    .db2-network .db2-network-v2__status--online .db2-network-v2__status-dot {
        background: #22c55e;
        box-shadow: 0 0 0 3px rgba(34, 197, 94, .12);
    }

    .db2-network .db2-network-v2__status--offline .db2-network-v2__status-dot {
        background: #f43f5e;
        box-shadow: 0 0 0 3px rgba(244, 63, 94, .12);
    }

    .db2-network .db2-network-v2__status--isolated .db2-network-v2__status-dot {
        background: #f59e0b;
        box-shadow: 0 0 0 3px rgba(245, 158, 11, .12);
    }

    .db2-network .db2-network-v2__status-label {
        overflow: hidden;
        color: #475569;
        font-size: .62rem;
        font-weight: 800;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .db2-network .db2-network-v2__status-count {
        color: #1e293b;
        font-size: .68rem;
        font-weight: 850;
        letter-spacing: -.02em;
        white-space: nowrap;
    }

    .db2-network .db2-network-v2__status-action {
        color: #2563eb;
        font-size: .55rem;
        font-weight: 850;
        white-space: nowrap;
    }

    .db2-network .db2-network-v2__updated {
        margin: .72rem 0 0;
        color: #94a3b8;
        font-size: .53rem;
        font-weight: 650;
    }

    .db2-network .db2-network-v2__updated strong {
        color: #64748b;
        font-weight: 800;
    }

    .dark .db2-network {
        border-color: #263e53;
        background: #122333;
    }

    .dark .db2-network .db2-network-v2__title,
    .dark .db2-network .db2-network-v2__router-copy strong,
    .dark .db2-network .db2-network-v2__metric-value,
    .dark .db2-network .db2-network-v2__donut-inner strong,
    .dark .db2-network .db2-network-v2__status-count {
        color: #e5edf9;
    }

    .dark .db2-network .db2-network-v2__customers,
    .dark .db2-network .db2-network-v2__donut-inner {
        border-color: #263e53;
        background: #172b3d;
    }

    .dark .db2-network .db2-network-v2__telemetry,
    .dark .db2-network .db2-network-v2__metric {
        border-color: #263e53;
    }

    .dark .db2-network .db2-network-v2__status {
        border-bottom-color: #263e53;
    }

    .dark .db2-network .db2-network-v2__status-label {
        color: #c7d5e7;
    }

    .dark .db2-network .db2-network-v2__customer-heading strong {
        color: #dce7f5;
    }

    @media (max-width: 640px) {
        .db2-network .db2-network-v2__customers {
            grid-template-columns: 6.1rem minmax(0, 1fr);
            gap: .65rem;
            padding: .62rem;
        }

        .db2-network .db2-network-v2__donut {
            width: 6.1rem;
            height: 6.1rem;
        }

        .db2-network .db2-network-v2__status {
            grid-template-columns: .5rem minmax(0, 1fr) auto auto;
            gap: .34rem;
        }

        .db2-network .db2-network-v2__status-label {
            font-size: .59rem;
        }

        .db2-network .db2-network-v2__status-action {
            font-size: .52rem;
        }
    }
</style>

<style>
    /* NETWORK-CPU-BAR-COMPACT */
    .db2-network .db2-network-v2__cpu-bar {
        display: block;
        width: 2.55rem;
        height: .18rem;
        margin-top: .32rem;
        overflow: hidden;
        border-radius: 999px;
        background: #dbeafe;
    }

    .db2-network .db2-network-v2__cpu-bar i {
        display: block;
        min-width: .18rem;
        height: 100%;
        border-radius: inherit;
        background: linear-gradient(90deg, #38bdf8, #0ea5e9);
    }

    .dark .db2-network .db2-network-v2__cpu-bar {
        background: rgba(96, 165, 250, .20);
    }
</style>

<style>
    /* NETWORK-DONUT-STATUS-SCALE */
    .db2-network .db2-network-v2__customers {
        grid-template-columns: 7.6rem minmax(0, 1fr);
        gap: 1rem;
        padding: .85rem;
    }

    .db2-network .db2-network-v2__donut {
        width: 7.6rem;
        height: 7.6rem;
        padding: .5rem;
    }

    .db2-network .db2-network-v2__donut-inner strong {
        font-size: 1.28rem;
    }

    .db2-network .db2-network-v2__donut-inner span {
        margin-top: .24rem;
        font-size: .54rem;
    }

    .db2-network .db2-network-v2__status {
        grid-template-columns: .62rem minmax(0, 1fr) auto auto;
        gap: .5rem;
        padding: .56rem 0;
    }

    .db2-network .db2-network-v2__status-dot {
        width: .5rem;
        height: .5rem;
    }

    .db2-network .db2-network-v2__status-label {
        font-size: .7rem;
    }

    .db2-network .db2-network-v2__status-count {
        font-size: .76rem;
    }

    .db2-network .db2-network-v2__status-action {
        font-size: .62rem;
    }

    @media (max-width: 640px) {
        .db2-network .db2-network-v2__customers {
            grid-template-columns: 6.9rem minmax(0, 1fr);
            gap: .72rem;
            padding: .72rem;
        }

        .db2-network .db2-network-v2__donut {
            width: 6.9rem;
            height: 6.9rem;
        }

        .db2-network .db2-network-v2__status {
            grid-template-columns: .55rem minmax(0, 1fr) auto auto;
            gap: .4rem;
            padding: .5rem 0;
        }

        .db2-network .db2-network-v2__status-label {
            font-size: .65rem;
        }

        .db2-network .db2-network-v2__status-count {
            font-size: .71rem;
        }

        .db2-network .db2-network-v2__status-action {
            font-size: .58rem;
        }
    }
</style>

<style>
    /* NETWORK-DONUT-STATUS-SCALE-XL */
    .db2-network .db2-network-v2__customers {
        grid-template-columns: 8.8rem minmax(0, 1fr);
        gap: 1.15rem;
        padding: 1rem;
    }

    .db2-network .db2-network-v2__donut {
        width: 8.8rem;
        height: 8.8rem;
        padding: .58rem;
    }

    .db2-network .db2-network-v2__donut-inner strong {
        font-size: 1.5rem;
    }

    .db2-network .db2-network-v2__donut-inner span {
        margin-top: .28rem;
        font-size: .6rem;
    }

    .db2-network .db2-network-v2__status {
        grid-template-columns: .68rem minmax(0, 1fr) auto auto;
        gap: .58rem;
        padding: .68rem 0;
    }

    .db2-network .db2-network-v2__status-dot {
        width: .56rem;
        height: .56rem;
    }

    .db2-network .db2-network-v2__status-label {
        font-size: .78rem;
    }

    .db2-network .db2-network-v2__status-count {
        font-size: .84rem;
    }

    .db2-network .db2-network-v2__status-action {
        font-size: .68rem;
    }

    @media (max-width: 640px) {
        .db2-network .db2-network-v2__customers {
            grid-template-columns: 7.8rem minmax(0, 1fr);
            gap: .78rem;
            padding: .8rem;
        }

        .db2-network .db2-network-v2__donut {
            width: 7.8rem;
            height: 7.8rem;
        }

        .db2-network .db2-network-v2__donut-inner strong {
            font-size: 1.34rem;
        }

        .db2-network .db2-network-v2__donut-inner span {
            font-size: .56rem;
        }

        .db2-network .db2-network-v2__status {
            grid-template-columns: .6rem minmax(0, 1fr) auto auto;
            gap: .45rem;
            padding: .57rem 0;
        }

        .db2-network .db2-network-v2__status-label {
            font-size: .69rem;
        }

        .db2-network .db2-network-v2__status-count {
            font-size: .75rem;
        }

        .db2-network .db2-network-v2__status-action {
            font-size: .61rem;
        }
    }
</style>

<style>

<style>
/* DONUT-LIVE-ANIMATION */
.db2-network .db2-network-v2__donut {
    position: relative;
    overflow: hidden;
    animation: donut-enter .8s cubic-bezier(.16, 1, .3, 1) both;
}

.db2-network .db2-network-v2__donut::after {
    position: absolute;
    inset: -45%;
    z-index: 0;
    content: "";
    pointer-events: none;
    background: conic-gradient(
        from 0deg,
        transparent 0deg,
        transparent 300deg,
        rgba(255, 255, 255, .40) 336deg,
        transparent 360deg
    );
    animation: donut-glow 5.5s linear infinite;
}

.db2-network .db2-network-v2__donut-inner {
    position: relative;
    z-index: 1;
    animation: donut-content-enter .95s .1s cubic-bezier(.16, 1, .3, 1) both;
}

@keyframes donut-enter {
    0% {
        opacity: 0;
        transform: scale(.72) rotate(-10deg);
    }

    68% {
        opacity: 1;
        transform: scale(1.045) rotate(1deg);
    }

    100% {
        opacity: 1;
        transform: scale(1) rotate(0);
    }
}

@keyframes donut-content-enter {
    0% {
        opacity: 0;
        transform: scale(.78) translateY(6px);
    }

    100% {
        opacity: 1;
        transform: scale(1) translateY(0);
    }
}

@keyframes donut-glow {
    to {
        transform: rotate(360deg);
    }
}

@media (prefers-reduced-motion: reduce) {
    .db2-network .db2-network-v2__donut,
    .db2-network .db2-network-v2__donut::after,
    .db2-network .db2-network-v2__donut-inner {
        animation: none;
    }
}
</style>

<style>
/* DONUT-GLOW-CLIPPED-BRIGHT */
.db2-network .db2-network-v2__donut {
    position: relative;
    overflow: hidden;
    isolation: isolate;
    border-radius: 50%;
}

.db2-network .db2-network-v2__donut::after {
    position: absolute;
    inset: 0;
    z-index: 0;
    content: "";
    pointer-events: none;
    border-radius: inherit;
    background: conic-gradient(
        from 0deg,
        transparent 0deg,
        transparent 292deg,
        rgba(255, 255, 255, .98) 322deg,
        rgba(255, 255, 255, .55) 338deg,
        transparent 360deg
    );
    mix-blend-mode: screen;
    opacity: .95;
    animation: donut-glow-clipped 4.8s linear infinite;
}

.db2-network .db2-network-v2__donut-inner {
    position: relative;
    z-index: 1;
}

@keyframes donut-glow-clipped {
    to {
        transform: rotate(360deg);
    }
}

@media (prefers-reduced-motion: reduce) {
    .db2-network .db2-network-v2__donut::after {
        animation: none;
    }
}
</style>
