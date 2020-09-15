$(document).ready(() => {

	$('form#signup-form').on('submit', function(e) {
		// dont do anything stupid
		e.preventDefault();
		e.stopPropagation();

		if ( $(this)[0].checkValidity() ) { // client-side validation
			const formData = $(this).serializeJSON();

			// start the signup process
			$.post('post-signup-start.php', formData, () => {
				$(this).addClass('step2')
				trackEvent('signup', { event_category: 'account', event_label: formData.email || '', });
			}).fail( (error)=> {
				showResponseNotification(error);
			})
		}

		$(this).addClass('was-validated');
	});

	$('#attorney-switch').on('change', function(e) {
		if ($(this).is(':checked')) {
			$('input#barnumber').attr('required', true);
			$('input#barnumber').focus();
		}
		else {
			$('input#barnumber').attr('required', false);
		}
	});

});

// log context
$( _ => {	ctxUpdate({ id: -2, pkscreenid: -2, url: 'signup.php', }); } );