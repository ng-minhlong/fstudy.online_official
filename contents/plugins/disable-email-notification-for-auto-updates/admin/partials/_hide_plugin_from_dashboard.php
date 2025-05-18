<?php 
$settings_slug_sanitized = $this->get_settings() . '_hide_plugin_from_dashboard';
$settings = get_option( $settings_slug_sanitized );
$plugins = get_plugins();
?>
<span class="itc_title_bold"><?php esc_html_e('Hides only from the Plugin list, not the admin bar.', 'disable-email-notification-for-auto-updates'); ?></span>
<form action="options.php" method="post" class="options_form">
    <?php settings_errors( esc_attr( $settings_slug_sanitized ) . "_option_group" ); ?>
    <?php settings_fields( esc_attr( $settings_slug_sanitized ) . "_option_group" ); ?>

    <div class="itc_bg itc_width_xs">
        <table class="form-table itc_table">
            <?php foreach ( $plugins as $plugin_file => $plugin_data ): ?>
                <?php 
                $plugin_key_hide = $plugin_file; // Use plugin file as key
                ?>
                <tr valign="top">
                    <th scope="row" class="menu_tbl_heading">
                        <label for="<?php echo esc_attr( $settings_slug_sanitized . "[" . $plugin_key_hide . "]" ); ?>">
                            <span><?php echo esc_html( $plugin_data['Name'] ); ?></span>
                        </label>
                    </th>
                    <td>
                        <label class="form-switch">
                            <input class="checkbox" type="checkbox" id="<?php echo esc_attr( $settings_slug_sanitized . "[" . $plugin_key_hide . "]" ); ?>" name="<?php echo esc_attr( $settings_slug_sanitized . "[" . $plugin_key_hide . "]" ); ?>" value="1" <?php checked( 1, isset( $settings[$plugin_key_hide] ) && $settings[$plugin_key_hide] === 1 ); ?> />
                            <i></i>
                        </label>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>

    <?php submit_button( __( 'Save Changes', 'disable-email-notification-for-auto-updates' ), 'primary itc_btn_sm' ); ?>