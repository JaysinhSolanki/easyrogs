
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
    noResults: () => `No Results Found.`,
  }  
}).on('select2:select', (e) => {
  const caseId = $(e.target).val();
  if (caseId) {
    getCaseClients(caseId, 
      (clients) => {
        $('#join-case-client').html(`<option value="">Select a Party</option>`);
        for(k in clients) {
          $('#join-case-client').append(
            `<option value="${clients[k].id}">${clients[k].client_name}</option>`
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
