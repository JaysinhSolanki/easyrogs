var previous = '';
function selecttab(id,url,pkscreenid)
{
	//loading();
	//console.log("id="+id+'  url='+url+' pkscreenid='+pkscreenid);
	//alert(pkscreenid);

		//if($('#'+pkscreenid).length)
//{
		$('#'+id).className="active";
	//	document.getElementById(id+'_b').className="activetabclass";
		//document.getElementById(id).css({"background-color":"yellow","font-size":"200%"});
		if(id != previous && previous!= '')
		{
			$('#'+previous).className="inactive";
		//	document.getElementById(previous+'_b').className="inactive";		 
		}
	//	jQuery('#loadpreviousevaluation').html('');
		//jQuery('#manageprojecttask').html('');
		loadsection('wrapper',url,pkscreenid);
		previous  = id;
	//}
}
function loadsection(div,url,pkscreenid)
{
	loading();
	//displayloading();
	//return;
	//url	=	url.replace("&&","&");
	//url	=	url.replace("&&","&");
	if(url.indexOf('?')!=-1)
	{
		if(url.indexOf('pkscreenid=')!=-1)
		{
			$('#'+div).load(url);
			/*$("#divid").hide();*/
		}
		else
		{
			$('#'+div).load(url+'&pkscreenid='+pkscreenid);
		
		}
		//$("#imageloaderid").hide();
		
	}
	else
	{
		if(url.indexOf('pkscreenid=')!=-1)
		{
			$('#'+div).load(url);
			
		}
		else
		{
			$('#'+div).load(url+'?pkscreenid='+pkscreenid);
			
		
		}
		//$("#imageloaderid").hide();
			
	}
	loading(1);
}
function  displayloading()
{
	//$("#divid").show();
	//$("#wrapper").html('<img src="images/ownageLoader/loader4.gif" id="imageloaderid">');	
}
function show_types(id,clos)
{	
	if(clos=='1')
	{
		document.getElementById(id).style.display='block';
	}
	else
	{
		document.getElementById(id).style.display='none';
	}
}
function loadactionitem(page,id)
{
	 loadsection('center-column',page+'?id='+id);
}
$(document).ready(function() {
//  loadsection('center-column','managearrivals.php');
  selecttab('7_tab','dashboard.php');
	//window.history.forward(1);
});
function openpopup(wid,hig,page)
{
	var display='toolbar=0,location=0,directories=1,menubar=1,scrollbars=1,resizable=1,width='+wid+',height='+hig+',left=100,top=25';
	//jQuery('body').append('closingprint.php');
 	window.open(page,'Closing',display); 	 
}
/*function selectmodule(moduleid)
{
	window.location='index.php?sectionid='+moduleid;
}*/