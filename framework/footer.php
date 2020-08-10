<?php
@session_start();
?>
<!-- Footer-->
</div>

<div class="col-md-12" style="margin-top:20px !important">
	<div class="footer text-center" style="background-color:#f7f9fa"> <span> All rights reserved &copy; <?php echo date('Y');?> EasyRogs. U.S. Patent Pending</span>  </div>
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

<?php
	if( !@$_ENV['SMARTSUPP_DISABLED'] ) {
?>
<!-- Smartsupp Live Chat script -->
<script type="text/javascript">
	var _smartsupp = _smartsupp || {};
	_smartsupp.key = 'ae242385584ca4d3fd78d74a04dbd806ef3957e0';
	window.smartsupp||(function(d) {
		var s,c,o=smartsupp=function(){ o._.push(arguments)};o._=[];
		s=d.getElementsByTagName('script')[0];c=d.createElement('script');
		c.type='text/javascript';c.charset='utf-8';c.async=true;
		c.src='https://www.smartsuppchat.com/loader.js?';s.parentNode.insertBefore(c,s);
	})(document);
</script>
<?php
	}
	if( !@$_ENV['ANALYTICS_DISABLED'] ) {
?>
<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=<?= APP_GOOGLE_ANALYTICS_ID ?>"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', '<?= APP_GOOGLE_ANALYTICS_ID ?>', { 'transport_type': 'beacon'});
</script>
<?php
	}
?>

</body>
</html>
