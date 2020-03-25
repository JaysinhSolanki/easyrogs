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
  confirm.value && deleteTeamMember(memberId, 
    () => $(`#attr_${memberId}`).remove(),
    (error) => {
      toastr.error('We were unable to delete the team member at this time.');
      console.error(error)
    });
}

const onAddTeamMember = (e) => {
  const memberId = $('select#member_id').val();
  const name     = $('input#member_name').val();
  const email    = $('input#member_email').val();

  addTeamMember(memberId, name, email, 
    (response) => {
      toastr.info(response.msg);
      loadTeam();
      $('input#member_name, input#member_email').val('');
    },
    (response) => toastr.error(response.msg),
  )
}

const onMemberSelectChange = (e) => {
  const displayForm = $('select#member_id').val() == '0';
  $('#invite-member-form').css('display', displayForm ? 'block' : 'none');
  if (displayForm) {
    $('#member_name').focus()
  }  
}

$(document).ready(() => {
  // tooltips
  $('[data-toggle="tooltip"]').tooltip();

  // render team
  loadTeam();
  
  // controls
  $('select#member_id').select2({
    placeholder: 'Find or invite member by Name or Bar Number. Start typing...',
    ajax: {
      url: API_BASE + '/search-users.php',
      dataType: 'json',
      delay: 250,
      allowClear: true,
      processResults: function (data) {
        data.results.push({id: '0', text: 'Invite Member'});
        return data;
      }
    }
  })

  // Hooks
  $(document).on('click', 'a.delete-team-member', onDeleteTeamMember);
  $(document).on('click', 'a#add-team-member-btn', onAddTeamMember);
  $(document).on('select2:select', 'select#member_id', onMemberSelectChange);
});


// LEGACY
function addmyteammember()
{
	$('#addmyteammember').modal('show');
}