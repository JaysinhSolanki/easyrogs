submitCase = () => {
  // not touching this for now:
  addform('caseaction.php','clientform','wrapper','get-cases.php');      
}

showExistingCaseModal = (aCase) => {
  $('#join-existing-case-id').val(aCase.id);
  $('.join-action-btn, .join-case-clients').show();
  $('.join-case-text').html(`
    <strong>${aCase.case_title}</strong> <br/>
    ${aCase.county_name} County Case No. ${aCase.case_number} 
    <br/><br/>
    <strong>Already exists.</strong>
  `);
  getCaseClients(aCase.id, 
    (clients) => {
      $('#join-existing-case-client').html(`<option value="">Select a Party</option>`);
      for(k in clients) {
        $('#join-existing-case-client').append(
          `<option value="${clients[k].id}">${clients[k].client_name}</option>`
        );
      }
      $('#existing-case-modal').modal('show');
    },
    (e) => console.error(e)
  )
  return true;
}

// if case number changes restart the joining flow
$('input#case_number').on('change', () => { 
  $('.save-case-btn').data('force', false);
  $('#existing-case-modal .save-case-btn').data('force', true); 
});

// This hook handles all save buttons
// TODO: decouple this logic v
$('.save-case-btn').on('click', function() {
  const caseNumber = $('input[name=case_number]').val();
  const force = !!$(this).data('force');
  
  if(!isDraft)  { return submitCase(); }
  
  if (force) {
    $('.save-case-btn').data('force', true);
    if ($('#existing-case-modal').is(':visible')) {
      $('#existing-case-modal').modal('hide').on('hidden.bs.modal', submitCase);
    }
    else submitCase();    
  }
  else if (caseNumber) {
    getCaseByNumber(caseNumber, 
      (aCase) => {
        if (aCase.id && !aCase.team_member) {
          showExistingCaseModal(aCase)
        }
        else if(aCase.team_member) {
          toastr.warning('You are already part of this case.');
        }
        else submitCase();
      }
    );
  }
  else {
    toastr.error("Please fill the require fields.");
  }
});

// join case btn
$('#join-existing-case-btn').on('click', function() {
  const clientId = $('#join-existing-case-client').val();
  const caseId   = $('#join-existing-case-id').val();
  
  if (!clientId) { return toastr.error('Please select a Client.'); }
  
  joinCase(caseId, clientId,
    (response) => {
      $('.join-case-clients, .join-action-btn').hide();
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
    (e) => showResponseMessage(e)
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