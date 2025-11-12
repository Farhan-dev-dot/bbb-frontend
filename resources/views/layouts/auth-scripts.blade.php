<!--begin::Script-->
<!--begin::Required Plugin(popperjs for Bootstrap 5)-->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" crossorigin="anonymous">
</script>
<!--end::Required Plugin(popperjs for Bootstrap 5)-->

<!--begin::Required Plugin(Bootstrap 5)-->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.min.js" crossorigin="anonymous"></script>
<!--end::Required Plugin(Bootstrap 5)-->


<!-- Pastikan jQuery dimuat SEBELUM script kamu -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!--begin::Required Plugin(AdminLTE)-->
<script src="{{ asset('js/adminlte.js') }}"></script>
<script src="{{ asset('js/bbb.js') }}"></script>
<!--end::Required Plugin(AdminLTE)-->


@stack('scripts')
<!--end::Script-->
