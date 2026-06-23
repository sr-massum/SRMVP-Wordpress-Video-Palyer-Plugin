<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class SRMVP_Block {

    public function init() {
        $this->register_block();
    }

    public function register_block() {
        if ( ! function_exists( 'register_block_type' ) ) {
            return;
        }

        wp_register_script(
            'srmvp-block-editor',
            SRMVP_PLUGIN_URL . 'assets/js/block-editor.js',
            array( 'wp-blocks', 'wp-element', 'wp-block-editor', 'wp-components', 'wp-i18n' ),
            SRMVP_VERSION,
            true
        );

        register_block_type( 'srm-video-player/player', array(
            'editor_script'   => 'srmvp-block-editor',
            'render_callback' => array( $this, 'render_block' ),
            'attributes'      => array(
                'videoUrl'     => array( 'type' => 'string',  'default' => '' ),
                'videoTitle'   => array( 'type' => 'string',  'default' => '' ),
                'thumbnailUrl' => array( 'type' => 'string',  'default' => '' ),
                'thumbnailId'  => array( 'type' => 'integer', 'default' => 0 ),
                'sourceType'   => array( 'type' => 'string',  'default' => 'upload' ),
                'titleColor'   => array( 'type' => 'string',  'default' => '#ffffff' ),
            ),
        ) );
    }

    public function render_block( $attributes ) {
        $video_url    = esc_url_raw( $attributes['videoUrl'] ?? '' );
        $video_title  = sanitize_text_field( $attributes['videoTitle'] ?? '' );
        $thumbnail    = esc_url_raw( $attributes['thumbnailUrl'] ?? '' );
        $source_type  = sanitize_text_field( $attributes['sourceType'] ?? 'upload' );
        $title_color  = sanitize_text_field( $attributes['titleColor'] ?? '#ffffff' );

        if ( ! $video_url && $source_type === 'upload' ) {
            return '<p class="srmvp-error">' . esc_html__( 'No video selected.', 'srm-video-player' ) . '</p>';
        }

        $settings      = SRMVP_Settings::get();
        $autoplay      = $settings['autoplay'] ? 'true' : 'false';
        $loop          = $settings['loop'] ? 'true' : 'false';
        $muted         = $settings['mute_default'] ? 'true' : 'false';
        $show_controls = $settings['show_controls'];
        $theme         = esc_attr( $settings['player_theme'] );
        $accent        = esc_attr( $settings['primary_color'] );

        $video_id = 'block-' . uniqid();

        ob_start();
        include SRMVP_PLUGIN_DIR . 'public/templates/player.php';
        return ob_get_clean();
    }
}
