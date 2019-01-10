<html>
<head>
    <title><?php echo $template_inputs["title"] ?></title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="keywords" content="">
</head>
<body>
<style>
    /* Force Outlook to provide a "view in browser" message */
    #outlook a { padding: 0; }
    /* Force Hotmail to display emails at full width */
    .ReadMsgBody { width: 100%; }
    .ExternalClass { width: 100%; }
    /* Force Hotmail to display normal line spacing */
    .ExternalClass, .ExternalClass p, .ExternalClass span,
    .ExternalClass font, .ExternalClass td, .ExternalClass div { line-height: 100%; }
     /* Prevent WebKit and Windows mobile changing default text sizes */
    body, table, td, p, a, li, blockquote {
      -webkit-text-size-adjust: 100%;
      -ms-text-size-adjust: 100%;
    }
    /* Remove spacing between tables in Outlook 2007 and up */
    table, td {
      mso-table-lspace: 0pt;
      mso-table-rspace: 0pt;
    }
    /* Allow smoother rendering of resized image in Internet Explorer */
    img {
      -ms-interpolation-mode: bicubic;
    }
	.foss-mail-wrap{ font-size: 16px;font-family:Arial,Verdana; }
	@media screen and (max-width:480px){
		.foss-mail-wrap{font-size: 20px;}
		.foss-mail-title{font-size:22px!important;}
	}
	</style>
<table class="foss-mail-wrap" width="100%" height="100%">
	<tr>
		<td style="">
			<table style="width:95%;margin:auto;" cellspacing="0">
				<tr>
					<td class="foss-mail-header" style="background-color:#eee;padding:25px;">
						<img src="<?php echo $template_inputs["home_url"] ?>/wp-content/themes/egyptfoss/img/mail-logo.png" alt="EgyptFOSS" style="max-width:100%;height:auto;">
					</td>
				</tr>
								<tr>
					<td class="foss-mail-title" style="background-color:#4caf50;padding-top:25px;padding-bottom:25px;font-size:28px;color:#FFF;text-align:center;">
          <?php echo $template_inputs["title"] ?>
					</td>
</tr>