
 <script src="{{ asset('assets/js/jquery.min.js') }}"></script>
  <!-- Bootstrap bundle JS -->
  <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
  <!--plugins-->

  <script src="{{ asset('assets/plugins/simplebar/js/simplebar.min.js') }}"></script>
  <script src="{{ asset('assets/plugins/metismenu/js/metisMenu.min.js') }}"></script>
  <script src="{{ asset('assets/plugins/perfect-scrollbar/js/perfect-scrollbar.js') }}"></script>
  <script src="{{ asset('assets/plugins/vectormap/jquery-jvectormap-2.0.2.min.js') }}"></script>
  <script src="{{ asset('assets/plugins/vectormap/jquery-jvectormap-world-mill-en.js') }}"></script>
  <script src="{{ asset('assets/js/pace.min.js') }}"></script>
  <script src="{{ asset('assets/plugins/chartjs/js/Chart.min.js') }}"></script>
  <script src="{{ asset('assets/plugins/chartjs/js/Chart.extension.js') }}"></script>
  <script src="{{ asset('assets/plugins/apexcharts-bundle/js/apexcharts.min.js') }}"></script>
  <script src="{{ asset('assets/plugins/fancy-file-uploader/jquery.fileupload.js')}}"></script>
  <script src="{{ asset('assets/plugins/fancy-file-uploader/jquery.ui.widget.js')}}"></script>
  <script src="{{ asset('assets/plugins/fancy-file-uploader/jquery.fancy-fileupload.js')}}"></script>
  <script src="{{ asset('assets/plugins/fancy-file-uploader/jquery.iframe-transport.js')}}"></script>
  <script src="{{ asset('assets/plugins/select2/js/select2.min.js')}}"></script>
  <script src="{{ asset('assets/js/form-select2.js')}}"></script>
  <!--app-->
  <script src="{{ asset('assets/js/app.js') }}"></script>
  <script src="{{ asset('assets/js/index4.js') }}"></script>
  <script>
    new PerfectScrollbar(".best-product")
 </script>
    {{-- <script>
		$('#fancy-file-upload').FancyFileUpload({
			params: {
				action: 'fileuploader'
			},
			maxfilesize: 1000000
		});
	 </script> --}}
@stack('datatable-scripts')
@stack('datatable-scripts')


