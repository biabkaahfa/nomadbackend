<link rel="shortcut icon" type="image/x-icon" href="{{ asset('back_auth/assets/img/favicon.png') }}" />

<link rel="stylesheet" href="{{ asset('back_auth/assets/css/bootstrap.min.css') }}" />

<link rel="stylesheet" href="{{ asset('back_auth/assets/plugins/fontawesome/css/fontawesome.min.css') }}" />
<link rel="stylesheet" href="{{ asset('back_auth/assets/plugins/fontawesome/css/all.min.css') }}" />

<link rel="stylesheet" href="{{ asset('back_auth/assets/css/feathericon.min.css') }}" />

<link rel="stylesheet" href="https://cdn.oesmith.co.uk/morris-0.5.1.css" />
<link rel="stylesheet" href="{{ asset('back_auth/assets/plugins/morris/morris.css') }}" />

<link rel="stylesheet" href="{{ asset('back_auth/assets/css/style.css') }}" />

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/izitoast/1.4.0/css/iziToast.css"
    integrity="sha512-DIW4FkYTOxjCqRt7oS9BFO+nVOwDL4bzukDyDtMO7crjUZhwpyrWBFroq+IqRe6VnJkTpRAS6nhDvf0w+wHmxg=="
    crossorigin="anonymous" referrerpolicy="no-referrer" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-tagsinput/0.8.0/bootstrap-tagsinput.css">

<style>
/* --- DÉFINITIONS DES VARIABLES CSS AVEC COULEURS VERTES --- */
:root {
    --couleur-principale: #009688; /* Vert principal */
    --couleur-secondaire: #20c997; /* Vert secondaire */
    --success-color: #28a745;
    --warning-color: #ffc107;
    --danger-color: #dc3545;
    --light-blue: #e6f7f0;
    --soft-gray: #f8f9fa;
    --border-color: #dee2e6;
    --text-color: #4a5568;
    --heading-color: #2d3748;
}

/* --- APPLICATION DE CES VARIABLES DANS VOS STYLES EXISTANTS --- */
body {
    background: linear-gradient(135deg, var(--soft-gray) 0%, var(--border-color) 100%);
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    min-height: 100vh;
    color: var(--text-color);
}
.page-title {
    color: var(--heading-color);
    font-weight: 700;
    margin-bottom: 2rem;
    text-align: center;
    font-size: 2.2rem;
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

/* Boutons */
.btn-primary-gradient, .btn-primary-soft {
    background: linear-gradient(135deg, var(--couleur-principale), var(--couleur-secondaire));
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
.btn-primary-gradient:hover, .btn-primary-soft:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(40, 167, 69, 0.3);
    color: #fff;
}

/* Les autres boutons */
.btn-success-soft {
    background: linear-gradient(135deg, var(--success-color), #34ce57);
}
.btn-warning-soft {
    background: linear-gradient(135deg, var(--warning-color), #ffd351);
}
.btn-danger-soft {
    background: linear-gradient(135deg, var(--danger-color), #e4606d);
}

/* Tables */
.table-modern thead {
    background: linear-gradient(135deg, var(--couleur-principale), var(--couleur-secondaire));
    color: white;
}
.table-modern tbody tr:hover {
    background-color: var(--light-blue);
    transform: scale(1.005);
}
.table-modern tbody td {
    border: none;
}

/* Pagination */
.pagination .page-link {
    color: var(--couleur-principale);
    border: 1px solid var(--border-color);
}
.pagination .page-link:hover {
    background-color: var(--light-blue);
    border-color: var(--couleur-principale);
}
.pagination .page-item.active .page-link {
    background-color: var(--couleur-principale);
    border-color: var(--couleur-principale);
}

/* Formulaires */
.form-floating > .form-control:focus ~ label,
.form-floating > .form-control:not(:placeholder-shown) ~ label,
.form-floating > .form-select ~ label {
    color: var(--couleur-principale);
}
.form-control:focus, .form-select:focus {
    border-color: var(--couleur-principale);
    box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.15);
}
.required::after {
    content: " *";
    color: var(--danger-color);
}

/* Badge compagnie */
.badge-compagnie {
    background: linear-gradient(135deg, var(--couleur-principale), var(--couleur-secondaire));
    color: white;
}

body {
    background: linear-gradient(135deg, #f5f9f7 0%, #e6f2ed 100%);
}

/* Styles généraux */
.card-glass {
    background: rgba(255, 255, 255, 0.95);
    border-radius: 20px;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.18);
    padding: 2.5rem;
}

.carte-abonnement {
    width: 100%;
    max-width: 900px;
    height: 280px;
    border-radius: 20px;
    padding: 25px;
    color: white;
    position: relative;
    overflow: hidden;
    box-shadow: 0 8px 20px rgba(0,0,0,0.3);
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    font-family: 'Arial', sans-serif;
}

.carte-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.carte-logo img {
    height: 50px;
}

.carte-body {
    margin-top: 20px;
}

.carte-footer {
    display: flex;
    justify-content: space-between;
    align-items: flex-end;
}

.qr-code {
    background: white;
    padding: 5px;
    border-radius: 10px;
}
</style>

@push('styles')
<style>
    /* Variables CSS avec couleurs vertes */
    :root {
        --primary: #28a745;
        --primary-soft: rgba(40, 167, 69, 0.1);
        --success: #20c997;
        --success-soft: rgba(32, 201, 151, 0.1);
        --warning: #ffc107;
        --warning-soft: rgba(255, 193, 7, 0.1);
        --danger: #dc3545;
        --danger-soft: rgba(220, 53, 69, 0.1);
        --info: #17a2b8;
        --info-soft: rgba(23, 162, 184, 0.1);
        --dark: #343a40;
        --dark-soft: rgba(52, 58, 64, 0.1);
    }

    /* Header du Dashboard */
    .dashboard-header {
        background: linear-gradient(135deg, var(--primary) 0%, #20c997 100%);
        margin: -1.5rem -1.5rem 2rem -1.5rem;
        position: relative;
        overflow: hidden;
    }

    .dashboard-header::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 100" fill="rgba(255,255,255,0.05)"><polygon points="0,0 1000,50 1000,100 0,100"/></svg>');
        background-size: cover;
    }

    .welcome-section h1 {
        text-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .current-time {
        background: rgba(255,255,255,0.1);
        backdrop-filter: blur(10px);
        border-radius: 15px;
        padding: 1rem 1.5rem;
        border: 1px solid rgba(255,255,255,0.2);
    }

    /* Cartes de Statistiques */
    .stats-card {
        background: white;
        border-radius: 20px;
        padding: 1.5rem;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        border: 1px solid rgba(0,0,0,0.05);
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .stats-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: var(--primary);
    }

    .stats-card.success::before { background: var(--success); }
    .stats-card.warning::before { background: var(--warning); }
    .stats-card.info::before { background: var(--info); }
    .stats-card.dark::before { background: var(--dark); }
    .stats-card.premium::before { background: linear-gradient(135deg, #28a745 0%, #20c997 100%); }

    .stats-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 12px 30px rgba(0, 0, 0, 0.15);
    }

    .stats-icon {
        width: 60px;
        height: 60px;
        border-radius: 15px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 1rem;
        background: var(--primary-soft);
        color: var(--primary);
        font-size: 1.5rem;
    }

    .stats-card.success .stats-icon { background: var(--success-soft); color: var(--success); }
    .stats-card.warning .stats-icon { background: var(--warning-soft); color: var(--warning); }
    .stats-card.info .stats-icon { background: var(--info-soft); color: var(--info); }
    .stats-card.dark .stats-icon { background: var(--dark-soft); color: var(--dark); }
    .stats-card.premium .stats-icon { background: linear-gradient(135deg, rgba(40, 167, 69, 0.1) 0%, rgba(32, 201, 151, 0.1) 100%); color: #28a745; }

    .stats-content h3 {
        font-size: 2rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
        color: #2c3e50;
    }

    .stats-content p {
        font-size: 0.9rem;
        color: #6c757d;
        margin-bottom: 0.5rem;
        font-weight: 500;
    }

    .stats-trend {
        font-size: 0.75rem;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 0.25rem;
    }

    .stats-trend.up { color: var(--success); }
    .stats-trend.down { color: var(--danger); }

    /* Cartes Modernes */
    .card-modern {
        border: none;
        border-radius: 20px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        background: white;
        overflow: hidden;
    }

    .card-modern .card-header {
        background: white;
        border-bottom: 1px solid rgba(0,0,0,0.05);
        padding: 1.5rem;
    }

    .card-title {
        font-weight: 600;
        color: #2c3e50;
        margin: 0;
        display: flex;
        align-items: center;
    }

    .card-actions {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    /* Sections Gare */
    .garre-section {
        margin-bottom: 2rem;
    }

    .garre-header {
        display: flex;
        justify-content: between;
        align-items: center;
        margin-bottom: 1.5rem;
        padding: 1rem;
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border-radius: 15px;
    }

    .garre-info {
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .garre-avatar {
        width: 50px;
        height: 50px;
        border-radius: 12px;
        background: var(--primary-soft);
        color: var(--primary);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
    }

    /* Grille des Voyages */
    .voyages-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
        gap: 1.25rem;
    }

    .voyage-card {
        background: white;
        border-radius: 15px;
        padding: 1.25rem;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.06);
        border: 1px solid rgba(0,0,0,0.05);
        transition: all 0.3s ease;
        position: relative;
    }

    .voyage-card:hover {
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
        transform: translateY(-3px);
    }

    .voyage-card.departure {
        border-left: 4px solid var(--primary);
    }

    .voyage-card.arrival {
        border-left: 4px solid var(--success);
    }

    .voyage-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1rem;
    }

    .voyage-direction {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .direction-badge {
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        color: white;
    }

    .voyage-time {
        font-weight: 600;
        color: #2c3e50;
        font-size: 1.1rem;
    }

    .status-badge {
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .status-badge.completed { background: var(--dark-soft); color: var(--dark); }
    .status-badge.imminent { background: var(--warning-soft); color: var(--warning); }
    .status-badge.upcoming { background: var(--success-soft); color: var(--success); }
    .status-badge.active { background: var(--success-soft); color: var(--success); }

    /* Route */
    .voyage-route {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 1rem;
        padding: 1rem;
        background: #f8f9fa;
        border-radius: 10px;
    }

    .route-point {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-weight: 500;
    }

    .route-point.departure { color: var(--success); }
    .route-point.arrival { color: var(--danger); }

    .route-line {
        color: #6c757d;
        flex: 1;
        text-align: center;
    }

    /* Détails du voyage */
    .voyage-details {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 0.75rem;
        margin-bottom: 1rem;
    }

    .detail-item {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.875rem;
        color: #6c757d;
    }

    .detail-item i {
        width: 16px;
    }

    /* Progress bar */
    .voyage-progress {
        margin-top: 1rem;
    }

    .progress {
        height: 6px;
        border-radius: 10px;
        background: #e9ecef;
        overflow: hidden;
    }

    .progress-bar {
        border-radius: 10px;
        transition: width 0.6s ease;
    }

    /* États vides */
    .empty-state {
        text-align: center;
        padding: 3rem 2rem;
        color: #6c757d;
    }

    .empty-state i {
        margin-bottom: 1rem;
        opacity: 0.5;
    }

    .empty-state h5 {
        margin-bottom: 0.5rem;
        color: #495057;
    }

    .empty-state-sm {
        text-align: center;
        padding: 2rem 1rem;
        color: #6c757d;
    }

    /* Badges */
    .badge {
        padding: 0.35em 0.65em;
        font-weight: 600;
    }

    .bg-primary-soft {
        background: var(--primary-soft);
        color: var(--primary);
    }

    .bg-success-soft {
        background: var(--success-soft);
        color: var(--success);
    }

    .bg-info-soft {
        background: var(--info-soft);
        color: var(--info);
    }

    /* Animations */
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .animate-in {
        animation: fadeInUp 0.6s ease-out;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .voyages-grid {
            grid-template-columns: 1fr;
        }

        .voyage-details {
            grid-template-columns: 1fr;
        }

        .garre-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 1rem;
        }

        .stats-card {
            padding: 1rem;
        }

        .stats-content h3 {
            font-size: 1.5rem;
        }
    }

    /* Alertes élégantes */
    .alert-elegant {
        border: none;
        border-radius: 15px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        border-left: 4px solid var(--success);
    }
</style>
@endpush

@push('styles')
<style>
    /* Styles spécifiques pour chef de gare */
    .voyage-avatar {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        background: var(--primary-soft);
        color: var(--primary);
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .mini-progress {
        height: 4px;
        border-radius: 2px;
    }

    .table-modern th {
        border: none;
        font-weight: 600;
        color: #495057;
        background: #f8f9fa;
        padding: 1rem;
    }

    .table-modern td {
        padding: 1rem;
        vertical-align: middle;
        border-color: #f1f3f4;
    }

    .voyage-row:hover {
        background-color: #f8f9fa;
    }

    /* Cartes d'action rapide */
    .quick-action-card {
        background: white;
        border-radius: 15px;
        padding: 1.5rem;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        border: 1px solid rgba(0,0,0,0.05);
        text-align: center;
        transition: all 0.3s ease;
    }

    .quick-action-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.12);
    }

    .action-icon {
        width: 60px;
        height: 60px;
        border-radius: 15px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1rem;
        color: white;
        font-size: 1.5rem;
    }

    /* Styles pour réceptionniste */
    .voyages-sales-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
        gap: 1.5rem;
    }

    .voyage-sale-card {
        background: white;
        border-radius: 15px;
        padding: 1.5rem;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        border: 1px solid rgba(0,0,0,0.05);
        transition: all 0.3s ease;
        position: relative;
    }

    .voyage-sale-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
    }

    .voyage-sale-card.sold-out {
        opacity: 0.7;
        background: #f8f9fa;
    }

    .voyage-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 1rem;
    }

    .voyage-title h5 {
        margin: 0;
        color: #2c3e50;
    }

    .voyage-type {
        padding: 0.25rem 0.75rem;
        border-radius: 15px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .voyage-type.departure {
        background: var(--primary-soft);
        color: var(--primary);
    }

    .voyage-type.arrival {
        background: var(--success-soft);
        color: var(--success);
    }

    .voyage-price {
        text-align: right;
    }

    .price-tag {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--primary);
    }

    .detail-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 0.75rem;
    }

    .detail-item {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.9rem;
    }

    .occupation-bar {
        margin: 1rem 0;
    }

    .progress {
        height: 8px;
        border-radius: 10px;
    }

    .progress-text {
        font-size: 0.7rem;
        font-weight: 600;
    }

    .sale-actions {
        display: flex;
        gap: 0.5rem;
        align-items: center;
    }

    .btn-sale {
        flex: 1;
    }

    .availability-alert {
        position: absolute;
        top: -10px;
        left: 50%;
        transform: translateX(-50%);
        background: var(--warning);
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        white-space: nowrap;
    }

    /* Statistiques de vente */
    .sales-stats {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    .sale-stat-item {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1rem;
        background: #f8f9fa;
        border-radius: 10px;
    }

    .sale-stat-item .stat-icon {
        width: 50px;
        height: 50px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.25rem;
    }

    /* Actions rapides */
    .quick-actions {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
    }

    .quick-action-btn {
        background: white;
        border: 2px dashed #e9ecef;
        border-radius: 12px;
        padding: 1rem;
        text-align: center;
        transition: all 0.3s ease;
        cursor: pointer;
    }

    .quick-action-btn:hover {
        border-color: var(--primary);
        background: var(--primary-soft);
    }

    .quick-action-btn .action-icon {
        width: 40px;
        height: 40px;
        margin: 0 auto 0.5rem;
    }

    /* Dernières ventes */
    .recent-sales {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
    }

    .sale-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.75rem;
        background: #f8f9fa;
        border-radius: 8px;
    }

    .sale-badge {
        padding: 0.25rem 0.75rem;
        border-radius: 15px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .sale-badge.success {
        background: var(--success-soft);
        color: var(--success);
    }

    /* Légende */
    .legend {
        display: flex;
        align-items: center;
    }

    .legend-item {
        display: flex;
        align-items: center;
        font-size: 0.875rem;
        color: #6c757d;
    }

    .legend-color {
        width: 12px;
        height: 12px;
        border-radius: 50%;
        margin-right: 0.5rem;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .voyages-sales-grid {
            grid-template-columns: 1fr;
        }

        .quick-actions {
            grid-template-columns: 1fr;
        }

        .voyage-header {
            flex-direction: column;
            gap: 1rem;
        }

        .voyage-price {
            text-align: left;
        }
    }
</style>
@endpush

@push('styles')
<style>
    /* Styles pour la fonctionnalité multi-gares */
    .garres-section {
        margin: 1rem 0;
        padding: 1rem;
        background: #f8f9fa;
        border-radius: 10px;
        border-left: 4px solid var(--success);
    }

    .garres-header {
        display: flex;
        align-items: center;
        margin-bottom: 0.5rem;
    }

    .garres-list {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
    }

    .garre-badge {
        padding: 0.25rem 0.75rem;
        border-radius: 15px;
        font-size: 0.75rem;
        font-weight: 500;
        background: white;
        border: 1px solid #dee2e6;
        color: #495057;
    }

    .garre-badge.current-garre {
        background: var(--success-soft);
        border-color: var(--success);
        color: var(--success);
        font-weight: 600;
    }

    .garre-badge.more {
        background: var(--primary-soft);
        border-color: var(--primary);
        color: var(--primary);
    }

    .compagnie-badge {
        padding: 0.25rem 0.75rem;
        border-radius: 12px;
        font-size: 0.75rem;
        font-weight: 500;
        background: var(--info-soft);
        color: var(--info);
        border: 1px solid var(--info);
    }

    .voyage-meta {
        margin-top: 0.5rem;
    }

    .search-filters {
        background: white;
        padding: 1.5rem;
        border-radius: 15px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        border: 1px solid rgba(0,0,0,0.05);
    }

    .multi-gare-info {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    .info-item {
        display: flex;
        align-items: center;
        padding: 0.75rem;
        background: #f8f9fa;
        border-radius: 8px;
        font-size: 0.9rem;
    }

    /* Animation pour les cartes filtrées */
    .voyage-sale-card {
        transition: all 0.3s ease;
    }

    .voyage-sale-card[style*="display: none"] {
        opacity: 0;
        transform: scale(0.9);
    }

    /* Styles pour le modal de vente */
    .sale-modal-content {
        max-height: 70vh;
        overflow-y: auto;
    }

    .bg-warning-soft {
        background-color: rgba(255, 193, 7, 0.1);
        border: 1px solid rgba(255, 193, 7, 0.2);
    }

    /* Responsive */
    @media (max-width: 768px) {
        .garres-list {
            flex-direction: column;
        }

        .garre-badge {
            width: fit-content;
        }

        .search-filters .row {
            gap: 1rem;
        }
    }
</style>
@endpush
