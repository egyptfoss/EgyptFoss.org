/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

jQuery( '.ef_qmn_quiz_form' ).on( "submit", function( event ) {
    event.preventDefault();
    ef_qmnFormSubmit( this.id );
});

function ef_qmnFormSubmit( quiz_form_id ) {
    var quiz_id = +jQuery( '#' + quiz_form_id ).find( '.qmn_quiz_id' ).val();
    var $container = jQuery( '#' + quiz_form_id ).closest( '.qmn_quiz_container' );
    var result = qmnValidation( '#' + quiz_form_id + ' *', quiz_form_id );

    if ( ! result ) 
    { 
        jQuery('.ef_qmn_error_message_section').text(jQuery.validator.messages.awareness_required_answers);
        jQuery('.ef_qmn_error_message_section').show();
        jQuery('body,html').animate({scrollTop: (0)}, 1000);
        return result; 
    }

    jQuery('.ef_qmn_error_message_section').hide();
    jQuery( '.mlw_qmn_quiz input:radio' ).attr( 'disabled', false );
    jQuery( '.mlw_qmn_quiz input:checkbox' ).attr( 'disabled', false );
    jQuery( '.mlw_qmn_quiz select' ).attr( 'disabled', false );
    jQuery( '.mlw_qmn_question_comment' ).attr( 'disabled', false );
    jQuery( '.mlw_answer_open_text' ).attr( 'disabled', false );

    var data = {
            action: 'qmn_process_quiz',
            quizData: jQuery( '#' + quiz_form_id ).serialize()
    };

    qsmEndTimeTakenTimer();

    if ( qmn_quiz_data[quiz_id].hasOwnProperty( 'timer_limit' ) ) {
            qmnEndTimer( quiz_id );
    }

    jQuery( '#' + quiz_form_id + ' input[type=submit]' ).attr( 'disabled', 'disabled' );
    ef_qsmDisplayLoading( $container );
    
    jQuery.post( qmn_ajax_object.ajaxurl, data, function( response ) {
            ef_qmnDisplayResults( JSON.parse( response ), quiz_form_id, $container, quiz_id );
    });

    return false;
}

function ef_qsmDisplayLoading( $container ) {
    $container.empty();
    $container.append( '<div class="spinner text-center"><div class="double-bounce1"></div><div class="double-bounce2"></div></div>' );
    qsmScrollTo( $container );
}

function ef_qmnDisplayResults( results, quiz_form_id, $container, quiz_id ) {
    $container.empty();
    if ( results.redirect ) {
        window.location.replace( results.redirect_url );
    } else {
        
        var data = {
            action: 'ef_redirect_to_quiz_result',
            quiz: quiz_id
        };
        jQuery.ajax({
          type: 'GET',
          url: ajaxurl,
          data: data,
          success: function (data) {
            //redirect to result page
            jQuery( '#' + quiz_form_id + ' input[type=submit]' ).attr( 'disabled', false );
            document.location.href = data;  
          }
        });
        
        //$container.append( '<div class="qmn_results_page"></div>' );
        //$container.find( '.qmn_results_page' ).html( results.display );
        //qsmScrollTo( $container );
    }
}

