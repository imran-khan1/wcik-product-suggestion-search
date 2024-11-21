<?php

class WCIK_Settings {
    public static function register_settings_page() {
        add_options_page(
            'WCIK Search Settings',
            'WCIK Search',
            'manage_options',
            'wcik-search-settings',
            [ self::class, 'render_settings_page' ]
        );
    }

    public static function render_settings_page() {
        if ( isset( $_POST['wcik_save_settings'] ) ) {
            update_option( 'wcik_enable_categories', isset( $_POST['enable_categories'] ) );
            update_option( 'wcik_enable_attributes', isset( $_POST['enable_attributes'] ) );
            update_option( 'wcik_enable_tags', isset( $_POST['enable_tags'] ) );
            update_option( 'wcik_enable_price', isset( $_POST['enable_price'] ) );
        }

        ?>
        <div class="wrap">
            <h1>WCIK Search Settings</h1>
            <form method="post">
                <label>
                    <input type="checkbox" name="enable_categories" 
                        <?php checked( get_option( 'wcik_enable_categories' ) ); ?> />
                    Enable Categories
                </label><br />
                <label>
                    <input type="checkbox" name="enable_attributes" 
                        <?php checked( get_option( 'wcik_enable_attributes' ) ); ?> />
                    Enable Attributes
                </label><br />
                <label>
                    <input type="checkbox" name="enable_tags" 
                        <?php checked( get_option( 'wcik_enable_tags' ) ); ?> />
                    Enable Tags
                </label><br />
                <label>
                    <input type="checkbox" name="enable_price" 
                        <?php checked( get_option( 'wcik_enable_price' ) ); ?> />
                    Enable Price Range
                </label><br />
                <button type="submit" name="wcik_save_settings" class="button button-primary">
                    Save Settings
                </button>
            </form>
        </div>
        <?php
    }
}
