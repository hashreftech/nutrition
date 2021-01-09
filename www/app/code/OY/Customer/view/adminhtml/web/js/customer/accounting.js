define([
    'jquery',
    'uiComponent'
], function ($, Component) {
    var result = Component.extend({
        applyAction: function() {
            alert('Done!');
        }
    });
    return result;
});

