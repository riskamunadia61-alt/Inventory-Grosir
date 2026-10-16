#  Inventory Management System (Grosir)

Aplikasi Web Manajemen Inventaris Toko Grosir berbasis PHP Native & MySQL. Sistem ini dibuat untuk mengelola data produk, kategori, stok barang masuk dan keluar, serta menghitung nilai aset gudang secara otomatis dengan dukungan keamanan berbasis *role— Inventory Management System**.



## Fitur Utama

- **Autentikasi & Keamanan:**
  - Login & Logout berbasis Session.
  - Hashing password aman dengan `password_hash()`.
  - Pembatasan Hak Akses (*Role-based Authorization*: Admin & Staff).
  - Keamanan dari SQL Injection (*PDO Prepared Statements*) dan XSS (*Output Encoding* `htmlspecialchars`).
- **Manajemen Produk (CRUD):**
  - Pencarian produk interaktif (*Search*).
  - Pengelolaan data produk (Nama, SKU Unik, Kategori, Harga, Stok).
  - Pembatasan tombol hapus produk khusus untuk hak akses Admin.
- **Transaksi Stok & Laporan:**
  - Pencatatan Barang Masuk (Penambahan Stok) dan Barang Keluar (Pengurangan Stok).
  - Laporan Riwayat Transaksi Stok (*Stock Movement History*) lengkap dengan tanggal dan catatan.
- **Dashboard Agregat:**
  - Ringkasan Total SKU Grosir.
  - Perhitungan Nilai Total Aset Gudang (Stok × Harga).
  - Peringatan Stok Kritis (Stok ≤ 10).
  - Ringkasan 5 produk terbaru dan menu navigasi cepat ke seluruh fitur.

---

##  Tampilan & Desain Visual

- **Tema Warna:** Krem & Cokelat Warm (#8b5a2b, #6e441f, #f5f0eb, #faf7f4).
- **Tata Letak:** *Centered Flexbox* (Form dan tabel berada persis di tengah layar).

---

##  Persyaratan Sistem & Teknologi

- **PHP:** Version 7.4 / 8.x
- **Database:** MySQL / MariaDB
- **Web Server:** Apache (via XAMPP / Laragon)
- **Extension:** PDO PHP Extension

---

##  Struktur Folder Project

```text
inventory-grosir/
├── config/
│   └── database.php          # Koneksi Database (PDO)
├── helpers/
│   ├── auth.php              # Helper Session & Role Authorization
│   └── escape.php            # Helper XSS Protection (Output Encoding)
├── public/
│   ├── login.php             # Halaman Autentikasi Login
│   ├── logout.php            # Halaman Logout
│   ├── dashboard.php        # Dashboard Utama & Ringkasan Laporan
│   ├── products.php         # Daftar & Pencarian Produk
│   ├── product_form.php     # Form Tambah/Edit Produk
│   ├── stock_transaction.php# Form Input Transaksi Stok (Masuk/Keluar)
│   └── transaction_history.php # Halaman Laporan Riwayat Transaksi
└── README.md                 # Dokumentasi Project