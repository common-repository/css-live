/**
 * Created by ra on 7/7/2016.
 */


(function () {
    'use strict';


    jQuery().ready(function(){


        var clickedOnEditor = false;



        // top button to show and hide the editor
        jQuery('#wp-admin-bar-td_live_css_css_writer a').click(function(event){
            event.preventDefault();
            event.stopPropagation();
            jQuery('#tdw-css-writer').toggleClass('tdw-visible');
        });



        jQuery('#tdw-css-writer').click(function(event){
            clickedOnEditor = true;
        });


        // hide the window on body
        jQuery('body').click(function(event){
            if (clickedOnEditor === true) {
                clickedOnEditor = false;
                return;
            }

            var $editor = jQuery('#tdw-css-writer');
            if ($editor.hasClass('tdw-visible')) {
                $editor.removeClass('tdw-visible');
            }
        });




        // editor tabs
        jQuery('.tdw-tab').click(function(event){
            event.preventDefault();

            // top tabs
            if (jQuery(this).hasClass('tdc-tab-active')) {
                return;
            }
            jQuery('.tdc-tab-active').removeClass('tdc-tab-active');
            jQuery(this).addClass('tdc-tab-active');

            //tabs content
            jQuery('.tdw-tabs-content').hide();
            jQuery('.' + jQuery(this).data('tab-content')).show();
        });


        // save button
        jQuery('.tdw-save-css').click(function(){
            event.preventDefault();

            if (jQuery(this).hasClass('tdw-saving-animation')) {
                return;
            }

            jQuery(this).addClass('tdw-saving-animation'); // add the saving gif

            var compiledCss = {}; // the compiled css array for each detect key
            var lessInput = {}; // what the user entered in each field

            jQuery( ".tdw-css-writer-editor" ).each(function( index ) {
                var currentDetectKey = jQuery(this).data('detect-ley');

                // save the input
                lessInput[currentDetectKey] = jQuery(this).val();

                // compile the less to css
                less.render(jQuery(this).val(), function (e, output) {
                    if (typeof output === 'undefined') {
                        compiledCss[currentDetectKey] = '';
                        return;
                    }
                    compiledCss[currentDetectKey] = output.css;
                });
            });




            jQuery.ajax({
                timeout: 10000,
                type: 'POST',

                // uuid is for browser cache busting
                url: _getRestEndPoint('tdw/save_css', 'uuid=' + _getUniqueID()),


                // add the nonce used for cookie authentication
                beforeSend: function ( xhr ) {
                    xhr.setRequestHeader( 'X-WP-Nonce', window.tdwGlobal.wpRestNonce);
                },

                dataType: 'json',
                data: {
                    compiledCss: compiledCss,
                    lessInput: lessInput
                }
            }).done(function( data, textStatus, jqXHR ) {
                window.onbeforeunload = '';
                jQuery('.tdw-saving-animation').removeClass('tdw-saving-animation');

            }).fail(function( jqXHR, textStatus, errorThrown ) {

            });
        });



        function _getRestEndPoint(restEndPoint, queryString) {
            if ( _.isEmpty(window.tdwGlobal.permalinkStructure) ) {
                // no permalinks
                return window.tdwGlobal.wpRestUrl + restEndPoint + '&' + queryString;
            } else {
                // we have permalinks enabled
                return window.tdwGlobal.wpRestUrl + restEndPoint + '?' + queryString;
            }
        }


        function _getUniqueID() {
            function s4() {
                return Math.floor((1 + Math.random()) * 0x10000)
                    .toString(16)
                    .substring(1);
            }
            return s4() + s4() + '-' + s4() + '-' + s4() + '-' +
                s4() + '-' + s4() + s4() + s4();
        }



    });


})();