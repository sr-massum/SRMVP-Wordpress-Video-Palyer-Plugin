/**
 * SRM Video Player — Gutenberg Block Editor JS
 * Author: Saieed Rahman | SidMan Solution 2026
 */

(function () {
    'use strict';

    var blocks     = window.wp.blocks;
    var el         = window.wp.element.createElement;
    var __         = window.wp.i18n.__;
    var blockEditor = window.wp.blockEditor || window.wp.editor;
    var components = window.wp.components;

    var InspectorControls  = blockEditor.InspectorControls;
    var MediaUpload        = blockEditor.MediaUpload;
    var MediaUploadCheck   = blockEditor.MediaUploadCheck;
    var RichText           = blockEditor.RichText;

    var PanelBody     = components.PanelBody;
    var SelectControl = components.SelectControl;
    var TextControl   = components.TextControl;
    var Button        = components.Button;
    var ColorPicker   = components.ColorPicker;
    var Placeholder   = components.Placeholder;

    blocks.registerBlockType('srm-video-player/player', {
        title: __('SRM Video Player', 'srm-video-player'),
        description: __('Embed a video using the SRM Video Player (SRMVP) — supports media library, YouTube, Facebook, and Google Drive.', 'srm-video-player'),
        icon: {
            src: el('svg', { viewBox: '0 0 24 24', xmlns: 'http://www.w3.org/2000/svg', fill: 'currentColor' },
                el('path', { d: 'M21 3H3c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h18c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H3V5h18v14zM10 8v8l6-4-6-4z' })
            )
        },
        category: 'media',
        keywords: [__('srmvp', 'srm-video-player'), __('video', 'srm-video-player'), __('player', 'srm-video-player'), __('srm', 'srm-video-player')],
        supports: { html: false },

        attributes: {
            videoUrl:     { type: 'string',  default: '' },
            videoTitle:   { type: 'string',  default: '' },
            thumbnailUrl: { type: 'string',  default: '' },
            thumbnailId:  { type: 'integer', default: 0 },
            sourceType:   { type: 'string',  default: 'upload' },
            titleColor:   { type: 'string',  default: '#ffffff' },
        },

        edit: function (props) {
            var attributes   = props.attributes;
            var setAttributes = props.setAttributes;

            var videoUrl     = attributes.videoUrl;
            var videoTitle   = attributes.videoTitle;
            var thumbnailUrl = attributes.thumbnailUrl;
            var sourceType   = attributes.sourceType;
            var titleColor   = attributes.titleColor;

            var sourceOptions = [
                { label: __('Media Library (Upload)', 'srm-video-player'), value: 'upload' },
                { label: __('YouTube', 'srm-video-player'),                value: 'youtube' },
                { label: __('Facebook', 'srm-video-player'),               value: 'facebook' },
                { label: __('Google Drive', 'srm-video-player'),           value: 'gdrive' },
            ];

            function getUrlPlaceholder() {
                if (sourceType === 'youtube')  return 'https://www.youtube.com/watch?v=...';
                if (sourceType === 'facebook') return 'https://www.facebook.com/watch?v=...';
                if (sourceType === 'gdrive')   return 'https://drive.google.com/file/d/...';
                return '';
            }

            function getSourceLabel() {
                var found = sourceOptions.filter(function (o) { return o.value === sourceType; });
                return found.length ? found[0].label : 'Video';
            }

            var hasVideo = videoUrl && videoUrl.length > 0;

            var inspectorPanel = el(
                InspectorControls,
                null,

                el(PanelBody, { title: __('Video Source', 'srm-video-player'), initialOpen: true },
                    el(SelectControl, {
                        label:    __('Source Type', 'srm-video-player'),
                        value:    sourceType,
                        options:  sourceOptions,
                        onChange: function (val) {
                            setAttributes({ sourceType: val, videoUrl: '' });
                        }
                    }),

                    sourceType === 'upload'
                        ? el(
                            'div',
                            { className: 'srmvp-block-media-pick' },
                            el(MediaUploadCheck, null,
                                el(MediaUpload, {
                                    onSelect: function (media) {
                                        setAttributes({ videoUrl: media.url });
                                    },
                                    allowedTypes: ['video'],
                                    value:        videoUrl,
                                    render:       function (ref) {
                                        return el(
                                            'div',
                                            null,
                                            hasVideo
                                                ? el('p', { className: 'srmvp-selected-url' },
                                                    el('strong', null, __('Selected: ', 'srm-video-player')),
                                                    el('span', null, videoUrl.split('/').pop())
                                                  )
                                                : null,
                                            el(Button, {
                                                onClick:   ref.open,
                                                variant:   'secondary',
                                                className: 'srmvp-block-btn-full',
                                            }, hasVideo
                                                ? __('Change Video', 'srm-video-player')
                                                : __('Select Video from Media Library', 'srm-video-player')
                                            )
                                        );
                                    }
                                })
                            )
                        )
                        : el(TextControl, {
                            label:    getSourceLabel() + ' URL',
                            value:    videoUrl,
                            placeholder: getUrlPlaceholder(),
                            onChange: function (val) { setAttributes({ videoUrl: val }); }
                        })
                ),

                el(PanelBody, { title: __('Thumbnail', 'srm-video-player'), initialOpen: false },
                    el(MediaUploadCheck, null,
                        el(MediaUpload, {
                            onSelect: function (media) {
                                setAttributes({ thumbnailUrl: media.url, thumbnailId: media.id });
                            },
                            allowedTypes: ['image'],
                            value:        attributes.thumbnailId,
                            render:       function (ref) {
                                return el(
                                    'div',
                                    null,
                                    thumbnailUrl
                                        ? el('div', { className: 'srmvp-block-thumb-preview' },
                                            el('img', { src: thumbnailUrl, alt: '' }),
                                            el(Button, {
                                                onClick:   function () { setAttributes({ thumbnailUrl: '', thumbnailId: 0 }); },
                                                variant:   'link',
                                                isDestructive: true,
                                                className: 'srmvp-block-thumb-remove',
                                            }, __('Remove Thumbnail', 'srm-video-player'))
                                          )
                                        : null,
                                    el(Button, {
                                        onClick:   ref.open,
                                        variant:   'secondary',
                                        className: 'srmvp-block-btn-full',
                                    }, thumbnailUrl
                                        ? __('Change Thumbnail', 'srm-video-player')
                                        : __('Select Thumbnail', 'srm-video-player')
                                    )
                                );
                            }
                        })
                    )
                ),

                el(PanelBody, { title: __('Title & Style', 'srm-video-player'), initialOpen: false },
                    el(TextControl, {
                        label:       __('Video Title', 'srm-video-player'),
                        value:       videoTitle,
                        placeholder: __('Enter video title…', 'srm-video-player'),
                        onChange:    function (val) { setAttributes({ videoTitle: val }); }
                    }),
                    el('div', { className: 'srmvp-block-color-label' },
                        el('label', null, __('Title Color', 'srm-video-player'))
                    ),
                    el(ColorPicker, {
                        color:            titleColor,
                        enableAlpha:      false,
                        onChange:         function (val) {
                            if (val && val.hex) {
                                setAttributes({ titleColor: val.hex });
                            } else if (typeof val === 'string') {
                                setAttributes({ titleColor: val });
                            }
                        },
                    })
                )
            );

            var mainView;

            if (!hasVideo) {
                mainView = el(
                    Placeholder,
                    {
                        icon: el('svg', { viewBox: '0 0 24 24', fill: 'currentColor', width: 24, height: 24 },
                            el('path', { d: 'M21 3H3c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h18c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H3V5h18v14zM10 8v8l6-4-6-4z' })
                        ),
                        label:        __('SRM Video Player', 'srm-video-player'),
                        instructions: __('Select a video source from the settings panel on the right (or above on smaller screens).', 'srm-video-player'),
                        className:    'srmvp-block-placeholder',
                    },
                    el(
                        'div',
                        { className: 'srmvp-block-placeholder-actions' },
                        el(MediaUploadCheck, null,
                            el(MediaUpload, {
                                onSelect: function (media) {
                                    setAttributes({ videoUrl: media.url, sourceType: 'upload' });
                                },
                                allowedTypes: ['video'],
                                render: function (ref) {
                                    return el(Button, {
                                        onClick:  ref.open,
                                        variant:  'primary',
                                        className: 'srmvp-block-placeholder-btn',
                                    }, __('Select from Media Library', 'srm-video-player'));
                                }
                            })
                        ),
                        el('p', { className: 'srmvp-block-placeholder-or' }, __('or use the panel to paste a YouTube / Facebook / Google Drive URL', 'srm-video-player'))
                    )
                );
            } else {
                mainView = el(
                    'div',
                    {
                        className: 'srmvp-block-editor-preview',
                        style: {
                            background: '#1a1a1a',
                            borderRadius: '8px',
                            overflow: 'hidden',
                            minHeight: '200px',
                            position: 'relative',
                        }
                    },
                    thumbnailUrl
                        ? el('div', {
                              style: {
                                  backgroundImage: 'url(' + thumbnailUrl + ')',
                                  backgroundSize: 'cover',
                                  backgroundPosition: 'center',
                                  minHeight: '220px',
                                  display: 'flex',
                                  alignItems: 'center',
                                  justifyContent: 'center',
                              }
                          },
                              el('div', {
                                  style: {
                                      width: '64px', height: '64px',
                                      borderRadius: '50%',
                                      background: 'rgba(0,0,0,0.55)',
                                      display: 'flex',
                                      alignItems: 'center',
                                      justifyContent: 'center',
                                  }
                              },
                                  el('svg', { viewBox: '0 0 24 24', fill: '#fff', width: 36, height: 36 },
                                      el('path', { d: 'M8 5v14l11-7z' })
                                  )
                              )
                          )
                        : el('div', {
                              style: {
                                  minHeight: '180px',
                                  display: 'flex',
                                  alignItems: 'center',
                                  justifyContent: 'center',
                                  flexDirection: 'column',
                                  gap: '12px',
                              }
                          },
                              el('div', {
                                  style: {
                                      width: '64px', height: '64px',
                                      borderRadius: '50%',
                                      background: 'rgba(255,255,255,0.1)',
                                      display: 'flex',
                                      alignItems: 'center',
                                      justifyContent: 'center',
                                  }
                              },
                                  el('svg', { viewBox: '0 0 24 24', fill: '#fff', width: 36, height: 36 },
                                      el('path', { d: 'M8 5v14l11-7z' })
                                  )
                              ),
                              el('p', { style: { color: '#aaa', margin: 0, fontSize: '13px' } },
                                  getSourceLabel() + ': ' + (videoUrl.length > 50 ? videoUrl.substring(0, 50) + '…' : videoUrl)
                              )
                          ),
                    videoTitle
                        ? el('div', {
                              style: {
                                  padding: '10px 14px',
                                  color: titleColor,
                                  fontSize: '14px',
                                  fontWeight: '600',
                                  background: 'rgba(0,0,0,0.6)',
                              }
                          }, videoTitle)
                        : null,
                    el('div', {
                        style: {
                            padding: '6px 12px',
                            background: 'rgba(255,107,53,0.15)',
                            borderTop: '1px solid rgba(255,107,53,0.3)',
                            fontSize: '11px',
                            color: '#ff6b35',
                            textAlign: 'center',
                        }
                    }, __('SRM Video Player — live on frontend', 'srm-video-player'))
                );
            }

            return el(
                'div',
                { className: 'srmvp-block-wrap' },
                inspectorPanel,
                mainView
            );
        },

        save: function () {
            return null;
        }
    });

})();
