<?php
@session_start();
require_once("{$_SESSION['system_path']}jsinclude.php");
?>

<div id="ctxhelp-video-modal" class="modal fade" role="dialog" style="min-width: 95vw; min-height: 95vh;">
  <div class="modal-dialog" style="width: 75%; margin:2rem auto; padding:0;">
    <div class="modal-content">
      <div class="modal-header" style="padding: 15px;">
        <h5 class="modal-title" style="text-align:center; font-size: 22px;">Get Started Using EasyRogs</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Cancel" style="margin-top: -40px !important;font-size: 25px !important;">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="video-container">
          <video id="ctxhelp-video" preload="none" controls style="max-width:100%;max-height:100%;">
              <source src="" type="video/mp4">
          </video>
        </div>
      </div>
      <div class="modal-footer">
        <a href="javascript:;" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-close"></i> Close </a>
      </div>
    </div>
  </div>
</div>

<script type="text/javascript">
    function showCtxHelp() { 
        const currentPage = globalThis['currentPage'];
        if( !currentPage ) { debugger; return; }
        const videos = [
            {id: "7",  video: "ctxhelp-dashboard.mp4", },
            {id: "8",  video: "ctxhelp-myprofile.mp4", },
            {id: "44", video: "ctxhelp-cases.mp4", },
            {id: "45", video: "ctxhelp-discoveries.mp4", },
            {id: "46", video: "ctxhelp-case.mp4", },
            {id: "47", video: "ctxhelp-discovery.mp4", },
            {id: "49", video: "ctxhelp-pdfviewer.mp4", },
        ];
        const idx = videos.findIndex( item => item && item.id == currentPage['pkscreenid'] );
        console.assert( idx >= 0, "[!] NOT FOUND:", {currentPage})
        if( idx < 0 ) debugger;
        $("#ctxhelp-video").html(`<source src='<?= ROOTURL ?>system/application/${ videos[idx].video }' type='video/mp4' />`);
        $('#ctxhelp-video-modal').modal('toggle');
        $('#ctxhelp-video-modal').on('hidden.bs.modal', _ => {
            $('video#ctxhelp-video')[0].pause();
        } );
	}

  //$(document).on( "keypress", ev => { ev && ev.which == 112/*F1*/ && showCtxHelp() } );
</script>
