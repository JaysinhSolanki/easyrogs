<!-- Vendor scripts --> 
<script src="<?= VENDOR_URL ?>jquery/dist/jquery.min.js"></script>
<script src="<?= VENDOR_URL ?>jquery-ui/jquery-ui.min.js"></script> 
<script src="<?= VENDOR_URL ?>toastr/build/toastr.min.js"></script> 
<script src="<?= VENDOR_URL ?>sweetalert/lib/sweet-alert.min.js"></script>
<script src="<?= VENDOR_URL ?>metisMenu/dist/metisMenu.min.js"></script> 
<script src="<?= VENDOR_URL ?>iCheck/icheck.min.js"></script> 

<script src="<?= VENDOR_URL ?>jquery-validation/jquery.validate.min.js"></script>
<script src="<?= VENDOR_URL ?>customjscss/jquery.numslider.js"></script>
<script src="<?= VENDOR_URL ?>jquery.tablesorter.js"></script>
<script src="<?= VENDOR_URL ?>moment/moment.js"></script>
<script src="<?= VENDOR_URL ?>daterangepicker.min.js"></script>
<script src="<?= VENDOR_URL ?>bootstrap/dist/js/bootstrap.min.js"></script>
<script src="<?= VENDOR_URL ?>bootstrap-datepicker.js"></script>
<script src="<?= VENDOR_URL ?>bootstrap-clockpicker.min.js"></script>
<script src="<?= VENDOR_URL ?>bootstrap-datetimepicker.js"></script>

<!-- isInViewport jQuery plugin @ https://cdnjs.cloudflare.com/ajax/libs/is-in-viewport/3.0.4/isInViewport.js -->
<script src="<?= VENDOR_URL ?>is-in-viewport/3.0.4/isInViewport.min.js"></script> 

<script src="<?= VENDOR_URL ?>jquery.initialize/jquery.initialize.min.js"></script>

<script src="<?= VENDOR_URL ?>jquery.form.js"></script>
<script src="<?= VENDOR_URL ?>jquery.uploadfile.min.js"></script>
<script src="<?= VENDOR_URL ?>jquery-loading-overlay/2.1.7/loadingoverlay.min.js"></script>
<script src="<?= VENDOR_URL ?>select2.full.js"></script>

<!-- DataTables -->
<script src="<?= VENDOR_URL ?>datatables/media/js/jquery.dataTables.min.js"></script>
<!-- DataTables buttons scripts -->
<script src="<?= VENDOR_URL ?>pdfmake/build/pdfmake.min.js"></script>
<script src="<?= VENDOR_URL ?>pdfmake/build/vfs_fonts.js"></script>

<script src="<?= VENDOR_URL ?>dropzone.js"></script>
<script src="<?= VENDOR_URL ?>ckeditor/ckeditor.js"></script>

<!-- easyrogs --> 
<!--script src="<?= VENDOR_URL ?>homer.js"></script--> 
<script src="<?= VENDOR_URL ?>header.js"></script> 
<script src="<?= VENDOR_URL ?>common.js"></script> 
<script src="<?= ROOTURL ?>system/application/custom.js"></script>

<script type="text/javascript">
function loadToolTipForClientBtn( c_id='' ) {
	if( !c_id ) {
		var c_id = $("#responding").val();
	}
	$.post( "loadclientnameemail.php", { c_id } )
		.done( data => {
			if( data ) {
				data = JSON.parse(data);
				globalThis['respondent'] = {id: c_id, ...data }; // make globally available
				data = `<span style='text-align:center'>${data.name + (data.email ? "<br/>" + data.email : '')}</span>`;
			}
			else {
				data = "Send to client.";
			}
			$(".client-btn").attr( "data-original-title",data );
		});
}

function trackEvent( action, extra = {} ) {
	if( typeof gtag == 'function' && globalThis.dataLayer ) {
		if( action != 'goto' ) {
			gtag('event', action, extra );
		} else {
			const { page_path, page_title, } = extra;
			gtag( 'config', '<?= APP_GOOGLE_ANALYTICS_ID ?>', { page_path, page_title, } );
		}
	} 
	console.log( {action, ...extra,} );
}
function trackVideos() {
	const videoTracker = jQuery.initialize( 'video', function() { 
		function _logVideo(action, element) { 
			$this = $(element);
			trackEvent( action, { event_category: 'video', event_label: $this.data('src') || $this.src, } ); 
		}
		$(this)
			//.on( 'error',		ev => { _logVideo( 'error', ev.target ) } )
			.on( 'play',		ev => { _logVideo( 'play', ev.target ) } )
			.on( 'pause ended', ev => { _logVideo( 'stop', ev.target ) } )
	} );
	//videoTracker.disconnect();
}
jQuery( $ => {
	trackVideos();
} );

const knownContexts = [
//		{id: "-3",              title: "Forgot Password",                                    video: "forgot-password.mp4",       },
		{id: "-2",              title: "Join",                                               video: "join.mp4",                  },
		{id: "-1",              title: "Login",                                              video: "login.mp4",                 },
		{id: "7",               title: "",                                                   video: ".mp4",                      }, // Dashboard, shouldn't be found in the wild
		{id: "8",               title: "My Profile",                                         video: "user_profile.mp4",          },
		{id: "44",              title: "My Case List",                                       video: "my_cases.mp4",              },
		{id: "45",              title: "Discovery in Case",                                  video: "case_discoveries.mp4",      },
		{id: "46",              title: "Case",                                               video: "case.mp4",                  },
		{id: "47",              title: "Creating Discovery",                                 video: "create_discovery.mp4",      }, // discovery-propound
		{id: "47_1",            title: "Creating Discovery",                                 video: "create_discovery.mp4",      },
		{id: "47_1@FROGS",      title: "Form Interrogatories - General",                     video: "creating_FROGS.mp4",        },
		{id: "47_1@FROGSE",     title: "Form Interrogatories - Employment",                  video: "creating_FROGSE.mp4",       },
		{id: "47_1@SROGS",      title: "Special Interrogatories",                            video: "creating_SROGS.mp4",        },
		{id: "47_1@RFAs",       title: "Requests for Admission",                             video: "creating_RFAS.mp4",         },
		{id: "47_1@RPDs",       title: "Requests for Production of Documents",               video: "creating_RPDS.mp4",         },
		{id: "47_2",            title: "Responding to Discovery",                            video: "respond_discovery.mp4",     }, // discovery-respond
		{id: "47_2@FROGS",      title: "Responding to Form Interrogatories - General",       video: "responding_FROGS.mp4",      },
		{id: "47_2@FROGSE",     title: "Responding to Form Interrogatories - Employment",    video: "responding_FROGSE.mp4",     },
		{id: "47_2@SROGS",      title: "Responding to Special Interrogatories",              video: "responding_SROGS.mp4",      },
		{id: "47_2@RFAs",       title: "Responding to Requests for Admission",               video: "responding_RFAS.mp4",       },
		{id: "47_2@RPDs",       title: "Responding to Requests for Production of Documents", video: "responding_RPDS.mp4",       },
		{id: "49_1@FROGS",      title: "Form Interrogatories - General",                     video: "creating_FROGS.mp4",        },
		{id: "49_1@FROGSE",     title: "Form Interrogatories - Employment",                  video: "creating_FROGSE.mp4",       },
		{id: "49_1@SROGS",      title: "Special Interrogatories",                            video: "creating_SROGS.mp4",        },
		{id: "49_1@RFAs",       title: "Requests for Admission",                             video: "creating_RFAS.mp4",         },
		{id: "49_1@RPDs",       title: "Requests for Production of Documents",               video: "creating_RPDS.mp4",         },
		{id: "49_2@FROGS",      title: "Responding to Form Interrogatories - General",       video: "responding_FROGS.mp4",      },
		{id: "49_2@FROGSE",     title: "Responding to Form Interrogatories - Employment",    video: "responding_FROGSE.mp4",     },
		{id: "49_2@SROGS",      title: "Responding to Special Interrogatories",              video: "responding_SROGS.mp4",      },
		{id: "49_2@RFAs",       title: "Responding to Requests for Admission",               video: "responding_RFAS.mp4",       },
		{id: "49_2@RPDs",       title: "Responding to Requests for Production of Documents", video: "responding_RPDS.mp4",       },
		{id: "49",              title: "PDF Viewer",                                         video: "pdf_view.mp4",              }, // pdfviewer
	];

var previous = '';

globalThis['AppContexts'] = knownContexts;
function ctxUpdate( aPage, fn ) {
	addTooltips(); // activate any new tooltips loaded in the new context

	const { id, pkscreenid, } = globalThis['currentPage'] || {},
		  page = Object.assign( {}, { id, pkscreenid, }, aPage, {previous} );

	globalThis['currentPage'] = page;
	previous = page.pkscreenid;

	const idx = knownContexts.findIndex(item => item && item.id == currentPage.id);
	if (idx < 0) { console.log("[!] TAB NOT FOUND:", currentPage ); debugger; exit; }
	trackEvent( 'goto', { page_path:	`${page.url}#${page.id}`,
						  page_title:	knownContexts[idx].title,
						  event_category:	'navigation', 
						  event_label: 		knownContexts[idx].title, 
						} );
}

function selecttab(id,url,pkscreenid) {
	$('#'+id).className="active";
	if( id != previous && previous ) {
		$('#'+previous).className="inactive";
	}
	loadsection('wrapper',url,pkscreenid);

	id = pkscreenid || 7;
	const params = new URLSearchParams(url),
		  type = params.get('type');
	if( type ) id += '_' + type;
	ctxUpdate( { id, previous, pkscreenid, url, } );
}

function _doAutoplayVideos() {
	$('video').each( function() {
		const $this = $(this);
		if( $this.is(":in-viewport") ) {
			if( $this.is(":not(.autoplayed") ) {
				$this.removeClass('autopaused').addClass('autoplayed')[0].play();
			}
		} else {
			if( $this.is(":not(.autopaused)") ) {
				$this.removeClass('autoplayed').addClass('autopaused')[0].pause();
			}
		}
	} );
}
function autoPlayOrPauseVideos( options = { watchdog: null } ) {
	// Autoplay visible videos, pause not visible ones,
	//      use {watchdog:"yes"} to install a background task that enforces this
	//      use {watchdog:"no"/"remove"} to remove it
    _doAutoplayVideos();
	const { watchdog, } = ( typeof options === "object" ) && options || { watchdog: String(options).toLowerCase() };
	switch( watchdog ) {
		case true:
		case "yes":
			if( !globalThis.timerAutoPlayVideos ) {
				globalThis.timerAutoPlayVideos = setInterval( _doAutoplayVideos, 500 ) ;
			}
			break;
		case "remove":
		case "no":
			globalThis.timerAutoPlayVideos && clearInterval( globalThis.timerAutoPlayVideos );
			break;
		default:
			console.assert( !watchdog, {options} );
	}
}

function addTooltips() { //console.log( $('.tooltipshow').length, "tooltips enabled.." );
	$('.tooltipshow').tooltip( {
		container: 'body',
		html: true,
	} );
}
jQuery( $ => {
	CKEDITOR.addCss(`h4, h5 { font-weight: 600; font-size: 14px; }
					.text-center { text-align: center; }
					`);
	CKEDITOR.config.allowedContent = 'u b i strong em span div ol ul li  table tr th td h3 h5(*){*}[*]';
	// CKEDITOR.on( 'instanceReady', _ => { // check the filter is working properly
	// 	console.table( _.editor.filter.allowedContent.map(_=>_.elements) );
	// } );
});
jQuery( $ => {
	autoPlayOrPauseVideos();
	addTooltips();
});
</script>

<?php if( !@$_ENV['PAY_DISABLED'] ) { ?>
	<script src="https://js.stripe.com/v3/"></script>
<?php } ?>
