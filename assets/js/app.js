window.$ = window.jQuery = require('jquery');

require('bootstrap');
require("jquery-validation");
require("block-ui");

$.blockUI.defaults.message = '<img src="public/images/hdfc-loader.gif" />';
$.blockUI.defaults.css = {
    'z-index': '1199',
    width: '100%',
    border: '0px solid #FFFFFF',
    top: '30%',
    cursor: 'wait',
    backgroundColor: 'transparent',
    'text-align': 'center'
};
$.blockUI.defaults.overlayCSS = {backgroundColor: '#FFFFFF', opacity: 0.9, cursor: 'wait', 'z-index': 1100}

/* block back button */

$(function () {
    if (typeof history.pushState === "function") {
        history.pushState("", null, null);
        window.onpopstate = function () {
            history.pushState("", null, null);
        };
    }
    else {
        var ignoreHashChange = true;
        window.onhashchange = function () {
            if (!ignoreHashChange) {
                ignoreHashChange = true;
                window.location.hash = Math.random();
            }
            else {
                ignoreHashChange = false;
            }
        };
    }
});