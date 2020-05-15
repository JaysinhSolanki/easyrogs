<?php
@session_start();
?>
 <!-- Footer-->

</div>

<div class="col-md-12" style="margin-top:20px !important">
<div class="footer text-center" style="background-color:#f7f9fa"> <span> All rights reserved &copy; <?php echo date('Y');?> EasyRogs. U.S. Patent Pending</span>  </div>
</div>


<?php
require_once("{$_SESSION['system_path']}jsinclude.php");
require_once("{$_SESSION['framework_path']}faq_modal.php");
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
  window.setInterval( _ => checksession(), 10000 );
</script>
</body>
</html>