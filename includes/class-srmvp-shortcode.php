<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class SRMVP_Shortcode {

    public function init() {
        add_shortcode( 'srmvp', array( $this, 'render' ) );
    }

    public function render( $atts ) {
        $atts = shortcode_atts( array(
            'id' => 0,
        ), $atts, 'srmvp' );

        $video_id = absint( $atts['id'] );
        if ( ! $video_id ) {
            return '<p class="srmvp-error">' . esc_html__( 'Invalid video ID.', 'srm-video-player' ) . '</p>';
        }

        $post = get_post( $video_id );
        if ( ! $post || $post->post_type !== 'srm_video' || $post->post_status !== 'publish' ) {
            return '<p class="srmvp-error">' . esc_html__( 'Video not found.', 'srm-video-player' ) . '</p>';
        }

        $settings     = SRMVP_Settings::get();
        $source_type  = get_post_meta( $video_id, '_srmvp_source_type', true ) ?: 'upload';
        $video_url    = get_post_meta( $video_id, '_srmvp_video_url', true );
        $title_color  = get_post_meta( $video_id, '_srmvp_title_color', true ) ?: '#ffffff';
        $thumbnail    = get_the_post_thumbnail_url( $video_id, 'large' );

        if ( ! $thumbnail ) {
            $custom_thumb = get_post_meta( $video_id, '_srmvp_thumbnail', true );
            $thumbnail    = $custom_thumb ? wp_get_attachment_image_url( $custom_thumb, 'large' ) : '';
        }

        $autoplay      = $settings['autoplay'] ? 'true' : 'false';
        $loop          = $settings['loop'] ? 'true' : 'false';
        $muted         = $settings['mute_default'] ? 'true' : 'false';
        $show_controls = $settings['show_controls'];
        $theme         = esc_attr( $settings['player_theme'] );
        $accent        = esc_attr( $settings['primary_color'] );

        ob_start();
        include SRMVP_PLUGIN_DIR . 'public/templates/player.php';
        return ob_get_clean();
    }
}
