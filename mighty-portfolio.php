<?php
/*
 *  Plugin Name: Mighty Portfolio
 *  Plugin URL: http://mightyplugins.com/
 *  Description: Canto Clients simple and effective clients shortcode.
 *  Author: MightyPlugins
 *  Version: 1.0.3
 *  Author URI: http://mightyplugins.com/
 *  Text Domain: mighty-portfolio
 *  Domain Path: /languages/
 *  License: GPLv2 or later
 *  License URI: https://www.gnu.org/licenses/gpl-3.0.txt
 */

if ( ! defined( 'MP_PATH' ) ){
	define("MP_PATH", plugin_dir_path(__FILE__));
}
if ( ! defined( 'MP_URL' ) ){
	define('MP_URL', plugin_dir_url( __FILE__ ));
}


if ( ! defined( 'CTF_PATH' ) ){
	define('CTF_PATH',  plugin_dir_path( __FILE__ ).'/framework/');
}
if ( ! defined( 'CTF_URL' ) ){
	define('CTF_URL', plugin_dir_url( __FILE__ ).'framework/');
}

if ( ! defined( 'CTFMB_PATH' ) ){
	define('CTFMB_PATH',  plugin_dir_path( __FILE__ ).'/metabox/');
}
if ( ! defined( 'CTFMB_URL' ) ){
	define('CTFMB_URL', plugin_dir_url( __FILE__ ).'metabox/');
}


if (!class_exists('MP_Portfolio')):

	/**
	* Mighty Portfolio plugin main class
	*
	* @since 1.0
	*/
	class MP_Portfolio
	{
		
		function __construct()
		{
			$this->include_files();

			add_action( 'admin_enqueue_scripts', array($this, 'enqueue_admin_assets') );
			add_action( 'wp_enqueue_scripts', array($this, 'enqueue_front_end_assets') );
			add_action( 'init', array($this, 'add_image_sizes') );
			add_action( 'init', array($this, 'load_textdomain') );


			add_filter( 'the_content', array($this, 'portfolio_content') );
		}

		public function enqueue_admin_assets()
		{
			wp_enqueue_style( 'mp-portfolio-admin', MP_URL . '/assets/css/mp-portfolio-admin.css', false, '1.0.0' );
		}

		public function enqueue_front_end_assets()
		{
			wp_enqueue_script( 'magnific-popup', MP_URL . '/assets/magnific-popup/jquery.magnific-popup.min.js', array('jquery'), '1.0.0', true );
			wp_enqueue_script( 'isotope', MP_URL . '/assets/js/isotope.pkgd.min.js', array('jquery'), '1.0.0', true );
			wp_enqueue_script( 'mp-portfolio', MP_URL . '/assets/js/mp-portfolio.js', array('jquery', 'magnific-popup', 'isotope'), '1.0.0', true );

			wp_enqueue_style( 'magnific-popup', MP_URL . '/assets/magnific-popup/magnific-popup.css', false, '1.0.0' );
			wp_enqueue_style( 'mp-portfolio', MP_URL . '/assets/css/mp-portfolio.css', false, '1.0.0' );
		}

		public function add_image_sizes()
		{
			add_image_size( 'mp_gallery_thumb', 300, 300, true );
			add_image_size( 'mp_portfolio_banner', 1200, 600, true );
		}

		function load_textdomain() {
			load_plugin_textdomain( 'mighty-portfolio', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' ); 
		}

		public function portfolio_content( $content )
		{
			$located = locate_template( 'single-mp-portfolio.php' );

			if (empty($located) && get_post_type( get_the_id() ) == 'mp-portfolio'):

				$ct_portfolio_meta =  get_post_meta( get_the_id(), 'ct_portfolio_meta', true );


				ob_start();
				?>
				<div class="mpp-info clearfix">

					<div class="mpp-meta">
						<?php if(isset($ct_portfolio_meta['start_date']) && !empty($ct_portfolio_meta['start_date'])): ?>
							<div class="mpp-start-date">
								<strong><?php esc_html_e( 'Start Date:', 'mighty-portfolio' ); ?></strong>
								<span><?php echo esc_html( $ct_portfolio_meta['start_date'] ); ?></span>
							</div>
						<?php endif; ?>
						<?php if(isset($ct_portfolio_meta['end_date']) && !empty($ct_portfolio_meta['end_date'])): ?>
							<div class="mpp-end-date">
								<strong><?php esc_html_e( 'End Date:', 'mighty-portfolio' ); ?></strong>
								<span><?php echo esc_html( $ct_portfolio_meta['end_date'] ); ?></span>
							</div>
						<?php endif; ?>
						<?php if(isset($ct_portfolio_meta['client']) && !empty($ct_portfolio_meta['client'])): ?>
							<div class="mpp-client">
								<strong><?php esc_html_e( 'Client:', 'mighty-portfolio' ); ?></strong>
								<?php if(isset($ct_portfolio_meta['client_url']) && !empty($ct_portfolio_meta['client_url'])): ?>
									<span><a href="<?php echo esc_url( $ct_portfolio_meta['client_url'] ); ?>" rel="nofollow"><?php echo esc_html( $ct_portfolio_meta['client'] ); ?></a></span>
								<?php else: ?>
									<span><?php echo esc_html( $ct_portfolio_meta['client'] ); ?></span>
								<?php endif; ?>
							</div>
						<?php endif; ?>
					</div>
					<?php if(isset($ct_portfolio_meta['short_desc']) && !empty($ct_portfolio_meta['short_desc'])): ?>
						<div class="mpp-short-desc">
							<?php echo wp_kses_post( wpautop( $ct_portfolio_meta['short_desc'] ) ); ?>
						</div>
					<?php endif; ?>
				</div>
				<div class="mpp-gallery clearfix">
					<?php if (!empty($ct_portfolio_meta['gallery'])): foreach($ct_portfolio_meta['gallery'] as $gallery_item):?>
						<div class="mpp-gallery-item">
							<a href="<?php echo esc_url( $gallery_item['url'] ); ?>"><?php echo wp_get_attachment_image($gallery_item['id'], 'mp_gallery_thumb'); ?></a>
						</div>
					<?php endforeach; endif; ?>
				</div>

				<?php

				$output = ob_get_clean();

				$content = $output.$content;

			endif;

			return $content;
		}

		private function include_files()
		{
			require_once MP_PATH .'/framework/cantoframework.php';
			require_once MP_PATH .'/metabox/cantometabox.php';


			require_once MP_PATH . 'inc/post-type.php';
			require_once MP_PATH . 'inc/metabox-config.php';
			require_once MP_PATH . 'inc/shortcodes.php';
		}
	}

	$mpp = new MP_Portfolio();


endif;