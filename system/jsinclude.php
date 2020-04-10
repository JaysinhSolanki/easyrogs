<!-- Vendor scripts --> 
<script src="<?php echo VENDOR_URL;?>jquery-ui/jquery-ui.min.js"></script> 
<script src="<?php echo VENDOR_URL;?>toastr/build/toastr.min.js"></script> 
<script src="<?php echo VENDOR_URL;?>metisMenu/dist/metisMenu.min.js"></script> 
<script src="<?php echo VENDOR_URL;?>iCheck/icheck.min.js"></script> 
<?php /*?><script src="<?php echo VENDOR_URL;?>peity/jquery.peity.min.js"></script> <?php */?>
<script src="<?php echo VENDOR_URL;?>sweetalert/lib/sweet-alert.min.js"></script>
<!-- DataTables -->
<script src="<?php echo VENDOR_URL;?>datatables/media/js/jquery.dataTables.min.js"></script>
<!-- DataTables buttons scripts -->
<script src="<?php echo VENDOR_URL;?>pdfmake/build/pdfmake.min.js"></script>
<script src="<?php echo VENDOR_URL;?>pdfmake/build/vfs_fonts.js"></script>
<!-- App scripts --> 
<script src="<?php echo VENDOR_URL;?>homer.js"></script> 
<!-- Gumption scripts --> 
<script type="text/javascript" src="<?php echo VENDOR_URL;?>header.js"></script> 
<script type="text/javascript" src="<?php echo VENDOR_URL;?>common.js"></script> 
<script src="<?php echo VENDOR_URL;?>jquery-validation/jquery.validate.min.js"></script>
<script type="text/javascript" src="<?php echo VENDOR_URL;?>customjscss/jquery.numslider.js"></script>
<script type="text/javascript" src="<?php echo VENDOR_URL;?>jquery.tablesorter.js"></script>
<script src="<?php echo VENDOR_URL;?>bootstrap/dist/js/bootstrap.min.js"></script>
<script src="<?php echo VENDOR_URL;?>moment/moment.js"></script>
<script src="<?php echo VENDOR_URL;?>bootstrap-datepicker.js"></script>
<script src="<?php echo VENDOR_URL;?>bootstrap-clockpicker.min.js"></script>
<script src="<?php echo VENDOR_URL;?>bootstrap-datetimepicker.js"></script>
<script src="<?php echo VENDOR_URL;?>daterangepicker.min.js"></script>
<!--<script type="text/javascript" src="<?php echo VENDOR_URL;?>ckeditor/ckeditor.js"></script>-->
<script src="//cdn.ckeditor.com/4.12.1/basic/ckeditor.js"></script>

<script type="text/javascript" src="<?php echo VENDOR_URL;?>dropzone.js"></script> 
<script src="<?php echo VENDOR_URL;?>jquery.form.js"></script>
<script src="<?php echo VENDOR_URL;?>jquery.uploadfile.min.js"></script>
<script src="//cdn.jsdelivr.net/npm/gasparesganga-jquery-loading-overlay@2.1.6/dist/loadingoverlay.min.js"></script>

<!-- easyrogs --> 
<script src="/system/application/custom.js"></script>

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
</script>
