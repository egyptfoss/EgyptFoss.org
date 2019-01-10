jQuery(document).ready(function ($) {
  $('.archive-request').on('click', function() {
    id = $(this).attr('id');
    var data = {
      action: 'ef_archive_request',
      id: id,
    };
    $.ajax({
      type: 'POST',
      url: ajaxurl,
      data: data,
      success: function (response) {
        var result = JSON.parse(response);
        if(result.status == 'success') {
          $('.request-archived').show();
          $('.archive-request-button').addClass('disabled').removeAttr('data-target').removeAttr('data-toggle');
          $('div.respond-btns').remove();
          $('div.respond-btns-parent').remove();
        } else {
          $('.request-not-archived').show();
        }
      }
    });
  });
  $('.archive-my-thread').on('click', function() {
    id = $(this).attr('id');
    var data = {
      action: 'ef_archive_thread',
      id: id,
    };
    $.ajax({
      type: 'POST',
      url: ajaxurl,
      data: data,
      success: function (response) {
        var result = JSON.parse(response);
        if(result.status == 'success') {
          $('.thread-archived').show();
          $('.archive-thread-button').addClass('disabled').removeAttr('data-target').removeAttr('data-toggle');
          $('form#add_response').remove();
        } else {
          $('.thread-not-archived').show();
        }
      }
    });
  });
  $('#submit_response').on('click', function(e) {
    e.preventDefault();
    if($("#add_response").valid()) {
      document.getElementById('submit_response').disabled = true;
      msg = $('textarea#message').val();
      pid = $('#displayed_container_id').val();
      tid = $('#displayed_thread_id').val();
      nonce = $('#_wpnonce').val();
      var data = {
        action: 'ef_submit_response',
        security: nonce,
        msg: msg,
        pid: pid,
        tid: tid
      };
      $.ajax({
        type: 'POST',
        url: ajaxurl,
        data: data,
        success: function (res) {
          var result = JSON.parse(res);
          send = result.message;
          if(send == ''){
            $('.empty-state-thread').hide();
            $('textarea#message').val('');
            me = $('#me-myself').val();
            $('.conv-thread').append('<div class="response-row me"><p><span class="user-name">'+me+' :</span>'+msg+'</p><div class="message-time-stamp"><i class="fa fa-clock-o"></i> '+result.date+'</div></div>');
            $('.conv-thread').scrollTop($('.conv-thread')[0].scrollHeight);
            // Rating
            if(result.can_rate)
            {
                $('#dimmed-rate').removeClass('rating-readonly').addClass('rating-live'); 
                $('.rating-live').starRating('setReadOnly', false);
                $('.your-rate').removeClass('your-rate').addClass('live-rating').html('1');
                $('.rating-live').starRating('setRating', 1);
            }
          } else {
            window.location.href = '/?status=403';
          }
          document.getElementById('submit_response').disabled = false;
        }
      });
    }
  });
  $('#add_response').validate({
    onfocusout: function (element) {
      $(element).valid();
    },
    focusInvalid: false,
    invalidHandler: function(form, validator) {
      if (!validator.numberOfInvalids())
        return;
      $('html, body').animate({
        scrollTop: $(validator.errorList[0].element).offset().top - 100
      }, 500);
    },
    rules: {
      message: {
        required: true,
        contain_at_least_one_letter: true,
      }
    },
    messages: {
      message: {
        required: $.validator.messages.required_field,
        contain_at_least_one_letter: $.validator.messages.contain_at_least_one_letter
      }
    },
    // Fire bootstrap errors
    highlight: function(element) {
      if (element.type !== 'search') {
        $(element).closest('.form-group').addClass('has-error');
      }
    },
    unhighlight: function(element) {
      if (element.type !== 'search') {
        $(element).closest('.form-group').removeClass('has-error');
      }
    },
    errorElement: 'span',
    errorClass: 'error',
    errorPlacement: function(error, element) {
      if(element.parent('.form-group').length) {
        error.insertAfter(element.parent());
      } else {
        error.insertAfter(element);
      }
    }
  });
});