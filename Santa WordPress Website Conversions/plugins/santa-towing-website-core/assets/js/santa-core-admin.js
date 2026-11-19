/* global jQuery, wp */
(function ($) {
    'use strict';

    $(function () {
        // Initialize WP Color Pickers on Site Settings -> Theme Colors tab
        if ($.fn.wpColorPicker) {
            $('.stwc-color-field').wpColorPicker();
        }

        // Single image Media Library Picker (Site Logo)
        $(document).on('click', '.stwc-media-select', function (e) {
            e.preventDefault();
            var $wrap = $(this).closest('.stwc-media-picker');
            var frame = wp.media({
                title: 'Select Image',
                button: { text: 'Use This Image' },
                multiple: false
            });
            frame.on('select', function () {
                var attachment = frame.state().get('selection').first().toJSON();
                if (attachment && attachment.url) {
                    $wrap.find('.stwc-media-input').val(attachment.url);
                    $wrap.find('.stwc-media-preview').attr('src', attachment.url);
                }
            });
            frame.open();
        });

        // Multi-Image Gallery Picker for Cars, Rentals, and Parts CPTs
        function renderGalleryThumbs($field) {
            var raw = $field.find('.stwc-gallery-input').val() || '';
            var $thumbs = $field.find('.stwc-gallery-thumbs').empty();
            var urls = [];
            try {
                var parsed = JSON.parse(raw);
                if (Array.isArray(parsed)) urls = parsed;
            } catch (err) {
                if (raw.trim()) {
                    urls = raw.split(',').map(function (s) { return s.trim(); }).filter(Boolean);
                }
            }
            urls.forEach(function (u) {
                var $box = $('<div class="stwc-gallery-thumb"></div>');
                $box.append($('<img>').attr('src', u));
                $thumbs.append($box);
            });
        }

        $('.stwc-gallery-field').each(function () {
            renderGalleryThumbs($(this));
        });

        $(document).on('click', '.stwc-add-gallery-images', function (e) {
            e.preventDefault();
            var $field = $(this).closest('.stwc-gallery-field');
            var frame = wp.media({
                title: 'Select Gallery Photos',
                button: { text: 'Add to Gallery' },
                multiple: true
            });
            frame.on('select', function () {
                var urls = [];
                frame.state().get('selection').each(function (att) {
                    var data = att.toJSON();
                    if (data && data.url) urls.push(data.url);
                });
                $field.find('.stwc-gallery-input').val(JSON.stringify(urls));
                renderGalleryThumbs($field);
            });
            frame.open();
        });

        $(document).on('click', '.stwc-clear-gallery-images', function (e) {
            e.preventDefault();
            var $field = $(this).closest('.stwc-gallery-field');
            $field.find('.stwc-gallery-input').val('');
            renderGalleryThumbs($field);
        });
    });
})(jQuery);
