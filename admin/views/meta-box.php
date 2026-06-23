<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>
<div class="srmvp-meta-box">

    <div class="srmvp-field-row">
        <label class="srmvp-label"><?php esc_html_e( 'Video Source Type', 'srm-video-player' ); ?></label>
        <div class="srmvp-source-tabs">
            <?php
            $sources = array(
                'upload'   => array( 'label' => __( 'Upload / Media Library', 'srm-video-player' ), 'icon' => 'dashicons-upload' ),
                'youtube'  => array( 'label' => __( 'YouTube', 'srm-video-player' ), 'icon' => 'dashicons-video-alt3' ),
                'facebook' => array( 'label' => __( 'Facebook', 'srm-video-player' ), 'icon' => 'dashicons-facebook' ),
                'gdrive'   => array( 'label' => __( 'Google Drive', 'srm-video-player' ), 'icon' => 'dashicons-google' ),
            );
            foreach ( $sources as $value => $data ) : ?>
                <label class="srmvp-source-tab <?php echo $source_type === $value ? 'active' : ''; ?>">
                    <input type="radio" name="srmvp_source_type" value="<?php echo esc_attr( $value ); ?>" <?php checked( $source_type, $value ); ?>>
                    <span class="dashicons <?php echo esc_attr( $data['icon'] ); ?>"></span>
                    <?php echo esc_html( $data['label'] ); ?>
                </label>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="srmvp-field-row srmvp-video-url-row">
        <label for="srmvp_video_url" class="srmvp-label">
            <span class="srmvp-url-label srmvp-label-upload"><?php esc_html_e( 'Video File', 'srm-video-player' ); ?></span>
            <span class="srmvp-url-label srmvp-label-external" style="display:none"><?php esc_html_e( 'Video URL', 'srm-video-player' ); ?></span>
        </label>
        <div class="srmvp-url-input-wrap">
            <input type="url" id="srmvp_video_url" name="srmvp_video_url"
                value="<?php echo esc_url( $video_url ); ?>"
                class="large-text srmvp-url-input"
                placeholder="<?php esc_attr_e( 'Paste URL or upload file...', 'srm-video-player' ); ?>">
            <button type="button" class="button srmvp-media-select srmvp-upload-btn">
                <span class="dashicons dashicons-video-alt3"></span>
                <?php esc_html_e( 'Select from Media Library', 'srm-video-player' ); ?>
            </button>
        </div>
        <p class="srmvp-field-help srmvp-help-youtube" style="display:none">
            <?php esc_html_e( 'YouTube URL (e.g. https://www.youtube.com/watch?v=...)', 'srm-video-player' ); ?>
        </p>
        <p class="srmvp-field-help srmvp-help-facebook" style="display:none">
            <?php esc_html_e( 'Facebook video URL (e.g. https://www.facebook.com/watch?v=...)', 'srm-video-player' ); ?>
        </p>
        <p class="srmvp-field-help srmvp-help-gdrive" style="display:none">
            <?php esc_html_e( 'Google Drive share link (must be set to "Anyone with link")', 'srm-video-player' ); ?>
        </p>
    </div>

    <div class="srmvp-field-row">
        <label class="srmvp-label"><?php esc_html_e( 'Custom Thumbnail', 'srm-video-player' ); ?></label>
        <div class="srmvp-thumb-wrap">
            <div class="srmvp-thumb-preview">
                <?php if ( $thumb_url ) : ?>
                    <img src="<?php echo esc_url( $thumb_url ); ?>" alt="">
                <?php else : ?>
                    <span class="srmvp-thumb-placeholder"><span class="dashicons dashicons-format-image"></span></span>
                <?php endif; ?>
            </div>
            <input type="hidden" name="srmvp_thumbnail" id="srmvp_thumbnail" value="<?php echo esc_attr( $thumbnail ); ?>">
            <div class="srmvp-thumb-actions">
                <button type="button" class="button srmvp-thumb-select">
                    <?php esc_html_e( 'Select Image', 'srm-video-player' ); ?>
                </button>
                <?php if ( $thumbnail ) : ?>
                    <button type="button" class="button srmvp-thumb-remove">
                        <?php esc_html_e( 'Remove', 'srm-video-player' ); ?>
                    </button>
                <?php endif; ?>
            </div>
            <p class="description"><?php esc_html_e( 'Overrides the featured image as the video poster.', 'srm-video-player' ); ?></p>
        </div>
    </div>

    <div class="srmvp-field-row">
        <label for="srmvp_title_color" class="srmvp-label"><?php esc_html_e( 'Title Color', 'srm-video-player' ); ?></label>
        <input type="text" id="srmvp_title_color" name="srmvp_title_color"
            value="<?php echo esc_attr( $title_color ); ?>"
            class="srmvp-color-picker">
    </div>

</div>
