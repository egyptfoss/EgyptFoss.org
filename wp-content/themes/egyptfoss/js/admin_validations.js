(function ($) {
  $(document).ready(function () {
    $('body').on('submit', '#post', function () {
      title = $('#title').val();
      id = $('#post_ID').val();
      post_type = $('#post_type').val();
      titleLength = $("#title").val().replace(/ /g, '').length;
      special_char_contained = $("#title").val().search(/[أ-يa-zA-Z\:]{1,}/gm);
      is_numbers_only = $("#title").val().search(/^[0-9]{1,}$/);
      titleExists = 0;
      errorMessage = "";
      cpt_title_can_be_duplicated = ['request_center','success_story','news','expert_thought', 'service'];
      if(! $.inArray(post_type, cpt_title_can_be_duplicated) > -1) {
        titleExists = checkTitle(title, id, post_type);
      }
      $("form#post").parent().find(".customError").remove();

      startErrorStructure = "<div id='notice' class='error customError'><ul>";
      endErrorStructure = "</ul></div>";
      errorMessage = "";
      if (titleExists == 1) {
        errorMessage += "<li><p>"+ post_type.charAt(0).toUpperCase() + post_type.slice(1).replace('_', ' ') +" already exists</p></li>";
      }
      if (titleLength == 0) {
        errorMessage += "<li><p>"+ post_type.charAt(0).toUpperCase() + post_type.slice(1).replace('_', ' ') +" title is required</p></li>";
      } else {
        if (is_numbers_only >= 0) {
          errorMessage += "<li><p>title must at least contain one letter</p></li>";
        } else {
          if (special_char_contained < 0) {
            errorMessage += "<li><p>title must at least contain one letter</p></li>";
          }
        }
      }

      if(titleExists == 1 ||titleLength == 0||is_numbers_only >= 0||special_char_contained < 0) {
        $("#title").css({"border": "1px solid red"});
        $("#title").focus();
      }

      if(post_type == "news") {
        errorMessage = checkNewsValidations(errorMessage);
      } else if(post_type == "product") {
        errorMessage = checkProductValidation(errorMessage);
      } else if(post_type == "success_story") {
        errorMessage =  checkSuccessStoryValidation(errorMessage);
      } else if(post_type == "feedback") {
        errorMessage =  checkFeedbackValidation(errorMessage);
      } else if(post_type == "service") {
        errorMessage =  checkServiceValidations(errorMessage);
      } else if(post_type == "partner") {
        errorMessage = checkPartnersValidations(errorMessage);
      }


      if(errorMessage != "") {
        $("form#post").prepend(startErrorStructure + errorMessage + endErrorStructure);
        $('#major-publishing-actions .spinner').hide();
        $('#major-publishing-actions').find(':button, :submit, a.submitdelete, #post-preview').removeClass('disabled');
        $('body,html').animate({scrollTop: (0)}, 1000);
        return false;
      }
    });

    // alert when deleting post type
    delete_message = "Are you sure you want to take this action?";
    $('#doaction,#doaction2,#delete_all').click(function (e) {
      bulk_action = "";
      currentTarget = e.currentTarget.attributes.id.value;
      if (currentTarget == "doaction") {
        bulk_action = $(this).parent().find("#bulk-action-selector-top").val();
      }
      if (currentTarget == "doaction2") {
        bulk_action = $(this).parent().find("#bulk-action-selector-bottom").val();
      }
      if (bulk_action == "delete" || bulk_action == "trash" || currentTarget == "delete_all") {
          if(currentTarget == "delete_all") {
            if (!confirm(delete_message)) {
              e.preventDefault();
            }
          } else {
          e.preventDefault();
          if (confirm(delete_message)) {
            $("#posts-filter").submit();
          } else {
            return false;
          }
        }
      }
    });

    $('a.submitdelete').click(function (e) {
      e.preventDefault();
      if (confirm(delete_message)) {
        window.location.href = e.target.href;
      } else {
        return false;
      }
    });

    function checkTitle(title, id, post_type) {
      //var ajaxurl = 'wp-admin/admin-ajax.php';
      var data = {
        action: 'title_check',
        post_title: title,
        post_type: post_type,
        post_id: id
      };
      result = false;
      //check if station is alive
      $.ajax({
        async: false,
        url: ajaxurl,
        data: data,
        type: "POST",
        success: function (resp) {
          result = resp;
        },
        error: function (e) {
          result = false;
        }
      });
      return result;
    }

    $('body').on('submit', '#edittag', function (e) {
      // e.preventDefault();
      // alert($("input[name='taxonomy']").val());
      var data = {
        action: 'tag_title_check',
        name: $("input[name='name']").val(),
        term_id: $("input[name='tag_ID']").val(),
        taxonomy: $("input[name='taxonomy']").val()
      };
      result = 0;
      $.ajax({
        async: false,
        url: ajaxurl,
        data: data,
        type: "POST",
        success: function (resp) {
          if (resp == "1")
          {
            result = 1;
            startErrorStructure = "<div id='notice' class='error customError'><ul>";
            endErrorStructure = "</ul></div>";
            errorMessage = "<li><p>A term with the name provided already exists in this taxonomy.</p></li>";
            $("form#edittag").prepend(startErrorStructure + errorMessage + endErrorStructure);
            $("input[name='name']").css({"border": "1px solid red"});
            $("#title").focus();
            $('body,html').animate({scrollTop: (0)}, 1000);
          }
        },
        error: function (e) {
          result = 0;
        }
      });
      if (result == 1)
      {
        return false;
      }
    });

    function checkNewsValidations(message) {
      if((title > 100) && titleLength != 0) {
        message += "<li><p>title should not be more than 100 characters</p></li>";
      }

      if((title < 10 ) && titleLength != 0) {
        message += "<li><p>title should be at least 10 characters</p></li>";
      }

      for (var i = 0; i < tinyMCE.editors.length; i++) {
        if (tinyMCE.editors[i].id.indexOf("wysiwyg-acf-field-description") > -1) {
          var description = tinyMCE.editors[i].getContent();
          description = description.replace(/<(?:.|\n)*?>/gm, '');
          desc_special_char_contained = description.search(/[أ-يa-zA-Z\:]{1,}/gm);
          desc_is_numbers_only = description.search(/^[0-9]{1,}$/);
          if (description.length <= 0 ) {
            message += "<li><p>description is required</p></li>";
          } else {
            if ( (desc_is_numbers_only >= 0 || desc_special_char_contained < 0)) {
              message += "<li><p>description must at least contain one letter</p></li>";
            }
          }
        }
      }

      if ( admin_obj.is_testing != 1 && jQuery("#set-post-thumbnail").find('img').size() == 0) {
          message += "<li><p>Featured image is required</p></li>";
      }

      return message;
    }

    function checkPartnersValidations(message) {
      if((title > 100) && titleLength != 0) {
        message += "<li><p>title should not be more than 100 characters</p></li>";
      }

      if ( admin_obj.is_testing != 1 && jQuery("#set-post-thumbnail").find('img').size() == 0) {
          message += "<li><p>Featured image is required</p></li>";
      }

      return message;
    }

    function checkSuccessStoryValidation(message) {
      if((title > 100) && titleLength != 0) {
        message += "<li><p>title should not be more than 100 characters</p></li>";
      }

      if((title < 10 ) && titleLength != 0) {
        message += "<li><p>title should be at least 10 characters</p></li>";
      }

      var description =  jQuery("#content").val();
      if(description == '') {
        description = tinymce.activeEditor.getContent();
      }

      description = description.replace(/<(?:.|\n)*?>/gm, '');
      desc_special_char_contained = description.search(/[أ-يa-zA-Z\:]{1,}/gm);
      desc_is_numbers_only = description.search(/^[0-9]{1,}$/);
      if (description.length <= 0 ) {
          message += "<li><p>description is required</p></li>";
      } else {
        if ( (desc_is_numbers_only >= 0 || desc_special_char_contained < 0)) {
          message += "<li><p>description must at least contain one letter</p></li>";
        }
      }
      return message;
    }

    function checkFeedbackValidation(message){
      if((title > 100) && titleLength != 0){
        message += "<li><p>title should not be more than 100 characters</p></li>";
      }

      if((title < 10 ) && titleLength != 0){
        message += "<li><p>title should be at least 10 characters</p></li>";
      }

      var description =  jQuery("#content").val();
      if(description == '')
        description = tinymce.activeEditor.getContent();

      description = description.replace(/<(?:.|\n)*?>/gm, '');
      desc_special_char_contained = description.search(/[أ-يa-zA-Z\:]{1,}/gm);
      desc_is_numbers_only = description.search(/^[0-9]{1,}$/);
      if (description.length <= 0 ){
        message += "<li><p>description is required</p></li>";
      } else {
        if ( (desc_is_numbers_only >= 0 || desc_special_char_contained < 0)){
          message += "<li><p>description must at least contain one letter</p></li>";
        }
      }
      return message;
    }

    function checkProductValidation(message) {
      developer_special_char = $("#acf-field-developer").val().length;
      func_special_char = $("#acf-field-functionality").val().length;
      usage_special_char = $("#acf-field-usage_hints").val().length;
      references_special_char = $("#acf-field-references").val().length;
      link_special_char = $("#acf-field-link_to_source").val().length;
      validate_dev_field = $("#acf-field-developer").val().search(/[أ-يa-zA-Z]{1,}/g);
      validate_func_field = $("#acf-field-functionality").val().search(/[أ-يa-zA-Z]{1,}/g);
      validate_usage_field = $("#acf-field-usage_hints").val().search(/[أ-يa-zA-Z]{1,}/g);
      validate_references_field = $("#acf-field-references").val().search(/[أ-يa-zA-Z]{1,}/g);
      validate_link_field = $("#acf-field-link_to_source").val().search(/^(https?):\/\/[^ ]+\.[^ ]+$/);
      add_product_description = $('#acf-field-description').val();
      validate_desc_field = $("#acf-field-description").val().search(/[أ-يa-zA-Z\:]{1,}/gm);
      if(validate_desc_field < 0) {
        message += "<li><p>"+ post_type +" description must at least contain one letter</p></li>";
      }
      if(developer_special_char !== 0 && validate_dev_field < 0) {
        message += "<li><p>Product developer must at least contain one letter</p></li>";
      }
      if(func_special_char !== 0 && validate_func_field < 0) {
        message += "<li><p>Product functionality must at least contain one letter</p></li>";
      }
      if(usage_special_char !== 0 && validate_usage_field < 0) {
        message += "<li><p>Product usage hints must at least contain one letter</p></li>";
      }
      if(references_special_char !== 0 && validate_references_field < 0) {
        message += "<li><p>Product references must at least contain one letter</p></li>";
      }
      if(link_special_char !== 0 && validate_link_field < 0) {
        message += "<li><p>Please enter a valid link to source</p></li>";
      }
      return message;
    }

    function checkServiceValidations(message) {
      for (var i = 0; i < tinyMCE.editors.length; i++) {
        if (tinyMCE.editors[i].id.indexOf("wysiwyg-acf-field-description") > -1) {
          var description = tinyMCE.editors[i].getContent();
          description = description.replace(/<(?:.|\n)*?>/gm, '');
          desc_special_char_contained = description.search(/[أ-يa-zA-Z\:]{1,}/gm);
          desc_is_numbers_only = description.search(/^[0-9]{1,}$/);
          if (description.length <= 0 ) {
            message += "<li><p>description is required</p></li>";
          } else {
            if ( (desc_is_numbers_only >= 0 || desc_special_char_contained < 0)) {
              message += "<li><p>description must at least contain one letter</p></li>";
            }
          }
        }
      }
      return message;
    }
  });
}(jQuery));
