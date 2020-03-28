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
?>
<div class="modal fade" id="faqModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header" style="padding:10px">
        <!--<h5 class="modal-title" id="exampleModalLabel">FAQ's</h5>-->
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true" style="font-size: 36px;">&times;</span>
        </button>
      </div>
      <div class="modal-body" id="load_faq_modal_content">
        
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
<style>
	body.modal-open {
	    position: static !important;
	}
</style>
<script>
	function PopupfaqModal()
	{
	    $.post( "loadfaqpopupcontent.php",{}).done(function( data ) 
	    {
	        $("#load_faq_modal_content").html(data);
	        $('#faqModal').modal('toggle');
	    }); 
	}
  function checksession()
   {
	  $.post( "<?php echo $_SESSION['framework_url'] ?>checksession.php",function( data )
	  {
		  //alert(data);
		   if(data == 'loggedout')
		   {
			 setTimeout(function(){ window.location.href = "<?php echo $_SESSION['framework_url'] ?>signout.php";}, 1000);
		   }
	   
	  });   
  }
  window.setInterval(function()
   {
   checksession(); 
 }, 10000);
</script>
</body>
</html>