(function($) {
    var methods = {
        init: function(options) {
            var settings = {
                photo: null,
                onUpload: null
            };
            var settings = $.extend(settings, options);
            return this.each(function() {
                $(this).data('settings', settings);
                $(this).change(function() {
                    // Change the form target and enctype
                    var $form = $(this).parents('form');
                    var oldAction = $form.attr('action');
                    $form.attr('action', $(this).attr('data-target'));
                    // Submit
                    var $this = $(this);
                    $this.data('settings').photo.parents('.photo').addClass('loading');
                    $form.ajaxSubmit({
                        success: function(response) {
                            $this.data('settings').photo.parents('.photo').removeClass('loading');
                            methods.finishUpload.apply($this, [response]);
                        }
                    });
                    // End submit
                    $form.attr('action', oldAction);
                });
            });
        },
        finishUpload: function(path) {
            $(this).data('settings').photo.attr('src', path+'?c='+(new Date().getTime()));
            if(typeof $(this).data('settings').onUpload == 'function') {
                $(this).data('settings').onUpload(path);
            }
        }
    };

    $.fn.photoUpload = function(method) {
        // Method calling logic
        if (methods[method]) {
            return methods[ method ].apply(this, Array.prototype.slice.call(arguments, 1));
        } else if (typeof method === 'object' || !method) {
            return methods.init.apply(this, arguments);
        } else {
            $.error('Method ' + method + ' does not exist on jQuery.photoUpload');
        }
    };

})(jQuery);