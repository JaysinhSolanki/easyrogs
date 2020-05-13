<?php
@session_start();
require_once("{$_SESSION['system_path']}jsinclude.php");
?>
<div class="modal fade" id="faq-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header" style="padding:10px">
        <h5 class="modal-title text-center">EasyRogs FAQs
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true" style="font-size: 36px;">&times;</span>
        </button>
        </h5>
      </div>
      <div class="modal-body" id="load_faq_modal_content">
        
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
<script type="text/javascript">
	function autoPlayOrPauseVideos() {
		$('video').each( function() {
			const $this = $(this);
			if( $this.is(":in-viewport") ) {
				if( $this.is(":not(.autoplayed") ) $this.removeClass('autopaused').addClass('autoplayed')[0].play();
			} else {
				if( $this.is(":not(.autopaused)") ) $this.removeClass('autoplayed').addClass('autopaused')[0].pause();
			}
		} );
	}

    function showFAQ() {
	    $.post( "<?= ROOTURL ?>system/application/faqs.php",{} ).done( data => {
            setInterval( autoPlayOrPauseVideos, 500 );
            debugger;
	        $("#load_faq_modal_content").html(data);
	        $('#faq-modal').modal('toggle');
	    }); 
	}
</script>
