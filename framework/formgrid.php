<?php
require_once("formclass.php");
$forms		=	$AdminDAO->getrows("system_form","*","pkformid=:pkformid",array('pkformid'=>3));
$form		=	$forms[0];
$pkformid	=	$form['pkformid'];
//$formfields	=	$AdminDAO->getrows("system_formfield","*","fkformid	=	'$pkformid'", "displayorder", "ASC");
$formfields	=	$AdminDAO->getrows('system_formfield',"*","fkformid= :fkformid", array(":fkformid"=>$pkformid), "displayorder", "ASC" );
//dump($formfields);

?>
<style>
.help-block{margin-bottom:20px !important;}
</style>
<script>
Dropzone.autoDiscover = false;
$(document).ready(function() {
$('.dropzone').each(function(){
	//alert();
    var options = $(this).attr("id").split("-");
    var dropUrl = "fileuploadaction.php?files="+options[1];
    var dropMaxFiles = parseInt(options[2]);
    var dropParamName = "file" + options[1];
    var dropMaxFileSize = parseInt(options[3]);
    $(this).dropzone({
						url: dropUrl,
						paramName: "file",
						autoProcessQueue: true,
						uploadMultiple: true, // uplaod files in a single request
						parallelUploads: 100, // use it with uploadMultiple
						maxFilesize: 3, // MB
						maxFiles: 10,
						acceptedFiles: ".jpg, .jpeg, .png, .gif, .pdf",
						addRemoveLinks: true,
						// Language Strings
						dictFileTooBig: "File is to big ({{filesize}}mb). Max allowed file size is {{maxFilesize}}mb",
						dictInvalidFileType: "Invalid File Type",
						dictCancelUpload: "Cancel",
						dictRemoveFile: "Remove",
						dictMaxFilesExceeded: "Only {{maxFiles}} files are allowed",
						dictDefaultMessage: "Drop files here to upload",
		
        // Rest of the configuration equal to all dropzones
    });

});
});
</script>
<div class="content animate-panel">
<div class="container">
<div class="row">
    <div class="col-lg-12" style="">
        <div class="hpanel">
            <div class="panel-body">
                <h3>Forms TESTING</h3>
                <p>Individual form controls automatically receive some global styling. All textual <code>&lt;input&gt;</code>, <code>&lt;textarea&gt;</code>, and <code>&lt;select&gt;</code> elements with <code>.form-control</code> are set to <code>width: 100%;</code> by default. Wrap labels and controls in <code>.form-group</code> for optimum spacing.</p>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="hpanel">
            <div class="panel-heading"><?php echo $form['formtitle'];?></div>
            <div class="panel-body">
                    <form  style=" <?php echo $form['cssinline'];?>"  class="form-horizontal <?php echo $form['cssclass'];?>" name="<?php echo $form['formname'];?>" id="<?php echo $form['formid'];?>" method="<?php if($form['formname']==1){ echo "post";}else { echo "get"; } ?>" action="<?php echo $form['formaction']."?".$form['querystring'];?>" enctype="<?php if($form['enctype']==1){ echo "application/x-www-form-urlencoded";}elseif($form['enctype']==2) { echo "multipart/form-data"; }else{echo "text/plain";} ?>" >
                    <?php
					$GAF->makeFields($formfields);
                    
                    //dump($fielddata);
                    ?>
                    </form>
              
            </div>
        </div>
    </div>    
</div>
</div>
</div>
<?php
require_once("formgridjs.php");
?>