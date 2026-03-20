# Rencana Implementasi: Sales Invoice Automation

## Ikhtisar

Implementasi otomatisasi pembuatan Sales Invoice saat Inventory Transfer selesai diproses, mencakup service layer, penomoran invoice, cetak PDF, dan pembaruan tampilan detail invoice.

## Tugas

- [x] 1. Instal dependensi dan buat service layer
  - Jalankan `composer require barryvdh/laravel-dompdf` untuk package PDF
  - Jalankan `composer require --dev giorgiosironi/eris` untuk property-based testing
  - Buat direktori `app/Services/`
  - _Persyaratan: 1.1, 5.1_

- [x] 2. Implementasi `InvoiceNumberGenerator`
  - [x] 2.1 Buat file `app/Services/InvoiceNumberGenerator.php`
    - Implementasi method `generate(): string` yang menghasilkan format `Inv-SO{NNN}`
    - Gunakan `SalesInvoice::withTrashed()->latest('id')->first()` sebagai basis urutan
    - Padding nomor urut 3 digit dengan nol di depan
    - _Persyaratan: 6.1, 6.2, 6.3_

  - [ ]* 2.2 Tulis property test untuk Property 2: Format nomor invoice valid dan unik
    - **Property 2: Format nomor invoice valid dan unik**
    - **Validates: Persyaratan 1.2, 6.1, 6.2, 6.3**
    - Buat `tests/Unit/InvoiceNumberGeneratorPropertyTest.php` menggunakan Eris
    - Verifikasi setiap nomor yang dihasilkan cocok dengan pola `^Inv-SO\d{3}$`
    - Verifikasi tidak ada dua invoice yang memiliki `inv_number` yang sama

  - [ ]* 2.3 Tulis unit test untuk `InvoiceNumberGenerator`
    - Buat `tests/Unit/InvoiceNumberGeneratorTest.php`
    - Verifikasi format `Inv-SO001`, `Inv-SO002`, dst.
    - Verifikasi nomor tetap unik setelah ada invoice yang di-soft-delete
    - _Persyaratan: 6.1, 6.2, 6.3_

- [x] 3. Implementasi `SalesInvoiceService`
  - [x] 3.1 Buat file `app/Services/SalesInvoiceService.php`
    - Implementasi method `createFromTransfer(InventoryTransfer $transfer): ?SalesInvoice`
    - Cek duplikat: `SalesInvoice::where('sales_order_id', $so->id)->exists()`
    - Jika duplikat: log warning dan kembalikan `null`
    - Kalkulasi `subtotal = Σ(quantity × unit_price)` dari item SO
    - Kalkulasi `tax = round(subtotal * 0.11, 2)`
    - Kalkulasi `total = subtotal + tax - discount`
    - Buat record `sales_invoices` dengan status `issued` dan `payment_status` `unpaid`
    - Buat record `sales_invoice_items` dari item SO
    - Update status `sales_orders` → `completed`
    - _Persyaratan: 1.1, 1.2, 1.3, 1.4, 1.5, 1.6, 2.1, 2.2, 2.3, 2.4, 5.4_

  - [ ]* 3.2 Tulis property test untuk Property 1: Invoice terbuat tepat satu kali per IT
    - **Property 1: Invoice terbuat tepat satu kali per Inventory Transfer**
    - **Validates: Persyaratan 1.1, 1.6**
    - Buat `tests/Feature/SalesInvoiceServicePropertyTest.php` menggunakan Eris
    - Verifikasi pemanggilan `createFromTransfer` dua kali pada transfer yang sama tetap menghasilkan tepat satu invoice

  - [ ]* 3.3 Tulis property test untuk Property 3: Data invoice sesuai SO dan Transfer
    - **Property 3: Data invoice sesuai dengan Sales Order dan Transfer terkait**
    - **Validates: Persyaratan 1.3, 1.4**
    - Verifikasi `customer_id`, `sales_order_id`, item, dan `invoice_date` sesuai dengan data SO dan transfer

  - [ ]* 3.4 Tulis property test untuk Property 4: Status awal invoice selalu issued dan unpaid
    - **Property 4: Status awal invoice selalu issued dan unpaid**
    - **Validates: Persyaratan 1.5**
    - Verifikasi setiap invoice baru memiliki `status = issued` dan `payment_status = unpaid`

  - [ ]* 3.5 Tulis property test untuk Property 5: Kalkulasi nilai invoice selalu benar
    - **Property 5: Kalkulasi nilai invoice selalu benar**
    - **Validates: Persyaratan 2.1, 2.2, 2.3, 2.4**
    - Buat `tests/Unit/SalesInvoiceCalculationPropertyTest.php` menggunakan Eris
    - Generate random `quantity` dan `unit_price` positif, verifikasi `subtotal`, `tax`, dan `total`

  - [ ]* 3.6 Tulis property test untuk Property 6: SO status completed setelah invoice dibuat
    - **Property 6: Status Sales Order berubah menjadi completed setelah invoice dibuat**
    - **Validates: Persyaratan 5.4**
    - Verifikasi `sales_orders.status = completed` setelah `createFromTransfer` berhasil

  - [ ]* 3.7 Tulis unit test untuk `SalesInvoiceService`
    - Buat `tests/Feature/SalesInvoiceServiceTest.php`
    - Verifikasi skip duplikat + log warning dicatat
    - Verifikasi kalkulasi dengan contoh konkret
    - _Persyaratan: 1.6, 2.1, 2.2, 2.3_

- [x] 4. Checkpoint — Pastikan semua test lulus
  - Pastikan semua test lulus, tanyakan kepada user jika ada pertanyaan.

- [x] 5. Modifikasi `InventoryController::process()`
  - [x] 5.1 Tambahkan pemanggilan `SalesInvoiceService` di dalam `DB::transaction` yang sudah ada
    - Inject `app(SalesInvoiceService::class)->createFromTransfer($transfer)` setelah update transfer
    - Tambahkan try-catch di sekitar transaction untuk log error dan flash pesan ke user
    - _Persyaratan: 1.1, 5.1, 5.2_

  - [ ]* 5.2 Tulis property test untuk Property 7: Atomicity rollback saat kegagalan
    - **Property 7: Atomicity — kegagalan invoice me-rollback seluruh transaksi**
    - **Validates: Persyaratan 5.1, 5.2**
    - Buat `tests/Feature/InventoryTransferAtomicityPropertyTest.php` menggunakan Eris
    - Simulasi exception di dalam `createFromTransfer`, verifikasi tidak ada perubahan parsial tersimpan

  - [ ]* 5.3 Tulis unit test untuk rollback transaction
    - Buat `tests/Feature/InventoryControllerTest.php`
    - Verifikasi rollback saat invoice gagal dibuat (simulasi exception)
    - _Persyaratan: 5.1, 5.2_

- [x] 6. Tambahkan relasi `inventoryTransfer` pada model `SalesInvoice`
  - Tambahkan method `inventoryTransfer(): HasOneThrough` di `app/Models/SalesInvoice.php`
  - Gunakan `hasOneThrough(InventoryTransfer::class, SalesOrder::class, ...)` sesuai desain
  - _Persyaratan: 3.6_

- [x] 7. Tambahkan route dan method PDF di `InvoicingController`
  - [x] 7.1 Tambahkan route baru di `routes/web.php`
    - `Route::get('/invoicing/sales/{invoice}/pdf', [InvoicingController::class, 'salesPdf'])->name('invoicing.sales.pdf')`
    - _Persyaratan: 4.1, 4.5, 4.6_

  - [x] 7.2 Tambahkan method `salesPdf(SalesInvoice $invoice)` di `InvoicingController`
    - Load relasi: `invoice`, `salesOrder`, `customer`, `items.product`, `inventoryTransfer.creator`
    - Render view `invoicing.sales.pdf` dan generate PDF dengan DomPDF
    - Set ukuran kertas A4 portrait
    - Kembalikan response stream PDF
    - _Persyaratan: 4.1, 4.5, 4.6_

  - [ ]* 7.3 Tulis unit test untuk PDF route
    - Buat `tests/Feature/InvoicingControllerTest.php`
    - Verifikasi HTTP 404 saat invoice tidak ditemukan
    - Verifikasi response Content-Type `application/pdf` saat invoice ditemukan
    - _Persyaratan: 4.6_

- [x] 8. Buat template PDF `resources/views/invoicing/sales/pdf.blade.php`
  - Tambahkan header perusahaan: nama "PT. Baktiya Utama Indonesia" dan alamat
  - Tambahkan bagian detail: Nomor Invoice, Nomor SO, nama Customer, tanggal invoice
  - Tambahkan tabel item: nama produk, QTY, harga satuan, total per item
  - Tambahkan ringkasan: subtotal, PPN 11%, total
  - Tambahkan dua kolom TTD: kiri "Sales / Pemberi Barang" (nama dari `$transfer->creator->name`), kanan "Penerima Barang" (nama dari `$transfer->receiver_name`)
  - _Persyaratan: 4.1, 4.2, 4.3, 4.4, 4.5_

  - [ ]* 8.1 Tulis property test untuk Property 10: Template PDF memuat semua konten
    - **Property 10: Template PDF memuat semua konten yang diperlukan**
    - **Validates: Persyaratan 4.1, 4.2, 4.3, 4.4**
    - Buat `tests/Feature/SalesInvoicePdfPropertyTest.php` menggunakan Eris
    - Render template dengan data acak, verifikasi semua elemen wajib ada dalam HTML output

- [x] 9. Perbarui view detail invoice `resources/views/invoicing/sales/show.blade.php`
  - Tambahkan tombol "Cetak PDF" yang mengarah ke route `invoicing.sales.pdf`
  - Tambahkan bagian informasi TTD: nama Sales (dari `$invoice->inventoryTransfer->creator->name`) dan timestamp konfirmasi
  - Tambahkan nama penerima (dari `$invoice->inventoryTransfer->receiver_name`) dan timestamp konfirmasi
  - Perbarui `salesShow()` di `InvoicingController` untuk eager load relasi `inventoryTransfer.creator`
  - _Persyaratan: 3.1, 3.6_

  - [ ]* 9.1 Tulis property test untuk Property 9: Halaman detail memuat semua informasi
    - **Property 9: Halaman detail invoice memuat semua informasi yang diperlukan**
    - **Validates: Persyaratan 3.1, 3.6**
    - Buat `tests/Feature/SalesInvoiceDetailPropertyTest.php` menggunakan Eris
    - Verifikasi response HTML mengandung semua elemen wajib untuk berbagai data invoice

  - [ ]* 9.2 Tulis property test untuk Property 8: Indikator warna status pembayaran konsisten
    - **Property 8: Indikator warna status pembayaran selalu konsisten**
    - **Validates: Persyaratan 3.3, 3.4, 3.5**
    - Verifikasi badge `bg-danger`, `bg-warning`, `bg-success` sesuai dengan `payment_status`

- [x] 10. Checkpoint akhir — Pastikan semua test lulus
  - Pastikan semua test lulus, tanyakan kepada user jika ada pertanyaan.

## Catatan

- Tugas bertanda `*` bersifat opsional dan dapat dilewati untuk MVP yang lebih cepat
- Setiap tugas mereferensikan persyaratan spesifik untuk keterlacakan
- Property tests menggunakan library **eris/eris** dengan minimum 100 iterasi
- Unit tests difokuskan pada contoh konkret, edge case, dan kondisi error
- Seluruh operasi transfer + pembuatan invoice harus berjalan dalam satu `DB::transaction`
