$(function()
{
	alert("i am here....");
	var email= getCookie('email');
	var fkgroupid=getCookie('fkgroupid');
	var suid=getCookie('surveyuid');
	var ouid=getCookie('observeruid');
	var pkspecietypeid=getCookie('pkspecietypeid');
	/*var v1=email.substr(0, email.indexOf(','));
	var v2=fkgroupid.substr(0, fkgroupid.indexOf(',')); */
	alert(email);
	//location.reload();
	//alert(pkspecietypeid);
	if(navigator.onLine)
	{
		if(email =="" || fkgroupid != 5)
		{
		 window.location="login.php";
		}
	
	}
	else
	{
	     if(email =="" && fkgroupid != 5)	
	     {
		   window.location="error404.php";
	     }
	}
});
	function getCookie(cname) {
    var name = cname + "=";
    var ca = document.cookie.split(';');
    for(var i=0; i<ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0)==' ') {
            c = c.substring(1);
        }
        if (c.indexOf(name) == 0) {
            return c.substring(name.length, c.length);
        }
    }
    return "";
}

  
/*function fun()
{
	user = prompt("Please enter your name:", "");
	var email=getCookie('uid');
	if(email==user){}
	else
	{       
			var r = confirm("Reenter Password!");
			if (r == true)
			 {
			  fun();
			 } 
			else
			 {
			txt = "You pressed Cancel!";
			 }
	}
}
*/	
	