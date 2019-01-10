(function ($) {
  $(document).ready(function () {
    $('body').on('submit.edit-post', '#post', function (e) {
      title = $('#title').val();
      id = $('#post_ID').val();
      post_type = $('#post_type').val();
      titleLength = $("#title").val().replace(/ /g, '').length;
      special_char_contained = $("#title").val().search(/[أ-يa-zA-Z\:]{1,}/gm);
      func_special_char = $("#acf-field-functionality").val().length;
      is_numbers_only = $("#title").val().search(/^[0-9]{1,}$/);
      validate_func_field = $("#acf-field-functionality").val().search(/[أ-يa-zA-Z]{1,}/g);

      $("form#post").parent().find(".customError").remove();

      if (titleLength === 0 || is_numbers_only >= 0 || special_char_contained < 0 || validate_desc_field < 0 || (func_special_char !== 0 && validate_func_field < 0) ) {
        startErrorStructure = "<div id='notice' class='error customError'><ul>";
        endErrorStructure = "</ul></div>";
        errorMessage = "";
        if (titleLength === 0) {
          errorMessage += "<li><p>Event title is required</p></li>";
        } else {
          if (is_numbers_only >= 0) {
            errorMessage += "<li><p>Event title must at least contain one letter</p></li>";
          } else {
            if (special_char_contained < 0) {
              errorMessage += "<li><p>Event title must at least contain one letter</p></li>";
            }
          }
        }
        if (func_special_char !== 0 && validate_func_field < 0){
          errorMessage += "<li><p>Event functionality must at least contain one letter</p></li>";
        }
        errorMessage += "<li><p>Event saved as draft.</p></li>";

        $("form#post").prepend(startErrorStructure + errorMessage + endErrorStructure);
        $('#major-publishing-actions .spinner').hide();
        $('#major-publishing-actions').find(':button, :submit, a.submitdelete, #post-preview').removeClass('disabled');
        $("#title").css({"border": "1px solid red"});
        $("#title").focus();
        return false;
      }
    });
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
            if(currentTarget == "delete_all")
            {
                if (!confirm(delete_message)) {
                  e.preventDefault();
                }
            }else
            {
                e.preventDefault();
                if(confirm(delete_message)) {
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
      var data = {
        action: 'title_check',
        post_title: title,
        post_type: post_type,
        post_id: id
      };
      result = false;
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
  });
}(jQuery));