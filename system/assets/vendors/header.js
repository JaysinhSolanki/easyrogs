function loadsection(div,url,pkscreenid) {
	loading();
	if( url.indexOf('?')!=-1 ) {
		if( url.indexOf('pkscreenid=')!=-1 ) {
			$('#'+div).load(url);
		}
		else {
			$('#'+div).load(url+'&pkscreenid='+pkscreenid);
		}
	}
	else {
		if( url.indexOf('pkscreenid=')!=-1 ) {
			$('#'+div).load(url);
		}
		else {
			$('#'+div).load(url+'?pkscreenid='+pkscreenid);
		}
	}
	loading(1);
}
function  displayloading() {
	//$("#divid").show();
	//$("#wrapper").html('<img src="images/ownageLoader/loader4.gif" id="imageloaderid">');	
}
function show_types(id,clos) {	
	if( clos=='1' ) {
		document.getElementById(id).style.display='block';
	}
	else {
		document.getElementById(id).style.display='none';
	}
}
function loadactionitem(page,id) {
	 loadsection('center-column',page+'?id='+id);
}
function openpopup(wid,hig,page) {
	var display='toolbar=0,location=0,directories=1,menubar=1,scrollbars=1,resizable=1,width='+wid+',height='+hig+',left=100,top=25';
	//jQuery('body').append('closingprint.php');
 	window.open(page,'Closing',display); 	 
}