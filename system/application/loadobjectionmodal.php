<?php
@session_start();
require_once("adminsecurity.php");
$discovery_question_id	=	$_POST['discovery_question_id'];
$questiondetails		=	$AdminDAO->getrows('discovery_questions as dq,questions as q',"dq.id,dq.discovery_id,dq.question_id,q.question_title,q.form_id,dq.answer,dq.answer_detail,dq.objection","q.id = dq.question_id AND dq.id = '$discovery_question_id'");

?>
<form class="form-horizontal" name="objectionform" id="objectionform">
<input type="hidden" id="discovery_question_id" name="discovery_question_id" value="<?php echo $discovery_question_id ?>" />
<div class="modal-body">
     <div class="form-group">
        <label class="control-label col-sm-2">Question:</label>
        <div class="col-sm-10">
        	<label class="control-label" style="font-weight:400 !important; text-align:left"><?php echo $questiondetails[0]['question_title']; ?></label>
        </div>
    </div>
     <div class="form-group">
        <label class="control-label col-sm-2">Answer:</label>
        <div class="col-sm-10">
        	<label class="control-label" style="font-weight:400 !important"><?php echo $questiondetails[0]['answer']; ?></label>
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-sm-2" for="email">Objection:</label>
        <div class="col-sm-10">
        	<textarea id="objection" class="form-control " name="objection" placeholder="Objection" ><?php echo $questiondetails[0]['objection']; ?></textarea>
        </div>
    </div>
</div>
<div class="modal-footer">
	<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
	<button type="button" class="btn btn-primary" onclick="saveObjectionFunction()">Save</button>
</div>
</form> 

                   