<?php 
$settings_slug_sanitized = $this->get_settings() . '_disable_plugin_updates';
$settings = get_option( $settings_slug_sanitized );
$plugins = get_plugins();
?>

<form action="options.php" method="post" class="options_form">
    <?php settings_errors( esc_attr( $settings_slug_sanitized ) . "_option_group" ); ?>
    <?php settings_fields( esc_attr( $settings_slug_sanitized ) . "_option_group" ); ?>

    <div class="itc_bg itc_width_xs margin-t30">
        <table class="form-table itc_table">
            <?php foreach ( $plugins as $plugin_file => $plugin_data ): ?>
                <?php 
                $plugin_key = $plugin_file; // Use plugin file as key
                $plugin_slug = dirname( $plugin_file ); // Get plugin slug
                $plugin_id = $plugin_slug;  
                ?>
                <tr valign="top" id="<?php echo esc_attr( $plugin_id ); ?>"> 
                    <th scope="row" class="menu_tbl_heading">
                        <label for="<?php echo esc_attr( $settings_slug_sanitized . "[" . $plugin_key . "]" ); ?>">
                            <span><?php echo esc_html( $plugin_data['Name'] ); ?></span>
                        </label>
                    </th>
                    <td>
                        <label class="form-switch">
                            <input class="checkbox" type="checkbox" id="<?php echo esc_attr( $settings_slug_sanitized . "[" . $plugin_key . "]" ); ?>" name="<?php echo esc_attr( $settings_slug_sanitized . "[" . $plugin_key . "]" ); ?>" value="1" <?php checked( 1, isset( $settings[$plugin_key] ) && $settings[$plugin_key] === 1 ); ?> />
                            <i></i>
                        </label>
                    </td>
                    <td>
                     </td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>

    <?php submit_button( __( 'Save Changes', 'disable-email-notification-for-auto-updates' ), 'primary itc_btn_sm' ); ?>
</form>
