<script type="text/javascript">
$( document ).ready(function() 
{
	/************* Time Picker ************/
	<?php
	if($GAF->timepicker == 1)
	{
	?>
		$('.clockpicker').clockpicker({
		placement: '<?php echo $pickerposition;?>',
		autoclose: true,
		'default': 'now'
	});
	<?php
	}
	
	if($GAF->datetimepicker == 1)
	{
	?>
		/*************** Date Time Picker ******************/
		$('.datetimepicker').datetimepicker({format: '<?php echo $jsdateformat." ".$jstimeformat; ?>'});
	<?php
	}
	if($GAF->daterangepicker == 1)
	{
	?>
		/*************** Date Range Picker ******************/
		$('.dateandrangepicker').daterangepicker({locale: {format: '<?php echo $jsdateformat;?>'}});
	<?php
	}
	if($GAF->datepicker == 1)
	{
	?>
		/*************** Date Picker ******************/
		$('.datepicker').datetimepicker({format: '<?php echo $jsdateformat;?>'});
	<?php
	}
	if($GAF->datetimerangepicker == 1)
	{
	?>
		/*************** Date Time Range Picker ******************/
		$('.datetimerangepicker').daterangepicker(
		{
			timePicker: true,
			locale: {format: '<?php echo $jsdateformat." ".$jstimeformat; ?>'}
		});
	<?php
	}
	if($GAF->select2 == 1)
	{
	?>
	 	$('.select2').select2();
	 <?php
	}
	?>
	
});
function loadList(selectedvalue,filename,fieldid,fkformfieldid)
	{
		
		$.post( filename, { selectedvalue: selectedvalue, fieldid : fieldid, fkformfieldid:fkformfieldid })
		  .done(function( data ) {
			$("#child_div_"+fieldid).remove();
			
			$('#'+fieldid).parent().parent().append(data);
		  });
	}
	
/********************************************************DropZone*************************************/
// Get the template HTML and remove it from the doumenthe template HTML and remove it from the doument
var previewNode = document.querySelector("#template");
previewNode.id = "";
var previewTemplate = previewNode.parentNode.innerHTML;
previewNode.parentNode.removeChild(previewNode);

var myDropzone = new Dropzone(document.body, { // Make the whole body a dropzone
  url: "/target-url", // Set the url
  thumbnailWidth: 80,
  thumbnailHeight: 80,
  parallelUploads: 20,
  previewTemplate: previewTemplate,
  autoQueue: false, // Make sure the files aren't queued until manually added
  previewsContainer: "#previews", // Define the container to display the previews
  clickable: ".fileinput-button" // Define the element that should be used as click trigger to select files.
});

myDropzone.on("addedfile", function(file) {
  // Hookup the start button
  file.previewElement.querySelector(".start").onclick = function() { myDropzone.enqueueFile(file); };
});

// Update the total progress bar
myDropzone.on("totaluploadprogress", function(progress) {
  document.querySelector("#total-progress .progress-bar").style.width = progress + "%";
});

myDropzone.on("sending", function(file) {
  // Show the total progress bar when upload starts
  document.querySelector("#total-progress").style.opacity = "1";
  // And disable the start button
  file.previewElement.querySelector(".start").setAttribute("disabled", "disabled");
});

// Hide the total progress bar when nothing's uploading anymore
myDropzone.on("queuecomplete", function(progress) {
  document.querySelector("#total-progress").style.opacity = "0";
});

// Setup the buttons for all transfers
// The "add files" button doesn't need to be setup because the config
// `clickable` has already been specified.
document.querySelector("#actions .start").onclick = function() {
  myDropzone.enqueueFiles(myDropzone.getFilesWithStatus(Dropzone.ADDED));
};
document.querySelector("#actions .cancel").onclick = function() {
  myDropzone.removeAllFiles(true);
};	
</script>