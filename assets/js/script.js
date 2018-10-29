(function () {
    "use strict";

    // Price range slider
    jQuery("input[type='range']").change(function() {
        var el      = jQuery(this);
        jQuery('.count').html(el.val());
    }).trigger('change');

    // Trigger form submit
    jQuery(".lbs-form").on('submit', function(e) {
        e.preventDefault();
        var el  = jQuery(this),
            wrap= jQuery(".lbs-results"),
            note= jQuery(".lbs-result-notice"),
            btn = jQuery(".search-book"),
            data= jQuery(".lbs-form").serialize();

        note.html('');
        wrap.html('<tr><td colspan="6" class="text-center">Processing...</td></tr>');
        btn.prop('disabled', true).html('Processing request...');

        jQuery.post(lbs.ajaxurl, data, function(res){
            btn.prop('disabled', false).html('Search');
            res = JSON.parse(res);
            note.html(res.message);
            wrap.html(res.html);
        });        
    });
})();