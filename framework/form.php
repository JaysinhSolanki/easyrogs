<?php

include_once("../includes/security/adminsecurity.php");

$qs		=	$_SESSION['qstring'];

$id		=   $_REQUEST['id'];

if($id!=-1)

{

	$rows 	=	$AdminDAO->getrows("tblreviewtemplate","*","pktemplateid = '$id'");

	$row	=	$rows[0];

	/**************************************feedback form data*************************/

	$title			=	$row['title'];

	$fkcourseid		=	$row['fkcourseid'];

}

/*echo "<pre>";

print_r($rows);

exit;*/

/********************************************Courses***********************************/

$courses			=	$AdminDAO->getrows('tblcourse','*',"",'coursename', 'ASC');

/********************************************Field Types***********************************/

$fieldtypes			=	$AdminDAO->getrows('tblfieldtype','*',"",'pkfieldtypeid', 'ASC');

/*****************************************************************************************/

?>

<script type="text/javascript">

function insertfield(fieldid,hasoptions)

{

	//alert("Field ID: "+fieldid);

	$('#no_element').remove();

	var totalElements = $('.element').length;

	$.post("element.php",{fieldid:fieldid,hasoptions:hasoptions,sortorder:totalElements+1},function(data){

		$('#form_elements').prepend(data);

	});

}

function remove_element(id)

{

	if(confirm("Are you sure to remove form element?")==true)

	{

		var cur_val = $('#removeelements').val();

		if(cur_val!="")

		  $('#removeelements').val(cur_val + "," + id);

		else

		  $('#removeelements').val(id);

		$('#tr_'+id).remove(); // remove <tr> element

	}

}

function add_option(id,type)

{

	$.post("addoption.php",{id:id,type:type},function(data){

		$('#table_'+id).append(data);

	});

}

function remove_option(id,type)

{

	if(confirm("Are you sure to remove option?")==true)

	{

		var cur_val = $('#removeoptions').val();

		if(cur_val)

		  $('#removeoptions').val(cur_val + "," + id);

		else

		  $('#removeoptions').val(id);

		$('#tr_'+type+'_'+id).remove(); // remove <tr> element

	}

}

</script>
<script src="js/jquery.form.js"></script>

<div id="error" class="notice" style="display:none"></div>

<div id="accountdiv">

<form name="frmfeedback" id="frmfeedback" style="width:920px;" class="form">

<input type="hidden" name="id" value="<?php echo $id?>" />

<input type="hidden" id="removeelements" name="removeelements" value=""/>

<input type="hidden" id="removeoptions" name="removeoptions" value=""/>

<fieldset>

<legend>

<?php 

if($id=='-1')

{echo "Add Feedback Form";}

else

{echo "Edit Feedback Form";}

?>

</legend>

<?php buttons("formaction.php?id=$id","frmfeedback","maindiv","forms.php",'1');?>

<table width="100%" cellpadding="5" cellspacing="0">

    <tr>

        <td>Title</td>

        <td><input type="text" name="title" id="title" value="<?php echo $title;?>" /></td>

    </tr>

    <tr>

        <td>Course</td>

        <td>

        	<select name="fkcourseid" data-placeholder="Select Course"  class="chzn-select">

                <?php

                    for($i=0;$i<sizeof($courses);$i++)

                    {

                ?>

						<option <?php if($fkcourseid == $courses[$i]['pkcourseid']) {echo "SELECTED=SELECTED";} ?>  value="<?php echo $courses[$i]['pkcourseid'];?>"><?php echo $courses[$i]['coursename'];?></option>

                <?php

                    }//for

                ?>

            </select>

        </td>

    </tr>

    <tr>

    	<td colspan="2">

        	<table width="51%">

                <tr style="background:#9ECEEB;">

                    <td colspan="2"><strong>Fields</strong></td>

                </tr>

                <tr>

                    <td style="border:1px solid #CCC; vertical-align:top;" width="25%">

                        <table>

                            <?php

                                if(sizeof($fieldtypes)>0)

                                {

                                    foreach($fieldtypes as $fieldtype)

                                    {

                                        ?>

                                        <tr>

                                            <td>

                                                <a href="javascript:;" onclick="insertfield('<?php echo $fieldtype['pkfieldtypeid'];?>','<?php echo $fieldtype['hasoptions'];?>')" style="color:#4285F4 !important;"><?php echo $fieldtype['fieldtypename'];?></a>

                                            </td>

                                        </tr>

                                        <?php

                                    }

                                }

                            ?>

                        </table>

                    </td>

                    <?php

					if($id!=-1)

					{

					?>

                        <td style="border:1px solid #CCC; vertical-align:text-top;" width="75%">

							<table id="form_elements" width="50%">

								<?php

									include_once("editelement.php");

								?>

							</table>

						</td>

					<?php

					}

					else

					{

					?>

                    	<td style="border:1px solid #CCC; vertical-align:text-top;" width="75%">

                            <table id="form_elements" width="50%">

                                &nbsp;

                            </table>

                        </td>

                    <?php

					}

					?>

                </tr>

			</table>

		</td>

    <tr>

        <td colspan="2" align="center">

			<?php buttons("formaction.php?id=$id","frmfeedback","maindiv","forms.php",'0');?>

        </td>

	</tr>

</table>

</fieldset>

</form>

</div>

<script language="javascript">

loading('Loading...');

$(".chzn-select").chosen();

$(".chzn-select-deselect").chosen({allow_single_deselect: true});

</script>

<br />