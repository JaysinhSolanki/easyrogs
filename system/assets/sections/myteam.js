const loadTeam = () => {
  getTeam(
    (response) => $('#loadattoneys').html(response),
    (error) => {
      // this is not supposed to happen in normal scenarios
      console.error('An error occurred retrieving your team.', error);
    }
  );
}

const onDeleteTeamMember = async (e) => {
  const memberId = $(e.target).parent().data('id');
  const confirm = await confirmAction({
    title: "Are you sure you want to delete this person from Your Team?",
  });
  confirm && deleteTeamMember(memberId, 
    () => $(`#attr_${memberId}`).remove(),
    (error) => {
      toastr.error('We were unable to delete the team member at this time.');
      console.error(error)
    });
}

const onSubmitTeamMember = (e) => {
  const memberId = $('select[name="member_id"]').val();
  const name     = $('input[name="member_name"]').val();
  const email    = $('input[name="member_email"]').val();
  
  valid = memberId || (name && email);

  if (!valid) {
    toastr.error('Please select or invite an user');
    return;
  }

  addTeamMember(memberId, name, email, 
    (response) => {
      showResponseMessage(response);
      loadTeam();
      $('input[name="member_name"], input[name="member_email"]')
        .val('')
        .first()
        .focus();
      $('#memberModal').modal('hide');
    },
    (e) => showResponseMessage(e.responseJSON)
  )
}

const onAddTeamMember = (e) => {
  $('#memberModal').modal('show');
}

$(document).ready(() => {
  // controls
  erInviteControl();
  $('[data-toggle="tooltip"]').tooltip();

  // render team
  loadTeam();
  
  // Hooks
  $(document).on('click', 'a.delete-team-member', onDeleteTeamMember);
  $(document).on('click', 'a#submit-team-member-btn', onSubmitTeamMember);
  $(document).on('click', 'a#add-team-member-btn', onAddTeamMember);
});