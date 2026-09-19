# 🛍️ TokoKu - Premium WhatsApp Store Theme (v2.3.3)

**TokoKu** adalah tema WordPress premium yang dirancang khusus untuk toko online minimalis dengan sistem pemesanan langsung melalui WhatsApp. Tema ini menghilangkan kerumitan WooCommerce, memberikan pengalaman belanja yang cepat, ringan, dan sangat intuitif baik di perangkat mobile maupun desktop.

---

## ✨ Fitur Terbaru v2.3.3
*   **Presisi Tata Letak & Alignment Semua Ikon Button Admin / Dasbor (Zero Drift & Optical Centering)**:
    *   **Solusi Baseline Shift WP Core**: Mengatasi styling bawaan WordPress Core (`.wp-core-ui .button .dashicons`) yang memiliki `vertical-align: text-top;` sehingga ikon sering tampak miring atau tidak sejajar dengan sumbu tengah teks tombol.
    *   **Pembersihan Rogue Whitespace DOM**: Mengeliminasi node spasi teks bebas antar-elemen dan membungkus seluruh label teks tombol ke dalam `<span class="tokoku-btn-text">`, menghasilkan kalkulasi `gap` flexbox yang simetris dan presisi di semua browser.
    *   **Proporsi & Ukuran Ikon Harmonis**:
        *   **Tombol Simpan / Update & Cek Pembaruan**: Ikon 18px bergaris tegas dengan gap 8px dan border radius pill premium.
        *   **Tombol Tambah Unit & Upload Media**: Ikon 16px dengan gap 7px yang proporsional untuk pemilihan gambar, logo partner, slider banner, dan meta kategori produk.
        *   **Tombol Hapus / Reset**: Ikon 15px dengan gap 6px dalam container soft-danger yang rapi.
        *   **Tombol Widget Dasbor Beranda**: Ikon 16px sejajar vertikal dengan teks aksi cepat ("Tambah Produk Baru", "Pengaturan TokoKu", "Kunjungi Website").
    *   **Reset Pseudo-Element `.dashicons:before`**: Mengatur `line-height: 1` dan `display: block` pada pseudo-elemen ikon Dashicons agar glyph berada tepat di tengah tanpa offset font descender.
    *   **Harmonisasi Tombol Dinamis JavaScript**: Pembaruan tombol aksi pada Theme Updater (*One-Click Update* dan *Reinstall*) dengan struktur kelas yang bersih dan bebas inline-margin bertabrakan.

---

## ✨ Fitur v2.3.2
*   **Overhaul Tampilan Semua Tombol Admin (Modern SaaS Button Design)**:
    *   **Tombol Simpan / Perbarui (`.tokoku-submit-update-btn`)**: Gradasi biru mewah dengan efek *hover lift*, *shadow glow*, dan status pemuatan dinamis.
    *   **Tombol Tambah Unit / Repeater (`.tokoku-btn-add`)**: Desain *pill* modern aksen biru lembut (`#eff6ff`) dengan border presisi dan ikon plus vektor pada penambahan Testimoni, Logo Partner, Banner Slider, dan Tanya Jawab (FAQ).
    *   **Tombol Upload & Pilih Media (`.tokoku-upload-btn`, `.tokoku-upload-btn-id`, `.tokoku-tax-upload-btn`)**: Desain SaaS profesional berikon upload dengan transisi halus saat dipilih.
    *   **Tombol Hapus & Reset (`.tokoku-remove-btn`, `.tokoku-tax-remove-btn`, `.tokoku-remove-faq`)**: Tampilan elegan dengan palet *danger red* lembut (`#fef2f2`) berikon trash yang aman dan intuitif.
*   **Perbaikan Layout Tab Vertikal Repeater (Testimoni, Logo Klien & Slider)**:
    *   Menambahkan styling lengkap sistem kontainer tab vertikal (`.tokoku-vtabs-container`) dengan navigasi tab kiri (*pill links*), indikator aktif gradasi biru, tombol hapus badge bulat (`.tokoku-remove-unit-v`), dan panel konten kanan beranimasi transisi halus.
    *   Responsif penuh pada layar kecil/tablet dengan beralih ke tab geser horizontal yang rapi.
*   **Penyempurnaan FAQ Repeater & Accordion**:
    *   Pembaruan header accordion dengan status *open/close* beraksen border biru dan chevron beranimasi.
    *   Tombol hapus item pertanyaan dengan styling modern pill danger.
*   **Peningkatan Event Delegation JavaScript**:
    *   Memperbaiki seluruh pemanggilan media uploader WordPress dan penambahan/penghapusan unit dinamis menggunakan *event delegation* (`$(document).on(...)`), memastikan semua elemen yang ditambahkan secara dinamis langsung berfungsi 100% tanpa error.
*   **Desain Kartu Impor & Ekspor**:
    *   Pembaruan visual kartu pencadangan data tema (*JSON backup*) dan data situs (*WordPress XML*) dengan ikon badge dan tombol aksi yang terpadu.

---

## ✨ Fitur v2.3.1
*   **Perbaikan Ikon Dasbor & Admin (Crisp SVG Icons)**: Menggantikan seluruh ikon menu tab pengaturan admin dan widget ringkasan toko dengan ikon SVG modern berbasis vektor (termasuk logo WhatsApp, Testimoni, SEO, Kategori, dan Produk) yang 100% presisi, tajam, dan tidak bergantung pada font Dashicons pihak ketiga yang rentan hilang/rusak.
*   **Penyempurnaan Tombol Update & Simpan Pengaturan**: 
    *   Menambahkan tombol **"Perbarui Pengaturan"** di bagian bawah setiap tab panel, sehingga admin dapat langsung menyimpan tanpa harus menggulir ke paling atas.
    *   Mempertahankan status tab aktif saat form disimpan, mencegah halaman melompat kembali ke tab General secara acak.
    *   Memberikan indikator loading dinamis saat tombol update ditekan ("Menyimpan Perubahan...").
    *   Menampilkan notifikasi pop-up melayang (*toast notice*) yang elegan ketika pengaturan berhasil disimpan.
*   **Pembaruan Sistem Theme Updater (GitHub Releases & Reinstall)**:
    *   Memperbaiki sistem perbandingan versi menggunakan logika semantic versioning (*semver*) yang akurat.
    *   Mendukung pembaruan otomatis satu-klik langsung dari rilis resmi GitHub, serta menambahkan opsi **"Instal Ulang / Paksa Perbarui Versi Ini"** untuk kemudahan pemeliharaan tema.
    *   Memperluas daftar host terverifikasi dan batas waktu pengunduhan paket pembaruan.
*   **Pembersihan Duplikasi DOM Admin**: Menghapus duplikasi ID `#tab-import-export` pada panel admin untuk integritas query JavaScript yang sempurna.

---

## ✨ Fitur v2.3.0
*   **Header Height & Proportional Enhancement**: Penyesuaian tinggi header desktop menjadi 82px (sticky: 70px) dan mobile menjadi 60px dengan ukuran logo yang lebih proporsional, lapang, dan berkelas.
*   **Modern Admin Dashboard & Settings Redesign**: Tampilan halaman pengaturan TokoKu dirancang ulang secara menyeluruh dengan estetika modern SaaS (glassmorphism sticky header, live badge status, sidebar vertikal yang rapi, dan kontrol input berkelas dengan visual focus ring).
*   **WordPress Dashboard TokoKu Widget**: Integrasi widget toko pintar langsung di Beranda Dashboard WordPress (`index.php`) yang menampilkan statistik langsung (Total Produk, Nomor WhatsApp Aktif, Kategori Produk, dan Artikel Blog) beserta tautan pintas pengaturan.
*   **Mobile Bottom Navigation & Centered Live Search**: Menu mobile kini terletak di bagian bawah layar yang ramah jempol (ergonomis) lengkap dengan tombol WhatsApp cepat, serta kotak pencarian live di header mobile dengan animasi transisi mulus dan ikon close centering.
*   **Dark & Light Mode Polish**: Perbaikan kontras dan konsistensi warna latar, border, dan teks di seluruh komponen tema saat berganti mode gelap/terang. Penghapusan outline kaku pada kotak input pencarian agar menyatu elegan dengan desain.
*   **Product Catalog & Related Products Overhaul**: Desain kartu produk yang lebih tajam dan modern dengan label diskon & kategori berbentuk rounded-pill, pembatasan 2 baris judul (*line-clamp*), tombol WhatsApp satu-klik, serta grid produk terkait yang konsisten.
*   **Full Blog System & Social Share SVG**: Redesain arsip dan artikel blog tunggal dengan tipografi modern, formulir komentar ("Leave a Reply") yang elegan, dan tombol "Bagikan Artikel" berikon SVG modern (WhatsApp, Facebook, Twitter/X, Telegram, Salin Tautan) dengan toast notification interaktif.

---

## ✨ Fitur v2.2.6
*   **Header Layout & Spacing Refinement**: Memperbaiki bug spasi kosong (gap) di bawah menu/header pada halaman pencarian dan arsip produk dengan menggunakan layout sticky header yang dinamis, serta mengatur offset Admin Bar WordPress secara tepat di semua ukuran layar (desktop, tablet, mobile).
*   **Dynamic View All Products Link**: Mengubah link "Lihat Semua Produk" pada pencarian AJAX desktop agar mengarah secara otomatis ke URL arsip produk (`/produk/`) sesuai dengan nama domain server yang aktif (lokal maupun production).

---

## ✨ Fitur Terbaru v2.2.5
*   **Product Tag Layout Reposition**: Memindahkan letak komponen Tag Produk dari dalam tabel Spesifikasi Produk ke bagian bawah Deskripsi Produk, memberikan tampilan akhir yang lebih rapi, modern, dan mudah dibaca oleh pengunjung.

---

## ✨ Fitur Terbaru v2.2.4
*   **Product Tag Archive Fix**: Memperbaiki tampilan halaman Arsip Tag Produk yang sebelumnya error/berantakan dengan menambahkan *template* `taxonomy-tag_produk.php` serta mengintegrasikan *body class* dan dukungan *product query* yang sama dengan arsip kategori.

---

## ✨ Fitur Terbaru v2.2.3
*   **AJAX Search Fix**: Memperbaiki isu "Terjadi kesalahan koneksi" pada pencarian live (AJAX) dengan menghapus pengecekan *nonce* yang sering bentrok dengan sistem *caching*, memastikan pencarian tetap responsif dan lancar bagi pengunjung publik.

---

## ✨ Fitur Terbaru v2.2.2
*   **Full-Width FAQ Desktop**: Mengubah tampilan FAQ desktop menjadi lebar penuh mengikuti kontainer utama (2 kolom) untuk visibilitas yang lebih maksimal.

---

## ✨ Fitur v2.2.1
*   **Search UI Refinement**: Penghapusan efek glassmorphism pada hasil pencarian AJAX (desktop & mobile) untuk meningkatkan keterbacaan teks dan performa navigasi.

---

## ✨ Fitur v2.2.0
*   **Global Glassmorphism Overhaul**: Implementasi efek *Smooth Blur* (kaca transparan) di seluruh bagian halaman depan (Produk, Artikel, Kategori, Header, dll) untuk tampilan premium kelas atas.
*   **FAQ Section Redesign**: Tampilan FAQ baru dengan latar belakang gradasi dinamis, efek akordeon yang diperbaiki (JavaScript), dan penggunaan ikon SVG modern yang elegan.
*   **Mobile Typography Optimization**: Penyesuaian ukuran font di seluruh tema khusus untuk tampilan mobile agar lebih proporsional, nyaman dibaca, dan menarik secara visual.
*   **Seamless Section Transition**: Penghapusan jarak dan garis pembatas antara FAQ dan Footer untuk menciptakan alur visual yang lebih mengalir dan modern.
*   **Testimonial Glassmorphism**: Pembaruan desain kartu ulasan klien dengan efek *glass-blur* transparan yang premium.

---

## ✨ Fitur v2.1.0
*   **Admin UI Optimization**: Implementasi `box-sizing: border-box` pada seluruh kolom input di admin panel dan meta box produk untuk mencegah tampilan meluap (*overflow*).
*   **Footer Icon Synchronization**: Sinkronisasi warna ikon footer (email, WhatsApp, jam operasional) secara otomatis mengikuti warna utama tema website (`var(--primary)`).
*   **Admin Panel Cleanup**: Penghapusan input "Tentang Kami" yang redundan di admin panel untuk antarmuka yang lebih bersih dan fokus pada menu dinamis.

---

## ✨ Fitur v2.0.0
*   **Full Codebase Documentation**: Seluruh file inti (core PHP) kini telah dilengkapi dengan dokumentasi dan komentar berbahasa Indonesia yang sangat komprehensif, memudahkan pengembangan dan modifikasi di masa mendatang.
*   **Security Audit & Hardening**: Peningkatan keamanan skala penuh. Telah diimplementasikan verifikasi *nonce* ketat, pengecekan hak akses (*capability checks*), dan sanitasi data di setiap form input dan pemrosesan AJAX.
*   **Marketplace Integration Overhaul**: Tata letak tombol marketplace (Shopee, Tokopedia, dll) dirombak ulang menjadi lebih bersih dengan desain premium tanpa ikon, serta teks yang tersusun rapi dan *centered*.
*   **License System Removal**: Sistem lisensi yang memberatkan telah dihapus sepenuhnya, menjadikan kode tema lebih bersih, ringan, dan bebas dari pembatasan.

---

## ✨ Fitur Terbaru v1.9.0
*   **Article Card Redesign**: Desain baru bergaya *magazine overlay* (*full background image* dengan gradasi transparan warna tema) untuk tampilan artikel terbaru yang lebih premium.
*   **Desktop Footer Overhaul**: Tata letak footer khusus desktop baru dengan sistem grid 4 kolom, dukungan pengaturan menu dinamis, dan integrasi admin panel untuk informasi kontak WhatsApp & jam operasional.

---

## ✨ Fitur Terbaru v1.8.0
*   **Enhanced AJAX Search**: Peningkatan sistem pencarian yang kini mendukung pencarian berdasarkan Kode Produk (SKU) secara akurat.
*   **Smart Search Empty State**: Hasil pencarian kini menampilkan produk terbaru secara otomatis saat kolom input kosong, memberikan pengalaman navigasi yang lebih baik di modal mobile.
*   **Fixed Price Logic**: Perbaikan logika pemanggilan harga (`tokoku_get_harga`) untuk memastikan konsistensi tampilan harga di hasil pencarian.
*   **Improved Search Footer**: Tautan "Lihat Semua" kini secara cerdas mengarahkan ke halaman pencarian lengkap dengan kata kunci yang tetap terjaga.

---

## ✨ Fitur v1.7.9
*   **Desktop Footer Layout**: Optimalisasi tata letak footer khusus untuk tampilan desktop agar lebih seimbang dan premium.
*   **Security Hardening**: Peningkatan lapisan keamanan pada header responsif dan sanitasi data input.

---

## ✨ Fitur v1.7.8
*   **Dynamic PWA Icon**: Ikon PWA (Progressive Web App) kini disinkronkan secara otomatis dengan *Site Icon* (Favicon) utama WordPress Anda.

---

## ✨ Fitur v1.7.7
*   **Product Title Adjustment**: Menyeragamkan batas karakter judul produk menjadi maksimal 35 karakter untuk semua tampilan (desktop & mobile).

---

## ✨ Fitur v1.7.6
*   **Product Title Truncation**: Penyesuaian batas karakter judul produk khusus di halaman depan desktop maksimal 30 karakter.
*   **Mobile Bottom Navigation UI**: Pewarnaan ikon navigasi bawah mobile agar lebih menarik dan dinamis.

---

## ✨ Fitur v1.7.5
*   **Version Synchronization**: Penyelarasan versi sistem (v1.7.5) pada seluruh file inti tema untuk stabilitas pembaruan.
*   **Elegant Top Button**: Penggunaan ikon SVG Chevron yang modern dan elegan untuk fitur kembali ke atas.
*   **Clean Desktop UI**: Penghapusan batang gulir (scrollbar) default pada desktop untuk estetika premium yang lebih bersih.
*   **Mobile UI Restoration**: Restorasi navigasi bawah mobile kembali ke gaya versi 1.6.7 yang intuitif.

---

## ✨ Fitur v1.7.4
*   **Back to Top Fix**: Memastikan tombol kembali ke atas berfungsi sempurna di desktop dan diposisikan secara tepat di atas ikon WhatsApp.

---

## ✨ Fitur v1.7.3
*   **Elegant Top Button**: Penggantian ikon Scroll to Top dengan SVG Chevron yang lebih modern dan elegan.

---

## ✨ Fitur v1.7.2
*   **Mobile UI Restoration**: Restorasi navigasi bawah (bottom nav) khusus mobile kembali ke gaya versi 1.6.7.

---

## ✨ Fitur v1.6.7
*   **Core Security Hardening**: Penambahan sistem keamanan `ABSPATH` check pada seluruh file tema.

---

## 👨‍💻 Developer
Dibuat dengan ❤️ oleh **m.alfiandiismet**.

---

## 📄 Lisensi
Tema ini tersedia untuk penggunaan pribadi dan komersial. Dilarang menghapus kredit developer tanpa izin.
