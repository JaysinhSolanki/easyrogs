submitCase = () => {
  // not touching this for now:
  addform('caseaction.php','clientform','wrapper','get-cases.php');      
}

showExistingCaseModal = (aCase, isTeamMember) => {
  $('.join-case-text').html(`
    <strong>${aCase.case_title}</strong> <br/>
    ${aCase.county_name} County Case No. ${aCase.case_number} 
    <br/><br/>
    <strong>${isTeamMember ? 'You are already part of this case' : 'Already exists'}.</strong>
  `);

  if (isTeamMember) {
    $('#join-close-btn, #join-case-btn, .join-case-clients').hide();    
    $('#join-create-case-btn').show();
    $('#existing-case-modal').modal('show');
  }
  else {
    $('#join-existing-case-id').val(aCase.id);
    $('#join-close-btn').hide();
    $('#join-case-btn, #join-create-case-btn, .join-case-clients').show();
    getCaseClients(aCase.id, 
      (clients) => {
        $('#join-existing-case-client').html(`<option value="">Select a Party</option>`);
        for(k in clients) {
          $('#join-existing-case-client').append(
            `<option value="${clients[k].id}">${clients[k].client_role} ${clients[k].client_name}</option>`
          );
        }
        $('#existing-case-modal').modal('show');
      },
      (e) => console.error(e)
    )
  }

  return true;
}

$('.save-case-btn').on('click', function() {
  const caseNumber = $('input[name=case_number]').val();
  const force      = !!$(this).data('force');

  if(!isDraft)  { return submitCase(); }
  
  if (force) {
    $('.save-case-btn').data('force', true);
    if ($('#existing-case-modal').is(':visible')) {
      $('#existing-case-modal').modal('hide').on('hidden.bs.modal', submitCase );
    }
    else submitCase();    
  }
  else if (caseNumber) {
    getCaseByNumber(caseNumber, 
      (aCase) => {
        if (aCase.id) {
          showExistingCaseModal(aCase, aCase.team_member)
        }
        else submitCase();
      }
    );
  }
  else {
    toastr.error("Please fill the require fields.");
  }
});

$('#join-create-case-btn').on('click', () =>{
  $('.save-case-btn').data('force', true);
});

// if case number changes restart the joining flow
$('input#case_number').on('change', () => { 
  $('.save-case-btn').data('force', false);
});

$('input#case_number').on('blur', function() {
  if (!isDraft) { return }
  
  const caseNumber = $(this).val();
  if (caseNumber) {
    getCaseByNumber(caseNumber, 
      (aCase) => aCase.id && showExistingCaseModal(aCase, aCase.team_member)
    );
  }
});

// join case btn
$('#join-case-btn').on('click', function() {
  const clientId = $('#join-existing-case-client').val();
  const caseId   = $('#join-existing-case-id').val();
  
  if (!clientId) { return toastr.error('Please select a Client.'); }
  
  joinCase(caseId, clientId,
    (response) => {
      $('.join-case-clients, #join-case-btn, #join-create-case-btn').hide();
      $('#join-close-btn').show();
      $('.join-case-text').html(`${response.msg}`);
      $('#existing-case-modal').on('hidden.bs.modal', () => {
        if (response.awaiting_request) {
          $('#wrapper').load('get-cases.php');
        }
        else {
          $('#wrapper').load(`get-case.php?id=${caseId}`);
        }        
      });      
    },
    (e) => {console.log(e);showResponseMessage(e)}
  )
  
});

$(document).on('click', ".approve-join-request", function(e) {
  const caseId = $(e.target).data('caseId');
  const userId = $(e.target).data('userId');
  
  approveJoinCaseRequest(userId, caseId, 
    (response) => showResponseMessage(response),
    (e)        => showResponseMessage(e),
  );
  loadCasePeople(caseId);
});

$(document).on('click', ".deny-join-request", function(e) {
  const caseId = $(e.target).data('caseId');
  const userId = $(e.target).data('userId');

  denyJoinCaseRequest(userId, caseId, 
    (response) => showResponseMessage(response),
    (e)        => showResponseMessage(e),
  );
  loadCasePeople(caseId);
});

// service list
$(document).on('click', '.delete-service-list-user-btn', async function(e) {
  const element = $(e.target).parent();
  const userId  = element.data('userId');
  const caseId  = element.data('caseId');
  
  confirm = await confirmAction();
  if ( confirm ) {
    deleteServiceListUser(userId, caseId, 
      (response) => {
        showResponseMessage(response);
        loadCasePeople(caseId);
      },
      (error)    => showResponseMessage(error)
    );
  }

});

$(document).off('click', '.edit-service-list-user-btn');
$(document).on('click', '.edit-service-list-user-btn', (e) => {
  const element      = $(e.target).parent();
  const userId       = element.data('userId');
  const caseId       = element.data('caseId');
  const slAttorneyId = element.data('slAttorneyId');

  loadServiceListModal(caseId, userId, slAttorneyId);
});

// Edit Letter Head Time Aksed to User
function letterHeadChanged() {
  swal({
    title: "Are you sure you want to changed Letter Head?",
    text: "Once Changed, you will not be able to recover!",
    icon: "warning",
    dangerMode: true,
    buttons: [true, "Yes"],
  }).then((result) => {
    if (result) {
      // $("#edit_letterhead").remove();
      // $("#edit_letterhead_img").remove();
      // $(".edit_letterhead_btn").remove();
      // $("#letterhead_edit").remove();

      if (!$(".case-profile-letterhead").hasClass("hide")) {
        $(".case-profile-letterhead").addClass("hide");
      }

      if (!$(".edit_letterhead_btn").hasClass("hide")) {
        $(".edit_letterhead_btn").addClass("hide");
      }

      $("#letterhead_edit_field").removeClass("hide").addClass("show");

      $("#letterhead_edit").val(null);
      $("#letterhead_edit").removeClass("show").addClass("hide");

      $("#header_height").val(null);
      $("#footer_height").val(null);
    }
  });
}

// Letter Head File Validation
$("#letterheads, #letterhead_edit_field").on("change", function (evt) {
  console.log("changed events");
  var ext = $(this).val().split(".").pop().toLowerCase();
  var allowedExtension = ["pdf", "PDF"];
  var files = evt.target.files[0];
  var filesID = $(this).attr("id");

  if ($.inArray(ext, allowedExtension) == -1) {
    if ($("#letterheadError").length) $("#letterheadError").remove();
    $(this).after(
      "<div id='letterheadError' style='color:red;'>Please select valid File Format. Allowed File Format (PDF).</div>"
    );
    $(".buttonid").prop("disabled", true);
    return false;
  } else {
    // Get Count of PDF File
    var reader = new FileReader();
    reader.readAsBinaryString(files);
    reader.onloadend = function () {
      var count = reader.result.match(/\/Type[\s]*\/Page[^s]/g).length;
      if (count > 1) {
        if ($("#letterheadError").length) $("#letterheadError").remove();

        $("#" + filesID).after(
          `<div id='letterheadError' style='color:red;'>Upload PDF Page size is ${count}. Please upload 1 page PDF.</div>`
        );
        $(".buttonid").prop("disabled", true);
        return false;
      } else {
        const reader = new FileReader();
        var filename = files.name;
        const preview = document.querySelector("iframe");

        $("#pdfIframe").attr({
          width: "100%",
          height: "100%",
          scrolling: "no",
        });

        // Open PDF Modal
        $("#pdf-modal").modal("toggle");

        reader.addEventListener(
          "load",
          function () {
            // convert file to base64 string
            preview.src =
              reader.result + "#toolbar=0&navpanes=0&zoom=scale&view=Fit";
          },
          false
        );

        if (files) {
          reader.readAsDataURL(files);
        }

        $(".pdf-display")
          .children("div")
          .css({ width: "100%", height: "100%", scrolling: "no" });

        loadHeaderFooterAreas();
      }
    };

    $("#letterheadError").remove();
    $(".buttonid").prop("disabled", false);
  }
});

function loadHeaderFooterAreas() {
  sleep(500).then(() => {
    windowWidth = $(".pdf-display").width();
    windowHeight = $(".pdf-display").height();
    // $('#pdfIframe').selectAreas();

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

    $("#pdfIframe").selectAreas("add", areaOptions);
    $(".pdf-display")
      .children("div")
      .css({ width: "100%", height: "100%", scrolling: "no" });
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

  $("#btnView").click(function () {
    var areas = $("#pdfIframe").selectAreas("areas");
    displayAreas(areas);

    setTimeout(function () {
      // Open PDF Modal
      $("#pdf-modal").modal("hide");
    }, 1000);
  });
  $("#btnReset").click(function () {
    output("reset");
    $("#pdfIframe").selectAreas("reset");
  });
});

function sleep(time) {
  return new Promise((resolve) => setTimeout(resolve, time));
}

function areaToString(area) {
  return typeof area.id === "undefined"
    ? ""
    : "Height  = " + area.height.toFixed(2) + "<br />";
}

function output(text) {
  $("#output").html(text);
}

// Log the quantity of selections
function debugQtyAreas(event, id, areas) {
  console.log(areas.length + " areas", arguments);
}

// Display areas coordinates in a div
function displayAreas(areas) {
  var text = "";
  $.each(areas, function (id, area) {
    if (area.id == 0) {
      $("#header_height").val(area.height.toFixed(2));
    } else {
      $("#footer_height").val(area.height.toFixed(2));
    }

    text += areaToString(area);
  });
  output(text);
}

function ValidateSingleInput(oInput) {
  var _validFileExtensions = [".pdf"];

  if (oInput.type == "file") {
    var sFileName = oInput.value;
    if (sFileName.length > 0) {
      var blnValid = false;
      for (var j = 0; j < _validFileExtensions.length; j++) {
        var sCurExtension = _validFileExtensions[j];
        if (
          sFileName
            .substr(
              sFileName.length - sCurExtension.length,
              sCurExtension.length
            )
            .toLowerCase() == sCurExtension.toLowerCase()
        ) {
          blnValid = true;
          break;
        }
      }

      if (!blnValid) {
        alert(
          "Sorry, is invalid, allowed extensions are: " +
            _validFileExtensions.join(", ")
        );
        oInput.value = "";
        return false;
      }
    }
  }
  return true;
}
