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
</style>
