<!-- ✅ jQuery en premier -->
<script src="{{ asset('back_auth/assets/js/jquery-3.5.1.min.js') }}"></script>


<!-- ✅ Popper.js (obligatoire pour Dropdowns Bootstrap 4) -->
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>


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
</script>

<script>
    $(document).ready(function () {
        $('input[data-role="tagsinput"]').tagsinput();
    });
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
