// automatically toggle definitions when present
$(document).ready( _ => { 
  toggleKBSidebar(mcFormId, ObjectionKillerPanel) 
})

function save(successCallback = null) {
  const formData = new FormData($('form.er-mc-body')[0])
  const data = serializeFormData(formData);  

  postMeetConfer(data, (mc) => {
    mcId = mc.id
    $('input[name=id]').val(mcId)
    toastr.success('Meet & Confer Letter saved successfully.')
    successCallback && successCallback()
  }, (error) => {
    showResponseMessage(error)
  })
}


// reply toggle btn
$('.er-mc-toggle-question').on('click', function() {
  const $button  = $(this).siblings('a.er-mc-toggle-question')
  const target   = $(this).data('target');
  const isActive = $(this).toggleClass('active').hasClass('active') // update UI, return active status

  $button.toggleClass('active') // update button as well

  $(`${target} textarea`).prop( "disabled", !isActive ); // make M&C textarea disabled when ianctive
})

function goToDiscoveries() {
  selecttab('45_tab', `discoveries.php?pid=${mcCaseId}`,'45') // go back to case discovery page
}

// back
$('.er-mc-cancel-button').on('click', goToDiscoveries);

// save
$('.er-mc-save-button').on('click', _ => save())

// serve
$('.er-mc-serve-button').on('click', _ => {
  save(() => {
    posModal(mcId, 'meet_confer', () => {
      $.LoadingOverlay("show");
      serveMeetConfer(mcId, (success) => {
        $('#general_modal').modal('toggle');
        $.LoadingOverlay("hide");
        confirmAction({
          title: 'Service Complete!',
          text: creditsText(),
          icon: 'success',
          dangerMode: false,
          buttons: null
        });
        goToDiscoveries();
      }, (error) => {
        showResponseMessage(error)
      });
    });
  })
})