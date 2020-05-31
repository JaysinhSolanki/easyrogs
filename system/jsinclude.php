<!-- Vendor scripts --> 
<script src="<?= VENDOR_URL ?>jquery-ui/jquery-ui.min.js"></script> 
<script src="<?= VENDOR_URL ?>toastr/build/toastr.min.js"></script> 
<script src="<?= VENDOR_URL ?>metisMenu/dist/metisMenu.min.js"></script> 
<script src="<?= VENDOR_URL ?>iCheck/icheck.min.js"></script> 
<?php /*?><script src="<?= VENDOR_URL ?>peity/jquery.peity.min.js"></script> <?php */?>
<script src="<?= VENDOR_URL ?>sweetalert/lib/sweet-alert.min.js"></script>
<!-- DataTables -->
<script src="<?= VENDOR_URL ?>datatables/media/js/jquery.dataTables.min.js"></script>
<!-- DataTables buttons scripts -->
<script src="<?= VENDOR_URL ?>pdfmake/build/pdfmake.min.js"></script>
<script src="<?= VENDOR_URL ?>pdfmake/build/vfs_fonts.js"></script>

<!-- easyrogs --> 
<script src="<?= VENDOR_URL ?>homer.js"></script> 
<script src="<?= ROOTURL ?>system/application/custom.js"></script>
<!-- Gumption scripts --> 
<script type="text/javascript" src="<?= VENDOR_URL ?>header.js"></script> 
<script type="text/javascript" src="<?= VENDOR_URL ?>common.js"></script> 

<script src="<?= VENDOR_URL ?>jquery-validation/jquery.validate.min.js"></script>
<script type="text/javascript" src="<?= VENDOR_URL ?>customjscss/jquery.numslider.js"></script>
<script type="text/javascript" src="<?= VENDOR_URL ?>jquery.tablesorter.js"></script>
<script src="<?= VENDOR_URL ?>moment/moment.js"></script>
<script src="<?= VENDOR_URL ?>bootstrap/dist/js/bootstrap.min.js"></script>
<script src="<?= VENDOR_URL ?>bootstrap-datepicker.js"></script>
<script src="<?= VENDOR_URL ?>bootstrap-clockpicker.min.js"></script>
<script src="<?= VENDOR_URL ?>bootstrap-datetimepicker.js"></script>
<script src="<?= VENDOR_URL ?>daterangepicker.min.js"></script>
<script src="<?= VENDOR_URL ?>ckeditor/ckeditor.js"></script>

<script type="text/javascript" src="<?= VENDOR_URL ?>dropzone.js"></script> 
<script src="<?= VENDOR_URL ?>jquery.form.js"></script>
<script src="<?= VENDOR_URL ?>jquery.uploadfile.min.js"></script>
<script src="<?= VENDOR_URL ?>jquery-loading-overlay/2.1.7/loadingoverlay.min.js"></script>

<!-- isInViewport jQuery plugin @ https://cdnjs.cloudflare.com/ajax/libs/is-in-viewport/3.0.4/isInViewport.js -->
<script type="text/javascript" src="<?= VENDOR_URL ?>is-in-viewport/3.0.4/isInViewport.min.js"></script> 

<script type="text/javascript">
function loadToolTipForClientBtn( c_id='' ) {
	if( c_id == '' ) {
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

var previous = '';
function ctxUpdate( aPage, fn ) { 
	const { id, pkscreenid, } = globalThis['currentPage'] || {},
		  page = Object.assign( {}, { id, pkscreenid, }, aPage, {previous} );

	globalThis['currentPage'] = page;
	previous = page.pkscreenid;
//!! this is a temporary copy, to detect untracked pages
const videos = [ 
            {id: "-2",               }, 
            {id: "-1",               }, 
            {id: "7",                }, 
            {id: "8",                }, 
            {id: "44",               }, 
            {id: "45",               }, 
            {id: "46",               }, 
            {id: "47",               }, 
            {id: "47_1",             }, 
            {id: "47_1@FROGS",       }, 
            {id: "47_1@FROGSE",      }, 
            {id: "47_1@SROGS",       }, 
            {id: "47_1@RFAs",        }, 
            {id: "47_1@RPDs",        }, 
            {id: "47_2",             }, 
            {id: "47_2@FROGS",       }, 
            {id: "47_2@FROGSE",      }, 
            {id: "47_2@SROGS",       }, 
            {id: "47_2@RFAs",        }, 
            {id: "47_2@RPDs",        }, 
            {id: "49",               }, 
];
const idx = videos.findIndex(item => item && item.id == currentPage.id);
if (idx < 0) { console.log("[!] TAB NOT FOUND:", currentPage ); debugger; }
//!!
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
    _doAutoplayVideos();
	const { watchdog, } = ( typeof options === "object" ) && options || { watchdog: String(options).toLowerCase() };
	switch( watchdog ) {
		case "yes":
			if( !globalThis.timerAutoPlayVideos ) {
				globalThis.timerAutoPlayVideos = setInterval( _doAutoplayVideos, 500 ) ;
			}
			break;	
		case "no":
			globalThis.timerAutoPlayVideos && clearInterval( globalThis.timerAutoPlayVideos );
			break;
		default:
			console.assert( !watchdog, {options} );
	}
}
jQuery( $ => {
	autoPlayOrPauseVideos();
});
</script>
