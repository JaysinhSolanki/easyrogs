var previous = '';
function selecttab(id,url,pkscreenid) { 
	console.log( "TAB:", {id, previous, url, pkscreenid } ); //debugger;

	$('#'+id).className="active";
	if( id != previous && previous ) {
		$('#'+previous).className="inactive";
	}
	loadsection('wrapper',url,pkscreenid);
	globalThis['currentPage'] = { id, previous, pkscreenid: pkscreenid || 7, url, };
//!!	
const videos = [
	{ id: "7",  video: "ctxhelp-dashboard.mp4", },
	{ id: "8",  video: "ctxhelp-myprofile.mp4", },
	{ id: "44", video: "ctxhelp-cases.mp4", },
	{ id: "45", video: "ctxhelp-discoveries.mp4", },
	{ id: "46", video: "ctxhelp-case.mp4", },
	{ id: "47", video: "ctxhelp-discovery.mp4", },
];
const idx = videos.findIndex(item => item && item.id == currentPage['pkscreenid']);
console.assert(idx >= 0, "[!] NOT FOUND:", { currentPage })
if (idx < 0) debugger;
//!!
	previous = id;
}
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
$(document).ready(function() {
//  loadsection('center-column','managearrivals.php');
  selecttab('7_tab','dashboard.php');
	//window.history.forward(1);
});
function openpopup(wid,hig,page) {
	var display='toolbar=0,location=0,directories=1,menubar=1,scrollbars=1,resizable=1,width='+wid+',height='+hig+',left=100,top=25';
	//jQuery('body').append('closingprint.php');
 	window.open(page,'Closing',display); 	 
}