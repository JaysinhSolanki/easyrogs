<!-- Vendor scripts --> 
<script src="<?= VENDOR_URL ?>jquery/dist/jquery.min.js"></script>

<?/* 
	!!!! WARNING !!!!
	Changing the load order of this (jquery.uploadfile) plugin could break it !!!!
	https://github.com/EasyRogs/easyrogs/pull/420
*/?>
<script src="<?= VENDOR_URL ?>jquery.uploadfile.min.js"></script>

<script src="<?= VENDOR_URL ?>jquery-ui/jquery-ui.min.js"></script> 
<script src="<?= VENDOR_URL ?>toastr/build/toastr.min.js"></script> 
<script src="<?= VENDOR_URL ?>sweetalert/lib/sweet-alert.min.js"></script>
<script src="<?= VENDOR_URL ?>metisMenu/dist/metisMenu.min.js"></script> 
<script src="<?= VENDOR_URL ?>iCheck/icheck.min.js"></script> 

<script src="<?= VENDOR_URL ?>jquery-validation/jquery.validate.min.js"></script>
<script src="<?= VENDOR_URL ?>customjscss/jquery.numslider.js"></script>
<script src="<?= VENDOR_URL ?>jquery.tablesorter.js"></script>
<script src="<?= VENDOR_URL ?>moment/moment.js"></script>
<script src="<?= VENDOR_URL ?>daterangepicker.min.js"></script>
<script src="<?= VENDOR_URL ?>bootstrap/dist/js/bootstrap.min.js"></script>
<script src="<?= VENDOR_URL ?>bootstrap-datepicker.js"></script>
<script src="<?= VENDOR_URL ?>bootstrap-clockpicker.min.js"></script>
<script src="<?= VENDOR_URL ?>bootstrap-datetimepicker.js"></script>

<!-- isInViewport jQuery plugin @ https://cdnjs.cloudflare.com/ajax/libs/is-in-viewport/3.0.4/isInViewport.js -->
<script src="<?= VENDOR_URL ?>is-in-viewport/3.0.4/isInViewport.min.js"></script> 

<script src="<?= VENDOR_URL ?>jquery.initialize/jquery.initialize.min.js"></script>

<script src="<?= VENDOR_URL ?>jquery.form.js"></script>
<script src="<?= VENDOR_URL ?>jquery-loading-overlay/2.1.7/loadingoverlay.min.js"></script>
<script src="<?= VENDOR_URL ?>select2.full.js"></script>

<!-- DataTables -->
<script src="<?= VENDOR_URL ?>datatables/media/js/jquery.dataTables.min.js"></script>
<!-- DataTables buttons scripts -->
<script src="<?= VENDOR_URL ?>pdfmake/build/pdfmake.min.js"></script>
<script src="<?= VENDOR_URL ?>pdfmake/build/vfs_fonts.js"></script>

<script src="<?= VENDOR_URL ?>dropzone.js"></script>
<script src="<?= VENDOR_URL ?>ckeditor/ckeditor.js"></script>

<!-- easyrogs -->
<!--script src="<?= VENDOR_URL ?>homer.js"></script--> 
<script src="<?= VENDOR_URL ?>header.js"></script> 
<script src="<?= VENDOR_URL ?>common.js"></script> 

<script type="text/javascript">
	const APP_GOOGLE_ANALYTICS_ID = '<?= APP_GOOGLE_ANALYTICS_ID ?>';
</script>
<script src="<?= ASSETS_URL ?>custom.js"></script>

<script type="text/javascript">
	function trackVideos() {
		const videoTracker = jQuery.initialize( 'video', function() { 
			function _logVideo(action, element) { 
				$this = $(element);
				trackEvent( action, { event_category: 'video', event_label: $this.data('src') || $this.src, } );
			}
			$(this)
				//.on( 'error',		ev => { _logVideo( 'error', ev.target ) } )
				.on( 'play',		ev => { _logVideo( 'play', ev.target ) } )
				.on( 'pause ended', ev => { _logVideo( 'stop', ev.target ) } )
		} );
		//videoTracker.disconnect();
	}
	jQuery( $ => {
		trackVideos();
	} );

	jQuery( $ => {
		CKEDITOR.addCss(`h4, h5 { font-weight: 600; font-size: 14px; }
						.text-center { text-align: center; }
						`);
		CKEDITOR.config.allowedContent = 'u b i strong em span div ol ul li  table tr th td h3 h5(*){*}[*]';
		// CKEDITOR.on( 'instanceReady', _ => { // check the filter is working properly
		// 	console.table( _.editor.filter.allowedContent.map(_=>_.elements) );
		// } );
	});
</script>

<?php if( !@$_ENV['PAY_DISABLED'] ) { ?>
	<script src="https://js.stripe.com/v3/"></script>
<?php } ?>