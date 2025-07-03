<!-- 🔗 HEAD LINKS -->
<!-- Favicon -->
<link rel="shortcut icon" type="image/x-icon" href="{{ asset('back_auth/assets/img/favicon.png') }}" />

<!-- Bootstrap 5 CSS -->
<link rel="stylesheet" href="{{ asset('back_auth/assets/css/bootstrap.min.css') }}" />

<!-- Font Awesome -->
<link rel="stylesheet" href="{{ asset('back_auth/assets/plugins/fontawesome/css/fontawesome.min.css') }}" />
<link rel="stylesheet" href="{{ asset('back_auth/assets/plugins/fontawesome/css/all.min.css') }}" />

<!-- Feather Icons -->
<link rel="stylesheet" href="{{ asset('back_auth/assets/css/feathericon.min.css') }}" />

<!-- Morris.js CSS -->
<link rel="stylesheet" href="https://cdn.oesmith.co.uk/morris-0.5.1.css" />
<link rel="stylesheet" href="{{ asset('back_auth/assets/plugins/morris/morris.css') }}" />

<!-- Custom Styles -->
<link rel="stylesheet" href="{{ asset('back_auth/assets/css/style.css') }}" />

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/izitoast/1.4.0/css/iziToast.css"
    integrity="sha512-DIW4FkYTOxjCqRt7oS9BFO+nVOwDL4bzukDyDtMO7crjUZhwpyrWBFroq+IqRe6VnJkTpRAS6nhDvf0w+wHmxg=="
    crossorigin="anonymous" referrerpolicy="no-referrer" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-tagsinput/0.8.0/bootstrap-tagsinput.css">


<!-- Bootstrap Tagsinput CSS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-tagsinput/0.8.0/bootstrap-tagsinput.css">

<style>



 body { 
            
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            min-height: 100vh;
        }
        .card-glass { 
            background: rgba(255, 255, 255, 0.95); 
            border-radius: 20px; 
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1); 
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.18);
            padding: 2.5rem; 
        }
        .btn-primary-gradient { 
            background: linear-gradient(45deg, #6a11cb, #2575fc); 
            border: none; 
            color: #fff; 
            padding: 12px 30px;
            border-radius: 25px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .btn-primary-gradient:hover { 
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(106, 17, 203, 0.4);
            color: #fff;
        }
        .btn-secondary-gradient {
            background: linear-gradient(45deg, #6c757d, #495057);
            border: none;
            color: #fff;
            padding: 12px 30px;
            border-radius: 25px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .btn-secondary-gradient:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(108, 117, 125, 0.4);
            color: #fff;
        }
        .form-control, .form-select {
            border-radius: 12px;
            border: 2px solid #e9ecef;
            padding: 12px 16px;
            transition: all 0.3s ease;
        }
        .form-control:focus, .form-select:focus {
            border-color: #6a11cb;
            box-shadow: 0 0 0 0.2rem rgba(106, 17, 203, 0.25);
        }
        .form-label {
            font-weight: 600;
            color: #495057;
            margin-bottom: 8px;
        }
        .alert {
            border-radius: 12px;
            border: none;
            padding: 15px 20px;
        }
        .user-image {
            border-radius: 12px;
            border: 3px solid #e9ecef;
            transition: all 0.3s ease;
        }
        .user-image:hover {
            transform: scale(1.05);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }
        .required {
            color: #dc3545;
        }
        .form-group {
            margin-bottom: 1.5rem;
        }
        .page-title {
            color: #2c3e50;
            font-weight: 700;
            margin-bottom: 2rem;
            text-align: center;
        }
        .loading {
            display: none;
        }
        .loading.show {
            display: inline-block;
        }
        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.875rem;
            font-weight: 500;
        }
        .status-actif {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .status-inactif {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
    .bootstrap-tagsinput {
        width: 100%;
        min-height: 38px;
        border: 1px solid #ccc;
        padding: 5px;
        line-height: 22px;
    }

    .bootstrap-tagsinput .tag {
        margin-right: 2px;
        color: white;
        background-color: #007bff;
        padding: 5px 10px;
        border-radius: 4px;
    }
    body {
            background: #f4f6f9;
            font-family: 'Segoe UI', sans-serif;
        }
        .page-title {
            font-weight: 700;
            color: #343a40;
        }
        .card-glass {
            background: rgba(255, 255, 255, 0.85);
            border: none;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            backdrop-filter: blur(10px);
        }
        .btn-primary-gradient {
            background: linear-gradient(to right, #6a11cb, #2575fc);
            border: none;
            color: #fff;
        }
        .btn-primary-gradient:hover {
            filter: brightness(1.1);
        }
        .badge-status {
            padding: 0.5em 0.75em;
            border-radius: 1rem;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
        }
        .badge-actif {
            background: #28a745;
            color: white;
        }
        .badge-inactif {
            background: #dc3545;
            color: white;
        }
        .table-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
        }
        .form-xl {
    font-size: 1.3rem;     /* Texte plus grand */
    padding: 1rem 1.25rem; /* Plus de hauteur et largeur */
    height: auto;          /* Laisse s’adapter */
    border-radius: 0.5rem; /* Bords arrondis plus visibles (optionnel) */
}
 :root {
            --primary-color: #6c7ae0;
            --secondary-color: #a8b8f0;
            --success-color: #52c41a;
            --warning-color: #faad14;
            --danger-color: #ff4d4f;
            --light-blue: #e6f3ff;
            --soft-gray: #f8f9fa;
            --border-color: #e8e8e8;
        }

        body { 
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%); 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            min-height: 100vh;
            color: #4a5568;
        }

        .card-glass { 
            background: rgba(255, 255, 255, 0.98); 
            border-radius: 16px; 
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08); 
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            padding: 2rem; 
        }

        .btn-primary-soft { 
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color)); 
            border: none; 
            color: #fff; 
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            font-size: 0.9rem;
        }

        .btn-primary-soft:hover { 
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(108, 122, 224, 0.3);
            color: #fff;
        }

        .btn-success-soft {
            background: linear-gradient(135deg, #52c41a, #73d13d);
            border: none;
            color: #fff;
            padding: 6px 12px;
            border-radius: 6px;
            font-weight: 500;
            transition: all 0.3s ease;
            text-decoration: none;
            font-size: 0.8rem;
        }

        .btn-success-soft:hover {
            transform: translateY(-1px);
            box-shadow: 0 3px 10px rgba(82, 196, 26, 0.3);
            color: #fff;
        }

        .btn-warning-soft {
            background: linear-gradient(135deg, #faad14, #ffc53d);
            border: none;
            color: #fff;
            padding: 6px 12px;
            border-radius: 6px;
            font-weight: 500;
            transition: all 0.3s ease;
            text-decoration: none;
            font-size: 0.8rem;
        }

        .btn-warning-soft:hover {
            transform: translateY(-1px);
            box-shadow: 0 3px 10px rgba(250, 173, 20, 0.3);
            color: #fff;
        }

        .btn-danger-soft {
            background: linear-gradient(135deg, #ff4d4f, #ff7875);
            border: none;
            color: #fff;
            padding: 6px 12px;
            border-radius: 6px;
            font-weight: 500;
            transition: all 0.3s ease;
            font-size: 0.8rem;
        }

        .btn-danger-soft:hover {
            transform: translateY(-1px);
            box-shadow: 0 3px 10px rgba(255, 77, 79, 0.3);
        }

        .table-modern {
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.05);
            background: white;
        }

        .table-modern thead {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
        }

        .table-modern thead th {
            border: none;
            padding: 1rem;
            font-weight: 600;
            font-size: 0.9rem;
        }

        .table-modern tbody tr {
            transition: all 0.3s ease;
            border-bottom: 1px solid var(--border-color);
        }

        .table-modern tbody tr:hover {
            background-color: var(--light-blue);
            transform: scale(1.005);
        }

        .table-modern tbody td {
            padding: 1rem;
            vertical-align: middle;
            border: none;
        }

        .page-title {
            color: #2d3748;
            font-weight: 700;
            margin-bottom: 2rem;
            text-align: center;
            font-size: 2.2rem;
        }

        .alert {
            border-radius: 10px;
            border: none;
            padding: 12px 16px;
            font-size: 0.9rem;
        }

        .search-box {
            border-radius: 8px;
            border: 2px solid var(--border-color);
            padding: 10px 16px;
            transition: all 0.3s ease;
            font-size: 0.9rem;
        }

        .search-box:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(108, 122, 224, 0.15);
        }

        .stats-card {
            background: linear-gradient(135deg, #fff, #f8fafc);
            color: #4a5568;
            border-radius: 12px;
            padding: 1.5rem;
            text-align: center;
            margin-bottom: 2rem;
            border: 1px solid var(--border-color);
            transition: all 0.3s ease;
        }

        .stats-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .stats-card i {
            color: var(--primary-color);
        }

        .stats-card h4 {
            color: #2d3748;
            font-weight: 700;
            margin: 0.5rem 0;
        }

        .badge-compagnie {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 6px 12px;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 500;
        }

        .action-buttons {
            display: flex;
            gap: 0.4rem;
            justify-content: center;
        }

        .filter-section {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            border: 1px solid var(--border-color);
        }

        .empty-state {
            text-align: center;
            padding: 3rem 1rem;
            color: #718096;
        }

        .empty-state i {
            color: #cbd5e0;
            margin-bottom: 1rem;
        }

        .pagination .page-link {
            color: var(--primary-color);
            border: 1px solid var(--border-color);
            border-radius: 6px;
            margin: 0 2px;
            padding: 0.5rem 0.75rem;
        }

        .pagination .page-link:hover {
            background-color: var(--light-blue);
            border-color: var(--primary-color);
        }

        .pagination .page-item.active .page-link {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        :root {
            --primary-color: #6c7ae0;
            --secondary-color: #a8b8f0;
            --success-color: #52c41a;
            --warning-color: #faad14;
            --danger-color: #ff4d4f;
            --light-blue: #e6f3ff;
            --soft-gray: #f8f9fa;
            --border-color: #e8e8e8;
        }

        body { 
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%); 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            min-height: 100vh;
            color: #4a5568;
        }

        .card-glass { 
            background: rgba(255, 255, 255, 0.98); 
            border-radius: 16px; 
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08); 
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            padding: 2rem; 
            margin-bottom: 2rem;
        }

        .form-floating > .form-control:focus ~ label,
        .form-floating > .form-control:not(:placeholder-shown) ~ label,
        .form-floating > .form-select ~ label {
            color: var(--primary-color);
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(108, 122, 224, 0.15);
        }

        .btn-primary-soft { 
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color)); 
            border: none; 
            color: #fff; 
            padding: 12px 24px;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            font-size: 1rem;
        }

        .btn-primary-soft:hover { 
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(108, 122, 224, 0.3);
            color: #fff;
        }

        .btn-secondary-soft {
            background: linear-gradient(135deg, #6c757d, #adb5bd);
            border: none;
            color: #fff;
            padding: 12px 24px;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            font-size: 1rem;
        }

        .btn-secondary-soft:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(108, 117, 125, 0.3);
            color: #fff;
        }

        .btn-warning-soft {
            background: linear-gradient(135deg, var(--warning-color), #ffc53d);
            border: none;
            color: #fff;
            padding: 12px 24px;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            font-size: 1rem;
        }

        .btn-warning-soft:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(250, 173, 20, 0.3);
            color: #fff;
        }

        .btn-danger-soft {
            background: linear-gradient(135deg, var(--danger-color), #ff7875);
            border: none;
            color: #fff;
            padding: 12px 24px;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            font-size: 1rem;
        }

        .btn-danger-soft:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(255, 77, 79, 0.3);
            color: #fff;
        }

        .page-title {
            color: #2d3748;
            font-weight: 700;
            margin-bottom: 2rem;
            text-align: center;
            font-size: 2rem;
        }

        .breadcrumb {
            background: linear-gradient(135deg, var(--light-blue), rgba(255, 255, 255, 0.8));
            border-radius: 10px;
            padding: 12px 20px;
            margin-bottom: 2rem;
        }

        .breadcrumb-item + .breadcrumb-item::before {
            color: var(--primary-color);
        }

        .breadcrumb-item.active {
            color: var(--primary-color);
            font-weight: 600;
        }

        .breadcrumb-item a {
            color: #6c757d;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .breadcrumb-item a:hover {
            color: var(--primary-color);
        }

        .alert {
            border-radius: 10px;
            border: none;
            padding: 12px 16px;
            font-size: 0.9rem;
        }

        .info-card {
            background: linear-gradient(135deg, #fff, #f8fafc);
            border-radius: 12px;
            padding: 1.5rem;
            border: 1px solid var(--border-color);
            margin-bottom: 1rem;
        }

        .info-card .info-label {
            color: #718096;
            font-size: 0.875rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
        }

        .info-card .info-value {
            color: #2d3748;
            font-size: 1rem;
            font-weight: 500;
        }

        .form-section {
            background: white;
            border-radius: 12px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            border: 1px solid var(--border-color);
        }

        .section-title {
            color: #2d3748;
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: 1.5rem;
            padding-bottom: 0.75rem;
            border-bottom: 2px solid var(--light-blue);
            display: flex;
            align-items: center;
        }

        .required::after {
            content: " *";
            color: var(--danger-color);
        }

        .form-text {
            color: #718096;
            font-size: 0.875rem;
        }

        .invalid-feedback {
            color: var(--danger-color);
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }

        .is-invalid {
            border-color: var(--danger-color);
        }

        .action-section {
            background: var(--soft-gray);
            padding: 2rem;
            border-radius: 12px;
            text-align: center;
        }

        .readonly-field {
            background-color: #f8f9fa;
            cursor: not-allowed;
        }
</style>
