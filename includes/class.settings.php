<?php

namespace WFLR;

class Settings {

	/**
	 * @var Plugin
	 */
	protected $plugin;

	/**
	 * @var array
	 */
	protected $settings;

	/**
	 * Settings constructor.
	 *
	 * @param $plugin Plugin
	 */
	public function __construct( $plugin ) {
		$this->plugin   = $plugin;
		$this->settings = $this->settings();

		add_action( 'admin_menu', [ $this, 'add_options_page' ] );
		add_action( 'admin_init', [ $this, 'init_settings' ] );
	}

	/**
	 * Array of the settings
	 *
	 * @return array
	 */
	public function settings() {
		$settings = [
			'settings_group' => [ //unique slug of the settings group
				'sections' => [
					[
						'title'   => __( 'Freemius API settings', 'freemius-license-redemption' ),
						'slug'    => 'section_freemius',
						'options' => [
							'developer_id'  => [
								'title'             => __( 'Developer ID', 'freemius-license-redemption' ),
								'render_callback'   => [ $this, 'fill_text_field' ],
								'sanitize_callback' => [ $this, 'sanitize_callback' ],
							],
							'developer_public_key'  => [
								'title'             => __( 'Developer public key', 'freemius-license-redemption' ),
								'render_callback'   => [ $this, 'fill_text_field' ],
								'sanitize_callback' => [ $this, 'sanitize_callback' ],
							],
							'developer_secret_key'  => [
								'title'             => __( 'Developer secret key', 'freemius-license-redemption' ),
								'render_callback'   => [ $this, 'fill_text_field' ],
								'sanitize_callback' => [ $this, 'sanitize_callback' ],
							],
						],
					],
				]
			]
		];

		return $settings;
	}

	public function add_options_page() {
		add_options_page( __( 'Freemius License Redemption settings', 'freemius-license-redemption' ), __( 'Freemius License Redemption', 'freemius-license-redemption' ), 'manage_options', WFLR_PLUGIN_PREFIX . '_settings', function () {
			echo Plugin::render_template( 'settings-page', [ 'settings' => $this->settings ] );
		} );
	}

	public function init_settings() {
		foreach ( $this->settings as $group_slug => $group ) {
			$group_slug = WFLR_PLUGIN_PREFIX . '_' . $group_slug;
			foreach ( $group['sections'] as $section ) {
				$section_slug = WFLR_PLUGIN_PREFIX . '_' . $section['slug'];
				foreach ( $section['options'] as $opt_name => $option ) {
					$opt_name = WFLR_PLUGIN_PREFIX . '_' . $opt_name;
					register_setting( $group_slug, $opt_name, [
						'sanitize_callback' => $option['sanitize_callback'],
						'show_in_rest'      => false,
					] );
					add_settings_field( $opt_name, $option['title'], $option['render_callback'], WFLR_PLUGIN_PREFIX . '_settings_page', $section_slug, $opt_name );
				}
				add_settings_section( $section_slug, $section['title'], '', WFLR_PLUGIN_PREFIX . '_settings_page' );
			}
		}
	}

	/**
	 * Get settings option
	 *
	 * @param string $option_name
	 * @param string $default_value
	 *
	 * @return false|mixed|void
	 */
	public function getOption( $option_name, $default_value = '' ) {
		$option = get_option( WFLR_PLUGIN_PREFIX . '_' . $option_name );

		return $option ? $option : $default_value;
	}

	/**
	 * Add settings option
	 *
	 * @param string $option_name
	 * @param string $option_value
	 *
	 * @return bool
	 */
	public function addOption( $option_name, $option_value ) {
		return add_option( WFLR_PLUGIN_PREFIX . '_' . $option_name, $option_value );
	}

	/**
	 * Update settings option
	 *
	 * @param string $option_name
	 * @param string $option_value
	 *
	 * @return bool
	 */
	public function updateOption( $option_name, $option_value ) {
		return update_option( WFLR_PLUGIN_PREFIX . '_' . $option_name, $option_value );
	}

	/**
	 * Delete settings option
	 *
	 * @param string $option_name
	 *
	 * @return bool
	 */
	public function deleteOption( $option_name ) {
		return delete_option( WFLR_PLUGIN_PREFIX . '_' . $option_name );
	}

	/**
	 * @param $option_name
	 */
	function fill_text_field( $option_name ) {
		$val = get_option( $option_name );
		$val = $val ? $val : '';
		?>
        <input type="text" name="<?= $option_name; ?>" id="<?= $option_name; ?>"
               value="<?php echo esc_attr( $val ) ?>"/>
		<?php
	}

	/**
	 * @param $option_name
	 */
	function fill_checkbox_field( $option_name ) {
		$val   = get_option( $option_name );
		$val   = $val ? 1 : 0;
		$check = __( 'Check', 'freemius-license-redemption' );
		?>
        <label for="<?= $option_name; ?>">
            <input type="checkbox" name="<?= $option_name; ?>" id="<?= $option_name; ?>"
                   value="<?= $val; ?>" <?php checked( 1, $val ) ?> />
			<?= $check; ?>
        </label>
		<?php
	}

	/**
	 * @param mixed $value
	 *
	 * @return mixed
	 */
	function sanitize_callback( $value ) {
		if ( is_string( $value ) ) {
			return strip_tags( $value );
		}

		if ( is_numeric( $value ) ) {
			return intval( $value );
		}

		return $value;
	}
}