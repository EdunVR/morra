# Requirements Document: Purchase Order Installment Payment

## Introduction

Implementasi sistem pembayaran cicilan untuk Purchase Order, mirip dengan sistem yang sudah ada di Sales Invoice. Fitur ini memungkinkan pembayaran PO secara bertahap dengan tracking history pembayaran.

## Glossary

-   **Purchase Order (PO)**: Pesanan pembelian dari supplier
-   **Installment Payment**: Pembayaran cicilan/bertahap
-   **Payment History**: Riwayat pembayaran yang telah dilakukan
-   **Hutang**: Kewajiban pembayaran kepada supplier
-   **Status PO**: Status pembayaran PO (pending, partial, paid, cancelled)

## Requirements

### Requirement 1: Payment History Tracking

**User Story:** Sebagai user, saya ingin mencatat setiap pembayaran PO agar dapat melacak riwayat pembayaran

#### Acceptance Criteria

1. WHEN user melakukan pembayaran PO, THE System SHALL menyimpan record pembayaran ke tabel payment history
2. THE System SHALL mencatat tanggal pembayaran, jumlah, jenis pembayaran, dan bukti transfer
3. THE System SHALL menampilkan total yang sudah dibayar dan sisa yang belum dibayar
4. THE System SHALL mendukung upload bukti pembayaran (gambar/PDF)
5. THE System SHALL mengkompress gambar bukti pembayaran untuk menghemat storage

### Requirement 2: Installment Payment Support

**User Story:** Sebagai user, saya ingin membayar PO secara bertahap agar dapat mengelola cash flow

#### Acceptance Criteria

1. WHEN PO dibuat, THE System SHALL set status = 'pending'
2. WHEN pembayaran pertama dilakukan, THE System SHALL update status = 'partial' jika belum lunas
3. WHEN total pembayaran = total PO, THE System SHALL update status = 'paid'
4. THE System SHALL menghitung sisa pembayaran secara otomatis
5. THE System SHALL mencegah pembayaran melebihi total PO

### Requirement 3: Payment Modal UI

**User Story:** Sebagai user, saya ingin interface yang mudah untuk melakukan pembayaran PO

#### Acceptance Criteria

1. THE System SHALL menampilkan modal pembayaran dengan informasi PO
2. THE System SHALL menampilkan total PO, sudah dibayar, dan sisa
3. THE System SHALL menyediakan input untuk jumlah pembayaran
4. THE System SHALL menyediakan pilihan jenis pembayaran (Cash/Transfer)
5. THE System SHALL menyediakan upload bukti pembayaran untuk Transfer
6. THE System SHALL menampilkan preview bukti pembayaran sebelum upload

### Requirement 4: Payment History Modal

**User Story:** Sebagai user, saya ingin melihat riwayat pembayaran PO yang telah dilakukan

#### Acceptance Criteria

1. THE System SHALL menampilkan list semua pembayaran untuk PO tertentu
2. THE System SHALL menampilkan tanggal, jumlah, jenis, dan penerima untuk setiap pembayaran
3. THE System SHALL menyediakan tombol untuk melihat bukti pembayaran
4. THE System SHALL menampilkan total yang sudah dibayar
5. THE System SHALL menampilkan bukti pembayaran dalam modal terpisah

### Requirement 5: Hutang Integration

**User Story:** Sebagai user, saya ingin hutang terupdate otomatis saat pembayaran PO dilakukan

#### Acceptance Criteria

1. WHEN PO dibuat, THE System SHALL create record hutang
2. WHEN pembayaran dilakukan, THE System SHALL update sisa_hutang
3. WHEN PO lunas, THE System SHALL update status hutang = 'lunas'
4. THE System SHALL maintain konsistensi data antara PO dan hutang
5. THE System SHALL mencatat pembayaran di jurnal keuangan

### Requirement 6: Status Management

**User Story:** Sebagai user, saya ingin status PO mencerminkan kondisi pembayaran yang akurat

#### Acceptance Criteria

1. THE System SHALL menampilkan status badge dengan warna berbeda untuk setiap status
2. THE System SHALL update status secara otomatis berdasarkan pembayaran
3. THE System SHALL menampilkan tombol aksi sesuai dengan status PO
4. THE System SHALL mencegah edit PO yang sudah dibayar
5. THE System SHALL allow pembayaran hanya untuk PO dengan status pending/partial

### Requirement 7: Data Validation

**User Story:** Sebagai user, saya ingin sistem mencegah input data pembayaran yang tidak valid

#### Acceptance Criteria

1. THE System SHALL validate jumlah pembayaran > 0
2. THE System SHALL validate jumlah pembayaran <= sisa yang belum dibayar
3. THE System SHALL validate format file bukti pembayaran (jpg, png, pdf)
4. THE System SHALL validate ukuran file bukti pembayaran (max 5MB)
5. THE System SHALL menampilkan error message yang jelas untuk setiap validasi error

### Requirement 8: Performance & UX

**User Story:** Sebagai user, saya ingin proses pembayaran berjalan cepat dan smooth

#### Acceptance Criteria

1. THE System SHALL menampilkan loading indicator saat proses pembayaran
2. THE System SHALL mengkompress gambar bukti pembayaran sebelum upload
3. THE System SHALL menampilkan toast notification untuk feedback
4. THE System SHALL reload data PO setelah pembayaran berhasil
5. THE System SHALL menutup modal secara otomatis setelah pembayaran berhasil
