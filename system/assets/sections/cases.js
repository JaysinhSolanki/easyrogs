// case search
$(`select.case-search`).select2({
  placeholder: 'Search by name or number...',
  ajax: {
    url: API_BASE + `/search-cases.php`,
    dataType: 'json',
    delay: 250,
    allowClear: true,
  },
  width: '100%',
  language: {
    noResults: () => {
      const term = JSON.parse(event.target.response).term;
      return term ? `No Results Found. <button class="btn btn-xs btn-success join-add-new-case-btn">Create New Case</button>` : ''
    },
  },
  escapeMarkup: function(markup) {
    return markup;
  },
}).on('select2:select', (e) => {
  const caseId = $(e.target).val();
  if (caseId) {
    getCaseClients(caseId, 
      (clients) => {
        $('#join-case-client').html(`<option value="">Select a Party</option>`);
        for(k in clients) {
          $('#join-case-client').append(
            `<option value="${clients[k].id}">${clients[k].client_role} ${clients[k].client_name}</option>`
          );
        }
        $('.join-case-clients').show()
      }, 
      (e) => showResponseMessage(e)
    )
  }
});

$('#join-case-btn').on('click', function() {
  const caseId   = $('#join-case-id').val();
  const clientId = $('#join-case-client').val();
  
  if (!caseId) { return toastr.error('Please select a Case.'); }
  if (!clientId) { return toastr.error('Please select a Client.'); }
  
  joinCase(caseId, clientId,
    (response) => {
      $('.join-case-action').hide();
      $('.join-case-text').html(`${response.msg}`);
      $('#join-case-modal').on('hidden.bs.modal', () => {
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

$('.add-new-case-btn').on('click', () => {
  // check can create case
  getPermissions(
    (permissions) => {
      if (permissions.cases.create) {
        selecttab('46_tab','get-case.php','46');
      }
      else {
        $('#create-case-error-modal').modal('show');
      }
    }
  )
});

$(document).on('click', '.join-add-new-case-btn', () => {
  $('#join-case-modal').on('hidden.bs.modal', () => {
    $('.add-new-case-btn').trigger('click');
  });
  $('.select2-container').hide();
  $('#join-case-modal').modal('hide');
});
