<?php 
$settings_slug_sanitized = $this->get_settings();
$settings = $this->get_option();
$default_setting_sanitized_plugin = esc_attr($settings['plugin']);
$default_setting_sanitized_themes = esc_attr($settings['themes']);
$default_setting_sanitized_wordpress = esc_attr($settings['wordpress']);
$default_setting_sanitized_wp_update_button = esc_attr($settings['wp_update_button']);
$default_setting_sanitized_wp_core = esc_attr($settings['wp_core']);
$default_setting_sanitized_wp_themes = esc_attr($settings['wp_themes']);
?>

<form action="options.php" method="post" class="options_form">
    <?php settings_errors( esc_attr($settings_slug_sanitized) . "_option_group" ); ?>
    <?php settings_fields( esc_attr($settings_slug_sanitized) . "_option_group" ); ?>
    <div class="itc_bg itc_width_xs margin-t30">
        <table class="form-table itc_table">
            <tr valign="top">
                <th scope="row" class="menu_tbl_heading">
                    <label for="<?php echo esc_attr($settings_slug_sanitized); ?>[plugin]">
                        <span><?php esc_html_e('Disable Email Notification (Plugins)', 'disable-email-notification-for-auto-updates'); ?></span>
                    </label>
                </th>
                <td>
                    <label class="form-switch">
                        <input class="checkbox" type="checkbox" id="<?php echo esc_attr($settings_slug_sanitized); ?>[plugin]" name="<?php echo esc_attr($settings_slug_sanitized); ?>[plugin]" value="1" <?php checked( 1, isset($default_setting_sanitized_plugin) && $default_setting_sanitized_plugin == "1"); ?> />
                        <i></i>
                    </label>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row" class="menu_tbl_heading">
                    <label for="<?php echo esc_attr($settings_slug_sanitized); ?>[themes]">
                        <span><?php esc_html_e('Disable Email Notification (Themes)', 'disable-email-notification-for-auto-updates'); ?></span>
                    </label>
                </th>
                <td>
                    <label class="form-switch">
                        <input class="checkbox" type="checkbox" id="<?php echo esc_attr($settings_slug_sanitized); ?>[themes]" name="<?php echo esc_attr($settings_slug_sanitized); ?>[themes]" value="1" <?php checked( 1, isset($default_setting_sanitized_themes) && $default_setting_sanitized_themes == "1"); ?> />
                        <i></i>
                    </label>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row" class="menu_tbl_heading">
                    <label for="<?php echo esc_attr($settings_slug_sanitized); ?>[wordpress]">
                        <span><?php esc_html_e('Disable Email Notification (WordPress)', 'disable-email-notification-for-auto-updates'); ?></span>
                    </label>
                </th>
                <td>
                    <label class="form-switch">
                        <input class="checkbox" type="checkbox" id="<?php echo esc_attr($settings_slug_sanitized); ?>[wordpress]" name="<?php echo esc_attr($settings_slug_sanitized); ?>[wordpress]" value="1" <?php checked( 1, isset($default_setting_sanitized_wordpress) && $default_setting_sanitized_wordpress == "1"); ?> />
                        <i></i>
                    </label>
                </td>
            </tr>
        </table>
    </div>
    <div class="itc_bg itc_width_xs margin-t30">
        <table class="form-table itc_table">
            <tr valign="top">
                <th scope="row" class="menu_tbl_heading">
                    <label for="<?php echo esc_attr($settings_slug_sanitized); ?>[wp_update_button]">
                        <span><?php esc_html_e('Remove Update Button (Dashboard)', 'disable-email-notification-for-auto-updates'); ?></span>
                    </label>
                </th>
                <td>
                    <label class="form-switch">
                        <input class="checkbox" type="checkbox" id="<?php echo esc_attr($settings_slug_sanitized); ?>[wp_update_button]" name="<?php echo esc_attr($settings_slug_sanitized); ?>[wp_update_button]" value="1" <?php checked( 1, isset($default_setting_sanitized_wp_update_button) && $default_setting_sanitized_wp_update_button == "1"); ?> />
                        <i></i>
                    </label>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row" class="menu_tbl_heading">
                    <label for="<?php echo esc_attr($settings_slug_sanitized); ?>[wp_core]">
                        <span><?php esc_html_e('Block WordPress Core updates', 'disable-email-notification-for-auto-updates'); ?></span>
                    </label>
                </th>
                <td>
                    <label class="form-switch">
                        <input class="checkbox" type="checkbox" id="<?php echo esc_attr($settings_slug_sanitized); ?>[wp_core]" name="<?php echo esc_attr($settings_slug_sanitized); ?>[wp_core]" value="1" <?php checked( 1, isset($default_setting_sanitized_wp_core) && $default_setting_sanitized_wp_core == "1"); ?> />
                        <i></i>
                    </label>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row" class="menu_tbl_heading">
                    <label for="<?php echo esc_attr($settings_slug_sanitized); ?>[wp_themes]">
                        <span><?php esc_html_e('Block WordPress Theme updates', 'disable-email-notification-for-auto-updates'); ?></span>
                    </label>
                </th>
                <td>
                    <label class="form-switch">
                        <input class="checkbox" type="checkbox" id="<?php echo esc_attr($settings_slug_sanitized); ?>[wp_themes]" name="<?php echo esc_attr($settings_slug_sanitized); ?>[wp_themes]" value="1" <?php checked( 1, isset($default_setting_sanitized_wp_themes) && $default_setting_sanitized_wp_themes == "1"); ?> />
                        <i></i>
                    </label>
                </td>
            </tr>
        </table>
    </div>
    <?php 
    submit_button( esc_html__('Save Changes', 'disable-email-notification-for-auto-updates'), 'primary itc_btn_sm' );
    ?>
</form>

<tr valign="top">
    <th scope="row" class="menu_tbl_heading">
        <span class="itc_title_bold"><?php esc_html_e('IMPORTANT!', 'disable-email-notification-for-auto-updates'); ?></span>
        <span><?php esc_html_e('- IT IS NOT RECOMMENDED TO DISABLE WORDPRESS CORE UPDATES, OR EVEN THEME AND PLUGIN UPDATES. ALWAYS KEEP THEM UPDATED FOR SECURITY REASONS.', 'disable-email-notification-for-auto-updates'); ?></span>
        <br><span><?php esc_html_e('- ALWAYS KEEP THEM UPDATED FOR SECURITY REASONS.', 'disable-email-notification-for-auto-updates'); ?></span>
    </th>
</tr>
