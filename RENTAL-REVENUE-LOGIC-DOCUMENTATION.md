# Logika Pendapatan Sewa & Status Aktif Berlangsung

## Overview Sistem

Sistem tracking pendapatan sewa BMN dengan status sewa yang jelas dan tracking pembayaran per periode.

---

## 1. Status Sewa (Workflow)

### State Diagram
```
┌─────────┐
│  DRAFT  │ ← Baru dibuat, data belum lengkap
└────┬────┘
     │ (Admin melengkapi data)
     ▼
┌─────────┐
│ REVIEW  │ ← Dalam proses review/persetujuan
└────┬────┘
     │ (KPKNL approve)
     ▼
┌──────────┐
│ APPROVED │ ← Disetujui, menunggu pembayaran
└────┬─────┘
     │ (Pembayaran masuk)
     ▼
┌────────┐
│ ACTIVE │ ← Sewa sedang berlangsung (AKTIF!)
└────┬───┘
     │ (Tanggal berakhir tercapai)
     ▼
┌───────────┐
│ COMPLETED │ ← Sewa selesai
└───────────┘

     OR

┌───────────┐
│ CANCELLED │ ← Dibatalkan kapan saja
└───────────┘

     OR

┌──────────┐
│ EXPIRED  │ ← Kadaluarsa (tidak diperpanjang)
└──────────┘
```

### Status Details

#### 1. **DRAFT**
- **Kondisi**: Data baru dibuat
- **Karakteristik**:
  - Data belum lengkap
  - Belum ada review
  - Bisa diedit bebas
- **Action yang bisa dilakukan**:
  - Edit semua field
  - Delete
  - Submit untuk review

#### 2. **REVIEW**
- **Kondisi**: Dalam proses persetujuan
- **Karakteristik**:
  - Data sudah lengkap
  - Menunggu review dari KPKNL/approver
  - Edit terbatas (hanya admin/reviewer)
- **Action**:
  - Approve → status jadi APPROVED
  - Reject → status kembali ke DRAFT
  - Request revision

#### 3. **APPROVED**
- **Kondisi**: Disetujui, menunggu pembayaran
- **Karakteristik**:
  - Sudah ada persetujuan KPKNL
  - Invoice sudah terbit
  - Menunggu pembayaran dari penyewa
- **Action**:
  - Setelah pembayaran masuk → ACTIVE
  - Cancel jika penyewa tidak bayar

#### 4. **ACTIVE** ⭐
- **Kondisi**: Sewa sedang berlangsung (INI YANG AKTIF!)
- **Karakteristik**:
  - `tanggal_mulai <= TODAY <= tanggal_berakhir`
  - Pembayaran sudah masuk (minimal periode pertama)
  - Perjanjian sudah ditandatangani
- **Action**:
  - Generate invoice untuk periode berikutnya
  - Monitor pembayaran per periode
  - Perpanjangan (jika mendekati akhir masa sewa)
  - Complete (jika sudah sampai tanggal_berakhir)

#### 5. **COMPLETED**
- **Kondisi**: Sewa telah selesai
- **Karakteristik**:
  - `TODAY > tanggal_berakhir`
  - Semua pembayaran lunas
  - BMN dikembalikan
- **Action**:
  - Archive
  - Generate laporan
  - Perpanjangan (jika masih dalam masa perpanjangan)

#### 6. **CANCELLED**
- **Kondisi**: Dibatalkan
- **Karakteristik**:
  - Bisa dari status manapun
  - Ada alasan pembatalan
  - Perlu settlement pembayaran
- **Action**:
  - Refund (jika sudah bayar)
  - Archive

#### 7. **EXPIRED**
- **Kondisi**: Kadaluarsa
- **Karakteristik**:
  - Masa sewa habis
  - Tidak ada perpanjangan
  - Pembayaran mungkin belum lunas
- **Action**:
  - Collection (jika ada tagihan outstanding)
  - Archive

---

## 2. Logika "Aktif Berlangsung"

### Definisi "Aktif Berlangsung"
Sewa BMN dianggap **aktif berlangsung** jika memenuhi SEMUA kriteria berikut:

```php
$isActive = (
    $status_sewa == 'active' &&
    $tanggal_aktivasi <= TODAY &&
    TODAY <= $tanggal_berakhir &&
    $total_pendapatan_terealisasi > 0
);
```

### Kriteria Detail:

1. **Status = ACTIVE**
   - Status sewa harus 'active'
   - Bukan draft, review, atau approved

2. **Tanggal dalam Range**
   ```php
   $tanggal_aktivasi <= today() && today() <= $tanggal_berakhir
   ```
   - Hari ini harus di antara tanggal aktivasi dan tanggal berakhir
   - Tidak termasuk sewa yang belum mulai
   - Tidak termasuk sewa yang sudah selesai

3. **Sudah Ada Pembayaran**
   ```php
   $total_pendapatan_terealisasi > 0
   ```
   - Minimal pembayaran periode pertama sudah masuk
   - Punya NTPN atau bukti pembayaran

### Contoh Kasus:

#### ✅ AKTIF BERLANGSUNG
```
Sewa A:
- status_sewa: 'active'
- tanggal_aktivasi: 2025-01-01
- tanggal_berakhir: 2025-12-31
- TODAY: 2025-06-15
- total_pendapatan_terealisasi: Rp 50.000.000

Status: AKTIF BERLANGSUNG ✓
```

#### ❌ TIDAK AKTIF - Belum Mulai
```
Sewa B:
- status_sewa: 'approved'
- tanggal_mulai: 2025-12-01  ← Belum sampai tanggal ini
- TODAY: 2025-11-15

Status: TIDAK AKTIF (Belum Mulai)
```

#### ❌ TIDAK AKTIF - Sudah Selesai
```
Sewa C:
- status_sewa: 'completed'
- tanggal_berakhir: 2024-12-31  ← Sudah lewat
- TODAY: 2025-11-15

Status: TIDAK AKTIF (Sudah Selesai)
```

#### ❌ TIDAK AKTIF - Belum Bayar
```
Sewa D:
- status_sewa: 'approved'
- tanggal_mulai: 2025-01-01
- TODAY: 2025-06-15
- total_pendapatan_terealisasi: 0  ← Belum ada pembayaran

Status: TIDAK AKTIF (Belum Bayar)
```

---

## 3. Tracking Pendapatan Sewa

### A. Field Tracking di Tabel Utama

```sql
-- Tabel: bmn_pemanfaatan

biaya_sewa (decimal)                    -- Biaya per periode
total_biaya_sewa (decimal)              -- Total biaya keseluruhan
total_pendapatan_terealisasi (decimal)  -- Sudah dibayar
total_pendapatan_outstanding (decimal)  -- Belum dibayar (sudah diinvoice)
periode_pembayaran_ke (int)             -- Periode pembayaran ke berapa sekarang
total_periode_pembayaran (int)          -- Total berapa periode
```

### B. Detail Pembayaran Per Periode

Tabel: **bmn_pemanfaatan_pembayaran**

Setiap sewa bisa punya multiple pembayaran (per bulan, per quarter, dll)

```sql
CREATE TABLE bmn_pemanfaatan_pembayaran (
    id INT PRIMARY KEY,
    pemanfaatan_id INT,
    periode_ke INT,                      -- Periode ke-1, ke-2, dst
    periode_nama VARCHAR,                 -- "Januari 2025", "Q1 2025"
    tanggal_mulai_periode DATE,
    tanggal_akhir_periode DATE,
    nomor_invoice VARCHAR,
    tanggal_invoice DATE,
    tanggal_jatuh_tempo DATE,
    jumlah_tagihan DECIMAL,              -- Nominal periode ini
    status_pembayaran ENUM,              -- pending, paid, overdue
    jumlah_dibayar DECIMAL,
    sisa_tagihan DECIMAL,
    ntpn VARCHAR,                        -- Nomor bukti bayar
    tanggal_bayar DATE,
    dokumen_bukti_bayar TEXT,
    denda DECIMAL,                       -- Jika terlambat
    hari_terlambat INT
);
```

### C. Perhitungan Pendapatan

#### 1. Total Pendapatan Terealisasi
```php
// Sum dari semua pembayaran yang status = 'paid'
$total_pendapatan_terealisasi = BmnPemanfaatanPembayaran::where('pemanfaatan_id', $id)
    ->where('status_pembayaran', 'paid')
    ->sum('jumlah_dibayar');
```

#### 2. Total Pendapatan Outstanding
```php
// Sum dari semua invoice yang belum dibayar
$total_pendapatan_outstanding = BmnPemanfaatanPembayaran::where('pemanfaatan_id', $id)
    ->whereIn('status_pembayaran', ['pending', 'partial', 'overdue'])
    ->sum('sisa_tagihan');
```

#### 3. Proyeksi Pendapatan
```php
// Total yang akan diterima sampai akhir sewa
$proyeksi_pendapatan = $total_biaya_sewa;

// Atau calculate dari periode yang tersisa
$proyeksi = ($total_periode_pembayaran - $periode_pembayaran_ke) * $biaya_sewa;
```

---

## 4. Workflow Pembayaran

### Skenario 1: Sewa Tahunan - Bayar Sekaligus
```
Sewa: Kantin, 1 tahun, Rp 120.000.000

Timeline:
├─ Jan 2025: Approved
├─ Feb 2025: Bayar Rp 120jt (1x bayar) → ACTIVE
├─ Feb 2025 - Jan 2026: ACTIVE (running)
└─ Feb 2026: COMPLETED

Pembayaran:
- Periode 1: Rp 120.000.000 (paid)
- Total terealisasi: Rp 120.000.000
- Total outstanding: Rp 0
```

### Skenario 2: Sewa Tahunan - Bayar Bulanan
```
Sewa: Kantin, 1 tahun, Rp 120.000.000 (Rp 10jt/bulan)

Timeline:
├─ Jan 2025: Approved
├─ Feb 2025: Bayar Periode 1 (Feb) → ACTIVE
├─ Mar 2025: Bayar Periode 2 (Mar)
├─ Apr 2025: Bayar Periode 3 (Apr)
├─ ... (9 bulan berikutnya)
└─ Feb 2026: COMPLETED

Pembayaran (contoh di bulan Mei 2025):
- Periode 1 (Feb): Rp 10jt (paid)
- Periode 2 (Mar): Rp 10jt (paid)
- Periode 3 (Apr): Rp 10jt (paid)
- Periode 4 (Mei): Rp 10jt (paid)
- Periode 5 (Jun): Rp 10jt (pending) ← Belum bayar
- ... dst

Status Mei 2025:
- Total terealisasi: Rp 40.000.000 (4 bulan)
- Total outstanding: Rp 10.000.000 (1 bulan sudah diinvoice)
- Sisa proyeksi: Rp 70.000.000 (7 bulan belum diinvoice)
```

### Skenario 3: Sewa dengan Perpanjangan
```
Sewa Awal: 1 tahun (Jan 2025 - Dec 2025)

Timeline:
├─ Jan 2025: ACTIVE (sewa pertama)
├─ Nov 2025: Ajukan perpanjangan
├─ Dec 2025: Approved perpanjangan
├─ Jan 2026: ACTIVE (sewa perpanjangan)
└─ Dec 2026: COMPLETED

Notes:
- Perpanjangan = record terpisah atau update tanggal_berakhir
- Field: kali_perpanjangan = 1
```

---

## 5. Dashboard Metrics

### Metrics yang Perlu Ditampilkan di /utilization-dashboard

#### A. Overview Cards
```
┌─────────────────────────────────────────────────┐
│  💼 Total Sewa Aktif         📊 Pendapatan      │
│     12 Sewa                      Rp 450jt       │
│  (Sedang Berlangsung)         (Bulan ini)       │
└─────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────┐
│  ⏳ Menunggu Pembayaran       📈 Total YTD      │
│     5 Invoice                    Rp 2,4M        │
│  Rp 85jt outstanding          (Year to Date)    │
└─────────────────────────────────────────────────┘
```

#### B. Status Breakdown
```
Status Sewa:
- ACTIVE:     12 sewa  (Sedang berlangsung)
- APPROVED:    5 sewa  (Menunggu pembayaran)
- REVIEW:      3 sewa  (Dalam review)
- DRAFT:       8 sewa  (Draft)
- COMPLETED:  45 sewa  (Selesai tahun ini)
```

#### C. Revenue Chart
```
Pendapatan Bulanan (2025):

Jan  ████████████  Rp 120jt
Feb  ████████████  Rp 110jt
Mar  ████████████  Rp 130jt
Apr  ████████████  Rp 125jt
May  ████████████  Rp 140jt
Jun  ████████      Rp 85jt (ongoing)
...
```

#### D. Active Rentals Table
```
╔═══════════════╦═══════════╦═════════════╦══════════════╦═══════════╗
║ Mitra         ║ Lokasi    ║ Periode     ║ Pembayaran   ║ Status    ║
╠═══════════════╬═══════════╬═════════════╬══════════════╬═══════════╣
║ PT ABC        ║ Kantin A  ║ Jan-Dec 25  ║ 6/12 Paid    ║ 🟢 Active ║
║ CV XYZ        ║ Ruang B   ║ Mar-Aug 25  ║ 3/6 Paid     ║ 🟢 Active ║
║ Koperasi 123  ║ Lahan C   ║ Apr-Mar 26  ║ 2/12 Paid    ║ 🟢 Active ║
╚═══════════════╩═══════════╩═════════════╩══════════════╩═══════════╝
```

---

## 6. Query Examples

### Get All Active Rentals
```php
$activeRentals = BmnPemanfaatan::where('status_sewa', 'active')
    ->whereDate('tanggal_aktivasi', '<=', now())
    ->whereDate('tanggal_berakhir', '>=', now())
    ->where('total_pendapatan_terealisasi', '>', 0)
    ->get();
```

### Get Monthly Revenue
```php
$monthlyRevenue = BmnPemanfaatanPembayaran::whereYear('tanggal_bayar', 2025)
    ->whereMonth('tanggal_bayar', 6)
    ->where('status_pembayaran', 'paid')
    ->sum('jumlah_dibayar');
```

### Get Outstanding Invoices
```php
$outstanding = BmnPemanfaatanPembayaran::whereIn('status_pembayaran', ['pending', 'overdue'])
    ->where('tanggal_jatuh_tempo', '<', now())
    ->with('pemanfaatan')
    ->get();
```

### Get Revenue YTD (Year to Date)
```php
$ytdRevenue = BmnPemanfaatanPembayaran::whereYear('tanggal_bayar', now()->year)
    ->where('status_pembayaran', 'paid')
    ->sum('jumlah_dibayar');
```

---

## 7. Auto Status Update (Cron Job)

### Daily Cron Jobs Needed:

#### 1. Aktivasi Sewa yang Sudah Dibayar
```php
// Setiap hari jam 00:00
// Cek sewa yang approved + sudah bayar + tanggal mulai = today
BmnPemanfaatan::where('status_sewa', 'approved')
    ->whereDate('tanggal_mulai', '<=', now())
    ->where('total_pendapatan_terealisasi', '>', 0)
    ->update([
        'status_sewa' => 'active',
        'tanggal_aktivasi' => now(),
        'activated_at' => now()
    ]);
```

#### 2. Complete Sewa yang Sudah Berakhir
```php
// Cek sewa active yang tanggal berakhir = yesterday
BmnPemanfaatan::where('status_sewa', 'active')
    ->whereDate('tanggal_berakhir', '<', now())
    ->update([
        'status_sewa' => 'completed',
        'tanggal_penyelesaian' => now(),
        'completed_at' => now()
    ]);
```

#### 3. Mark Overdue Invoices
```php
// Cek invoice yang jatuh tempo tapi belum dibayar
BmnPemanfaatanPembayaran::where('status_pembayaran', 'pending')
    ->whereDate('tanggal_jatuh_tempo', '<', now())
    ->update(['status_pembayaran' => 'overdue']);
```

---

## 8. API Endpoints Needed

### For Dashboard

```php
// GET /api/utilization/active
// Response: List of active rentals

// GET /api/utilization/revenue-summary
// Response: Total revenue, monthly, YTD, etc

// GET /api/utilization/pending-payments
// Response: List of pending/overdue invoices

// GET /api/utilization/stats
// Response: Count per status

// POST /api/utilization/{id}/activate
// Activate rental (change to active)

// POST /api/utilization/{id}/complete
// Complete rental

// POST /api/utilization/{id}/cancel
// Cancel rental

// POST /api/utilization/{id}/payment
// Record payment for a period
```

---

## 9. Business Rules

### Rule 1: Aktivasi
- Sewa bisa diaktifkan HANYA jika:
  - Status = approved
  - Pembayaran periode 1 sudah masuk
  - Perjanjian sudah ditandatangani
  - Tanggal mulai <= today

### Rule 2: Pembayaran
- Invoice digenerate otomatis sesuai periode
- Jatuh tempo: H-7 sebelum periode dimulai
- Overdue: H+3 setelah jatuh tempo
- Denda: 1% per hari (max 10 hari)

### Rule 3: Perpanjangan
- Bisa diajukan: H-60 sebelum berakhir
- Batas pengajuan: H-30 sebelum berakhir
- Setelah H-30: Tidak bisa perpanjangan

### Rule 4: Pembatalan
- Sebelum active: Full refund
- Saat active: Refund prorata (sisa periode)
- Setelah 50% periode: No refund

---

## 10. Notification System

### Email/SMS Notifications:

1. **H-30 Sebelum Jatuh Tempo**
   - Reminder pembayaran akan jatuh tempo

2. **H-7 Sebelum Berakhir**
   - Reminder sewa akan berakhir
   - Info cara perpanjangan

3. **H+1 Overdue**
   - Warning pembayaran terlambat
   - Info denda

4. **H+7 Overdue**
   - Final warning
   - Ancaman pemutusan sewa

---

## Summary

### Key Points:

1. ✅ **Status Sewa**: 7 status jelas (draft → review → approved → active → completed)
2. ✅ **Aktif Berlangsung**: Status = active + tanggal dalam range + sudah bayar
3. ✅ **Tracking Pendapatan**:
   - Terealisasi (sudah dibayar)
   - Outstanding (invoice terbit, belum bayar)
   - Proyeksi (belum invoice)
4. ✅ **Pembayaran Periodik**: Tracking per periode dengan tabel terpisah
5. ✅ **Auto Update**: Cron job untuk aktivasi & complete otomatis
6. ✅ **Dashboard Metrics**: Revenue, active rentals, outstanding

### Next Steps:
1. Run migration
2. Update Model & Controller
3. Implement dashboard view dengan metrics
4. Setup cron jobs
5. Testing workflow lengkap

---

**Created**: 2025-11-14
**Version**: 1.0
**Status**: Ready for Implementation
