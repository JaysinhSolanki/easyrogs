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
  if ( confirm.value ) {
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