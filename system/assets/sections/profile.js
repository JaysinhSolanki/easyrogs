

// LEGACY
function verifyEmail()
{
	var email	=	$('#email').val();
	if(email == "")
	{
		$("#verification_msg").html("Please enter your email.");
	}
	else
	{
		$.post( "/system/application/verifyemail.php", { email: email })
		.done(function( data ) 
		{
			if(data == "success")
			{
				$("#verification_msg").html("Verification code has been sent to you on your email.");
			}
			else
			{
				$("#verification_msg").html("Error.");
			}
		});
	}
	$("#verification_msg").show();
	setTimeout(function(){ $("#verification_msg").html("")}, 3000);
}
function deleteAccountFunction()
{	
Swal.fire({
 title: "Are you sure to permanently delete Your Account?",
  text: "Once deleted, you will not be able to recover account!",
icon: 'warning',
showCancelButton: true,
confirmButtonColor: '#187204',
cancelButtonColor: '#C2391B',
confirmButtonText: "Yes, delete it!"
}).then((result) => {
if (result.value) {
 window.location.replace("deletemyaccount.php");
}
});
	
}

function barnoFunction(id)
{
	 if($("#fkgroupid").prop("checked") == true)
	 {
		 $("#barnumber").show();
		 $("#myteamDiv").show();
	 }
	 else
	 {
		 $("#barnumber").hide();
		 $("#myteamDiv").hide();
	 }
	/*if(id == 3)
	{
		$("#barnumber").show();
	}
	else
	{
		$("#barnumber").hide();
	}*/
}
function checkEmail(oldEmail, newEmail)
{
  $(".verifyDiv").css('display', oldEmail === newEmail ? 'none' : 'block');
}