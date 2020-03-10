<?php
//echo "<br>------------<br>";
//echo __FILE__;
//echo __LINE__;
//ini_set("display_errors",0);
//echo "{$_SESSION['includes_path']}classes/adminsecurity.php";
require_once("adminsecurity.php"); 
/********************* function grid ******************************/
/* accepts parameters
	1. $labels 		=	Field Labels 
	2. $fields 		=	Database Fields
	3. $query			=	Database Query
	4. $limit 		=	Paging Limit
	5. $navbtn 		=	Button Image or Text values are a = add; e = edit; d=delete; custom= custom value
	6. $jsrc		=	JavaScript file path(s)
	7. $dest		= 	Destination Page
	8. $div			= 	Div of the grid
	9. $css 		=	Grid CSS path
	10.	$form		=	Name of the form
	11. $type		=	
	12. $optionsarray	=	
	13. $sortorder	=	Field name and field order e.g. brandname DESC
************ Custom work for this project**********************
	11. limit -1 hides the paging navigation
	12. $optionsarray if this is set then it will diplay the attributeoption names with productname on the basis of productattributeoptionname field name
	13. if some data have ('.jpg','.jpeg','.gif','.bmp','.png','.GIF','.JPG','.JPEG') image extentions this will add the image src tag and displays the image 
	14. if finds the field name with (description or comments) will add the view more option and enables the lightbox control to view details
*******************************************************************/
/*function advanceserach($searchOper,$searchField,$searchString,$compound)
{
	//print"I am in advance search call";
	//print"$searchField --- $searchOper ----$searchString <br>";
	//$searchf	=	$searchField;
	//echo '<br> found: '.$searchOper;
	//break;
	//$searchOper)
	//{
	if($searchOper=='bw')
	{
		return(" LIKE '$searchString". "%' ");
		//return($searchOper);
		//break;
	}
	if($searchOper=='eq')
	{
		return(" = '$searchString'");
		//return($searchOper);
		//	break;
	}
	if($searchOper=='neq')
	{
		return(" <> '$searchString'");
		//return($searchOper);
		//break;
	}
	if($searchOper=='lt')
	{
		return(" < '$searchString'");
		//return($searchOper);
		//break;
	}
	if($searchOper=='le')
	{
		return(" <= '$searchString'");
		//return($searchOper);
		break;
	}
	if($searchOper=='gt')
	{
		return(" > '$searchString'");
		//return($searchOper);
		//break;
	}
	if($searchOper=='ge')
	{
		return(" >= '$searchString'");
		//return($searchOper);
		//break;
	}
	if($searchOper=='ew')
	{
		return("LIKE '%"."$searchString'");
		//return($searchOper);
		//break;
	}
	if($searchOper=='cn')
	{
		$searchq		=	strip_tags($searchString);
		$list			=	explode(' ',$searchq);
		foreach($list as $val)// preparing the search condition
		{
			$condition.="%$val%";
		}
		$condition	=	str_replace('%%','%',$condition);
		return(" LIKE '$condition' ");
		//return($searchOper);
		//break;
	}
	//print"I am here";
	//}
}// end of advancesearch*/
function grid($labels='',$fields='',$query='',$limit=10,$navbtn='',$jsrc='',$dest='', $div='', $css='', $form='frm1',$type='',$optionsarray='',$sortorder='',$customfilters = '')
{
	//dump($fields);
	//dump($labels);
	$fields	=	array_values($fields);
	$labels	=	array_values($labels);

	global $sectionname, $screenname, $formurl, $actiontypesarr,$screenid,$filters;
	$_SESSION['qstring'][$screenid]	=	 $_SERVER['QUERY_STRING'];
	//print_r($actiontypesarr);
	$replacestr	=	array("manage",".php");
	$printtitle	=	strtoupper(str_replace($replacestr,'',$dest));
	//$navbtn.=printaction($printtitle);
	$navbtn.="<a href='printgrid.php'  class='DTTT_button DTTT_button_print' target='_blank'></a>";
	/*	$navbtn.="&nbsp;|&nbsp;
        <a href='exportexcel.php?filename=$printtitle'>
            <img src='../images/file_excel.png' height='14' width='16' border='0'>
        </a>
        ";*/
	global $AdminDAO,$Paging,$qs;
	//echo $dbname_detail;
	// $_SESSION['qstring']	= $_SERVER['QUERY_STRING'];
	//****************search***********************
	$searchString	=	trim(stripslashes(filter($_REQUEST['searchString'])));
	$searchField 	=	trim(stripslashes(filter($_REQUEST['searchField'])));
	$search 		=	trim(stripslashes(filter($_REQUEST['_search'])));
	$searchOper	 	=	trim(stripslashes(filter($_REQUEST['searchOper'])));
	$searchoperator	=	trim(stripslashes(filter($_REQUEST['searchOper'])));
	$page			=	trim(stripslashes(filter($_REQUEST['page'])));
	$pagelimit		=	trim(stripslashes(filter($_REQUEST['pagelimit'])));
	
	if(strpos($query,$_GET['field'])!=false )
	{
		$sort_index		=	trim(stripslashes(filter($_REQUEST['field'])));
		$sort_order		=	trim(stripslashes(filter($_REQUEST['order'])));
	}
	$id				=	trim(stripslashes(filter($_REQUEST['id'])));
	$param			=	trim(stripslashes(filter($_REQUEST['param'])));
	if($search=='true')
	{
		if($searchString=='')
		{
			$msg="<li>Please Enter the text to search.</li>";
		}
	}
	if($searchString != '')
	{
		$qs		=	"_search=true&searchField=$searchField&searchOper=$searchOper&searchString=$searchString";
	}
	if ($id !="")
	{
		$qs	.="&id=$id";
	}
	if ($param != '')
	{
		$qs.="&param=$param";
	}
	
	$condition		=	"";
	if($search!='false')
	{
		if($searchString!="")
		{
			switch($searchOper)
			{
				case 'bw':
				{
					$searchOper	=	" LIKE '$searchString". "%' ";
					break;
				}
				case 'eq':
				{
					$searchOper	=	" = '$searchString'";
					break;
				}
				case 'ne':
				{
					$searchOper	=	" <> '$searchString'";
					break;
				}
				case 'lt':
				{
					$searchOper	=	" < '$searchString'";
					break;
				}
				case 'le':
				{
					$searchOper	=	" <= '$searchString'";
					break;
				}
				case 'gt':
				{
					$searchOper	=	" > '$searchString'";
					break;
				}
				case 'ge':
				{
					$searchOper	=	" >= '$searchString'";
					break;
				}
				case 'ew':
				{
					$searchOper	=	"LIKE '%"."$searchString'";
					break;
				}
				case 'cn':
				{
	
					$searchq		=	strip_tags($searchString);
					$list			=	explode(' ',$searchq);
	
					foreach($list as $val)// preparing the search condition
					{
						$condition.="%$val%";
					}
					$condition	=	str_replace('%%','%',$condition);
					$searchOper	=	" LIKE '$condition' ";
					break;
				}
			}
			if($searchField!='')
			{
				$condition	=	"  $searchField $searchOper ";
	
			}
		}
	}
	if($condition!='')
	{
		$query	.= " HAVING ".$condition;
	}
	//dump($query);
//exit;
	//print_r($_POST);
	//echo $totalsearchfields	=	$_REQUEST['totalsearchfields'].'--------------------------------------';
/*	if(($_REQUEST['totalsearchfields'])>0)
	{
		$it=0;
		for($adsr=1;$adsr<=$_REQUEST['totalsearchfields'];$adsr++)
		{
			$searchField	=	$_REQUEST['searchField'.$adsr];
			$searchOper		=	$_REQUEST['searchOper'.$adsr];
			$searchString	=	trim(trim($_REQUEST['searchString'.$adsr],','),'');
			$compound		=	$_REQUEST['compound'.$adsr];
			$condition='';
			$cond='';
			if($searchField!='' && $searchString!='')
			{
				switch($searchOper)
				{
					case 'bw':
					{
						$searchOper	=	" LIKE '$searchString". "%' ";
						break;
					}
					case 'eq':
					{
						$searchOper	=	" = '$searchString'";
						break;
					}
					case 'ne':
					{
						$searchOper	=	" <> '$searchString'";
						break;
					}
					case 'lt':
					{
						$searchOper	=	" < '$searchString'";
						break;
					}
					case 'le':
					{
						$searchOper	=	" <= '$searchString'";
						break;
					}
					case 'gt':
					{
						$searchOper	=	" > '$searchString'";
						break;
					}
					case 'ge':
					{
						$searchOper	=	" >= '$searchString'";
						break;
					}
					case 'ew':
					{
						$searchOper	=	"LIKE '%"."$searchString'";
						break;
					}
					case 'cn':
					{
						$searchq		=	strip_tags($searchString);
						$list			=	explode(' ',$searchq);
						foreach($list as $val)// preparing the search condition
						{
							$cond.="%$val%";
						}
						$condition	=	str_replace('%%','%',$cond);
						$searchOper	=	" LIKE '$condition' ";
						break;
					}
				}//end of switch
				if($searchField!='')
				{
					$condition	=	"  $searchField $searchOper ";
				}
				//}
				if($condition!='' && $it==0)
				{
					$query	.= " HAVING ".$condition;
				}
				else
				{
					$query.=" $compound $condition";
				}
				$it++;
			}
		}
	}*/
	/*echo $query;
        exit;*/
	//echo $condition2;
	if($sort_index!='' && $sort_order!='')
	{
		$sort		=	" ORDER BY $sort_index $sort_order ";
		$sort		=	str_replace("eventdate","sortdate",$sort);
		$sort		=	str_replace("newsstartdate","sortdate",$sort);
		$sort		=	str_replace("newsenddate","sortdate2",$sort);
		$sort		=	str_replace("sentemaildatetime","sortdate",$sort);
		$sort_qs	=	"&field=$sort_index&order=$sort_order";
	}
	else
	{
		if($sortorder!='' )
		{
			$sort	=	"ORDER BY ".$sortorder; // takes field name and field order e.g. brandname DESC
		}
		else
		{
			//$sort=" ORDER BY 1 DESC";
		}
	}
	/*if($form=='frmstock')
	{
		$sort="";
	}
	*/
	/************** Paging Start ****************/
	if($page=="")
	{
		$page=1;
	}
	//echo "<br>----------------page limit--------------<br>";
	//echo "$pagelimit";
	if($pagelimit!="")
	{
		$_SESSION['pagelimit']	=	$pagelimit;
		$Paging->ResultsPerPage =	$_SESSION['pagelimit'];
		$Paging->ResultsPerPage =	$pagelimit;
	}
	else if($_SESSION['pagelimit'] > 0)
	{
		$Paging->ResultsPerPage =	$_SESSION['pagelimit'];
	}
	else
	{
		$pagelimitsarray	=	  explode(",",$_SESSION['pagingoptions']);
		$Paging->ResultsPerPage =$pagelimitsarray[0];
		//$Paging->ResultsPerPage =	15;
	}
	
	$Paging->LinksPerPage	=	5;
	$page  = $Paging->getCurrentPage();
	if($page > 1)
	{
		$start = ($page-1) * $Paging->ResultsPerPage;
	}
	else
	{
		$start = 0;
	}
	$end   = $Paging->ResultsPerPage;
	$records=" LIMIT $start , $end";
	$query.= " $sort $records ";
	//echo "<br>--------------------------<br>";
//	echo $query;
	//echo "<br>--------------------------<br>";
	/****************************MODIFY for getting Number of Results*************************/
	//$query	=	str_replace('SELECT', 'SELECT SQL_CALC_FOUND_ROWS',$query);
	//$newString = preg_replace('/(.*?)(\.[^\.]*)$/', '\1n\2', $string);
	// pattern, replacement, string, limit
	//echo time();
	//echo "<br>";
	
	$query	= preg_replace('/SELECT/', 'SELECT SQL_CALC_FOUND_ROWS', $query, 1); // outputs '123def abcdef 
//	echo $query;
	//exit;
	/*****************************************************************************************/
	/*
    echo	$query;
    exit;*/
	//$AdminDAO->displayquery=1;
	$fieldsarray =	$AdminDAO->queryresult($query,$type);
	//$AdminDAO->displayquery=0;
	/***************************************PAGING STUFF**************************************
	if($searchString=='')
	{
	$fieldsdata =	$AdminDAO->queryresult($query,$type);
	//echo	$fieldsdata	=	$AdminDAO->getrowsunbuffred($query);
	//echo count($fieldsdata);
	}
	//$fieldsdata	=	$AdminDAO->getrowsunbuffred($query);
	//exit;
	$count					=	count($fieldsdata);
	 */
	//$AdminDAO->displayquery=1;
	//$totalrows		=	$AdminDAO->executeQuery('SELECT FOUND_ROWS() as totalrows');
	//dump($totalrows);
	$Paging->TotalResults 	=	$_SESSION['totalrows'];//$totalrows[0][totalrows];
	//$AdminDAO->displayquery=0;
	//echo "<br>".time();
	//echo "<br>";
	/****************************************************************************************/
	//if($Paging->TotalResults > $Paging->ResultsPerPage)
	$pagelinks		=	$Paging->pageHTML('javascript: call_ajax_paging("'.$qs.$sort_qs.'&page=~~i~~","'.$dest.'","'.$div.'","pagelimittop")','top');
	$pagelinksbottom	=	$Paging->pageHTML('javascript: call_ajax_paging("'.$qs.$sort_qs.'&page=~~i~~","'.$dest.'","'.$div.'","pagelimitbottom")','bottom');
	/*if($pagelinks)
	{
		
		$pagelinks	=	"$pagelinks";
	}*/
	//$pagelinks="Next Page";
	/************** Paging End *****************/
	// Checking Array Size for Fields and Labels
	$labelsize		=	@sizeof($labels);
	$fieldsize		= 	@sizeof($fields);
	$hiddenfields	=	explode(',',$_COOKIE['datafields'.$printtitle]);
	//explode
	//print_r($hiddenfields);
	if($labelsize!=$fieldsize)
	{
		echo "Label count does not match with database Fields.";
		exit;
	}//end if
	//creating the list of field for show hide check boxes
	$showhidecheck="<ul class='chkboxlist'>";
	for($i=0;$i<@count($labels);$i++)
	{
		//if(trim($labels[$i]," ")!='ID')//hides ID field
		//{
		$val=$i+1;
		$showhidecheck.="<li>";
		if(!in_array($fields[$i],$hiddenfields))
		{
			$checked='checked="checked"';
		}
		else
		{
			$checked="";
		}
		$showhidecheck.='<input type="checkbox" name="'.$fields[$i].'" id="'.$fields[$i].'" value="'.$val.'" title="Uncheck to hide '.$labels[$i].' column." onclick="togglecol(this.id,this.checked)" '.$checked.' class="fieldchk"/>'.$labels[$i].'</li>';
		//}
	}
	$showhidecheck.="</ul>";
	// Including CSS
	//echo $css;
	// Including JavaScript Files	
	//echo $jsrc;
	// Including Page Navigation	
	//echo $pagelinks;
	?>
<!-- building table... -->
<?php
	if($fieldsarray == '-1')
	{
		
		$msg	=	msg(101,1);
		//$msg="<li>Sorry! But no record exists!</li>";
		//exit;
	}
	if($msg!='')
	{
	?>
    	<script type="text/javascript">
			msg('<?php echo $msg;?>');
		</script>
    <?php
	}
	?>
<script language="javascript">
function hidecoulmn(colid)
{
	return;
	//$('input:checkbox[name='+colid+']').each(function () 
	//{ 
		//alert(colid);
		//this.checked = false; 
		if(colid!='')
		{
			$('#gridtable<?php echo $form.''.$div;?>').toggleColumns([+$('#'+colid).val()]); 
		}
	//});
}
function showcheckbox(chosen,objectID)
{
	return;
	if(chosen == "thout") 
	{
		document.getElementById(objectID).className="";
		document.getElementById(objectID+'span').style.display="none";
	}
	else 
	{
		document.getElementById(objectID).className="thover";
		document.getElementById(objectID+'span').style.display="block";
	}
}
function showfields(listid)
{
	if(document.getElementById(''+listid+'chk').style.display=="none")
	{
		document.getElementById(''+listid+'chk').style.display="block";
	}
	else
	{
	
		document.getElementById(''+listid+'chk').style.display="none";
	}
	
}
 
	var fields='';
	function togglecol(id,ev)
	{
		//selectSelecterChange(id); 
		
		if(ev==false)
		{
			//alert(ev);//this will uncheck
			//this blocks hides the columns and saves its values in cookies
			$('input:checkbox[name='+id+']:checked').each(function () 
			{ 
				
				this.checked = false; 
				
			});
			//fieldchk
			var chks	=	document.getElementsByClassName('fieldchk');
			for(var i=0;i<chks.length;i++)
			{
				if(chks[i].checked==false)//if unchecked
				{
					//alert(fields);
					if(fields.search(chks[i].name)=='-1')//if not found in string
					{
					 	fields+=chks[i].name+',';
					}
				}
			}
		}
		else
		{
			$('input:checkbox[name='+id+']').each(function () {
			this.checked = true;
			});
		
			var chks	=	document.getElementsByClassName('fieldchk');
			for(var i=0;i<chks.length;i++)
			{
				if(chks[i].checked==true)//if checked
				{
					//alert(fields);
					if(fields.search(chks[i].name)!='-1')//if found in string
					{
					 	fields	=	fields.replace(chks[i].name+',', "");//add to field string with comma
						
						
					}
				}
			}
		}
		if(document.getElementById('th'+id).style.display=='block')
		{
			document.getElementById('th'+id).style.display=='none';
		}
		else
		{
			document.getElementById('th'+id).style.display=='block';
		}
		
		$('#gridtable<?php echo $form.''.$div;?>').toggleColumns([+$('#'+id).val()]); 
 		$('#dummyfieldsdiv').load('dbgridfieldscookie.php?field='+fields+'&screen=<?php echo $printtitle;?>');
		
		
	}
</script>
<form action="exportgrid.php" id="form_export" method="post" onsubmit="target_popup(this)">
    <input id="form_export_type" name="type" type="hidden" value="">
    <input id="form_export_heading" name="heading" type="hidden" value="">
    <input id="form_export_table" name="tab" type="hidden" value="">
</form>
<div style="display:block" id="dummyfieldsdiv"></div>
<div id="subsection"></div>
<?php
	if($div!='maindiv')
	{
		?>
<div style="margin-top:-22px; margin-right:4px;margin-bottom:4px" align="right"> <a href="javascript:void(0)" onclick="hidediv('<?php echo $div;?>')"> <i class=\"icon-remove bigger-110\"></i></a> </div>
<?php
	}
	?>
<div id="advancesearch<?php echo $div;?>" style="display:<?php if($_REQUEST['totalsearchfields']<1 || $_REQUEST['filter']=='filter'){print'none';}else{print'block';}?>">
    <fieldset>
        <legend>Advance Search</legend>
        <form name="advancesearchform<?php echo $div;?>" id="advancesearchform<?php echo $div;?>" method="get" class="form">
            <?php
	//advance search code
	for($fl=1;$fl<sizeof($fields);$fl++)
	{
		?>
            <table width="85%">
                <tr>
                    <td width="6%"><select name="searchField<?php echo $fl;?>"  id="searchField<?php echo $fl;?>" onkeydown="return checkkey(event,'<?php print $div;?>','<?php print $dest;?>')">
                            <?php
		for($i=0;$i<count($labels);$i++)
		{
			if(trim($labels[$i]," ")!='ID')//hides ID field
			{
				if(trim($labels[$i]," ")!='Picture')//hides Picture field
				{
					?>
                            <option value="<?php echo $fields[$i];?>" <?php if($fields[$i]==$_REQUEST['searchField'.$fl]){print"selected";}?>><?php echo $labels[$i]; ?></option>
                            <?php
				}
			}//end of if
		}
		?>
                        </select></td>
                    <td width="14%"><select name="searchOper<?php echo $fl;?>"  id="searchOper<?php echo $fl;?>" style="width:100px;" onkeydown="return checkkey(event,'<?php print $div;?>','<?php print $dest;?>')">
                            <option value="bw" <?php  if($_REQUEST['searchOper'.$fl]=='bw'){print"selected";}?>>begins with</option>
                            <option value="eq" <?php  if($_REQUEST['searchOper'.$fl]=='eq'){print"selected";}?>>equal</option>
                            <option value="ne" <?php  if($_REQUEST['searchOper'.$fl]=='ne'){print"selected";}?>>not equal</option>
                            <option value="lt" <?php  if($_REQUEST['searchOper'.$fl]=='lt'){print"selected";}?>>less</option>
                            <option value="le" <?php  if($_REQUEST['searchOper'.$fl]=='le'){print"selected";}?>>less or equal</option>
                            <option value="gt" <?php  if($_REQUEST['searchOper'.$fl]=='gt'){print"selected";}?>>greater</option>
                            <option value="ge" <?php  if($_REQUEST['searchOper'.$fl]=='ge'){print"selected";}?>>greater or equal</option>
                            <option value="ew" <?php  if($_REQUEST['searchOper'.$fl]=='ew'){print"selected";}?>>ends with</option>
                            <option value="cn" <?php  if($_REQUEST['searchOper'.$fl]=='cn' || $_REQUEST['searchOper'.$fl]==''){print"selected";}?>>contains</option>
                        </select></td>
                    <td width="17%"><input name="searchString<?php echo $fl;?>" type="text"  value="<?php echo trim($_REQUEST['searchString'.$fl],',');?>" id="searchString<?php echo $fl;?>" size="20" maxlength="100" onkeydown="javascript:if(event.keycode==13){advancesearchgrid('<?php print $div;?>','<?php print $dest;?>','advancesearchform<?php echo $div;?>'); return false;}" onfocus="this.select()"/></td>
                    <td width="63%"><?php
		//print_r($_GET);
		if(sizeof($fields)>1 && $fl<(sizeof($fields)-1))
		{
			//echo $_REQUEST['compound'.$fl+1].'---';
			$compname=$fl+1;
			?>
                        <input type="radio" id="compound<?php echo $fl+1;?>" name="compound<?php echo $compname;?>" value="AND" <?php  if($_REQUEST['compound'.$compname]==''){print"checked";}?>>
                        AND
                        <input type="radio" id="compound<?php echo $fl+1;?>" name="compound<?php echo $compname;?>" value="OR" <?php  if($_REQUEST['compound'.$compname]=='OR'){print"checked";}?> >
                        OR
                        <?php
		}
		?></td>
                </tr>
            </table>
            <?php
		//}//end of for advance search fields */
		?>
            </table>
            <?php
	}//end of for advance search fields
	?>
            <span class="buttons">
            <button type="button" class="btn btn-mini btn-info" onclick="advancesearchgrid('<?php print $div;?>','<?php print $dest;?>','advancesearchform<?php echo $div;?>')"><i class="fa fa-search"></i>Search</button>
            </span> <span class="buttons">
            <button type="button" class="positive" onclick="resetsearchform('<?php print $div;?>','<?php print $dest;?>')"><i class="fa fa-undo"></i>Reset</button>
            </span> <span class="buttons">
            <button type="button" class="positive" onclick="document.getElementById('advancesearch<?php echo $div;?>').style.display='none'"><i class="icon-remove bigger-110"></i>Cancel</button>
            </span>
            <input type="hidden" id="totalsearchfields" name="totalsearchfields" value="<?php echo sizeof($fields)-1;?>" />
            <input type="hidden" name="param" value="<?php echo $_GET['param'];?>" id="param"/>
            <input type="hidden" name="id<?php echo $div;?>" value="<?php echo $id;?>" id="id<?php echo $div;?>"/>
        </form>
    </fieldset>
    <br />
</div>
<?php
	//advanced data filters section
	global $filter;
	if(@sizeof($filter)>0)
	{
		?>
<fieldset>
    <legend>Advanced Data Filters</legend>
    <form name="advancedatafilterfrm<?php echo $div;?>" id="advancedatafilterfrm<?php echo $div;?>" style="display:block">
        <table width="100%" align="left" >
            <tr>
                <?php
						$compname=0;
						//for($g=0;$g<count($labels);$g++)
						//	{
						?>
                <td valign="middle"><?php
							$ab=1;
							//echo $_REQUEST['searchString'.$ab];
							for($fl=0;$fl<sizeof($filter);$fl++)
							{
								$tablefilter		=	$filter[$fl][0];
								$labelfilter		=	$filter[$fl][1];
								$fieldfilter		=	$filter[$fl][2];
								$aliasfilter		=	$filter[$fl][3];
								if($labelfilter)
								{
									$sqlfilter="select $fieldfilter from $tablefilter ";
									//$filterarray	=	$AdminDAO->queryresult($sqlfilter);
									if($aliasfilter!='')
									{
										$fieldfilter=$aliasfilter;
									}
									echo "<b>". $labelfilter."</b>";
									$selectedindextext	=	 $fl+1;
									?>
                    <input type="hidden" name="searchFieldName<?php echo $fl+1;?>" id="searchFieldName<?php echo $fl+1;?>" value="<?php echo $fieldfilter;?>" />
                    <input type="text" name="searchFieldFilter<?php echo $fl+1;?>"  id="searchFieldFilter<?php echo $fl+1;?>"  value="<?php echo trim($_REQUEST['searchString'.$selectedindextext],',');?>" class="searchfieldfiltertextbox"/>
                    <script language="javascript">
										maketoken('searchFieldFilter<?php echo $fl+1;?>','tokenizerresult.php?qry=<?php echo $sqlfilter;?>&field=<?php echo $fieldfilter;?>','horizental','s');
									</script>
                    <?php
									if($fl<(sizeof($filter)-1))
									{
										print"</td><td>";
										if($fl==0)
										{
											$compname=$fl+2;
										}
										else
										{
											$compname=$fl+2;
										}
										//echo $_REQUEST['compound'.$compname];
										?>
                    <input type="hidden" name="searchOperFilter<?php echo $fl+1;?>"  id="searchOperFilter<?php echo $fl+1;?>" value="cn">
                    <input type="radio" id="compoundFilter<?php echo $fl+1;?>" name="compoundFilter<?php echo $compname;?>" value="AND" <?php  if($_REQUEST['compound'.$compname]=='' || $_REQUEST['compound'.$compname]=='AND'){print"checked";}?>>
                    AND
                    <input type="radio" id="compoundFilter<?php echo $fl+1;?>" name="compoundFilter<?php echo $compname;?>" value="OR" <?php  if($_REQUEST['compound'.$compname]=='OR'){print"checked";}?> >
                    OR
                    <?php
									}//if
								}//if
								else
								{
									print"&nbsp;";
								}//else
							}//if
							?></td>
                <?php
						//}//for
						?>
                <td><span class="buttons">
                    <button type="button" class="btn btn-info byn-mini" onclick="advancesearchgrid('<?php print $div;?>','<?php print $dest;?>','advancedatafilterfrm<?php echo $div;?>','filter')"><i class="fa fa-search"></i>Search</button>
                    </span></td>
            </tr>
        </table>
        <input type="hidden" id="totalsearchfieldsfilter" name="totalsearchfieldsfilter" value="<?php echo sizeof($filter);?>" />
        <input type="hidden" name="param" value="<?php echo $_GET['param'];?>" id="param"/>
        <?php /*?><input type="hidden" name="id<?php echo $div;?>" value="<?php echo $id;?>" id="id<?php echo $div;?>"/><?php */?>
    </form>
</fieldset>
<?php
	}//advanced data filters section ended
?>
<!---------------------  New Code--------------------->
<?php /*?> <link href="image/style.css" rel="stylesheet"><?php */?>
<div class="hpanel">
    <div class="panel-heading" style="">
        <?php /*?><div class="panel-tools"> <a class="showhide"><i class="fa fa-chevron-up"></i></a> <a class="closebox"><i class="fa fa-times"></i></a> </div><?php */?>
		<h4 style="color:#34495e; font-size:17px; font-weight:600; ">
		<?php 
		if($sectionname != $screenname)
		{
			echo ucwords($sectionname).": ".ucwords($screenname);
		}
		else
		{
			echo ucwords($screenname);
		}
		?></h4>
        <span style="float:right; vertical-align:middle !important;">
        	<?php
				echo $filters;
			?>
        </span>
        </div>
    <div class="panel-body" style="display: block;">
        <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap no-footer"> 
            <!------------------------table-responsive end  of new code----------------->
            <div class="row" style="margin-bottom:5px !important;">
                <div class="col-sm-4">
                    <div id="example2_length" class="dataTables_length" > <?php echo $navbtn;?> </div>
                </div>
                <?php
                      //dump($labels);
                      //dump($fieldsarray,1);
                      ?>
                <div class="col-sm-8">
                    <div id="example2_length" class="dataTables_length" style="padding-top: 5px; text-align:right;">
                        <form name="searchform<?php echo $div;?>" id="searchform<?php echo $div;?>" method="get" class="form">
                        <?php
								echo $customfilters;
						?>
                            <select  class="form-control"  name="searchField<?php echo $div;?>"  id="searchField<?php echo $div;?>" onkeydown="return checkkey(event,'<?php print $div;?>','<?php print $dest;?>')" style="height: 33px; width: 145px;">
                                <?php
                            
                            for($i=0;$i<count($labels);$i++)
                            {
                            if(trim($labels[$i]," ")!='ID'  )//hides ID field
                            {
                                if(trim($labels[$i]," ")!='Picture')//hides Picture field
                                {
                                    ?>
                                <option value="<?php echo $fields[$i];?>" <?php if($fields[$i]==$searchField){print"selected";}?>><?php echo $labels[$i]; ?></option>
                                <?php
                                }
                            }//end of if
                            }
                            ?>
                            </select>
                            <select  class="form-control" name="searchOper<?php echo $div;?>"  id="searchOper<?php echo $div;?>" style="width:160px; height:33px;" onkeydown="return checkkey(event,'<?php print $div;?>','<?php print $dest;?>')" >
                                <option value="bw" <?php  if($searchoperator=='bw'){print"selected";}?>>begins with</option>
                                <option value="eq" <?php  if($searchoperator=='eq'){print"selected";}?>>equal</option>
                                <option value="ne" <?php  if($searchoperator=='ne'){print"selected";}?>>not equal</option>
                                <option value="lt" <?php  if($searchoperator=='lt'){print"selected";}?>>less</option>
                                <option value="le" <?php  if($searchoperator=='le'){print"selected";}?>>less or equal</option>
                                <option value="gt" <?php  if($searchoperator=='gt'){print"selected";}?>>greater</option>
                                <option value="ge" <?php  if($searchoperator=='ge'){print"selected";}?>>greater or equal</option>
                                <option value="ew" <?php  if($searchoperator=='ew'){print"selected";}?>>ends with</option>
                                <option value="cn" <?php  if($searchoperator=='cn' || $searchoperator==''){print"selected";}?>>contains</option>
                            </select>
                            <input class="form-control" style="height:33px !important; width:130px !important;" name="searchString<?php echo $div;?>" type="text"  value="<?php echo $searchString;?>" id="searchString<?php echo $div;?>"  onkeydown="return checkkey(event,'<?php print $div;?>','<?php print $dest;?>')" onfocus="this.select()"/>
                            <input type="hidden" name="param" value="<?php echo $_GET['param'];?>" id="param"/>
                            <input type="hidden" name="id<?php echo $div;?>" value="<?php echo $id;?>" id="id<?php echo $div;?>"/>
                            
                            <button type="button" class="btn btn-warning  btn-mini" onclick="searchgrid('<?php print $div;?>','<?php print $dest;?>')"><i class="fa fa-search"></i><span class="bold">Search</span></button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="row">
                <form name="<?php echo $form?>"  method="post" id="<?php echo $form?>">
                 <div class="table-responsive">
                    <table cellspacing="1" cellpadding="1" class="table table-bordered table-hover" id="gridtable<?php echo $form.''.$div;?>" style="width:99% !important;">
                        <thead class="panel-heading" style="background-color:#F7F9FA !important;">
                            <tr>
                                <th class=""  > <?php
	if($fieldsarray!=-1)
	{
		?>
                                    <label>
<?php /*?>                                        <input type="checkbox"  class="ace ace-checkbox" name="chkAll" value="checkbox" id="chkAll" onClick="checkAll(this,document.<?php echo $form?>.checks,'<?php echo $div;?>')"/><?php */?>
                                        <span class="lbl">
                                        <a id="chkAll" onClick="checkAll(this,document.<?php echo $form?>.checks,'<?php echo $div;?>')"  style="color:#6a6c6f !important">Select</a>
                                        </span> </label>
                                    <?php
	}
	?>
                                </th>
                                <?php
	for($i=0;$i<$labelsize;$i++)
	{
		if(trim($labels[$i]," ")!='ID' &&  (trim($labels[$i]) !=''))//hides ID field
		{
			?>
                                <th   onmouseover="showcheckbox('thover', this.id)" onmouseout="showcheckbox('thout',this.id)" id="th<?php echo $fields[$i];?>" class="footable-visible footable-sortable"> <?php
				//Displaying the Grid heads
				//changing sorting order and image
				if($sort_index== $fields[$i] && $sort_order=='asc')
				{
					$sorder='desc';
					$img="active_sortdown.gif";
					$iconsort	=	"<i class=\"fa fa-sort-down\"></i>";
				}
				else
				{
					$sorder='asc';
					$img="active_sortup.gif";
					$iconsort	=	"<i class=\"fa fa-sort-up\"></i>";
				}
				?>
                                    <a href="javascript: void(0)"  style="color:#6a6c6f !important" onclick="javascript: call_ajax_sort('<?php echo $fields[$i];?>','<?php echo $sorder;?>','<?php echo $qs.'&page='.$page;?>','<?php echo $dest;?>','<?php echo $div;?>')">
                                    <?php /*?><img src="<?php echo IMGPATH.$img;?>" width="8" height="10" hspace="2" border="0" /><?php */?>
                                    <?php
					echo $labels[$i];
					echo "<span class=red> $iconsort</span>";
					?>
                                    </a> <span id="th<?php echo $fields[$i];?>span" style="display:none; float:left; margin:2px;"> <a href="javascript:void(0)" onmouseover="showfields('<?php echo $fields[$i];?>')">
                                    <?php /*?><img src="<?php echo IMGPATH;?>sq_down.png" width="12" height="12" border="0"/><?php */?>
                                    <?php
									echo $iconsort;
									?>
                                    </a> <span id="<?php echo $fields[$i];?>chk" style="display:block; position:absolute; background:#99CCFF; border:solid; border-color:#9AC2DA;"> <?php echo $showhidecheck;?> </span> </span> </th>
                                <?php
		}//end of if
		else
		{
			$id_index	=	$i;
		}
	}//end for
	?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
			$samecategory =1;
	/**************** table data ******************/
	$previouscategory	=	"";
	if($fieldsarray !='-1')
	{
		for($j=0;$j<count($fieldsarray);$j++)
		{
			if($j%2==0)
			{
				$class="footable-even";
				$sortedcolor = "evensort";
			}
			else
			{
				$class="footable-odd";
				$sortedcolor = "oddsort";
			}
			
						
			?>
                            <tr id="tr_<?php echo $fieldsarray[$j][0].'_'.$div;?>"	onmousedown="highlight('<?php echo $fieldsarray[$j][0];?>','<?php print"$class"; ?>','row','<?php echo $div;?>')"  class="<?php echo $class;?> rowclass">
                                <td class="footable-visible gtd" align="center"><label>
                                        <input class="ace ace-checkbox-2" onClick="highlight('<?php echo $fieldsarray[$j][0];?>','<?php print"$class"; ?>','chk','<?php echo $div;?>')" type="checkbox" name="checks" id="cb_<?php echo $fieldsarray[$j][0].'_'.$div;?>" value="<?php echo $fieldsarray[$j][0];?>"/>
                                        <span class="lbl"></span> </label></td>
                                <?php
	$findme   	= array('.jpg','.jpeg','.gif','.bmp','.png','.GIF','.JPG','.JPEG');//extentions to find in values
	for($k=0;$k<$fieldsize;$k++)
	{
		if($k != $id_index)
		{
		}
		if($k!='0' &&   (trim($labels[$k]) !=''))
		{
			?>
                                <td class='<?php if($sort_index == $fields[$k]){echo $sortedcolor;}else{echo $fields[$k];}?> footable-visible'><?php
				$strvalue	=	$fieldsarray[$j][$fields[$k]];
				for($a=0;$a<=count($findme);$a++)
				{
					$pos = strpos($strvalue, $findme[$a]);
					if($pos!=false)
					{
						?>
                                    <img src="../sliderimages/thumbs/<?php echo $fieldsarray[$j][$fields[$k]];?>"/>
                                    <?php
					}
				}
				if($fields[$k]=='comments' || $fields[$k]=='question' || $fields[$k]=='answer' || $fields[$k]=='description' || $fields[$k]=='metadescription' || $fields[$k]=='metakeyword')
				{
					$strlength	=	strlen($fieldsarray[$j][$fields[$k]]);
					echo $str	=	 substr($fieldsarray[$j][$fields[$k]],0,80);
					$strcount	=	@count($str);
					if($strlength>80)
					{
						?>
                                    ... <a href="javascript: void(0)" title="Click to View Details" class="basic more" onclick="javascript: loaddetail('basicModalContent<?php echo $fieldsarray[$j][$fields[$id_index]];?><?php echo $div;?>')">View More</a>
                                    <?php
					}//end of strlength
					?>
                                    
                                    <!-- this div contains the full description of data for modal window -->
                                    
                                    <div id="basicModalContent<?php echo $fieldsarray[$j][$fields[$id_index]];?><?php echo $div;?>" style='display:none'> <span style="padding:20px;">
                                        <h3> &nbsp;Details </h3>
                                        <?php
											echo $fieldsarray[$j][$fields[$k]]; //details for comments and descrption in modal window
											?>
                                        </span> </div>
                                    <?php
				}
				else
				{
					echo $fieldsarray[$j][$fields[$k]];// data for general td
				}
				
				?></td>
                                <?php
		}//end of if
		//$totlacolumns++;
				
				
	}//inner for
	?>
                            </tr>
                            <?php
}//outer for
//dump($sumcategory);
?>
                            <?php
		/**************** end table data **************/
		?>
                            <?php
	}
	else
	{
		?>
                            <tr>
                                <td colspan="<?php echo $fieldsize;?>" style=" text-align:center; padding:10px; color:#F00; font-weight:bold;">Sorry! but no record exists.</td>
                            </tr>
                            <?php
	}
	?>
    <tr>
    	<td colspan="<?php echo $fieldsize?>" align="right" valign="top">
        <?php
            if($limit!='-1')
            {
                //echo $totalrecords/$page;
               // if($pagelinksbottom=='' && $totalrecords>10)
               // {
                   // $pagelinksbottom="<a href=javascript:resetpaging('$dest'); style='color:red;font-weight:bold'>Reset Page Mode</a>";
               // }
        
               echo "$totalrecords&nbsp;&nbsp;".$pagelinksbottom;
            }
			
        ?>
    
        </td>
    </tr>
                        </tbody>
                    </table>
                 </div>
                </form>
                
            </div>
            <!-------row----------->
            
            
        </div>
        <!------- REsponsive -----> 
    
    </div>
    <!-------panel-body-----------> 
</div>
<!-------hpanel-----------> 
<input type="hidden" value="" id="selecteitems<?php echo $div;?>" name="selecteditems" placeholder = "selecteitems<?php echo $div;?>" />
<script language="javascript" type="text/javascript">
<?php
	//$selected	=	"selected$div";
	//echo "var $selected='';";
	//echo "alert($selected);";
	if($search!='')
	{
		echo "\n document.getElementById('searchString".$div."').focus();";
		echo "\n document.getElementById('searchString".$div."').select();";
	}
	?>
	//loading(1);
	//confirmdelete();
</script>
<?php
//$_SESSION['qstring']=$qs.'&'.$sort_qs;
	$_SESSION['print_labels']=$labels;
	$_SESSION['print_fields']=$fields;
	$_SESSION['sql_query']=$query;
}//end function grid
?>
<script type="text/javascript">
$( document ).ready(function() {
	//ace-checkbox-2
	
/*	var $unique = $('input.ace-checkbox-2');
$unique.click(function() {
	//alert('hjewwewe');
    $unique.filter(':checked').not(this).removeAttr('checked');
});*/
	
		/*$('.ace-checkbox-2').on('change', function() {
			alert('');
			$('.ace-checkbox-2').not(this).attr('checked', false);  
		});*/
});
	
</script>