/**
 * SRM Video Player — Admin JS
 * Author: Saieed Rahman | SidMan Solution 2026
 */

(function ($) {
    'use strict';

    var mediaUploader, thumbUploader;

    $(document).ready(function () {

        // ---- Color Pickers ----
        if ($.fn.wpColorPicker) {
            $('.srmvp-color-picker').wpColorPicker();
        }

        // ---- Source Type Tabs ----
        function updateSourceUI(sourceType) {
            var $tabs = $('.srmvp-source-tab');
            $tabs.removeClass('active');
            $tabs.filter(function () {
                return $(this).find('input').val() === sourceType;
            }).addClass('active');

            var isUpload = sourceType === 'upload';
            $('.srmvp-upload-btn').toggle(isUpload);
            $('.srmvp-url-label').hide();
            if (isUpload) {
                $('.srmvp-label-upload').show();
            } else {
                $('.srmvp-label-external').show();
            }
            $('.srmvp-field-help').hide();
            if (sourceType === 'youtube') $('.srmvp-help-youtube').show();
            if (sourceType === 'facebook') $('.srmvp-help-facebook').show();
            if (sourceType === 'gdrive') $('.srmvp-help-gdrive').show();
        }

        var initial = $('input[name="srmvp_source_type"]:checked').val() || 'upload';
        updateSourceUI(initial);

        $('input[name="srmvp_source_type"]').on('change', function () {
            updateSourceUI($(this).val());
        });

        $(document).on('click', '.srmvp-source-tab', function () {
            var $input = $(this).find('input[type="radio"]');
            $input.prop('checked', true).trigger('change');
        });

        // ---- Video Media Uploader ----
        $(document).on('click', '.srmvp-media-select', function (e) {
            e.preventDefault();

            if (mediaUploader) {
                mediaUploader.open();
                return;
            }

            mediaUploader = wp.media({
                title: srmvpAdmin.mediaTitle,
                button: { text: srmvpAdmin.mediaButton },
                multiple: false,
                library: { type: 'video' }
            });

            mediaUploader.on('select', function () {
                var attachment = mediaUploader.state().get('selection').first().toJSON();
                $('#srmvp_video_url').val(attachment.url);
            });

            mediaUploader.open();
        });

        // ---- Thumbnail Uploader ----
        $(document).on('click', '.srmvp-thumb-select', function (e) {
            e.preventDefault();

            if (thumbUploader) {
                thumbUploader.open();
                return;
            }

            thumbUploader = wp.media({
                title: srmvpAdmin.thumbTitle,
                button: { text: srmvpAdmin.thumbButton },
                multiple: false,
                library: { type: 'image' }
            });

            thumbUploader.on('select', function () {
                var attachment = thumbUploader.state().get('selection').first().toJSON();
                $('#srmvp_thumbnail').val(attachment.id);
                var preview = attachment.sizes && attachment.sizes.medium
                    ? attachment.sizes.medium.url
                    : attachment.url;
                var $prev = $('.srmvp-thumb-preview');
                $prev.html('<img src="' + preview + '" alt="">');
                if (!$('.srmvp-thumb-remove').length) {
                    $('.srmvp-thumb-actions').append(
                        '<button type="button" class="button srmvp-thumb-remove">Remove</button>'
                    );
                }
            });

            thumbUploader.open();
        });

        $(document).on('click', '.srmvp-thumb-remove', function (e) {
            e.preventDefault();
            $('#srmvp_thumbnail').val('');
            $('.srmvp-thumb-preview').html(
                '<span class="srmvp-thumb-placeholder"><span class="dashicons dashicons-format-image"></span></span>'
            );
            $(this).remove();
        });

        // ---- Copy Shortcode ----
        $(document).on('click', '.srmvp-copy-shortcode', function () {
            var $btn = $(this);
            var text = $btn.data('shortcode');
            if (navigator.clipboard) {
                navigator.clipboard.writeText(text).then(function () {
                    $btn.text('Copied!');
                    setTimeout(function () { $btn.text('Copy'); }, 2000);
                });
            } else {
                var ta = document.createElement('textarea');
                ta.value = text;
                document.body.appendChild(ta);
                ta.select();
                document.execCommand('copy');
                document.body.removeChild(ta);
                $btn.text('Copied!');
                setTimeout(function () { $btn.text('Copy'); }, 2000);
            }
        });

    });

})(jQuery);
