  function fossModal(params) {
    var modal = this;
    var modalHtml = jQuery("#confirm-modal");

    modal.init = function () {
      modalHtml.find('.modal-title').text(params.title)
      modalHtml.find('.modal-body').html(params.body)
      modalHtml.find('.modal-footer').html(params.footer);
      for (btnIndex in params.buttons) {
        btn_class = "btn btn-primary btn-sm";
        if(params.buttons[btnIndex].class)
        {
          btn_class = params.buttons[btnIndex].class;
        }
        var btnHtml = '<button type="button" class="'+ btn_class +'" >' + params.buttons[btnIndex].text + '</button>'
        modalHtml.find('.modal-footer').append(btnHtml);
        btn = modalHtml.find('.modal-footer .btn:last-child');

        if (params.buttons[btnIndex].action === "close") {
          btn.attr('data-dismiss', 'modal');
        } else {
            if(params.buttons[btnIndex].action == "submitEditDocument")
            {
             btn.attr("name", "popup_save");
             btn.attr("id", "popup_save");
            }  
          btn.attr("data-action", params.buttons[btnIndex].action);
          if(params.autoSaveBtnClose !== false)
          {
            btn.attr('data-dismiss', 'modal');
          }
          btn.attr("data-btn-index", btnIndex);
          
          btn.click(function () {
            if (typeof window[jQuery(this).attr("data-action")] === 'function') {
              window[jQuery(this).attr("data-action")](params.buttons[jQuery(this).attr("data-btn-index")].clickedElement);
            }
          });
        }
      }
    };

    modal.show = function () {
      jQuery('#confirm-modal').modal();
    };

    this.init();
  }