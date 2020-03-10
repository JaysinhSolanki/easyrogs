<?php
include_once("../includes/security/adminsecurity.php");
$id				=	(int)($_GET['id']);
$rmv_elements	=	trim($_POST['removeelements'],",");
$rmv_options	=	trim($_POST['removeoptions'],",");
$removeelements	=	explode(",",$rmv_elements);
$removeoptions	=	explode(",",$rmv_options);
$form_elements	=	$AdminDAO->getrows("tblfield","pkfieldid","fkformid = '$id'");
if(sizeof($form_elements)>0)
{
	foreach($form_elements as $form_element)
	{
		$original_elements[]	=	$form_element['pkfieldid'];
	}
}
//if(!empty($rmv_elements))
//{
	if(sizeof($original_elements)>0)
	{
		$elements_not_deleted	=	array_diff($original_elements,$removeelements);
		//dump(original_elements);
		//dump($elements_not_deleted);
		if(!empty($rmv_elements))
		{
			$AdminDAO->deleterows("tblfield","pkfieldid IN ($rmv_elements)");
			$AdminDAO->deleterows("tblfieldoption","fkfieldid IN ($rmv_elements)");
		}
	}
//}
$form_options	=	$AdminDAO->getrows("tblfield, tblfieldoption","pkfieldoptionid","pkfieldid = fkfieldid");
if(sizeof($form_options)>0)
{
	foreach($form_options as $form_option)
	{
		$original_options[]	=	$form_option['pkfieldoptionid'];
	}
}
if(!empty($rmv_options))
{
	if(sizeof($original_options)>0)
	{
		$options_not_deleted	=	array_diff($original_options,$removeoptions);
		$AdminDAO->deleterows("tblfieldoption","pkfieldoptionid IN ($rmv_options)");
	}
}
//dump($_POST,1);
foreach($_POST as $k => $v)
{

    if(strpos($k, 'lbl_txt_')!==false)
    {
		//echo $k;
		$lbl_values		=	explode("lbl_txt_",$k);
		//echo " nnnnn ";//
		//dump($lbl_values);
		//dump($elements_not_deleted);
		/*if(sizeof($elements_not_deleted)>0)
		{
			if(!(in_array($lbl_values[1],$elements_not_deleted)))
			{
				$elements_numbers['txt'][]	=	$lbl_values[1];
			}
		}
		else
		{
			
			$elements_numbers['txt'][]	=	$lbl_values[1];
		}*/
		$elements_numbers['txt'][]	=	$lbl_values[1];
		dump($elements_numbers);
    }
	else if(strpos($k, 'lbl_txta_') === 0)
    {
		$lbl_values					=	explode("lbl_txta_",$k);
		/*if(sizeof($elements_not_deleted)>0)
		{
			if(!(in_array($lbl_values[1],$elements_not_deleted)))
			{
				$elements_numbers['txta'][]	=	$lbl_values[1];
			}
		}
		else
		{
			$elements_numbers['txta'][]	=	$lbl_values[1];
		}*/
		$elements_numbers['txta'][]	=	$lbl_values[1];
    }
	else if(strpos($k, 'lbl_rb_') === 0)
    {
		$lbl_values					=	explode("lbl_rb_",$k);
		/*if(sizeof($elements_not_deleted)>0)
		{
			if(!(in_array($lbl_values[1],$elements_not_deleted)))
			{
				$elements_numbers['rb'][]	=	$lbl_values[1];
			}
		}
		else
		{
			$elements_numbers['rb'][]	=	$lbl_values[1];
		}*/
		$elements_numbers['rb'][]	=	$lbl_values[1];
    }
	else if(strpos($k, 'lbl_chk_') === 0)
    {
		$lbl_values					=	explode("lbl_chk_",$k);
		/*if(sizeof($elements_not_deleted)>0)
		{
			if(!(in_array($lbl_values[1],$elements_not_deleted)))
			{
				$elements_numbers['chk'][]	=	$lbl_values[1];
			}
		}
		else
		{
			$elements_numbers['chk'][]	=	$lbl_values[1];
		}*/
		$elements_numbers['chk'][]	=	$lbl_values[1];
    }
	else if(strpos($k, 'lbl_drp_') === 0)
    {
		$lbl_values					=	explode("lbl_drp_",$k);
		/*if(sizeof($elements_not_deleted)>0)
		{
			if(!(in_array($lbl_values[1],$elements_not_deleted)))
			{
				$elements_numbers['drp'][]	=	$lbl_values[1];
			}
		}
		else
		{
			$elements_numbers['drp'][]	=	$lbl_values[1];
		}*/
		$elements_numbers['drp'][]	=	$lbl_values[1];
    }
	else
	{
		// something here!
	}
}
//dump($elements_numbers,1);

$fields	=	array("label","fkfieldtypeid","isrequired","sortorder","hasoptions","fkformid");
if(sizeof($elements_numbers)>0)
{
	foreach($elements_numbers as $type => $values)
	{
		switch($type)
		{
			case "txt" :
				foreach($values as $value)
				{
					$label			=	$_POST['lbl_txt_'.$value];
					$required		=	$_POST['req_txt_'.$value];
					$sortorder		=	$_POST['sortorder_txt_'.$value];
					$data			=	array($label,1,$required,$sortorder,0,$id);
					if(!(@in_array($value,$elements_not_deleted)))//new elements
					{
						$AdminDAO->insertrow('tblfield',$fields,$data);
					}
					else
					{
						$AdminDAO->updaterow('tblfield',$fields,$data,"pkfieldid = '$value'");
					}
				}
				break;
			case "txta" :
				foreach($values as $value)
				{
					$label			=	$_POST['lbl_txta_'.$value];
					$required		=	$_POST['req_txta_'.$value];
					$sortorder		=	$_POST['sortorder_txta_'.$value];
					$data			=	array($label,2,$required,$sortorder,0,$id);
					if(!(sizeof($elements_not_deleted)>0))
					{
						if(!(in_array($value,$elements_not_deleted)))
						{
							$AdminDAO->insertrow('tblfield',$fields,$data);
						}
					}
					else
					{
						$AdminDAO->updaterow('tblfield',$fields,$data,"pkfieldid = '$value'");
					}
				}
				break;
			case "rb" :
				foreach($values as $value)
				{
					$label			=	$_POST['lbl_rb_'.$value];
					$required		=	$_POST['req_rb_'.$value];
					$sortorder		=	$_POST['sortorder_rb_'.$value];
					$data			=	array($label,3,$required,$sortorder,1,$id);
					$fields_options	=	array("label","fkfieldid");
					if($id == -1)
					{
						// new elements
						$fieldid	=	$AdminDAO->insertrow('tblfield',$fields,$data);
						$optionvalues	=	$_POST['option_rb_'.$value];
						if(sizeof($optionvalues)>0)
						{
							foreach($optionvalues as $optionvalue)
							{
								if(sizeof($options_not_deleted)>0)
								{
									if(!in_array($optionvalue,$options_not_deleted))
									{
										$data_options	=	array($optionvalue,$fieldid);
										$AdminDAO->insertrow('tblfieldoption',$fields_options,$data_options);
									}
								}
								else
								{
									$data_options	=	array($optionvalue,$fieldid);
									$AdminDAO->insertrow('tblfieldoption',$fields_options,$data_options);
								}
							}
						}
					}
					else
					{
						// existing elements
						// check if the form has any field
						if(sizeof($form_elements)>0)
						{
							$AdminDAO->updaterow('tblfield',$fields,$data,"pkfieldid = '$value'");
							$optionvalues	=	$_POST['option_rb_'.$value];
							$field_options	=	$AdminDAO->getrows("tblfield, tblfieldoption","pkfieldoptionid","pkfieldid = fkfieldid AND pkfieldid = '$value'");
							if(sizeof($optionvalues)>0)
							{
								foreach($optionvalues as $optionvalue)
								{
									if(sizeof($field_options)>0)
									{
										if(in_array($value,$original_options))
										{
											$data_options	=	array($optionvalue,$value);
											$AdminDAO->insertrow('tblfieldoption',$fields_options,$data_options);
										}
										else
										{
											$data_options	=	array($optionvalue,$value);
											$AdminDAO->updaterow('tblfieldoption',$fields_options,$data_options,"pkfieldoptionid = '$value'");
										}
									}
									else
									{
										$data_options	=	array($optionvalue,$value);
										$AdminDAO->insertrow('tblfieldoption',$fields_options,$data_options);
									}
								}
							}
						}
						else
						{
							$fieldid		=	$AdminDAO->insertrow('tblfield',$fields,$data);
							$optionvalues	=	$_POST['option_rb_'.$value];
							if(sizeof($optionvalues)>0)
							{
								foreach($optionvalues as $optionvalue)
								{
									$data_options	=	array($optionvalue,$fieldid);
									$AdminDAO->insertrow('tblfieldoption',$fields_options,$data_options);
								}
							}
						}
					}
				}
				break;
			case "chk" :
				foreach($values as $value)
				{
					$label			=	$_POST['lbl_chk_'.$value];
					$required		=	$_POST['req_chk_'.$value];
					$sortorder		=	$_POST['sortorder_chk_'.$value];
					$data			=	array($label,4,$required,$sortorder,1,$id);
					if(!(sizeof($elements_not_deleted)>0))
					{
						if(!(in_array($value,$elements_not_deleted)))
						{
							$AdminDAO->insertrow('tblfield',$fields,$data);
						}
					}
					else
					{
						$AdminDAO->updaterow('tblfield',$fields,$data,"pkfieldid = '$value'");
					}
					$fields_options	=	array("label","fkfieldid");
					$optionvalues	=	$_POST['option_chk_'.$value];
					if(sizeof($optionvalues)>0)
					{
						foreach($optionvalues as $optionvalue)
						{
							if(sizeof($options_not_deleted)>0)
							{
								if(!in_array($optionvalue,$options_not_deleted))
								{
									$data_options	=	array($optionvalue,$fieldid);
									$AdminDAO->insertrow('tblfieldoption',$fields_options,$data_options);
								}
							}
							else
							{
								$data_options	=	array($optionvalue,$fieldid);
								$AdminDAO->insertrow('tblfieldoption',$fields_options,$data_options);
							}
						}
					}
				}
				break;
			case "drp" :
				foreach($values as $value)
				{
					$label			=	$_POST['lbl_drp_'.$value];
					$required		=	$_POST['req_drp_'.$value];
					$sortorder		=	$_POST['sortorder_drp_'.$value];
					$data			=	array($label,5,$required,$sortorder,1,$id);
					$fieldid		=	$AdminDAO->insertrow('tblfield',$fields,$data);
					$fields_options	=	array("label","fkfieldid");
					$optionvalues	=	$_POST['option_drp_'.$value];
					if(sizeof($optionvalues)>0)
					{
						foreach($optionvalues as $optionvalue)
						{
							if(sizeof($options_not_deleted)>0)
							{
								if(!in_array($optionvalue,$options_not_deleted))
								{
									$data_options	=	array($optionvalue,$fieldid);
									$AdminDAO->insertrow('tblfieldoption',$fields_options,$data_options);
								}
							}
							else
							{
								$data_options	=	array($optionvalue,$fieldid);
								$AdminDAO->insertrow('tblfieldoption',$fields_options,$data_options);
							}
						}
					}
				}
				break;
			default :
				break;
			// do something here
		}
	}
}
//dump($elements_numbers);
//dump($_POST);
exit;
if(sizeof($_POST)>0)
{
	$id				=	(int)($_GET['id']);
	/********************ADDRESSBOOK DATA****************************************/
	$title			=	$_POST['title'];
	$subject		=	$_POST['subject'];
	$body			=	htmlentities($_POST['templatebody'],ENT_QUOTES);
	$status			=	$_POST['status'];
	$type			=	$_POST['type'];
	$fromname		=	$_POST['fromname'];
	$fromemail		=	$_POST['fromemail'];
		
	/*************************************Validation*******************************/
	validate($title,"Email Template Title");
	validate($subject,"Email Template Subject");
	/******************************************************************************/
	/*if(sizeof($togroups)==0 && sizeof($tousers)==0 && $toindividuals=="")
	{
		echo "Please select at least one recipient";
		exit;
	}*/
	/******************************************************************************/
	$fields						=	array('templatetitle','templatesubject','templatebody','templatestatus','templatetype','fromname','fromemail');
	$data						=	array($title,$subject,$body,$status,$type,$fromname,$fromemail);
	if($id!=-1)
	{
		$AdminDAO->updaterow('tblemailtemplate',$fields,$data,"pkemailtemplateid = '$id' AND fkcompanyid = '1'");
	}
	else
	{
		$id			=	$AdminDAO->insertrow('tblemailtemplate',$fields,$data);
	}
}
exit;
?>