<?php
@session_start();
require_once("{$_SESSION['system_path']}jsinclude.php");
require_once(SYSTEMPATH.'body.php');
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
        const currentPage = globalThis['currentPage'];
        if( !currentPage ) { 
          console.assert( currentPage, "[!] NOT FOUND: window.currentPage");
          debugger; return; 
        } 
        const videos = [ 
//          {id: "-3",              video: "forgot-password.mp4",       title: "Forgot Password",                                    }, 
            {id: "-2",              video: "join.mp4",                  title: "Join",                                               }, 
            {id: "-1",              video: "login.mp4",                 title: "Login",                                              }, // dashboard
            {id: "8",               video: "user_profile.mp4",          title: "My Profile",                                         }, // myprofile
            {id: "44",              video: "my_cases.mp4",              title: "My Case List",                                       }, // cases
            {id: "45",              video: ".mp4",                      title: "Discovery in Case",                                  }, // discoveries
            {id: "46",              video: ".mp4",                      title: "Case",                                               }, // case
            {id: "47",              video: "create_discovery.mp4",      title: "Creating Discovery",                                 }, // discovery-propound
            {id: "47_1",            video: "create_discovery.mp4",      title: "Creating Discovery",                                 }, 
            {id: "47_1@FROGS",      video: ".mp4",                      title: "Form Interrogatories - General",                     }, 
            {id: "47_1@FROGSE",     video: ".mp4",                      title: "Form Interrogatories - Employment",                  }, 
            {id: "47_1@SROGS",      video: ".mp4",                      title: "Special Interrogatories",                            }, 
            {id: "47_1@RFAs",       video: ".mp4",                      title: "Requests for Admission",                             }, 
            {id: "47_1@RPDs",       video: ".mp4",                      title: "Requests for Production of Documents",               }, 
            {id: "47_2",            video: ".mp4",                      title: "Responding to Discovery",                            }, // discovery-respond
            {id: "47_2@FROGS",      video: ".mp4",                      title: "Responding to Form Interrogatories - General",       }, 
            {id: "47_2@FROGSE",     video: ".mp4",                      title: "Responding to Form Interrogatories - Employment",    }, 
            {id: "47_2@SROGS",      video: ".mp4",                      title: "Responding to Special Interrogatories",              }, 
            {id: "47_2@RFAs",       video: ".mp4",                      title: "Responding to Requests for Admission",               }, 
            {id: "47_2@RPDs",       video: ".mp4",                      title: "Responding to Requests for Production of Documents", }, 
            {id: "49",              video: ".mp4",                      title: "PDF Viewer",                                         }, // pdfviewer
        ];
        const idx = videos.findIndex( item => item && item.id == currentPage.id );
        if( idx < 0 ) {
          console.assert( idx >= 0, "[!] NOT FOUND:", {currentPage})
          debugger;
        } else {
          const $video = $("#ctxhelp-video"),
                $title = $("#ctxhelp-video-modal .modal-title");
          $video[0].src = "<?= ROOTURL ?>system/application/" + videos[idx].video;
          $title.text( videos[idx].title );
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
