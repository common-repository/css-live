/**
 * Created by ra on 7/15/2016.
 */
var tdLiveCssInject = {};


(function () {
    tdLiveCssInject = {


        _getCssPlaceholder: function () {
            var $cssPlaceholder = jQuery( "#tdw-css-placeholder");

            if ( $cssPlaceholder.length > 0 ) {
                return $cssPlaceholder;
            }


            $cssPlaceholder = jQuery('<style id="tdw-css-placeholder"></style>');
            jQuery('body').append($cssPlaceholder);
            return $cssPlaceholder;

        },


        css: function (newCss) {
            var $cssPlaceholder = tdLiveCssInject._getCssPlaceholder();
            $cssPlaceholder.html(newCss);
        },


        less: function (newLess) {
            less.render(newLess, function (e, output) {

                // console.log(newLess);
                // console.log(output);
                if (typeof output === 'undefined') {
                    return;
                }
                tdLiveCssInject.css(output.css);

            });
        }




    }
})();