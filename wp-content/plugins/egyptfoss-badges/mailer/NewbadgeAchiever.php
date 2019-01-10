<tr>
  <td class="foss-msg-body" style="text-align:center;padding:10px;
  <?php
    if ($lang == "ar") {
      echo 'direction:rtl';
    }
    ?>">
    <?php if (isset($template_inputs['user_name'])) { ?>
    <p style="text-align:center;">
    <?php echo $template_inputs["intro"]; ?>
    </p>
  <?php } ?>
  <p style="text-align:center;  <?php
    if ($lang == "ar") {
      echo 'direction:rtl';
    }
    ?>"><?php echo $template_inputs['message']; ?></p>
  <br>
<?php if (isset($template_inputs['btn_title'])) { ?>
    <div style="margin-left: 20px;margin-right: 20px;">
      <div class="btn btn--flat" style="margin-bottom: 20px;text-align: center;">
        <!--[if !mso]-->
        <a style="border-radius: 4px;display: inline-block;font-weight: bold;text-align: center;text-decoration: none !important;transition: opacity 0.1s ease-in;color: #fff;background-color: #4caf50;font-family: sans-serif;font-size: 14px;line-height: 24px;padding: 12px 35px;" href="<?php echo $template_inputs['btn_url']; ?>" target="_blank"><?php echo $template_inputs['btn_title']; ?></a><!--[endif]-->
      <!--[if mso]><p style="line-height:0;margin:0;">&nbsp;</p><a href="<?php echo $template_inputs['btn_url']; ?>" target="_blank"><v:roundrect xmlns:v="urn:schemas-microsoft-com:vml" href="<?php echo $template_inputs['btn_url']; ?>" style="width:262px" arcsize="9%" fillcolor="#4caf50" stroke="f"><v:textbox style="mso-fit-shape-to-text:t" inset="0px,11px,0px,11px"><center style="font-size:14px;line-height:24px;color:#FFFFFF;font-family:sans-serif;font-weight:bold;mso-line-height-rule:exactly;mso-text-raise:4px"><?php echo $template_inputs['btn_title']; ?></center></v:textbox></v:roundrect></a><![endif]--></div>
    </div>
<?php } ?>
</td>    
</tr>
