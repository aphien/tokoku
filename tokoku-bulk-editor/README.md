# TokoKu Bulk Product Editor (Edit Masal Produk)

Plugin pengelolaan dan pembaruan masal produk (**Bulk Product Editor**) lengkap dan mutakhir yang dirancang khusus untuk tema **TokoKu** (Custom Post Type: `produk`).

Memungkinkan pemilik toko dan administrator mengedit puluhan hingga ribuan produk sekaligus dalam hitungan detik secara aman melalui **Dashboard Interaktif AJAX** dan **Integrasi Native WordPress Quick & Bulk Edit**.

---

## 🌟 Fitur Utama & Kemampuan

### 1. 🏷️ Edit Masal Harga & Diskon (Pricing & Discounts)
- **Harga Normal (`_produk_harga`)**:
  - Tetapkan harga pasti (misal: set semua produk ke Rp 150.000).
  - Naikkan harga berdasarkan nominal (misal: +Rp 10.000).
  - Turunkan harga berdasarkan nominal (misal: -Rp 5.000).
  - Naikkan harga berdasarkan persentase (misal: +10%).
  - Turunkan harga berdasarkan persentase (misal: -15%).
- **Harga Diskon / Coret (`_produk_harga_diskon`)**:
  - Tetapkan harga diskon pasti (misal: promo Rp 99.000).
  - Berikan diskon persentase langsung dari harga normal masing-masing produk (misal: diskon 20% otomatis menghitung harga promo).
  - Hapus diskon masal (mengembalikan harga ke harga normal tanpa diskon coret).

### 2. 📦 Edit Masal Stok & Ketersediaan (Stock Management)
- **Status Ketersediaan**:
  - 🟢 Tersedia
  - 🔴 Habis
  - 🟡 Pre-Order
- **Jumlah Kuantitas Stok (`_produk_jumlah_stok`)**:
  - Tetapkan nilai pasti (set kuantitas stok tertentu).
  - Tambah kuantitas stok (+X).
  - Kurangi kuantitas stok (-X).
  - Kosongkan stok (stok tak terbatas).
- **Auto Habis jika 0**:
  - Otomatis mengubah status produk menjadi "Habis" jika kuantitas stok mencapai 0.

### 3. 📁 Edit Masal Kategori & Tag
- **Kategori Produk (`kategori_produk`)**:
  - Tambahkan kategori baru ke produk (tanpa menghapus kategori yang sudah ada).
  - Ganti total kategori produk yang dipilih.

### 4. 🌟 Edit Masal Label Promo (Badge) & Berat
- **Label Khusus Promo (`_produk_label_khusus`)**:
  - Tetapkan label promo serentak (contoh: "HOT", "PROMO", "TERLARIS", "BEST SELLER", "NEW").
  - Hapus label promo.
- **Berat Produk (`_produk_berat`)**:
  - Tetapkan berat produk dalam satuan gram (untuk kalkulasi ongkos kirim).
  - Kosongkan data berat produk.

### 5. ⚙️ Edit Masal Status Post
- Ubah status produk secara serentak ke **Terbit (Publish)**, **Draf (Draft)**, atau **Pending Review**.

### 6. ⚡ Aksi Simpan Cepat Langsung di Tabel (Inline Quick Edit)
- Ubah **Harga Normal**, **Harga Diskon**, **Status Ketersediaan**, dan **Kuantitas Stok** langsung di baris tabel produk.
- Tekan tombol centang atau tombol `Enter` untuk menyimpan data secara instan tanpa perlu reload halaman!

### 7. 🛡️ Pemrosesan Aman Berbasis Batch (AJAX Engine)
- Menjalankan pembaruan data secara bertahap dalam batch (25 produk/batch).
- Tidak membebani server dan bebas dari resiko *PHP Memory Limit* atau *Gateway 504 Timeout* pada katalog produk berskala besar.
- Modal animasi menampilkan progress bar persentase realtime serta log status per produk.

### 8. 🔍 Filter & Pencarian Lengkap
- Pencarian instan berdasarkan **Nama Produk** atau **Kode SKU**.
- Filter berdasarkan **Kategori Produk**.
- Filter berdasarkan **Status Ketersediaan** (Semua, Tersedia, Habis, Pre-Order).
- Filter berdasarkan **Status Diskon** (Semua, Sedang Diskon, Tanpa Diskon).
- Filter berdasarkan **Status Post** (Semua, Terbit, Draf).
- Pengurutan (Sorting): Terbaru, Terlama, Nama A-Z, Harga Termurah, Harga Tertinggi, Stok Terendah.
- Pengaturan jumlah baris per halaman (15, 30, 50, 100).

### 9. 🔌 Integrasi Native WordPress (`edit.php?post_type=produk`)
- Kolom tabel standar admin dilengkapi info **Harga** (normal & diskon coret) dan **Ketersediaan & Stok**.
- Form **Quick Edit** native mendukung input harga, diskon, ketersediaan, dan stok.
- Form **Bulk Edit** native mendukung pengaturan masal harga dan ketersediaan stok.

---

## 📁 Struktur File Plugin

```
tokoku-bulk-editor/
├── tokoku-bulk-editor.php          # File bootstrap & inisialisasi plugin
├── README.md                       # Dokumentasi lengkap plugin
├── assets/
│   ├── css/
│   │   ├── admin-bulk.css          # Styling dashboard khusus & modal progress
│   │   └── admin-native-edit.css   # Styling badge kolom dan quick/bulk edit native
│   └── js/
│       ├── admin-bulk.js           # AJAX controller, tabs, batch engine, dan live filter
│       └── admin-native-edit.js    # Helper JavaScript untuk native Quick/Bulk Edit
└── includes/
    ├── class-product-manager.php   # Core business logic update produk (harga, diskon, stok, kategori, label, dll)
    ├── class-stock-manager.php     # Compatibility wrapper untuk backwards compatibility
    ├── class-admin-page.php        # Controller halaman dashboard khusus & endpoint AJAX
    └── class-native-bulk-edit.php  # Integrasi kolom, filter, dan bulk/quick edit WP
```

---

## 🚀 Cara Instalasi & Penggunaan

### Opsi 1: Digunakan Langsung Bersama Tema TokoKu (Zero Setup)
Folder plugin sudah berada di direktori tema (`wp-content/themes/tokoku/tokoku-bulk-editor/`) dan otomatis aktif saat tema TokoKu digunakan.
Cukup buka dashboard WordPress -> menu **Produk** -> **Edit Masal Produk**.

### Opsi 2: Instal Sebagai Plugin Mandiri (Standalone WordPress Plugin)
1. Unduh file `tokoku-bulk-editor.zip`.
2. Masuk ke Admin WordPress -> **Plugins (Plugin)** -> **Add New (Tambah Baru)** -> **Upload Plugin**.
3. Pilih file `tokoku-bulk-editor.zip`, lalu klik **Install Now (Pasang Sekarang)** dan **Activate (Aktifkan)**.
4. Menu **Edit Masal Produk** akan otomatis muncul di bawah menu **Produk**.
