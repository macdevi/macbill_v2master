# MacBilling V2 Master

Aplikasi billing dan operasional RT/RW Net berbasis Laravel 13 untuk manajemen pelanggan, tagihan, pembayaran, pengeluaran, area operasional, dan integrasi MikroTik PPPoE.

Repository ini dapat digunakan untuk:

- Deploy aplikasi baru ke VPS kosong dengan database baru.
- Pengembangan dan deployment lanjutan dari branch `main`.
- Menjalankan sistem billing dengan akses berbasis role dan area operasional.

## Fitur Utama

- Login Modern SaaS bertema jaringan, dengan nama usaha dinamis dari Pengaturan Billing.
- Role pengguna: `super_admin`, `admin`, dan `kasir`.
- Area Operasional untuk pembatasan data pelanggan, tagihan, pembayaran, dan pengeluaran.
- Manajemen pelanggan, paket internet, ODP, router, dan PPPoE.
- Integrasi MikroTik RouterOS API.
- Tagihan otomatis dan tagihan manual.
- Pembayaran penuh, pembayaran parsial, dan titip saldo.
- Pengeluaran dengan bukti/riwayat serta aksi edit dan pembatalan.
- Dashboard monitoring pelanggan online/offline dan ringkasan finansial.
- Import/export data pelanggan Excel.
- Dukungan light/dark theme pada panel aplikasi.

## Teknologi

- Laravel 13
- PHP 8.3 atau lebih baru
- SQLite sebagai konfigurasi default
- Tailwind CSS dan Alpine.js
- MikroTik RouterOS API melalui PHP sockets

## Requirement

- Ubuntu 24.04 LTS atau distribusi Linux setara
- PHP 8.3+ beserta extension: `sqlite3`, `mbstring`, `xml`, `curl`, `zip`, `bcmath`, `intl`, `sockets`
- Composer
- Nginx
- Node.js 22+ dan npm untuk build frontend
- Git
- SQLite 3

## Fresh Install

Panduan deployment lengkap dari GitHub ke VPS kosong tersedia di:

```text
DEPLOY.md
```

Fresh install membuat database baru. Data customer, invoice, pembayaran, pengeluaran, router, dan setting dari server lama tidak ikut dari GitHub.

Setelah menjalankan migration dan seed, akun bootstrap awal adalah:

| Field | Nilai |
|---|---|
| Username | `admin` |
| Email | `admin@macbilling.local` |
| Password | `ChangeMe123!` |
| Role | `super_admin` |
| Timezone | `Asia/Jakarta` |

> **Penting:** Ganti email dan password bootstrap segera setelah login pertama. Jangan gunakan akun bootstrap sebagai akun operasional harian.

## Keamanan

- Jangan commit `.env`, database SQLite, `storage/`, `vendor/`, atau file backup lokal.
- File backup dengan pola `*.bak_*`, `*before_*`, dan `*.backup` diabaikan oleh Git.
- Gunakan deploy key read-only untuk clone repository private di VPS.
- Batasi akses API MikroTik hanya dari IP VPS dan gunakan user API dengan hak minimum.
- Gunakan HTTPS di production.
- Jalankan `APP_DEBUG=false` di production.

## Backup Production

GitHub adalah backup source code, bukan backup data operasional. Untuk recovery penuh, backup secara terpisah:

1. Source code GitHub.
2. File `.env`.
3. Database SQLite atau dump MariaDB/MySQL.
4. `storage/app` untuk dokumen/bukti upload.

Lihat `DEPLOY.md` untuk detail deployment dan pemulihan.
