<?php
require_once(SYSTEMPATH.'body.php');
?>

<div id="ctxhelp-video-modal" class="modal fade" role="dialog" style="min-width: 95vw; min-height: 95vh;">
  <div class="modal-dialog" style="width: 75%; margin:2rem auto; padding:0;">
    <div class="modal-content">
      <div class="modal-header" style="padding: 15px;">
        <h5 class="modal-title" style="text-align:center; font-size: 22px;">Get Started Using AI4Discovery</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Cancel" style="margin-top: -40px !important;font-size: 25px !important;">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="video-container">
          <video id="ctxhelp-video" preload="none" controls style=" position: relative; max-width: 99%; width: 100% !important; height: auto !important;">
              <source src="" type="video/mp4">
          </video>
        </div>
      </div>
      <div class="modal-footer">
        <a href="javascript:;" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-close"></i> Close </a>
      </div>
    </div>
  </div>
</div><!-- ctxhelp-video-modal -->

<script type="text/javascript">
    function showCtxHelp() {
        const { currentPage, AppContexts: knownContexts, } = globalThis;
        if( !currentPage ) {
          console.assert( currentPage, "[!] NOT FOUND: window.currentPage");
          debugger; return;
        }
        const idx = knownContexts.findIndex( item => item && item.id == currentPage.id );
        if( idx < 0 ) {
          console.assert( idx >= 0, "[!] NOT FOUND:", {currentPage})
          debugger;
        } else {
          const $title = $("#ctxhelp-video-modal .modal-title"),
                $video = $("#ctxhelp-video"),
                videoTitle = knownContexts[idx].title;
          $title.text( videoTitle );
          $video[0].src = "<?= ROOTURL ?>system/application/" + knownContexts[idx].video;
          $video
            .data( 'title', videoTitle )
            .data( 'src',   knownContexts[idx].video )

          console.table( {help: knownContexts[idx], currentPage, } );
          $('#ctxhelp-video-modal')
            .on('show.bs.modal', _ => {
                $('#ctxhelp-video')[0].play();
            } )
            .on('hidden.bs.modal', _ => {
                $('#ctxhelp-video')[0].pause();
            } )
            .modal('show');
        }
	}

  //$(document).on( "keypress", ev => { ev && ev.which == 112/*F1*/ && showCtxHelp() } );
</script>
