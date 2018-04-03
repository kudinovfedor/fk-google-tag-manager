<?php
/*
Plugin Name: FK Google Tag Manager
Plugin URI: https://joompress.biz
Description: This is an implementation of the new Tag Management system from Google. It adds a field to the existing General Settings page for the ID.
Version: 1.0.0
Author: Kudinov Fedor
Author URI: https://joompress.biz
License: GPLv2 or later
Text Domain: gtm
Domain Path: /languages
*/

/**
 * Class FK_Google_Tag_Manager
 *
 * @author Kudinov Fedor <admin@joompress.biz>
 */
class FK_Google_Tag_Manager {
	/**
	 * Container ID
	 *
	 * @var string|bool $ID
	 */
	private $ID;

	/**
	 * FK_Google_Tag_Manager constructor.
	 */
	public function __construct() {
		$this->ID = get_option( 'gtm_id' );
		add_action( 'admin_init', array( $this, 'settings' ) );
		add_action( 'plugins_loaded', array( $this, 'textDomain' ) );

		if ( $this->ID ) {
			add_action( 'wp_head', array( $this, 'printHead' ) );
			add_action( 'wp_body', array( $this, 'printBody' ) );
		}

		if ( ! has_action( 'wp_body' ) ) {
			add_action( 'admin_notices', array( $this, 'notification' ) );
		}
	}

	/**
	 * Get container ID
	 *
	 * @return string|bool
	 */
	public function getID() {
		return $this->ID;
	}

	/**
	 * Get array of containers IDs
	 *
	 * @return array
	 */
	public function getArrayIDs() {
		return explode(
			',',
			str_replace(
				array( ';', ' ' ),
				array( ',', '' ),
				$this->getID()
			)
		);
	}

	/**
	 * Loads a plugin's translated strings.
	 *
	 * @return void
	 */
	public function textDomain() {
		load_plugin_textdomain( 'gtm', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
	}

	/**
	 * Display the admin notification
	 *
	 * @return void
	 */
	public function notification() {
		$output = sprintf(
			'<div class="notice notice-warning is-dismissible"><p>%s</p></div>',
			__( 'Add a hook <b><code>do_action(\'wp_body\')</code></b> after opening tag <b>&lt;body&gt;</b>', 'gtm' )
		);

		echo $output;
	}

	/**
	 * Register a setting and its data.
	 *
	 * @return void
	 */
	public function settings() {
		register_setting( 'general', 'gtm_id', 'esc_attr' );
		$gtm_title = sprintf(
			'<label for="gtm_id">%s</label>',
			__( 'Google Tag Manager ID', 'gtm' )
		);
		add_settings_field( 'gtm_id', $gtm_title, array( $this, 'field_callback' ), 'general' );
	}

	/**
	 * Function that fills the field with the desired form inputs.
	 *
	 * @return void
	 */
	public function field_callback() { ?>
        <input id="gtm_id" class="regular-text"
               type="text" name="gtm_id"
               placeholder="<?php _e( 'GTM-XXXXXXX', 'gtm' ); ?>"
               value="<?php echo esc_attr( $this->ID ); ?>">
        <p class="description">
			<?php _e( 'You can get yours <b>container ID</b> <a href="https://www.google.com/analytics/tag-manager/" target="_blank">here</a>!', 'gtm' ); ?>
			<?php _e( 'Use comma without space (,) to enter multiple IDs.', 'gtm' ); ?>
            <br>
			<?php _e( 'Add a hook <b><code>do_action(\'wp_body\')</code></b> after opening tag <b>&lt;body&gt;</b>', 'gtm' ) ?>
        </p>
	<?php }

	/**
	 * Prints script in the head tag on the front end.
	 *
	 * @return void
	 */
	public function printHead() {
		$output = '<!-- Google Tag Manager -->' . PHP_EOL;

		foreach ( $this->getArrayIDs() as $id ) {
			$output .= "<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
            new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
            j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
            'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
            })(window,document,'script','dataLayer','" . esc_js( $id ) . "');</script>" . PHP_EOL;
		}

		$output .= '<!-- End Google Tag Manager -->' . PHP_EOL;

		echo $output;
	}

	/**
	 * Prints noscript in the body tag on the front end.
	 *
	 * @return void
	 */
	public function printBody() {
		$output = '<!-- Google Tag Manager (noscript) -->' . PHP_EOL;

		foreach ( $this->getArrayIDs() as $id ) {
			$output .= '<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=' . esc_attr( $id ) . '" height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>' . PHP_EOL;
		}

		$output .= '<!-- End Google Tag Manager (noscript) -->' . PHP_EOL;

		echo $output;
	}
}

/** @var FK_Google_Tag_Manager $fk_gtm */
$fk_gtm = new FK_Google_Tag_Manager();

global $fk_gtm;
