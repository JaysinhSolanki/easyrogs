<div id="discovery-client-instructions_modal" class="modal fade " role="dialog" style="min-height: 95vh;">
  <div class="modal-dialog" style="width: 50%; margin:2rem auto; padding:0;">
    <div class="modal-content">
      <div class="modal-header" style="padding: 15px;">
        <button type="button" class="close" data-dismiss="modal" aria-label="Cancel" style="font-size: 25px !important;"><span aria-hidden="true">&times;</span></button>
        <h5 class="modal-title" style="text-align:center; font-size: 22px;"> Optional Instructions for Client </h5>
      </div>
      <div class="modal-body">
        <div style="font-size: 11pt;">
        <p>AI4Discovery will email <span id="responding-client-name" /> asking them to respond to this discovery. Add instructions below, if you’d like, then click Send.</p>
        <textarea type="text" rows="20" name="notes_for_client" style="height:15em;" class="form-control" id="notes_for_client"><?= ""
        ?></textarea>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" id="client-instructions-send" data-dismiss="modal" class="btn btn-success"><i class="fa fa-share"></i> Send </button>
        <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fa fa-close"></i> Cancel </button> 
      </div>
    </div>
  </div>
</div>

<script language="javascript">
  $('#discovery-client-instructions_modal').on('show.bs.modal', _ => { 
    const name = globalThis['respondent'] && globalThis['respondent'].name;
    $('#responding-client-name').text( name || 'the client' );
  } );
</script>
