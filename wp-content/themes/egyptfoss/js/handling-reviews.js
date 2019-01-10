jQuery(document).ready(function ($) {
    submitted = false;
    triggered = false;
  $('.submit-review').on('click', function(e) {
    e.preventDefault();
    if($("#add_review").valid()) {
      review = $('textarea#review').val();
      rate = $('.rating-live').starRating('getRating');
      pid = $('#displayed_container_id').val();
      tid = $('#displayed_thread_id').val();
      nonce = $('#_wpnonce').val();
      var data = {
        action: 'ef_submit_review',
        security: nonce,
        rate: rate,
        review: review,
        pid: pid,
        tid: tid
      };
      $.ajax({
        type: 'POST',
        url: ajaxurl,
        data: data,
        success: function (res) {
          var result = JSON.parse(res);
          if(result.status == "success") {
            $('#review-submitted').show(); // Your review has been submitted
            $('#add-review').modal('hide');
            $('#show-all-reviews').html("("+result.reviewers_count+")");
            $('#average-rate').starRating('setRating', parseFloat(result.rate));
            $('#average-rate').attr('title', parseFloat(result.rate));
            $('#reviewer-section h3').html(result.section_header);
            $('.rating-live').starRating('setReadOnly', true);
            submitted = true;
          } else {
            window.location.href = '/?status=403';
          }
        }
      });
    }
  });
  var review_validator = $('#add_review').validate({
    onfocusout: false,
    focusInvalid: false,
    invalidHandler: function(form, validator) {
      if (!validator.numberOfInvalids())
        return;
    },
    rules: {
      review: {
        required: true,
        contain_at_least_one_letter: true,
      }
    },
    messages: {
      review: {
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

	function loadReviews() {
            $('.loading_reviews').removeClass('hidden');
	  var pid = $('#displayed_container_id').val();
	  var offset = parseInt($('#load_more_reviews').data("offset"));
	  var data = {
	    action: 'ef_show_more_reviews',
	    pid: pid,
	    offset: offset
	  };
	  $.ajax({
	    type: 'POST',
	    url: ajaxurl,
	    data: data,
	    success: function (res) {
	      var result = JSON.parse(res);
	      status = result.status;
	      if(status == "success") {
	        $('#service-reviews').append(result.output);
          $("#service-reviews .rating-readonly").starRating({
            starSize: 25,
            emptyColor: 'lightgray',
            hoverColor: '#74b977',
            activeColor: '#4caf50',
            strokeWidth: 0,
            useGradient: false,
            readOnly: true,
          });
                $('.loading_reviews').addClass('hidden');
	        $('#load_more_reviews').data("offset", result.offset);
	        if(!result.load_more) {
	          $('#load_more_reviews').hide();
	        }
                else {
                    $('#load_more_reviews').show();
                }
                $('#load_more_reviews').attr('disabled', false);
	      } else {
	        window.location.href = '/?status=403';
	      }
	    }
	  });
	}
	$('#show-all-reviews').on('click', function(e) {
	  e.preventDefault();
	  $('#service-reviews').html('');
	  $('#load_more_reviews').data("offset", 0);
	  loadReviews();
	});
	$('#load_more_reviews').on('click', function(e) {
            $(this).attr('disabled', true);
            loadReviews();
	});

  $('.cancel-review').on('click', function() {
    if(triggered) {
        triggered = false;
        return;
    }
    review_validator.resetForm();
    $('.live-rating').html('1');
    $('.rating-live').starRating('setRating', 1);
    $('.rating-live').starRating('setReadOnly', false);
    $('#add-review .form-group').removeClass('has-error').find('textarea').val('');
  });
  
  $('#add-review').on( 'hidden.bs.modal', function(){
    if( submitted ) {
        return;
    }
    if(triggered) {
        triggered = false;
        return;
    }
    review_validator.resetForm();
    $('.live-rating').html('1');
    $('.rating-live').starRating('setRating', 1);
    $('.rating-live').starRating('setReadOnly', false);
    $('#add-review .form-group').removeClass('has-error').find('textarea').val('');
  });

  $( '#reviewer-section' ).on('click', '.rating-live', function() {
   rate_clicked = $('.rating-live').starRating('getRating');
   if (rate_clicked < 0.5 ) {
    $('.rating-live').starRating('setRating', 0.5);
    $('.live-rating').html(0.5);
   }
  });

});