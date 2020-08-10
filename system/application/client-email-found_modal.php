<div class="modal fade" id="client-email-found_modal" role="dialog" tabindex="-1" data-backdrop="static">
  <div class="modal-dialog" role="document" id="m-width">
    <div class="modal-content">
      <div class="modal-header" style="padding:13px !important">
        <button type="button" class="close" data-dismiss="modal" aria-label="Cancel" style="font-size: 30px;"><span aria-hidden="true">&times;</span></button>
        <h5 class="modal-title text-center" style="font-size:24px">Please enter <span class="responding-client-name" />'s email address below</h5>
      </div>
      <div class="modal-body" id="clientemailfound_loads_here"></div>
    </div>
  </div>
</div>

<script language="text/javascript">
function callclientemailmodal( discovery_id, actiontype, client_id ) {
    $("#clientemailfound_loads_here").html("");
    $.post( "loadclientemailmodal.php", { discovery_id, actiontype, client_id } )
        .done( data => {
            $("#clientemailfound_loads_here").html(data);
            $('#client-email-found_modal').modal('show');
        });
}

jQuery( $ => { 
  $('#client-email-found_modal')
    .on('show.bs.modal', _ => { 
        const name = globalThis['respondent'] && globalThis['respondent'].name;
        $('#client-email-found_modal .responding-client-name').text( name || 'the client' );
    } );
});
</script>
