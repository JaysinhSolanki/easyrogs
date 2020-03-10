<?php 
require_once("adminsecurity.php");
$_SESSION['fieldid']=1;
@session_start();
if($_GET['id'])
{
	$pkformid	=	$_GET['id'];
	//$formfields			=	$AdminDAO->getrows("system_formfield","pkformfieldid","fkformid	=	:pkformid", array(":pkformid"=>$pkformid));
	$forms	=	$AdminDAO->getrows('system_form',"*","pkformid= :pkformid", array(":pkformid"=>$pkformid));
	$form	=	$forms[0];
}
?>
    <div class="panel-body">
        <div class="hpanel">
            <div class="panel-body">
                    <div class="col-lg-12" style="margin-top:10px;">
                    <form  name="createform" id="createform"  class="form">	
                    	<div class="hpanel">
                        <div class="panel-heading hbuilt" style="background-color:#F7F9FA !important;">Create form </div>
                        <input type="hidden" placeholder="" class="form-control m-b"  name="pkformid" id="pkformid" value="<?php echo  $form['pkformid'] ?>">
                        <div class="panel-body" style="">
                            <div class="form-group">
                                <label class="col-sm-2 control-label">Form Title<span class='redstar' style='color:#F00' title='This field is compulsory'>*</span></label>
                                <div class="col-sm-4">
                                    <input type="text" placeholder="Title" class="form-control m-b"  name="formtitle" id="formtitle" value="<?php echo  $form['formtitle'] ?>">
                                </div>
                                <label class="col-sm-2 control-label">Form Name<span class='redstar' style='color:#F00' title='This field is compulsory'>*</span></label>
                                <div class="col-sm-4">
                                    <input type="text" placeholder="Name" class="form-control m-b"  name="formname" id="formname" value="<?php echo  $form['formname'] ?>">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">Form ID<span class='redstar' style='color:#F00' title='This field is compulsory'>*</span></label>
                                <div class="col-sm-4">
                                    <input type="text" placeholder="Id" class="form-control m-b"  name="formid" id="formid" value="<?php echo  $form['formid'] ?>">
                                </div>
                                <label class="col-sm-2 control-label">Form Action<span class='redstar' style='color:#F00' title='This field is compulsory'>*</span></label>
                                <div class="col-sm-4">
                                    <input type="text" placeholder="Action" class="form-control m-b"  name="formaction" id="formaction" value="<?php echo  $form['formaction'] ?>">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">Form Method</label>
                                <div class="col-sm-4">
                                    <select placeholder="Method" class="form-control m-b"  name="formmethod" id="formmethod" >
                                        <option <?php if($form['method']==1) echo 'selected="selected"'; ?>   value="1">POST</option>
                                        <option <?php if($form['method']==2) echo 'selected="selected"'; ?>value="2">GET</option>
                                    </select>
                                </div>
                                <label class="col-sm-2 control-label">Enctype</label>
                                <div class="col-sm-4">
                                    <select placeholder="Enctype" class="form-control m-b"  name="formenctype" id="formenctype" >
                                        <option <?php if($form['enctype']==2 || $form['enctype']=="") echo 'selected="selected"'; ?> value="2">multipart/form-data</option>
                                        <option <?php if($form['enctype']==1) echo 'selected="selected"'; ?> value="1">application/x-www-form-urlencoded</option>
                                        
                                        <option <?php if($form['enctype']==3) echo 'selected="selected"'; ?> value="3">text/plain</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">Query string</label>
                                <div class="col-sm-4">
                                    <input type="text" placeholder="Query string" class="form-control m-b"  name="formquerystring" id="formquerystring" value="<?php echo  $form['querystring'] ?>">
                                </div>
                                <label class="col-sm-2 control-label">Redirect URL</label>
                                <div class="col-sm-4">
                                    <input type="text" placeholder="Redirect url" class="form-control m-b"  name="formredirecturl" id="formredirecturl" value="<?php echo  $form['redirecturl'] ?>">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">Javascript</label>
                                <div class="col-sm-4">
                                    <textarea class="form-control m-b"  placeholder="Javascript"  name="formjs" id="formjs" ><?php echo  $form['javascript'] ?></textarea>
                                </div>
                                <label class="col-sm-2 control-label">Inline CSS</label>
                                <div class="col-sm-4">
                                    <textarea  placeholder="Inline CSS " class="form-control m-b"  name="forminlinecss" id="forminlinecss" value=""><?php echo  $form['cssinline'] ?></textarea>
                                </div>
                            </div>
                            <div class="form-group ">
                                <label class="col-sm-2 control-label">CSS class</label>
                                <div class="col-sm-4">
                                    <textarea type="text" placeholder="CSS class" class="form-control m-b"  name="formcssclass" id="formcssclass" ><?php echo  $form['cssclass'] ?></textarea>
                                </div>
                                <label class="col-sm-2 control-label">Grid style</label>
                                <div class="col-sm-4">
                                     <select placeholder="Enctype" class="form-control m-b"  name="gridstyle" id="gridstyle" >
                                        <option <?php if($form['gridstyle']==1 || $form['gridstyle']=="") echo 'selected="selected"'; ?> value="1">One field on full Page</option>
                                        <option <?php if($form['gridstyle']==2) echo 'selected="selected"'; ?> value="2">Two fields on full Page</option>
                                        <option <?php if($form['gridstyle']==3) echo 'selected="selected"'; ?> value="3">Three fields on full Page</option>
                                        <option <?php if($form['gridstyle']==4) echo 'selected="selected"'; ?> value="4">One field on center Page</option>
                                    </select>
                                 </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">Is AJAX?</label>
                                <div class="col-sm-4">
                                    <label class="radio-inline">
                                        <input type="radio" value="1" <?php if($form['isajax']==1) echo 'checked="checked"'; ?> checked="checked" name="isajax">Yes
                                    </label>
                                    <label class="radio-inline">
                                        <input type="radio" value="0" <?php if($form['isajax']==2) echo 'checked="checked"'; ?> name="isajax">No
                                    </label>
                                </div>
                            </div>  
                            <div class="col-sm-6" style="text-align:right; vertical-align:bottom">
                                   <a onclick="loadmorefield()"><span class="glyphicon glyphicon-plus"></span> load fields</a>
                            </div>   
                        </div>
                        <div class="panel-footer" id="sectionfooter<?php echo $formfield['pkformfieldid'];?>">
                        	Form End
                        </div>
                        <div class="loadmore" id="loadmore" style="margin-top:25px;"></div><!----Load Form field---->
                         <div class="col-sm-12" style="margin-top:5px;">
                         <script src="js/jquery.form.js"></script>
                       <?php
                            buttons('createformaction.php','createform','maindiv','createform.php?pkscreenid=139',0)
                        ?>
                    </div>
                    </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
<?php
if($_GET['id'])
{
	$pkformid	=	$_GET['id'];
	//$formfields	=	$AdminDAO->getrows("system_formfield","pkformfieldid"," fkformid = '$pkformid'",  "displayorder", "ASC");
	$formfields			=	$AdminDAO->getrows("system_formfield","pkformfieldid","fkformid	=	:pkformid", array(":pkformid"=>$pkformid));
	foreach($formfields as $formfield)
	{
		?>
			<script>
			setTimeout(function(){
            loadmorefieldedit(<?php echo  $formfield['pkformfieldid']; ?>);
			}, 200);
            </script>
        <?php
		//sleep(2);
	}
}
?>
<script>
function Dependson(id,uid)
{
	if(id==1)
	{
		$(".dependent_"+uid).show('slow');
		loadallfields(id)
	}
	else
	{
		$(".dependent_"+uid).hide('slow');
	}
	
}
function loadallfields(id)
{
	var fieldoptions	=	''
	$(".allfields").each(function() 
	{
		$("#dependentfield"+id).html('');
    	var getthis	=	$(this);
		if(getthis.val()!= id)
		{
			fieldoptions	+=	"<option value='"+getthis.val()+"'>"+$("#fieldtitle"+getthis.val()).text()+"</option>";
		}
		//alert(getthis.val());
    });
	alert(fieldoptions);
	$("#dependentfield"+id).append(fieldoptions);
}

</script>