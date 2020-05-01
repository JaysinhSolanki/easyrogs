<?php  session_start();
	require_once("{$_SESSION['system_path']}application/faqs.php");
?>
<script type="text/javascript">
	// TODO Move to custom.js if better..

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

	$(document).ready( _ => { 
		setInterval( autoPlayOrPauseVideos, 500 );
	} );
</script>