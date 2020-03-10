<?php
require_once("adminsecurity.php");
$jssettings		=	$AdminDAO->getrows("system_setting","*","pksettingid = :pksettingid", array("pksettingid"=>1));
$jssetting		=	$jssettings[0];
$jstimeformat	=	$jssetting['jstimeformat'];
$jsdateformat	=	$jssetting['jsdateformat'];
$currencyicon	=	$jssetting['currencyicon'];
$iconposition	=	$jssetting['iconposition'];
$pickerposition	=	$jssetting['pickerposition'];
$defaultfaicon	=	$jssetting['defaultfaicon'];
class GumptionAdminForm
{
	public 	$timepicker			=	0;
	public	$datetimepicker		=	0;
	public	$daterangepicker	=	0;
	public	$datetimerangepicker=	0;
	public	$datepicker			=	0;
	public	$select2			=	0;
	public	$numbersarray		=	array(10,17,19,23,24,25,26,30,39);
	public function __construct($formdata="")
	{
		
	}
	
	public function printInstructions($instructionsText)
	{
		if($instructionsText=="")
		{
			return "<br>";
		}
		return "<span class='help-block m-b-none'><i>".$instructionsText."</i></span>";
	}
	
	public function printRedStar($isRequired)
	{
		if($isRequired=="")
		{
			return "";
		}
		return "<span class='redstar' style='color:#F00' title='Required Field'>*</span>";
	}
	
	
	public function makeLabel($fieldData)
	{
		$fieldid	=	$fieldData['fieldid'];
		return "<label class='control-label col-sm-3' for='{$fieldid}'>".$fieldData['label'].$this->printRedStar($fieldData['isrequired'])." :"."</label>";
	}
	
	public function makeTextBox($fielddata)
	{
		//dump($fielddata);
		global	$defaultfaicon;
		
		$pickerclass		=	'';
		$faicon				=	$defaultfaicon;
		/*echo "<script>alert({$fielddata['fkformfieldtypeid']});</script>";*/
		switch($fielddata['fkformfieldtypeid'])
		{
			
			 case  1: //text
			 {
				$fielddata['fieldtype']			=	"text";
				$fielddata['specialnumberjs']	=	"";
				break;
			 }
			 case  2: //number
			 {
				$fielddata['fieldtype']			=	"number";
				$fielddata['specialnumberjs']	=	" onkeypress='return isNumber(event)' ";
				break;
			 }
			 case 3 : //float
			 {
				$fielddata['fieldtype']			=	"text";
				$fielddata['specialnumberjs']	=	" onkeypress='return isFloat(event)' ";
				//$fieldsarray 			=	"<input type='text' id='$fieldid' $fieldplaceholder name='$fieldname' min='$min' max='$max' step='$step' value='$fieldvalue' $specialnumberjs $fieldjs style='$fieldstyle' class='form-control $fieldtypeclass  $fieldclass'/>";
				break;
			 }
			 case  39: //email
			{
				$fielddata['fieldtype']			=	"number";
				$fielddata['specialnumberjs']	=	" onkeypress='return isNumber(event)' ";
				$faicon							=	"money";
				break;
			}
			case  9: //text Area
			{
				$fielddata['fieldtype']			=	"textarea";
				$fielddata['specialnumberjs']	=	"";
				break;
			} 
			case  10: //email
			{
				$fielddata['fieldtype']	=	"email";
				$faicon					=	"envelope";
				break;
			}
			case  11: //password
			{
				$fielddata['fieldtype']			=	"password";
				break;
			}
			case  12: //color
			{
				$fielddata['fieldtype']			=	"color";
				break;
			}
			case  13: //month
			{
				$fielddata['fieldtype']			=	"month";
				break;
			}
			case  14: //range
			{
				$fielddata['fieldtype']			=	"range";
				break;
			}
			case  15: //search
			{
				$fielddata['fieldtype']			=	"search";
				break;
			}
			case  16: //time
			{
				$fielddata['fieldtype']			=	"time";
				break;
			}
			case  17: //url
			{
				$fielddata['fieldtype']			=	"url";
				$faicon							=	"link";
				break;
			}
			case  18: //week
			{
				$fielddata['fieldtype']			=	"week";
				break;
			}
			case  19: //tel
			{
				$fielddata['fieldtype']			=	"tel";
				$faicon							=	"phone";
				break;
			}
			
			
			case  23: //time picker
			{
				
				//$fieldsarray 			=	"<input id='$fieldid' name='$fieldname' value='$fieldvalue' $specialnumberjs $fieldjs style='$fieldstyle' class='form-control $fieldtypeclass  $fieldclass' type='text'>";
				//"<input type='text'  id='$fieldid' name='$fieldname' value='$fieldvalue' $specialnumberjs $fieldjs style='$fieldstyle' class='form-control $fieldtypeclass  $fieldclass'/>";
				$pickerclass			=	"clockpicker";
				$this->timepicker		=	1;
				$faicon					=	"clock-o";
				break;
			}
			case  24: //date time picker
			{
				//$fieldsarray 			=	"<input type='text' id='$fieldid' name='$fieldname' value='$fieldvalue' $specialnumberjs $fieldjs style='$fieldstyle' class='form-control $fieldtypeclass  $fieldclass'/>";
				$pickerclass			=	"datetimepicker";
				$this->datetimepicker	=	1;
				$faicon					=	"calendar";
				break;
			}
			case  25: //Date time range Picker
			{
				//$fieldsarray 			=	"<input type='text' id='$fieldid' name='$fieldname' value='$fieldvalue' $specialnumberjs $fieldjs style='$fieldstyle' class='form-control $fieldtypeclass  $fieldclass'/>";
				$pickerclass				=	"datetimerangepicker";
				$this->datetimerangepicker	=	1;
				$faicon						=	"calendar";
				break;
			}
			case  26: //Date range Picker
			{
				//$fieldsarray 			=	"<input type='text' id='$fieldid' name='$fieldname' value='$fieldvalue' $specialnumberjs $fieldjs style='$fieldstyle' class='form-control $fieldtypeclass  $fieldclass'/>";
				//$daterangepickerclass	=	"daterangepicker";
				$pickerclass			=	"dateandrangepicker";
				$this->daterangepicker	=	1;
				$faicon					=	"calendar";
				break;
			}
			case  30: //Date picker
			{
				//$fieldsarray 			=	"<input type='text'  id='$fieldid' name='$fieldname' value='$fieldvalue' $specialnumberjs $fieldjs style='$fieldstyle' class='form-control $fieldtypeclass  $fieldclass'/>";
				$pickerclass		=	"datepicker";
				$this->datepicker	=	1;
				$faicon				=	"calendar";
				break;
			}
			default://something else
			{
				$fielddata['fieldtype']			=	"text";
				$fielddata['specialnumberjs']	=	"";
				break;
			}
		}
		$label	=	$this->makeLabel($fielddata);
		$textbox	= "{$label}
							<div class='$fieldsize col-sm-9'>
							";
		//if(in_array($fielddata['fkformfieldtypeid'],$this->numbersarray))
		{
			$textbox	.= "<div class='input-group {$pickerclass}'>";
			global	$iconposition;	
			if($iconposition == 'left')
			{
				$textbox	.= "<span class='input-group-addon'>
							<span class='fa fa-{$faicon}'></span>
														</span>
											";
			}
			
		}	
		$fieldtype	=	$fielddata['fieldtype'];
		$fieldid	=	$fielddata['fieldid'];
		$fieldname	=	$fielddata['fieldname'];
		$fieldvalue	=	$fielddata['fieldvalue'];
		$style		=	$fielddata['style'];
		$fieldtypeclass	=	$fielddata['fieldtypeclass'];
		$cssclass		=	$fielddata['cssclass'];
		$min			=	$fielddata['min'];
		$max			=	$fielddata['max'];
		$step			=	$fielddata['step'];
		$fieldvalue		=	$fielddata['fieldvalue'];
		$fieldplaceholder=	$fielddata['fieldplaceholder'];
		$textbox	.= "		<input	
								{$fielddata['specialnumberjs']} {$fielddata['fieldjavascript']} 
								type		=	'{$fieldtype}' 
								id			=	'{$fieldid}' 
								placeholder	=	'{$fieldplaceholder}'
								name		=	'{$fieldname}' 
								value		=	'{$fieldplaceholder}' 
								style		=	'{$style}' 
								class		=	'form-control {$fieldtypeclass} {$cssclass} $timepickerclass'
								min			=	'{$min}'
								max			=	'{$max}' 
								step		=	'{$step}' 
								value		=	'{$fieldvalue}'
								/>
							
							";
		//if(in_array($fielddata['fkformfieldtypeid'],$this->numbersarray))
		{
			if($iconposition == 'right')
			{
				$textbox	.= "<span class='input-group-addon'>
							<span class='fa fa-{$faicon}'></span>
														</span>
											";
			}
			$textbox	.= "</div>";
		}
									
		$textbox	.= $this->printInstructions($fielddata['instructions'])
		."</div>";
		 
		
		return $this->makeField($textbox,$fielddata['fieldid']);
	}
	/********************** LISTS ****************/
	public function makeList($fielddata,$parentfieldid)
	{
		$listitems 	=	$fielddata['listitems'];
		$options	=	'';
		$list	=	'';
		switch($fielddata['fkformfieldtypeid'])
		{
			case 34://34 Radio
			{
				foreach($listitems as $listitem)
				{
					/*$list.= "<input 
								{$fielddata['fieldjavascript']}
								type	=	'radio' 
								value	=	'{$listitem['pklistid']}' 
								id		=	'{$fielddata[fieldid]}' 
								name	=	'{$fielddata[fieldname]}' 
								style	=	'{$fielddata[style]}' 
								class	=	'{$fielddata[fieldtypeclass]} {$fielddata[cssclass]}'
								>{$listitem['listname']}";*/
					$lr++;
					$list.=" <div class='radio radio-success radio-inline'>
                                    <input type='radio' id='{$fielddata['fieldid']}_{$lr}' value='{$listitem['pklistid']}' name='{$fielddata['fieldname']}' {$fielddata['fieldjavascript']} class	=	'{$fielddata['fieldtypeclass']} {$fielddata['cssclass']}'>
                                    <label for='{$fielddata['fieldid']}_{$lr}'> {$listitem['listname']} </label>
                                </div>";			
				
				}
				break;
			}
			case 35://35 Checkobx
			{
				foreach($listitems as $listitem)
				{
					/*$list.= "<input 
								{$fielddata['fieldjavascript']}
								type	=	'checkbox' 
								value	=	'{$listitem['pklistid']}' 
								id		=	'{$fielddata[fieldid]}' 
								name	=	'{$fielddata[fieldname]}' 
								style	=	'{$fielddata[style]}' 
								class	=	'{$fielddata[fieldtypeclass]} {$fielddata[cssclass]}'
								>{$listitem['listname']}";*/
					$lc++;
					$list.=" <div class='checkbox checkbox-success checkbox-inline'>
                                    <input type='checkbox' id='{$fielddata['fieldid']}_{$lc}' value='{$listitem['pklistid']}'  name='{$fielddata['fieldname']}' {$fielddata['fieldjavascript']} class	=	'{$fielddata['fieldtypeclass']} {$fielddata['cssclass']}'>
                                    <label for='{$fielddata['fieldid']}_{$lc}'> {$listitem['listname']} </label>
                                </div>
";
				}
				break;
			} 
			case 36://36 Select Multi
			{
				$this->select2		=	1;
				foreach($listitems as $listitem)
				{
					$options	.= "<option value='{$listitem['pklistid']}'>{$listitem['listname']}";
				}
				$list .= "<select 
								class='form-control select2 {$fielddata['fieldtypeclass']} {$fielddata['cssclass']}' 
								id		=	'{$fielddata['fieldid']}' 
								name	=	'{$fielddata['fieldname']}' 
								style	=	'{$fielddata['style']}'
								{$fielddata['fieldjavascript']}
								multiple=	'multiple'
							>
								{$options}
						</select>";
				break;
			}
			case 37://37 Select Single
			{
				$this->select2		=	1;
				foreach($listitems as $listitem)
				{
					$options	.= "<option value='{$listitem['pklistid']}'>{$listitem['listname']} ";
				}
				if($fielddata['loadfilename'] != '')
				{
					$onchange	=	"onchange	=	\"javascript:loadList(this.value,'{$fielddata['loadfilename']}','{$fielddata['fieldid']}',{$fielddata['fkformfieldid']})\"";
				}
				else
				{
					$onchange	=	"";
				}
				$list .= "<select 
								{$onchange}
								class='form-control select2 {$fielddata['fieldtypeclass']} {$fielddata['cssclass']}' 
								id		=	'{$fielddata['fieldid']}' 
								name	=	'{$fielddata['fieldname']}' 
								style	=	'{$fielddata['style']}' 
								{$fielddata['fieldjavascript']}
							>
								{$options}
						</select>";
				break;
			}
			case 33://37 Select Single
			{
				$this->select2		=	1;
				foreach($listitems as $listitem)
				{
					$options	.= "<option value='{$listitem['pklistid']}'>{$listitem['listname']} ";
				}
				if($fielddata['loadfilename'] != '')
				{
					$onchange	=	"onchange	=	\"javascript:loadList(this.value,'{$fielddata['loadfilename']}','{$fielddata['fieldid']}',{$fielddata['fkformfieldid']})\"";
				}
				else
				{
					$onchange	=	""; 
				}
				$list .= "<select 
								{$onchange}
								class='form-control select2 {$fielddata['fieldtypeclass']} {$fielddata['cssclass']}' 
								id		=	'{$fielddata['fieldid']}' 
								name	=	'{$fielddata['fieldname']}' 
								style	=	'{$fielddata['style']}' 
								{$fielddata['fieldjavascript']}
							>
								{$options}
						</select>";
				break;
			}
			default:
			{
				echo "Wrong data type";
				break;
			}
		}
	$label		=	$this->makeLabel($fielddata);
	$listdata	= "{$label}<div class='$fieldsize col-sm-9'>{$list}";	
	$listdata	.= $this->printInstructions($fielddata['instructions'])."</div>";
	return $this->makeField($listdata,$fielddata['fieldid'],$parentfieldid);
	
	
	
	}
	
	/********************** Text Area/Ckeditor ****************/
	public function makeTextArea($fielddata)
	{	
		$label			=	$this->makeLabel($fielddata);
		$textareabox	= 	"{$label}<div class='$fieldsize col-sm-9'>";
		$fieldjavascript=	$fielddata['fieldjavascript'];
		$fieldid		=	$fielddata['fieldid'];
		$fieldname		=	$fielddata['fieldname'];
		$style			=	$fielddata['style'];
		$fieldtypeclass	=	$fielddata['fieldtypeclass'];
		$cssclass	=	$fielddata['cssclass'];
		$textareabox	.= "<textarea 
								{$fieldjavascript} 
								id		=	'{$fieldid}' 
								name	=	'{$fieldname}' 
								style	=	'{$style}' 
								class	=	'{$fieldtypeclass} {$cssclass}' rows='4' cols='65'
								></textarea>";
		if($fielddata['fkformfieldtypeid'] == 21)// Editor
		{
			?>
            <script type="text/javascript">
				CKEDITOR.replace( '<?php echo $fielddata['fieldname']; ?>');
			</script>
            <?php	
		}								
	$textareabox	.= $this->printInstructions($fielddata['instructions'])."</div>";
	return $this->makeField($textareabox,$fielddata['fieldid']);
	}
	/********************** FileUpload ****************/
	public function makeFileUpload($fielddata)
	{
		/*$filesetting	=	$AdminDAO->getrows('tblfilesetting',"*","fkformfieldid=".$formfield['pkformfieldid']);
		if(@count($filesetting))
		{
			$filetypes		=	$AdminDAO->getrows('tblfiletype ',"extension",'pkfiletypeid IN ('.$filesetting[0]['filetype'].')');
			$exts=array();
			foreach($filetypes as $filetype)
			{
				$exts[]	=	$filetype['extension'];
			}
		}
*/		$label			=	$this->makeLabel($fielddata);
		$fileupload		= 	"{$label}<div class='$fieldsize col-sm-9'>";
		$fileupload		.= '<div id="myDropzone-1" class="dropzone"></div>';
		$fileupload		.= $this->printInstructions($fielddata['instructions'])."</div>";
		return $this->makeField($fileupload,$fielddata['fieldid']);
	} 
	
	/********************** makeField ****************/
	public function makeField($field,$fieldid,$parentfieldid="")
	{	
		if($parentfieldid!="")
		{
			$id	=	"child_div_{$parentfieldid}";	
		}
		else
		{
			$id	=	"div_{$fieldid}";	
		}
		return	"<div class='form-group' id='{$id}'>{$field}<div class='$fieldsize col-sm-2'></div></div>";	
	}
	
	
	
	function makeFields($formfields,$parentfieldid="")
	{
		global	$AdminDAO;
		foreach($formfields as $formfield)
		{
			if(in_array($formfield['fkformfieldtypeid'],array(33,34,35,36,37)))//Lists
			{
				//$AdminDAO->displayquery=1;
				$formfield['listitems'] = $AdminDAO->getrows("system_list","*"," fklisttypeid = :fklisttypeid",array(":fklisttypeid"=>$formfield['fklisttypeid']), "listname", "ASC");
				$field = $this->makeList($formfield,$parentfieldid);
			}
			else if(in_array($formfield['fkformfieldtypeid'],array(9,21)))// Text Area & CkEditor
			{
				$field = $this->makeTextArea($formfield);
			}
			else if($formfield['fkformfieldtypeid']==8 || $formfield['fkformfieldtypeid']==22 )//file upload
			{
				$field =$this->makeFileUpload($formfield);
			}
			else //all but with textboxes
			{
				$field = $this->makeTextBox($formfield);
			}
			echo $field;
			echo "<div style='margin:10px;'></div>";
		}
	}
	
}
$GAF	=	new GumptionAdminForm();