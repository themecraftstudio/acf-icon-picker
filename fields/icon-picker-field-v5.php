<?php if( ! defined( 'ABSPATH' ) ) exit;
/**
 * Class tc_acf_field_icon_picker
 *
 * TODO
 *   - modal: filter by whatever
 * 
 * 
 * NOTES for front-end SVG integration:
Both use a simple string, so get_field() should return the icon class/id

1) drop .svg in assets/icons/
2) use gulp-svg-symbols to generate a single file with symbols
3) load the file and use the symbols, OR only embed used symbols in page..

ACF Icon Picker deals with: returning the symbol or class
 */
class tc_acf_field_icon_picker extends acf_field
{
    /** @var array */
    protected $settings;

	function __construct( $settings )
    {
        $this->name = 'icon-picker';
		$this->label = __('Icon Picker', 'tc-acf-icon-picker-field');
		$this->category = 'jquery';
		$this->defaults = array(
			'font_size'	=> 14,
		);

        $this->settings = wp_parse_args($settings, [
            // any other default settings
        ]);

        $this->register_scripts();

        acf_localize_data(['icon_picker' => [
            'icons' => $this->get_icon_list()
        ]]);

    	parent::__construct();
	}


    /*
    * TODO: options to output inlined SVG, ajax loaded SVG
	*  render_field()
	*
	*  Create the HTML interface for your field
	*
	*  @param	$field (array) the $field being rendered
	*
	*  @type	action
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	$field (array) the $field being edited
	*  @return	n/a
	*/
	function render_field( $field )
    {
        $iconPath = $this->settings['icon_path'] . $field['value'];
        $iconUrl = $this->settings['icon_url'] . $field['value'];

        if (is_file($iconPath))
            echo sprintf('<div class="icon-preview"><a><img height="32" width="32" src="%s"/></a></div>', $iconUrl);

            // TODO add support for SVG viewBox and preserveAspectRatio attribute

        $name = esc_attr($field['name']);
        $value = esc_attr($field['value']);

        echo <<<HTML
            <input type="text" name="$name" value="$value"/>
HTML;

    }
    
	function input_admin_footer()
    {
        wp_enqueue_script('a11y-dialog');
        wp_enqueue_script('tc-acf-field-icon-picker');
	}

	/**
     * Invoked from the template via get_field(name, post, format=true);
     * 
     * By detecting whether we are returning a front icon class or an SVG image,
     * it is possible to add SVG's <symbol>s in the footer;
	 * Idea: when format is invoked, return <svg></svg>. When it is not, return
     * 
     * // SVG stored as relative path in the database
     * // class icons stored as string
	 */
	function format_value( $value, $post_id, $field )
    {
		if (empty($value))
			return $value;

        $pathname = $this->settings['icon_path'] . ltrim($value, '/');
        if (substr($pathname, -4, 4) === '.svg' && is_file($pathname)) {
            $symbolId = 'svg-icon-'. basename($pathname, '.svg');

            $value = $symbolId;
        }
        // else $value is expected to be an icon class

		return $value;
	}

    /**
     * Undocumented function
     *
     * @return array
     */
    protected function get_icon_list(): array
    {
        $list = [];
        foreach ($icons = scandir($this->settings['icon_path']) as $path) {
            $pathname = $this->settings['icon_path'].$path;

            if (!is_file($pathname))
                continue;

            $name = basename($pathname);
            $relPath = substr($pathname, strlen($this->settings['icon_path']));

            $list[] = [
                'id' => $relPath, // must be unique
                'type' => 'svg',
                'url' => $this->settings['icon_url'].$path
            ];
        }

        return $list;
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    function register_scripts(): void
    {
        $debug = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '' : '.min';

        wp_register_script('a11y-dialog', $this->settings['url'].'assets/js/a11y-dialog'.$debug.'.js', [], '5.1.2', true);
        wp_register_script('tc-acf-field-icon-picker', $this->settings['url'].'assets/js/input.js', [], '0.0.1', true);
    }

}
