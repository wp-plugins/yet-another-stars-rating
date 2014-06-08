jQuery(document).ready(function() {

    tinymce.create('tinymce.plugins.yasr_plugin', {
        init : function(ed, url) {
                // Register command for when button is clicked
                ed.addCommand('yasr_insert_shortcode', function() {

                        // triggers the thickbox
                        var width = jQuery(window).width(), W = ( 720 < width ) ? 720 : width;
                        W = W - 80;
                        tb_show( 'Insert YASR Shortcode', '#TB_inline?width=' + W + '&inlineId=yasr-form' );

                    tinymce.execCommand('mceInsertContent', false, content);
                });

            // Register buttons - trigger above command when clicked
            ed.addButton('yasr_button', {title : 'Yasr Shortcode', cmd : 'yasr_insert_shortcode', image: url + '/../img/star_tiny.png' });
        },   
    });

    // Register our TinyMCE plugin
    // first parameter is the button ID1
    // second parameter must match the first parameter of the tinymce.create() function above
    tinymce.PluginManager.add('yasr_button', tinymce.plugins.yasr_plugin);

    // executes this when the DOM is ready
    jQuery(document).ready(function(){
        var data = { 
            action: 'yasr_create_shortcode'
        }

        jQuery.post(ajaxurl, data, function(button_content) {
        // creates a table to be displayed everytime the button is clicked

            var response=button_content;

            var table = jQuery(response).find('yasr-form');
            jQuery(response).appendTo('body').hide();  
        }); 
    });

});