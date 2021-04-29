<?php

namespace WFLR;

/*
 * Main plugin class
 *
 * */

class Plugin {

	/**
	 * Settings class
	 *
	 * @var Settings
	 */
	public $settings;

	/**
	 * Settings class
	 *
	 * @var Freemius_Api
	 */
	public $freemius;

	/**
	 * Plugin constructor.
	 */
	public function __construct() {
		$this->settings = new Settings( $this );

		if ( wp_doing_ajax() ) {
			$dev_id = $this->settings->getOption( 'developer_id' );
			$dev_pk = $this->settings->getOption( 'developer_public_key' );
			$dev_sk = $this->settings->getOption( 'developer_secret_key' );
			// Init SDK.
			$this->freemius = new Freemius_Api( 'developer', $dev_id, $dev_pk, $dev_sk );
		}

		add_action( 'wp_enqueue_scripts', [ $this, 'wp_enqueue_assets' ] );

		add_shortcode( 'wflr_form', [ $this, 'shortcode' ] );

		add_action( 'wp_ajax_wflr_redeem', [ $this, 'redeem' ] );
		add_action( 'wp_ajax_nopriv_wflr_redeem', [ $this, 'redeem' ] );

	}

	public function wp_enqueue_assets() {
		//wp_enqueue_script( WFLR_PLUGIN_PREFIX . '_js', WFLR_PLUGIN_URL . "/assets/wflr_shortcode.js", [ 'jquery' ], '', true );
	}

	/**
	 * @param array $args
	 *
	 * @return string
	 */
	public function shortcode( $args ) {
		$args = shortcode_atts( [ 'plugin_id' => 0 ], $args );

		if ( isset( $args['plugin_id'] ) ) {
			$return = $this->render_template( 'shortcode', $args );
		}

		return $return ?? '';
	}

	public function redeem() {
		check_ajax_referer( 'wflr', 'nonce' );

		$plugin_id = $_POST['plugin_id'] ?? '';
		if ( empty( $plugin_id ) ) {
			wp_send_json_error( 'Invalid plugin id. Please contact the site developer.' );
		}

		$license_key = empty( $_POST['code'] ) ? '' : $this->sanitize( $_POST['code'] );
		if ( empty( $license_key ) ) {
			wp_send_json_error( 'Invalid AppSumo code. Please try again.' );
		}

		$firstname = $this->sanitize( $_POST['firstname'] );
		if ( empty( $firstname ) ) {
			wp_send_json_error( 'First name is empty. Please try again.' );
		}

		$lastname = $this->sanitize( $_POST['lastname'] );
		if ( empty( $lastname ) ) {
			wp_send_json_error( 'Last name is empty. Please try again.' );
		}

		$email = $this->sanitize( $_POST['email'], FILTER_VALIDATE_EMAIL );
		if ( empty( $email ) ) {
			wp_send_json_error( 'Invalid email address. Please try again.' );
		}

		$is_marketing_allowed = ! empty( $_POST['agree_email'] );

		// Freemius API request
		$license   = $this->freemius->Api( "/plugins/{$plugin_id}/licenses.json", 'PUT', array(
			'license_key'          => urlencode( $license_key ),
			'email'                => $email,
			'name'                 => "{$firstname} {$lastname}",
			// Add an opt-in checkbox: "[ ] Send me security & feature updates, educational content and offers."
			'is_marketing_allowed' => $is_marketing_allowed,
		) );

		if ( is_object( $license ) && ! empty( $license->user_id ) && is_numeric( $license->user_id ) ) {
			// Successful activation, email sent. Redirect user to success page or show some message.
			wp_send_json_success( $license->user_id );
		} else if ( ! empty( $license->error ) ) {
			$error = $license->error->message;
			wp_send_json_error( $error );
		} else {
			wp_send_json_error( 'unexpected error' );
		}
	}

	/**
	 * Grab the incoming form data
	 */
	public function sanitize( $str, $filter = FILTER_SANITIZE_STRING ) {
		if ( empty( trim( $str ) ) ) {
			return false;
		}
		$str = filter_var( trim( $str ), $filter );
		if ( empty( $str ) ) {
			return false;
		}

		return $str;
	}

	/**
	 * Method renders layout template
	 *
	 * @param string $template_name Template name without ".php"
	 * @param array $args Template arguments
	 *
	 * @return false|string
	 */
	public static function render_template( $template_name, $args = [] ) {
		$template_name = apply_filters( WFLR_PLUGIN_PREFIX . '/template/name', $template_name, $args );

		$path = WFLR_PLUGIN_DIR . "/templates/$template_name.php";
		if ( file_exists( $path ) ) {
			ob_start();
			include $path;

			return apply_filters( WFLR_PLUGIN_PREFIX . '/content/template', ob_get_clean(), $template_name, $args );
		} else {
			return apply_filters( WFLR_PLUGIN_PREFIX . '/message/template_not_found', __( 'This template does not exist!', 'freemius-license-redemption' ) );
		}
	}
}