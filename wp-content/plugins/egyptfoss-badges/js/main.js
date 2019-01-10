jQuery(document).ready(function ($) {
 
     var data = {
      action: 'updateNotificationStatus',
    };
    jQuery.ajax({
      type: 'POST',
      url: ajaxurl,
      data: data,
      success: function (data) {
        //retrieve data  
         result = JSON.parse(data);
        if (result.status >= 1)
        {
          $('.achievement-modal').modal();
          var sound = $('#notification-sound');
          sound.get(0).play();   
        }
      }
    });
});