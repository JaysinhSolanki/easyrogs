

// LEGACY ----------------------------------------------------------------------
function verifyEmail()
{
	var email	=	$('#email').val();
	if(email == "")
	{
		$("#verification_msg").html("Please enter your email.");
	}
	else
	{
		$.post( "verifyemail.php", { email: email })
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
function barnoFunction(id)
{
	if($("#fkgroupid").prop("checked") == true)
	{
	 $("#barnumber").show();
	}
	else
	{
	 $("#barnumber").hide();
	}
}
function planbtnftn(id)
{
	
	$(".packageschkbox").attr("checked", false);
	$("#pkpackageid"+id).attr("checked", true);
	$("addclass").addClass("active");
	$('.plandivs').removeClass('active');
	$('#maindiv'+id).addClass('active');
}

function changestateftn(id)
{
	$('#loadstates').load('loadstatessignup.php?fkcountryid='+id);
}
function changecityftn(id)
{
	$('#loadcities').load("loadcitiessignup.php?fkstateid="+id);
}
$('input.packageschkbox').on('change', function() {
	 $("#package-error").hide();
    $('input.packageschkbox').not(this).prop('checked', false);
	
});

/***************************************** Validation Form ***********************************/
  $("#signupform").validate({
            rules: {
                firstname: {
                    required: true,
                    minlength: 3
                },
                lastname: {
                    required: true,
                    minlength: 3
                },
                email: {
					required: true,
					email: true
                },
                password: {
                    required: true,
                    minlength: 6
                },
				phone: {
                    required: true
                },
				address: {
                    required: true
                },
				fkstateid: {
                    required: true
                },
				zipcode: {
                    required: true
                }
            },
            messages: {
				firstname: {
                    required: "Please enter your first name",
                    minlength: "Please enter alteast 3 characters)"
                },
				lastname: {
                    required: "Please enter your last name",
                    minlength: "Please enter alteast 3 characters)"
                },
				email: {
                    required: "Please enter your email address",
                    email: "Please enter valid email address)"
                },
				password: {
                    required: "Please enter your phone number",
                    minlength: "Please enter atleast 6 characters)"
                },
                phone: {
                    required: "Please enter your phone number"
                },
				address: {
                    required: "Please enter your address"
                },
				fkstateid: {
                    required: "Please select your state."
                },
				zipcode: {
                    required: "Please enter your zipcode."
                }
				
            },
            submitHandler: function(form) 
			{
				     var formData = new FormData(form);
       			  $.ajax({
						type: 'POST',
						url: 'signupaction.php',
						data:formData,
						cache:false,
						contentType: false,
						processData: false,
						success: function(data) {
							var result			=	JSON.parse(data);
							var msg				=	result.msg;
							var membershipuid	=	result.membershipuniqueid;

              if(msg == 'alreadyuser')
							{
								$("#errormsg").show();
								$("#errormsg").html("Email already exists.");
								$("#errormsg").delay(2000).fadeOut();		
							}
							else if(msg == "success")
							{
								$("#successmsg").show();
									setTimeout(function()
									{
										$("#successmsg").delay(1000).fadeOut();	
										window.location.href= "userlogin.php";	
									},5000);
							}
						},
						error: function(data) {
									$("#errormsg").show();
									$("#errormsg").delay(2000).fadeOut();	
						}
						});
            }
        });

$(function() 
{
	changestateftn(254);
	$(".selectsearch").select2();
});
