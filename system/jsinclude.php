<!-- Vendor scripts --> 
<script src="<?= VENDOR_URL ?>jquery-ui/jquery-ui.min.js"></script> 
<script src="<?= VENDOR_URL ?>toastr/build/toastr.min.js"></script> 
<script src="<?= VENDOR_URL ?>metisMenu/dist/metisMenu.min.js"></script> 
<script src="<?= VENDOR_URL ?>iCheck/icheck.min.js"></script> 
<?php /*?><script src="<?= VENDOR_URL ?>peity/jquery.peity.min.js"></script> <?php */?>
<script src="<?= VENDOR_URL ?>sweetalert/lib/sweet-alert.min.js"></script>
<!-- DataTables -->
<script src="<?= VENDOR_URL ?>datatables/media/js/jquery.dataTables.min.js"></script>
<!-- DataTables buttons scripts -->
<script src="<?= VENDOR_URL ?>pdfmake/build/pdfmake.min.js"></script>
<script src="<?= VENDOR_URL ?>pdfmake/build/vfs_fonts.js"></script>
<!-- App scripts --> 
<script src="<?= VENDOR_URL ?>homer.js"></script> 
<!-- Gumption scripts --> 
<script type="text/javascript" src="<?= VENDOR_URL ?>header.js"></script> 
<script type="text/javascript" src="<?= VENDOR_URL ?>common.js"></script> 
<script src="<?= VENDOR_URL ?>jquery-validation/jquery.validate.min.js"></script>
<script type="text/javascript" src="<?= VENDOR_URL ?>customjscss/jquery.numslider.js"></script>
<script type="text/javascript" src="<?= VENDOR_URL ?>jquery.tablesorter.js"></script>
<script src="<?= VENDOR_URL ?>bootstrap/dist/js/bootstrap.min.js"></script>
<script src="<?= VENDOR_URL ?>moment/moment.js"></script>
<script src="<?= VENDOR_URL ?>bootstrap-datepicker.js"></script>
<script src="<?= VENDOR_URL ?>bootstrap-clockpicker.min.js"></script>
<script src="<?= VENDOR_URL ?>bootstrap-datetimepicker.js"></script>
<script src="<?= VENDOR_URL ?>daterangepicker.min.js"></script>
<script src="<?= VENDOR_URL ?>ckeditor/ckeditor.js"></script>

<script type="text/javascript" src="<?= VENDOR_URL ?>dropzone.js"></script> 
<script src="<?= VENDOR_URL ?>jquery.form.js"></script>
<script src="<?= VENDOR_URL ?>jquery.uploadfile.min.js"></script>
<script src="<?= VENDOR_URL ?>jquery-loading-overlay/2.1.7/loadingoverlay.min.js"></script>

<!-- isInViewport jQuery plugin @ https://cdnjs.cloudflare.com/ajax/libs/is-in-viewport/3.0.4/isInViewport.js -->
<script type="text/javascript" src="<?= VENDOR_URL ?>is-in-viewport/3.0.4/isInViewport.min.js"></script> 

<!-- easyrogs --> 
<script src="<?= ROOTURL ?>system/application/custom.js"></script>

<script type="text/javascript">
function loadToolTipForClientBtn(c_id='')
{
	if(c_id == "") 
	{
		var c_id	=	$("#responding").val();
	}
	$.post( "loadclientnameemail.php", { c_id: c_id}).done(function( data ) 
	{
		if(data == "")
		{
			data = "Send to client.";
		}
		$(".client-btn").attr("data-original-title",data)
	});
}
	$(document).ready(function(){
		$('video').each(function () {
			if ($(this).is(":in-viewport")) {
				$(this)[0].play();
			} else {
				$(this)[0].pause();
			}
		});
	});
</script>
