# 🚀 Quick Start - Rekonsiliasi Bank

## Setup (5 menit)

### 1. Jalankan Migration

```bash
php artisan migrate
```

### 2. Akses Fitur

```
Menu: Keuangan (F&A) → Rekonsiliasi Bank
URL: /admin/finance/rekonsiliasi
```

---

## 📖 Cara Pakai (3 Langkah Mudah)

### Step 1: Buat Rekonsiliasi

1. Klik **"Buat Rekonsiliasi"**
2. Pilih **Outlet** dan **Rekening Bank**
3. Masukkan **Saldo Bank** (dari rekening koran)
4. Masukkan **Saldo Buku** (dari sistem)
5. Klik **"Simpan"**

### Step 2: Selesaikan

1. Review data yang sudah dibuat
2. Klik **"Selesai"** jika sudah benar

### Step 3: Approve (Opsional)

1. Manager klik **"Setujui"**
2. Selesai! ✅

---

## 🎯 Fitur Utama

| Fitur              | Deskripsi                                |
| ------------------ | ---------------------------------------- |
| **Multi-Outlet**   | Setiap outlet punya rekonsiliasi sendiri |
| **Multi-Bank**     | Support banyak rekening bank             |
| **Auto Calculate** | Selisih otomatis terhitung               |
| **Status Flow**    | Draft → Completed → Approved             |
| **Export PDF**     | Download laporan siap print              |
| **Filter**         | Filter by outlet, status, periode, bank  |

---

## 📊 Status Workflow

```
Draft (Kuning)
  ↓ [Klik "Selesai"]
Completed (Hijau)
  ↓ [Klik "Setujui"]
Approved (Biru)
```

**Aturan:**

-   ✅ Draft bisa diedit & dihapus
-   ✅ Completed tidak bisa diedit, bisa dihapus
-   ❌ Approved tidak bisa diedit & dihapus

---

## 🔍 Tips & Tricks

### Tip 1: Cek Selisih

Jika ada selisih, cek:

-   ✅ Transaksi yang belum tercatat di buku
-   ✅ Biaya admin bank
-   ✅ Bunga bank
-   ✅ Cek yang belum dicairkan
-   ✅ Setoran dalam perjalanan

### Tip 2: Rekonsiliasi Rutin

-   📅 Lakukan setiap akhir bulan
-   📅 Jangan tunda lebih dari 1 bulan
-   📅 Simpan rekening koran sebagai bukti

### Tip 3: Dokumentasi

-   📄 Export PDF setiap selesai rekonsiliasi
-   📄 Simpan sebagai arsip
-   📄 Attach rekening koran asli

---

## ❓ FAQ

**Q: Apa itu rekonsiliasi bank?**
A: Proses mencocokkan saldo bank dengan saldo buku perusahaan.

**Q: Kenapa ada selisih?**
A: Bisa karena transaksi yang belum tercatat, biaya admin, bunga, atau error input.

**Q: Berapa sering harus rekonsiliasi?**
A: Minimal 1x sebulan, idealnya setiap minggu.

**Q: Bisa hapus rekonsiliasi yang sudah approved?**
A: Tidak bisa, untuk menjaga integritas data.

**Q: Bagaimana jika salah input?**
A: Jika masih Draft/Completed, bisa diedit. Jika sudah Approved, buat rekonsiliasi baru dengan koreksi.

---

## 🆘 Troubleshooting

| Problem            | Solution                             |
| ------------------ | ------------------------------------ |
| Menu tidak muncul  | Clear cache browser (Ctrl+F5)        |
| Error saat save    | Cek koneksi internet & session login |
| PDF tidak download | Cek popup blocker browser            |
| Data tidak muncul  | Cek filter yang aktif                |

---

## 📞 Butuh Bantuan?

Hubungi tim IT atau buat ticket support.

---

**Selamat menggunakan fitur Rekonsiliasi Bank! 🎉**
