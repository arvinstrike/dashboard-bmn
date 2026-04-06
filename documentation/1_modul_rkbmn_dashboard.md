# Dokumentasi Modul RKBMN Dashboard

## 1. Use Case Diagram

```mermaid
useCaseDiagram
    actor User as "Staff BMN / Pimpinan"

    package "Modul RKBMN Dashboard" {
        usecase "Lihat Dashboard Utama" as UC1
        usecase "Lihat Statistik Ringkas" as UC2
        usecase "Filter Daftar Pengajuan" as UC3
        usecase "Lihat Detail Pengajuan" as UC4
    }

    User --> UC1
    User --> UC2
    User --> UC3
    User --> UC4
```

## 2. Activity Diagram: Monitoring Pengajuan (Swimlanes)

@startuml
|User (Staff/Pimpinan)|
start
:Akses URL Dashboard (/);

|Sistem|
:Terima Request;
:Query Data Statistik (Total, Approved, dll);
:Query Data Daftar Pengajuan (Paginated);
:Render Halaman Dashboard;

|User (Staff/Pimpinan)|
:Lihat Ringkasan KPI & Tabel;

if (Perlu Filter Data?) then (Ya)
    :Klik Panel Filter;
    :Pilih Kriteria (Bagian, Tahun, Status);
    :Klik Tombol "Terapkan";
    
    |Sistem|
    :Terima Parameter Filter;
    :Query Ulang Database dengan Kondisi WHERE;
    :Update Tampilan Tabel;
    
    |User (Staff/Pimpinan)|
else (Tidak)
    |User (Staff/Pimpinan)|
    :Analisis Data yang Tampil;
endif

stop
@enduml

## 3. Class Diagram

classDiagram
    class BmnPengajuanRkbmn {
        +Number id
        +String kode_jenis_pengajuan
        +String id_bagian_pengusul
        +String program
        +String kegiatan
        +String output
        +String kode_barang
        +String status
        +String tahun_anggaran
        +Number total_anggaran
        +scopeFilter()
        +scopePaginate()
    }

    class BmnDashboardController {
        +index(Request)
    }

    class User {
        +Number id
        +String name
        +String role
    }

    BmnDashboardController --> BmnPengajuanRkbmn : reads
    User "1" -- "*" BmnPengajuanRkbmn : views


## 4. Sequence Diagram: Load Dashboard Data

```mermaid
sequenceDiagram
    participant User
    participant View (Dashboard)
    participant Controller (BmnDashboardController)
    participant Model (BmnPengajuanRkbmn)
    participant Database

    User->>View: Akses Halaman Dashboard
    View->>Controller: GET /
    
    par Load Statistics
        Controller->>Model: Count Status (Approved, Rejected, etc)
        Model->>Database: SELECT count(*) GROUP BY status
        Database-->>Model: Return Counts
    and Load Table Data
        Controller->>Model: Get Paginated Data
        Model->>Database: SELECT * FROM pengajuan LIMIT 10 OFFSET 0
        Database-->>Model: Return Collection
    end

    Controller-->>View: Return View with Data
    View-->>User: Tampilkan Dashboard Lengkap
```

## 5. ERD (Entity Relationship Diagram)

```mermaid
erDiagram
    bmn_pengajuanrkbmnbagian {
        bigint id PK
        string kode_jenis_pengajuan
        string id_bagian_pengusul
        string program
        string kegiatan
        string output
        string kode_barang
        string status
        string tahun_anggaran
        decimal harga_barang
        decimal total_anggaran
        text uraian_barang
        string tor_signed_path
        date tanggal_pengajuan
        timestamp created_at
        timestamp updated_at
    }

    users {
        bigint id PK
        string name
        string email
        string role
    }

    users ||--o{ bmn_pengajuanrkbmnbagian : "mengajukan"
```
