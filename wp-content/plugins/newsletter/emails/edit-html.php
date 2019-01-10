<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.20.2/codemirror.css" type="text/css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.20.2/addon/hint/show-hint.css">
<style>
    .CodeMirror {
        height: 600px;
    }
</style>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.20.2/codemirror.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.20.2/mode/xml/xml.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.20.2/mode/css/css.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.20.2/mode/javascript/javascript.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.20.2/mode/htmlmixed/htmlmixed.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.20.2/addon/hint/show-hint.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.20.2/addon/hint/xml-hint.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.20.2/addon/hint/html-hint.js"></script>
<script>
    var templateEditor;
    jQuery(function () {
        templateEditor = CodeMirror.fromTextArea(document.getElementById("options-message"), {
            lineNumbers: true,
            mode: 'htmlmixed',
            lineWrapping: true,
            extraKeys: {"Ctrl-Space": "autocomplete"}
        });
        templateEditorAr = CodeMirror.fromTextArea(document.getElementById("options-message_ar"), {
            lineNumbers: true,
            mode: 'htmlmixed',
            lineWrapping: true,
            extraKeys: {"Ctrl-Space": "autocomplete"}
        });
    });
</script>

<input class="button-primary" type="button" onclick="newsletter_textarea_preview('options-message', templateEditor); return false;" value="Switch editor/preview">

<input type="button" class="button-primary" value="Add media" onclick="tnp_media(templateEditor)">

<br><br>
<?php $controls->textarea_preview('message', '100%', 700, '', '', false); ?>
<br><br>
<h1>Raw HTML ( AR )</h1>
<input class="button-primary" type="button" onclick="newsletter_textarea_preview('options-message_ar', templateEditorAr); return false;" value="Switch editor/preview">
<input type="button" class="button-primary" value="Add media" onclick="tnp_media(templateEditorAr)" style="margin-bottom: 10px;">
<br>
<?php 
  
  $controls->textarea_preview('message_ar', '100%', 700, '', '', false); 
?>