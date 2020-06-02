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

getDiscoveryPayment = (id, paymentMethodId, saveToSide, saveToProfile) => {
  return fetch(API_BASE + `/get-discovery-payment.php?id=${id}&payment_method_id=${paymentMethodId}&save_to_side=${saveToSide}&save_to_profile=${saveToProfile}`);
}

getPaymentSetup = () => {
  return fetch(API_BASE + '/get-payment-setup.php');
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
  
const showResponseMessage = (response) => {
	response = response.responseJSON ? response.responseJSON : response;
  switch(response.type) {
    case 'warning': toastr.warning(response.msg); break;
    case 'info': toastr.info(response.msg); break;
    case 'success': toastr.success(response.msg); break;
    case 'error': toastr.error(response.msg); break;
  }
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

$(document).ready( _ => {
  $('.tooltipshow').tooltip({
	   container: 'body'
	  });
});