# ✚ Zenora Pharma — Smart Pharmacy Inventory

**Sistem Manajemen Inventori Apotek Masa Depan** berbasis PHP Native dan MySQL.

Mini Project Pemrograman Web — Pertemuan 3 (Integrasi PHP, MySQL & UI Styling).


Nama : INTAN MANDALIKA 
NIM : 250180005
Kelas : A1 — Sistem Informasi

## 🎯 Deskripsi Proyek

Zenora Pharma adalah aplikasi web untuk mengelola katalog dan stok obat apotek dengan tampilan bertema *smart pharmacy* (dark/light theme). Aplikasi menerapkan keamanan berlapis: PDO Prepared Statement untuk mencegah SQL Injection, `htmlspecialchars()` untuk mencegah XSS, token CSRF kriptografik via `random_bytes()`, serta pola Post-Redirect-Get (PRG) untuk mencegah duplikasi data saat refresh. Kode disusun dengan prinsip pemisahan tanggung jawab: konfigurasi, logika, dan tampilan berada di file yang berbeda.

### 📌 Capaian Pembelajaran

- Memahami siklus request-response antara browser dan server PHP.
- Menerapkan keamanan berlapis (PDO, anti-XSS, anti-CSRF, PRG) sebagai standar minimum aplikasi web.
- Mengimplementasikan CRUD berbasis MySQL dengan validasi server-side (nama ≥ 3 karakter, harga > 0, stok ≥ 0, nama unik).
- Membangun antarmuka responsif dengan Box Model dan Flexbox tanpa library CSS eksternal.

## 🛠️ Tech Stack

| Komponen | Teknologi |
|---|---|
| Bahasa server | PHP Native + PDO |
| Database | MySQL / MariaDB (XAMPP) |
| Frontend | HTML5 + CSS3 (Box Model, Flexbox, CSS Variables) |
| Framework CSS | Tidak ada (Pure CSS) |
| Library JS | Tidak ada (Vanilla JS, hanya untuk toggle tema dan alert) |

## 🖥️ Tampilan Antarmuka

### Halaman Utama — Katalog Obat (`index.php`)

- **Sidebar navigasi**: logo, menu, filter kategori berwarna, dan tombol ganti tema.
- **Dashboard statistik**: total jenis obat, total unit stok, nilai persediaan, dan jumlah stok kritis.
- **Search bar dan filter kategori** memakai parameter GET yang aman.
- **Grid kartu (Flexbox)**: kode obat (ZP-0001), kategori, harga, progress bar stok, badge status (Tersedia / Menipis / Habis), tombol Edit dan Hapus.
- **Flash alert** otomatis muncul setelah operasi CRUD berhasil.

![Halaman utama](public/assets/screenshots/ss_00_index.png)

### Halaman Tambah — `create.php`

Form nama, kategori, harga, dan stok dengan validasi server-side serta pola PRG.

![Tambah obat](public/assets/screenshots/ss_01_create.png)

### Halaman Edit — `edit.php`

Form terisi otomatis dari database, validasi sama dengan create, UPDATE memakai prepared statement.

![Edit obat](public/assets/screenshots/ss_07_edit.png)

### Tema Terang

Tema dapat diganti lewat tombol di sidebar dan tersimpan di browser.

![Tema terang](public/assets/screenshots/ss_11_light.png)

## 🔐 Ringkasan Keamanan

| Ancaman | Kontrol yang Diterapkan |
|---|---|
| SQL Injection | PDO prepared statement pada INSERT, SELECT by ID, UPDATE, DELETE, dan search |
| XSS | `htmlspecialchars($x, ENT_QUOTES, "UTF-8")` melalui fungsi `e()` pada semua output |
| CSRF | Token `random_bytes(32)` di session, dicek dengan `hash_equals` pada create, edit, delete |
| Data ganda saat refresh | Pola PRG (`header("Location: ...")` + `exit`) |
| Data tidak valid | Validasi server-side + `UNIQUE` pada kolom nama di database |
| Akses delete lewat URL | Hanya menerima POST, selain itu HTTP 405 |

## 🚀 Cara Menjalankan (XAMPP)

1. Jalankan **Apache** dan **MySQL** di XAMPP Control Panel.
2. Salin folder project ke `C:\xampp\htdocs\apotek-manager`.
3. Buka `http://localhost/phpmyadmin`, pilih tab **Import**, lalu import file `database/store_db.sql`.
4. Cek `config/db.php` (default: user `root`, password kosong, database `store_db`).
5. Buka `http://localhost/apotek-manager/public/`.

## 📁 Struktur Project

```
apotek-manager/
├── config/
│   ├── db.php             # koneksi PDO
│   └── helpers.php        # e(), CSRF, validasi, layout, form
├── public/
│   ├── index.php          # READ + search/filter + statistik
│   ├── create.php         # CREATE + validasi + PRG
│   ├── edit.php           # READ one + UPDATE
│   ├── delete.php         # DELETE + CSRF (POST only)
│   └── assets/
│       ├── style.css      # Box Model, Flexbox, dark/light theme
│       ├── app.js         # toggle tema + alert
│       └── screenshots/   # bukti pengujian
├── database/store_db.sql
└── README.md
```

## 🧪 Skenario Pengujian

### Skenario 1 — Create Sukses

| Atribut | Detail |
|---|---|
| Aksi / Data Masukan | Nama: Vitamin D3 1000 IU, Kategori: Vitamin & Suplemen, Harga: 60000, Stok: 25 |
| Perilaku yang Diharapkan | Tersimpan di database, redirect ke index.php, banner sukses muncul |
| Hasil Uji | LULUS ✅ |
| Bukti | Lihat gambar di bawah |

![Create Sukses](public/assets/screenshots/ss_01_create.png)

### Skenario 2 — Validasi Nama Pendek

| Atribut | Detail |
|---|---|
| Aksi / Data Masukan | Nama: Ab, harga dan stok valid |
| Perilaku yang Diharapkan | Ditolak, pesan "Nama minimal 3 karakter.", data tidak tersimpan |
| Hasil Uji | LULUS ✅ |
| Bukti | Lihat gambar di bawah |

![Validasi Nama Pendek](public/assets/screenshots/ss_02_nama_pendek.png)

### Skenario 3 — Validasi Harga dan Stok Negatif

| Atribut | Detail |
|---|---|
| Aksi / Data Masukan | Harga: -5000, Stok: -3 |
| Perilaku yang Diharapkan | Ditolak, pesan "Harga harus > 0." dan "Stok tidak boleh negatif." |
| Hasil Uji | LULUS ✅ |
| Bukti | Lihat gambar di bawah |

![Validasi Harga dan Stok Negatif](public/assets/screenshots/ss_03_harga_stok.png)

### Skenario 4 — Refresh Setelah Create (PRG)

| Atribut | Detail |
|---|---|
| Aksi / Data Masukan | Tekan F5 setelah berhasil menambah obat |
| Perilaku yang Diharapkan | Tidak ada data ganda karena memakai Post-Redirect-Get |
| Hasil Uji | LULUS ✅ |
| Bukti | Lihat gambar di bawah |

![Refresh Setelah Create (PRG)](public/assets/screenshots/ss_04_refresh.png)

### Skenario 5 — Cegah XSS

| Atribut | Detail |
|---|---|
| Aksi / Data Masukan | Nama: <b>Promo</b> |
| Perilaku yang Diharapkan | Tag tampil sebagai teks biasa, bukan huruf tebal |
| Hasil Uji | LULUS ✅ |
| Bukti | Lihat gambar di bawah |

![Cegah XSS](public/assets/screenshots/ss_05_xss.png)

### Skenario 6 — Nama Duplikat

| Atribut | Detail |
|---|---|
| Aksi / Data Masukan | Nama: Paracetamol 500 mg (Strip) |
| Perilaku yang Diharapkan | Ditolak, pesan "Nama produk sudah terdaftar." |
| Hasil Uji | LULUS ✅ |
| Bukti | Lihat gambar di bawah |

![Nama Duplikat](public/assets/screenshots/ss_06_duplikat.png)

### Skenario 7 — Update Data

| Atribut | Detail |
|---|---|
| Aksi / Data Masukan | Edit Termometer Digital, ubah harga menjadi 50000 |
| Perilaku yang Diharapkan | Data berubah, redirect ke index.php, banner update muncul |
| Hasil Uji | LULUS ✅ |
| Bukti | Lihat gambar di bawah |

![Update Data](public/assets/screenshots/ss_07_edit.png)

### Skenario 8 — Delete dengan CSRF

| Atribut | Detail |
|---|---|
| Aksi / Data Masukan | Klik Hapus lalu OK pada konfirmasi |
| Perilaku yang Diharapkan | Data terhapus, banner hapus muncul |
| Hasil Uji | LULUS ✅ |
| Bukti | Lihat gambar di bawah |

![Delete dengan CSRF](public/assets/screenshots/ss_08_delete.png)

### Skenario 9 — Akses delete.php via GET

| Atribut | Detail |
|---|---|
| Aksi / Data Masukan | Buka http://localhost/apotek-manager/public/delete.php langsung |
| Perilaku yang Diharapkan | Sistem menolak, HTTP 405 Method Not Allowed |
| Hasil Uji | LULUS ✅ |
| Bukti | Lihat gambar di bawah |

![Akses delete.php via GET](public/assets/screenshots/ss_09_405.png)

### Skenario 10 — Responsif Layar Sempit

| Atribut | Detail |
|---|---|
| Aksi / Data Masukan | Perkecil lebar browser / mode HP |
| Perilaku yang Diharapkan | Sidebar berubah jadi bar atas, kartu membungkus rapi |
| Hasil Uji | LULUS ✅ |
| Bukti | Lihat gambar di bawah |

![Responsif Layar Sempit](public/assets/screenshots/ss_10_responsive.png)


### ✅ Checklist Pengujian

- [ ] 1. Create item valid — Banner hijau + obat tampil di grid
- [ ] 2. Nama < 3 karakter — Pesan "Nama minimal 3 karakter."
- [ ] 3. Harga nol / negatif — Pesan "Harga harus > 0."
- [ ] 4. Stok negatif — Pesan "Stok tidak boleh negatif."
- [ ] 5. Nama duplikat — Pesan "Nama produk sudah terdaftar."
- [ ] 6. Injeksi XSS — Tag tampil sebagai teks
- [ ] 7. Refresh setelah create (PRG) — Tidak ada data ganda
- [ ] 8. Update item — Nilai baru tampil + banner
- [ ] 9. Delete item — Obat hilang + banner
- [ ] 10. Delete lewat GET — HTTP 405
- [ ] 11. Token CSRF palsu — HTTP 403
- [ ] 12. Pencarian + SQL injection — Filter akurat, input aman
- [ ] 13. Responsivitas layar — Satu kolom di ±400px

