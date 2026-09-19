# 🛍️ TokoKu — WordPress WhatsApp Store Theme

**Versi Aktif: v2.3.5**

Tema WordPress ringan untuk toko online dengan sistem pemesanan via WhatsApp. Tanpa WooCommerce, cepat, dan responsif di semua perangkat.

![Version](https://img.shields.io/badge/versi-2.3.5-blue) ![PHP](https://img.shields.io/badge/PHP-7.4%2B-purple) ![WP](https://img.shields.io/badge/WordPress-5.9%2B-blue) ![License](https://img.shields.io/badge/lisensi-GPL--2.0-green)

---

## 🚀 Fitur Utama

| Fitur | Keterangan |
|---|---|
| 🛒 Katalog Produk | Grid produk responsif dengan filter kategori & pencarian live |
| 💬 Pesan via WhatsApp | Tombol order langsung ke WhatsApp tanpa plugin tambahan |
| 🌙 Dark / Light Mode | Toggle mode gelap/terang dengan animasi halus |
| 📱 Mobile-First | Bottom navigation, safe area support, touch-friendly |
| 🔧 Admin Panel | Pengaturan lengkap: warna, font, slider, FAQ, testimoni |
| 📝 Blog Sistem | Arsip, artikel tunggal, komentar, dan social share |
| 🔄 Auto Updater | Pembaruan satu-klik langsung dari GitHub Releases |
| 🔒 Keamanan | Nonce verification, capability checks, sanitasi input |

---

## 📋 Changelog

### v2.3.5 — Perbaikan Theme Updater *(Terbaru)*
- Fix: `WP_Filesystem()` gagal di server lokal → tambah `filesystem_method=direct`
- Fix: Versi tidak berubah setelah update → tambah `opcache_reset()` pasca instalasi
- Fix: GitHub API checker pakai cache lama → tambah header & cache-busting
- Fix: Fallback Tags API pakai `zipball_url` yang benar dari objek tag
- Improve: Pesan error lebih informatif + link download manual ke GitHub Releases

### v2.3.4 — Mobile-First Button System
- Semua tombol: `touch-action: manipulation`, `user-select: none`, no tap highlight
- `min-height: 44–52px` pada semua CTA untuk memenuhi standar WCAG 2.5.5
- Font-size fluid dengan `clamp()` di semua breakpoint
- Bottom nav: `env(safe-area-inset-bottom)` untuk iPhone notch / Dynamic Island
- `@media (hover: none)`: hapus sticky hover effect di touchscreen
- Breakpoint ≤400px: marketplace button 1 kolom, ukuran kompak

### v2.3.3 — Perbaikan Ikon Admin
- Fix alignment ikon semua tombol admin (baseline shift WP Core)
- Bungkus teks tombol dalam `<span class="tokoku-btn-text">` eliminasi rogue whitespace
- Ikon 15–18px presisi: Simpan (18px), Upload (16px), Hapus (15px)
- Fix: `.dashicons:before` → `line-height: 1; display: block`

### v2.3.2 — Overhaul Tombol Admin (SaaS Design)
- Tombol Simpan/Perbarui: gradasi biru, hover lift, shadow glow
- Tombol Tambah/Repeater: desain pill modern, aksen biru lembut
- Tombol Upload Media & Hapus: ikon SVG presisi, animasi transisi halus
- Tab vertikal repeater (Testimoni, Logo, Slider) responsif penuh

### v2.3.1 — Perbaikan Dasbor & Updater
- Ikon SVG di semua menu tab admin (ganti Dashicons)
- Tombol "Perbarui Pengaturan" di setiap tab panel
- Toast notification saat pengaturan berhasil disimpan
- Theme updater: semver comparison, one-click update, reinstall paksa

### v2.3.0 — Redesain Admin & Mobile Nav
- Header: tinggi 82px desktop, 60px mobile, lebih proporsional
- Admin panel redesain: SaaS glassmorphism, sidebar vertikal, live badge
- Widget toko di Beranda Dashboard WordPress
- Bottom navigation mobile ergonomis + tombol WhatsApp cepat
- Blog sistem lengkap: arsip, artikel, komentar, social share SVG

### v2.2.x — Perbaikan & Optimasi
| Versi | Ringkasan |
|---|---|
| v2.2.6 | Fix gap header di halaman pencarian & arsip |
| v2.2.5 | Tag produk dipindah ke bawah deskripsi |
| v2.2.4 | Fix halaman arsip tag produk |
| v2.2.3 | Fix AJAX search error koneksi |
| v2.2.2 | FAQ desktop full-width 2 kolom |
| v2.2.1 | Hapus glassmorphism hasil pencarian AJAX |
| v2.2.0 | Glassmorphism global, FAQ redesain, tipografi mobile |

### v2.1.0 — Perbaikan UI & Footer
- Admin panel: `box-sizing: border-box` semua input
- Footer: warna ikon sinkron dengan `var(--primary)`

### v2.0.0 — Keamanan & Dokumentasi
- Audit keamanan penuh: nonce, capability check, sanitasi
- Dokumentasi kode lengkap bahasa Indonesia
- Hapus sistem lisensi, tambah marketplace integration

### v1.x — Fondasi & Fitur Awal
| Versi | Ringkasan |
|---|---|
| v1.9.0 | Redesain kartu artikel & footer 4 kolom |
| v1.8.0 | AJAX search + pencarian SKU produk |
| v1.7.x | Scroll to top, PWA icon dinamis, bottom nav mobile |
| v1.6.7 | Security hardening `ABSPATH` check |

---

## ⚙️ Instalasi

1. Upload folder `tokoku` ke `/wp-content/themes/`
2. Aktifkan tema di **Tampilan → Tema**
3. Atur di **Pengaturan → TokoKu**

## 🔄 Pembaruan Otomatis

Buka **Pengaturan → TokoKu → Tab "Pembaruan Tema"** → klik **"Cek Pembaruan Sekarang"**.

---

## 👨‍💻 Developer

Dibuat dengan ❤️ oleh **m.alfiandiismet**

## 📄 Lisensi

GPL-2.0 — Bebas digunakan untuk keperluan pribadi & komersial. Dilarang menghapus kredit developer tanpa izin.
