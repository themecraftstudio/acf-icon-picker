<?php
/*
 * Plugin Name: Advanced Custom Fields: Icon Picker
 * Plugin URI:  https://github.com/themecraftstudio/wordpress-acf-icon-picker
 * Description: Icon Picker field for Advanced Custom Fields.
 * Version:     0.0.1
 * Author:      Themecraft Studio
 * Author URI:  https://themecraft.studio
 * License:     GPL2
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: tc-acf-icon-picker-field
 * Domain Path: /languages
 * GitHub Plugin URI: https://github.com/themecraftstudio/wordpress-acf-icon-picker

 ACF Icon Picker is free software: you can redistribute it and/or modify
 it under the terms of the GNU General Public License as published by
 the Free Software Foundation, either version 2 of the License, or
 any later version.

 ACF Icon Picker is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 GNU General Public License for more details.

 You should have received a copy of the GNU General Public License
 along with Content Attachments. If not, see LICENSE.txt .
 */

// exit if accessed directly
if( ! defined( 'ABSPATH' ) ) exit;

if (!class_exists(tc_acf_plugin_icon_picker::class)):

class tc_acf_plugin_icon_picker
{
    /** @var array  */
	protected $settings;

	function __construct()
    {
        load_plugin_textdomain( 'tc-acf-icon-picker-field', false, plugin_basename( dirname( __FILE__ ) ) . '/languages' );

		// include field
		add_action('acf/include_field_types', 	array($this, 'include_field'));
	}

    /**
     * @param bool $version
     *
     * Loads the field.
     * Notice that this function is executed *after* themes have been loaded.
     */
	function include_field( $version = false )
    {
        $settings = [
            'version' => '0.0.1',
            'url' => apply_filters('acf/fields/icon_picker/url', plugin_dir_url(__FILE__)),
            'path' => apply_filters('acf/fields/icon_picker/path', plugin_dir_path(__FILE__)),
            'icon_path' => apply_filters('acf/fields/icon_picker/icon_path', get_theme_file_path('assets/icons/')),
            'icon_url' => apply_filters('acf/fields/icon_picker/icon_url', get_theme_file_uri('assets/icons/'))
        ];

		// support empty $version
		if( !$version ) $version = 4;

        // include
        require_once __DIR__ .'/fields/icon-picker-field-v'.$version.'.php';

        new tc_acf_field_icon_picker($settings);
	}

}

add_action('after_setup_theme', function () {
    new tc_acf_plugin_icon_picker();
});

endif;
