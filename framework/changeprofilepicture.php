<?php
@session_start();
require_once("adminsecurity.php");
$languagelabels		=   getlabel(42);
$sessiongroupid		=	$_SESSION['groupid'];
$ab_id				=	$_SESSION['addressbookid'];
$logincheckuser		=	$AdminDAO->getrows('system_addressbook,system_groups',"pkaddressbookid","fkgroupid	=	3 AND pkaddressbookid = '$ab_id'");
$issuperadmin		=	$logincheckuser[0]['pkaddressbookid'];
$name				=	$_SESSION['name'];
$id					=	$_GET['id'];
if(!$id)
{
	$id	=	$_SESSION['addressbookid'];
}
/****************************************************************************/
$users			=	$AdminDAO->getrows("system_addressbook","*","pkaddressbookid	=	'$addressbookid'");
$user			=	$users[0];
$userimage		=	$user['userimage'];	
?>
<script src="dist/jquery.cropit.js"></script>
<script type="text/javascript">
function uploadprofileimage()
{
	 var imageData = $('.image-editor').cropit('export');
	 
	 $.post( "changeprofilepictureaction.php", { idata: imageData})
		  .done(function( data ) {
			  msg(data);
			  var parsed = JSON.parse(data);
			 if(parsed.messagetype == 2)
			 {
			  setTimeout(function(){
				 location.reload();
				}, 2000);
			 }
			 
		  });
	  var formValue = $(this).serialize();
}
$(function() 
{
	$('.image-editor').cropit();
	$('html, body').animate({ scrollTop: 0 }, 0);
});
  </script>
    
<style>
      .cropit-preview {
        background-color: #f8f8f8;
        background-size: cover;
        border: 1px solid #ccc;
        border-radius: 3px;
        margin-top: 7px;
        width: 100px;
        height: 100px;
      }

      .cropit-preview-image-container {
        cursor: move;
      }

      .image-size-label {
        margin-top: 10px;
      }

      input {
        display: block;
      }

      button[type="submit"] {
        margin-top: 10px;
      }

      #result {
        margin-top: 10px;
        width: 900px;
      }

      #result-data {
        display: block;
        overflow: hidden;
        white-space: nowrap;
        text-overflow: ellipsis;
        word-wrap: break-word;
      }
    </style>
<div id="loaditemscript"> </div>
<div id="screenfrmdiv" style="display: block;">

<div class="col-lg-12">
    <div class="hpanel">
        <div class="panel-heading">
            
            <h4 class="page-headding">
			<?php 
			echo "Edit Profile Picture: $firstname $lastname";
			?>	
            </h4>
        </div>
        <div class="panel-body">   
        <div class="form-group">
	<label class="col-sm-2 control-label">Upload Picture <span class="redstar" style="color:#F00" title="This field is compulsory">*</span></label>
	<div class="col-sm-8">
      <div class="image-editor">
        <input type="file" class="cropit-image-input">
        <?php /*?>  <img src="uploads/profile/<?php echo $userimage;?>" alt="logo" width="76px"><?php */?>
        <div class="cropit-preview">
        </div>
        <div class="image-size-label">
          Resize image
        </div>
        <input type="range" class="cropit-image-zoom-input" style="width:15% !important;">
        
        <button type="button" onClick="uploadprofileimage()" class="btn btn-success">Upload</button>
      </div>
      <div id="successmsg"></div>
    </div>
</div>
        </div>
    </div>
</div>
</div>