# Dokumentasi Modul Pemanfaatan BMN (Utilization Dashboard)

## 1. Use Case Diagram

```mermaid
useCaseDiagram
    actor Staff as "Staff Pengelola Aset"
    actor Kasubag as "Kepala Sub Bagian"

    package "Modul Pemanfaatan BMN" {
        usecase "Input Data Sewa Baru" as UC1
        usecase "Lengkapi Dokumen (Wizard)" as UC2
        usecase "Generate Dokumen Otomatis" as UC3
        usecase "Upload Dokumen Final" as UC4
        usecase "Monitoring Jatuh Tempo" as UC5
        usecase "Tracking Pendapatan Sewa" as UC6
    }

    Staff --> UC1
    Staff --> UC2
    Staff --> UC3
    Staff --> UC4
    Staff --> UC5
    Staff --> UC6

    Kasubag --> UC5
    Kasubag --> UC6
```

## 2. Activity Diagram: Siklus Pemanfaatan BMN (Swimlanes)

```mermaid
activityDiagram
    |Staff Pengelola|
    start
    :Input Data Awal (Mitra, PIC);
    :Klik "Simpan";
    
    |Sistem|
    :Validasi Input;
    :Create Record (Status: Draft);
    :Redirect ke Halaman Detail;
    
    |Staff Pengelola|
    :Buka Tab "Kelengkapan Dokumen";
    
    partition "Proses Dokumen (Berulang)" {
        |Staff Pengelola|
        :Pilih Jenis Dokumen (mis: Surat Konfirmasi);
        :Lengkapi Form Data Dokumen;
        :Klik "Generate Dokumen";
        
        |Sistem|
        :Ambil Template .docx;
        :Replace Placeholder dengan Data;
        :Download File ke User;
        
        |Staff Pengelola|
        :Cetak & Mintakan Tanda Tangan;
        :Scan Dokumen Final;
        :Upload File PDF;
        
        |Sistem|
        :Simpan File ke Storage;
        :Update Status Dokumen -> Lengkap;
    }

    |Sistem|
    if (Semua Dokumen Wajib Ada?) then (Ya)
        :Update Status Sewa -> Active;
        :Mulai Hitung Mundur Jatuh Tempo;
    else (Tidak)
        :Tetap Status Draft;
    endif
    
    stop
```

## 3. Class Diagram

```mermaid
classDiagram
    class BmnPemanfaatan {
        +int id
        +string status_sewa
        +boolean is_complete
        +relation perjanjianSewa()
        +relation suratKonfirmasi()
        +relation nodinBerjenjang()
        +relation suratUsulanKpknl()
        +scopeActive()
        +scopeWithDocuments()
    }

    class PerjanjianSewa {
        +int pemanfaatan_id
        +string nomor_surat
        +date tanggal_surat
        +decimal nilai_sewa
        +decimal nilai_pendapatan_bukti_bayar
        +string dokumen_perjanjian
    }

    class SuratKonfirmasi {
        +int pemanfaatan_id
        +string nomor
        +date tanggal
    }

    class BmnUtilizationController {
        +index()
        +store()
        +uploadDocuments()
        +saveDocumentData()
    }

    class BmnDocumentController {
        +generate()
        +generateAll()
    }

    BmnPemanfaatan "1" *-- "1" PerjanjianSewa
    BmnPemanfaatan "1" *-- "1" SuratKonfirmasi
    BmnUtilizationController --> BmnPemanfaatan : manages
    BmnDocumentController ..> BmnPemanfaatan : reads
```

## 4. Sequence Diagram: Generate Dokumen

```mermaid
sequenceDiagram
    participant User
    participant View (Documents Modal)
    participant DocController (BmnDocumentController)
    participant Model (BmnPemanfaatan)
    participant TemplateEngine (PhpWord)

    User->>View: Klik "Generate Surat Konfirmasi"
    View->>DocController: POST /generate/surat-konfirmasi
    DocController->>Model: findOrFail($id) with relations
    Model-->>DocController: Data Model Lengkap
    DocController->>TemplateEngine: Load Template (template-surat-konfirmasi.docx)
    TemplateEngine->>TemplateEngine: Replace ${nomor}, ${tanggal}, ${mitra}
    TemplateEngine-->>DocController: Temporary File Path
    DocController-->>User: Download File (.docx)
```

## 5. ERD (Entity Relationship Diagram)

```mermaid
erDiagram
    bmn_pemanfaatan ||--|| surat_konfirmasi_perpanjangan_sewa : "1-to-1"
    bmn_pemanfaatan ||--|| nodin_berjenjang : "1-to-1"
    bmn_pemanfaatan ||--|| surat_usulan_kpknl_sptjm : "1-to-1"
    bmn_pemanfaatan ||--|| nodin_persetujuan_kpknl : "1-to-1"
    bmn_pemanfaatan ||--|| perjanjian_sewa : "1-to-1"
    bmn_pemanfaatan ||--|| surat_permohonan_ttds : "1-to-1"
    bmn_pemanfaatan ||--|| nodin_internals : "1-to-1"
    bmn_pemanfaatan ||--|| surat_penyampaian_perjanjians : "1-to-1"
    bmn_pemanfaatan ||--|{ daftar_bmn : "1-to-Many"

    bmn_pemanfaatan {
        bigint id PK
        string status_sewa
        string pic_penyewa
        boolean is_complete
    }

    perjanjian_sewa {
        bigint id PK
        int pemanfaatan_id FK
        string nomor_surat
        decimal nilai_sewa
        decimal nilai_pendapatan_bukti_bayar
        string dokumen_perjanjian
    }
```
