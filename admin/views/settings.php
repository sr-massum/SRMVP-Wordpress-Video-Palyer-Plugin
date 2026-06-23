<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>
<div class="wrap srmvp-admin-wrap">
    <h1>
        <span class="dashicons dashicons-video-alt3"></span>
        <?php esc_html_e( 'SRM Video Player — Settings', 'srm-video-player' ); ?>
    </h1>

    <?php if ( ! empty( $_GET['updated'] ) ) : ?>
        <div class="notice notice-success is-dismissible">
            <p><?php esc_html_e( 'Settings saved.', 'srm-video-player' ); ?></p>
        </div>
    <?php endif; ?>

    <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
        <?php wp_nonce_field( 'srmvp_settings_nonce', 'srmvp_settings_nonce' ); ?>
        <input type="hidden" name="action" value="srmvp_save_settings">

        <div class="srmvp-settings-grid">

            <div class="srmvp-settings-card">
                <h2><?php esc_html_e( 'Appearance', 'srm-video-player' ); ?></h2>

                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="primary_color"><?php esc_html_e( 'Accent Color', 'srm-video-player' ); ?></label>
                        </th>
                        <td>
                            <input type="text" id="primary_color" name="primary_color"
                                value="<?php echo esc_attr( $settings['primary_color'] ); ?>"
                                class="srmvp-color-picker">
                            <p class="description"><?php esc_html_e( 'Main accent color used for progress bar, buttons, and highlights.', 'srm-video-player' ); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="player_theme"><?php esc_html_e( 'Player Theme', 'srm-video-player' ); ?></label>
                        </th>
                        <td>
                            <select id="player_theme" name="player_theme">
                                <option value="dark" <?php selected( $settings['player_theme'], 'dark' ); ?>>
                                    <?php esc_html_e( 'Dark (VLC-style)', 'srm-video-player' ); ?>
                                </option>
                                <option value="light" <?php selected( $settings['player_theme'], 'light' ); ?>>
                                    <?php esc_html_e( 'Light', 'srm-video-player' ); ?>
                                </option>
                            </select>
                        </td>
                    </tr>
                </table>
            </div>

            <div class="srmvp-settings-card">
                <h2><?php esc_html_e( 'Playback Options', 'srm-video-player' ); ?></h2>

                <table class="form-table">
                    <tr>
                        <th scope="row"><?php esc_html_e( 'Show Controls', 'srm-video-player' ); ?></th>
                        <td>
                            <label class="srmvp-toggle">
                                <input type="checkbox" name="show_controls" value="1" <?php checked( $settings['show_controls'] ); ?>>
                                <span class="srmvp-toggle-slider"></span>
                            </label>
                            <p class="description"><?php esc_html_e( 'Show or hide the player control bar.', 'srm-video-player' ); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php esc_html_e( 'Autoplay', 'srm-video-player' ); ?></th>
                        <td>
                            <label class="srmvp-toggle">
                                <input type="checkbox" name="autoplay" value="1" <?php checked( $settings['autoplay'] ); ?>>
                                <span class="srmvp-toggle-slider"></span>
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php esc_html_e( 'Loop', 'srm-video-player' ); ?></th>
                        <td>
                            <label class="srmvp-toggle">
                                <input type="checkbox" name="loop" value="1" <?php checked( $settings['loop'] ); ?>>
                                <span class="srmvp-toggle-slider"></span>
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php esc_html_e( 'Mute by Default', 'srm-video-player' ); ?></th>
                        <td>
                            <label class="srmvp-toggle">
                                <input type="checkbox" name="mute_default" value="1" <?php checked( $settings['mute_default'] ); ?>>
                                <span class="srmvp-toggle-slider"></span>
                            </label>
                        </td>
                    </tr>
                </table>
            </div>

            <div class="srmvp-settings-card srmvp-full-width">
                <h2><?php esc_html_e( 'Custom CSS', 'srm-video-player' ); ?></h2>
                <textarea id="custom_css" name="custom_css" rows="10" class="large-text code"><?php echo esc_textarea( $settings['custom_css'] ); ?></textarea>
                <p class="description"><?php esc_html_e( 'Add custom CSS to override player styles. This is added to every page where the player is embedded.', 'srm-video-player' ); ?></p>
            </div>

        </div>

        <p class="submit">
            <button type="submit" class="button button-primary button-large">
                <span class="dashicons dashicons-saved"></span>
                <?php esc_html_e( 'Save Settings', 'srm-video-player' ); ?>
            </button>
        </p>
    </form>
</div>
