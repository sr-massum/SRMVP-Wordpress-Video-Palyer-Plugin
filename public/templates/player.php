<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! function_exists( 'srmvp_get_embed_url' ) ) {
    function srmvp_get_embed_url( $url, $source_type ) {
        switch ( $source_type ) {
            case 'youtube':
                preg_match( '/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/i', $url, $matches );
                if ( isset( $matches[1] ) ) {
                    return 'https://www.youtube.com/embed/' . esc_attr( $matches[1] ) . '?enablejsapi=1&rel=0&modestbranding=1';
                }
                return '';
            case 'facebook':
                return 'https://www.facebook.com/plugins/video.php?href=' . rawurlencode( $url ) . '&show_text=false&appId';
            case 'gdrive':
                preg_match( '/\/d\/([a-zA-Z0-9_-]+)/', $url, $matches );
                if ( isset( $matches[1] ) ) {
                    return 'https://drive.google.com/file/d/' . esc_attr( $matches[1] ) . '/preview';
                }
                return '';
            default:
                return '';
        }
    }
}

$display_title = isset( $video_title ) ? $video_title : ( isset( $post ) && $post ? $post->post_title : '' );
$player_id     = 'srmvp-' . $video_id . '-' . uniqid();
$is_embed      = in_array( $source_type, array( 'youtube', 'facebook', 'gdrive' ) );
$embed_url     = $is_embed ? srmvp_get_embed_url( $video_url, $source_type ) : '';
?>
<div
    id="<?php echo esc_attr( $player_id ); ?>"
    class="srmvp-player-wrap srmvp-theme-<?php echo esc_attr( $theme ); ?>"
    data-autoplay="<?php echo esc_attr( $autoplay ); ?>"
    data-loop="<?php echo esc_attr( $loop ); ?>"
    data-muted="<?php echo esc_attr( $muted ); ?>"
    data-source="<?php echo esc_attr( $source_type ); ?>"
    data-accent="<?php echo esc_attr( $accent ); ?>"
>
    <div class="srmvp-video-container">

        <?php if ( $is_embed && $embed_url ) : ?>
            <?php if ( $thumbnail ) : ?>
                <div class="srmvp-poster srmvp-iframe-poster" style="background-image: url('<?php echo esc_url( $thumbnail ); ?>');" data-url="<?php echo esc_url( $embed_url ); ?>">
                    <div class="srmvp-center-play">
                        <svg viewBox="0 0 24 24" fill="currentColor"><path d="M8 5v14l11-7z"/></svg>
                    </div>
                </div>
            <?php endif; ?>
            <div class="srmvp-iframe-wrap <?php echo $thumbnail ? 'srmvp-iframe-hidden' : ''; ?>">
                <iframe
                    src="<?php echo $thumbnail ? '' : esc_url( $embed_url ); ?>"
                    data-src="<?php echo esc_url( $embed_url ); ?>"
                    class="srmvp-iframe"
                    allowfullscreen
                    allow="autoplay; fullscreen"
                    frameborder="0"
                    loading="lazy"
                ></iframe>
            </div>

        <?php else : ?>
            <div class="srmvp-spinner" aria-label="Loading">
                <div class="srmvp-spinner-ring"></div>
            </div>

            <?php if ( $thumbnail ) : ?>
                <div class="srmvp-poster" style="background-image: url('<?php echo esc_url( $thumbnail ); ?>');">
                    <div class="srmvp-center-play">
                        <svg viewBox="0 0 24 24" fill="currentColor"><path d="M8 5v14l11-7z"/></svg>
                    </div>
                </div>
            <?php else : ?>
                <div class="srmvp-center-play srmvp-center-play-novideo">
                    <svg viewBox="0 0 24 24" fill="currentColor"><path d="M8 5v14l11-7z"/></svg>
                </div>
            <?php endif; ?>

            <video
                class="srmvp-video"
                src="<?php echo esc_url( $video_url ); ?>"
                preload="metadata"
                <?php echo $muted === 'true' ? 'muted' : ''; ?>
                <?php echo $loop === 'true' ? 'loop' : ''; ?>
                playsinline
            ></video>

            <?php if ( $show_controls ) : ?>
            <div class="srmvp-controls">
                <div class="srmvp-progress-wrap">
                    <div class="srmvp-progress-bar">
                        <div class="srmvp-progress-buffered"></div>
                        <div class="srmvp-progress-played"></div>
                        <div class="srmvp-progress-thumb"></div>
                    </div>
                </div>

                <div class="srmvp-controls-row">
                    <div class="srmvp-controls-left">
                        <button class="srmvp-btn srmvp-btn-play" aria-label="<?php esc_attr_e( 'Play', 'srm-video-player' ); ?>">
                            <svg class="srmvp-icon-play" viewBox="0 0 24 24" fill="currentColor"><path d="M8 5v14l11-7z"/></svg>
                            <svg class="srmvp-icon-pause" viewBox="0 0 24 24" fill="currentColor" style="display:none"><path d="M6 19h4V5H6v14zm8-14v14h4V5h-4z"/></svg>
                        </button>

                        <div class="srmvp-volume-wrap">
                            <button class="srmvp-btn srmvp-btn-mute" aria-label="<?php esc_attr_e( 'Mute', 'srm-video-player' ); ?>">
                                <svg class="srmvp-icon-volume" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M3 9v6h4l5 5V4L7 9H3zm13.5 3c0-1.77-1.02-3.29-2.5-4.03v8.05c1.48-.73 2.5-2.25 2.5-4.02zM14 3.23v2.06c2.89.86 5 3.54 5 6.71s-2.11 5.85-5 6.71v2.06c4.01-.91 7-4.49 7-8.77s-2.99-7.86-7-8.77z"/>
                                </svg>
                                <svg class="srmvp-icon-muted" viewBox="0 0 24 24" fill="currentColor" style="display:none">
                                    <path d="M16.5 12c0-1.77-1.02-3.29-2.5-4.03v2.21l2.45 2.45c.03-.2.05-.41.05-.63zm2.5 0c0 .94-.2 1.82-.54 2.64l1.51 1.51C20.63 14.91 21 13.5 21 12c0-4.28-2.99-7.86-7-8.77v2.06c2.89.86 5 3.54 5 6.71zM4.27 3L3 4.27 7.73 9H3v6h4l5 5v-6.73l4.25 4.25c-.67.52-1.42.93-2.25 1.18v2.06c1.38-.31 2.63-.95 3.69-1.81L19.73 21 21 19.73l-9-9L4.27 3zM12 4L9.91 6.09 12 8.18V4z"/>
                                </svg>
                            </button>
                            <input type="range" class="srmvp-volume-slider" min="0" max="1" step="0.05" value="1" aria-label="<?php esc_attr_e( 'Volume', 'srm-video-player' ); ?>">
                        </div>

                        <div class="srmvp-time">
                            <span class="srmvp-current-time">0:00</span>
                            <span class="srmvp-time-sep"> / </span>
                            <span class="srmvp-duration">0:00</span>
                        </div>
                    </div>

                    <div class="srmvp-controls-right">
                        <div class="srmvp-speed-wrap">
                            <button class="srmvp-btn srmvp-btn-speed" aria-label="<?php esc_attr_e( 'Playback Speed', 'srm-video-player' ); ?>">
                                <span class="srmvp-speed-label">1x</span>
                            </button>
                            <div class="srmvp-speed-menu">
                                <?php foreach ( array( 0.5, 0.75, 1, 1.25, 1.5, 2 ) as $speed ) : ?>
                                    <button class="srmvp-speed-opt <?php echo $speed === 1 ? 'active' : ''; ?>" data-speed="<?php echo esc_attr( $speed ); ?>">
                                        <?php echo esc_html( $speed . 'x' ); ?>
                                    </button>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <button class="srmvp-btn srmvp-btn-fullscreen" aria-label="<?php esc_attr_e( 'Fullscreen', 'srm-video-player' ); ?>">
                            <svg class="srmvp-icon-fullscreen" viewBox="0 0 24 24" fill="currentColor"><path d="M7 14H5v5h5v-2H7v-3zm-2-4h2V7h3V5H5v5zm12 7h-3v2h5v-5h-2v3zM14 5v2h3v3h2V5h-5z"/></svg>
                            <svg class="srmvp-icon-exit-fullscreen" viewBox="0 0 24 24" fill="currentColor" style="display:none"><path d="M5 16h3v3h2v-5H5v2zm3-8H5v2h5V5H8v3zm6 11h2v-3h3v-2h-5v5zm2-11V5h-2v5h5V8h-3z"/></svg>
                        </button>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>

    <?php if ( $display_title ) : ?>
        <div class="srmvp-video-title" style="color: <?php echo esc_attr( $title_color ); ?>">
            <?php echo esc_html( $display_title ); ?>
        </div>
    <?php endif; ?>
</div>
