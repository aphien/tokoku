# 🏆 JualPlakat — Premium WhatsApp Plakat Store Theme (v2.4.8)

**JualPlakat** adalah tema WordPress premium yang dirancang khusus untuk toko plakat online — melayani penjualan plakat akrilik, plakat kayu, plakat resin, piala, trophy, dan souvenir penghargaan custom. Sistem pemesanan langsung melalui WhatsApp, tanpa kerumitan WooCommerce, ringan, cepat, dan sangat intuitif di perangkat mobile maupun desktop.

---

## 🚀 Rilis Terbaru v2.4.8 — Dynamic Product Slider Autoplay & Customizable Stock Notice

Pembaruan **v2.4.8** menghadirkan perombakan total pada galeri produk menjadi slider interaktif berperforma tinggi dengan fitur pergantian slide otomatis (*autoplay*), navigasi sentuh (*swipe gesture*), indikator slide elegan, modal zoom/lightbox terintegrasi, serta penambahan menu admin untuk mengkustomisasi judul dan teks keterangan notice status stok secara bebas.

### 🖼️ 1. Galeri & Slider Foto Produk Interaktif (Single Product)
*   **Penggabungan Foto Utama & Galeri Tambahan**: Foto utama (*featured image*) otomatis menjadi slide #1 dan terhubung dengan seluruh foto galeri tambahan (`_produk_gallery`), sehingga pengunjung dapat dengan mudah kembali ke foto utama.
*   **Engine Slide Otomatis (*Autoplay*)**:
    *   Galeri produk otomatis berganti gambar secara berkala (default: 4 detik) untuk menampilkan berbagai sudut produk plakat.
    *   **Smart Pause**: Otomatis dijeda saat kursor mouse diarahkan (*hover desktop*), saat layar disentuh/digeser (*touch swipe mobile*), saat modal zoom/lightbox terbuka, atau saat pengunjung berpindah tab browser (*Page Visibility API*).
    *   **Timer Reset**: Menghitung ulang timer jeda secara otomatis ketika pengguna mengklik panah navigasi atau thumbnail secara manual.
*   **Kontrol Pengaturan di WP Admin**: Opsi mengaktifkan/menonaktifkan autoplay serta memilih durasi jeda (3, 4, 5, 6, atau 8 detik) melalui menu **Pengaturan TokoKu -> Halaman Produk -> Galeri & Slider Foto Produk**.
*   **Touch Swipe & Keyboard Navigation**: Mendukung gestur swipe layar sentuh yang mulus dan tombol keyboard panah kiri/kanan (`ArrowLeft` & `ArrowRight`).
*   **Navigasi Frosted Glass & Counter**: Tombol panah navigasi elegan dengan efek blur kaca semi-transparan, badge counter posisi slide (`1 / N`), dan thumbnail bar horizontal dengan highlight aktif yang bergeser otomatis (*auto-scroll*).
*   **Desktop Lightbox & Mobile Pinch Zoom**: Integrasi modal lightbox desktop dan zoom layar penuh mobile yang selalu menampilkan gambar resolusi tinggi dari slide yang sedang aktif.

### 📝 2. Kustomisasi Teks Notice Status Stok di Halaman Admin
*   **Pengaturan Fleksibel**: Menu baru di **WP Admin -> Pengaturan TokoKu -> Halaman Produk -> Notice Status Stok Produk** untuk mengelola tampilan dan isi teks notice stok.
*   **Master Toggle**: Opsi untuk menampilkan atau menyembunyikan strip notice status stok secara global.
*   **3 Status Stok Dapat Dikustomisasi**:
    *   **Stok Tersedia**: Judul dan keterangan teks dapat diedit bebas (Default: `STOK TERSEDIA` — *Produk ini tersedia dan siap untuk dipesan sekarang.*).
    *   **Stok Habis**: Judul dan keterangan teks dapat diedit bebas (Default: `STOK HABIS` — *Produk ini sedang tidak tersedia. Hubungi kami untuk informasi ketersediaan berikutnya.*).
    *   **Pre Order**: Judul dan keterangan teks dapat diedit bebas (Default: `PRE ORDER` — *Hubungi kami untuk informasi lebih lanjut mengenai pemesanan produk ini.*).
*   **Dukungan Baris Baru**: Mendukung format baris baru (*multi-line*) dengan sanitasi aman sehingga tampilan keterangan di halaman produk tetap rapi dan terstruktur.

---

## 🚀 Rilis Sebelumnya v2.4.7 — Animated Stock Badges, Special Badges & Clean Mobile Single Product

Pembaruan **v2.4.7** menghadirkan badge ketersediaan stok beranimasi pada tabel spesifikasi detail produk, restrukturisasi posisi label khusus (special badge) pada katalog desktop, serta pembersihan elemen visual mobile untuk pengalaman penjelajahan yang lebih cepat dan bebas distraksi.

### 🟢 1. Badge Stok Beranimasi di Detail Produk (*Single Product*)
*   **Indikator Animasi Pulse Dot**: Status ketersediaan stok di tabel spesifikasi produk dilengkapi badge interaktif yang hidup:
    *   **Tersedia**: Pill hijau segar dengan titik *pulse dot* berdenyut lembut.
    *   **Pre Order**: Pill oranye dengan ikon jam dan denyut perhatian.
    *   **Habis**: Pill merah elegan menginformasikan stok sedang kosong.
*   **Notice Stok Minimalis Desktop**: Tampilan notice status stok dirombak menjadi strip minimalis tipis satu baris dengan border elegan, menggantikan box besar sebelumnya.

### 🏷️ 2. Restrukturisasi Label Khusus (*Special Badge*)
*   **Pemisahan Posisi Badge Kartu Produk**: Label khusus (*Featured / Terlaris / Baru*) kini menempati sudut kanan atas kartu produk dengan gradien oranye-merah dan efek *glow pulse*, terpisah dari badge diskon dan stok di sudut kiri atas.
*   **Galeri Produk Bersih**: Menghapus badge label khusus yang menumpuk di atas foto utama halaman single produk, memastikan foto plakat tampil bersih dan profesional di desktop maupun mobile.

### 📱 3. Optimasi Antarmuka Mobile Rapi & Ringkas
*   **Bebas Distraksi di Mobile**: Menyembunyikan notice stok, trust badge duplikat, dan badge katalog di layar perangkat seluler demi menghemat ruang vertikal layar pengguna.
*   **Tombol Marketplace Rata Tengah**: Judul dan tombol tautan marketplace kini otomatis rata tengah (*center-aligned*) pada tampilan mobile.

---

## 🚀 Rilis Sebelumnya v2.4.6 — Product Card Layout Refinement, Responsive Typography & Clean Mobile UX

Pembaruan **v2.4.6** menyempurnakan struktur tata letak kartu produk (*Product Card*) pada katalog beranda dan arsip, merapikan hierarki konten visual, serta mengoptimalkan pengalaman pengguna (*User Experience*) di perangkat mobile.

### 🎨 1. Penyempurnaan Tata Letak Kartu Produk (*Product Card*)
*   **Struktur Kartu Produk Bersih**: Mengembalikan tata letak klasik elegan dengan pemisahan proporsional antara gambar thumbnail produk dan blok informasi teks di bawahnya (`.product-card__content`).
*   **Hierarki Visual Jelas**: Penataan kategori produk, judul 2-baris rapi (*line-clamp*), harga dinamis (*current & discount price*), dan tombol pemesanan WhatsApp satu blok yang konsisten di semua resolusi layar.
*   **Efek Interaktif Halus**: Transisi zoom gambar elegan (`transform: scale(1.1)`) saat di-hover tanpa mengaburkan detail foto plakat.

### 📱 2. Penyesuaian Tampilan Mobile Super Rapi
*   **Tipografi Mobile Proporsional**: Penyesuaian ukuran font judul produk, kategori, dan harga agar pas di layar kecil tanpa terpotong.
*   **Tombol WhatsApp Kompak**: Tombol pemesanan WhatsApp berukuran slender dengan padding ergonomis dan ikon WhatsApp SVG tajam untuk mempermudah pemesanan instan lewat jempol (*one-thumb navigation*).

---

## 🚀 Rilis Sebelumnya v2.4.5 — 100/100 Mobile & Desktop PageSpeed Optimization (FCP, LCP & Speed Index Overhaul)

Pembaruan **v2.4.5** mengimplementasikan serangkaian teknik optimasi performa web mutakhir untuk meraih skor maksimal (100 / Hijau) pada Google PageSpeed Insights dan Core Web Vitals, baik pada perangkat Mobile maupun Desktop.

### ⚡ 1. First Contentful Paint (FCP) & Render-Blocking Elimination
*   **Asynchronous Google Fonts Loading**: Memuat Google Fonts melalui metode modern non-render-blocking (`rel="preload"` + `media="print" onload="this.media='all'"` dengan fallback `<noscript>`), sehingga peramban dapat langsung merender teks dan layout di bawah 0.4 detik tanpa tertahan jaringan font eksternal.
*   **Pembersihan Total Dashicons CSS**: Menghapus antrean `dashicons.min.css` (~35 KB render-blocking CSS) dan webfont `dashicons.woff2` pada frontend bagi seluruh pengunjung non-admin.
*   **Penggantian Ikon ke Inline SVG Ringan**: Mengganti semua ikon frontend (keranjang kosong, panah tombol jelajah, tanda kutip ulasan, rating bintang, dan metadata artikel) dengan kode SVG inline berukuran beberapa byte saja.
*   **Pembersihan Gutenberg Block CSS**: Menonaktifkan pemuatan stylesheet bawaan WordPress yang tidak terpakai (`wp-block-library`, `wp-block-library-theme`, `classic-theme-styles`, `global-styles`, dan SVG filters) pada frontend.

### 🖼️ 2. Largest Contentful Paint (LCP) Boost
*   **Preload Gambar LCP di `<head>`**:
    *   Halaman Beranda: Menambahkan tag `<link rel="preload" as="image" href="..." fetchpriority="high">` untuk gambar Hero Banner pertama.
    *   Halaman Produk Tunggal: Menambahkan preload otomatis untuk featured image produk (`tokoku-product-large`).
    *   Halaman Artikel: Menambahkan preload untuk foto sampul artikel.
*   **Atribut Prioritas Render Maksimal**: Menerapkan kombinasi atribut `fetchpriority="high"`, `loading="eager"`, dan `decoding="sync"` pada gambar utama di atas lipatan layar (*above-the-fold*).

### 🚀 3. Speed Index & Zero Cumulative Layout Shift (CLS)
*   **Dimensi Gambar Eksplisit**: Menyematkan atribut `width`, `height`, dan `sizes` responsif pada seluruh gambar (Hero slider, logo partner marquee, thumbnail produk katalog, placeholder, dan avatar ulasan) untuk memastikan browser mengalokasikan ruang layout secara instan sebelum file gambar terunduh.
*   **Optimasi Transisi Awal**: Menghaluskan keyframe animasi kartu kategori beranda agar elemen tampil instan tanpa jeda opacity buatan, mempercepat pengukuran visual kelengkapan halaman (*Visual Completion Index*).

---

## 🚀 Rilis Sebelumnya v2.4.4 — Comprehensive Bugfixes, Mobile Search Fix, CSS/JS Modularization & Extreme Speed Optimization

Pembaruan **v2.4.4** berfokus pada optimasi performa tinggi, modularisasi aset kode, perbaikan menyeluruh pada modal pencarian mobile, dan percepatan waktu muat (Core Web Vitals) ke tingkat maksimal.

### 📦 1. Pemisahan & Modularisasi CSS/JS Halaman Produk Tunggal (*Single Product*)
*   **Ekstraksi Kode Bersih**:
    *   Mengekstrak 977 baris kode CSS inline dan JavaScript inline dari `single-produk.php` ke file terpisah: [`assets/css/single-product.css`](file:///Users/m.alfiandiismet/server/jualplakat/app/public/wp-content/themes/tokoku/assets/css/single-product.css) dan [`assets/js/single-product.js`](file:///Users/m.alfiandiismet/server/jualplakat/app/public/wp-content/themes/tokoku/assets/js/single-product.js).
    *   Ukuran payload HTML mentah halaman produk terpangkas lebih dari 50% (dari ~68 KB menjadi ~34 KB), mempercepat pengunduhan dan penguraian DOM oleh peramban.
*   **Pemuatan Kondisional Cerdas**:
    *   Aset style dan skrip produk tunggal kini dimuat secara kondisional hanya saat membuka halaman produk (`is_singular('produk')`), sehingga halaman Beranda, Kategori, dan Arsip Blog bebas dari beban CSS/JS produk.

### 🔍 2. Perbaikan Total Navigasi & Modal Pencarian Mobile
*   **Interaksi Modal Pencarian Instan**:
    *   Memperbaiki event listener trigger pencarian modal mobile (`mobile-search-nav-trigger` / `#header-search-input-mobile`).
    *   Mengetuk input pencarian di header pada layar mobile (`<= 768px`) kini otomatis membuka modal pencarian AJAX interaktif secara instan dan responsif.
    *   Penanganan event tombol Escape, klik backdrop overlay, dan tombol tutup modal bekerja 100% mulus tanpa terjadi bubbling atau tabrakan event DOM.

### ⚡ 3. Optimasi Kecepatan Ekstrem & Core Web Vitals (FCP / LCP / CLS)
*   **Resource Hints Otomatis**:
    *   Menambahkan header `preconnect` & `dns-prefetch` untuk `fonts.googleapis.com` dan `fonts.gstatic.com` via hook `wp_resource_hints` dan `<head>` tag, mempercepat pengunduhan Google Fonts (Inter).
*   **Deferred JavaScript Execution**:
    *   Menambahkan filter `script_loader_tag` untuk menerapkan atribut `defer` secara otomatis pada seluruh skrip tema frontend guna mengeliminasi *render-blocking resources*.
*   **Pembersihan Skrip WP Emoji**:
    *   Mematikan script dan stylesheet emoji bawaan WordPress di sisi pengunjung (`print_emoji_detection_script`, `print_emoji_styles`) untuk memangkas HTTP requests.
*   **LCP Optimization pada Gambar Utama**:
    *   Menambahkan atribut `fetchpriority="high"` dan `loading="eager"` pada gambar featured utama produk untuk rendering visual instan di atas lipatan layar (*above-the-fold*).
*   **Throttling Query Database (`no_found_rows`)**:
    *   Mengaktifkan `'no_found_rows' => true` pada query produk terkait, pencarian SKU/judul AJAX, produk beranda, dan artikel beranda untuk meniadakan kalkulasi `SQL_CALC_FOUND_ROWS` yang berat pada MySQL.
*   **Query Artikel Terkait Cepat**:
    *   Mengganti `ORDER BY RAND()` yang membebani CPU database dengan `ORDER BY date DESC` yang memanfaatkan indeks tabel secara optimal.
*   **Transient Cache Rasio Banner**:
    *   Menyimpan kalkulasi rasio aspek hero banner dalam transient cache selama 24 jam untuk menghilangkan disk I/O synchronous `@getimagesize` di setiap kunjungan beranda.
*   **Optimasi Logo Partner Beranda**:
    *   Menyederhanakan iterasi ganda logo partner menjadi perulangan tunggal yang ringan dengan atribut `loading="lazy"` dan `decoding="async"`.

### 🚀 4. Scroll & Render Performance Smoothness
*   **Single Loop `requestAnimationFrame`**:
    *   Menggabungkan listener scroll sticky header dan tombol *Scroll to Top* ke dalam satu loop `requestAnimationFrame` dengan opsi `{ passive: true }` untuk mencegah stuttering/jank pada layar refresh rate tinggi (120Hz/ProMotion).
*   **Resolusi Konflik Sticky Order Bar**:
    *   Menyelaraskan IntersectionObserver dengan scroll handler untuk mencegah glitch saat bar pemesanan muncul/sembunyi.

### 🛡️ 5. PWA Service Worker Enhancement
*   **Strategi Caching Adaptif**:
    *   Service Worker `sw.js` diperbarui ke versi `v2.4.4` dengan strategi Network-First untuk navigasi halaman HTML dan Stale-While-Revalidate untuk static assets, serta bypass aman untuk `/wp-admin/` dan `admin-ajax.php`.

---

## 🚀 Rilis Sebelumnya v2.4.3 — Single Product Lead Time & Trust Badges, Glassmorphism Sticky Bar, Mobile Square Categories & Ultra-Smooth Animations

Pembaruan **v2.4.3** menghadirkan penyempurnaan besar pada fleksibilitas kustomisasi admin, pengalaman visual modern (*Glassmorphism*), tata letak kategori mobile yang lebih rapi (*Square 1:1*), serta perbaikan total pada efek animasi menu mobile dan navigasi.

### 🛠️ 1. Panel Pengaturan Baru di Admin: Lead Time Bar & Trust Badges
*   **Tab Baru "Halaman Produk" (`tab-single-product`)**:
    *   Tersedia langsung di menu pengaturan tema WordPress Admin untuk mengelola komponen konversi di halaman produk.
*   **Product Lead Time Bar Dinamis (`product-lead-time-bar`)**:
    *   Sakelar ON/OFF untuk menampilkan atau menyembunyikan bar estimasi waktu pengerjaan.
    *   Teks label, nilai estimasi hari/waktu, subteks, serta teks badge (*chip*) kini dapat diubah bebas melalui admin dashboard.
    *   Pilihan ikon fleksibel: 7 preset SVG (*Clock, Lightning, Truck, Calendar, Shield, Award, Star*), unggah gambar/ikon sendiri via WP Media Library, atau tempel kode SVG kustom (dilengkapi fungsi sanitasi keamanan `tokoku_sanitize_svg()`).
    *   Efek animasi premium: sapuan kilap cahaya (*shimmer sweep*), pendaran ikon lembut, dan titik radar berkedip langsung (*live radar ping dot*).
*   **Product Trust Badges Dinamis (`product-trust-badges`)**:
    *   Sakelar ON/OFF untuk menampilkan atau menyembunyikan 4 kartu garansi & kepercayaan toko.
    *   Tiap badge dapat diatur judul, deskripsi, dan ikonnya secara terpisah (preset ikon: *Design, Shield, Lightning, Craftsman, Award, Check, Heart, Box, Thumbs-up, Star*, gambar kustom, atau SVG).
    *   Animasi melayang halus (*ambient float*) dan efek pegas dinamis saat disentuh/di-hover.

### ✨ 2. Single Product Glassmorphism Sticky Order Bar
*   **Tampilan Kaca Mengambang (*Glassmorphism*)**:
    *   Bar pemesanan mengambang di halaman produk saat aktif kini menggunakan efek *frosted glass* modern (`backdrop-filter: blur(24px) saturate(180%)`, border specular reflektif, dan bayangan lembut).
    *   Penyelarasan posisi presisi di atas bottom navigation mobile dengan memperhitungkan *safe-area-inset* perangkat layar modern (`bottom: calc(68px + env(safe-area-inset-bottom, 0px))`).
*   **Animasi Tombol Sticky Order**:
    *   Animasi muncul pegas (*pop entrance*), efek pendaran cahaya WhatsApp berkilau (*radiant pulse glow*), dan sapuan kilau (*shimmer light sweep*).

### 📱 3. Tampilan Kategori Halaman Depan Mobile Menjadi Square (1:1)
*   **Bentuk Kartu Kategori Square (1:1)**:
    *   Kartu kategori (`.category-item`) pada layar mobile kini berasio simetris **Square 1:1** (`aspect-ratio: 1 / 1 !important;`).
    *   Perataan tengah sempurna secara vertikal dan horizontal (`justify-content: center !important;`).
    *   Proporsi ukuran ikon dinamis (`clamp`) dan tipografi seimbang, memastikan nama kategori 1 baris maupun 2 baris tampil rapi, pas, dan tidak meluber keluar kartu (*anti-overflow*).

### 🎬 4. Perbaikan Total Efek Animasi Menu & Navigasi Mobile
*   **Backdrop Overlay Lembut Tanpa Kedip**:
    *   Menggantikan transisi instan `display: none`/`block` dengan animasi Fade-in / Fade-out halus berbasis `opacity` dan efek buram kaca (`backdrop-filter: blur(8px)`).
*   **Drawer Sheet Hardware-Accelerated**:
    *   Transisi geser menggunakan `translate3d(...)` dengan kurva pegas iOS (`cubic-bezier(0.32, 0.72, 0, 1)`), sudut melengkung modern (*sheet curvature* `border-top-left-radius: 20px`), dan dukungan tema gelap (*dark mode*).
*   **Efek Muncul Bertingkat (*Staggered Waterfall Entrance*)**:
    *   Tautan menu meluncur masuk secara berurutan (*delays* 0.06s – 0.34s) dari kanan ke kiri saat drawer dibuka.
*   **Ikon Menu Bottom Nav Berubah Jadi 'X' (*Morphing Hamburger to Close*)**:
    *   Tiga garis ikon hamburger di Bottom Navigation secara dinamis berputar membentuk silang 'X' saat menu aktif, dilengkapi pendaran cahaya ungu (*navMenuPulse*).
*   **Akordion Submenu Halus**:
    *   Submenu kini memiliki transisi slide vertikal akordion mulus dengan rotasi 180° pada indikator panah (*chevron*).
*   **Kunci Gulir Latar Belakang (*Scroll Lock*)**:
    *   Mencegah halaman utama ikut bergeser di belakang menu saat menu mobile sedang terbuka (`body.menu-open`).

### 🎯 5. Optimasi Tombol & Navigasi Bawah
*   **Tombol "Lihat Semua Produk" Proporsional**:
    *   Ukuran tombol diperkecil 10% khusus mobile untuk keseimbangan visual beranda.
    *   Dilengkapi animasi pendaran cahaya lembut (*pulse glow*), sapuan kilap berkilau (*shimmer reflection*), dan pantulan panah halus (*arrow bounce*).
*   **Bottom Navigation**:
    *   Ketinggian dinaikkan 5% (68px) dan ukuran ikon menu diperbesar untuk kenyamanan navigasi ibu jari.

---

## 🚀 Rilis Sebelumnya v2.4.2 — Codebase Cleanup & Performance Streamlining

Pembaruan **v2.4.2** berfokus pada penyederhanaan antarmuka pengguna (*UI streamlining*), optimasi kecepatan rendering halaman, serta pembersihan elemen-elemen dan widget non-esensial agar alur pemesanan plakat menjadi lebih fokus, bersih, cepat, dan konversi WhatsApp meningkat.

### 🧹 Pembersihan Komponen & UI Streamlining:
*   **Pembersihan Elemen Countdown Timer (`product-countdown-bar`)**:
    *   Menghapus widget countdown timer di halaman detail produk (`single-produk.php`), logika timer di `assets/js/main.js`, dan styling terkait di `assets/css/main.css`. Tampilan detail produk kini lebih tenang, elegan, dan profesional.
*   **Pembersihan Kalkulator Estimasi Harga (`price-calculator-widget`)**:
    *   Menghapus widget kalkulator harga interaktif dan tier grosir, skrip kalkulasi, serta CSS accordion kalkulator. Alur pemesanan dialihkan langsung ke konsultasi personal WhatsApp yang jauh lebih fleksibel untuk produk kustom.
*   **Pembersihan Tombol Desain Tambahan (`btn-view-designs`)**:
    *   Menghapus tombol variasi desain yang redundan pada detail produk agar pengunjung fokus langsung pada tombol pemesanan utama.
*   **Pembersihan WhatsApp Floating Chat Button Global**:
    *   Menghapus tombol floating WhatsApp global di `footer.php` dan skrip terkait. Akses WhatsApp tetap prima dan tidak tumpang tindih berkat *Mobile Bottom Navigation* dan *Sticky Order Bar*.
*   **Pembersihan Modal Pop-up & Lightbox**:
    *   Menghapus komponen `template-parts/catalog-popup.php` (exit-intent modal) dan `template-parts/lightbox-gallery.php` beserta inisialisasi skripnya.
*   **Pembersihan File Template Halaman Tambahan**:
    *   Menghapus file template khusus `page-cara-pemesanan.php` dan `page-portofolio.php` yang sudah tidak diperlukan, merapikan struktur file tema secara menyeluruh.

### ⚡ Dampak & Keunggulan:
*   **Codebase Ramping**: Memangkas lebih dari **1.700 baris kode** (HTML, CSS, JS, PHP) yang tidak terpakai.
*   **Loading & Rendering Lebih Cepat**: Ukuran bundle aset lebih kecil, DOM lebih efisien, dan waktu muat halaman meningkat signifikan.
*   **Stabilitas 100%**: Seluruh file PHP & JavaScript divalidasi bebas error sintaks.

---

## ✨ Fitur v2.4.1

*   **Grid Kategori Desktop Satu Baris (Single-Row Modern Layout)**:
    *   Deretan kategori pada beranda desktop kini tersusun rapi dalam 1 baris (`flex-wrap: nowrap`) dengan perataan tengah yang simetris dan elegan.
    *   Proporsi kartu diperkecil 20% agar tidak memakan ruang berlebih dan tampil lebih kompak di berbagai resolusi layar monitor.
    *   Dimensi ikon kategori desktop disesuaikan secara proporsional (88px dengan ikon 44px) untuk harmonisasi visual yang seimbang.
*   **Perbaikan Efek Visual Kategori Anti-Potong (Overflow Fix)**:
    *   Pembaruan kontainer kategori dengan `overflow: visible` dan penambahan *padding compensation* sehingga efek bayangan melayang (*hover box-shadow*), pendaran cahaya (*glow halo*), serta animasi naik (*lift-up transform*) tampil utuh tanpa terpotong batas kontainer.
*   **Pembersihan Category Pill-Bar di Desktop**:
    *   Komponen *category-pill-bar* kini disembunyikan pada layar desktop (`display: none`) untuk menjaga tampilan katalog tetap bersih, dan secara otomatis tampil optimal hanya di layar sentuh mobile.
*   **Redesain Halaman Cara Pemesanan (`page-cara-pemesanan.php`)**:
    *   Tampilan baru dengan estetika modern bergaya SaaS:
        *   **Hero Section**: Badge verifikasi proses terpercaya, judul estetik, dan metrik kepercayaan (Ribuan Plakat Terkirim, Desain Mockup Gratis, Jaminan Kualitas).
        *   **6 Langkah Visual Terstruktur**: Konsultasi & Pilih Produk, Kirim Materi / Logo, Preview Desain Gratis, Pembayaran DP 50%, Proses Produksi Cepat, dan Pelunasan & Pengiriman Aman.
        *   **Panel Informasi Pembayaran**: Metode pembayaran transfer bank lengkap (BCA, Mandiri, BRI, BNI) dan info skema DP 50% yang transparan.
        *   **Akordeon FAQ Interaktif**: Pertanyaan yang sering diajukan seputar pemesanan, minimal order, pengerjaan kilat, hingga garansi kerusakan saat pengiriman (tanpa ketergantungan library luar).
        *   **Call To Action Ganda**: Tombol pesan langsung via WhatsApp dan unduh katalog.
*   **Penyempurnaan Struktur CSS Core**:
    *   Restorasi dan validasi modul styling v2.4.0 pada `assets/css/main.css` untuk memastikan kalkulator harga, galeri lightbox, floating WA, dan popup katalog bekerja mulus.

---

## ✨ Fitur v2.4.0

*   **⏰ Countdown Timer Pemesanan (Urgency & Conversions)**:
    *   Bar hitung mundur dinamis di halaman produk tunggal yang menghitung mundur ke pukul 17:00 setiap harinya.
    *   Memberikan dorongan psikologis (*Urgency/FOMO*) kepada calon pembeli untuk segera memesan agar pesanan dapat masuk antrean produksi dan dikirim pada hari yang sama.
*   **🧮 Kalkulator Estimasi Harga & Tier Grosir Interaktif**:
    *   Kalkulator estimasi harga otomatis berbasis accordion di halaman detail produk.
    *   Menampilkan tingkatan diskon kuantiti (*tiered pricing*): Satuan (1–9 pcs), Grosir Kecil (10–49 pcs), Grosir Sedang (50–99 pcs), dan Partai Besar (100+ pcs).
    *   Dilengkapi tombol *"Pesan via WhatsApp dengan Detail Kalkulator"* yang langsung memformat rincian jumlah dan estimasi harga ke pesan WhatsApp.
*   **🖼️ Lightbox Gallery Full-Screen**:
    *   Galeri pratinjau foto produk resolusi tinggi layar penuh (*full-screen overlay*).
    *   Mendukung gestur sentuh *swipe* di perangkat mobile, tombol navigasi panah keyboard (kiri/kanan/Esc), navigasi tombol visual, dan deretan thumbnail gambar yang responsif.
*   **💬 WhatsApp Floating Chat Button**:
    *   Tombol mengambang (*floating action button*) WhatsApp di pojok kanan bawah seluruh halaman situs.
    *   Dilengkapi animasi pop-in saat pertama kali dimuat dan tooltip informatif saat cursor diarahkan.
*   **🎁 Catalog Popup (Exit-Intent & Auto-Timer)**:
    *   Modal penawaran unduh katalog produk gratis yang cerdas.
    *   Muncul secara otomatis setelah 10 detik atau saat mendeteksi kursor pengunjung bergerak menuju tombol keluar (*exit-intent*), dilengkapi opsi unduh katalog langsung via WhatsApp.
*   **🏆 Template Halaman Portofolio / Hasil Karya (`page-portofolio.php`)**:
    *   Template khusus untuk menampilkan portofolio proyek dan hasil produksi plakat.
    *   Menampilkan galeri dinamis dari data produk yang memiliki foto, dilengkapi counter jumlah foto, filter kategori, dan tombol ajakan konsultasi.

---

## ✨ Fitur v2.3.9

*   **Template Pesan WhatsApp Premium (Branded Order Template)**:
    *   Pembaruan template pesan WhatsApp menjadi format profesional berlabel `[ DETAIL PESANAN PLAKAT ]` dengan pemisah dekoratif (`✧━━━━━✧`), emoji informatif, dan tata letak tabel pesanan yang mudah dibaca.
    *   Seluruh variabel dinamis tersedia: `{nama}`, `{produk}`, `{sku}`, `{link}`, `{harga}`, `{jumlah}`, dan `{catatan}`.
    *   Pesan diakhiri dengan ajakan konfirmasi elegan: *"Silakan balas CONFIRM agar pesanan dapat segera kami proses"* untuk mempercepat alur penjualan.
*   **Mobile Sticky Order Bar di Halaman Produk**:
    *   Bar pemesanan mengambang di bagian bawah layar muncul otomatis saat pengguna mulai menggulir ke bawah, memudahkan akses pesan tanpa perlu kembali ke tombol utama.
    *   Desain tombol elegan dengan ikon WhatsApp, label teks, dan badge harga produk aktif.
    *   Menggunakan `IntersectionObserver` + scroll fallback untuk performa optimal di semua browser.
    *   Harga pada sticky bar otomatis sinkron saat pengguna memilih varian produk.
*   **Quick Prompts — Template Catatan Instan di Modal WhatsApp**:
    *   Empat tombol pil cepat tersedia di atas kolom Catatan: 🎨 Custom Desain, 📦 Pesan Grosir, ⚡ Butuh Cepat, 📋 Minta Katalog.
    *   Multi-select: pengguna dapat memilih lebih dari satu pil sekaligus; teks prompt otomatis disusun rapi di textarea catatan.
    *   Tap sekali untuk pilih, tap lagi untuk batal — dengan visual state aktif yang jelas dan animasi halus.
*   **Identitas Tema JualPlakat (Theme Rebranding)**:
    *   Metadata tema (`style.css`) diperbarui: nama tema menjadi **JualPlakat**, URI `jualplakat.com`, deskripsi khusus toko plakat, dan tags SEO relevan (`plakat, piala, penghargaan, souvenir, custom, whatsapp-order`).
    *   Konstanta versi `TOKOKU_VERSION` diperbarui ke `2.3.9` di `functions.php` dan `style.css`.

---

## ✨ Fitur v2.3.8
*   **Tipografi Blog & Card Responsif Khusus Mobile (Fluid Typography)**:
    *   **Kartu Blog Mobile (`.blog-card`)**: Tipografi kartu artikel blog kini menggunakan formula `clamp()` yang presisi di semua resolusi ponsel (320px–768px). Judul artikel, kutipan excerpt, meta tanggal/waktu baca, dan badge kategori otomatis menyesuaikan proporsi tanpa terpotong atau terlalu padat.
    *   **Kenyamanan Baca Artikel Penuh (`single.php`)**: Seluruh hierarki tipografi isi artikel (`h2`, `h3`, `h4`, paragraf, kutipan `blockquote`, daftar *list*, tabel, dan blok kode) dioptimalkan secara fluid dengan `line-height: 1.8` dan ukuran kontainer yang nyaman di genggaman ponsel.
    *   **Elemen Pendukung Interaktif Mobile**: Kotak info penulis (*Author Box*), navigasi artikel sebelumnya/berikutnya, tombol *share* media sosial, dan form komentar dirancang ulang agar pas dan rapi tanpa *overflow* horizontal.
*   **Pembaruan Animasi Tampilan Kategori Produk (SaaS Micro-Animations)**:
    *   **Staggered Entrance Animation**: Efek transisi kemunculan kartu kategori berjenjang halus (*fade-in slide-up*) saat halaman dimuat.
    *   **Radial Glow Halo Backdrop**: Efek pendaran cahaya ambient gradasi biru yang merekah lembut saat kartu kategori disentuh atau di-hover.
    *   **Diagonal Light Shimmer Sweep**: Sapuan kilau diagonal elegan di permukaan kartu kategori saat pointer diarahkan.
    *   **Spring Pop Icon & Image Transform**: Ikon kategori bergerak dinamis dengan transisi pegas (*spring curve* `cubic-bezier`), membesar proporsional dengan bayangan mengambang mewah.
    *   **Respon Sentuh Presisi Mobile**: Menghilangkan efek *hover* kaku di layar sentuh ponsel dan menggantinya dengan respon *active tactile feedback* (scale 0.95) yang responsif dan cepat.

---

## ✨ Fitur v2.3.7
*   **Optimalisasi SEO Menyeluruh (Comprehensive SEO Overhaul)**:
    *   **Canonical URL Otomatis**: Menambahkan tag `<link rel="canonical">` yang cerdas dan akurat untuk seluruh tipe halaman (beranda, produk tunggal, arsip katalog, taksonomi kategori/tag, artikel blog, arsip tanggal/penulis, hingga halaman paginasi). Mengeliminasi risiko duplikat konten dan menghapus hook standar WP yang berpotensi menghasilkan tag ganda.
    *   **Breadcrumb Schema Markup (JSON-LD)**: Implementasi struktur data Schema.org `BreadcrumbList` bersarang di `<head>` untuk seluruh halaman produk, blog, kategori, dan arsip sehingga Google menampilkan breadcrumb rich snippet di hasil pencarian.
    *   **Robots Meta Tag Dinamis**: Mengatur crawling search engine secara presisi (`index, follow, max-image-preview:large`), otomatis menerapkan `noindex, follow` pada halaman pencarian dan 404, serta sinkron dengan opsi visibilitas WordPress.
    *   **Product Schema Price Guard**: Memperbaiki schema produk agar tidak lagi mengeluarkan `price: 0` jika produk tidak memiliki harga tetap/custom quote, mencegah peringatan "Offer price must be greater than 0" di Google Search Console.
    *   **Teks Alt Banner Slider & Logo Mitra**: Menambahkan input kustom "Teks Alt Banner (SEO)" di Panel Pengaturan TokoKu dan WordPress Customizer, serta menyematkan fallback deskriptif berbasis nama situs untuk banner dan logo partner.
    *   **Penyempurnaan Breadcrumb Visual Produk**: Melengkapi navigasi breadcrumb di halaman produk dengan judul aktif (`.current`) dan dukungan teks panjang responsif (`flex-wrap`).

---

## ✨ Fitur v2.3.6
*   **Nonaktifkan Tombol Enter pada Kotak Pencarian Produk**:
    *   **Desktop Search**: Tombol Enter tidak lagi me-redirect ke halaman pencarian (`/s=keyword`). Pengguna tetap di halaman yang sama dan hanya melihat hasil AJAX real-time.
    *   **Mobile Modal Search**: Tombol Enter di keyboard mobile juga diblokir agar tidak men-submit form secara tidak sengaja.
    *   **Validasi Menyeluruh**: Seluruh 24 file PHP divalidasi tanpa error sintaks. CSS `main.css` (882 brace) dan `admin.css` (128 brace) seimbang. Semua file JS valid.

---

## ✨ Fitur v2.3.5
*   **Perbaikan Sistem Pembaruan Tema (Theme Updater Fix)**:
    *   **Filesystem Direct Method**: Menambahkan `add_filter('filesystem_method', 'direct')` sebelum `WP_Filesystem()` agar proses update tidak meminta kredensial FTP — bekerja langsung di server lokal maupun shared hosting.
    *   **Flush Opcache Pasca-Update**: Setelah file tema berhasil disalin, `opcache_reset()` dan `wp_clean_themes_cache()` dipanggil otomatis sehingga versi baru langsung terbaca tanpa perlu restart server.
    *   **Perbaikan GitHub API Checker**: Tambahkan `Accept: application/vnd.github.v3+json` header dan cache-busting `?_=timestamp` agar GitHub API selalu mengembalikan data terkini, tidak menggunakan cache 304.
    *   **Fallback Tag API yang Benar**: Fallback ke `/tags?per_page=1` menggunakan `zipball_url` langsung dari objek tag (bukan dibuat manual), memastikan URL unduhan selalu valid.
    *   **Validasi Versi Kosong**: Jika tag GitHub tidak mengandung angka versi (format bukan `vX.Y.Z`), muncul pesan error spesifik beserta panduan perbaikan.
    *   **Link Unduh Manual di Error**: Jika cek pembaruan gagal total, ditampilkan link langsung ke [GitHub Releases](https://github.com/aphien/tokoku/releases) untuk unduh manual.

---

## ✨ Fitur v2.3.4
*   **Mobile-First Button System — Responsif Semua Device**:
    *   **Touch Accessibility Global**: Seluruh tombol interaktif (`.btn-view-all`, `.btn-whatsapp-order`, `.btn-submit-comment`, `.btn-contact-us`, `.btn-marketplace`, `.slider-btn`, dll.) kini memiliki `-webkit-tap-highlight-color: transparent`, `touch-action: manipulation`, dan `user-select: none` untuk pengalaman sentuh yang presisi di semua perangkat.
    *   **Ukuran Sentuh Optimal (Min-Height 44–52px)**: Semua tombol utama memenuhi standar aksesibilitas WCAG 2.5.5 dengan `min-height` minimal 44px pada layar ≤600px. Tombol pesan utama (`.btn-contact-us`) memiliki `min-height: 52px` untuk kemudahan penekanan.
    *   **Tipografi Fluid dengan `clamp()`**: Font-size tombol menggunakan `clamp()` untuk skala otomatis yang halus di semua resolusi — tidak terlalu kecil di 320px, tidak terlalu besar di 600px.
    *   **Safe Area Bottom Nav**: `.bottom-nav` kini mendukung `env(safe-area-inset-bottom)` untuk iPhone dengan notch/Dynamic Island, mencegah tombol tertutup gesture bar sistem.
    *   **Breakpoint ≤400px (Ultra-Small)**: Tombol marketplace beralih ke layout satu kolom; padding & font-size dikompreskan lebih lanjut tanpa kehilangan keterbacaan.
    *   **`@media (hover: none) and (pointer: coarse)`**: Efek `:hover` transform dihapus khusus pada perangkat sentuh (menghindari "sticky hover state"), digantikan oleh `:active` scale feedback yang responsif.
    *   **Landscape Mobile**: Pada orientasi lanskap dengan tinggi ≤500px, tinggi tombol dikurangi proporsional dan bottom nav disembunyikan untuk memaksimalkan ruang konten.

---

## ✨ Fitur v2.3.3
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
