<!-- ✅ jQuery en premier -->
<script src="{{ asset('back_auth/assets/js/jquery-3.5.1.min.js') }}"></script>


<!-- ✅ Popper.js (obligatoire pour Dropdowns Bootstrap 4) -->
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>


<!-- ✅ Bootstrap JS -->
<script src="{{ asset('back_auth/assets/js/bootstrap.min.js') }}"></script>

<!-- Slimscroll plugin -->
<script src="{{ asset('back_auth/assets/plugins/slimscroll/jquery.slimscroll.min.js') }}"></script>

<!-- Charts (Raphael + Morris.js) -->
<script src="{{ asset('back_auth/assets/plugins/raphael/raphael.min.js') }}"></script>
<script src="{{ asset('back_auth/assets/plugins/morris/morris.min.js') }}"></script>
<script src="{{ asset('back_auth/assets/js/chart.morris.js') }}"></script>

<!-- Custom Scripts -->
<script src="{{ asset('back_auth/assets/js/script.js') }}"></script>


<script src="https://cdnjs.cloudflare.com/ajax/libs/izitoast/1.4.0/js/iziToast.min.js"
    integrity="sha512-Zq9o+E00xhhR/7vJ49mxFNJ0KQw1E1TMWkPTxrWcnpfEFDEXgUiwJHIKit93EW/XxE31HSI5GEOW06G6BF1AtA=="
    crossorigin="anonymous" referrerpolicy="no-referrer"></script>

<!-- CSS -->


<!-- JS -->
{{-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-tagsinput/0.8.0/bootstrap-tagsinput.min.js"></script>





<!-- jQuery -->
{{-- <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script> --}}

<!-- Bootstrap Tags Input -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-tagsinput/0.8.0/bootstrap-tagsinput.min.js"></script>

<script>
    $(document).ready(function() {
        // Forcer le comportement d'ajout avec virgule ou espace
        $('input[data-role="tagsinput"]').on('keypress', function(e) {
            if (e.which === 44 || e.which === 32) { // 44 = virgule, 32 = espace
                e.preventDefault();
                let $input = $(this);
                let val = $input.val().trim();
                if (val !== '') {
                    $input.tagsinput('add', val);
                    $input.val('');
                }
            }
        });
    });

    document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('userForm');
    const submitBtn = document.getElementById('submitBtn');
    const loading = document.querySelector('.loading');
    const btnText = document.querySelector('.btn-text');

    // Gestion de la soumission du formulaire
    if (form && submitBtn) {
        form.addEventListener('submit', function(e) {
            // Afficher le loading
            loading.classList.add('show');
            submitBtn.disabled = true;
            btnText.textContent = 'Traitement en cours...';
        });
    }

    // Prévisualisation de l'image
    const imageInput = document.querySelector('input[name="image"]');
    if (imageInput) {
        imageInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    let preview = document.querySelector('.image-preview');
                    if (!preview) {
                        preview = document.createElement('img');
                        preview.className = 'image-preview user-image mt-2';
                        preview.width = 120;
                        preview.height = 120;
                        preview.style.objectFit = 'cover';
                        imageInput.parentNode.appendChild(preview);
                    }
                    preview.src = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        });
    }

    // Auto-hide alerts
    setTimeout(function() {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(function(alert) {
            if (alert.classList.contains('alert-success')) {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            }
        });
    }, 5000);
});
</script>

<script>
    $(document).ready(function () {
        $('input[data-role="tagsinput"]').tagsinput();
    });



<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-submit du formulaire de recherche quand on change la compagnie
    const compagnieSelect = document.getElementById('compagnie');
    if (compagnieSelect) {
        compagnieSelect.addEventListener('change', function() {
            document.getElementById('searchForm').submit();
        });
    }

    // Recherche en temps réel (optionnel - décommentez si souhaité)
    /*
    const searchInput = document.getElementById('search');
    let searchTimeout;

    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            document.getElementById('searchForm').submit();
        }, 500);
    });
    */

    // Auto-hide des alertes de succès
    setTimeout(function() {
        const alerts = document.querySelectorAll('.alert-success');
        alerts.forEach(function(alert) {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        });
    }, 5000);

    // Animation des cartes statistiques
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    });

    document.querySelectorAll('.stats-card').forEach(card => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        card.style.transition = 'all 0.6s ease';
        observer.observe(card);
    });

    // Confirmation de suppression améliorée
    document.querySelectorAll('form[onsubmit*="confirm"]').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();

            if (confirm('⚠️ Êtes-vous sûr de vouloir supprimer cette gare ?\n\nCette action est irréversible.')) {
                this.submit();
            }
        });
    });
});
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Auto-dismiss alerts after 5 seconds
    document.addEventListener('DOMContentLoaded', function() {
        const alerts = document.querySelectorAll('.alert-dismissible');
        alerts.forEach(function(alert) {
            setTimeout(function() {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            }, 5000);
        });
    });

    // Form validation enhancement
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.querySelector('form');
        if (form) {
            form.addEventListener('submit', function(e) {
                const requiredFields = form.querySelectorAll('[required]');
                let isValid = true;

                requiredFields.forEach(function(field) {
                    if (!field.value.trim()) {
                        field.classList.add('is-invalid');
                        isValid = false;
                    } else {
                        field.classList.remove('is-invalid');
                    }
                });

                if (!isValid) {
                    e.preventDefault();
                    // Scroll to first invalid field
                    const firstInvalid = form.querySelector('.is-invalid');
                    if (firstInvalid) {
                        firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        firstInvalid.focus();
                    }
                }
            });

            // Remove is-invalid class on input
            const inputs = form.querySelectorAll('input, select, textarea');
            inputs.forEach(function(input) {
                input.addEventListener('input', function() {
                    if (this.classList.contains('is-invalid') && this.value.trim()) {
                        this.classList.remove('is-invalid');
                    }
                });
            });
        }
    });
</script>
</script>




{{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/izitoast/1.4.0/js/iziToast.min.js"
    integrity="sha512-Zq9o+E00xhhR/7vJ49mxFNJ0KQw1E1TMWkPTxrWcnpfEFDEXgUiwJHIKit93EW/XxE31HSI5GEOW06G6BF1AtA=="
    crossorigin="anonymous" referrerpolicy="no-referrer"></script> --}}

{{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/izitoast/1.4.0/js/iziToast.js"
    integrity="sha512-OmBbzhZ6lgh87tQFDVBHtwfi6MS9raGmNvUNTjDIBb/cgv707v9OuBVpsN6tVVTLOehRFns+o14Nd0/If0lE/A=="
    crossorigin="anonymous" referrerpolicy="no-referrer"></script> --}}

{{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/izitoast/1.3.0/js/iziToast.min.js"
    integrity="sha512-uYozdwEBPuEoXLQcMWQjmM9M6zOC5njCV4ajFgImWv6M/fn6x/Usp/JPKyAQT0H5YfzUrxmYoXWePgNjV4T92A=="
    crossorigin="anonymous" referrerpolicy="no-referrer"></script> --}}


{{-- <script src="
https://cdn.jsdelivr.net/npm/izitoast@1.4.0/dist/js/iziToast.min.js
"></script> --}}
