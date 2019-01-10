jQuery.fn.exists = function() { return this.length > 0; }

jQuery(document).ready(function ($) {
  var share_titles = ['Facebook','Twitter','Linkedin','Googleplus'];
  for (var i=0;i < share_titles.length;i++)
  {
    $(".heateorSss"+share_titles[i]+"Background").attr("alt",$.validator.messages[share_titles[i]]);
    $(".heateorSss"+share_titles[i]+"Background").attr("title",$.validator.messages[share_titles[i]]);
  }

  var newValue = $("a.tribe-events-ical").text().replace('+', '');
  $("a.tribe-events-ical").text( newValue );

	//add js class to html
	$('html').addClass('js');
	 $('[data-toggle="tooltip"]').tooltip()

	// Display image uploader

	$(document).on('click', '#close-preview', function () {
		$('.image-preview').popover('hide');
		// Hover befor close the preview
		$('.image-preview').hover(
			function () {
				$('.image-preview').popover('show');
			},
			function () {
				$('.image-preview').popover('hide');
			}
		);
	});

				if($('body').hasClass('rtl')) {
var $slidedir = true

} else {

	var $slidedir = false

}

	var closebtn = $('<button/>', {
		type: "button",
		text: 'x',
		id: 'close-preview',
		style: 'font-size: initial;',
	});
	closebtn.attr("class", "close pull-right");
	// Set the popover default content
	$('.image-preview').popover({
		trigger: 'manual',
		html: true,
		title: "<strong>"+ $.validator.messages.Preview +"</strong>" + $(closebtn)[0].outerHTML,
		content: $.validator.messages.There_is_no_image,
		placement: 'bottom'
	});
	// Clear event
	$('.image-preview-clear').click(function () {
		$('.image-preview').attr("data-content", "").popover('hide');
		$('.image-preview-filename').val("");
		$('.image-preview-clear').hide();
		$('.image-preview-input input:file').val("");
		$(".image-preview-input-title").text($.validator.messages.Browse);
	});
	// Create the preview image
	$(".image-preview-input input:file").change(function () {
		var img = $('<img/>', {
			id: 'dynamic',
			width: 250,
			height: 200
		});
		var file = this.files[0];
		var reader = new FileReader();
		// Set preview image into the popover data-content
		reader.onload = function (e) {
			$(".image-preview-input-title").text($.validator.messages.Change);
      $(".image-preview-clear").show();
      $(".image-preview-filename").val(file.name);
      img.attr('src', e.target.result);
      if (file.type.match('image.*')) {
        $(".image-preview").attr("data-content", $(img)[0].outerHTML).popover("show");
      }
		}
		reader.readAsDataURL(file);
	});

if(efLocalizedVars.is_front == 0) {
	if ($(".technologies").exists()) {
		$(".technologies").select2({
        language: {
          noResults: function () {
            return jQuery.validator.messages.select2_no_results;
          }
        }
		});
	}

	if ($(".add-product-tax").exists()) {
		$(".add-product-tax").select2({
			multiple: true,
      language: {
        noResults: function () {
          return jQuery.validator.messages.select2_no_results;
        }
      }
		});
	}

	if ($("#venue, #organizer").exists()) {
		$("#venue, #organizer").select2({
			multiple: false,
			allowClear: true,
      language: {
        noResults: function () {
          return jQuery.validator.messages.select2_no_results;
        }
      }
		});
	}

	// Popup
	if ($(".data-url-image-link").exists()) {
		$('.data-url-image-link').magnificPopup({
                type: 'image',
                zoom: {
			enabled: true,
			duration: 300 // don't foget to change the duration also in CSS
		}
		});
	}

	if ($(".data-url-gallery-link").exists()) {
		$('.data-url-gallery-link').magnificPopup({
                type: 'image',
                delegate: 'a',
                gallery: {
			enabled: true,
		},
                zoom: {
			enabled: true,
			duration: 300 // don't foget to change the duration also in CSS
		}
		});
	}

  if ($(".image-link").exists()) {
		$('.image-link').magnificPopup({
		  type: 'image',
		  gallery:{
		    enabled:true
		  },
			zoom: {
			enabled: true,
			duration: 300 // don't foget to change the duration also in CSS
		}
		});
	}
	// Rating
	$(".rating-live").starRating({
		starSize: 18,
    disableAfterRate: false,
    emptyColor: 'lightgray',
    hoverColor: '#74b977',
    activeColor: '#4caf50',
    initialRating: 1,
    strokeWidth: 0,
    useGradient: false,
    onHover: function(currentIndex, currentRating, $el){
      $('.live-rating').text(currentIndex);
    },
    onLeave: function(currentIndex, currentRating, $el){
      $('.live-rating').text(currentRating);
    },
    callback: function(currentRating, $el){
    	$('#add-review').modal('show');
  	}
  });

	$(".rating-readonly").starRating({
            starSize: 25,
            emptyColor: 'lightgray',
            hoverColor: '#74b977',
            activeColor: '#4caf50',
            strokeWidth: 0,
            useGradient: false,
            readOnly: true,
            onHover: function(currentIndex, currentRating, $el){
                $('.live-rating').text(currentIndex);
            },
            onLeave: function(currentIndex, currentRating, $el){
                $('.live-rating').text(currentRating);
            },
            callback: function(currentRating, $el){
                $('#add-review').modal('show');
            }
        });
	//End

	$( ".open-list" ).click(function() {
	 $('.collapsable-list').toggleClass('show-menu');
	});

	//event date picker
 	if ($(".date-picker").exists()) {
		$('.date-picker').datetimepicker({
     	format: 'YYYY-MM-DD HH:mm:ss',
      locale: moment.locale(efLocalizedVars.current_lang),
			icons: {
				time: 'fa fa-clock-o',
				date: 'fa fa-calendar-o',
				up: 'fa fa-chevron-up',
				down: 'fa fa-chevron-down',
				previous: 'fa fa-chevron-left',
				next: 'fa fa-chevron-right',
				clear: 'fa fa-remove',
				close: 'fa fa-remove',
			},
      tooltips: {
        today: $.validator.messages.today,
        clear: $.validator.messages.clear,
        close: $.validator.messages.close,
        selectMonth: $.validator.messages.selectMonth,
        prevMonth: $.validator.messages.prevMonth,
        nextMonth: $.validator.messages.nextMonth,
        selectYear: $.validator.messages.selectYear,
        prevYear: $.validator.messages.prevYear,
        nextYear: $.validator.messages.nextYear,
        selectDecade: $.validator.messages.selectDecade,
        prevDecade: $.validator.messages.prevDecade,
        nextDecade: $.validator.messages.nextDecade,
        prevCentury: $.validator.messages.prevCentury,
        nextCentury: $.validator.messages.nextCentury,
        pickHour: $.validator.messages.pickHour,
        incrementHour:$.validator.messages.incrementHour,
        decrementHour:$.validator.messages.decrementHour,
        pickMinute:$.validator.messages.pickMinute,
        incrementMinute:$.validator.messages.incrementMinute,
        decrementMinute:$.validator.messages.decrementMinute,
        pickSecond:$.validator.messages.pickSecond,
        incrementSecond:$.validator.messages.incrementSecond,
        decrementSecond:$.validator.messages.decrementSecond,
        togglePeriod:$.validator.messages.togglePeriod,
        selectTime:$.validator.messages.selectTime,
      }
		});
	}

	if ($(".tribe-events-content,.expand-text").exists()) {
		if($('body').hasClass('rtl')) {
			$('.tribe-events-content,.expand-text').readmore({
				speed: 75,
				collapsedHeight:150,
				moreLink: '<a href="#">قراءة المزيد ...</a>',
				lessLink: '<a href="#">قراءة أقل...</a>'
			});
		} else {
			$('.tribe-events-content,.expand-text').readmore({
				speed: 75,
				collapsedHeight:150,
                                moreLink: '<a href="#"> Read More...</a>',
                                lessLink: '<a href="#"> Read Less...</a>'
			});
		}
	}

        if ($(".expand-text-large").exists()) {
            if($('body').hasClass('rtl')) {
                $('.expand-text-large').readmore({
                    speed: 75,
                    collapsedHeight:250,
                    moreLink: '<a href="#">قراءة المزيد ...</a>',
                    lessLink: '<a href="#">قراءة أقل...</a>'
                });
            } else {
                $('.expand-text-large').readmore({
                    speed: 75,
                    collapsedHeight:250,
                    moreLink: '<a href="#"> Read More...</a>',
                    lessLink: '<a href="#"> Read Less...</a>'
                });
            }
	}
}


	$('[data-toggle="tooltip"]').tooltip();


	//Product image carousel

	$("#product-images").owlCarousel({
		rtl:$slidedir,
		items:1,
		navContainerClass:'owl-buttons',
		smartSpeed:60,
		singleItem: true,
		nav:true,
		navText:'',
	});

  $(".events-list--upcoming").owlCarousel({
   items: 2,
			nav:true,
			rtl:$slidedir,
			slideBy:2,
			dotsEach:2,
			navText:'',
			navContainerClass:'owl-buttons',
			responsive : {
    // breakpoint from 0 up
    0 : {
       items: 1,
					  nav:false,
					  	dotsEach:1,
					   slideBy:1
    },
    // breakpoint from 480 up
    480 : {
         items: 1,
					  nav:false,
					  	dotsEach:1,
					   slideBy:1
    },
    // breakpoint from 768 up
    768 : {
   items: 2,
					navText:'',
			nav:true,
    }
}
	});


		$(".home-products .products,.news-list-loop").owlCarousel({
   items: 4,
			nav:true,
	slideBy:4,
			rtl:$slidedir,
			navText:false,
			smartSpeed:60,
			dotsEach:4,
			navContainerClass:'owl-buttons',
			responsive : {
    // breakpoint from 0 up
    0 : {
       items: 1,
					  nav:false,
					  	dotsEach:1,
					   slideBy:1
    },
    // breakpoint from 480 up
    480 : {
         items: 1,
					  nav:false,
					  	dotsEach:1,
					   slideBy:1
    },
    // breakpoint from 768 up
    768 : {
   items: 4,
			nav:true,
    }
}
	});

	$(".default-carousel").owlCarousel({
   items: 5,
				margin:10,
				stagePadding:60,
			nav:true,
	slideBy:5,
			rtl:$slidedir,
			navText:false,
			smartSpeed:60,
				dots:false,
			navContainerClass:'owl-buttons',
			responsive : {
    // breakpoint from 0 up
    0 : {
       items: 1,
					  nav:false,
					  	dotsEach:1,
					   dots:true,
					   slideBy:1
    },
    // breakpoint from 480 up
    480 : {
         items: 2,
					  nav:false,
					  	dotsEach:2,
					   dots:true,
					   slideBy:2
    },
    // breakpoint from 768 up
    768 : {
		items: 5,
			nav:true,
    }
}
	});

    // Market place carousel
    	$(".mp-carousel").owlCarousel({
   items: 4,
				margin:0,
				stagePadding:60,
			nav:true,
	slideBy:4,
			rtl:$slidedir,
			navText:false,
			smartSpeed:60,
				dots:false,
			navContainerClass:'owl-buttons',
			responsive : {
    // breakpoint from 0 up
    0 : {
       items: 1,
					  nav:false,
					  	dotsEach:1,
					   dots:true,
					   slideBy:1
    },
    // breakpoint from 480 up
    480 : {
         items: 2,
					  nav:false,
					  	dotsEach:2,
					   dots:true,
					   slideBy:2
    },
    // breakpoint from 768 up
    768 : {
		items: 4,
			nav:true,
    }
}
	});

// Reset Password - Setting
$('#change-email').click(function(){
$('.change-password').addClass('hidden');
	$('.change-email').removeClass('hidden');
		$('.chng-pass').parent('li').removeClass('active');
	$('.chng-email').parent('li').addClass('active');
});

$('#change-pass').click(function(){
$('.change-password').removeClass('hidden');
	$('.change-email').addClass('hidden');
	$('.chng-pass').parent('li').addClass('active');
	$('.chng-email').parent('li').removeClass('active');
});

	/*Notifications Page Inline Edit*/
			$('.edit-field').click(function(){
$(this).parent().addClass('hidden');
				$(this).parent().parent().find('.save-select').removeClass('hidden');
			//$(this).addClass('hidden');
});

	$('#mobile-menu .menu-item-has-children a').on('click', function () {
    $(this).parent().find('.sub-menu').slideToggle();
	});

	// map filter

	$('.close-filter-btn').click(function(){
			$('.filter-map').slideToggle();
		$("i",this).toggleClass("fa-angle-down fa-angle-up ");
		});

    $('.open-related').click(function(){
			$('.related-panel').slideToggle();
		$("i",this).toggleClass("fa-angle-down fa-angle-up ");
		});

	// Mobile Menu
	if ($(".open-menu").exists()) {
		if($('body').hasClass('rtl')) {
			$('.open-menu').sidr({
				side: 'left',
				name: 'mobile-nav'
			});
		} else {
			$('.open-menu').sidr({
				side: 'right',
				name: 'mobile-nav',
				displace: true
			});
		}
	}
	// Close Menu on body click
	$("html").on("click",function(e) {
		$.sidr('close','mobile-nav');
	});

	$("#mobile-nav").on("click",function(e) {
		e.stopPropagation();
	});
	//End

	// Trunk8
  if ($(".profile-card.user-event-card .event-date").exists()) {
		$('.profile-card.user-event-card .event-date').trunk8({
			lines:2
		});
	}

    if ($(".service-panel-content h3 a").exists()) {
        $(".service-panel-content h3 a").trunk8({
            lines: 3,
            tooltip: false
        });
    }

    if($(".category_trim").exists()) {
        $(".category_trim").trunk8({
            lines: 1,
            tooltip: false
        });
    }

    if ($(".service-card .card-content h4").exists()) {
        $(".service-card .card-content h4").trunk8({
            lines: 2,
            tooltip: false
        });
    }

    if ($(".service-offeredby").exists()) {
        $(".service-offeredby").trunk8({lines: 2, tooltip: false});
    }


     if ($(".product-info h4 a").exists()) {
		$('.product-info h4 a').trunk8({
            lines:2
        });
	}

  if ($(".products h4").exists()) {
		$('.products h4').trunk8({
			lines:1
		});
	}
	if ($(".item--event h4").exists()) {
		$('.item--event h4').trunk8({
            lines:2,
			tooltip: false
		});
	}
	if ($(".box--25 a,.news-list-loop h4,.news-list-loop p").exists()) {
		$('.box--25 a,.news-list-loop h4,.news-list-loop p').trunk8({
			lines: 3,
			tooltip: false
		});
	}
  if ($(".featured--article p").exists()) {
  	$('.featured--article p').removeClass('hidden');
		$('.featured--article p').trunk8({
			lines: 3,
			tooltip: false
		});
	}

	if ($(".short-line").exists()) {
		$('.short-line').trunk8({
			tooltip: false
		});
	}

//Thread Scroll from bottom
	if ($(".conv-thread").exists()) {
		$('.conv-thread').scrollTop($('.conv-thread')[0].scrollHeight);
	}
	//end

  if ($(".story-content p").exists()) {
		$('.story-content p').trunk8({
			lines:4,
			tooltip: false
		});
	}

	$("[data-toggle=popover]").popover({
    html: true,
    content: function() {
    	return $(this).parent().find('.popover-content').html();
    }
  });

	$(".group-shared").popover({ trigger: "hover" });

	// Sticky Plugin
	if ($("#masthead").exists()) {
		$('#masthead').sticky({ topSpacing: 0 });
	}

	if ($(".likes-list-btn").exists()) {
		$('.likes-list-btn').on('click', function(){
			id = $(this).attr('data-target').replace('#likes-modal-', '');
			var data = {
				action: 'ef_load_likes_list',
				id: id,
			};
			$.ajax({
				type: 'POST',
				url: ajaxurl,
				data: data,
				success: function (data) {
					result = JSON.parse(data);
					$('.likes-'+id).html(result.content);
					//$('.likes-count-'+id).html(result.count);
				}
			});
		});
	}

	if ($(".re-count").exists()) {
		$('.re-count').on('click', function(){
			id = $(this).attr('target');
			count = parseInt($('.likes-count-'+id).html());
			if($(this).hasClass("fav")) {
				//$('.likes-count-'+id).html(count+1);
			} else {
				//$('.likes-count-'+id).html(count-1);
			}
		});
	}

	$(".nano").nanoScroller();

  //valide search bar not empty or special characters only
  /*
  $('#searchform').validate({
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
        s: {
          required: true,
          pattern: /[أ-يa-zA-Z]+/
        }
      },
      errorPlacement: function (error, element) {
          var name = $(element).attr("name");
          error.appendTo($("#" + name + "_validate"));
      },
      messages: {

        s: {
          required: ($.validator.messages.ef_search + " " + $.validator.messages.ef_required),
          pattern: ($.validator.messages.ef_search + " " + $.validator.messages.ef_pattern)
        }
      }
  });
	*/

  //valide search bar not empty or special characters only
  $('#searchform_inner').validate({
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
        s: {
          required: true,
          pattern: /[أ-يa-zA-Z]+/
        }
      },
      errorPlacement: function (error, element) {
          var name = $(element).attr("name");
          var id = $(element).attr("id");
          error.appendTo($("#" + id + "_validate"));
      },
      messages: {

        s: {
          required: ($.validator.messages.ef_search + " " + $.validator.messages.ef_required),
          pattern: ($.validator.messages.ef_search + " " + $.validator.messages.ef_pattern)
        }
      }
  });

	if ($('.dismiss-welcome').exists()) {
		$('.dismiss-welcome').on('click', function(){
			cookie_name = $(this).attr('cname');
			createCookie(cookie_name, 'dismiss');
		});
	}

  if ($('.dismiss-help-welcome').exists()) {
		$('.dismiss-help-welcome').on('click', function(){
			cookie_name = $(this).attr('cname');
			createCookie(cookie_name, 'dismiss');
		});
	}

  if ($('.dismiss-spash-screen').exists()) {
		$('.dismiss-spash-screen').on('click', function(){
			cookie_name = $(this).attr('cname');
			createCookie(cookie_name, '1');
      jQuery('#spash-screen').hide();
		});
    jQuery('#spash-screen').modal('show')
	}

  //load more button
  $('.loadmore-btn').on('click', function() {
      var $this = $(this);
    $this.button('loading');
      setTimeout(function() {
         $this.button('reset');
     }, 8000);
  });

// smooth scrolling
$(function() {
  $('a.custom-scroll').click(function() {
    if (location.pathname.replace(/^\//,'') == this.pathname.replace(/^\//,'') && location.hostname == this.hostname) {
      var target = $(this.hash);
      target = target.length ? target : $('[name=' + this.hash.slice(1) +']');
      if (target.length) {
        $('html, body').animate({
          scrollTop: target.offset().top
        }, 1000);
      }
    }
  });

  $('#resend_action_email').click(function() {
    var data = {
        action: 'ef_resend_activation_link'
    };
    jQuery.ajax({
      type: 'POST',
      url: ajaxurl,
      data: data,
      success: function (data) {
        //retrieve data & update paragarph
        if(data == "success")
        {
            $(".resend_email_text").text($.validator.messages.resendActivation);
        }else if(data == "waiting")
        {
            $(".resend_email_text").text($.validator.messages.resendActivationWaiting);
        }
      }
    });
  });

  $(document).on('submit', 'form:not(#tribe-bar-form, #signup_form, #settings-form, #mw-upload-form, #newsletter-widget)', function () {
      $(this).find('button[type=submit], input[type=submit]').attr('disabled', true);
  });
});

$('#info-bar').affix({
  offset: {
    top: 235
  }
});

    // FossPedia navigate to links
    $(".toclevel-1 a").on("click", function( e ) {

        e.preventDefault();

        var element = document.getElementById( $(this).attr('href').substring(1) )
        var bodyRect = document.body.getBoundingClientRect(),
            elemRect = element.getBoundingClientRect(),
            offset   = elemRect.top - bodyRect.top;

        $("body, html").animate({
          scrollTop: offset - $( '#masthead' ).height() - 20
        }, 600);

      });
});
