

  <!-- Bootstrap bundle JS -->
  <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
  <!--plugins-->
  <script src="{{ asset('assets/js/jquery.min.js') }}"></script>
  <script src="{{ asset('assets/plugins/simplebar/js/simplebar.min.js') }}"></script>
  <script src="{{ asset('assets/plugins/metismenu/js/metisMenu.min.js') }}"></script>
  <script src="{{ asset('assets/plugins/perfect-scrollbar/js/perfect-scrollbar.js') }}"></script>
  <script src="{{ asset('assets/plugins/vectormap/jquery-jvectormap-2.0.2.min.js') }}"></script>
  <script src="{{ asset('assets/plugins/vectormap/jquery-jvectormap-world-mill-en.js') }}"></script>
  <script src="{{ asset('assets/js/pace.min.js') }}"></script>
  <script src="{{ asset('assets/plugins/chartjs/js/Chart.min.js') }}"></script>
  <script src="{{ asset('assets/plugins/chartjs/js/Chart.extension.js') }}"></script>
  <script src="{{ asset('assets/plugins/apexcharts-bundle/js/apexcharts.min.js') }}"></script>
  <!--app-->
  <script src="{{ asset('assets/js/app.js') }}"></script>
  <script src="{{ asset('assets/js/index4.js') }}"></script>
  <script>
    new PerfectScrollbar(".best-product")
 </script>
 <script>
$(document).ready(function() {
    $('#users-table').DataTable({
        "paging": true,         // pagination
        "searching": true,      // search box
        "ordering": true,       // sortable columns
        "info": true,           // table info
        "lengthMenu": [5, 10, 25, 50], // page length options
        "pageLength": 10        // default page length
    });
});
</script>

