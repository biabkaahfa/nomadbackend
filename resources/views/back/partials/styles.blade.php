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


<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-tagsinput/0.8.0/bootstrap-tagsinput.css">

<style>
/* --- DÉFINITIONS DES VARIABLES CSS PAR DÉFAUT --- */
:root {
    --couleur-principale: #6c7ae0; /* Bleu par défaut */
    --couleur-secondaire: #a8b8f0; /* Bleu clair par défaut */
    --success-color: #52c41a;
    --warning-color: #faad14;
    --danger-color: #ff4d4f;
    --light-blue: #e6f3ff;
    --soft-gray: #f8f9fa;
    --border-color: #e8e8e8;
    --text-color: #4a5568; /* Couleur de texte par défaut */
    --heading-color: #2d3748; /* Couleur des titres par défaut */
}

/* --- APPLICATION DE CES VARIABLES DANS VOS STYLES EXISTANTS --- */
/* C'est ici que vous remplacez les couleurs en dur par les variables */

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
    font-size: 2.2rem; /* ou 2rem selon votre préférence finale */
}
.card-glass {
    background: rgba(255, 255, 255, 0.98);
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2); /* Peut-être une couleur de bordure plus neutre ou basée sur une variable */
    padding: 2rem;
    margin-bottom: 2rem;
}

/* Boutons */
.btn-primary-gradient, .btn-primary-soft { /* Regroupez si leurs styles sont similaires ou choisissez une nomenclature */
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
    /* Utilisez des couleurs cohérentes pour les ombres, ou une variable pour les ombres */
    box-shadow: 0 4px 12px rgba(var(--couleur-principale-rgb, 108, 122, 224), 0.3);
    color: #fff;
}

/* Les autres boutons, etc., devront aussi utiliser vos variables */
.btn-success-soft {
    background: linear-gradient(135deg, var(--success-color), #73d13d);
    /* ... */
}
.btn-warning-soft {
    background: linear-gradient(135deg, var(--warning-color), #ffc53d);
    /* ... */
}
.btn-danger-soft {
    background: linear-gradient(135deg, var(--danger-color), #ff7875);
    /* ... */
}

/* Tables */
.table-modern thead {
    background: linear-gradient(135deg, var(--couleur-principale), var(--couleur-secondaire));
    color: white;
}
.table-modern tbody tr:hover {
    background-color: var(--light-blue); /* Ou une version plus claire de --couleur-principale */
    transform: scale(1.005);
}
.table-modern tbody td {
    border: none;
}

/* Pagination */
.pagination .page-link {
    color: var(--couleur-principale);
    border: 1px solid var(--border-color);
    /* ... */
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
    box-shadow: 0 0 0 0.2rem rgba(var(--couleur-principale-rgb, 108, 122, 224), 0.15);
}
.required::after {
    content: " *";
    color: var(--danger-color);
}

/* ... continuez pour toutes les règles CSS pertinentes ... */

/* Badge compagnie (si lié au thème) */
.badge-compagnie {
    background: linear-gradient(135deg, var(--couleur-principale), var(--couleur-secondaire));
    color: white;
    /* ... */
}

/* Pour le `body` lui-même, vous pouvez choisir si vous voulez un dégradé ou une couleur unie */
body {
    /* Si vous voulez un dégradé basé sur vos couleurs de thème */
    background: linear-gradient(135deg, var(--couleur-principale-light, #f5f7fa) 0%, var(--couleur-secondaire-light, #c3cfe2) 100%);
    /* Ou simplement une couleur unie: */
    /* background-color: var(--soft-gray); */
    /* Ou la couleur principale */
    /* background-color: var(--couleur-principale); */
}


/* Styles généraux comme avant */
.card-glass {
    background: rgba(255, 255, 255, 0.95);
    border-radius: 20px;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.18);
    padding: 2.5rem;
}
/* ... tous vos autres styles qui n'ont pas besoin de changer dynamiquement ... */
</style>
