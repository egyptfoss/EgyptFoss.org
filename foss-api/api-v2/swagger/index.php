<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Swagger UI</title>
  <link rel="icon" type="image/png" href="images/favicon-32x32.png" sizes="32x32" />
  <link rel="icon" type="image/png" href="images/favicon-16x16.png" sizes="16x16" />
  <link href='css/typography.css' media='screen' rel='stylesheet' type='text/css'/>
  <link href='css/reset.css' media='screen' rel='stylesheet' type='text/css'/>
  <link href='css/screen.css' media='screen' rel='stylesheet' type='text/css'/>
  <link href='css/reset.css' media='print' rel='stylesheet' type='text/css'/>
  <link href='css/print.css' media='print' rel='stylesheet' type='text/css'/>
  <script src='lib/jquery-1.8.0.min.js' type='text/javascript'></script>
  <script src="//malsup.github.io/min/jquery.form.min.js"></script>
  <script src='lib/jquery.slideto.min.js' type='text/javascript'></script>
  <script src='lib/jquery.wiggle.min.js' type='text/javascript'></script>
  <script src='lib/jquery.ba-bbq.min.js' type='text/javascript'></script>
  <script src='lib/handlebars-2.0.0.js' type='text/javascript'></script>
  <script src='lib/underscore-min.js' type='text/javascript'></script>
  <script src='lib/backbone-min.js' type='text/javascript'></script>
  <script src='swagger-ui.js' type='text/javascript'></script>
  <script src='lib/highlight.7.3.pack.js' type='text/javascript'></script>
  <script src='lib/marked.js' type='text/javascript'></script>
  <script src='lib/swagger-oauth.js' type='text/javascript'></script>

  <!-- Some basic translations -->
  <!-- <script src='lang/translator.js' type='text/javascript'></script> -->
  <!-- <script src='lang/ru.js' type='text/javascript'></script> -->
  <!-- <script src='lang/en.js' type='text/javascript'></script> -->

  <script type="text/javascript">
    $(function () {
      var url = window.location.search.match(/url=([^&]+)/);
      if (url && url.length > 1) {
        url = decodeURIComponent(url[1]);
      } else {
        url = "foss-docs.json";
      }

      // Pre load translate...
      if(window.SwaggerTranslator) {
        window.SwaggerTranslator.translate();
      }
      window.swaggerUi = new SwaggerUi({
        url: url,
        dom_id: "swagger-ui-container",
        supportedSubmitMethods: ['get', 'post', 'put', 'delete', 'patch'],
        onComplete: function(swaggerApi, swaggerUi){
          if(typeof initOAuth == "function") {
            initOAuth({
              clientId: "your-client-id",
              clientSecret: "your-client-secret-if-required",
              realm: "your-realms",
              appName: "your-app-name", 
              scopeSeparator: ","
            });
            modifyTextAreas();
            modifyFileSubmit();
          }

          if(window.SwaggerTranslator) {
            window.SwaggerTranslator.translate();
          }

          $('pre code').each(function(i, e) {
            hljs.highlightBlock(e)
          });

          addApiKeyAuthorization();
          //$(".collapseResource").click();
        },
        onFailure: function(data) {
          log("Unable to Load SwaggerUI");
        },
        docExpansion: "none",
        apisSorter: "alpha",
        operationsSorter:"alpha",
        defaultModelRendering: 'schema',
        showRequestHeaders: false,
        validatorUrl : null,
      });

      function addApiKeyAuthorization(){
        var key = encodeURIComponent($('#input_apiKey')[0].value);
        if(key && key.trim() != "") {
            var apiKeyAuth = new SwaggerClient.ApiKeyAuthorization("api_key", key, "query");
            window.swaggerUi.api.clientAuthorizations.add("api_key", apiKeyAuth);
            swaggerUi.api.clientAuthorizations.add("x-api-key", new SwaggerClient.ApiKeyAuthorization("x-api-key", key, "header"));
            log("added key " + key);
        }
      }

      $('#input_apiKey').change(addApiKeyAuthorization);

      // if you have an apiKey you would like to pre-populate on the page for demonstration purposes...
      /*
        var apiKey = "myApiKeyXXXX123456789";
        $('#input_apiKey').val(apiKey);
      */

      window.swaggerUi.load();

      function log() {
        if ('console' in window) {
          console.log.apply(console, arguments);
        }
      }
  });
  
    function modifyTextAreas() {
        var cell = $("td.code label:contains('pageContent')").parent().parent().find("td:nth-child(2)");
        var oldInput = cell.find("input");
        cell.html('<textarea class="' + oldInput.attr("class")+ '" name="' + oldInput.attr("name")+ '" id="' + oldInput.attr("id")+ '"></textarea>');
    
        var arr_textAreasByName= ["description","audience","objectives","prerequisites","functionality","comment","reply"
                ,"references","usage_hints","requirements","constraints","content","open_dataset_description","post_description","conditions"];
        for(var i = 0; i < arr_textAreasByName.length; i++)
        {
            var cell = $("[name='"+arr_textAreasByName[i]+"']").parent().parent().find("td:nth-child(2)");
            var oldInput = cell.find("input");
            cell.html('<textarea class="body-textarea ' + oldInput.attr("class")+ '" name="' + oldInput.attr("name")+ '" id="' + oldInput.attr("id")+ '"></textarea>');
        }
    }
    
    function modifyFileSubmit() {
        $(".sandbox_header input").each(function() {
            var submitBtn = $(this);
//            submitBtn.parent().parent().prop("method","post");
//            submitBtn.parent().parent().prop("enctype","multipart/form-data");
//            fields = new Array();
//            submitBtn.parent().parent().unbind("submit");
            submitBtn.parent().parent().find("table:first tbody tr td:nth-child(2)").each(function() {
                if($(this).find("input").prop("type") == "file") {
                    if($(this).find("input").parent().parent().children(":first").hasClass("required")) {
                        //add multi part to the form
                        $(".sandbox").attr( 'enctype', 'multipart/form-data' );
                        $(this).find("input").attr( 'multiple', 'multiple' );
                        var name = $(this).find("input").prop("name");
                        name = name+'[]';
                        $(this).find("input").attr( 'name', name );
                    }
                    submitBtn.parent().parent().submit(function (e) {
                        submitFile($(this));
                        $(this).parent().find("div.response").show();
                        return false;
                    });
                    submitBtn.replaceWith("<input type='submit' value='Try'>");
                }
            });
        });
    }
    var currentForm;
    function submitFile(form) {
        currentForm = form;
        var submitUrl = form.parent().parent().find(".heading .path a").text(); // your upload script
        var methodType = form.parent().parent().find(".heading .http_method a").text(); // method type
        var language = form.find('label:contains("language")').parents("tr").find("input").val();
        var token = form.find('label:contains("token")').parents("tr").find("input").val();
        submitUrl = submitUrl.replace("{language}",language);
        
        //replace ids is exists
        var ids = ["product_id"]; 
        for (var i = 0; i < ids.length; i++) 
        {
            var itemInput = form.find('label:contains("'+ids[i]+'")').parents("tr").find("input").val();
            submitUrl = submitUrl.replace("{"+ids[i]+"}",itemInput);
        }
        var mydata = currentForm.serialize();
        var options = {
            // beforeSubmit: showRequest,
            success: showResponse,
            url: submitUrl,
            dataType: 'json',
            data: mydata,
            headers: {
                "x-api-key": encodeURIComponent($('#input_apiKey')[0].value),
                "token": token
            },
            type: 'POST'
        };       
        
        if(methodType === "put")
        {
            options.headers['X-HTTP-Method-Override'] = 'PUT';
        }
        form.ajaxSubmit(options);
        return false;
    }
    
    function showResponse(data, statusText) {
        if (statusText === 'success') {
            currentForm.parent().find('.response .block.response_body').html('<pre><code>'+JSON.stringify(data, null, 2)+'</pre></code>');
            currentForm = null;
        }
    }
  </script>
  <style>
    .alert {
        padding: 15px;
        border: 1px solid transparent;
        border-radius: 0;
        text-align: center;
        font-family: "Droid Sans", sans-serif;
    }
    .alert-success {
        color: #3c763d;
        background-color: #dff0d8;
        border-color: #d6e9c6;
    }
    </style>
</head>

<body class="swagger-section">
<div id='header'>
  <div class="swagger-ui-wrap">
    <a id="logo" href="http://swagger.io">swagger</a>
    <form id='api_selector'>
      <div class='input'><input placeholder="http://example.com/api" id="input_baseUrl" name="baseUrl" type="text"/></div>
      <div class='input'><input placeholder="api_key" id="input_apiKey" name="apiKey" type="text" value=""/></div>
      <div class='input'><a id="explore" href="#" data-sw-translate>Explore</a></div>
    </form>
  </div>
</div>
<?php include '../../../db-config.php'; ?> 
<div class="alert alert-success">
    <i class="fa fa-check"></i> Please request your API key from <a href="<?php echo EF_WEB_URL; ?>/en/feedback/add/" target="_blank">Here</a>
</div>
<div id="message-bar" class="swagger-ui-wrap" data-sw-translate>&nbsp;</div>
<div id="swagger-ui-container" class="swagger-ui-wrap"></div>
</body>
</html>
