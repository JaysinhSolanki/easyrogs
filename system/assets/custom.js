// not const, need these in the global scope (window object)
globalThis.API_BASE = '/system/application';
globalThis.FORMAT_JSON = 'json';
globalThis.FORMAT_HTML = 'html';

getPermissions = (success, error) => {
	$.get(API_BASE + '/get-permissions.php', success, 'json').fail(error);
}

getCaseClients = (caseId, success, error, format = FORMAT_JSON) => {
	$.get(API_BASE + '/get-case-clients.php', {case_id: caseId, format: format}, success, 'json')
	 .fail(error);
}

getCaseByNumber = (number, success, error) => {
	$.get(API_BASE + '/get-case-by-number.php', {number: number}, success, 'json')
	 .fail(error);
}

getTeam = (success, error, format = FORMAT_HTML) => {
	$.get(API_BASE + '/get-team.php', {format: format}, success)
	 .fail(error);
}

deleteTeamMember = (memberId, success, error) => {
	$.post(API_BASE + '/delete-team-member.php', {memberId: memberId}, success)
	 .fail(error)
}

deleteServiceListUser = (userId, caseId, success, error) => {
	$.post(API_BASE + '/delete-service-list-user.php', {user_id: userId, case_id: caseId}, success)
	 .fail(error)
}

addTeamMember = (memberId, name, email, success, error) => {
	$.post(API_BASE + '/post-team-member.php',
		{memberId: memberId, name: name, email: email},
		success, FORMAT_JSON
	).fail(error)
}

getTeamAttorneys = (caseId, success, error) => {
	$.get(API_BASE + '/get-team-attorneys.php', {case_id: caseId}, success)
	  .fail(error);
}

getAttorney = (id, success, error) => {
	$.get(API_BASE + '/get-attorney.php', {id: id}, success, FORMAT_JSON)
	 .fail(error);
}

getSidesLetterHead = (caseId, userId, success, error) => {
	$.get(API_BASE + '/get-sides-letter.php', {caseId: caseId, userId: userId}, success, FORMAT_JSON)
	 .fail(error);
}

joinCase = (caseId, clientId, success, error) => {
	$.post(API_BASE + '/post-request-join-case.php', {case_id: caseId, client_id: clientId}, success)
	 .fail(error);
}

approveJoinCaseRequest = (userId, caseId, success, error) => {
	$.post(API_BASE + '/post-grant-join-case.php', {user_id: userId, case_id: caseId}, success)
	 .fail(error);
}

denyJoinCaseRequest = (userId, caseId, success, error) => {
	$.post(API_BASE + '/post-deny-join-case.php', {user_id: userId, case_id: caseId}, success)
	 .fail(error);
}

getDiscovery = (id, success, error) => {
  $.get(API_BASE + '/get-discovery.php', {id: id}, success, FORMAT_JSON)
    .fail(error);
}

updateDiscovery = (id, data, success, error) => {
  $.post(API_BASE + '/post-update-discovery.php', {id: id, discovery: data}, success, FORMAT_JSON)
		.fail(error);
}

// with no case id will return only the user's payment methods, not the side's
getPaymentMethods = (caseId = null, success, error) => {
  $.get(API_BASE + '/get-payment-methods.php', {case_id: caseId}, success, FORMAT_JSON)
   .fail(error);
}

getPayment = (id, type, paymentMethodId, saveToSide, saveToProfile) => {
  return fetch(API_BASE + `/get-payment.php?id=${id}&type=${type}&payment_method_id=${paymentMethodId}&save_to_side=${saveToSide}&save_to_profile=${saveToProfile}`);
}

getPaymentSetup = () => {
  return fetch(API_BASE + '/get-payment-setup.php');
}

postMeetConfer = (data, success, error) => {
  $.post(API_BASE + '/post-meet-confer.php', data, success, FORMAT_JSON)
	 .fail(error);
}

serveMeetConfer = (id, success, error) => {
  $.post(API_BASE + '/post-meet-confer-serve.php', {id: id}, success, FORMAT_JSON)
	 .fail(error);
}

getCoupon = (code, success, error) => {
  $.get(API_BASE + '/get-coupon.php', {code: code}, success, FORMAT_JSON)
   .fail(error);
}

// HELPERS ----

serializeFormData = (formData) => {
	return Array.from(formData.keys()).reduce(
		(acc, key) => {acc[key] = formData.get(key); return acc; }, {}
	)
}

posModal = (itemId, itemType, payCallback) => {
	$("#load_general_modal_content").html('');
	$.post( "loadpospopupcontent.php", {id: itemId, item_type: itemType} )
			.done( data => {
					$("#load_general_modal_content").html(data);
					$('#pos-pay-and-serve-btn').attr('onclick', null);
					$('#pos-pay-and-serve-btn').off('click').on('click', () => payAndServe(payCallback));
			} );
	$('#general_modal_title').html("PROOF OF ELECTRONIC SERVICE");
	$('#general-width').addClass('w-900');
	setTimeout( _ => $('#general_modal').modal('show'), 2000);
}


confirmAction = (options) => {
	return swal({
		title: "Are you sure?",
		text:  "You will not be able to undo this action!",
		icon:  "warning",
		dangerMode: true,
		buttons: [true, "Yes, delete it!"],
		// confirmButtonColor: '#187204',
		// cancelButtonColor: '#C2391B',
		...options
	});
}

// .er-invite
const erInviteControl = (selector = '.er-invite') => {
	$(selector).each((idx, elm) => {
		const fieldPrefix = $(elm).data('fieldPrefix') || 'user';
		const placeHolder = $(elm).data('placeHolder') || 'Find users by Name or Bar Number. Start typing...';
		const filters = $(elm).data('filters');
		const inviteOnly = $(elm).data('inviteOnly');
		const idSuffix = Date.now();
		const formId = `er-invite-form-${idSuffix}`;
		const selectId = `er-invite-select-${idSuffix}`;
		const ctaId = `er-invite-cta-${idSuffix}`
		const btnClass = `er-invite-btn-${idSuffix}`;

		$(elm).html(`
			<div  id="er-invite-${idSuffix}">
				<select class="form-control m-b attr_names" name="${fieldPrefix}_id" id="${selectId}"></select>
				<div style="float: right; margin-top: 3px" id="${ctaId}">
					<span style="font-size: 11px;">Can't find the user?</span> <button  type="button" class="${btnClass} btn btn-xs btn-warning">Send Invite &raquo;</button>
				</div>
			</div>
			<div class="row invite-member-form" id="${formId}">
				<div class="form-group col-md-6">
					<input type="text" placeholder="Name" class="form-control" name="${fieldPrefix}_name" />
				</div>
				<div class="form-group col-md-6">
					<input type="text" placeholder="Email" class="form-control"  name="${fieldPrefix}_email" />
				</div>
			</div>
		`);
		$(`#${selectId}`).select2({
			placeholder: placeHolder,
			ajax: {
				url: API_BASE + `/search-users.php?${filters}`,
				dataType: FORMAT_JSON,
				delay: 250,
				allowClear: true,
			},
			width: '100%',
			language: {
				noResults: () => `No Results Found <button  type="button" class="btn btn-xs btn-warning ${btnClass}">Send Invite &raquo;</button>`,
			},
			escapeMarkup: (markup) => markup
		}).on('select2:select', (e) => {
			if ($(e.target).val()) {
				$(`#${ctaId}`).show();
				$(`#${formId}`).hide();
			}
		});
		$(document).on( 'click', `.${btnClass}`, () => {
			$(`#${ctaId}`).hide();
			$(`#${formId}`).show();
			$(`#${selectId}`).val(null).trigger('change.select2').select2('close');
			$(`#${formId} .form-control:first`).focus();
		});
		if ( inviteOnly ) {
			$(`#${formId}`).show();
			$(`#er-invite-${idSuffix}`).hide();
		}
	});
};

// .er-team-attorney-select
const erTeamAttorneySelectControl = (caseId, done = null, selector = '.er-team-attorney-select') => {
	getTeamAttorneys(caseId,
		(response) => {
			const attorneys = JSON.parse(response);
			$(selector).each((idx, elm) => {
				$(elm).html('');
				$.each(attorneys, (idx, attorney) => {
					$(elm).append(`
						<option value="${attorney['id']}" ${attorney['id'] == $(elm).data('value') ? 'selected="selected"' : ''}>
							${attorney['full_name']}
						</option>
					`);
				});
			});
			done && done();
		},
		(e) => console.error(e),
	)
}

// display server response as toastr notification
const showResponseMessage = (response) => {
	response = response.responseJSON ? response.responseJSON : response;
	switch(response.type) {
		case 'warning': toastr.warning(response.msg); break;
		case 'info': toastr.info(response.msg); break;
		case 'success': toastr.success(response.msg); break;
		case 'error': toastr.error(response.msg); break;
	}
}

// display server response as bootstrap alert
const showResponseNotification = (response, containerSelector = '#notifications') => {
	response = JSON.parse(response.responseText);

	let actionButton = ''
	if(response._action) {
		actionButton = `<a class="btn btn-sm btn-primary" href="${response._action.url}">${response._action.text}</a>`
	}

	$(containerSelector).append(`
		<div class="alert alert-${response._color_class ? response._color_class : 'info'} alert-dismissible fade show" role="alert">
			${response.msg}
			${actionButton}
			<button type="button" class="close" data-dismiss="alert" aria-label="Close">
				<span aria-hidden="true">&times;</span>
			</button>
		</div>`);
}

// https://stackoverflow.com/questions/20425771/how-to-replace-1-with-first-2-with-second-3-with-third-etc
// handles up to 99, otherwise returns the number
function stringifyNumber(n) {
	const special = ['zeroth','first', 'second', 'third', 'fourth', 'fifth', 'sixth', 'seventh', 'eighth', 'ninth', 'tenth', 'eleventh', 'twelfth', 'thirteenth', 'fourteenth', 'fifteenth', 'sixteenth', 'seventeenth', 'eighteenth', 'nineteenth'];
	const deca 		= ['twent', 'thirt', 'fort', 'fift', 'sixt', 'sevent', 'eight', 'ninet'];

	if (n > 99) return n;
  if (n < 20) return special[n];
  if (n % 10 === 0) return deca[Math.floor(n/10)-2] + 'ieth';
  return deca[Math.floor(n/10)-2] + 'y-' + special[n%10];
}

// LEGACY ----------------------------------------------------------------------

function editCaseClient(id, caseId)
{
	addparty(id, caseId);
}

function addAttorney(case_id,attorney_type)
{
	var attorney_name	 =	$("#attorney_name").val();
	var attorney_email =	$("#attorney_email").val();
	var client_id 		 = 	$("input[name='client_id[]']:checked").map(function(){return $(this).val();}).get();
	var sl_attorney_id =	$("#sl_attorney_id").val();
	var user_id				 =	$("#user_id").val();

	$.post("post-service-list-user.php", {
		attorney_email:  attorney_email,
		attorney_name: 	 attorney_name,
		client_id:			 client_id,
		case_id: 				 case_id,
		user_id:				 user_id,
		sl_attorney_id: sl_attorney_id
	}).done(function( response ) {
		showResponseMessage(response);
		if(response.type == "success")
		{
			$("#attorney_name").val('');
			$("#attorney_email").val('');
			$('.modal').modal('hide');
			//attDropdownFunction();
			loadCasePeople(case_id);
		}
	}).fail((response) => showResponseMessage(JSON.parse(response.responseText)));
}

function toggleAll(selector, self) {
	const items = $(selector);
	if( items.length > 0 ) {
		const newValue = !$(items[0]).prop("checked");
		items.each( (idx, x) => {
			x = $(x);
			if ( x.prop("checked") != newValue ) x.click();
		} );
		$(self).prop("checked", newValue);
	} else console.warn( `${selector} didn't match anything` );
}


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
			gtag( 'config', APP_GOOGLE_ANALYTICS_ID, { page_path, page_title, } );
		}
	}
	console.log( action, {action, ...extra,} );
}

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
		{id: "meet-confer",     title: "Meet & Confer",                                      video: "demo.mp4",									 },
		{id: "knowledge-base",  title: "Knowledge Base",                                     video: "demo.mp4",              		 },
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
	if (idx < 0) { console.log("[!] TAB NOT FOUND:", currentPage ); debugger; return; }
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

function autogrowTextareas(filter='') { //debugger;
	setTimeout( _ => {
		$('textarea'+filter)
			.on("input", function() { // TODO debounce this once we have a debounce fn available
				const $el = $(this)
				$el.css('height', $el[0].scrollHeight+2+'px' )
				//console.log( $el, "grown")
			} )
	}, 500 )
}

function enableCKEditor( $selector, options ) {
	if( $($selector).length ) {
		const id = $selector.split('#')[1]
		CKEDITOR.replace( id, options );
	}
}

jQuery( $ => {
	autoPlayOrPauseVideos();
	addTooltips();
});


function showKnowledgeBase(requireCoupon = true) {
	if (requireCoupon) {
		swal({
			title: 'Password',
			content: "input",
		}).then((code) => {
			getCoupon(code, 
				(coupon) => selecttab(`knowledge-base`,`kb.php?context=index&coupon=${coupon.code}`, `knowledge-base`),
				(error)  => showResponseMessage(error)
			)
		});
	}
	else {
		selecttab(`knowledge-base`,`kb.php?context=index`, `knowledge-base`);
	}
}
