function collapsepanel(id,paneltype)
{
	if($("#"+paneltype+"updown"+id).hasClass( "fa-chevron-down" ))
	{
		$("#"+paneltype+"header"+id).removeClass( "panel-collapse");
		$("#"+paneltype+"updown"+id).removeClass( "fa-chevron-down").addClass("fa-chevron-up");
		
	}
	else
	{
		$("#"+paneltype+"header"+id).addClass( "panel-collapse");
		$("#"+paneltype+"updown"+id).removeClass( "fa-chevron-up").addClass("fa-chevron-down");
	}
	$("#"+paneltype+"body"+id).toggle();
	$("#"+paneltype+"footer"+id).toggle();
}
function isNumber(evt) {
    evt = (evt) ? evt : window.event;
    var charCode = (evt.which) ? evt.which : evt.keyCode;
	//alert(charCode);
	if(charCode == 39)
	{
		return false;
	}
    if (charCode > 31 && (charCode < 48 || charCode > 57))
	{
		if(charCode == 35 ||charCode == 36 ||charCode == 37 || charCode == 39 ||  charCode == 46)
		{
			return true;
		}
        return false;
    }
    return true;
}
///////////////////////////////////CREATE FORM////////////////////////////////////////////////////////
function loadfieldtype(type,div)
{
	//alert(type);
	//alert(div);
	$("#loadfield"+div).load('loadformfield.php?type='+type);
}
function LoadTypes(type)
{
	if(type== 1)
	{
		$("#typelist").hide();
		$("#typequery").show();	
	}
	else
	{
		$("#typequery").hide();
		$("#typelist").show();	
	}
}
/*function loadfieldtypeedit(type,div,pkformid)
{
	$("#loadfield"+div).load("loadformfield.php?type="+type+"7pkformid="+pkformid);
}*/
function fieldtitlefucntion(id,val)
{
	$('#fieldtitle'+id).html(val);
	$('#fieldfooter'+id).html(val);	
}
function loadmorefield()
{
	var response	=	'';
	$.post("formfield.php",function(msg){ response=msg;});
	setTimeout(function(){
		$('#loadmore').prepend('<div>'+response+'</div>');
	}, 1000);
	
}
function loadmorefieldoption(id)
{
	var response	=	'';
	$.post("loadfieldoptions.php?id="+id,function(msg){ response=msg;});
	setTimeout(function(){
		$('#optionsdiv'+id).append('<div>'+response+'</div>');
	}, 1000);
	
}
function loadmorefieldedit(pkformfieldid)
{
	var response	=	'';
	$.post("formfieldedit.php?pkformfieldid="+pkformfieldid,function(msg){ response=msg;});
	setTimeout(function(){
		$('#loadmore').append('<div>'+response+'</div>');
	}, 2000);
	
}
function deletefield(id)
{ 
	var x=confirm("Are you sure you want to delete "+$("#fieldlabel"+id).val()+" field?");
	if (x)
	{
	 	$('#panel-body'+id).remove();
	}
	else
	{
		return false;
	}	
}	
function deletefieldedit(id,val)
{ 
	var x=confirm("Are you sure you want to delete "+$("#fieldlabel"+id).val()+" field?");
	if (x)
	{
	 	$('#panel-body'+id).remove();  
		$.post("deleteformfield.php",{pkformfieldid:val},function(msg){});
	}
	else
	{
		return false;
	}	
}	
function deletefieldoption(id)
{ 
	var x=confirm("Are you sure you want to delete  field option?");
	if (x)
	{
	 	$('#deleteoptions'+id).remove();
	}
	else
	{
		return false;
	}	
}	
/////////////////////////////////// END////////////////////////////////////////////////////////
//Valid Date Check
function isValidDate(s) {
	// format D(D)/M(M)/(YY)YY
	var dateFormat = /^\d{1,4}[\.|\/|-]\d{1,2}[\.|\/|-]\d{1,4}$/;
	if (dateFormat.test(s)) {
		// remove any leading zeros from date values
		s = s.replace(/0*(\d*)/gi,"$1");
		var dateArray = s.split(/[\.|\/|-]/);
		// correct month value
		dateArray[1] = dateArray[1]-1;
		// correct year value
		if (dateArray[2].length<4) {
			// correct year value
			dateArray[2] = (parseInt(dateArray[2]) < 50) ? 2000 + parseInt(dateArray[2]) : 1900 + parseInt(dateArray[2]);
		}
		var testDate = new Date(dateArray[2], dateArray[1], dateArray[0]);
		if (testDate.getDate()!=dateArray[0] || testDate.getMonth()!=dateArray[1] || testDate.getFullYear()!=dateArray[2]) {
			return false;
		} else {
			return true;
		}
	} else {
		return false;
	}
}
//Load Item name for stocks screen
function loaditemname(bcid)
{
	jQuery('#itemname').load('getitemname.php?bcid='+bcid);
}
// JavaScript Document
function loaddetail(div)
{
	$('#'+div).modal();	
}
function loadpage(divid,page)
{
 $('#'+divid).load(page);
}
var selectedstring	='';
function getDiscoveries()
{
	alert(selecteditems+'...'+selectedstring);
}
function setselected(i,div,chkAll)
{
	//loading('Loading...');
	var selecteditems	=	$('#selecteitems'+div).val();
//	alert('selected'+div);
//window['selected' + div]	= 'hello';
//alert(window['selected' + div]);
//window['selected' + div]= window['selected' + div].replace('undefined',''+i);
if($('#cb_'+i+'_'+div).length)
{
	
	var c	=	document.getElementById('cb_'+i+'_'+div);
	if(chkAll==1)
	{
		if(c.checked==false)
		{
			selectedstring += ','+ i;
			//window['selected' + div]	+= ','+ i;
			selecteditems = selecteditems +','+ i;
			$('#selecteitems').val(selecteditems);
		}
		selectedstring = selectedstring.replace(','+i,','+i);
		//window['selected' + div]= window['selected' + div].replace(','+i,','+i);
		selecteditems= selecteditems.replace(','+i,','+i);
		
	}
	else if(chkAll==2)
	{
		selectedstring = selectedstring.replace(','+i,'');
		//window['selected' + div]= window['selected' + div].replace(','+i,'');
		selecteditems= selecteditems.replace(','+i,','+i);
	}
	else
	{
		if (c.checked == false)
		{
			selectedstring += ','+ i;
		//	window['selected' + div]	+= ','+ i;
			selecteditems += ','+ i;
			//eval('selected'+div)	+= ','+ i;	
		}
		else
		{
			selectedstring = selectedstring.replace(','+i,"");
			//window['selected' + div]= window['selected' + div].replace(','+i,"");
			selecteditems			=	selecteditems.replace(','+i,"");
			//eval('selected'+div) = eval('selected'+div).replace(','+i,"");
		}
	}
}
$('#selecteitems'+div).val(selecteditems);
//alert($('#selecteitems'+div).val());
}
function getselected(div)
{
	//alert(div);
	var selectedarray	=	new Array();
	selectedarray		=	$('#selecteitems'+div).val().split(',');//(window['selected' + div].replace("undefined","")).split(',');
	/*for(i=0;i<selectedarray.length;i++)
	{
		alert(selectedarray[i]);
	}*/
//	selectedarray		=	selectedstring.split(',');
	return (selectedarray);
}
function loading(hide)
{
	return;
	//alert(1);
	if(hide==1)
	{
		jQuery('#overlay').hide(300);
	}
	else
	{
		jQuery('#overlay').show();
	}
}
function loading1()
{
	jQuery('#overlay').show();
	//overlay.appendTo(document.body)
return;
	selectedstring = "";
	$(".loading").ajaxStart(function()
	{
	  $('#test2').oLoader({
	  wholeWindow: true, //makes the loader fit the window size
  	  lockOverflow: false, //disable scrollbar on body
	  backgroundColor:'#555555',
      image: 'assets/images/ownageLoader/loader4.gif',
/*      fadeInTime: 500,
      fadeOutTime: 1000,
      fadeLevel: 0.8*/
    });
 	});
	
	$(".loading").ajaxStop(function()
	{
		$('#test2').oLoader('hide');
	 });
}
/***********************************************highlight()*********************************/
function highlight(id,clas,ev,cdiv)
{
	
	if(ev=='row')
	{
		setselected(id,cdiv);//set the selected check boxes array
	}
	var cb	=	document.getElementById('cb_'+id+'_'+cdiv);
	//alert(cb);
	
	if(cb.checked == false)
	{
		
		document.getElementById('tr_'+id+'_'+cdiv).className='selected'+clas;
		
		cb.checked		=	true;
		//viewsuppliers	=	id;
	}
	else
	{
		//viewsuppliers=0;
		document.getElementById('tr_'+id+'_'+cdiv).className=clas;
		cb.checked = false;
	}
	if(ev=='chk')
	{
			
		if(cb.checked == false)
		{
			//viewsuppliers=0;
			document.getElementById('tr_'+id+'_'+cdiv).className=clas;
			cb.checked = false;
		}
		else
		{
			document.getElementById('tr_'+id+'_'+cdiv).className='selected'+clas;
			cb.checked		=	true;
			//viewsuppliers	=	id;
		}
			
	}
}
function highlight1(id,clas,ev,cdiv)
{
	
	//loading();
if($('#cb_'+id+'_'+cdiv).length)
{	
	var x = 0;
	var cb	=	document.getElementById('cb_'+id+'_'+cdiv);
	if(cb.checked==true)
	{
		x= 1;
	}
	var $unique = $('input.ace-checkbox-2');
    $unique.filter(':checked').removeAttr('checked');
	
	if(x==1)
	{
		cb.checked=true;
	}
	
	if(ev=='row')
	{
		setselected(id,cdiv);//set the selected check boxes array
	}
	
	//alert(cb);
	if(cb.checked == false)
	{
		
		document.getElementById('tr_'+id+'_'+cdiv).className='selected'+clas;
			
		cb.checked		=	true;
		//viewsuppliers	=	id;
	}
	else
	{
		//viewsuppliers=0;
		document.getElementById('tr_'+id+'_'+cdiv).className=clas;
		cb.checked = false;
	}
	if(ev=='chk')
	{
			
		if(cb.checked == false)
		{
			//viewsuppliers=0;
			document.getElementById('tr_'+id+'_'+cdiv).className=clas;
			cb.checked = false;
		}
		else
		{
			document.getElementById('tr_'+id+'_'+cdiv).className='selected'+clas;
			cb.checked		=	true;
			//viewsuppliers	=	id;
		}
			
	}
} 
//loading(1);
}
/***********************************************loadsuppliers()*********************************/
function loadsubgrid(div,checks,url,cdiv)
{
	loading();
//	alert(div+checks+url+cdiv);
	var selectedbrands	=	getselected(cdiv);
	var sb;
	if (selectedbrands.length > 1)
	{
		for (i=1; i < selectedbrands.length; i++)
		{
			 sb	=	selectedbrands[i];
		} 
		var sb1	=	sb.split(cdiv);
		prepareforedit(checks, sb,cdiv);
		$('#'+div).load(url+'?id='+sb1[0], function() {
  			loading(1);
		});
	}
	else
	{
		//alert("Please make sure that you have selected at least one row.");
		swal({
					title: "No record selected",
					text: "Please select at least 1 row.",
					showCancelButton: false,
					confirmButtonColor: "#DD6B55",
					confirmButtonText: "OK"
				});
	}//else
	//$('#'+div).load(url+'?id='+id);
	
	// document.getElementById(div).style.backgroundColor="#F90";
	// document.getElementById(cdiv).style.backgroundColor="#fff";
}//loadsuppliers
/***************************************************getsuppliers()**************************************************/
function getgrid(page,checks,tabid,cdiv,param)
{
	var selectedbrands	=	getselected(cdiv);
	var sb;
	if (selectedbrands.length > 1)
	{
		for (i=1; i < selectedbrands.length; i++)
		{
			 sb	=	selectedbrands[i];
		} 
		prepareforedit(checks, sb,cdiv);
		//jQuery("#"+div).load(page+'?id='+sb);
		//alert(sb);
		var sb1	=	sb.split(cdiv);
		//alert(sb1[0]+"--"+param)
		selecttab(tabid,page+'?id='+sb1[0]+'&param='+param);
	}
	else
	{
		//alert("Please make sure that you have selected at least one row.");
		swal({
					title: "No record selected",
					text: "Please select at least 1 row.",
					showCancelButton: false,
					confirmButtonColor: "#DD6B55",
					confirmButtonText: "OK"
				});
	}//else
}//getsuppliers	
/*****************************************showbrandform()**************************************************/
var olddiv="";
function showpage(clickedon,cbfield,page,div,cdiv,param,pagetype)
{
	loading();
	var id;
	if(clickedon !=0)
	{
		var selectedbrands	=	getselected(cdiv);
	}
	else
	{
		var selectedbrands	=	new Array();
	}
	
	var sb;
	var show = 1;
	if(clickedon == '1')
	{
		if (selectedbrands.length > 1)
		{
			for (i=1; i < selectedbrands.length; i++)
			{
				//alert(i+'---'+selectedbrands[i]);
				sb	=	selectedbrands[i];
			} 
			
			var sb1	=	sb.split(cdiv);
			//alert(clickedon,cbfield,page,div,cdiv);
			prepareforedit(cbfield, sb,cdiv);
			id	=	sb1[0];
			//jQuery("#"+div).load(page+'?id='+sb1[0]);
			
		}
		else
		{
			$('#screenfrmdiv').html('');
			//alert("Please make sure that you have selected at least one row.");
			swal({
					title: "No record selected",
					text: "Please select at least 1 record to update?",
					showCancelButton: false,
					confirmButtonColor: "#DD6B55",
					confirmButtonText: "OK"
				});
			show = 0;
			
		}//else
	}
	else if (clickedon =='2')
	{
		
		if (selectedbrands.length > 1)
		{
			for (i=1; i < selectedbrands.length; i++)
			{
			//	alert(i+'---'+selectedbrands[i]);
				 sb	=	selectedbrands[i];
			} 
			
			var sb1	=	sb.split(cdiv);
			//alert(clickedon,cbfield,page,div,cdiv);
			prepareforedit(cbfield, sb,cdiv);
			id	=	sb1[0];
			//jQuery("#"+div).load(page+'?id='+sb1[0]);
		}
		else
		{
			id	= '-1';
		}
	}
	else
	{
		id	=	'-1';
	}
	/*************************SHOW !=0**************/
	if(pagetype!='f' || pagetype=='')
	{
		if (show!=0)
		{
			if(page.indexOf('?')!=-1)
			{
				jQuery("#" + div).load(page + '&id=' + id + '&param=' + param);
			}
			else
			{
				jQuery("#" + div).load(page + '?id=' + id + '&param=' + param);
			}
		}
		$('#'+div).fadeIn(1500);
	}
	else
	{	//opens the dialogbox for forms
		var targetpage=page+'?id='+id+'&param='+param;
		//jQuery("#"+div).load(page+'?id='+id+'&param='+param);
		showdialog('',targetpage)
	}
	loading(1);
	//$('html,body').animate({'scrollTop':$('.loadpreviousevaluation').position().top}, 10); 
}//editform
/****************************  Mass Update ***********************************/
//<a href='javascript:showpage(1,document.~form~.checks,'budgetmassupdate.php','sugrid','~div~','')' title='Mass Update of Project Budget' class='btn btn-mini btn btn-mini'><i class='bigger-110 fa fa-list'></i></a>
//<a title="Mass Update of Task" href="javascript:massupdate('budgetmassupdate.php','maindiv','','')" onmouseout="buttonmouseout(this.id)" onmouseover="buttonmouseover(this.id)" id="massupdate" class="btn btn-mini btn-info"><i class="fa fa-list"></i></a>
function massupdatebudget(page,div,qs,param)
{
	var selectedbrands	=	getselected(div);
	var totalrecords	=	(selectedbrands.length)-1;
	brandsfordelete='';
	if( totalrecords > 0)
	{
		for (i = 0; i < selectedbrands.length; i++)
		{
			brandsfordelete	+=	','+selectedbrands[i];
		}//for
		//alert(brandsfordelete);
		///return;
		jQuery("#loadprojecttask").load(page + '?id=' + brandsfordelete + '&param=' + param);
		$('html,body').animate({'scrollTop':$('#loadprojecttask').position().top}, 10);
		//$('#'+div).fadeIn(1500);
	}//if selected brands
	else
	{
		alert("Please select at least one record for mass update.");
	}
}//Mass update 
function massupdate(page,div,qs,param)
{
	var selectedbrands	=	getselected(div);
	var totalrecords	=	(selectedbrands.length)-1;
	brandsfordelete='';
	if( totalrecords > 0)
	{
		for (i = 0; i < selectedbrands.length; i++)
		{
			brandsfordelete	+=	','+selectedbrands[i];
		}//for
		//alert(brandsfordelete);
		///return;
		jQuery("#massupdate").load(page + '?id=' + brandsfordelete + '&param=' + param);
		$('html,body').animate({'scrollTop':$('#loadmassupdate').position().top}, 10);
		//$('#'+div).fadeIn(1500);
	}//if selected brands
	else
	{
		alert("Please select at least one record for mass update.");
	}
}//Mass update 
/************************	Double click	***********/
function showform(id,page,div,param,pagetype)
{
	var id; 
	var sb;
	var show = 1;
  
	/*************************SHOW !=0**************/
	//alert("here  id = "+id+" page =  "+page+" div = "+div+"	param = "+param+"	pagetype = "+pagetype);
		//sugrid 
		jQuery("#"+div).load(page+'?id='+id+'&param='+param);	
		$('#'+div).fadeIn(1500);
	 
}//editform end of double click edit form
function inlineedit(id,screenid,page,div)
{
	//alert(screenid);
	var id; 
	var sb;
	var show = 1;
  //alert(screenid);
	/*************************SHOW !=0**************/
	//alert("here  id = "+id+" page =  "+page+" div = "+div+"	param = "+param+"	pagetype = "+pagetype);
		//sugrid 
		jQuery('#inline').load('inlineedit.php?id='+id+'&screenid='+screenid);	
		//jQuery("#"+div).load('inlineedit.php?id='+id+'&screenid='+screenid+'&param='+param);	
		//$('#'+div).fadeIn(1500);
	 
}//editform end of double click edit form
/****************************************************SHOWMAP()************************/
function showmap(page,cdiv)
{
	//alert(clickedon+'='+cbfield);
	var id;
	var selectedbrands	=	getselected(cdiv);
	var sb;
	var show = 1;
	//alert(selectedbrands);
	if (selectedbrands.length > 1)
	{
			for (i=1; i < selectedbrands.length; i++)
			{
				sb	=	selectedbrands[i];
			} 
			var sb1	=	sb.split(cdiv);
			id	=	sb1[0];
			mywindow	=	window.open('googlemap.php?id='+id,'gmap','',true);
			mywindow.focus();
	}
	else
	{
		//alert("Please make sure that you have selected at least one row.");
		swal({
					title: "No record selected",
					text: "Please select at least 1 row.",
					showCancelButton: false,
					confirmButtonColor: "#DD6B55",
					confirmButtonText: "OK"
				});
		return false;
	}//else
}//editform
/****************************************************SHOWMAPALL()************************/
function showmapall()
{
	//alert(clickedon+'='+cbfield);
	mywindow	=	window.open('showterritoriesmap.php','',true);
	mywindow.focus();
}//editform
function chkadvancesearch(div,param)
{
		//builds the query string of advance search
		//this function is called on paging action ,sorting, Advance serach 
		
		
		if(param=='filter')
		{
			var elm='advancedatafilterfrm'+div;
			var formname='advancedatafilterfrm'+div;
		}
		else
		{
			var elm='advancesearch'+div;
			var formname='advancesearchform'+div;
		}
		//alert(param);
		if(document.getElementById(elm).style.display=='block')
		{
			
			var len=formname.length;
			var fel	=	document.getElementById(formname);
			var ck=2;
			var searchField='';
			var searchOper='';
			var searchString='';
			var compound='';
			var totalsearchfields='';
			var qs='';
			var str;
			var v=2;
			var compound='';
			var	indexscompound='';
			//alert(len);
			if(param!='filter')
			{
				
				for(var x=1;x<(fel.length-1);x++)
				{
					//will get the name and value of each element and build the query string
					//searchField	=	
					//alert(fel[ck].name+'='+fel[ck].value);
					
					if(fel[ck].value!='')
					{
						var searchStringName	=	fel[ck].name;
						searchString			=	encodeURIComponent(fel[ck].value);
						indexsearchField		=	'searchField'+x;
						
						searchField				=	document.getElementById(indexsearchField).value;
						
						indexsearchOper			=	'searchOper'+x;
						searchOper				=	document.getElementById(indexsearchOper).value;
						
						
						indexscompound		=	'compound'+v;
						v++;
						//alert(indexscompound);
						var ic	=	eval('document.'+formname+'.'+indexscompound);
						
						if(ic[1].checked==true)
						{
							//alert('OR');
							compound='OR';
						}
						else
						{
							//alert('AND');
							compound='AND';
						}
						qs+=searchStringName+'='+searchString+'&'+indexsearchField+'='+searchField+'&'+indexsearchOper+'='+searchOper+'&'+indexscompound+'='+compound+'&';
					}//for
					ck+=5;
					if(fel.length<=ck)
					{
						x=ck;
						//return true;	
					}
				}
			}//if !filter
					//alert(len);
				if(param=='filter')
				{
					for(var x=0;x<(fel.length-1);x++)
					{
							indexsearchField		=	'searchField'+x;
							var ty	=	fel[x].type
							//alert(ty);
							if(ty=='radio')
							{
								if(ic==true)
								{
									compound='OR';
									indexscompound=fel[ck].name+''+x;
								}
								else
								{
									compound='AND';
									indexscompound=fel[ck].name+''+x;
								}	
								
								x++;
							}
							else
							{
								searchStringName		=	fel[x].name;
								searchString			=	encodeURIComponent(fel[x].value);
								indexsearchOper			=	'searchOper'+x;
								searchOper				=	'cn';
							}
							
							qs+=searchStringName+'='+searchString+'&'+indexsearchField+'='+searchField+'&'+indexsearchOper+'='+searchOper+'&'+indexscompound+'='+compound+'&';	
						
					
				}//for
			//	alert(qs);
				return false;
			}//if param
				//return false;
		//	alert(qs);
			if(qs!='' && qs!='&')
			{
				totalsearchfields=document.getElementById('totalsearchfields').value;
				qs+='totalsearchfields='+totalsearchfields;
				return(qs);
			}
			else
			{
				qs	=	qs.trim('&');
				return (qs);	
			}
		}//if style check
}
function resetsearchform(div,page)//will reset the Advance search form 
{
	var formname='advancesearchform'+div;
	jQuery('#'+div).load(page);
	//alert('abc');
	
	if(document.getElementById('advancesearch'+div))
	{
		//alert(div);
		//jQuery().show();
		document.getElementById('advancesearch'+div).style.display=='block'	;
	}
}
function call_ajax_paging(string,dest,div,pagelimit)// cals the ajax paging "Loads the section again with page paramater"
{
	loading(0);
	var advancesearchstr	=	chkadvancesearch(div);
	var pagelimit	=	document.getElementById(pagelimit).value;
	//jQuery('#'+div).load(dest+'?'+string+'&pagelimit='+pagelimit+'&'+advancesearchstr);
	
	//alert(dest);
	if(dest.indexOf('?') == -1)
	{
		//alert('if');
		jQuery('#'+div).load(dest+'?'+string+'&pagelimit='+pagelimit+'&'+advancesearchstr, function() {
			loading(1);
			});
	}
	else
	{	
		//alert('else');
		jQuery('#'+div).load(dest+'&'+string+'&pagelimit='+pagelimit+'&'+advancesearchstr, function() {
			loading(1);
			});
	}
	
}
/*************************************************call_ajax_sort()*************************************************/
function call_ajax_sort(field,order,page,urlpage,div)
{
	loading();
	var advancesearchstr	=	chkadvancesearch(div);
	var param='';
	if(page)
	{
		param='&page='+page;
	}
	
	if(urlpage.indexOf('?') == -1)
	{
		jQuery('#'+div).load(urlpage+'?field='+field+'&order='+order+'&'+page+'&'+advancesearchstr, function() {
  			loading(1);
});
	}
	else
	{	
		//alert('else');
		jQuery('#'+div).load(urlpage+'&field='+field+'&order='+order+'&'+page+'&'+advancesearchstr, function() {
  			loading(1);
		});
	}
	//jQuery('#'+div).load(urlpage+'?field='+field+'&order='+order+'&'+page+'&'+advancesearchstr);
}
/*************************************************showhide()*************************************************/
function showhide(id,lin)
{
		var cur	=	document.getElementById(id).style.display;
		if(cur == 'none')
		{
			document.getElementById(id).style.display='block';
			document.getElementById(lin).innerHTML='Hide';
			
		}
		else
		{
			document.getElementById(id).style.display='none';
			document.getElementById(lin).innerHTML='Show';
		}
}
/**************************************************************************************************************/
function prepareforedit(field, dontchangethis,cdiv)
{
	//alert(cdiv);
	for (i = 0; i < field.length; i++)
	{
		var cb	=	field[i].id;
		var	ab	=	cb.split('_');
		var tr	=	document.getElementById('tr_'+ab[1]+'_'+cdiv);
		if (ab[1] != dontchangethis)
		{
			field[i].checked = false;
			
			if(tr.className == 'even' || tr.className == 'selectedeven' )
			{
				tr.className	=	'even';
			}
			else
			{
				tr.className	=	'odd';
			}
		}
	}//for
}
function checkAll(currentcheckbox,field,cdiv)
{
	
	if(currentcheckbox.checked == true )
	{
		for (i = 0; i < field.length; i++)
		{
			var cb	=	field[i].id;
			var	ab	=	cb.split('_');
			var tr	=	document.getElementById('tr_'+ab[1]+'_'+ab[2]);
			setselected(ab[1],ab[2],1);
			if(tr.className == 'even' || tr.className == 'selectedeven')
			{
				tr.className	=	'selectedeven';
			}
			else
			{
				tr.className	=	'selectedodd';
			}
			
			field[i].checked = true ;
		}//for
	}//if
	else
	{
		selectedstring = "";
		for (i = 0; i < field.length; i++)
		{
			var cb	=	field[i].id;
			var	ab	=	cb.split('_');
			var tr	=	document.getElementById('tr_'+ab[1]+'_'+ab[2]);
			setselected(ab[1],ab[2],2);
			if(tr.className == 'even' || tr.className == 'selectedeven')
			{
				tr.className	=	'even';
			}
			else
			{
				tr.className	=	'odd';
			}
			field[i].checked = false ;
		}//for
	}
	
}//  End -->
function buttonmouseover(id)
{
		//document.getElementById(id).className ='button3';
}
function buttonmouseout(id)
{
		//document.getElementById(id).className ='button2';
}
function create_crumb(crumb)
{
	$('#breadcrumbs').html(crumb);
}
var brandsfordelete='';
function deleterecords(page,div,qs,param)
{
	
	var selectedbrands	=	getselected(div);
	var totalrecords	=	(selectedbrands.length)-1;
	brandsfordelete='';
	if( totalrecords > 0)
	{
		swal({
					title: "Are you sure to delete "+ totalrecords +" records?",
					text: "You will not be able to undo this action!",
					showCancelButton: true,
					confirmButtonColor: "#DD6B55",
					confirmButtonText: "Yes, delete it!"
				},
				function () {
					for (i = 0; i < selectedbrands.length; i++)
					{
						brandsfordelete	+=	','+selectedbrands[i];
					}//for
					if(page.indexOf('?') == -1)
					{
						jQuery('#'+div).load(page+'?'+qs+'&oper=del&id='+brandsfordelete+'&param='+param);
					}
					else
					{	
						jQuery('#'+div).load(page+'&'+qs+'&oper=del&id='+brandsfordelete+'&param='+param);
					}
					
				});
	}
	else
	{
		swal({
					title: "No record selected",
					text: "Please select at least 1 record to delete?",
					showCancelButton: false,
					confirmButtonColor: "#DD6B55",
					confirmButtonText: "OK"
				}
				);
	}
}
var brandsfordelete='';
function updaterecords(page,div,qs,param)
{
	var oper 	=	"update";
	var selectedbrands	=	getselected(div);
	var totalrecords	=	(selectedbrands.length)-1;
	brandsfordelete='';
	if( totalrecords > 0)
	{
		swal({
					title: "Are you sure to update "+ totalrecords +" records?",
					text: "You will not be able to undo this action!",
					showCancelButton: true,
					confirmButtonColor: "#DD6B55",
					confirmButtonText: "Yes, update "+ totalrecords +" records"
				},
				function () {
					for (i = 0; i < selectedbrands.length; i++)
					{
						brandsfordelete	+=	','+selectedbrands[i];
					}//for
					if(page.indexOf('?') == -1)
					{
						jQuery('#'+div).load(page+'?'+qs+'&oper='+oper+'&id='+brandsfordelete+'&param='+param);
					}
					else
					{	
						jQuery('#'+div).load(page+'&'+qs+'&oper='+oper+'&id='+brandsfordelete+'&param='+param);
					}
					
				});
	}
	else
	{
		swal({
					title: "No record selected",
					text: "Please select at least 1 record to update?",
					showCancelButton: false,
					confirmButtonColor: "#DD6B55",
					confirmButtonText: "OK"
				}
				);
	}
}
	
	/*function deleterecords(page,div,qs,param)
	{
		var selectedbrands	=	getselected(div);
		
		var totalrecords	=	(selectedbrands.length)-1;
		brandsfordelete='';
		var msg='';
		var act='';
		if(param=='confirm')
		{
			 msg='Are you sure to CONFIRM selected';
			 act="CONFIRM.";
		}
		else
		{
			 msg='Are you sure to DELETE selected';	
			  act="DELETE.";
		}
		if( totalrecords > 0)
		{
			
			if(confirm(msg+' ('+totalrecords+') record(s)?'))
			{
				//var br1	=	document.getElementById('brandformmain').brands;
				//alert(br1);
				for (i = 0; i < selectedbrands.length; i++)
				{
				//	var v	=	br1[i].value;
					brandsfordelete	+=	','+selectedbrands[i];
				
					
				}//for
				//alert(brandsfordelete);
				
				jQuery('#'+div).load(page+'?'+qs+'&oper=del&id='+brandsfordelete+'&param='+param);
				
				//jQuery('#'+div).show();
				//alert(document.getElementById(div).innerHTML);
				//alert(jQuery('#'+div).html());
				//.jQuery("#loading")
				//alert('a');
			}//if confirm
		
		}//if selected brands
		else
		{
			alert("Please select at least one record to "+act);
		}
	}*/
	/*******************************************************************************/
	function changeproduct(page,div1,div2,param)
	{
		var selectedbrands	=	getselected(div1);
		var totalrecords	=	(selectedbrands.length)-1;
		brandsfordelete='';
		if( totalrecords == 0)
		{
			//alert("Please select at least one row.");
			swal({
					title: "No record selected",
					text: "Please select at least 1 row.",
					showCancelButton: false,
					confirmButtonColor: "#DD6B55",
					confirmButtonText: "OK"
				});
			return;
		}//if selected brands
		jQuery('#'+div2).load(page+'?'+'id='+selectedbrands+'&param='+param);
	}
	//marks the items as fixed and wrong
	function markitems(page,div1,div2,status,param)
	{
		var selectedbrands	=	getselected(div1);
		var totalrecords	=	(selectedbrands.length)-1;
		brandsfordelete='';
		if( totalrecords == 0)
		{
			//alert("Please select at least one row.");
			swal({
					title: "No record selected",
					text: "Please select at least 1 row.",
					showCancelButton: false,
					confirmButtonColor: "#DD6B55",
					confirmButtonText: "OK"
				});
			return;
		}//if selected brands
		jQuery('#'+div2).load(page+'?'+'id='+selectedbrands+'&status='+status+'&mark=1&param='+param);
	}
	function displayrecords(page,dispdiv,maindiv,qs,param,pagetype)
	{
		
		var totalrecords	=	(selectedstring.length)-1;
		
		
		if( totalrecords > 0)
		{
			if(pagetype!='f' || pagetype=='')
			{
				
				jQuery('#'+dispdiv).load(page+'?'+qs+'&oper=show&id='+selectedstring+'&param='+param);
				//jQuery("#"+div).load(page+'?'+qs+'&oper=show&id='+selectedstring+'&param='+param);	
				
					//$('#'+div).fadeIn(1500);
			}
			else
			{
				var targetpage=page+'?'+qs+'&oper=show&id='+selectedstring+'&param='+param;
				//jQuery("#"+div).load(page+'?id='+id+'&param='+param);	
				showdialog('',targetpage)
			}
		}//if selected brands
		else
		{
			//alert("Please select at least one record to display.");
			swal({
					title: "No record selected",
					text: "Please select at least 1 record.",
					showCancelButton: false,
					confirmButtonColor: "#DD6B55",
					confirmButtonText: "OK"
				});
		}
	}
	function submitchecks(page,div,cdiv)
	{
	
		var selectedbrands	=	getselected(cdiv);
		var totalrecords	=	(selectedbrands.length)-1;
		
		//alert(selectedbrands);
			//var br1	=	document.getElementById('brandformmain').brands;
				//alert(br1);
				for (i = 0; i < selectedbrands.length; i++)
				{
					selectedbrands[i]=selectedbrands[i].replace(cdiv,'');
					brandsfordelete	+=	','+selectedbrands[i];
				}//for
				brandsfordelete=brandsfordelete.replace(',,','');
				//alert(brandsfordelete);
				jQuery('#'+div).load(page+'?id='+brandsfordelete);
	}
/***********************************************************SEARCH AREA********************************************/	
function searchgrid(div,page)
{
	loading();
	var searchField		= document.getElementById('searchField'+div).value;
	var searchString	= document.getElementById('searchString'+div).value;
	searchString		= encodeURIComponent(searchString);
	var searchOper		= document.getElementById('searchOper'+div).value;
	var param			= document.getElementById('param').value;
	var id				= document.getElementById('id'+div).value;
	
	if(page.indexOf('?') == -1)
	{
		$('#'+div).load(page+'?_search=true&searchField='+searchField+'&searchOper='+searchOper+'&searchString='+searchString+'&id='+id+'&param='+param, function() {
  			loading(1);
		});	
	}
	else
	{	
		//alert('else');
		$('#'+div).load(page+'&_search=true&searchField='+searchField+'&searchOper='+searchOper+'&searchString='+searchString+'&id='+id+'&param='+param, function() {
  			loading(1);
		});
}
loading(1);
	//$('#'+div).load(page+'?_search=true&searchField='+searchField+'&searchOper='+searchOper+'&searchString='+searchString+'&id='+id+'&param='+param);
	
}
function chkadvancedatafilter(div,param)
{
		//builds the query string of advance search
		//this function is called on paging action ,sorting, Advance serach 
		
		
		if(param=='filter')
		{
			var elm='advancedatafilterfrm'+div;
			var formname='advancedatafilterfrm'+div;
			
		}
		else
		{
			var elm='advancesearch'+div;
			var formname='advancesearchform'+div;
			totalsearchfields=document.getElementById('totalsearchfields').value;
		}
		//alert(param);
		if(document.getElementById(elm).style.display=='block')
		{
			
			var len=formname.length;
			var fel	=	document.getElementById(formname);
			var ck=2;
			var searchField='';
			var searchOper='';
			var searchString='';
			var compound='';
			var totalsearchfields='';
			var qs='';
			var str;
			var v=1;
			
			//alert(len);
			
				
				for(var x=1;x<(fel.length-1);x++)
				{
					//will get the name and value of each element and build the query string
					//searchField	=	
					//alert(fel[ck].name+'='+fel[ck].value);
					var searchStringName	=	fel[ck].name;
					//alert(searchStringName+'====id '+x);
					
						var searchStringName	=	fel[ck].name;
						searchString			=	encodeURIComponent(fel[ck].value);
					
						if(param=='filter')
						{
							var txtboxfilter	=	document.getElementsByClassName('searchfieldfiltertextbox');
							//alert(txtboxfilter.length);
							searchStringName		=	'searchString'+x;
							//indexsearchName			=	'searchFieldName'+x;
							//searchString			=	encodeURIComponent(fel[ck].value);
							//var searchStringName	=	fel[ck].name;
							//searchString			=	encodeURIComponent(fel[ck].value);
							indexsearchField		=	'searchFieldFilter'+x;
							indexsearchName			=	'searchFieldName'+x;
							//alert(indexsearchField);
							if(txtboxfilter.length>=x)
							{
								//alert(indexsearchField);
								searchString			=	encodeURIComponent(document.getElementById(indexsearchField).value);
								searchField				=	document.getElementById(indexsearchName).value;
							}
							indexsearchOper			=	'searchOperFilter'+x;
							if(document.getElementById(indexsearchOper))
							{
								searchOper				=	document.getElementById(indexsearchOper).value;
								
							}
							indexscompound			=	'compoundFilter'+parseInt(v);
						//	alert(indexscompound);
							indexsearchField		=	'searchField'+x;
							indexsearchName			=	'searchField'+x;
							indexsearchOper			=	'searchOper'+x;
							totalsearchfields=document.getElementById('totalsearchfieldsfilter').value;
							//alert(indexscompound);
							//if(v<=txtboxfilter.length)
							//{
								
							//}
						}
						else
						{
							
							//if(fel[ck].value!='')
							//{
								indexsearchField		=	'searchField'+x;
								searchField				=	document.getElementById(indexsearchField).value;
								indexsearchOper			=	'searchOper'+x;
								if(document.getElementById(indexsearchOper))
								{
									searchOper				=	document.getElementById(indexsearchOper).value;
									
								}
								indexscompound			=	'compound'+parseInt(v+1);
	
							//}
							/*var ic	=	eval('document.'+formname+'.'+indexscompound);
							indexscompound			=	'compound'+v;*/
						}
						v++;
						//alert(indexscompound);
						
						
							//alert("hello");
							//ic='';	
						
						//alert(ic);
						//var cmpname	=	ic[1].name;
						//return false;
						//alert(document.getElementsByTagName('radio').length);
						//chk=chk+5;
						
						var len=fel.length-4;
						//alert(len+'-----'+ck);
						if(ck<=len)
						{
								var ic	=	eval('document.'+formname+'.'+indexscompound);
							//alert(ic[1].id);
							
								if(ic[1].checked==true)
								{
									compound='OR';
								}
								else
								{
									compound='AND';
								}
								indexscompound	=	'compound'+v;
									
								
							}
						
						
						//alert(indexscompound);
						qs+=searchStringName+'='+searchString+'&'+indexsearchField+'='+searchField+'&'+indexsearchOper+'='+searchOper+'&'+indexscompound+'='+compound+'&';
						ck+=5;
						if(fel.length<=ck)
						{
							x=ck;
							//return true;	
						}
					}//for
					/*ck+=5;
					if(fel.length<=ck)
					{
						x=ck;
						//return true;	
					}*/
			
				
			if(qs!='' && qs!='&')
			{
				//alert(qs);
				qs+='totalsearchfields='+totalsearchfields+'&filter='+param;
				return(qs);
			}
			else
			{
				qs	=	qs.trim('&');
				return (qs);	
			}
			
			//return false;
		}//if style check
}
var resdiv;
/*function advancesearchgrid(div,page)
{
	
	var advancesearchqs	=	chkadvancesearch(div);
	jQuery('#'+div).load(page+'?'+advancesearchqs);	
}
*/
function advancesearchgrid(div,page,frm,para)
{
	var advancesearchqs	='';
	if(para=='filter')
	{
		advancesearchqs	=	chkadvancedatafilter(div,para);
	}
	else
	{
		advancesearchqs	=	chkadvancesearch(div,para);
	}
	jQuery('#'+div).load(page+'?'+advancesearchqs);	
}
/******************************************************************************************************************/
function checkkey(event,div,page)
{
	if(event.keyCode == 13)	
	{
		searchgrid(div,page);
		return false;
	}
	
}
/********************************************************************/
function numbersonly(e, decimal) {
var key;
var keychar;
if (window.event) {
   key = window.event.keyCode;
}
else if (e) {
   key = e.which;
}
else {
   return true;
}
keychar = String.fromCharCode(key);
if ((key==null) || (key==0) || (key==8) ||  (key==9) || (key==13) || (key==27) ) {
   return true;
}
else if ((("0123456789").indexOf(keychar) > -1)) {
   return true;
}
else if (decimal && (keychar == ".")) { 
  return true;
}
else
   return false;
}
function clearfield(val,targetfield)
{
	if(val!='')
	{
		document.getElementById(targetfield).value='';
		document.getElementById(targetfield).readOnly =true;
	}
	else
	{
		document.getElementById(targetfield).readOnly =false;
		document.getElementById(targetfield).value='Add New';
	}
}
var previns='';
function viewstoredetails(rowid)
{
	var dis	=	document.getElementById(rowid+'_detail').style.display;	
	if(dis=='block')
	{
			document.getElementById(rowid+'_link').src="../images/max.gif";	
			 $('#'+rowid+'_detail').slideUp("slow");
			//document.getElementById(rowid+'_detail').style.display='none';	
	}
	else
	{
		document.getElementById(rowid+'_link').src="../images/min.gif";
		//document.getElementById(rowid+'_detail').style.display='block';	
		 $('#'+rowid+'_detail').slideDown("slow");	
	}
	//tole the previouse
	//alert(previns+'=='+rowid);
	if(previns==rowid)
	{
		previns='';	
	}
	if(previns!='')
	{
		var disp	=	document.getElementById(previns+'_detail').style.display;	
		if(dis==disp)
		{
			if(dis=='none')
			{
				disp='block';	
			}
			else
			{
				disp='none';	
			}
		}
		if(disp=='block')
		{
				document.getElementById(previns+'_link').src="../images/max.gif";	
				 $('#'+previns+'_detail').slideUp("slow");
				//document.getElementById(rowid+'_detail').style.display='none';	
				
		}
		else
		{
			document.getElementById(previns+'_link').src="../images/min.gif";
			//document.getElementById(rowid+'_detail').style.display='block';	
			 $('#'+previns+'_detail').slideDown("slow");	
		}
	}
	previns=rowid;
}
function hidediv(divid,pagetype)
{
	//document.getElementById(olddiv).className='divborderkhali';
//	jQuery('#'+divid).html('');
	if(pagetype=='')
	{
		$('#'+divid).slideUp(500);
		$('#subsection').slideUp(500);
	}
	else
	{
		$('#dialog-form').dialog( "close" );	
	}
	$('#'+divid).hide();
	//document.getElementById("subsection").style.display	=	'none';
	
}
function hidedialog(did)
{
	$('#'+did).dialog( "close" );	
}
function toggleitem(id)
{
	var dis	=	document.getElementById(id).style.display;
	if(dis=='none')
	{
		//document.getElementById(id).style.display='block';
		 $('#'+id).slideDown("slow");
		document.getElementById(id+'_img').src="../includes/themes/theme-ace/assets/images/min.gif";
		return false;
	}
	else
	{
		document.getElementById(id+'_img').src="../includes/themes/theme-ace/assets/images/max.gif";
		$('#'+id).slideUp("slow");
		//document.getElementById(id).style.display='none';
		return false;
	}
}
/************* all items ***********/
function toggleallitem(id)
{
 var chkId = $('#screen_'+id);
 if(chkId.is(':checked')==true){
  $('.screendiv_'+id).attr("checked","checked");
 }
 else{
  $('.screendiv_'+id).removeAttr("checked");
 }
}
function adminnotice(text,cancel,timelimit)
{
	if(cancel=='1')
	{
		jQuery('.msg').hide();
	}
	else
	{
		jQuery('.msg').css('display','block');
		text	="<div id=error class=alert alert-block alert-success>"+text+"    <a href='javascript:adminnotice(0,1,0)'><i class='icon-remove'></i></a></div>";
		jQuery('.msg').html(text);
	}
}
/*function adminnotice(text,cancel,timelimit)
{
	//alert(cancel);
	if(cancel==1)
	{
		alert(cancel);
		//document.getElementById('notice').style.display='none';
		jQuery('#error').hide();
		//return false;
	}
	else
	{
		document.getElementById('error').style.display='block';
		text=text+" <a href='javascript:notice(0,1,0)'><img src='../images/min.GIF' border='0' /></a>";
		jQuery('#error').html(text);
		jQuery('#error').fadeOut(timelimit);	
	}
}*/
function trim (str) {
	var whitespace = ' \n\r\t\f\x0b\xa0\u2000\u2001\u2002\u2003\u2004\u2005\u2006\u2007\u2008\u2009\u200a\u200b\u2028\u2029\u3000';
	for (var i = 0; i < str.length; i++) {
		if (whitespace.indexOf(str.charAt(i)) === -1) {
			str = str.substring(i);
			break;
		}
	}
	for (i = str.length - 1; i >= 0; i--) {
		if (whitespace.indexOf(str.charAt(i)) === -1) {
			str = str.substring(0, i + 1);
			break;
		}
	}
	return whitespace.indexOf(str.charAt(0)) === -1 ? str : '';
}
function isNumberKey(evt)
{
	var charCode = (evt.which) ? evt.which : evt
	if (charCode > 31 && (charCode < 48 || charCode > 57) && (charCode !=46))
	return false;
	return true;
}
/*******************************************Form Submission***************************************/
var reloaddiv, reloadpage,formid,pagetype;
function addform(url,frm,div,page) 
{
	if(frm=="signupform")
	{
		//alert('Here');
		loadLoginDetails();
	}
	loading();
	//displayloading();
	$("#loading").show();
	$(".buttonid").hide();
	$("#loading").html('<img src="../assets/images/ownageLoader/loader4.gif" id="imageloaderid">');
	
	reloaddiv	=	div;
	reloadpage	=	page;
	formid		=	frm;
	/*if(document.getElementById('pagedescription'))
	{
		$('#pagedescription_tinymce').val($('.tinymce').html());
	}*/
	  //var fd = new FormData(document.getElementById("fileinfo"));
	var formData = new FormData(document.getElementById(formid));
   $.ajax({
       url: url,
       type: 'POST',
       data: formData,
       async: false,
       cache: false,
       contentType: false,
       enctype: 'multipart/form-data',
       processData: false,
       success: function (data) {
         //alert(response);
		 response(data);
       }
   });
   //return;
	/*
	options	=	{	
					url : url,
					type: 'POST',
					success: response
				}
	$('#'+formid).ajaxSubmit(options);*/
	setTimeout(function(){ if(page != "" && page == 'discoveryfront-thanks.php')
	{
		window.location.replace(page);
	} }, 3000);
	
}
function response(text)
{
	
	$("#loading").hide();
	$(".buttonid").show();
	//displayloading();
	//console.log(text);
	var m = $.parseJSON(text);
	var msgtype	=	m.messagetype;
	var pkerrorid	=	m.pkerrorid;
	var redirectme	=	m.redirectme;
	var loaddivname	=	m.loaddivname;
	var loadpageurl	=	m.loadpageurl
	var msgtext	=	"<b>System Message # "+pkerrorid+"</b><br>"+m.messagetext;
	//alert(redirectme);
	if(msgtype == 2)
	{
		if (typeof redirectme === "undefined") 
		{
			redirectme	=	'null';
		}
		if(redirectme != 'null')
		{
			if(redirectme=="userlogin.php")
			{
				$('#loginform').submit();
			}
			else if(redirectme=="login")
			{
				window.location.href =	"../../framework/signout.php";
			}
			else
			{
				window.location.href =	redirectme;
			}
			
		}
		
		msg(text);
		$('#'+reloaddiv).load(reloadpage, function() {
					// alert( "Load was performed." );
				loading(1);
 				
				});
		
		
		hideform();
		
		if(loadpageurl!="" && loaddivname!="")
		{
			$('#'+loaddivname).load(loadpageurl, function() {
					// alert( "Load was performed." );
				loading(1);
 				
				});
				
		}
		else if(redirectme!='null' )
		{
				//redirect user to redirectme
				//alert(redirectme);
				window.location.href =	redirectme;
	
		}
		
	}
	else
	{
		
		
		msg(text);
			loading(1);
	}
	//loading(1);
}
function submitdata(url,frm,div,page,ptype)
{
	alert(url+' '+formid+' '+reloaddiv+' '+reloadpage);
	reloaddiv	=	div;
	reloadpage	=	page;
	formid		=	frm;
	pagetype	=	ptype;
	
	options	=	{	
					url : url,
					type: 'POST',
					success: showresponse
				}
	jQuery('#'+formid).ajaxSubmit(options);
}
function showresponse(text)
{
	if(text.length>2)
	{
		adminnotice(text,0,5000);	
		//alert('test');
	}
	else
	{
		if(pagetype=='f')
		{
			//alert('here');
			hidedialog('dialog-form');	
		}
		adminnotice('Data has been saved.',0,5000);
		//reloaddiv = reloaddiv+'_tab';
		//selecttab(reloaddiv,reloadpage);
		//alert(reloaddiv);
		jQuery('#'+reloaddiv).load(reloadpage);
		hideform(formid,pagetype);
	}
}
function hideform(divid,pagetype)
{
	$('#sugrid').html("");
	$('#'+formid).hide();
	if(pagetype=='')
	{
		$('#'+divid).slideUp(500);
		$('#subsection').slideUp(500);
	}
	else
	{
		$('#dialog-form').dialog( "close" );	
	}
}
function loadschoolsforpostcode(postcode)
{
	postcode	=	encodeURI(postcode);
	$('#schooltd').load('loadschools.php?postcode='+postcode);
}
function loadpage(div,url)
{
	url	=	encodeURI(url);
	//salert(div +' '+url);
	$('#'+div).load(url);
}
//---	--------------control number of column start------------------------->
function show_hide_col(cl)
{
	//alert(cl);
	var c = $('#showcb_'+cl).is(":checked");
	if(!c)
	{
		//alert('if');
		$("."+cl).hide();
	}
	else
	{
		//alert('else');
		$("."+cl).show();
	}
	 
}
function export_table(table_id,type,heading)
{
	var tab  = document.getElementById(table_id).outerHTML;
	var type = type;
	jQuery("#form_export_type").val(type);
	jQuery("#form_export_heading").val(heading);
	jQuery("#form_export_table").val(tab);
	$("#form_export").submit();
	/*$.ajax({
                        url: "exportgrid.php",
                        type: "POST",
                        data: { tab: tab, type: type, heading:heading },
                        success: function(response){
                           //  alert(response);
						   //console.log(response);
                        },
                        error: function(){
                              alert('error');
							  // do action
                        }
                    });*/
	   
	 
}
function target_popup(form) {
   var type = jQuery("#form_export_type").val();
	//alert(jQuery("#form_export_type").val());
	if(type=='print')
	{
    	window.open('', 'formpopup', 'width=600,height=600,resizeable,scrollbars');
		form.target = 'formpopup';
	}
}
/*********************************************************************/
//---	--------------Message------------------------->
function msg(msg)
{
	//console.log(msg);
	//alert(msg);
	var m = $.parseJSON(msg);
	var msgtype	=	m.messagetype;
	var pkerrorid	=	m.pkerrorid;
	var redirectme	=	m.redirectme;
	if (typeof redirectme === "undefined" || redirectme == "")  
	{
		redirectme	=	'';
	}
	var msgtext	=	"<b>System Message # "+pkerrorid+"</b><br>"+m.messagetext;
	//alert('Hello');
	// Toastr options
        toastr.options = {
            "debug": false,
            "newestOnTop": false,
            "positionClass": "toast-top-center",
            "closeButton": true,
            "toastClass": "animated fadeInDown",
        };
	if(msgtype==1)
	{
       // $('.homerDemo1').click(function (){
            toastr.info(msgtext);
        //});
	}
	else if(msgtype==2)
	{
       // $('.homerDemo2').click(function (){
            toastr.success(msgtext);
        //});
	}
	else if(msgtype==3)
	{
        //$('.homerDemo3').click(function (){
            toastr.warning(msgtext);
       // });
	}
	else if(msgtype==4)
	{
        //$('.homerDemo4').click(function (){
            toastr.error(msgtext);
	   // });
	}
	if(redirectme != "")
	{
		setTimeout(function(){window.location.replace(redirectme); }, 3000);
		
	
	}
	
}