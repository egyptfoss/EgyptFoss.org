<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>File Upload UI</title>
</head>

<body>
	<div id='header'>
	  <div>
			<form action="/changeProfilePhoto" method="post" enctype="multipart/form-data" id="MyUploadForm">
				<input name="apiKey" id="input_apiKey" type="text" value="eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9" style="display:none;"/><br />
				token: <input name="token" id="token" type="text" /><br /><br />
				<input name="profile_photo" id="profile_photo" type="file" />
				<input type="submit"  id="submit-btn" value="Upload" />
				<img src="images/ajax-loader.gif" id="loading-img" style="display:none;" alt="Please Wait"/>
			</form><br />
			<div id="progressbox" ><div id="progressbar"></div ><div id="statustxt">0%</div></div>
			<div id="output"></div>

	  </div>
	</div>
	<script>
		function OnProgress(event, position, total, percentComplete) {
			//Progress bar
			$('#progressbox').show();
			$('#progressbar').width(percentComplete + '%') //update progressbar percent complete
			$('#statustxt').html(percentComplete + '%'); //update status text
			if(percentComplete>50) {
				$('#statustxt').css('color','#000'); //change status text to white after 50%
			}
		}
		$(document).ready(function() { 
			var options = { 
					target:   '#output',   // target element(s) to be updated with server response 
					beforeSubmit:  beforeSubmit,  // pre-submit callback 
					success:       afterSuccess,  // post-submit callback 
					uploadProgress: OnProgress, //upload progress callback 
					resetForm: true        // reset the form after successful submit 
			};
			$('#MyUploadForm').submit(function() { 
				$(this).ajaxSubmit(options);
				return false;
			});
		});
	</script>
</body>
</html>
