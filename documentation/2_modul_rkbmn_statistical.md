# Dokumentasi Modul RKBMN Statistical Dashboard

## 1. Use Case Diagram

```mermaid
useCaseDiagram
    actor User as "Pimpinan / Eksekutif"
    actor Staff as "Staff BMN"

    package "Modul Statistical Dashboard" {
        usecase "Lihat Dashboard Statistik" as UC1
        usecase "Analisis Distribusi Status" as UC2
        usecase "Analisis Tren per Tahun" as UC3
        usecase "Analisis Sebaran per Bagian" as UC4
        usecase "Filter Data Statistik" as UC5
    }

    User --> UC1
    User --> UC2
    User --> UC3
    User --> UC4
    User --> UC5

    Staff --> UC1
    Staff --> UC5
```

## 2. Activity Diagram: Analisis Data Statistik (Swimlanes)

```mermaid
activityDiagram
    |User (Pimpinan)|
    start
    :Akses Menu Statistik (/statistical-dashboard);
    
    |Sistem|
    :Terima Request;
    :Query Aggregat Data (Count, Group By);
    fork
        :Siapkan Data KPI;
    fork again
        :Siapkan Data Chart Status;
    fork again
        :Siapkan Data Chart Tren;
    end fork
    :Render Halaman dengan Chart.js;

    |User (Pimpinan)|
    :Lihat Visualisasi Data;
    
    if (Ubah Filter Tahun?) then (Ya)
        :Pilih Tahun di Dropdown;
        
        |Sistem|
        :Terima Parameter Tahun;
        :Query Ulang Data Sesuai Tahun;
        :Update Dataset Grafik (AJAX);
        
        |User (Pimpinan)|
        :Lihat Grafik Terupdate;
    endif
    
    stop
```

## 3. Class Diagram

```mermaid
classDiagram
    class BmnStatisticalDashboardController {
        +index()
        +exportExcel()
        +exportPdf()
        -getStatisticsData()
    }

    class BmnPengajuanRkbmn {
        +scopeFilterByYear()
        +scopeGroupByStatus()
        +scopeGroupByBagian()
    }

    class ExcelExporter {
        +download()
    }

    BmnStatisticalDashboardController --> BmnPengajuanRkbmn : queries
    BmnStatisticalDashboardController ..> ExcelExporter : uses
```

## 4. Sequence Diagram: Load Dashboard Statistik

```mermaid
sequenceDiagram
    participant User
    participant View (StatisticalDashboard)
    participant Controller (BmnStatisticalDashboardController)
    participant Model (BmnPengajuanRkbmn)
    participant Database

    User->>View: Buka Halaman /statistical-dashboard
    View->>Controller: GET /statistical-dashboard
    Controller->>Model: Count by Status
    Model->>Database: SELECT status, COUNT(*) GROUP BY status
    Database-->>Model: Result Set
    Controller->>Model: Count by Bagian
    Model->>Database: SELECT bagian, COUNT(*) GROUP BY bagian
    Database-->>Model: Result Set
    Controller-->>View: Return Data (JSON/View)
    View->>View: Render Charts (Chart.js/ApexCharts)
    View-->>User: Tampilkan Dashboard
```

## 5. ERD (Entity Relationship Diagram)

*Catatan: Modul ini bersifat read-only (analitik) dan menggunakan tabel yang sama dengan Modul RKBMN.*

```mermaid
erDiagram
    bmn_pengajuanrkbmnbagian {
        bigint id PK
        string status "Digunakan untuk grouping"
        string id_bagian_pengusul "Digunakan untuk grouping"
        string tahun_anggaran "Digunakan untuk filtering"
        decimal total_anggaran "Digunakan untuk sum/agregasi"
    }
```
