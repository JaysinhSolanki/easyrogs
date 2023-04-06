var selectAreaConfig = {};
var selectionExists;

// LEGACY
function verifyEmail()
{
	var email	=	$('#email').val();
	if(email == "")
	{
		$("#verification_msg").html("Please enter your email.");
	}
	else
	{
		$.post( "/system/application/verifyemail.php", { email: email })
		.done(function( data ) 
		{
			if(data == "success")
			{
				$("#verification_msg").html("Verification code has been sent to you on your email.");
			}
			else
			{
				$("#verification_msg").html("Error.");
			}
		});
	}
	$("#verification_msg").show();
	setTimeout(function(){ $("#verification_msg").html("")}, 3000);
}
function deleteAccountFunction() {
	swal({ 
		title: "Are you sure to permanently delete Your Account?",
		text: "Once deleted, you will not be able to recover account!",
		icon: 'warning',
		dangerMode: true,
		buttons: [true, "Yes, delete it!"],
		// confirmButtonColor: '#187204',
		// cancelButtonColor: '#C2391B',
	})
	.then( result => {
		if (result) {
			window.location.replace("deletemyaccount.php");
		}
	});
}

function barnoFunction(id)
{
	 if($("#fkgroupid").prop("checked") == true)
	 {
		 $("#barnumber").show();
		 $("#myteamDiv").show();
		 $("#attorneyMastheadDiv").show();
	 }
	 else
	 {
		 $("#barnumber").hide();
		 $("#myteamDiv").hide();
		 $("#attorneyMastheadDiv").hide();
	 }
	/*if(id == 3)
	{
		$("#barnumber").show();
	}
	else
	{
		$("#barnumber").hide();
	}*/
}
function checkEmail(oldEmail, newEmail)
{
  $(".verifyDiv").css('display', oldEmail === newEmail ? 'none' : 'block');
}

// Edit Letter Head Time Aksed to User
function letterHeadChanged() {
	swal({ 
		title: "Are you sure you want to changed Letter Head?",
		text: "Once Changed, you will not be able to recover!",
		icon: 'warning',
		dangerMode: true,
		buttons: [true, "Yes"],
	})
	.then( result => {
		if (result) {
			$("#edit_letterhead").remove();
			$("#edit_letterhead_img").remove();
			$(".edit_letterhead_btn").remove();

			$('#letterhead_edit_field').removeClass('hide').addClass('show');
			$('#letterhead_edit').removeClass('show').addClass('hide');
			$('#letterhead_edit').val("");

			$('#header_height').val("");
			$('#footer_height').val("");
			
		}
	});
}

// Letter Head File Validation
$('#letterhead, #letterhead_edit_field').on('change', function(evt) {
	console.log("changed events");
	var ext 				= 	$(this).val().split('.').pop().toLowerCase();
	var allowedExtension	=	['pdf', 'PDF'];
	var files 				=	evt.target.files[0]; 
	var filesID				=	$(this).attr('id');

	if($.inArray(ext, allowedExtension) == -1) {
		if($("#letterheadError").length)
			$("#letterheadError").remove();					
		$(this).after("<div id='letterheadError' style='color:red;'>Please select valid File Format. Allowed File Format (PDF).</div>");
		$('.buttonid').prop('disabled', true);
		return false;
	} else {
		// Get Count of PDF File
		var reader 	=	new FileReader();
		reader.readAsBinaryString(files);
		reader.onloadend = function(){
			var count = reader.result.match(/\/Type[\s]*\/Page[^s]/g).length;
			if(count > 1){
				if($("#letterheadError").length)
					$("#letterheadError").remove();	

				$("#"+filesID).after(`<div id='letterheadError' style='color:red;'>Upload PDF Page size is ${count}. Please upload 1 page PDF.</div>`);
				$('.buttonid').prop('disabled', true);
				return false;
			} else {
        		const reader = new FileReader();
        		var filename = files.name;
				const preview = document.querySelector('iframe');

        		$('#pdfIframe').attr({ width: '100%', height: '100%', scrolling: 'no' });

				// Open PDF Modal
				$('#pdf-modal').modal('toggle');

				reader.addEventListener("load", function () {
					// convert file to base64 string
					preview.src = reader.result+'#toolbar=0&navpanes=0&zoom=scale&view=Fit';
				  }, false);
		  
				  if (files) {
					reader.readAsDataURL(files);
				  }
		  
				  $('.pdf-display').children('div').css({ width: '100%', height: '100%', scrolling: 'no' });

				  loadHeaderFooterAreas();
			}
		}

		$("#letterheadError").remove();	
		$('.buttonid').prop('disabled', false);
	}
});

function loadHeaderFooterAreas(){
	sleep(500).then(() => {
		windowWidth = $('.pdf-display').width();
		windowHeight = $('.pdf-display').height();

		var footer_position =	( $('#pdfIframe').position().top + $('#pdfIframe').outerHeight(true) - 300 );
		// $('#pdfIframe').selectAreas();

		var areaOptions = [
			{
				x: 0,
				y: 0,
				width: $('#pdfIframe').width(),
				height: 200,
			},
			{
				y: footer_position,
				x: 0,
				width: $('#pdfIframe').width(),
				height: 200,
			}
		];
		
		$('#pdfIframe').selectAreas('add', areaOptions);
		$('.pdf-display').children('div').css({ width: '100%', height: '100%', scrolling: 'no' });
	});
}

$(document).ready(function () {
	selectAreaConfig = {
		minSize: [10, 10],
		onChanged: debugQtyAreas,
		// overlayOpacity: 0.1,
		// outlineOpacity: 0.1,
		// width: 500,
		// maxAreas:2,
		// allowEdit:false,
		// allowResize:false,
		// allowMove:false,
		// allowNudge:false,
		// allowDelete:false,
		// allowSelect:false,
	};

	$('#btnView').click(function () {
		var areas = $('#pdfIframe').selectAreas('areas');
		displayAreas(areas);

		setTimeout(function() { 
			// Open PDF Modal
			$('#pdf-modal').modal('hide');
		}, 1000);
		
	});
	$('#btnReset').click(function () {
		output("reset")
		$('#pdfIframe').selectAreas('reset');
	});
});

function sleep (time) {
  return new Promise((resolve) => setTimeout(resolve, time));
}

function areaToString (area) {
	return (typeof area.id === "undefined") ? "" : 'Height  = '+ area.height.toFixed(2) + '<br />'
}

function output (text) {
	$('#output').html(text);
}

// Log the quantity of selections
function debugQtyAreas (event, id, areas) {
	console.log(areas.length + " areas", arguments);
};

// Display areas coordinates in a div
function displayAreas (areas) {
	var text = "";
	$.each(areas, function (id, area) {
		if(area.id == 0){
			$('#header_height').val(area.height.toFixed(2));
		} else {
			$('#footer_height').val(area.height.toFixed(2));
		}

		text += areaToString(area);
	});
	output(text);
};