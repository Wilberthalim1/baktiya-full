# Dokumen Persyaratan

## Pendahuluan

Fitur **Invoice Penjualan Otomatis** pada sistem ERP PT. Baktiya Utama Indonesia bertujuan untuk mengotomatiskan pembuatan Invoice Penjualan segera setelah proses Inventory Transfer selesai dilakukan. Fitur ini mencakup pembuatan nomor invoice otomatis, kalkulasi nilai transaksi, tampilan detail invoice, serta cetak PDF invoice yang memuat kolom tanda tangan Sales (pemberi) dan Customer/Penerima Barang.

Alur bisnis yang didukung:
**Sales Order dibuat → Inventory Transfer diproses → Invoice Penjualan otomatis terbuat → Customer melunasi invoice**

---

## Glosarium

- **System**: Sistem ERP PT. Baktiya Utama Indonesia berbasis Laravel
- **Invoice_Generator**: Komponen sistem yang bertanggung jawab membuat Invoice Penjualan secara otomatis
- **Sales_Invoice**: Dokumen tagihan penjualan yang diterbitkan kepada Customer setelah barang dikirim
- **Inventory_Transfer**: Dokumen perpindahan stok barang dari gudang ke Customer berdasarkan Sales Order
- **Sales_Order**: Dokumen pesanan penjualan yang menjadi acuan Invoice Penjualan
- **Customer**: Pelanggan PT. Baktiya Utama Indonesia yang menerima barang dan tagihan
- **PDF_Printer**: Komponen sistem yang menghasilkan dokumen PDF Invoice Penjualan siap cetak
- **TTD_Sales**: Kolom tanda tangan beserta nama dan timestamp pihak Sales (pemberi barang)
- **TTD_Penerima**: Kolom tanda tangan beserta nama dan timestamp pihak penerima barang (Customer/Ekspedisi)
- **PPN**: Pajak Pertambahan Nilai sebesar 11% dari subtotal
- **Nomor_Invoice**: Nomor unik invoice dengan format `Inv-SO{nomor_urut}` (contoh: `Inv-SO001`)

---

## Persyaratan

### Persyaratan 1: Pembuatan Invoice Penjualan Otomatis

**User Story:** Sebagai staf gudang/inventory, saya ingin Invoice Penjualan terbuat secara otomatis ketika Inventory Transfer selesai diproses, sehingga proses penagihan ke Customer dapat segera berjalan tanpa input manual tambahan.

#### Kriteria Penerimaan

1. WHEN status Inventory Transfer berubah menjadi `transferred`, THE Invoice_Generator SHALL membuat satu Sales_Invoice baru secara otomatis dalam satu database transaction yang sama.
2. WHEN Invoice_Generator membuat Sales_Invoice baru, THE Invoice_Generator SHALL mengisi field `inv_number` dengan format `Inv-SO{nomor_urut_3_digit}` yang unik dan berurutan (contoh: `Inv-SO001`, `Inv-SO002`).
3. WHEN Invoice_Generator membuat Sales_Invoice baru, THE Invoice_Generator SHALL menyalin `customer_id`, `sales_order_id`, dan seluruh item beserta `quantity` dan `unit_price` dari Sales_Order yang terkait.
4. WHEN Invoice_Generator membuat Sales_Invoice baru, THE Invoice_Generator SHALL mengisi `invoice_date` dengan tanggal dan waktu saat Inventory Transfer selesai diproses.
5. WHEN Invoice_Generator membuat Sales_Invoice baru, THE Invoice_Generator SHALL menetapkan status awal Sales_Invoice sebagai `issued` dan `payment_status` sebagai `unpaid`.
6. IF Sales_Invoice untuk Sales_Order yang sama sudah ada, THEN THE Invoice_Generator SHALL membatalkan pembuatan invoice duplikat dan mencatat log peringatan tanpa menggagalkan proses Inventory Transfer.

---

### Persyaratan 2: Kalkulasi Nilai Invoice Otomatis

**User Story:** Sebagai staf keuangan, saya ingin nilai subtotal, PPN, dan total invoice dikalkulasi secara otomatis dari data Sales Order, sehingga tidak ada risiko kesalahan hitung manual.

#### Kriteria Penerimaan

1. WHEN Invoice_Generator membuat Sales_Invoice, THE Invoice_Generator SHALL menghitung `subtotal` sebagai penjumlahan dari `quantity × unit_price` seluruh item Sales_Order.
2. WHEN Invoice_Generator membuat Sales_Invoice, THE Invoice_Generator SHALL menghitung `tax` (PPN) sebesar 11% dari nilai `subtotal`.
3. WHEN Invoice_Generator membuat Sales_Invoice, THE Invoice_Generator SHALL menghitung `total` sebagai `subtotal + tax - discount`, di mana `discount` diambil dari Sales_Order.
4. THE System SHALL menyimpan nilai `subtotal`, `tax`, `discount`, dan `total` dalam tipe data desimal dengan presisi 2 angka di belakang koma.

---

### Persyaratan 3: Tampilan Detail Invoice Penjualan

**User Story:** Sebagai staf penjualan atau manajer, saya ingin melihat detail lengkap Invoice Penjualan, sehingga saya dapat memantau status tagihan dan informasi transaksi.

#### Kriteria Penerimaan

1. WHEN pengguna mengakses halaman detail Sales_Invoice, THE System SHALL menampilkan informasi: Nomor Invoice, Nomor SO referensi, nama Customer, tanggal invoice, daftar item (nama produk, QTY, harga satuan, total per item), subtotal, PPN, total, dan status pembayaran.
2. WHEN pengguna mengakses halaman daftar Sales_Invoice, THE System SHALL menampilkan daftar invoice dengan kolom: Nomor Invoice, Customer, tanggal invoice, total, dan status pembayaran, diurutkan dari yang terbaru.
3. WHILE status `payment_status` adalah `unpaid`, THE System SHALL menampilkan indikator visual berwarna merah pada baris invoice tersebut.
4. WHILE status `payment_status` adalah `partial`, THE System SHALL menampilkan indikator visual berwarna kuning pada baris invoice tersebut.
5. WHILE status `payment_status` adalah `paid`, THE System SHALL menampilkan indikator visual berwarna hijau pada baris invoice tersebut.
6. WHEN pengguna mengakses halaman detail Sales_Invoice, THE System SHALL menampilkan informasi TTD_Sales (nama pemberi dan timestamp konfirmasi) dan TTD_Penerima (nama penerima dan timestamp konfirmasi) yang diambil dari data Inventory Transfer terkait.

---

### Persyaratan 4: Cetak PDF Invoice Penjualan

**User Story:** Sebagai staf penjualan, saya ingin mencetak Invoice Penjualan dalam format PDF, sehingga tersedia dokumen fisik yang dapat ditandatangani oleh Sales dan Customer/Penerima Barang.

#### Kriteria Penerimaan

1. WHEN pengguna memilih aksi cetak pada Sales_Invoice, THE PDF_Printer SHALL menghasilkan dokumen PDF yang memuat header perusahaan PT. Baktiya Utama Indonesia (nama perusahaan, alamat, dan logo jika tersedia).
2. WHEN PDF_Printer menghasilkan dokumen PDF, THE PDF_Printer SHALL memuat bagian detail transaksi yang berisi: Nomor Invoice, Nomor SO referensi, nama Customer, tanggal invoice, tabel item (nama produk, QTY, harga satuan, total per item), subtotal, PPN, dan total.
3. WHEN PDF_Printer menghasilkan dokumen PDF, THE PDF_Printer SHALL memuat kolom TTD_Sales dengan label "Sales / Pemberi Barang", nama Sales, dan area tanda tangan.
4. WHEN PDF_Printer menghasilkan dokumen PDF, THE PDF_Printer SHALL memuat kolom TTD_Penerima dengan label "Penerima Barang", nama penerima, dan area tanda tangan.
5. WHEN PDF_Printer menghasilkan dokumen PDF, THE PDF_Printer SHALL memformat dokumen dengan ukuran kertas A4 dalam orientasi portrait agar dapat dicetak sebagai dokumen fisik.
6. IF data Sales_Invoice tidak ditemukan saat permintaan cetak diterima, THEN THE PDF_Printer SHALL mengembalikan respons HTTP 404 dengan pesan kesalahan yang deskriptif.

---

### Persyaratan 5: Integritas Data dan Konsistensi Transaksi

**User Story:** Sebagai administrator sistem, saya ingin pembuatan invoice dan proses inventory transfer berjalan dalam satu transaksi database, sehingga tidak ada data yang tidak konsisten jika terjadi kegagalan di tengah proses.

#### Kriteria Penerimaan

1. WHEN proses Inventory Transfer dan pembuatan Sales_Invoice dijalankan, THE System SHALL membungkus seluruh operasi dalam satu database transaction sehingga jika salah satu operasi gagal, seluruh perubahan akan di-rollback.
2. IF terjadi kegagalan saat pembuatan Sales_Invoice di dalam transaction, THEN THE System SHALL melakukan rollback seluruh perubahan termasuk update status Inventory Transfer dan mencatat pesan error ke log aplikasi.
3. THE System SHALL memastikan setiap `inv_number` pada tabel `sales_invoices` bersifat unik dengan constraint `UNIQUE` pada level database.
4. WHEN Sales_Invoice berhasil dibuat, THE System SHALL memperbarui status Sales_Order menjadi `completed`.

---

### Persyaratan 6: Nomor Invoice Unik dan Berurutan

**User Story:** Sebagai staf keuangan, saya ingin nomor invoice dibuat secara otomatis dengan format yang konsisten dan tidak duplikat, sehingga mudah dilacak dan diarsipkan.

#### Kriteria Penerimaan

1. THE Invoice_Generator SHALL menghasilkan `inv_number` dengan format `Inv-SO{NNN}` di mana `{NNN}` adalah nomor urut 3 digit yang dipadding dengan nol di depan (contoh: `Inv-SO001`).
2. WHEN Invoice_Generator menghasilkan nomor invoice baru, THE Invoice_Generator SHALL menggunakan nomor urut berikutnya berdasarkan jumlah total Sales_Invoice yang sudah ada termasuk yang telah dihapus (soft-deleted).
3. THE System SHALL memastikan proses penomoran invoice berjalan dengan benar meskipun ada invoice yang dihapus (soft-delete), sehingga tidak ada nomor yang terulang.
