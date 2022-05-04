<?php
@session_start();
?>
<!-- Footer-->
</div>

<div class="col-md-12" style="margin-top:20px !important">
	<div class="footer text-center" style="background-color:#f7f9fa"> <span> All rights reserved &copy; <?php echo date('Y');?> 
AI4Discovery. U.S. Patent Pending</span>  </div>
</div>


<?php
require_once( SYSTEMPATH.'jsinclude.php');
require_once( SYSTEMPATH.'application/ctxhelp_modal.php');
require_once( SYSTEMPATH.'application/kb_modal.php');
require_once( FRAMEWORK_PATH.'faq_modal.php');
?>
<style>
	body.modal-open {
	    position: static !important;
	}
</style>
<script>
function checksession() {
	$.post( "<?php echo $_SESSION['framework_url'] ?>checksession.php", data => {
		if(data == 'loggedout') {
			setTimeout(function(){ window.location.href = "<?php echo $_SESSION['framework_url'] ?>signout.php";}, 1000);
		}
	} );
}
window.setInterval( _ => checksession(), <?= $_ENV['APP_ENV'] == 'local' ? 3600000 : 10000 ?> );
</script>

<?= SNIPPET_SMARTSUPP ?>
<?= SNIPPET_ANALYTICS ?>

</body>
</html>
