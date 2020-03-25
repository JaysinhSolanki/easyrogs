// not const, need these in the global scope (window object)
API_BASE = '/system/application';
FORMAT_JSON = 'json';
FORMAT_HTML = 'html';

getTeam = (success, error, format = FORMAT_HTML) => {
	$.get( API_BASE + '/get-team.php', {format: format}, success)
	 .fail(error);
}

deleteTeamMember = (memberId, success, error) => {
	$.post( API_BASE + '/delete-team-member.php', {memberId: memberId}, success)
	 .fail(error)
}

addTeamMember = (memberId, name, email, success, error) => {
	$.post( API_BASE + '/add-team-member.php', 
		{memberId: memberId, name: name, email: email}, 
		success, 'json'
	).fail(error)
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

// LEGACY
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
function editCaseClient(id)
{
	addparty(id);
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
				loadCaseAttorneys(obj.case_id);
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
	console.log(client_id);
	var editattorney_id	=	$("#editattorney_id").val();
	$("#msgAttr").html("");
	$.post( "addattorney.php", { attorney_email: attorney_email, attorney_name: attorney_name,client_id:client_id,attorney_type:attorney_type,case_id: case_id,editattorney_id:editattorney_id}).done(function( data ) 
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
				//attDropdownFunction();
				loadClientsFunction(obj.case_id);	
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
		}
		else
		{
			$("#msgAttr").html(obj.msg);
		}
	}); 
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
$(document).ready(function(){
  $('.tooltipshow').tooltip({
	   container: 'body'
	  });
});