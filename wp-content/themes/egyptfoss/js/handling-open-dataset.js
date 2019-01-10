jQuery(document).ready(function ($) {
    if ($(".published-date-picker").exists()) {
      $('.published-date-picker').datetimepicker({
        
        format: 'YYYY-MM-DD',
        icons: {
          date: 'fa fa-calendar-o',
          up: 'fa fa-chevron-up',
          down: 'fa fa-chevron-down',
          previous: 'fa fa-chevron-left',
          next: 'fa fa-chevron-right',
          clear: 'fa fa-remove',
          close: 'fa fa-remove'
        }
      });
    }
    jQuery.validator.addMethod('filesize', function(value, element, param) {
        // param = size (in bytes) 
        // element = element to validate (<input>)
        // value = value of the element (file name)
        var input = document.getElementById('sizes').value.slice(1);
        input = input.split("|"); 
        for (var x = 0; x < input.length; x++) {
           if(input[x] > param)
            return false;
        }
        return true;
    });
    
    jQuery.validator.addMethod('fileExtension', function(value, element, param) {
        // param = size (in bytes) 
        // element = element to validate (<input>)
        // value = value of the element (file name)
        var input = document.getElementById('names').value.slice(1);
        input = input.split("|"); 
        for (var x = 0; x < input.length; x++) 
        {
            var extension = input[x].substr( (input[x].lastIndexOf('.') +1) ).toLowerCase(); 
            if (param.indexOf(extension) < 0)
                return false;
        }
        return true;
    });
    
    jQuery.validator.addMethod('filesRequired', function(value, element, param) {
        var input = document.getElementById('names').value.slice(1);
        var sizes = document.getElementById('sizes').value.slice(1);
        if(input == '' && sizes == '')
            return false;
        return true;
    });
    
  $('#add_open_dataset').validate({
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
      open_dataset_title: {
        required: true,
        pattern: /[أ-يa-zA-Z]+/,
        minlength: 10,
        maxlength: 100,
      },
      open_dataset_description: {
        required: true,
        pattern: /[أ-يa-zA-Z]+/
      },
      open_dataset_publisher: {
        required: true,
        pattern: /[أ-يa-zA-Z]+/
      },      
      type: {
        required: true
      },      
      theme: {
        required: true
      }, 
      license: {
        required: true
      },  
      open_dataset_references: {
        required: true,
        pattern: /[أ-يa-zA-Z]+/
      },      
      open_dataset_source: {
        required: true,
        url: true
      },
      open_dataset_usage: {
          pattern: /[أ-يa-zA-Z]+/
      },
      'open_dataset_resources[]': {
        fileExtension: "pdf|json|csv|xml|html|doc|docx|xls|xlsx|jpg|jpeg|png",
        filesize: 20971520 //20MB 
      }
    },
    errorPlacement: function (error, element) {
            var name = $(element).attr("name");
            if(name == "open_dataset_resources[]")
            {
                name = "open_dataset_resources";
            }
            error.appendTo($("#" + name + "_validate"));
        },
    messages: {
      open_dataset_title: {
        required: ($.validator.messages.ef_title + " " + $.validator.messages.ef_required),
        pattern: ($.validator.messages.ef_title + " " + $.validator.messages.ef_pattern),
        minlength: ($.validator.messages.ef_title + " " + $.validator.messages.ef_minlength),
        maxlength: ($.validator.messages.ef_title + " " + $.validator.messages.ef_maxlength)
      },
      open_dataset_description: {
        required: ($.validator.messages.ef_description + " " + $.validator.messages.ef_required),
        pattern: ($.validator.messages.ef_description + " " + $.validator.messages.ef_pattern)
      },
      open_dataset_publisher: {
        required: ($.validator.messages.ef_publisher + " " + $.validator.messages.ef_required),
        pattern: ($.validator.messages.ef_publisher + " " + $.validator.messages.ef_pattern)
      },      
      type: {
        required: ($.validator.messages.type + " " + $.validator.messages.ef_required),  
      },
      theme: {
        required: ($.validator.messages.ef_theme + " " + $.validator.messages.ef_required),  
      },
      license: {
        required: ($.validator.messages.license + " " + $.validator.messages.ef_required),  
      },   
      open_dataset_references: {
        required: ($.validator.messages.ef_references + " " + $.validator.messages.ef_required), 
        pattern: ($.validator.messages.ef_references + " " + $.validator.messages.ef_pattern)
      }, 
      open_dataset_source: {
        required: ($.validator.messages.ef_link_to_source + " " + $.validator.messages.ef_required), 
        url: ($.validator.messages.ef_link_to_source + " " + $.validator.messages.ef_invalid)
      },
      open_dataset_usage: {
          pattern: ($.validator.messages.ef_usage + " " + $.validator.messages.ef_pattern_fem)
      },
      'open_dataset_resources[]': {
          fileExtension: ($.validator.messages.ef_resources + " " + $.validator.messages.ef_extension),
          extension: ($.validator.messages.ef_resources + " " + $.validator.messages.ef_extension),
          filesize: ($.validator.messages.ef_resources + " " + $.validator.messages.ef_maxsize)
      }
    }
  });
  
  //add resources validation
  $('#add_resources_open_dataset').validate({
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
      open_dataset_description: {
        required: true,
        pattern: /[أ-يa-zA-Z]+/
      },
      'open_dataset_resources[]': {
        filesRequired: true,
        fileExtension: "pdf|json|csv|xml|html|doc|docx|xls|xlsx|jpg|jpeg|png",
        filesize: 20971520 //20MB 
      }
    },
    errorPlacement: function (error, element) {
            var name = $(element).attr("name");
            if(name == "open_dataset_resources[]")
            {
                name = "open_dataset_resources";
            }
            error.appendTo($("#" + name + "_validate"));
        },
    messages: {
      open_dataset_description: {
        required: ($.validator.messages.ef_description + " " + $.validator.messages.ef_required),
        pattern: ($.validator.messages.ef_description + " " + $.validator.messages.ef_pattern)
      },
      'open_dataset_resources[]': {
          required: ($.validator.messages.ef_resources + " " + $.validator.messages.ef_required_fem),
          filesRequired: ($.validator.messages.ef_resources + " " + $.validator.messages.ef_required_fem),
          fileExtension: ($.validator.messages.ef_resources + " " + $.validator.messages.ef_extension),
          extension: ($.validator.messages.ef_resources + " " + $.validator.messages.ef_extension),
          filesize: ($.validator.messages.ef_resources + " " + $.validator.messages.ef_maxsize)
      }
    }
  });
  
  //handling modal description of resources
    loadDescription = function(resource_id, opendataset_id) {
        var data = {
            action: 'ef_load_resource_description',
            resource_id: resource_id,
            opendataset_id: opendataset_id
        };
        jQuery.ajax({
          type: 'POST',
          url: ajaxurl,
          data: data,
          success: function (data) {
            //retrieve data & update paragarph
            $("#resource-description").html(data);
          }
        });
    };
    
    $('#ef-sort-files').click( function (e) {
      $( '.sort-list' ).toggle();
      e.stopPropagation();
    });
    $(document).click(function () {
        $( '.sort-list' ).hide();
    });
    $('#sort-by-name').click(function () {
      var label = $(this).data( 'label' );
      $( '#sort-label' ).html(label);
      files.sort(function(a,b) {return (a.name > b.name) ? 1 : ((b.name > a.name) ? -1 : 0);} ); 
      rebuildHTML(files);
    });
    $('#sort-by-size').click(function () {
      var label = $(this).data( 'label' );
      $( '#sort-label' ).html(label);
      files.sort(function(a,b) {return (parseInt(a.size) > parseInt(b.size)) ? 1 : ((parseInt(b.size) > parseInt(a.size)) ? -1 : 0);} ); 
      rebuildHTML(files);
    });
    $('#sort-by-type').click(function () {
      var label = $(this).data( 'label' );
      $( '#sort-label' ).html(label);
      files.sort(function(a,b) {return (a.type > b.type) ? 1 : ((b.type > a.type) ? -1 : 0);} );
      rebuildHTML(files);
    });

    var files = [];
    $( '.file-item' ).each( function() {
      var fileObj = { name: $(this).find( '.file-name' ).html(), size: $(this).find( '.file-bytes' ).html(), type: $(this).find( '.file-type' ).html(), html: $(this).wrap('<p/>').parent().html() };
      files.push( fileObj );
      $(this).unwrap();
    });
    
});

//declartion of resource description to be called from anywhere and code inside document.ready
function loadDescription(resource_id, opendataset_id) {}

function rebuildHTML(files) {
    $ = jQuery;
    $( '.file-item' ).remove();
    $.each(files, function(k,v) {
        $( v.html ).insertBefore( '.download-all-files' );
    });
  }