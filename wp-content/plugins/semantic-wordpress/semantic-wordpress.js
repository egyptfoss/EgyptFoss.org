jQuery(document).ready(function($){
	var $content = $( '#acf-field-description' ),
	contentEditor,
	prevText = "";
        
	function updateTags(text){
		var text;

		if ( ! contentEditor || contentEditor.isHidden() ) {
			text = $content.val();
		} else {
			text = contentEditor.getContent( { format: 'text' } );
		}

		if(text.localeCompare(prevText) != 0 && text.localeCompare("") != 0){
			$.ajax({
				url: ajaxurl,
				type: 'POST',
				data: {text: text, action: 'ef_enhance_content'},
				success: function(data){
                                        var parsedData = JSON.parse( data );
                                        data = parsedData.body;
					rdfstore.create(function(err, store) {
                                            //query ld+json response from stanbol using rdfStore
                                            // purpose : get subject and urls for the entities
						store.load("application/ld+json", data, function(err,results) {
							store.execute(
							 'PREFIX rdf:  <http://www.w3.org/1999/02/22-rdf-syntax-ns#>\
								PREFIX foaf: <http://xmlns.com/foaf/0.1/>\
								PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#>\
								PREFIX fise: <http://fise.iks-project.eu/ontology/>\
								PREFIX purl: <http://purl.org/dc/terms/>\
								SELECT ?textAnnotation ?entityAnnotation ?confidence ?entity ?text\
								{ \
									?textAnnotation rdf:type fise:TextAnnotation .\
									?textAnnotation fise:selected-text ?text .\
									?entityAnnotation purl:relation ?textAnnotation .\
									?entityAnnotation fise:confidence ?confidence .\
									?entityAnnotation fise:entity-reference ?entity .\
								}',
								function(err, results) {
									var hash = {};
                                                                        //iterate over stanbol response and get the highest confidence value  for each entity
									for(var i = 0; i < results.length; i++){
										if(results[i].textAnnotation.value in hash){
											if(parseFloat(hash[results[i].textAnnotation.value].conf) < parseFloat(results[i].confidence.value)){
												hash[results[i].textAnnotation.value] = {entity: results[i].entityAnnotation.value, entity: {url: results[i].entity.value, label: results[i].text.value}, conf: parseFloat(results[i].confidence.value)};
											}
										}else{
											hash[results[i].textAnnotation.value] = {entityAnnotation: results[i].entityAnnotation.value, entity: {url: results[i].entity.value, label: results[i].text.value}, conf: parseFloat(results[i].confidence.value)};
										}
									}
                                                                        //extract (url , label) for each entity
									var entities = {};
									Object.keys(hash).forEach(function (key) {
										var value = hash[key]
										entities[value.entity.url] = value.entity.label;
									});
									var $tags = $( '#tags' );
									$tags.text("");
									Object.keys(entities).forEach(function (key) {
										$tags.html($tags.html() + "<a href="+ key +" class='button'>" + entities[key] + "</a>")
									});
								});
							});
						});
					}
				});
			prevText = text;
		}
	}

	$( document ).on( 'tinymce-editor-init', function( event, editor ) {
                if(editor.id  == 'acf_settings') {
                    return;
                }

		if ( editor.id !== 'content' ) {
                    $('#acf-description').append("<table id='post-status-info'> <tbody></tbody></table>");
                }

		contentEditor = editor;

		editor.on( 'nodechange keyup', _.debounce( updateTags, 1000 ) );
                $( '#post-status-info' ).find('tbody').append(
                        "<tr>\
                                <td id='tags'></td>\
                        </tr>"
                );
		
	});

        $content.after("<table id='post-status-info'> <tbody></tbody></table>");
        $( '#post-status-info' ).find('tbody').append(
                "<tr>\
                        <td id='tags'></td>\
                </tr>"
        );

	$content.on( 'input keyup', _.debounce( updateTags, 1000 ) );

});
