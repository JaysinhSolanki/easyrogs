<?php
require_once(SYSTEMPATH.'body.php');
?>

<style>
.register-container {
	max-width:100% !important;	
}
p {
    line-height: 20px;
    text-align: justify;
}
.faq-question {
	font-size:16px !important;
}
#faq_modal {
	z-index: 9999; position: absolute;
}
#load_faq_modal_content{
	overflow: hidden;
}
</style>

<div id="faq-modal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header" style="padding:10px">
                <h5 class="modal-title text-center">AI4Discovery FAQs
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true"
                                                                                                      style="font-size: 36px;">&times;</span>
                    </button>
                </h5>
            </div>
            <div class="modal-body" id="load_faq_modal_content"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div><!-- faq-modal -->
<script type="text/javascript">
    function showFAQ() {
      $.post( "<?= ROOTURL ?>system/application/faqs.php", {} )
          .done( data => {
              autoPlayOrPauseVideos( {watchdog:"yes"});

              $("#load_faq_modal_content").html(data);
              $('#faq-modal').modal('toggle');
          } ); 
	}
</script>
