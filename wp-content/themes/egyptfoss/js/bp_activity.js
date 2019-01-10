(function ($) {
  $(document).ready(function () {
    $(document).ajaxComplete(function (event, xhr, settings) {
      var action = getQueryParamValue("action", settings.data);
      if (action == "new_activity_comment" || action == "delete_activity_comment")
      {
        var form_id = 0;
        if (action == "new_activity_comment")
        {
          form_id = getQueryParamValue("form_id", settings.data);
        } else
        {
          form_id = $(event.currentTarget.activeElement).attr("data-activity-id");
        }

        var data = {
          action: 'ef_bp_activity_get_comment_count',
          activity_id: form_id,
        };
        jQuery.ajax({
          type: 'POST',
          url: ajaxurl,
          data: data,
          async: false,
          success: function (data) {
            $("#ef_bp_activity_comment_count_" + form_id).html( (data == 0)? '' : data );
          }
        });
      }
      //alert(getQueryParamValue("form_id",settings.data));
    });
    $(".ef_activity_like_unlike").click(function(e){
      $(this).attr("disabled","");
      e.preventDefault();
      activity_id = $(this).attr("data-activity-id");
      is_like = $(this).attr("data-is-like");
      var data = {
        action: 'ef_activity_like_unlike',
        activity_id: activity_id,
        is_like: is_like
      };
      jQuery.ajax({
        type: 'POST',
        url: ajaxurl,
        data: data,
        async: false,
        success: function (data) {
          if(is_like == "1")
          {
            $("#ef_activity_like_unlike_"+activity_id).attr("data-is-like","0");
            $("#ef_activity_like_unlike_"+activity_id).removeAttr("disabled");
            $("#ef_activity_like_unlike_"+activity_id).html($.validator.messages.Dislike);
            $("#ef_activity_like_unlike_"+activity_id).removeAttr("title");
            $("#ef_activity_like_unlike_"+activity_id).prop("title",$.validator.messages.Dislike);
          }else {
            $("#ef_activity_like_unlike_"+activity_id).attr("data-is-like","1");
            $("#ef_activity_like_unlike_"+activity_id).removeAttr("disabled");
            $("#ef_activity_like_unlike_"+activity_id).html($.validator.messages.Like);
            $("#ef_activity_like_unlike_"+activity_id).removeAttr("title");
            $("#ef_activity_like_unlike_"+activity_id).prop("title",$.validator.messages.Like);
          }
          data = JSON.parse(data);
          $(".likes-count-"+activity_id).html( ( data.fav_count == 0 )? '' : data.fav_count );
          $('ul.likes-'+activity_id).html(data.liked_user_list.content);
          //alert(data);
        }
      });
      $("#ef_activity_like_unlike_"+activity_id).removeAttr("disabled");
      return false;
    });
    function getQueryParamValue(paramName, queryString)
    {
      var data = queryString.split("&");
      for (var i = 0; i < data.length; i++)
      {
        var param = data[i].split("=");
        if (param[0] == paramName)
        {
          return param[1];
        }
      }
      return false;
    }
  });
}(jQuery));