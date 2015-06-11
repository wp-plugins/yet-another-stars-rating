jQuery(document).ready(function() {

    tinymce.create('tinymce.plugins.yasr_plugin', {
        init : function(ed, url) {
                // Register command for when button is clicked
                ed.addCommand('yasr_insert_shortcode', function() {

                        jQuery('#yasr-tinypopup-form').dialog({
                            title: 'Insert YASR shortcode',
                            width: 530, // overcomes width:'auto' and maxWidth bug
                            maxWidth: 600,
                            height: 'auto',
                            modal: true,
                            fluid: true, //new option
                            resizable: false

                        });
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

            jQuery(response).appendTo('body').hide();  

        }); 
    });

});