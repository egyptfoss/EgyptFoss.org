jQuery(document).ready(function ($) {
  if (ef_collaboration.which_crud == "create" || ef_collaboration.which_crud == "edit") {
    /*Create Document*/
    $(".doc-name").keyup(function () {
      if ($(this).val() == "") {
        $(".doc-live-title").val($.validator.messages.ef_untitled_doc);
      } else {
        $(".doc-live-title").val($(this).val());
      }
    });
  }
  if (ef_collaboration.which_crud == "list") {
    /*Create Document*/
    $(".custom-select2").each(function() {
      $(this).select2({
        placeholder: $.validator.messages.ef_select,
        allowClear: true,
        language: {
          noResults: function () {
            return jQuery.validator.messages.select2_no_results;
          }
        }
      });
    });
    
    $("#new_space").click(function (e) {
      e.preventDefault();
      modal = new fossModal({
        title: $.validator.messages.ef_new_space,
        body: "<input type='text'class='form-control' placeholder='" + $.validator.messages.ef_title + "' name='new_space_title' id='new_space_title'>\n\
              <label id='newSpaceErrors' class='error hidden'></label>",
        buttons: new Array(
          {text: $.validator.messages.ef_cancel, action: "close", class: "btn btn-light"},
          {text: $.validator.messages.ef_save, action: "addNewCollaborativeSpace", clickedElement: $(this), class: "btn btn-primary"}
        ),
        autoSaveBtnClose: false,
        footer: "<i class='fa fa-circle-o-notch hidden fa-spin new-space-spinner'></i> "
      });
      modal.show();
    });
    
    $(document).on("click",".rename_space",function (e) {
      e.preventDefault();
      modal = new fossModal({
        title: $.validator.messages.ef_rename,
        body: "<input type='text'class='form-control' value='" + $(this).attr("data-old-title") + "' name='rename_space_title' id='rename_space_title'>\n\
              <label id='renameSpaceErrors' class='error hidden'></label>",
        buttons: new Array(
          {text: $.validator.messages.ef_cancel, action: "close", class: "btn btn-light"},
          {text: $.validator.messages.ef_save, action: "renameCollaborativeSpace", clickedElement: $(this), class: "btn btn-primary"}
        ),
        autoSaveBtnClose: false,
        footer: "<i class='fa fa-circle-o-notch hidden fa-spin rename-space-spinner'></i> "
      });
      modal.show();
    });

    //handle remove space/document
    $(document).on("click",".remove_space",function (e) {
      e.preventDefault();
      var is_space = $(this).attr("data-space");
      var title = "";
      var content = "";
      if(is_space == 1) {
        title = $.validator.messages.ef_remove_space;
        content = $.validator.messages.ef_collaboration_remove_space;
      } else {
        title = $.validator.messages.ef_remove_document;
        content = $.validator.messages.ef_collaboration_remove_document;
      }
      
      modal = new fossModal({
        title: title,
        body: "<p>"+content+"</p>\n\
              <label id='removeSpaceErrors' class='error hidden'></label>",
        buttons: new Array(
          {text: $.validator.messages.ef_cancel, action: "close", class: "btn btn-light"},
          {text: $.validator.messages.ef_remove, action: "removeCollaborativeSpaceorDocument", clickedElement: $(this), class: "btn btn-primary"}
        ),
        autoSaveBtnClose: false,
        footer: "<i class='fa fa-circle-o-notch hidden fa-spin rename-space-spinner'></i> "
      });
      modal.show();
    });

    $(document).on("keyup", "#new_space_title", function () {
      $("#newSpaceErrors").addClass("hidden").hide();
      if ($(this).val() == "") {
        $("button[data-action='addNewCollaborativeSpace']").attr("disabled", "disabled");
      } else {
        $("button[data-action='addNewCollaborativeSpace']").removeAttr("disabled");
      }
    });
    
    $(document).on("keyup", "#rename_space_title", function () {
      $("#renameSpaceErrors").addClass("hidden").hide();
      if ($(this).val() == "") {
        $("button[data-action='renameCollaborativeSpace']").attr("disabled", "disabled");
      } else {
        $("button[data-action='renameCollaborativeSpace']").removeAttr("disabled");
      }
    });
  }
  $('#add_collaboration_center_document').validate({
    onfocusout: function (element) {
      $(element).valid();
    },
    focusInvalid: false,
    invalidHandler: function (form, validator) {
      if (!validator.numberOfInvalids())
        return;
      $('html, body').animate({
        scrollTop: $(validator.errorList[0].element).offset().top - 100
      }, 500);
    },
    rules: {
      document_title: {
        required: true,
        pattern: /[أ-يa-zA-Z]+/,
      },
      document_content: {
        pattern: /[أ-يa-zA-Z]+/,
      },
    },
    messages: {
      document_title: {
        required: ($.validator.messages.ef_title + " " + $.validator.messages.ef_required),
        pattern: ($.validator.messages.ef_title + " " + $.validator.messages.ef_pattern),
      },
      document_content: {
        pattern: ($.validator.messages.ef_content + " " + $.validator.messages.ef_pattern),
      }
    }
  });
  //show popup on post submit with status change
  $("#edit_collaboration_center_document").submit(function(e) {
      var old_status = $("#old_status").val();
      var new_status = $("#status").val();
      if (typeof(new_status) === 'undefined') {
        $("#edit_collaboration_center_document").removeClass('passed');
        return true;
      }
      if(old_status !== new_status && !($("#edit_collaboration_center_document").hasClass('passed')) && $("#edit_collaboration_center_document").valid()) {
        e.preventDefault();
        $old_status_text = $.validator.messages.ef_status_draft;
        $new_status_text = $.validator.messages.ef_status_draft;
        if(old_status === 'reviewed') {
          $old_status_text= $.validator.messages.ef_status_reviewed;
        } else if(old_status === 'published') {
          $old_status_text = $.validator.messages.ef_status_published;
        }
        
        //new status
        if(new_status === 'reviewed') {
          $new_status_text= $.validator.messages.ef_status_reviewed;
        } else if(new_status === 'published') {
          $new_status_text= $.validator.messages.ef_status_published;
        }
          
        var message = $.validator.messages.ef_status_edit_document.replace('{0}', "<b>" + $old_status_text + "</b>").replace('{1}', "<b>" +$new_status_text+"</b>");
        modal = new fossModal({
          title: $.validator.messages.ef_warning,
          body: message,
          buttons: new Array(
            {text: $.validator.messages.ef_cancel, action: "close", class: "btn btn-light"},
            {text: $.validator.messages.ef_continue, action: "submitEditDocument", clickedElement: $(this), class: "btn btn-primary"}
          ),
          autoSaveBtnClose: false,
          footer: "<i class='fa fa-circle-o-notch hidden fa-spin rename-space-spinner'></i> "
        });
        modal.show();
      }
      
      $("#edit_collaboration_center_document").removeClass('passed');
      return true;
  });
  //validate edit collaboration center
  $('#edit_collaboration_center_document').validate({
    onfocusout: function (element) {
      $(element).valid();
    },
    focusInvalid: false,
    invalidHandler: function (form, validator) {
      if (!validator.numberOfInvalids())
        return;
      $('html, body').animate({
        scrollTop: $(validator.errorList[0].element).offset().top - 100
      }, 500);
    },
    rules: {
      document_title: {
        required: true,
        pattern: /[أ-يa-zA-Z]+/
      },
      document_content: {
        required: true,
        pattern: /[أ-يa-zA-Z]+/
      },
      status: 
      {
          required: true
      }
    },
    messages: {
      document_title: {
        required: ($.validator.messages.ef_title + " " + $.validator.messages.ef_required),
        pattern: ($.validator.messages.ef_title + " " + $.validator.messages.ef_pattern)
      },
      document_content: {
        required: ($.validator.messages.ef_content + " " + $.validator.messages.ef_required),
        pattern: ($.validator.messages.ef_content + " " + $.validator.messages.ef_pattern)
      },
      status : 
      {
          required: ($.validator.messages.ef_status + " " + $.validator.messages.ef_required_fem)
      }
    }
  });
  function validateEmail(email) {
    var atpos = email.indexOf("@");
    var dotpos = email.lastIndexOf(".");
    if (atpos<1 || dotpos<atpos+2 || dotpos+2>= email.length) {
        return false;
    }
    return true;
  }
  //load invited users
  var current_selected_item;
  var is_empty_list = true;
  var users_invited_ids = [];
  var users_invited = '';
  $(document).on("click",".invite-space-document",function (e) {
    current_selected_item = $(this).attr("data-id");
    if(is_user_tab == false)
    {
      refreshInviteGroupModel(current_selected_item);
    }
    else {
        $( '#share-item-title' ).html( $(this).attr("data-title") );
    }
    //load invited users and show spinner
    $("#list_invited_users").html('');
    jQuery(".invited-users-spinner").removeClass("hidden");
    var data = {
      action: 'ef_load_invited_users',
      item_id: $(this).attr("data-id")
    };
    jQuery.ajax({
      type: 'POST',
      url: ajaxurl,
      data: data,
      success: function (data) {
        //retrieve data    
        if (data !== 'false')
        {
            users_invited = '';
            users_invited_ids = [];
            var data = data.split("|||");
            //append html to set of data
            jQuery(".invited-users-spinner").addClass("hidden");
            $("#list_invited_users").html(data[0]);
            
            //add invited to list
            //var user_id = $jsondata['id'];    
            var data_ids = data[1].split("|");        
            for(var i = 0; i < data_ids.length; i++){
                var ids_role = data_ids[i].split(',');
                users_invited_ids.push([ids_role[0], ids_role[1]]);
                //set to filled data
                if(ids_role[0] != '')
                {
                    is_empty_list = false;
                }
            }
            users_invited += data[1];    
            //reset all data
            $("#user_email").val('');
            $("select#user_roles")[0].selectedIndex = 0;
        }
      }
    });
  });
  // Autocomplete user by display name
  $(".user_email").select2({
      placeholder: jQuery.validator.messages.ef_invite_user_placeholder,
      ajax: {
        url: ajaxurl,
       // dataType: 'json',
        delay: 250,
        data: function (search_keyword) {
          //var hiddenInputSelector = '#user_email';
          //var select2 = $(hiddenInputSelector).data('select2');
          return {
            action: 'ef_load_users_by_display_name',
            display_name: search_keyword['term'],
            item_id: current_selected_item,
            users_ids_roles: users_invited_ids
          };
        },
        processResults: function (data, params) {
          params.page = params.page || 1;
          var newData = [];
          
          if (typeof data['data'] !== "undefined") 
          {
              for ( var i = 0; i < data['data'].length; i++ ) {
                  newData.push({
                      id: data['data'][i].ID,  //id part present in data
                      text: "<div class='avatar'>" + data['data'][i].avatar + "</div> <div class='user-name'>" + data['data'][i].display_name + "<div>",  //string to be displayed
                  });
              }
          }
          return {
            results: newData
          };
        },
        cache: true
      },
      escapeMarkup: function (markup) { return markup; }, // let our custom formatter work
      minimumInputLength: 3,
      formatResult: function(item) {
          return "<div class=' user-row'>" + item.text + "</div>";
      },        
      language: { 
          inputTooShort: function () { return ''; },
          noResults: function () {
              return jQuery.validator.messages.select2_no_results;
          },
          searching: function () { return jQuery.validator.messages.select2_searching; }
      }
  });
  //Invite users validation
  $("#user_email_add").click(function (e) {
    var user_id = jQuery('#user_email').val();
    if (user_id === null)
    {
      $('.error-msg').removeClass('hide');
      $('.success-msg').addClass('hide');
      $('#error-msg').text($.validator.messages.ef_error_missing_user);
      return false;
    } else
    {
      //check that user id in the system and returns userid, displayname, profile picture
      var data = {
        action: 'ef_load_invite_user',
        user_id: user_id,
        role: $("#user_roles option:selected").val(),
        item_id: current_selected_item
      };
      jQuery.ajax({
        type: 'POST',
        url: ajaxurl,
        data: data,
        success: function (data) {
          //retrieve data    
          if (data.indexOf("{") >= 0)
          {
            var data = data.split("{");

            //save the id for saving
            $jsondata = JSON.parse('{' + data[1]);
            var user_id = $jsondata['id'];
            if (users_invited.indexOf(user_id+",") < 0)
            {
              users_invited_ids.push([$jsondata['id'], $("#user_roles option:selected").val()]);
              users_invited += user_id +','+$("#user_roles option:selected").val()+ '|';
              //append html to set of data
              $("#list_invited_users").append(data[0]);

              //reset all data
              $("#user_email").val('');
              $("select#user_roles")[0].selectedIndex = 0;
              
              //hide error msg if exists
              $('.error-msg').addClass('hide');
              
              //reset select2 item
              $("#user_email").select2("val", "");
              
            } else
            {
              $('.error-msg').removeClass('hide');
              $('.success-msg').addClass('hide');
              $('#error-msg').text($.validator.messages.ef_user_exists);
              
              //reset select2 item
              $("#user_email").select2("val", "");
              
              return false;
            }
          } else
          {
            $('.error-msg').removeClass('hide');
            $('.success-msg').addClass('hide');
            $('#error-msg').text(data);
            return false;
          }
        }
      });
    }
  });
  is_user_tab = true;
  //save list of invited users
  $("#save_invited").click(function (e) {
    if(is_user_tab == true) {
      //check active tab to do action depending on
      if (users_invited_ids.length === 1 && is_empty_list) {
        $('.error-msg').removeClass('hide');
        $('.success-msg').addClass('hide');
        $('#error-msg').text($.validator.messages.ef_error_save_empty);
        return false;
      } else if (current_selected_item == null) {
        $('.error-msg').removeClass('hide');
        $('.success-msg').addClass('hide');
        $('#error-msg').text($.validator.messages.ef_error_wrong_item_id);
        return false;
      } else {
        jQuery(".invited-users-spinner").removeClass("hidden");
        nonce = jQuery('#_wpnonce').val();
        //saved list of ids in the current document/space id
        var data = {
          action: 'ef_save_invited_users',
          security: nonce,
          item_id: current_selected_item,
          users_ids_roles: users_invited_ids,
          role: $("#user_roles option:selected").val()
        };
        jQuery.ajax({
          type: 'POST',
          url: ajaxurl,
          data: data,
          success: function (data) {
            var result = JSON.parse(data);
            if (result.status == 'error') {
              $('.success-msg').addClass('hide');
              $('.error-msg').removeClass('hide');
              $('#error-msg').text(result.data);
            } else {
              $('.error-msg').addClass('hide');
              $('.success-msg').removeClass('hide');
              $('#success-msg').text($.validator.messages.ef_invited_success);
              is_empty_list = true;
              refreshContributersCount(current_selected_item);
            }
            jQuery(".invited-users-spinner").addClass("hidden");
          }
        });
      }
    } else {
      jQuery(".invite-group-spinner").removeClass("hidden");
      jQuery("#save_invited").attr("disabled", "disabled");
      nonce = jQuery('#_wpnonce').val();
      var data = {
        action: 'ef_save_invited_groups',
        security: nonce,
        type: $("#share_type").val(),
        subtype_id: $("#sub_type").val(),
        interest_ids: $("#share_interests").val(),
        technology_ids: $("#share_technologies").val(),
        industry_id: $("#share_industry").val(),
        item_id: current_selected_item,
      };
      jQuery.ajax({
        type: 'POST',
        url: ajaxurl,
        data: data,
        success: function (data) {
          //retrieve data  
           var result = JSON.parse(data);
          if (result.status == 'error') {
            $('.error-msg').removeClass('hide');
            $('.success-msg').addClass('hide');
            $('.error-msg').text(result.data);
            jQuery(".invited-users-spinner").addClass("hidden");
          } else {
            if(result.status == 'success') {
              $('.error-msg').addClass('hide');
              $('.success-msg').removeClass('hide');
              $('#success-msg').text(result.data);
              refreshGroupContributersCount(current_selected_item, jQuery("#save_invited").data( 'locale' ));
              //emptyInviteModal();
            }
          }
          jQuery(".invite-group-spinner").addClass("hidden");
          jQuery("#save_invited").removeAttr("disabled");
        }
      });
    }
  });
  //switch between tabs
  $(document).on("click",".invite_users_groups_tab",function(e){
    if($(this).attr("href") == "#invite-users") {
      is_user_tab = true;
    } else {
      is_user_tab = false;
      refreshInviteGroupModel(current_selected_item);
    }
  });
  //remove from list
  removeInvitedUser = function(remove_div_class,remove_item_id, remove_item_role) {
      //remove user id from list of ids
      var user_id = remove_item_id; 
      for(var i = 0; i < users_invited_ids.length; i++)
      {
          if(users_invited_ids[i][0] === user_id)
          {
              users_invited_ids.splice(i, 1);
              break;
          }
      }
      //remove from string items
      users_invited = users_invited.replace(user_id+","+remove_item_role+"|", "");
      
      //hide div
      $("."+remove_div_class).hide();
  };
  $('#invite-space-document').on('hidden.bs.modal', function () {
      $('.error-msg').addClass('hide');
      $('.success-msg').addClass('hide');
      $(".invited-users-spinner").addClass("hidden");
      //$("#user_email").select2("val", "4");
      $('#user_email').select2('val', "", true);
      is_empty_list = true;
     // emptyInviteModal();
  });
  // update document status of section
  $("#status").on('change', function() {
    if($(this).val() === 'published'){
      $("#section_div").removeClass('hide');
    } else {
      $("#section_div").addClass('hide');
    }
  });
  $("#sectionFilter").change(function (e) {
    section = $(this).val();
    
    var sectionParam = '';
    
    if( section ) {
        sectionParam = "?section=" + section;
    }
    var myURL = window.location.href.split('?')[0] + sectionParam;

    window.history.pushState({path: myURL}, '', myURL);
    
    $(".loading_published_collaboration").removeClass("hidden");
    var data = {
      action: 'ef_load_published_templates',
      section: section
    };
    $.ajax({
      type: 'POST',
      url: ajaxurl,
      data: data,
      success: function (response) {
        var result = JSON.parse(response);
        if (result.status == "success")
        {
          $(".publishedItemCards").html(result.data);
        }
        
        var itemsCount = $( '.publishedItemCards div.document-row' ).length;
        if( itemsCount == 0 ) {
            $( '.ef-results-meta' ).hide();
        }
        else {
          $( '.ef-results-meta' ).show();
        }

        $( '.ef-results-count' ).html( itemsCount );

        if( section ) {
            $( '.ef-category-name' ).html( '"' + $('#sectionFilter option:selected').text() + '"' );
            $( '.ef-category' ).show();
        }
        else {
            $( '.ef-category-name' ).html( '' );
              $( '.ef-category' ).hide();
          }

      },
      complete: function () {
        $(".loading_published_collaboration").addClass("hidden");
      }
    });
  });
});

//declartion of remove user to be called from anywhere and code inside document.ready
function removeInvitedUser(remove_div_class,remove_item_id, remove_item_role) {}

function addNewCollaborativeSpace(target) {

  jQuery(".new-space-spinner").removeClass("hidden");
  jQuery("button[data-action='addNewCollaborativeSpace']").attr("disabled", "disabled");
  nonce = jQuery('#_wpnonce').val();
  var data = {
    action: 'ef_add_new_space',
    security: nonce,
    space_title: jQuery("#new_space_title").val()
  };
  jQuery.ajax({
    type: 'POST',
    url: ajaxurl,
    data: data,
    async: false,
    success: function (response) {
      var result = JSON.parse(response);
      if (result.status == "success")
      {
        jQuery("#newSpaceErrors").addClass("hidden")
        jQuery(".emptyItems").hide();
        jQuery("#SpacesAndDocumentsDiv").prepend(result.data);
        jQuery("button[data-action='addNewCollaborativeSpace']").removeAttr("disabled");
        jQuery(".new-space-spinner").addClass("hidden");
        jQuery('#confirm-modal').modal('toggle');
      } else
      {
        if (result.status == "error")
        {
          jQuery(".new-space-spinner").addClass("hidden");
          jQuery("#newSpaceErrors").removeClass("hidden").show();
          jQuery("#newSpaceErrors").html(result.data);
          jQuery("button[data-action='addNewCollaborativeSpace']").removeAttr("disabled");
        }
      }
      //jQuery('#confirm-modal').modal('toggle');
    }
  });
}

function submitEditDocument() {
  jQuery('#edit_collaboration_center_document').addClass('passed');
  jQuery('#edit_collaboration_center_document').submit();
}

function renameCollaborativeSpace(target) {
  jQuery(".rename-space-spinner").removeClass("hidden");
  jQuery("button[data-action='renameCollaborativeSpace']").attr("disabled", "disabled");
  space = jQuery(target).attr("data-id");
  nonce = jQuery('#_wpnonce').val();
  var data = {
    action: 'ef_rename_space',
    security: nonce,
    space_title: jQuery("#rename_space_title").val(),
    space_id: space
  };
  jQuery.ajax({
    type: 'POST',
    url: ajaxurl,
    data: data,
    async: false,
    success: function (response) {
      var result = JSON.parse(response);
      if (result.status == "success")
      {
        jQuery(target).attr("data-old-title",jQuery("#rename_space_title").val())
        jQuery("#renameSpaceErrors").addClass("hidden")
        jQuery("div#space_"+space).find(".space_title a:first").html(result.data);
        jQuery("div#space_"+space).find(".options a.invite-space-document").attr( 'data-title', result.data);
        jQuery("button[data-action='renameCollaborativeSpace']").removeAttr("disabled");
        jQuery(".rename-space-spinner").addClass("hidden");
        jQuery('#confirm-modal').modal('toggle');
      } else
      {
        if (result.status == "error")
        {
          jQuery(".rename-space-spinner").addClass("hidden");
          jQuery("#renameSpaceErrors").removeClass("hidden").show();
          jQuery("#renameSpaceErrors").html(result.data);
          jQuery("button[data-action='renameCollaborativeSpace']").removeAttr("disabled");
        }
      }
      //jQuery('#confirm-modal').modal('toggle');
    }
  });
}

//Remove Space or Document
function removeCollaborativeSpaceorDocument(target) {

  jQuery(".rename-space-spinner").removeClass("hidden");
  jQuery("button[data-action='removeCollaborativeSpaceorDocument']").attr("disabled", "disabled");
  item = jQuery(target).attr("data-id");
  is_space = jQuery(target).attr("data-space");
  nonce = jQuery('#_wpnonce').val();
  var data = {
    action: 'ef_remove_space_document',
    security: nonce,
    item_id: item
  };
  
  jQuery.ajax({
    type: 'POST',
    url: ajaxurl,
    data: data,
    async: false,
    success: function (response) {
      var result = JSON.parse(response);
      if (result.status == "success") {
        jQuery("#removeSpaceErrors").addClass("hidden")
        //Hide Row
        jQuery(target).parent().parent().remove();
        //check if the only row 
        if (jQuery("#SpacesAndDocumentsDiv").find(".space").length == 0 && is_space == 1){ 
          jQuery("#SpacesAndDocumentsDiv").html("<div class=\"emptyItems\"><div class=\"empty-state-msg\"><i class=\"fa fa-folder-open\"></i><br><span>" + jQuery.validator.messages.ef_no_spaces + "</span></div></div>");
        } else if(jQuery("#SpacesAndDocumentsDiv").find(".document").length == 0 && is_space == 0) {
          jQuery("#SpacesAndDocumentsDiv").html("<div class=\"emptyItems\"><div class=\"empty-state-msg\"><i class=\"fa fa-folder-open\"></i><br><span>" + jQuery.validator.messages.ef_no_documents + "</span></div></div>");
        }
        jQuery("button[data-action='removeCollaborativeSpaceorDocument']").removeAttr("disabled");
        jQuery(".rename-space-spinner").addClass("hidden");
        jQuery('#confirm-modal').modal('toggle');
      } else {
        jQuery(".rename-space-spinner").addClass("hidden");
        jQuery("#removeSpaceErrors").removeClass("hidden").show();
        jQuery("#removeSpaceErrors").html(result.data);
        jQuery("button[data-action='renameCollaborativeSpace']").removeAttr("disabled");
      }
    }
  });
}

function emptyInviteModal() {
  jQuery(".custom-select2").val(null).trigger("change");
  jQuery(".add-product-tax").val(null).trigger("change");
  jQuery("#invite_grp_form")[0].reset();
  jQuery('.share_sub_type option').remove();
  jQuery(".invite-group-spinner").addClass("hidden");
}

function refreshInviteGroupModel(current_selected_item) {
  jQuery(".loading_invite_groups").removeClass("hidden");
  var data = {
    action: 'ef_list_invited_groups',
    item_id: current_selected_item,
  };
  jQuery.ajax({
    type: 'POST',
    url: ajaxurl,
    data: data,
    async: false,
    success: function (response) {
      var result = JSON.parse(response);
      if (result.status == "success")
      {
        jQuery("#invite_groups_container").html(result.data);
        jQuery(".loading_invite_groups").addClass("hidden"); 
      }
    }
  });
  jQuery(document).find("#invite_grp_form .custom-select2").each(function () {
    jQuery(this).select2({
      placeholder: jQuery.validator.messages.ef_select,
      allowClear: true,
      language: {
        noResults: function () {
          return jQuery.validator.messages.select2_no_results;
        }
      }
    });
  });
  
  jQuery(document).find(".add-product-tax").select2({
    multiple: true,
    language: {
      noResults: function () {
        return jQuery.validator.messages.select2_no_results;
      }
    }
  });
  
  var classTypeSelected = jQuery('#share_type').val();
  var allSubTypes = jQuery('.share_sub_type option');
  var currentAccountSubTypeSelected = jQuery(".share_sub_type")[0].selectedIndex;
  if(classTypeSelected == "") {
    jQuery('.share_sub_type option').remove();
  } else {
    jQuery('.share_sub_type option:not(".'+ classTypeSelected +'")').remove();
  }
  
  jQuery(document).on("change",'#share_type',function () {
    jQuery('.share_sub_type option').remove(); //remove all options
    var classN = jQuery('#share_type').val();
    jQuery(".share_sub_type").val(null).trigger("change");
    if(classN !== "") {
    var opts = allSubTypes.filter('.' + classN);
    var opts_Individual = allSubTypes.filter('.Individual').length - 1;
    if(classTypeSelected === "Individual")
      opts_Individual = 0;
    jQuery.each(opts, function (i, j) {
      jQuery(j).appendTo('.share_sub_type'); //append those options back
    });
    if(classTypeSelected == classN)
      jQuery('.share_sub_type option').eq(currentAccountSubTypeSelected - opts_Individual).prop('selected', true);
    else
      jQuery('.share_sub_type option').eq(0).prop('selected', true);
    }
  }); 
}

function refreshContributersCount(current_selected_item) {
  var data = {
    action: 'ef_get_contributors_count_by_ajax',
    item_id: current_selected_item,
  };
  jQuery.ajax({
    type: 'POST',
    url: ajaxurl,
    data: data,
    async: false,
    success: function (response) {
      var result = JSON.parse(response);
      var item = jQuery( "#contributers_"+current_selected_item );
      if (result.status == "success")
      {
        item.html("<i class='fa fa-user'></i> "+ result.data);
        result.data?item.show():item.hide();
      }
    }
  });
}

function refreshGroupContributersCount(current_selected_item, locale) {
  var data = {
    action: 'ef_get_group_contributors_count_by_ajax',
    item_id: current_selected_item,
    locale: locale,
  };
  jQuery.ajax({
    type: 'POST',
    url: ajaxurl,
    data: data,
    async: false,
    success: function (response) {
      var result = JSON.parse(response);
      var item = jQuery( "#group_contributers_"+current_selected_item );
      if (result.status == "success")
      {
        item.find( '.contrib-strings' ).html( result.data );
        result.data?item.show():item.hide();
      }
    }
  }); 
}
