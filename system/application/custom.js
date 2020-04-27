// not const, need these in the global scope (window object)
API_BASE = '/system/application';
FORMAT_JSON = 'json';
FORMAT_HTML = 'html';

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

confirmAction = (options) => {
	return Swal.fire({
    title: "Are you sure?",
    text:  "You will not be able to undo this action!",
    icon:  "warning",
    showCancelButton: true,
    confirmButtonColor: '#187204',
    cancelButtonColor: '#C2391B',
		confirmButtonText: "Yes, delete it!",
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
  switch(response.type) {
    case 'warning': toastr.warning(response.msg); break;
    case 'info': toastr.info(response.msg); break;
    case 'success': toastr.success(response.msg); break;
    case 'error': toastr.error(response.msg); break;
  }
}

// LEGACY ----------------------------------------------------------------------
function loadAttoneysFunction(case_id,attorney_type,loaddiv)
{
	//alert(case_id+" "+attorney_type);
	$("#"+loaddiv).load("attorneyload.php?case_id="+case_id+"&attorney_type="+attorney_type);
}

function deleteAttorney(id,attorney_type,case_team_id,case_id)
{
	if(attorney_type == 1) 
	{
		var title	=	"Are you sure you want to delete this person from Your Team?";
	}
	else if(attorney_type == 2)
	{
		var title	=	"Are you sure you want to delete this person from the Service List?";
	}
	Swal.fire({
	title: title,
	text: "You will not be able to undo this action!",
	icon: 'warning',
	showCancelButton: true,
confirmButtonColor: '#187204',
	cancelButtonColor: '#C2391B',
	confirmButtonText: "Yes, delete it!" 
	}).then((result) => {
	if (result.value) {
	$.post( "caseattorneydelete.php", { id: id,attorney_type:attorney_type,case_team_id:case_team_id} );
	$("#attr_"+id).remove();
	if(attorney_type == 2)
	{
		attDropdownFunction();	
	}
	else if(attorney_type == 3)
	{
		loadMyTeamFunction(case_id);
	}
	}
	});
	$( ".swal-button-container:first" ).css( "float", "right" );
	
	
	/*swal({
		title: "Are you sure to permanently delete?",
		text: "You will not be able to undo this action!",
		icon: "warning",
		buttons: true,
		dangerMode: true,
	})
	.then((willDelete) => {
		if (willDelete) 
		{
			$.post( "caseattorneydelete.php", { id: id,attorney_type:attorney_type,case_team_id:case_team_id} );
			$("#attr_"+id).remove();
			if(attorney_type == 2)
			{
				attDropdownFunction();	
			}
			else if(attorney_type == 3)
			{
				loadMyTeamFunction(case_id);
			}
		}	
	});
	$( ".swal-button-container:first" ).css( "float", "right" );*/
}
function editAttorney(id,attorney_type,case_id)
{
	if(attorney_type==2)
	{
		//addservicelist(id);
	}
	else if(attorney_type==3)
	{
		//addcaseteam(id);
	}
}
function editCaseClient(id, caseId)
{
	addparty(id, caseId);
}
function addNewCaseTeamAttorney(case_id)
{
	var caseteam_attr_email	=	$("#caseteam_attr_email").val();
	var caseteam_attr_name	=	$("#caseteam_attr_name").val();
	var attorney_id			=	$("#attorney_id").val(); //Edit Case
	if($("#makememberofmyteam").prop("checked") == true)
	{
		var makememberofmyteam	=	1;
	}
	else
	{
		var makememberofmyteam	=	0;
	}
	if(case_id == 0)
	{
		var makememberofmyteam	=	1;
	}
	$("#msgAttrCaseTeam").html("");
	//alert(case_id);
	$.post( "addcaseteamattorney.php", { attorney_email: caseteam_attr_email, attorney_name: caseteam_attr_name,case_id: case_id,makememberofmyteam:makememberofmyteam,attorney_id:attorney_id}).done(function( data ) 
	{
		var obj = JSON.parse(data);
		if(obj.type == "success")
		{
			$("#caseteam_attr_email").val('');
			$("#caseteam_attr_name").val('');
			//loadMyTeamFunction(obj.case_id);
			if(obj.case_id == 0)
			{
				loadAttoneysFunction(0,1,"loadattoneys");
			}
			else
			{
				//loadCaseAttorneys(obj.case_id);
				loadAttoneysFunction(obj.case_id,3,"loadattoneys3");	
			}
			$('#modalcaseteam').modal('toggle');
		}
		else
		{
			$("#msgAttrCaseTeam").html(obj.msg);
		}
	}); 
}
function addAttorney(case_id,attorney_type)
{
	var attorney_name	=	$("#attorney_name").val();
	var attorney_email	=	$("#attorney_email").val();
	//var client_id		=	$("#client_id").val();
	var client_id 		= 	$("input[name='client_id[]']:checked").map(function(){return $(this).val();}).get();
	var editattorney_id	=	$("#editattorney_id").val();
	$("#msgAttr").html("");
	$.post( "post-service-list-user.php", { attorney_email: attorney_email, attorney_name: attorney_name,client_id:client_id,attorney_type:attorney_type,case_id: case_id,editattorney_id:editattorney_id}).done(function( data ) 
	{
		var obj = JSON.parse(data); 
		if(obj.type == "success")
		{
			$("#attorney_name").val('');
			$("#attorney_email").val('');
			$('.modal').modal('hide');
			var loaddiv;
			if(obj.attorney_type == 2)
			{
				attDropdownFunction();
				//loadClientsFunction(obj.case_id);	
				loaddiv	=	"loadattoneys2";
			}
			else if(obj.attorney_type == 3)
			{
				loaddiv	=	"loadattoneys3";
			}
			else
			{
				loaddiv	=	"loadattoneys";
			}
			loadAttoneysFunction(obj.case_id,obj.attorney_type,loaddiv);
			loadCasePeople(case_id);
		}
		else
		{
			$("#msgAttr").html(obj.msg);
		}
	}).fail((response) => showResponseMessage(JSON.parse(response.responseText))); 
}
/*function addMyAttorneyToCase(attorney_id,case_id)
{
	$.post( "addmyattorneytocase.php", { attorney_id: attorney_id, case_id: case_id}).done(function( data ) 
	{
		var obj = JSON.parse(data);
		if(obj.type == "success")
		{
			//loadMyTeamFunction(obj.case_id)
			loadAttoneysFunction(obj.case_id,3,"loadattoneys3");
		}
	});
}*/
function addMyAttorneyToCase(case_id)
{
	$.post( "addmyattorneytocase.php", {  case_id: case_id}).done(function( data ) 
	{
		var obj = JSON.parse(data);
		if(obj.type == "success")
		{
			loadAttoneysFunction(obj.case_id,3,"loadattoneys3");
		}
	});
}
function loadMyTeamFunction(case_id)
{
	$("#loadmyteam").load("myattorneyload.php?case_id="+case_id)
}

function loadModalCaseTeamFunction(case_id=0,attorney_id = 0,makememberofmyteam=0)
{
	$.post( "loadcaseteammodal.php", { case_id: case_id,attorney_id: attorney_id,makememberofmyteam:makememberofmyteam}).done(function( data ) 
	{
		$("#modalcaseteam_content").html(data);
		$("#modalcaseteam").modal("toggle");
	});
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

$(document).ready(function(){
  $('.tooltipshow').tooltip({
	   container: 'body'
	  });
});