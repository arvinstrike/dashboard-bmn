<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Dashboard Pemanfaatan BMN</title>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="{{ asset('js/terbilang.min.js') }}"></script>


    <style>
        :root {
            --bs-primary-rgb: 79, 70, 229;
            --bs-body-font-family: 'Inter', sans-serif;
            --primary-color: #4f46e5;
            --primary-light: #6366f1;
            --primary-dark: #4338ca;
            --success-color: #10b981;
            --warning-color: #f59e0b;
            --danger-color: #ef4444;
            --gray-50: #f9fafb;
            --gray-100: #f3f4f6;
            --gray-200: #e5e7eb;
            --gray-300: #d1d5db;
            --gray-400: #9ca3af;
            --gray-500: #6b7280;
            --gray-600: #4b5563;
            --gray-700: #374151;
            --gray-800: #1f2937;
            --gray-900: #111827;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: var(--bs-body-font-family);
            min-height: 100vh;
            padding: 2rem 0;
            position: relative;
            overflow-x: hidden;
        }

        /* Background Image with Subtle Overlay */
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: url('{{ asset('storage/image/bg_pemanfaatan.png') }}');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
            z-index: -2;
        }

        body::after {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg,
                rgba(55, 65, 81, 0.75) 0%,
                rgba(75, 85, 99, 0.72) 25%,
                rgba(107, 114, 128, 0.70) 50%,
                rgba(75, 85, 99, 0.72) 75%,
                rgba(55, 65, 81, 0.75) 100%
            );
            backdrop-filter: blur(1px);
            z-index: -1;
        }

        .container-fluid {
            max-width: 1400px;
        }

        /* Header Section - Clean & Professional */
        .page-header {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px) saturate(180%);
            -webkit-backdrop-filter: blur(10px) saturate(180%);
            border-radius: 1.5rem;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow:
                0 4px 20px rgba(0, 0, 0, 0.08),
                0 0 0 1px rgba(79, 70, 229, 0.08);
            border: 1px solid rgba(79, 70, 229, 0.1);
            position: relative;
            z-index: 100;
        }

        .page-header h1 {
            font-size: 1.875rem;
            font-weight: 700;
            color: var(--gray-900);
            margin-bottom: 0.5rem;
        }

        .page-header p {
            color: var(--gray-600);
            font-size: 0.95rem;
        }

        .page-header .text-primary-dark {
            color: var(--primary-color) !important;
        }

        /* Modern KPI Cards - Clean White Design */
        .stat-card {
            background: rgba(255, 255, 255, 0.97);
            backdrop-filter: blur(10px) saturate(180%);
            -webkit-backdrop-filter: blur(10px) saturate(180%);
            border-radius: 1.25rem;
            padding: 1.75rem;
            border: 1px solid rgba(226, 232, 240, 0.8);
            box-shadow:
                0 4px 20px rgba(0, 0, 0, 0.06),
                0 0 0 1px rgba(0, 0, 0, 0.02);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--primary-color), var(--primary-light));
            transform: scaleX(0);
            transform-origin: left;
            transition: transform 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-8px);
            background: rgba(255, 255, 255, 1);
            box-shadow:
                0 12px 40px rgba(79, 70, 229, 0.15),
                0 0 0 1px rgba(79, 70, 229, 0.1);
        }

        .stat-card:hover::before {
            transform: scaleX(1);
        }

        .stat-icon-wrapper {
            width: 56px;
            height: 56px;
            border-radius: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 1rem;
            transition: all 0.3s ease;
        }

        .stat-card:hover .stat-icon-wrapper {
            transform: scale(1.1) rotate(5deg);
        }

        .stat-card-primary .stat-icon-wrapper {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            box-shadow: 0 8px 16px rgba(102, 126, 234, 0.3);
        }

        .stat-card-success .stat-icon-wrapper {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            box-shadow: 0 8px 16px rgba(16, 185, 129, 0.3);
        }

        .stat-card-warning .stat-icon-wrapper {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            color: white;
            box-shadow: 0 8px 16px rgba(245, 158, 11, 0.3);
        }

        .stat-card-info .stat-icon-wrapper {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            color: white;
            box-shadow: 0 8px 16px rgba(59, 130, 246, 0.3);
        }

        .stat-card-danger .stat-icon-wrapper {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            color: white;
            box-shadow: 0 8px 16px rgba(239, 68, 68, 0.3);
        }

        .stat-value {
            font-size: 2.25rem;
            font-weight: 800;
            background: linear-gradient(135deg, var(--gray-900) 0%, var(--gray-700) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin: 0.5rem 0;
            line-height: 1;
        }

        .stat-title {
            color: var(--gray-600);
            font-weight: 600;
            font-size: 0.875rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin: 0;
        }

        .stat-change {
            display: inline-flex;
            align-items: center;
            font-size: 0.75rem;
            font-weight: 600;
            margin-top: 0.5rem;
            padding: 0.25rem 0.5rem;
            border-radius: 0.375rem;
        }

        .stat-change.positive {
            color: var(--success-color);
            background-color: rgba(16, 185, 129, 0.1);
        }

        .stat-change.negative {
            color: var(--danger-color);
            background-color: rgba(239, 68, 68, 0.1);
        }

        /* Modern Table Design - Clean White */
        .table-container {
            background: rgba(255, 255, 255, 0.97);
            backdrop-filter: blur(10px) saturate(180%);
            -webkit-backdrop-filter: blur(10px) saturate(180%);
            border-radius: 1.5rem;
            padding: 2rem;
            box-shadow:
                0 4px 20px rgba(0, 0, 0, 0.08),
                0 0 0 1px rgba(79, 70, 229, 0.08);
            border: 1px solid rgba(79, 70, 229, 0.1);
        }

        .table-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .table-header h3 {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--gray-900);
            margin: 0;
        }

        .table-header h3 .text-primary-dark {
            color: var(--primary-color) !important;
        }

        /* Search Bar - Clean Design */
        .search-wrapper {
            position: relative;
            width: 100%;
            max-width: 320px;
        }

        .search-input {
            width: 100%;
            padding: 0.75rem 1rem 0.75rem 3rem;
            border: 2px solid var(--gray-200);
            border-radius: 0.75rem;
            font-size: 0.875rem;
            transition: all 0.3s ease;
            background: white;
            color: var(--gray-900);
        }

        .search-input::placeholder {
            color: var(--gray-400);
        }

        .search-input:focus {
            outline: none;
            border-color: var(--primary-color);
            background: white;
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
        }

        .search-icon {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--gray-400);
            font-size: 1.125rem;
        }

        .table {
            margin: 0;
        }

        .table thead th {
            border-top: none;
            border-bottom: 2px solid var(--gray-200);
            font-weight: 700;
            color: var(--gray-700);
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.5px;
            padding: 1rem 0.75rem;
            background: rgba(249, 250, 251, 0.8);
        }

        .table thead th:first-child {
            border-top-left-radius: 0.75rem;
        }

        .table thead th:last-child {
            border-top-right-radius: 0.75rem;
        }

        .table tbody td {
            padding: 1rem 0.75rem;
            vertical-align: middle;
            border-bottom: 1px solid var(--gray-100);
            color: var(--gray-700);
            font-size: 0.875rem;
        }

        .table tbody tr {
            transition: all 0.2s ease;
            background: rgba(255, 255, 255, 0.6);
        }

        .table tbody tr:hover {
            background-color: rgba(243, 244, 246, 0.8);
            transform: scale(1.001);
        }

        .table tbody tr:last-child td {
            border-bottom: none;
        }

        /* Action Buttons */
        .action-buttons .btn {
            padding: 0.5rem 0.75rem;
            font-size: 0.875rem;
            border-radius: 0.5rem;
            transition: all 0.2s ease;
            border-width: 1.5px;
        }

        .action-buttons .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        /* Status Badges */
        .status-badge {
            display: inline-flex;
            align-items: center;
            padding: 0.5rem 1rem;
            border-radius: 0.75rem;
            font-weight: 600;
            font-size: 0.75rem;
            letter-spacing: 0.025em;
            transition: all 0.2s ease;
        }

        .status-lengkap {
            background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
            color: #065f46;
            border: 1.5px solid #6ee7b7;
        }

        .status-belum-lengkap {
            background: linear-gradient(135deg, #fed7aa 0%, #fdba74 100%);
            color: #9a3412;
            border: 1.5px solid #fb923c;
        }

        /* Row highlight based on completion status */
        tr.data-incomplete {
            background-color: rgba(254, 243, 199, 0.5) !important;
        }

        tr.data-incomplete:hover {
            background-color: rgba(254, 243, 199, 0.7) !important;
        }

        /* Toggle Switch */
        .form-check-input {
            width: 2.75rem;
            height: 1.5rem;
            cursor: pointer;
            background-color: var(--gray-300);
            border: none;
            transition: all 0.3s ease;
        }

        .form-check-input:checked {
            background-color: var(--success-color);
            border-color: var(--success-color);
        }

        .form-check-input:focus {
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.2);
        }

        .form-check-label {
            cursor: pointer;
            font-size: 0.813rem;
            font-weight: 500;
            color: var(--gray-600);
        }

        /* Buttons */
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #5568d3 0%, #63398e 100%);
            transform: translateY(-2px);
            box-shadow: 0 8px 16px rgba(102, 126, 234, 0.3);
        }

        .add-more-btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 0.75rem;
            padding: 0.875rem 1.75rem;
            font-weight: 600;
            font-size: 0.938rem;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
        }

        .add-more-btn:hover {
            background: linear-gradient(135deg, #5568d3 0%, #63398e 100%);
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(102, 126, 234, 0.4);
        }

        /* Pagination */
        .pagination-wrapper {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 1.5rem;
            padding-top: 1.5rem;
            border-top: 2px solid var(--gray-100);
        }

        .pagination-info {
            color: var(--gray-600);
            font-size: 0.875rem;
            font-weight: 500;
        }

        .pagination-info strong {
            color: var(--gray-900);
        }

        .pagination {
            margin: 0;
        }

        .pagination .page-link {
            border: 2px solid var(--gray-200);
            background: white;
            color: var(--gray-700);
            font-weight: 600;
            padding: 0.5rem 0.875rem;
            margin: 0 0.25rem;
            border-radius: 0.5rem;
            transition: all 0.2s ease;
        }

        .pagination .page-link:hover {
            background-color: var(--gray-100);
            border-color: var(--primary-color);
            color: var(--primary-color);
            transform: translateY(-2px);
        }

        .pagination .page-item.active .page-link {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-color: transparent;
            color: #ffffff;
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
        }

        .pagination .page-item.disabled .page-link {
            opacity: 0.5;
            cursor: not-allowed;
        }

        /* Utility Classes */
        .text-primary-dark {
            color: var(--primary-color) !important;
        }

        .text-success-dark {
            color: var(--success-color) !important;
        }

        .text-warning-dark {
            color: var(--warning-color) !important;
        }

        .text-danger-dark {
            color: var(--danger-color) !important;
        }

        .text-muted {
            color: var(--gray-500) !important;
        }

        table tbody strong {
            color: var(--gray-900);
        }

        table tbody small {
            color: var(--gray-500);
        }

        /* Tab navigation styling */
        .nav-pills .nav-link {
            color: #6b7280;
            background: #f3f4f6;
            border-radius: 0.375rem;
            font-size: 0.875rem;
            padding: 0.5rem 0.75rem;
            margin-right: 0.5rem;
            transition: all 0.2s ease;
        }

        .nav-pills .nav-link:hover {
            background: #e5e7eb;
        }

        .nav-pills .nav-link.active {
            background: #4f46e5;
            color: white;
        }

        /* Progress bar styling */
        .progress {
            background-color: #e5e7eb;
        }

        /* Modal styling */
        .modal-xl {
            max-width: 90%;
        }

        @media (max-width: 768px) {
            .nav-pills .nav-link {
                font-size: 0.75rem;
                padding: 0.4rem 0.6rem;
                margin-right: 0.25rem;
                margin-bottom: 0.25rem;
            }
        }
        /* Classy Tab Transitions */
        @keyframes slide-up-fade-in {
            from {
                opacity: 0;
                transform: translateY(15px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .tab-pane.active {
            /* The animation is applied when the tab becomes active */
            animation: slide-up-fade-in 0.4s ease-out forwards;
        }

        .terbilang-output {
            font-style: italic;
            color: #6c757d;
            margin-top: 0.25rem;
            display: block;
            text-transform: capitalize;
        }

        /* Premium SweetAlert2 Styling */
        div.swal2-container div.swal2-popup {
            border-radius: 16px !important;
            padding: 2rem !important;
            font-family: 'Inter', sans-serif !important;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.1) !important;
            border: none !important;
        }

        div.swal2-container div.swal2-title {
            font-size: 1.5rem !important;
            font-weight: 700 !important;
            color: #1f2937 !important;
            margin-bottom: 0.5rem !important;
        }

        div.swal2-container div.swal2-html-container {
            font-size: 1rem !important;
            color: #6b7280 !important;
            line-height: 1.6 !important;
        }

        div.swal2-container div.swal2-actions {
            margin-top: 2rem !important;
            gap: 1rem !important;
            width: 100% !important;
            justify-content: center !important;
        }

        div.swal2-container button.swal2-styled {
            padding: 0.75rem 1.5rem !important;
            font-weight: 600 !important;
            border-radius: 8px !important;
            font-size: 0.95rem !important;
            transition: all 0.2s ease !important;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06) !important;
        }

        div.swal2-container button.swal2-confirm {
            background-color: #4f46e5 !important; /* Primary Color */
            color: white !important;
        }

        div.swal2-container button.swal2-confirm:hover {
            background-color: #4338ca !important;
            transform: translateY(-1px);
            box-shadow: 0 10px 15px -3px rgba(79, 70, 229, 0.3) !important;
        }

        div.swal2-container button.swal2-cancel {
            background-color: #ffffff !important;
            color: #6b7280 !important;
            border: 1px solid #e5e7eb !important;
        }

        div.swal2-container button.swal2-cancel:hover {
            background-color: #f9fafb !important;
            color: #374151 !important;
            transform: translateY(-1px);
        }

        div.swal2-container div.swal2-icon {
            border-width: 3px !important;
            margin-bottom: 1.5rem !important;
        }

        /* Custom Icon Colors */
        div.swal2-icon.swal2-info {
            border-color: #4f46e5 !important;
            color: #4f46e5 !important;
        }

        div.swal2-icon.swal2-question {
            border-color: #6b7280 !important;
            color: #6b7280 !important;
        }

        div.swal2-icon.swal2-success {
            border-color: #10b981 !important;
            color: #10b981 !important;
        }

        div.swal2-icon.swal2-error {
            border-color: #ef4444 !important;
            color: #ef4444 !important;
        }

        div.swal2-icon.swal2-warning {
            border-color: #f59e0b !important;
            color: #f59e0b !important;
        }

        /* Premium Success Design */
        .swal2-custom-success {
            background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%) !important;
            border: 2px solid #86efac !important;
        }

        .swal2-custom-success .swal2-icon.swal2-success {
            border-width: 4px !important;
            width: 80px !important;
            height: 80px !important;
        }

        .swal2-custom-success .swal2-title {
            color: #065f46 !important;
            font-size: 1.75rem !important;
        }

        .swal2-custom-success .swal2-html-container {
            color: #047857 !important;
        }

        /* Success confetti animation */
        @keyframes confetti-fall {
            0% { transform: translateY(-100vh) rotate(0deg); opacity: 1; }
            100% { transform: translateY(100vh) rotate(720deg); opacity: 0; }
        }

        .success-confetti {
            position: fixed;
            width: 10px;
            height: 10px;
            z-index: 10000;
            pointer-events: none;
        }

        /* Force Center SweetAlert2 Buttons - Ultra Robust */
        body.swal2-shown div.swal2-container div.swal2-actions {
            display: flex !important;
            justify-content: center !important;
            align-items: center !important;
            width: 100% !important;
            margin-top: 1.5rem !important;
            gap: 1rem !important;
        }

        body.swal2-shown div.swal2-container div.swal2-actions button {
            margin: 0 !important;
            display: inline-flex !important;
            justify-content: center !important;
            align-items: center !important;
        }
        
        /* Ensure the popup itself doesn't interfere */
        body.swal2-shown div.swal2-container div.swal2-popup {
            display: flex !important;
            flex-direction: column !important;
            align-items: center !important;
        }

        /* Alert Banner System */
        .alert-banner {
            border-radius: 1rem;
            padding: 1rem 1.5rem;
            margin-bottom: 1.5rem;
            border-left: 4px solid;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            animation: slideDown 0.4s ease-out;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .alert-banner-critical {
            background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);
            border-left-color: #dc2626;
            color: #991b1b;
        }

        .alert-banner-warning {
            background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%);
            border-left-color: #f59e0b;
            color: #92400e;
        }

        .alert-banner-info {
            background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
            border-left-color: #3b82f6;
            color: #1e40af;
        }

        .alert-banner-icon {
            font-size: 1.75rem;
            margin-right: 1rem;
            flex-shrink: 0;
        }

        .alert-banner-content {
            flex-grow: 1;
        }

        .alert-banner-title {
            font-weight: 700;
            font-size: 1rem;
            margin-bottom: 0.25rem;
        }

        .alert-banner-text {
            font-size: 0.875rem;
            margin: 0;
        }

        .alert-banner-close {
            background: transparent;
            border: none;
            font-size: 1.25rem;
            opacity: 0.6;
            cursor: pointer;
            padding: 0.25rem 0.5rem;
            transition: opacity 0.2s;
        }

        .alert-banner-close:hover {
            opacity: 1;
        }

        .alert-banner-dismiss {
            background: rgba(0, 0, 0, 0.05);
            border: none;
            padding: 0.25rem 0.75rem;
            border-radius: 0.375rem;
            font-size: 0.75rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            margin-left: 0.5rem;
        }

        .alert-banner-dismiss:hover {
            background: rgba(0, 0, 0, 0.1);
        }

        .alert-banner-link {
            color: inherit;
            text-decoration: underline;
            font-weight: 600;
        }

        .alert-banner-link:hover {
            text-decoration: none;
        }

        /* Timeline Widget - Enhanced Collapsible Design */
        .timeline-widget {
            background: rgba(255, 255, 255, 0.97);
            backdrop-filter: blur(10px);
            border-radius: 1.25rem;
            padding: 1.5rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
            border: 1px solid rgba(79, 70, 229, 0.1);
            margin-bottom: 1.5rem;
            transition: all 0.3s ease;
        }

        .timeline-widget.collapsed {
            padding: 1rem 1.5rem;
        }

        .timeline-summary {
            display: flex;
            justify-content: space-between;
            align-items: center;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .timeline-summary:hover {
            opacity: 0.8;
        }

        .timeline-summary-left {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .timeline-summary-title {
            font-size: 1.125rem;
            font-weight: 700;
            color: var(--gray-900);
            margin: 0;
        }

        .timeline-summary-stats {
            display: flex;
            gap: 1rem;
            font-size: 0.875rem;
            font-weight: 600;
        }

        .timeline-stat-item {
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
        }

        .timeline-stat-critical {
            color: #dc2626;
        }

        .timeline-stat-warning {
            color: #f59e0b;
        }

        .timeline-toggle-btn {
            background: transparent;
            border: 2px solid var(--gray-200);
            border-radius: 0.5rem;
            padding: 0.5rem 1rem;
            font-size: 0.875rem;
            font-weight: 600;
            color: var(--gray-700);
            cursor: pointer;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .timeline-toggle-btn:hover {
            background: var(--gray-100);
            border-color: var(--primary-color);
            color: var(--primary-color);
        }

        .timeline-toggle-btn i {
            transition: transform 0.3s ease;
        }

        .timeline-widget.collapsed .timeline-toggle-btn i {
            transform: rotate(180deg);
        }

        .timeline-content {
            margin-top: 1.5rem;
            max-height: 500px;
            overflow: hidden;
            transition: max-height 0.4s ease, opacity 0.3s ease;
        }

        .timeline-widget.collapsed .timeline-content {
            max-height: 0;
            opacity: 0;
            margin-top: 0;
        }

        .timeline-tabs {
            display: flex;
            gap: 0.5rem;
            margin-bottom: 1.5rem;
            border-bottom: 2px solid var(--gray-100);
            padding-bottom: 0.75rem;
        }

        .timeline-tab {
            background: transparent;
            border: none;
            padding: 0.5rem 1rem;
            font-size: 0.875rem;
            font-weight: 600;
            color: var(--gray-600);
            cursor: pointer;
            border-radius: 0.5rem;
            transition: all 0.2s ease;
            position: relative;
        }

        .timeline-tab:hover {
            background: var(--gray-100);
            color: var(--gray-900);
        }

        .timeline-tab.active {
            color: var(--primary-color);
            background: rgba(79, 70, 229, 0.1);
        }

        .timeline-tab.active::after {
            content: '';
            position: absolute;
            bottom: -0.75rem;
            left: 0;
            right: 0;
            height: 2px;
            background: var(--primary-color);
        }

        .timeline-tab-content {
            display: none;
        }

        .timeline-tab-content.active {
            display: block;
        }

        .timeline-empty {
            text-align: center;
            padding: 2rem;
            color: var(--gray-500);
        }

        .timeline-empty i {
            font-size: 3rem;
            margin-bottom: 1rem;
            opacity: 0.5;
        }

        .timeline-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
            gap: 0.75rem;
        }

        .timeline-item {
            background: white;
            border-radius: 0.75rem;
            padding: 0.75rem;
            border: 2px solid var(--gray-100);
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .timeline-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .timeline-item-date {
            font-size: 0.75rem;
            color: var(--gray-500);
            margin-bottom: 0.25rem;
        }

        .timeline-item-name {
            font-size: 0.875rem;
            font-weight: 600;
            color: var(--gray-900);
            margin-bottom: 0.5rem;
            line-height: 1.3;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .timeline-item-badge {
            display: inline-block;
            padding: 0.25rem 0.5rem;
            border-radius: 0.375rem;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .timeline-item-critical {
            border-color: #fca5a5;
            background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);
        }

        .timeline-item-critical .timeline-item-badge {
            background: #dc2626;
            color: white;
        }

        .timeline-item-warning {
            border-color: #fcd34d;
            background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%);
        }

        .timeline-item-warning .timeline-item-badge {
            background: #f59e0b;
            color: white;
        }

        .timeline-item-info {
            border-color: #93c5fd;
            background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
        }

        .timeline-item-info .timeline-item-badge {
            background: #3b82f6;
            color: white;
        }

        /* Table Row Visual Indicators */
        tr.row-expired {
            background: linear-gradient(90deg, rgba(254, 226, 226, 0.3) 0%, rgba(254, 242, 242, 0.2) 100%) !important;
            border-left: 3px solid #dc2626;
        }

        tr.row-expiring-soon {
            background: linear-gradient(90deg, rgba(254, 243, 199, 0.3) 0%, rgba(255, 251, 235, 0.2) 100%) !important;
            border-left: 3px solid #f59e0b;
        }

        tr.row-expired:hover,
        tr.row-expiring-soon:hover {
            opacity: 0.9;
        }

        /* Status Badge Enhancements */
        .expiry-badge {
            display: inline-flex;
            align-items: center;
            padding: 0.4rem 0.75rem;
            border-radius: 0.5rem;
            font-size: 0.75rem;
            font-weight: 600;
            margin-left: 0.5rem;
        }

        .expiry-badge-critical {
            background: #dc2626;
            color: white;
        }

        .expiry-badge-warning {
            background: #f59e0b;
            color: white;
        }

        .expiry-badge i {
            margin-right: 0.25rem;
        }

    </style>
</head>
<body>

    <div class="container-fluid">
        <!-- Header -->
        <div class="page-header">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <h1>
                        <i class="bi bi-briefcase text-primary-dark me-2"></i>Dashboard Pemanfaatan BMN
                    </h1>
                    <p class="mb-0">Monitor dan kelola pemanfaatan Barang Milik Negara melalui sistem sewa/kerjasama</p>
                </div>
                <div class="d-flex align-items-center gap-3">
                    <button type="button" class="btn btn-primary add-more-btn" data-bs-toggle="modal" data-bs-target="#addUtilizationModal">
                        <i class="bi bi-plus-lg me-2"></i>Tambah Pemanfaatan
                    </button>
                </div>
            </div>
        </div>

        <!-- Alert Banners Container -->
        <div id="alert-banners-container"></div>

        <!-- Timeline Widget - Collapsible with Tabs -->
        <div class="timeline-widget collapsed" id="timeline-widget" style="display: none;">
            <!-- Summary (Always Visible) -->
            <div class="timeline-summary" onclick="toggleTimeline()">
                <div class="timeline-summary-left">
                    <h5 class="timeline-summary-title">
                        <i class="bi bi-calendar-event me-2"></i>Perjanjian Sewa
                    </h5>
                    <div class="timeline-summary-stats" id="timeline-stats">
                        <!-- Stats will be populated by JavaScript -->
                    </div>
                </div>
                <button class="timeline-toggle-btn" type="button">
                    <span id="timeline-toggle-text">Lihat Detail</span>
                    <i class="bi bi-chevron-up"></i>
                </button>
            </div>
            
            <!-- Content (Collapsible) -->
            <div class="timeline-content">
                <!-- Tabs -->
                <div class="timeline-tabs">
                    <button class="timeline-tab active" data-tab="expired" onclick="switchTimelineTab('expired')">
                        <i class="bi bi-exclamation-triangle-fill me-1"></i>Telah Berakhir
                    </button>
                    <button class="timeline-tab" data-tab="expiring" onclick="switchTimelineTab('expiring')">
                        <i class="bi bi-clock-fill me-1"></i>Akan Berakhir
                    </button>
                </div>
                
                <!-- Tab Content: Expired -->
                <div class="timeline-tab-content active" id="tab-expired">
                    <div class="timeline-grid" id="timeline-grid-expired">
                        <!-- Expired items will be populated by JavaScript -->
                    </div>
                </div>
                
                <!-- Tab Content: Expiring Soon -->
                <div class="timeline-tab-content" id="tab-expiring">
                    <div class="timeline-grid" id="timeline-grid-expiring">
                        <!-- Expiring items will be populated by JavaScript -->
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <!-- Stats Cards -->
        <div class="row row-cols-1 row-cols-md-3 g-4 mb-4">
            <div class="col">
                <div class="stat-card stat-card-primary h-100">
                    <div class="stat-icon-wrapper">
                        <i class="bi bi-journal-text"></i>
                    </div>
                    <p class="stat-title">Total Pemanfaatan</p>
                    <p class="stat-value" id="total-utilization">0</p>
                </div>
            </div>
            <div class="col">
                <div class="stat-card stat-card-success h-100">
                    <div class="stat-icon-wrapper">
                        <i class="bi bi-check-circle-fill"></i>
                    </div>
                    <p class="stat-title">Data Lengkap</p>
                    <p class="stat-value" id="complete-utilization">0</p>
                </div>
            </div>
            <div class="col">
                <div class="stat-card stat-card-warning h-100">
                    <div class="stat-icon-wrapper">
                        <i class="bi bi-cash-stack"></i>
                    </div>
                    <p class="stat-title">Pendapatan Sewa</p>
                    <p class="stat-value" id="revenue-utilization">Rp 0</p>
                </div>
            </div>
        </div>

        <!-- Main Content - Full Width Table -->
        <div class="table-container">
            <div class="table-header">
                <div>
                    <h3>
                        <i class="bi bi-table text-primary-dark me-2"></i>Daftar Pemanfaatan BMN
                    </h3>
                </div>
                <div class="d-flex gap-3 align-items-center flex-wrap">
                    <div class="search-wrapper">
                        <i class="bi bi-search search-icon"></i>
                        <input type="text" class="search-input" id="search-input" placeholder="Cari data pemanfaatan...">
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Status</th>
                            <th scope="col">PIC Penyewa</th>
                            <th scope="col">Nama Mitra</th>
                            <th scope="col">Jenis Mitra</th>
                            <th scope="col">Peruntukan Sewa</th>
                            <th scope="col">Kontak</th>
                            <th scope="col">Aksi</th>
                        </tr>
                        <tr>
                            <th></th>
                            <th>
                                <select class="form-select form-select-sm filter-input" data-column="status">
                                    <option value="">Semua</option>
                                    <option value="Lengkap">Lengkap</option>
                                    <option value="Belum Lengkap">Belum Lengkap</option>
                                </select>
                            </th>
                            <th><input type="text" class="form-control form-control-sm filter-input" data-column="pic_penyewa" placeholder="Filter PIC"></th>
                            <th><input type="text" class="form-control form-control-sm filter-input" data-column="nama_mitra" placeholder="Filter Mitra"></th>
                            <th>
                                <select class="form-select form-select-sm filter-input" data-column="jenis_mitra">
                                    <option value="">Semua</option>
                                    <option value="Perorangan">Perorangan</option>
                                    <option value="Badan Usaha">Badan Usaha</option>
                                </select>
                            </th>
                            <th><input type="text" class="form-control form-control-sm filter-input" data-column="peruntukan" placeholder="Filter Peruntukan"></th>
                            <th></th>
                            <th class="text-end">
                                <button class="btn btn-sm btn-outline-secondary" onclick="resetFilters()" title="Reset Semua Filter">
                                    <i class="bi bi-arrow-counterclockwise"></i> Reset
                                </button>
                            </th>
                        </tr>
                    </thead>
                    <tbody id="utilization-table-body">
                        <!-- Data will be populated by JavaScript -->
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="pagination-wrapper">
                <div class="pagination-info">
                    Menampilkan <strong id="showing-start">0</strong> - <strong id="showing-end">0</strong> dari <strong id="total-records">0</strong> data
                </div>
                <nav aria-label="Table pagination">
                    <ul class="pagination mb-0" id="pagination-controls">
                        <!-- Pagination will be populated by JavaScript -->
                    </ul>
                </nav>
            </div>
        </div>


    </div>

    <!-- Add Utilization Modal - TAHAP 1: Informasi Penyewa -->
    <div class="modal fade" id="addUtilizationModal" tabindex="-1" aria-labelledby="addUtilizationModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="addUtilizationModalLabel">
                        <i class="bi bi-plus-circle me-2"></i>Tambah Pemanfaatan BMN
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="add-utilization-form">
                    @csrf
                    <div class="modal-body">
                        <div class="alert alert-info mb-3">
                            <i class="bi bi-info-circle me-2"></i>
                            <strong>Tahap 1:</strong> Isi informasi penyewa terlebih dahulu. Anda dapat melengkapi detail lainnya nanti.
                        </div>

                        <h6 class="mb-3 text-primary"><i class="bi bi-person-badge me-2"></i>Informasi Penyewa</h6>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="pic_penyewa" class="form-label">PIC Penyewa <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="pic_penyewa" name="pic_penyewa" required>
                            </div>
                            <div class="col-md-6">
                                <label for="nomor_hp_pic_penyewa" class="form-label">Nomor HP PIC Penyewa <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="nomor_hp_pic_penyewa" name="nomor_hp_pic_penyewa" placeholder="08xx atau +62xxx" required>
                            </div>
                            <div class="col-md-6">
                                <label for="pic_administrasi_bmn" class="form-label">PIC Administrasi BMN <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="pic_administrasi_bmn" name="pic_administrasi_bmn" required>
                            </div>
                            <div class="col-md-6">
                                <label for="nomor_pic_administrasi_bmn" class="form-label">Nomor HP PIC Admin BMN <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="nomor_pic_administrasi_bmn" name="nomor_pic_administrasi_bmn" placeholder="08xx atau +62xxx" required>
                            </div>
                            <div class="col-12">
                                <label for="nama_mitra_penyewa" class="form-label">Nama Mitra Penyewa <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="nama_mitra_penyewa" name="nama_mitra_penyewa" required>
                            </div>
                            <div class="col-md-6">
                                <label for="jenis_mitra" class="form-label">Jenis Mitra <span class="text-danger">*</span></label>
                                <select class="form-select" id="jenis_mitra" name="jenis_mitra" required>
                                    <option value="">Pilih Jenis Mitra</option>
                                    <option value="Perusahaan">Perusahaan</option>
                                    <option value="Yayasan">Yayasan</option>
                                    <option value="Koperasi">Koperasi</option>
                                    <option value="Perseorangan">Perseorangan</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="jenis_usulan" class="form-label">Jenis Usulan <span class="text-danger">*</span></label>
                                <select class="form-select" id="jenis_usulan" name="jenis_usulan" required>
                                    <option value="">Pilih Jenis Usulan</option>
                                    <option value="Perpanjangan">Perpanjangan</option>
                                    <option value="Usulan Baru">Usulan Baru</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label for="peruntukan_sewa" class="form-label">Peruntukan Sewa</label>
                                <textarea class="form-control" id="peruntukan_sewa" name="peruntukan_sewa" rows="2" placeholder="Jelaskan peruntukan sewa BMN"></textarea>
                            </div>
                            <div class="col-12">
                                <label for="keterangan_uraian" class="form-label">Keterangan/Uraian</label>
                                <textarea class="form-control" id="keterangan_uraian" name="keterangan_uraian" rows="2" placeholder="Informasi tambahan"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-1"></i>Simpan & Lanjutkan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Utilization Modal - Edit Informasi Penyewa -->
    <div class="modal fade" id="editUtilizationModal" tabindex="-1" aria-labelledby="editUtilizationModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title" id="editUtilizationModalLabel">
                        <i class="bi bi-pencil me-2"></i>Edit Informasi Penyewa
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="edit-utilization-form">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <input type="hidden" id="edit_id" name="id">

                        <div class="alert alert-warning mb-3">
                            <i class="bi bi-info-circle me-2"></i>
                            <strong>Edit Informasi Dasar:</strong> Untuk mengubah data detail lainnya, gunakan tombol "Lengkapi Data".
                        </div>

                        <h6 class="mb-3 text-warning"><i class="bi bi-person-badge me-2"></i>Informasi Penyewa</h6>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="edit_pic_penyewa" class="form-label">PIC Penyewa <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="edit_pic_penyewa" name="pic_penyewa" required>
                            </div>
                            <div class="col-md-6">
                                <label for="edit_nomor_hp_pic_penyewa" class="form-label">Nomor HP PIC Penyewa <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="edit_nomor_hp_pic_penyewa" name="nomor_hp_pic_penyewa" required>
                            </div>
                            <div class="col-md-6">
                                <label for="edit_pic_administrasi_bmn" class="form-label">PIC Administrasi BMN <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="edit_pic_administrasi_bmn" name="pic_administrasi_bmn" required>
                            </div>
                            <div class="col-md-6">
                                <label for="edit_nomor_pic_administrasi_bmn" class="form-label">Nomor HP PIC Admin BMN <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="edit_nomor_pic_administrasi_bmn" name="nomor_pic_administrasi_bmn" required>
                            </div>
                            <div class="col-12">
                                <label for="edit_nama_mitra_penyewa" class="form-label">Nama Mitra Penyewa <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="edit_nama_mitra_penyewa" name="nama_mitra_penyewa" required>
                            </div>
                            <div class="col-md-6">
                                <label for="edit_jenis_mitra" class="form-label">Jenis Mitra <span class="text-danger">*</span></label>
                                <select class="form-select" id="edit_jenis_mitra" name="jenis_mitra" required>
                                    <option value="">Pilih Jenis Mitra</option>
                                    <option value="Perusahaan">Perusahaan</option>
                                    <option value="Yayasan">Yayasan</option>
                                    <option value="Koperasi">Koperasi</option>
                                    <option value="Perseorangan">Perseorangan</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="edit_jenis_usulan" class="form-label">Jenis Usulan <span class="text-danger">*</span></label>
                                <select class="form-select" id="edit_jenis_usulan" name="jenis_usulan" required>
                                    <option value="">Pilih Jenis Usulan</option>
                                    <option value="Perpanjangan">Perpanjangan</option>
                                    <option value="Usulan Baru">Usulan Baru</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label for="edit_peruntukan_sewa" class="form-label">Peruntukan Sewa</label>
                                <textarea class="form-control" id="edit_peruntukan_sewa" name="peruntukan_sewa" rows="2"></textarea>
                            </div>
                            <div class="col-12">
                                <label for="edit_keterangan_uraian" class="form-label">Keterangan/Uraian</label>
                                <textarea class="form-control" id="edit_keterangan_uraian" name="keterangan_uraian" rows="2"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-warning">
                            <i class="bi bi-save me-1"></i>Perbarui
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>



    <!-- Complete Data Confirmation Modal -->
    <div class="modal fade" id="completeDataConfirmModal" tabindex="-1" aria-labelledby="completeDataConfirmModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title" id="completeDataConfirmModalLabel">
                        <i class="bi bi-info-circle me-2"></i>Lengkapi Data Pemanfaatan
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-3"><strong>Data pemanfaatan berhasil ditambahkan!</strong></p>
                    <p>Apakah Anda ingin melengkapi data pemanfaatan sekarang?</p>
                    <div class="alert alert-light border mt-3" role="alert">
                        <i class="bi bi-lightbulb text-warning me-2"></i>
                        <small><strong>Catatan:</strong> Jika memilih "Nanti", Anda dapat melengkapi data kapan saja melalui tombol <strong>Edit</strong> pada daftar pemanfaatan.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Nanti</button>
                    <button type="button" class="btn btn-info text-white" id="complete-now-btn">
                        <i class="bi bi-pencil-square me-1"></i>Lengkapi Sekarang
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Complete Data Multi-Tab Modal -->
    <div class="modal fade" id="completeDataModal" tabindex="-1" aria-labelledby="completeDataModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title" id="completeDataModalLabel">
                        <i class="bi bi-clipboard-data me-2"></i>Lengkapi Data Pemanfaatan BMN
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="complete-data-form">
                    @csrf
                    <input type="hidden" id="complete_data_id" name="id">

                    <div class="modal-body">
                        <!-- Progress Indicator -->
                        <div class="progress mb-4" style="height: 3px;">
                            <div class="progress-bar bg-info" id="tab-progress" role="progressbar" style="width: 25%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>

                        <!-- Tab Navigation - 4 TABS FOR DOCUMENTS -->
                        <ul class="nav nav-pills mb-4" id="completeDataTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="tab1-tab" data-bs-toggle="pill" data-bs-target="#tab1" type="button" role="tab">
                                    <i class="bi bi-file-earmark-check me-1"></i>1. Dokumen Konfirmasi
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="tab2-tab" data-bs-toggle="pill" data-bs-target="#tab2" type="button" role="tab">
                                    <i class="bi bi-file-earmark-ruled me-1"></i>2. Dokumen Usulan
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="tab3-tab" data-bs-toggle="pill" data-bs-target="#tab3" type="button" role="tab">
                                    <i class="bi bi-building me-1"></i>3. Dokumen Penilaian
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="tab4-tab" data-bs-toggle="pill" data-bs-target="#tab4" type="button" role="tab">
                                    <i class="bi bi-file-text me-1"></i>4. Dokumen Final
                                </button>
                            </li>
                        </ul>

                        <!-- Tab Content -->
                        <div class="tab-content" id="completeDataTabContent">
                            <!-- Tab 1: Dokumen Konfirmasi -->
                            <div class="tab-pane fade show active" id="tab1" role="tabpanel">
                                <h6 class="mb-4 text-info"><i class="bi bi-file-earmark-check me-2"></i>Dokumen Konfirmasi</h6>

                                <div class="card mb-4">
                                    <div class="card-header bg-light">
                                        <strong><i class="bi bi-folder-plus me-2"></i>Upload Dokumen Konfirmasi</strong>
                                    </div>
                                    <div class="card-body">
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label for="surat_konfirmasi_lampiran" class="form-label">Lampiran Surat Konfirmasi</label>
                                                <div id="view-surat_konfirmasi_lampiran" class="mt-2 file-view-link mb-2"></div>
                                                <input type="file" class="form-control" id="surat_konfirmasi_lampiran" name="surat_konfirmasi_lampiran">
                                                <small class="text-muted">Format: PDF, DOC, DOCX, JPG, PNG (Max: 2MB)</small>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="dokumen_surat_usulan_sewa" class="form-label">Surat Usulan Sewa/Perpanjangan dari Mitra</label>
                                                <div id="view-dokumen_surat_usulan_sewa" class="mt-2 file-view-link mb-2"></div>
                                                <input type="file" class="form-control" id="dokumen_surat_usulan_sewa" name="dokumen_surat_usulan_sewa">
                                                <small class="text-muted">Format: PDF, DOC, DOCX, JPG, PNG (Max: 2MB)</small>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="dokumen_npwp" class="form-label">NPWP</label>
                                                <div id="view-dokumen_npwp" class="mt-2 file-view-link mb-2"></div>
                                                <input type="file" class="form-control" id="dokumen_npwp" name="dokumen_npwp">
                                                <small class="text-muted">Format: PDF, DOC, DOCX, JPG, PNG (Max: 2MB)</small>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="dokumen_ktp_penandatangan" class="form-label">KTP Penandatangan Perjanjian</label>
                                                <div id="view-dokumen_ktp_penandatangan" class="mt-2 file-view-link mb-2"></div>
                                                <input type="file" class="form-control" id="dokumen_ktp_penandatangan" name="dokumen_ktp_penandatangan">
                                                <small class="text-muted">Format: PDF, DOC, DOCX, JPG, PNG (Max: 2MB)</small>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="dokumen_nib" class="form-label">NIB</label>
                                                <div id="view-dokumen_nib" class="mt-2 file-view-link mb-2"></div>
                                                <input type="file" class="form-control" id="dokumen_nib" name="dokumen_nib">
                                                <small class="text-muted">Format: PDF, DOC, DOCX, JPG, PNG (Max: 2MB)</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Tab 2: Dokumen Usulan -->
                            <div class="tab-pane fade" id="tab2" role="tabpanel">
                                <h6 class="mb-4 text-info"><i class="bi bi-file-earmark-ruled me-2"></i>Dokumen Usulan Pemanfaatan</h6>

                                <div class="card mb-4">
                                    <div class="card-header bg-light">
                                        <strong><i class="bi bi-folder-plus me-2"></i>Upload Dokumen Usulan</strong>
                                    </div>
                                    <div class="card-body">
                                        <div class="row g-3">
                                            <div class="col-md-4">
                                                <label for="dokumen_psp" class="form-label">Upload PSP</label>
                                                <div id="view-dokumen_psp" class="mt-2 file-view-link mb-2"></div>
                                                <input type="file" class="form-control" id="dokumen_psp" name="dokumen_psp">
                                                <small class="text-muted">Format: PDF, DOC, DOCX, JPG, PNG (Max: 2MB)</small>
                                            </div>
                                            <div class="col-md-4">
                                                <label for="dokumen_kib" class="form-label">Upload KIB</label>
                                                <div id="view-dokumen_kib" class="mt-2 file-view-link mb-2"></div>
                                                <input type="file" class="form-control" id="dokumen_kib" name="dokumen_kib">
                                                <small class="text-muted">Format: PDF, DOC, DOCX, JPG, PNG (Max: 2MB)</small>
                                            </div>
                                            <div class="col-md-4">
                                                <label for="dokumen_usulan_ttd" class="form-label">Surat Usulan/SPTJM/Pernyataan (TTD)</label>
                                                <div id="view-dokumen_usulan_ttd" class="mt-2 file-view-link mb-2"></div>
                                                <input type="file" class="form-control" id="dokumen_usulan_ttd" name="dokumen_usulan_ttd">
                                                <small class="text-muted">Format: PDF, DOC, DOCX, JPG, PNG (Max: 2MB)</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Tab 3: Dokumen Penilaian -->
                            <div class="tab-pane fade" id="tab3" role="tabpanel">
                                <h6 class="mb-4 text-info"><i class="bi bi-building me-2"></i>Dokumen Penilaian KPKNL</h6>

                                <div class="card mb-4">
                                    <div class="card-header bg-light">
                                        <strong><i class="bi bi-folder-plus me-2"></i>Upload Dokumen Penilaian</strong>
                                    </div>
                                    <div class="card-body">
                                        <div class="row g-3">
                                            <div class="col-md-4">
                                                <label for="dokumen_jadwal_penilaian" class="form-label">Jadwal Penilaian</label>
                                                <div id="view-dokumen_jadwal_penilaian" class="mt-2 file-view-link mb-2"></div>
                                                <input type="file" class="form-control" id="dokumen_jadwal_penilaian" name="dokumen_jadwal_penilaian">
                                                <small class="text-muted">Format: PDF, DOC, DOCX, JPG, PNG (Max: 2MB)</small>
                                            </div>
                                            <div class="col-md-4">
                                                <label for="dokumen_basl" class="form-label">BASL</label>
                                                <div id="view-dokumen_basl" class="mt-2 file-view-link mb-2"></div>
                                                <input type="file" class="form-control" id="dokumen_basl" name="dokumen_basl">
                                                <small class="text-muted">Format: PDF, DOC, DOCX, JPG, PNG (Max: 2MB)</small>
                                            </div>
                                            <div class="col-md-4">
                                                <label for="dokumen_persetujuan_kpknl" class="form-label">Persetujuan KPKNL</label>
                                                <div id="view-dokumen_persetujuan_kpknl" class="mt-2 file-view-link mb-2"></div>
                                                <input type="file" class="form-control" id="dokumen_persetujuan_kpknl" name="dokumen_persetujuan_kpknl">
                                                <small class="text-muted">Format: PDF, DOC, DOCX, JPG, PNG (Max: 2MB)</small>
                                            </div>
                                            <div class="col-md-4">
                                                <label for="dokumen_kode_billing" class="form-label">Dokumen Kode Billing</label>
                                                <div id="view-dokumen_kode_billing" class="mt-2 file-view-link mb-2"></div>
                                                <input type="file" class="form-control" id="dokumen_kode_billing" name="dokumen_kode_billing">
                                                <small class="text-muted">Format: PDF, DOC, DOCX, JPG, PNG (Max: 2MB)</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Tab 4: Dokumen Final -->
                            <div class="tab-pane fade" id="tab4" role="tabpanel">
                                <h6 class="mb-4 text-info"><i class="bi bi-file-text me-2"></i>Dokumen Final</h6>

                                <div class="card mb-4">
                                    <div class="card-header bg-light">
                                        <strong><i class="bi bi-folder-plus me-2"></i>Upload Dokumen Final</strong>
                                    </div>
                                    <div class="card-body">
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label for="dokumen_bukti_bayar" class="form-label">Bukti Bayar</label>
                                                <div id="view-dokumen_bukti_bayar" class="mt-2 file-view-link mb-2"></div>
                                                <input type="file" class="form-control" id="dokumen_bukti_bayar" name="dokumen_bukti_bayar">
                                                <small class="text-muted">Format: PDF, DOC, DOCX, JPG, PNG (Max: 2MB)</small>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="nilai_pendapatan_bukti_bayar" class="form-label">Nilai Pendapatan (Bukti Bayar)</label>
                                                <input type="number" class="form-control" id="nilai_pendapatan_bukti_bayar" name="nilai_pendapatan_bukti_bayar" oninput="updateTerbilang(this)">
                                                <small class="terbilang-output">...</small>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="dokumen_perjanjian" class="form-label">Perjanjian Sewa</label>
                                                <div id="view-dokumen_perjanjian" class="mt-2 file-view-link mb-2"></div>
                                                <input type="file" class="form-control" id="dokumen_perjanjian" name="dokumen_perjanjian">
                                                <small class="text-muted">Format: PDF, DOC, DOCX, JPG, PNG (Max: 2MB)</small>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="perjanjian_logo_penyewa" class="form-label">Logo Penyewa (Opsional)</label>
                                                <div id="view-perjanjian_logo_penyewa" class="mt-2 file-view-link mb-2"></div>
                                                <input type="file" class="form-control" id="perjanjian_logo_penyewa" name="perjanjian_logo_penyewa" accept="image/*">
                                                <small class="text-muted">Format: JPG, PNG (Max: 1MB)</small>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="dokumen_bukti_tindak_lanjut_siman" class="form-label">Bukti Tindak Lanjut SIMAN</label>
                                                <div id="view-dokumen_bukti_tindak_lanjut_siman" class="mt-2 file-view-link mb-2"></div>
                                                <input type="file" class="form-control" id="dokumen_bukti_tindak_lanjut_siman" name="dokumen_bukti_tindak_lanjut_siman" accept="image/*">
                                                <small class="text-muted">Format: JPG, PNG (Max: 2MB)</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="modal-footer d-flex justify-content-between">
                        <button type="button" class="btn btn-secondary" id="prev-tab-btn" style="display: none;">
                            <i class="bi bi-arrow-left me-1"></i>Sebelumnya
                        </button>
                        <div class="ms-auto d-flex gap-2">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Tutup</button>
                            <button type="button" class="btn btn-info text-white" id="next-tab-btn">
                                Selanjutnya<i class="bi bi-arrow-right ms-1"></i>
                            </button>
                            <button type="submit" class="btn btn-success" id="save-complete-btn" style="display: none;">
                                <i class="bi bi-check-circle me-1"></i>Simpan
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        // Add this block to bypass ngrok browser warning for AJAX requests
        $.ajaxSetup({
            headers: {
                'ngrok-skip-browser-warning': 'true'
            }
        });

        let currentUtilizationId = null;
        let newlyAddedUtilizationId = null;
        let currentTab = 1;
        const totalTabs = 4; // Updated: 4 tabs for documents only

        // Bootstrap 5 Modal Helper - Enable jQuery-like syntax
        (function($) {
            const originalModal = $.fn.modal;
            $.fn.modal = function(action) {
                if (action === 'show' || action === 'hide' || action === 'toggle') {
                    this.each(function() {
                        const modalElement = this;
                        let modalInstance = bootstrap.Modal.getInstance(modalElement);

                        if (!modalInstance) {
                            modalInstance = new bootstrap.Modal(modalElement);
                        }

                        modalInstance[action]();
                    });
                    return this;
                }
                // Fallback to original if exists
                if (originalModal) {
                    return originalModal.apply(this, arguments);
                }
                return this;
            };
        })(jQuery);

        // Pagination and search variables
        let allUtilizationData = @json($utilizationData);
        let filteredData = [];
        let currentPage = 1;
        const itemsPerPage = 10;
                let searchQuery = '';
        
                // Terbilang function
                function updateTerbilang(element) {
                    const value = element.value;
                    const outputElement = element.nextElementSibling;
                    console.log('Input value:', value);
                    if (value) {
                        try {
                            const terbilangValue = terbilang(value);
                            console.log('Terbilang output:', terbilangValue);
                            outputElement.textContent = terbilangValue + ' rupiah';
                        } catch (e) {
                            console.error('Terbilang error:', e);
                            outputElement.textContent = 'Error konversi.';
                        }
                    } else {
                        outputElement.textContent = '...';
                    }
                }
        
        
        
        // Format currency
        function formatCurrency(amount) {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0
            }).format(amount);
        }

        // Format date
        function formatDate(dateString) {
            const date = new Date(dateString);
            return date.toLocaleDateString('id-ID', {
                day: '2-digit',
                month: 'short',
                year: 'numeric'
            });
        }

        // Format date for HTML input type="date" (YYYY-MM-DD)
        function formatToInputDate(dateString) {
            if (!dateString) return '';
            const date = new Date(dateString);
            return date.toISOString().split('T')[0];
        }

        // Filtering Logic
        $(document).on('change keyup', '.filter-input', function() {
            filterData();
        });

        function filterData() {
            const statusFilter = $('select[data-column="status"]').val();
            const picFilter = $('input[data-column="pic_penyewa"]').val().toLowerCase();
            const mitraFilter = $('input[data-column="nama_mitra"]').val().toLowerCase();
            const jenisMitraFilter = $('select[data-column="jenis_mitra"]').val();
            const peruntukanFilter = $('input[data-column="peruntukan"]').val().toLowerCase();

            filteredData = allUtilizationData.filter(item => {
                // Status Filter
                if (statusFilter) {
                    const isComplete = item.is_complete == 1 || item.is_complete === true;
                    if (statusFilter === 'Lengkap' && !isComplete) return false;
                    if (statusFilter === 'Belum Lengkap' && isComplete) return false;
                }

                // PIC Filter
                if (picFilter && !(item.pic_penyewa && item.pic_penyewa.toLowerCase().includes(picFilter))) return false;

                // Mitra Filter
                if (mitraFilter && !(item.nama_mitra_penyewa && item.nama_mitra_penyewa.toLowerCase().includes(mitraFilter))) return false;

                // Jenis Mitra Filter
                if (jenisMitraFilter && item.jenis_mitra !== jenisMitraFilter) return false;

                // Peruntukan Filter
                if (peruntukanFilter && !(item.peruntukan_sewa && item.peruntukan_sewa.toLowerCase().includes(peruntukanFilter))) return false;

                // Global Search (if any)
                if (searchQuery) {
                    const searchLower = searchQuery.toLowerCase();
                    const match = (item.nama_mitra_penyewa && item.nama_mitra_penyewa.toLowerCase().includes(searchLower)) ||
                                  (item.pic_penyewa && item.pic_penyewa.toLowerCase().includes(searchLower));
                    if (!match) return false;
                }

                return true;
            });

            currentPage = 1;
            displayTableData();
            renderPagination();
        }

        // Reset Filters
        function resetFilters() {
            $('.filter-input').val('');
            filterData();
        }

        // Override existing filterAndDisplayData to use the new filterData
        function filterAndDisplayData() {
             filterData();
        }

        // Calculate period
        function calculatePeriod(startDate, endDate) {
            const start = new Date(startDate);
            const end = new Date(endDate);
            const diffTime = Math.abs(end - start);
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
            const diffMonths = Math.ceil(diffDays / 30);
            return `${diffMonths} bulan`;
        }

        // Search functionality
        $('#search-input').on('keyup', function() {
            searchQuery = $(this).val().toLowerCase();
            currentPage = 1;
            filterAndDisplayData();
        });

        // Filter data based on search query
        function filterAndDisplayData() {
            if (searchQuery === '') {
                filteredData = [...allUtilizationData];
            } else {
                filteredData = allUtilizationData.filter(util => {
                    return (
                        (util.pic_penyewa && util.pic_penyewa.toLowerCase().includes(searchQuery)) ||
                        (util.pic_administrasi_bmn && util.pic_administrasi_bmn.toLowerCase().includes(searchQuery)) ||
                        (util.nama_mitra_penyewa && util.nama_mitra_penyewa.toLowerCase().includes(searchQuery)) ||
                        (util.jenis_mitra && util.jenis_mitra.toLowerCase().includes(searchQuery)) ||
                        (util.jenis_usulan && util.jenis_usulan.toLowerCase().includes(searchQuery)) ||
                        (util.peruntukan_sewa && util.peruntukan_sewa.toLowerCase().includes(searchQuery)) ||
                        (util.keterangan_uraian && util.keterangan_uraian.toLowerCase().includes(searchQuery)) ||
                        (util.nomor_hp_pic_penyewa && util.nomor_hp_pic_penyewa.toLowerCase().includes(searchQuery))
                    );
                });
            }
            displayTableData();
            renderPagination();
        }

        // Display table data with pagination
        function displayTableData() {
            const tbody = $('#utilization-table-body');
            tbody.empty();

            const startIndex = (currentPage - 1) * itemsPerPage;
            const endIndex = Math.min(startIndex + itemsPerPage, filteredData.length);
            const paginatedData = filteredData.slice(startIndex, endIndex);

            if (paginatedData.length === 0) {
                tbody.append(`
                    <tr>
                        <td colspan="9" class="text-center py-5">
                            <i class="bi bi-inbox display-4 text-muted d-block mb-3"></i>
                            <p class="text-muted mb-0">Tidak ada data yang ditemukan</p>
                        </td>
                    </tr>
                `);
                updatePaginationInfo(0, 0, 0);
                return;
            }

            paginatedData.forEach((util, index) => {
                        // Check if data is complete
                        const isComplete = util.is_complete == 1 || util.is_complete === true;

                        // Status badge
                        let statusBadge = isComplete
                            ? '<span class="badge bg-success"><i class="bi bi-check-circle-fill me-1"></i>Lengkap</span>'
                            : '<span class="badge bg-warning text-dark"><i class="bi bi-exclamation-circle-fill me-1"></i>Belum Lengkap</span>';

                        // Add expiry badge if available
                        if (util._expiryBadge) {
                            statusBadge += `<br>${util._expiryBadge}`;
                        }

                        // Combined row class for incomplete data AND expiry status
                        let rowClass = !isComplete ? 'data-incomplete' : '';
                        if (util._rowClass) {
                            rowClass += (rowClass ? ' ' : '') + util._rowClass;
                        }

                        const row = `
                            <tr class="${rowClass}" id="row-${util.id}">
                                <td>${startIndex + index + 1}</td>
                                <td>
                                    ${statusBadge}
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 35px; height: 35px;">
                                            <span class="text-primary fw-bold">${util.pic_penyewa ? util.pic_penyewa.charAt(0).toUpperCase() : '?'}</span>
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark">${util.pic_penyewa || '-'}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <strong>${util.nama_mitra_penyewa || '-'}</strong>
                                </td>
                                <td>
                                    <span class="badge bg-primary">${util.jenis_mitra || '-'}</span>
                                </td>
                                <td>
                                    ${util.peruntukan_sewa ? util.peruntukan_sewa.substring(0, 50) + '...' : '-'}
                                </td>
                                <td>
                                    <i class="bi bi-telephone me-1"></i>${util.nomor_hp_pic_penyewa || '-'}<br>
                                    <small class="text-muted"><i class="bi bi-telephone me-1"></i>${util.nomor_pic_administrasi_bmn || '-'}</small>
                                </td>
                                <td>
                                    <div class="d-flex gap-1 flex-wrap">
                                        <button class="btn btn-sm btn-outline-primary" onclick="editUtilization(${util.id})" title="Edit Informasi Dasar">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-info" onclick="completeDataUtilization(${util.id})" title="Lengkapi Data Detail">
                                            <i class="bi bi-clipboard-data"></i>
                                        </button>
                                        <a href="/utilization-dashboard/${util.id}/documents" class="btn btn-sm btn-outline-success" title="Generate Dokumen">
                                            <i class="bi bi-file-earmark-pdf"></i>
                                        </a>
                                        <button class="btn btn-sm btn-outline-danger" onclick="deleteUtilization(${util.id})" title="Hapus">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        `;
                        tbody.append(row);
                    });

            updatePaginationInfo(startIndex + 1, endIndex, filteredData.length);
            updateStats(allUtilizationData);
        }

        // Render pagination controls
        function renderPagination() {
            const totalPages = Math.ceil(filteredData.length / itemsPerPage);
            const paginationControls = $('#pagination-controls');
            paginationControls.empty();

            if (totalPages <= 1) {
                return;
            }

            // Previous button
            paginationControls.append(`
                <li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
                    <a class="page-link" href="javascript:void(0)" onclick="changePage(${currentPage - 1})">
                        <i class="bi bi-chevron-left"></i>
                    </a>
                </li>
            `);

            // Page numbers
            const maxPagesToShow = 5;
            let startPage = Math.max(1, currentPage - Math.floor(maxPagesToShow / 2));
            let endPage = Math.min(totalPages, startPage + maxPagesToShow - 1);

            if (endPage - startPage < maxPagesToShow - 1) {
                startPage = Math.max(1, endPage - maxPagesToShow + 1);
            }

            // First page
            if (startPage > 1) {
                paginationControls.append(`
                    <li class="page-item">
                        <a class="page-link" href="javascript:void(0)" onclick="changePage(1)">1</a>
                    </li>
                `);
                if (startPage > 2) {
                    paginationControls.append(`<li class="page-item disabled"><span class="page-link">...</span></li>`);
                }
            }

            // Page numbers
            for (let i = startPage; i <= endPage; i++) {
                paginationControls.append(`
                    <li class="page-item ${i === currentPage ? 'active' : ''}">
                        <a class="page-link" href="javascript:void(0)" onclick="changePage(${i})">${i}</a>
                    </li>
                `);
            }

            // Last page
            if (endPage < totalPages) {
                if (endPage < totalPages - 1) {
                    paginationControls.append(`<li class="page-item disabled"><span class="page-link">...</span></li>`);
                }
                paginationControls.append(`
                    <li class="page-item">
                        <a class="page-link" href="javascript:void(0)" onclick="changePage(${totalPages})">${totalPages}</a>
                    </li>
                `);
            }

            // Next button
            paginationControls.append(`
                <li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
                    <a class="page-link" href="javascript:void(0)" onclick="changePage(${currentPage + 1})">
                        <i class="bi bi-chevron-right"></i>
                    </a>
                </li>
            `);
        }

        // Change page
        function changePage(page) {
            const totalPages = Math.ceil(filteredData.length / itemsPerPage);
            if (page < 1 || page > totalPages) return;
            currentPage = page;
            displayTableData();
            renderPagination();
            // Scroll to top of table
            $('html, body').animate({
                scrollTop: $('.table-container').offset().top - 100
            }, 300);
        }

        // Update pagination info
        function updatePaginationInfo(start, end, total) {
            $('#showing-start').text(start);
            $('#showing-end').text(end);
            $('#total-records').text(total);
        }

        // This function is now used to REFRESH data from the server
        function resetCompleteDataForm() {
            // Reset the form itself, clearing all input fields
            $('#complete-data-form')[0].reset();

            // Manually clear any dynamically generated content
            $('.file-view-link').empty(); // Clear previously shown file links

            // Reset the tab navigation to the first tab
            currentTab = 1;
            updateTabNavigation();
        }

        function populateUtilizationTable() {
            $.ajax({
                url: '/utilization-dashboard',
                method: 'GET',
                success: function(response) {
                    allUtilizationData = response.utilizationData || [];
                    filterAndDisplayData();
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching utilization data:', error);
                    Swal.fire('Gagal', 'Gagal memuat data pemanfaatan: ' + error, 'error');
                }
            });
        }

        // Update statistics
        function updateStats(utilizationData) {
            const today = new Date();
            today.setHours(0, 0, 0, 0); // Reset time to start of day for accurate comparison

            const total = utilizationData.length;
            const complete = utilizationData.filter(u => u.is_complete == 1 || u.is_complete === true).length;

            // Pendapatan Sewa: Sum of nilai_pendapatan_bukti_bayar from Perjanjian Sewa
            const revenue = utilizationData.reduce((sum, util) => {
                let val = 0;
                // Check in Perjanjian Sewa (relation)
                if (util.perjanjian_sewa && util.perjanjian_sewa.nilai_pendapatan_bukti_bayar) {
                    val = parseFloat(util.perjanjian_sewa.nilai_pendapatan_bukti_bayar);
                } 
                // Check camelCase just in case
                else if (util.perjanjianSewa && util.perjanjianSewa.nilai_pendapatan_bukti_bayar) {
                    val = parseFloat(util.perjanjianSewa.nilai_pendapatan_bukti_bayar);
                }
                // Fallback to main table (legacy)
                else if (util.nilai_pendapatan_bukti_bayar) {
                    val = parseFloat(util.nilai_pendapatan_bukti_bayar);
                }
                return sum + (val || 0);
            }, 0);

            $('#total-utilization').text(total);
            $('#complete-utilization').text(complete);
            $('#revenue-utilization').text(formatCurrency(revenue));
        }

        function editUtilization(id) {
            $.ajax({
                url: '/utilization-dashboard/' + id,
                method: 'GET',
                success: function(response) {
                    const util = response.data;

                    $('#edit_id').val(util.id);
                    $('#edit_pic_penyewa').val(util.pic_penyewa || '');
                    $('#edit_nomor_hp_pic_penyewa').val(util.nomor_hp_pic_penyewa || '');
                    $('#edit_pic_administrasi_bmn').val(util.pic_administrasi_bmn || '');
                    $('#edit_nomor_pic_administrasi_bmn').val(util.nomor_pic_administrasi_bmn || '');
                    $('#edit_nama_mitra_penyewa').val(util.nama_mitra_penyewa || '');
                    $('#edit_jenis_mitra').val(util.jenis_mitra || '');
                    $('#edit_jenis_usulan').val(util.jenis_usulan || '');
                    $('#edit_peruntukan_sewa').val(util.peruntukan_sewa || '');
                    $('#edit_keterangan_uraian').val(util.keterangan_uraian || '');

                    $('#editUtilizationModal').modal('show');
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching utilization for edit:', error);
                    Swal.fire('Gagal', 'Gagal memuat data pemanfaatan: ' + error, 'error');
                }
            });
        }

        // Delete utilization with SweetAlert confirmation
        function deleteUtilization(id) {
            confirmDelete({
                itemName: 'data pemanfaatan BMN ini',
                onConfirm: function() {
                    // User klik "Ya, Hapus" - lakukan delete
                    $.ajax({
                        url: '/utilization-dashboard/' + id,
                        method: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response.success) {
                                populateUtilizationTable();
                                Swal.fire('Berhasil', 'Data pemanfaatan berhasil dihapus!', 'success');
                            } else {
                                Swal.fire('Gagal', 'Gagal menghapus data pemanfaatan.', 'error');
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('Error deleting utilization:', error);
                            Swal.fire('Error', 'Terjadi kesalahan saat menghapus data.', 'error');
                        }
                    });
                },
                onCancel: function() {
                    // User klik "Batal" - tidak perlu aksi khusus
                    console.log('Delete cancelled');
                }
            });
        }

        // Toggle complete status
        function toggleCompleteStatus(id, isComplete) {
            $.ajax({
                url: '/utilization-dashboard/' + id + '/toggle-complete',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    is_complete: isComplete ? 1 : 0
                },
                success: function(response) {
                    if (response.success) {
                        // Update the row visually
                        const row = $(`#row-${id}`);
                        const badge = row.find('.status-badge');
                        const label = row.find(`label[for="toggle-complete-${id}"]`);

                        if (isComplete) {
                            // Mark as complete
                            row.removeClass('data-incomplete');
                            badge.removeClass('status-belum-lengkap').addClass('status-lengkap');
                            badge.html('<i class="bi bi-check-circle-fill me-1"></i>Lengkap');
                            label.text('Lengkap');
                        } else {
                            // Mark as incomplete
                            row.addClass('data-incomplete');
                            badge.removeClass('status-lengkap').addClass('status-belum-lengkap');
                            badge.html('<i class="bi bi-exclamation-circle-fill me-1"></i>Belum Lengkap');
                            label.text('Tandai Lengkap');
                        }

                        // Find the item in the global data array and update it
                        const itemIndex = allUtilizationData.findIndex(item => item.id === id);
                        if (itemIndex > -1) {
                            allUtilizationData[itemIndex].is_complete = isComplete;
                        }

                        // Recalculate and update the KPI cards
                        updateStats(allUtilizationData);

                    } else {
                        Swal.fire({
                            title: 'Gagal',
                            text: 'Gagal mengubah status kelengkapan data.',
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                        // Revert checkbox state
                        $(`#toggle-complete-${id}`).prop('checked', !isComplete);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error toggling complete status:', error);
                    Swal.fire({
                        title: 'Terjadi kesalahan',
                        text: 'Terjadi kesalahan saat mengubah status.',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                    // Revert checkbox state
                    $(`#toggle-complete-${id}`).prop('checked', !isComplete);
                }
            });
        }

        // Complete data utilization (from dropdown menu) - UPDATED WITH ALL NEW FIELDS
        function completeDataUtilization(id) {
            currentUtilizationId = id;
            // Fetch existing data to populate file links only
            $.ajax({
                url: '/utilization-dashboard/' + id,
                method: 'GET',
                success: function(response) {
                    const util = response.data;
                    resetCompleteDataForm();
                    $('#complete_data_id').val(util.id);

                    // Clear all previous file links
                    $('.file-view-link').empty();

                    const createLink = (filePath, containerId, fieldName) => {
                        if (filePath) {
                            const url = `/storage/${filePath}`;
                            let displayFileName = filePath.substring(filePath.lastIndexOf('/') + 1);
                            const escapedFieldName = fieldName.replace(/_/g, '\\_');
                            const regex = new RegExp(`^\\d+_(${escapedFieldName})_`);
                            displayFileName = displayFileName.replace(regex, '');
                            if (!displayFileName) displayFileName = filePath.substring(filePath.lastIndexOf('/') + 1);

                            $(`#${containerId}`).html(
                                `<div class="card border-info mb-2" style="max-width: 300px;">
                                    <div class="card-body p-2 d-flex justify-content-between align-items-center">
                                        <span class="text-truncate me-2" title="${displayFileName}">${displayFileName}</span>
                                        <a href="${url}" target="_blank" class="btn btn-sm btn-info text-white">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    </div>
                                </div>`
                            );
                        }
                    };

                    // Tab 1: Dokumen Konfirmasi file links
                    createLink(util.surat_konfirmasi_lampiran, 'view-surat_konfirmasi_lampiran', 'surat_konfirmasi_lampiran');
                    createLink(util.dokumen_surat_usulan_sewa, 'view-dokumen_surat_usulan_sewa', 'dokumen_surat_usulan_sewa');
                    createLink(util.dokumen_npwp, 'view-dokumen_npwp', 'dokumen_npwp');
                    createLink(util.dokumen_ktp_penandatangan, 'view-dokumen_ktp_penandatangan', 'dokumen_ktp_penandatangan');
                    createLink(util.dokumen_nib, 'view-dokumen_nib', 'dokumen_nib');

                    // Tab 2: Dokumen Usulan file links
                    createLink(util.dokumen_psp, 'view-dokumen_psp', 'dokumen_psp');
                    createLink(util.dokumen_kib, 'view-dokumen_kib', 'dokumen_kib');
                    createLink(util.dokumen_usulan_ttd, 'view-dokumen_usulan_ttd', 'dokumen_usulan_ttd');

                    // Tab 3: Dokumen Penilaian file links
                    createLink(util.dokumen_jadwal_penilaian, 'view-dokumen_jadwal_penilaian', 'dokumen_jadwal_penilaian');
                    createLink(util.dokumen_basl, 'view-dokumen_basl', 'dokumen_basl');
                    createLink(util.dokumen_persetujuan_kpknl, 'view-dokumen_persetujuan_kpknl', 'dokumen_persetujuan_kpknl');
                    createLink(util.dokumen_kode_billing, 'view-dokumen_kode_billing', 'dokumen_kode_billing');

                    // Tab 4: Dokumen Final file links
                    createLink(util.dokumen_bukti_bayar, 'view-dokumen_bukti_bayar', 'dokumen_bukti_bayar');
                    createLink(util.dokumen_perjanjian, 'view-dokumen_perjanjian', 'dokumen_perjanjian');
                    createLink(util.dokumen_perjanjian, 'view-dokumen_perjanjian', 'dokumen_perjanjian');
                    createLink(util.dokumen_perjanjian, 'view-dokumen_perjanjian', 'dokumen_perjanjian');
                    createLink(util.perjanjian_logo_penyewa, 'view-perjanjian_logo_penyewa', 'perjanjian_logo_penyewa');
                    createLink(util.dokumen_bukti_tindak_lanjut_siman, 'view-dokumen_bukti_tindak_lanjut_siman', 'dokumen_bukti_tindak_lanjut_siman');

                    // Populate Nilai Pendapatan Bukti Bayar
                    $('#nilai_pendapatan_bukti_bayar').val(util.nilai_pendapatan_bukti_bayar || '');
                    // Trigger terbilang update if value exists
                    if (util.nilai_pendapatan_bukti_bayar) {
                         const input = document.getElementById('nilai_pendapatan_bukti_bayar');
                         updateTerbilang(input);
                    }

                    // Reset to first tab
                    currentTab = 1;
                    updateTabNavigation();

                    // Show modal
                    $('#completeDataModal').modal('show');
                },
                error: function(xhr, status, error) {
                    console.error('Error loading data:', error);
                    Swal.fire({
                        title: 'Gagal',
                        text: 'Gagal memuat data untuk dilengkapi.',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            });
        }

        // Form submission handlers
        $('#add-utilization-form').on('submit', function(e) {
            e.preventDefault();

            $.ajax({
                url: '/utilization-dashboard',
                method: 'POST',
                data: $(this).serialize(),
                success: function(response) {
                    if (response.success) {
                        $('#addUtilizationModal').modal('hide');
                        $('#add-utilization-form')[0].reset();
                        populateUtilizationTable();

                        newlyAddedUtilizationId = response.data.id;

                        // Langsung tampilkan modal konfirmasi lengkapi data tanpa SweetAlert
                        $('#completeDataConfirmModal').modal('show');

                    } else {
                        Swal.fire({
                            title: 'Gagal',
                            text: 'Gagal menambahkan pemanfaatan BMN.',
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error adding utilization:', error);
                    Swal.fire({
                        title: 'Terjadi kesalahan',
                        text: 'Terjadi kesalahan: ' + error,
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            });
        });

                $('#edit-utilization-form').on('submit', function(e) {

                    e.preventDefault();

                    const id = $('#edit_id').val();

                    $.ajax({

                        url: '/utilization-dashboard/' + id,

                        method: 'PUT',

                                                data: $(this).serialize(),

                                                success: function(response) {

                                                    if (response.success) {

                                                        $('#editUtilizationModal').modal('hide');

                                                        populateUtilizationTable();

                                                        createConfetti();
                                                        Swal.fire({

                                                            title: 'Berhasil!',

                                                            text: 'Data berhasil diperbarui.',

                                                            icon: 'success',

                                                            customClass: {
                                                                popup: 'swal2-custom-success'
                                                            },

                                                            timer: 2000,

                                                            timerProgressBar: true,

                                                            showConfirmButton: false

                                                        });

                                                    } else {

                                                        Swal.fire('Gagal', 'Gagal memperbarui pemanfaatan BMN.', 'error');

                                                    }

                                                },

                                                error: function(xhr, status, error) {

                                                    console.error('Error updating utilization:', error);

                                                    Swal.fire('Error', 'Terjadi kesalahan saat memperbarui: ' + error, 'error');

                                                }
            });
        });

        $('#confirm-delete-btn').on('click', function() {
            $.ajax({
                url: '/utilization-dashboard/' + currentUtilizationId,
                method: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        $('#deleteConfirmModal').modal('hide');
                        populateUtilizationTable();
                        createConfetti();
                        Swal.fire({
                            title: 'Berhasil',
                            text: 'Pemanfaatan BMN berhasil dihapus!',
                            icon: 'success',
                            customClass: {
                                popup: 'swal2-custom-success'
                            },
                            timer: 2000,
                            timerProgressBar: true,
                            showConfirmButton: false
                        });
                    } else {
                        Swal.fire({
                            title: 'Gagal',
                            text: 'Gagal menghapus pemanfaatan BMN.',
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error deleting utilization:', error);
                    Swal.fire({
                        title: 'Terjadi kesalahan',
                        text: 'Terjadi kesalahan saat menghapus: ' + error,
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            });
        });

        // Handle "Lengkapi Sekarang" button
        $('#complete-now-btn').on('click', function() {
            $('#completeDataConfirmModal').modal('hide');

            // Load data for completion
            $.ajax({
                url: `/utilization-dashboard/${newlyAddedUtilizationId}`,
                method: 'GET',
                success: function(response) {
                    const util = response.data;

                    // Populate read-only fields in Tab 1
                    $('#complete_data_id').val(util.id);
                    $('#complete_pic').val(util.pic || '');
                    $('#complete_mitra').val(util.mitra || '');
                    $('#complete_jenis_usaha').val(util.jenis_usaha || '');
                    $('#complete_lokasi').val(util.lokasi || '');
                    $('#complete_uraian').val(util.uraian || '');

                    // Reset to first tab
                    currentTab = 1;
                    updateTabNavigation();

                    // Show modal
                    $('#completeDataModal').modal('show');
                },
                error: function(xhr, status, error) {
                    console.error('Error loading data:', error);
                    Swal.fire({
                        title: 'Gagal',
                        text: 'Gagal memuat data untuk dilengkapi.',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            });
        });

        // Tab navigation functions
        function updateTabNavigation() {
            // Hide all tabs
            for (let i = 1; i <= totalTabs; i++) {
                $(`#tab${i}`).removeClass('show active');
                $(`#tab${i}-tab`).removeClass('active');
            }

            // Show current tab
            $(`#tab${currentTab}`).addClass('show active');
            $(`#tab${currentTab}-tab`).addClass('active');

            // Update button visibility
            if (currentTab === 1) {
                $('#prev-tab-btn').hide();
            } else {
                $('#prev-tab-btn').show();
            }

            if (currentTab === totalTabs) {
                $('#next-tab-btn').hide();
                $('#save-complete-btn').show();
            } else {
                $('#next-tab-btn').show();
                $('#save-complete-btn').hide();
            }

            // Update progress bar
            const progress = (currentTab / totalTabs) * 100;
            $('#tab-progress').css('width', progress + '%').attr('aria-valuenow', progress);
        }

        // Next button handler
        $('#next-tab-btn').on('click', function() {
            if (currentTab < totalTabs) {
                currentTab++;
                updateTabNavigation();
            }
        });

        // Previous button handler
        $('#prev-tab-btn').on('click', function() {
            if (currentTab > 1) {
                currentTab--;
                updateTabNavigation();
            }
        });

        // Complete data form submission - Upload documents only
        $('#complete-data-form').on('submit', function(e) {
            e.preventDefault();
            const id = $('#complete_data_id').val();
            const formData = new FormData(this);
            formData.append('_method', 'POST');

            // Check if any file is selected or any text field has value
            let hasData = false;

            // Check all file inputs
            const fileInputs = $(this).find('input[type="file"]');
            fileInputs.each(function() {
                if (this.files && this.files.length > 0) {
                    hasData = true;
                    return false; // break loop
                }
            });

            // Check text/number inputs (nilai_pendapatan_bukti_bayar)
            if (!hasData) {
                const nilaiPendapatan = $('#nilai_pendapatan_bukti_bayar').val();
                if (nilaiPendapatan) {
                    hasData = true;
                }
            }

            // If no data was provided, show warning
            if (!hasData) {
                Swal.fire({
                    title: 'Tidak Ada Data',
                    text: 'Anda belum mengupload file atau mengisi data apapun.',
                    icon: 'warning',
                    confirmButtonText: 'OK'
                });
                return;
            }

            // Show loader
            const submitBtn = $(this).find('button[type="submit"]');
            submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Mengunggah...');

            $.ajax({
                url: '/utilization-dashboard/' + id + '/upload-documents',
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.success) {
                        $('#completeDataModal').modal('hide');
                        populateUtilizationTable();

                        // Show specific success message based on what was uploaded
                        const uploadCount = response.uploaded_files ? response.uploaded_files.length : 0;
                        let message = '';
                        if (uploadCount > 0) {
                            message = `${uploadCount} dokumen berhasil diunggah`;
                        } else {
                            message = 'Data berhasil disimpan';
                        }

                        Swal.fire({
                            title: 'Berhasil!',
                            text: message,
                            icon: 'success',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    } else {
                        Swal.fire('Gagal', 'Gagal mengunggah dokumen.', 'error');
                    }
                },
                error: function(xhr, status, error) {
                    let errorMsg = 'Terjadi kesalahan saat mengunggah dokumen.';
                    if (xhr.status === 422) {
                        const errors = xhr.responseJSON.errors;
                        const firstError = Object.values(errors)[0][0];
                        errorMsg = `Gagal Validasi: ${firstError}`;
                    }
                    Swal.fire('Gagal!', errorMsg, 'error');
                },
                complete: function() {
                    // Re-enable button
                    submitBtn.prop('disabled', false).html('<i class="bi bi-check-circle me-1"></i>Simpan');
                }
            });
        });

        // Reset modal on close
        $('#completeDataModal').on('hidden.bs.modal', function() {
            currentTab = 1;
            updateTabNavigation();
            $('#complete-data-form')[0].reset();
            $('.file-view-link').empty(); // Clear file links
            $('.terbilang-output').text('...'); // Reset terbilang output
        });

        /**
         * Create confetti effect for success
         */
        function createConfetti() {
            const colors = ['#10b981', '#34d399', '#6ee7b7', '#a7f3d0'];
            const confettiCount = 50;

            for (let i = 0; i < confettiCount; i++) {
                const confetti = document.createElement('div');
                confetti.className = 'success-confetti';
                confetti.style.left = Math.random() * 100 + 'vw';
                confetti.style.backgroundColor = colors[Math.floor(Math.random() * colors.length)];
                confetti.style.animation = `confetti-fall ${2 + Math.random() * 2}s linear`;
                confetti.style.animationDelay = Math.random() * 0.5 + 's';

                document.body.appendChild(confetti);

                // Remove after animation
                setTimeout(() => {
                    confetti.remove();
                }, 4000);
            }
        }

        /**
         * Generate and display alert banners based on expiring leases
         */
        function renderAlertBanners(data) {
            const container = document.getElementById('alert-banners-container');
            if (!container) return;
            
            container.innerHTML = '';
            
            // Check if banners were dismissed today
            const dismissedDate = localStorage.getItem('banners_dismissed_date');
            const today = new Date().toDateString();
            
            if (dismissedDate === today) {
                // Banners dismissed for today, skip non-critical
                return;
            }
            
            const now = new Date();
            const criticalItems = [];
            const warningItems = [];
            const infoItems = [];
            
            // Categorize items by priority
            data.forEach(item => {
                if (!item.surat_konfirmasi_tanggal_berakhir) return;
                
                const expiryDate = new Date(item.surat_konfirmasi_tanggal_berakhir);
                const daysUntilExpiry = Math.ceil((expiryDate - now) / (1000 * 60 * 60 * 24));
                
                if (daysUntilExpiry < 0) {
                    criticalItems.push({...item, daysUntilExpiry});
                } else if (daysUntilExpiry <= 7) {
                    warningItems.push({...item, daysUntilExpiry});
                } else if (daysUntilExpiry <= 30) {
                    infoItems.push({...item, daysUntilExpiry});
                }
            });
            
            // Create critical banner (always shown, persistent)
            if (criticalItems.length > 0) {
                const banner = createAlertBanner(
                    'critical',
                    'Perjanjian Telah Berakhir',
                    `${criticalItems.length} perjanjian telah melewati tanggal berakhir dan memerlukan tindakan segera.`,
                    criticalItems,
                    false // Not dismissible for today
                );
                container.appendChild(banner);
            }
            
            // Create warning/info banners only if not dismissed
            const individualDismissed = localStorage.getItem('warning_dismissed') === 'true';
            
            if (warningItems.length > 0 && !individualDismissed) {
                const banner = createAlertBanner(
                    'warning',
                    'Segera Berakhir',
                    `${warningItems.length} perjanjian akan berakhir dalam 7 hari ke depan.`,
                    warningItems,
                    true
                );
                container.appendChild(banner);
            }
            
            if (infoItems.length > 0 && !individualDismissed) {
                const banner = createAlertBanner(
                    'info',
                    'Perhatian Diperlukan',
                    `${infoItems.length} perjanjian akan berakhir dalam 30 hari ke depan.`,
                    infoItems,
                    true
                );
                container.appendChild(banner);
            }
        }

        /**
         * Create individual alert banner element
         */
        function createAlertBanner(type, title, message, items, dismissible) {
            const banner = document.createElement('div');
            banner.className = `alert-banner alert-banner-${type}`;
            
            const iconMap = {
                critical: 'bi-exclamation-triangle-fill',
                warning: 'bi-exclamation-circle-fill',
                info: 'bi-info-circle-fill'
            };
            
            const dismissBtn = dismissible ? `
                <button class="alert-banner-dismiss" onclick="dismissBannerForToday(this)">
                    Jangan tampilkan hari ini
                </button>
            ` : '';
            
            banner.innerHTML = `
                <div class="d-flex align-items-center w-100">
                    <i class="bi ${iconMap[type]} alert-banner-icon"></i>
                    <div class="alert-banner-content">
                        <div class="alert-banner-title">${title}</div>
                        <p class="alert-banner-text">${message}</p>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        ${dismissBtn}
                        <button class="alert-banner-close" onclick="this.parentElement.parentElement.parentElement.remove()">
                            <i class="bi bi-x-lg"></i>
                        </button>
                    </div>
                </div>
            `;
            
            return banner;
        }

        /**
         * Dismiss banner for today (localStorage)
         */
        function dismissBannerForToday(button) {
            const today = new Date().toDateString();
            localStorage.setItem('banners_dismissed_date', today);
            localStorage.setItem('warning_dismissed', 'true');
            
            // Remove all dismissible banners
            document.querySelectorAll('.alert-banner').forEach(banner => {
                const hasDismissBtn = banner.querySelector('.alert-banner-dismiss');
                if (hasDismissBtn) {
                    banner.remove();
                }
            });
        }

        /**
         * Render timeline widget with tabs (expired vs expiring)
         */
        function renderTimelineWidget(data) {
            const widget = document.getElementById('timeline-widget');
            const statsEl = document.getElementById('timeline-stats');
            const expiredGrid = document.getElementById('timeline-grid-expired');
            const expiringGrid = document.getElementById('timeline-grid-expiring');
            
            if (!widget || !statsEl) return;
            
            const now = new Date();
            const expiredItems = [];
            const expiringItems = [];
            
            // Categorize items
            data.forEach(item => {
                if (!item.surat_konfirmasi_tanggal_berakhir) return;
                
                const expiryDate = new Date(item.surat_konfirmasi_tanggal_berakhir);
                const daysUntilExpiry = Math.ceil((expiryDate - now) / (1000 * 60 * 60 * 24));
                
                if (daysUntilExpiry < 0) {
                    expiredItems.push({
                        ...item,
                        expiryDate,
                        daysUntilExpiry
                    });
                } else if (daysUntilExpiry <= 60) {
                    expiringItems.push({
                        ...item,
                        expiryDate,
                        daysUntilExpiry
                    });
                }
            });
            
            // Hide widget if no items
            if (expiredItems.length === 0 && expiringItems.length === 0) {
                widget.style.display = 'none';
                return;
            }
            
            widget.style.display = 'block';
            
            // Sort by expiry date
            expiredItems.sort((a, b) => b.expiryDate - a.expiryDate); // Most recently expired first
            expiringItems.sort((a, b) => a.expiryDate - b.expiryDate); // Soonest first
            
            // Render stats summary
            statsEl.innerHTML = `
                <div class="timeline-stat-item timeline-stat-critical">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    <span>${expiredItems.length} Telah Berakhir</span>
                </div>
                <div class="timeline-stat-item timeline-stat-warning">
                    <i class="bi bi-clock-fill"></i>
                    <span>${expiringItems.length} Akan Berakhir</span>
                </div>
            `;
            
            // Render expired items
            if (expiredItems.length === 0) {
                expiredGrid.innerHTML = '<div class="timeline-empty"><i class="bi bi-check-circle"></i><p>Tidak ada perjanjian yang telah berakhir</p></div>';
            } else {
                expiredGrid.innerHTML = expiredItems.map(item => {
                    const dateStr = formatDateIndonesia(item.expiryDate);
                    const daysAgo = Math.abs(item.daysUntilExpiry);
                    
                    return `
                        <div class="timeline-item timeline-item-critical" onclick="window.location.href='/utilization-dashboard/${item.id}/documents'">
                            <div class="timeline-item-date">${dateStr}</div>
                            <div class="timeline-item-name">${item.nama_mitra_penyewa || 'N/A'}</div>
                            <span class="timeline-item-badge">${daysAgo} hari lalu</span>
                        </div>
                    `;
                }).join('');
            }
            
            // Render expiring items
            if (expiringItems.length === 0) {
                expiringGrid.innerHTML = '<div class="timeline-empty"><i class="bi bi-check-circle"></i><p>Tidak ada perjanjian yang akan berakhir dalam 60 hari</p></div>';
            } else {
                expiringGrid.innerHTML = expiringItems.map(item => {
                    const type = item.daysUntilExpiry <= 7 ? 'warning' : 'info';
                    const dateStr = formatDateIndonesia(item.expiryDate);
                    
                    return `
                        <div class="timeline-item timeline-item-${type}" onclick="window.location.href='/utilization-dashboard/${item.id}/documents'">
                            <div class="timeline-item-date">${dateStr}</div>
                            <div class="timeline-item-name">${item.nama_mitra_penyewa || 'N/A'}</div>
                            <span class="timeline-item-badge">${item.daysUntilExpiry} hari lagi</span>
                        </div>
                    `;
                }).join('');
            }
            
            // Restore collapsed state from localStorage
            const isCollapsed = localStorage.getItem('timeline_collapsed') !== 'false';
            if (!isCollapsed) {
                widget.classList.remove('collapsed');
                document.getElementById('timeline-toggle-text').textContent = 'Sembunyikan';
            }
        }

        /**
         * Toggle timeline widget collapse/expand
         */
        function toggleTimeline() {
            const widget = document.getElementById('timeline-widget');
            const toggleText = document.getElementById('timeline-toggle-text');
            
            widget.classList.toggle('collapsed');
            const isCollapsed = widget.classList.contains('collapsed');
            
            toggleText.textContent = isCollapsed ? 'Lihat Detail' : 'Sembunyikan';
            
            // Save state to localStorage
            localStorage.setItem('timeline_collapsed', isCollapsed);
        }

        /**
         * Switch between timeline tabs
         */
        function switchTimelineTab(tabName) {
            // Update tab buttons
            document.querySelectorAll('.timeline-tab').forEach(tab => {
                tab.classList.remove('active');
                if (tab.dataset.tab === tabName) {
                    tab.classList.add('active');
                }
            });
            
            // Update tab content
            document.querySelectorAll('.timeline-tab-content').forEach(content => {
                content.classList.remove('active');
            });
            document.getElementById(`tab-${tabName}`).classList.add('active');
        }

        /**
         * Format date to Indonesian format
         */
        function formatDateIndonesia(date) {
            const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 
                          'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
            const d = new Date(date);
            return `${d.getDate()} ${months[d.getMonth()]}`;
        }

        /**
         * Add visual indicators to table rows
         */
        function addTableRowIndicators(data) {
            const now = new Date();
            
            data.forEach(item => {
                if (!item.surat_konfirmasi_tanggal_berakhir) return;
                
                const expiryDate = new Date(item.surat_konfirmasi_tanggal_berakhir);
                const daysUntilExpiry = Math.ceil((expiryDate - now) / (1000 * 60 * 60 * 24));
                
                // This will be applied in populateUtilizationTable
                item._rowClass = '';
                item._expiryBadge = '';
                
                if (daysUntilExpiry < 0) {
                    item._rowClass = 'row-expired';
                    item._expiryBadge = `<span class="expiry-badge expiry-badge-critical"><i class="bi bi-exclamation-triangle-fill"></i>Berakhir ${Math.abs(daysUntilExpiry)} hari lalu</span>`;
                } else if (daysUntilExpiry <= 7) {
                    item._rowClass = 'row-expiring-soon';
                    item._expiryBadge = `<span class="expiry-badge expiry-badge-warning"><i class="bi bi-clock-fill"></i>${daysUntilExpiry} hari lagi</span>`;
                }
            });
        }

        // Initialize the page
        $(document).ready(function() {
            populateUtilizationTable();

            // Add table row indicators first
            addTableRowIndicators(allUtilizationData);
            
            // Render alert banners
            renderAlertBanners(allUtilizationData);
            
            // Render timeline widget
            renderTimelineWidget(allUtilizationData);
            
            // Initialize table display
            filteredData = [...allUtilizationData];
            displayTableData();
            renderPagination();

            // Event listener for tab clicks to update progress bar
            $('#completeDataTabs button[data-bs-toggle="pill"]').on('shown.bs.tab', function (e) {
                const targetTabId = $(e.target).attr('id'); // e.g., "tab1-tab"
                currentTab = parseInt(targetTabId.replace('tab', '').replace('-tab', ''));
                updateTabNavigation();
            });
        });
    </script>
    {{-- Session-based Alerts --}}
    @if (session('success'))
        <script>
            showSuccess('{{ session('success') }}');
        </script>
    @endif

    @if (session('error'))
        <script>
            showError('{{ session('error') }}');
        </script>
    @endif

    @if (session('info'))
        <script>
            showInfo('{{ session('info') }}');
        </script>
    @endif

    @if (session('warning'))
        <script>
            showWarning('{{ session('warning') }}');
        </script>
    @endif
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);
            const modalId = urlParams.get('open_modal_id');
            const tabId = urlParams.get('tab');

            if (modalId && tabId) {
                console.log(`Redirected: Opening modal for ID ${modalId} and switching to tab ${tabId}`);

                // A short delay to ensure the modal is fully initialized before switching tabs
                setTimeout(function() {
                    // Open the main modal
                    completeDataUtilization(modalId);

                    // Wait for the modal to be shown before trying to switch tabs
                    const completeModal = document.getElementById('completeDataModal');
                    $(completeModal).on('shown.bs.modal', function () {
                        const tabButton = document.getElementById(tabId);
                        if (tabButton) {
                            const tab = new bootstrap.Tab(tabButton);
                            tab.show();
                        }
                    });

                }, 300);
            }
        });

        // ===========================
        // AUTO-POPULATE KASUB FUNCTIONALITY
        // ===========================

        /**
         * Auto-populate kasub fields from previous documents in workflow
         * @param {string} documentType - Type of document (nodin_berjenjang or surat_usulan_kpknl)
         * @param {number} utilizationId - ID of the pemanfaatan record
         * @param {string} targetFieldNama - Selector for nama field
         * @param {string} targetFieldNomor - Selector for nomor field
         */
        function autoPopulateKasub(documentType, utilizationId, targetFieldNama, targetFieldNomor) {
            $.ajax({
                url: `/utilization-dashboard/${utilizationId}/auto-populate-kasub`,
                method: 'GET',
                data: { document_type: documentType },
                success: function(response) {
                    if (response.success && response.kasub) {
                        // Fill the fields
                        $(targetFieldNama).val(response.kasub.nama || '');
                        $(targetFieldNomor).val(response.kasub.nomor || '');

                        // Show notification
                        Swal.fire({
                            icon: 'info',
                            title: 'Auto-Populated',
                            text: 'Data Kasub telah diisi otomatis dari dokumen sebelumnya. Anda dapat mengubahnya jika diperlukan.',
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 3000,
                            timerProgressBar: true
                        });
                    }
                },
                error: function(xhr) {
                    console.error('Failed to auto-populate kasub:', xhr);
                }
            });
        }

        // Event listener for Nodin Berjenjang modal
        // Auto-populate kasub from Surat Konfirmasi when opening the modal
        $(document).on('shown.bs.modal', '#modalNodinBerjenjang', function(e) {
            const button = $(e.relatedTarget);
            const utilizationId = button.data('id');

            const kasubNamaField = '#nodin_berjenjang_kasub_nama';
            const kasubNomorField = '#nodin_berjenjang_kasub_nomor';

            // Only auto-populate if both fields are empty
            if (!$(kasubNamaField).val() && !$(kasubNomorField).val()) {
                autoPopulateKasub('nodin_berjenjang', utilizationId, kasubNamaField, kasubNomorField);
            }
        });

        // Event listener for Surat Usulan KPKNL modal
        // Auto-populate kasubag from Nodin Berjenjang (or Surat Konfirmasi as fallback) when opening the modal
        $(document).on('shown.bs.modal', '#modalSuratUsulanKPKNL', function(e) {
            const button = $(e.relatedTarget);
            const utilizationId = button.data('id');

            const kasubagNamaField = '#kasubag_nama';
            const kasubagNomorField = '#kasubag_nomor';

            // Only auto-populate if both fields are empty
            if (!$(kasubagNamaField).val() && !$(kasubagNomorField).val()) {
                autoPopulateKasub('surat_usulan_kpknl', utilizationId, kasubagNamaField, kasubagNomorField);
            }
        });
    </script>
</body>
</html>