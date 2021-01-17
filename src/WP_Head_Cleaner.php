<?php
/**
 * WP_Head_Cleaner class
 * 
 * Removes RSD, XMLRPC, WLW, WP Generator, ShortLink and Comment Feed links
 * 
 * @link https://orbitingweb.com/blog/remove-unnecessary-tags-wp-head/
 * @link https://stackoverflow.com/questions/34750148/how-to-delete-remove-wordpress-feed-urls-in-header
 * @link https://wordpress.stackexchange.com/questions/211467/remove-json-api-links-in-header-html
 * @link https://whatabouthtml.com/how-to-clean-up-unnecessary-code-from-wordpress-header-175
 *
 * Disable Emojis
 * @link https://kinsta.com/knowledgebase/disable-emojis-wordpress/
 * 
 * Disable oEmbed
 * @link https://kinsta.com/knowledgebase/disable-embeds-wordpress/
 * 
 * @since 1.0
 */
use WPHelper\AdminPage;

class WP_Head_Cleaner
{

	private $option_name='wp_head_cleaner_settings';

	private $admin_page;

	function __construct()
	{

		// must run before WP_Scripts->init() (init priority: 0)
		// add_action('init',[$this,'clean_wp_head'], -5 );
		$this->clean_wp_head();

		if ( ! is_admin() )
			return;

		// will not register menu on priority: 10
		// add_action('init', [$this,'register_admin_page'],0);
		$this->register_admin_page();

	}
	

	/**
	 * Register Admin Settings Page
	 * 
	 * @since 1.0
	 */
	function register_admin_page(){

		$args = [
			'slug'     => 'wp-head-cleaner',
			'title'    => 'WP-Head Cleaner',
			'parent'   => 'options',
			'render'   => 'settings-page', // built-in settings page
			'settings' => [
				'option_name' => $this->option_name, // option_name used in wp_options table
				// 'option_group' => 'wp_head_cleaner_settings' . '_settings_group', // Optional - Settings group used in register_setting() and settings_fields()
				'sections' => [
					[
						'id'          => 'wp_head_clean',
						'title'       => 'WP-Head',
						'description' => 'Remove links from wp-head.',
						'fields'      => [
							[
								'id' => 'feed_links',
								'title' => 'Post and Comment Feed',
								'type' => 'checkbox',
								'description' => 'Remove the links to the general feeds: Post and Comment Feed',
							],
							[
								'id' => 'feed_links_extra',
								'title' => 'Extra Feeds',
								'type' => 'checkbox',
								'description' => 'Remove the links to the extra feeds such as category feeds.',
							],
							[
								'id' => 'rsd_link',
								'title' => 'EditURI',
								'type' => 'checkbox',
								'description' => 'Remove the link to the Really Simple Discovery service endpoint, EditURI link.',
							],
							[
								'id' => 'wlwmanifest_link',
								'title' => 'Windows Live Writer',
								'type' => 'checkbox',
								'description' => 'Remove the link to the Windows Live Writer manifest file.',
							],
							[
								'id' => 'adjacent_posts_rel_link_wp_head',
								'title' => 'Prev Next Posts',
								'type' => 'checkbox',
								'description' => 'Remove the relational links for the posts adjacent to the current post.',
							],
							[
								'id' => 'locale_stylesheet',
								'title' => 'Locale Stylesheet',
								'type' => 'checkbox',
								'description' => 'Remove localized stylesheet link element (-rtl.css)',
							],
							[
								'id' => 'wp_generator',
								'title' => 'WP Generator',
								'type' => 'checkbox',
								'description' => 'Remove the XHTML generator that is generated on the wp_head hook, WP version',
							],
							[
								'id' => 'wp_shortlink_wp_head',
								'title' => 'Shortlink',
								'type' => 'checkbox',
								'description' => 'Remove shortlink meta link.',
							],
							[
								'id' => 'rest_output_link_wp_head',
								'title' => 'REST API link',
								'type' => 'checkbox',
								'description' => 'Remove the REST API link tag.',
							],
							[
								'id' => 'remove_jquery_migrate',
								'title' => 'Remove jQuery Migrate',
								'type' => 'checkbox',
								'description' => 'Remove jQuery-migrate script.',
							],
							[
								'id' => 'wp-block-library',
								'title' => 'Gutenberg Stylesheet',
								'type' => 'checkbox',
								'description' => 'Remove default Gutenberg css file.',
							],
							
						],
					],// section
					[
						'id'		=> 'disable_emojis',
						'title' 		=> 'Emojis',
						'description' => 'Remove emoji support introduced in WordPress 4.2',
						'fields'	=> [
							[
								'id' => 'print_emoji_detection_script',
								'title' => 'Emoji Detection Script',
								'type' => 'checkbox',
								'description' => 'Remove inline Emoji detection script.',
							],
							[
								'id' => 'print_emoji_styles',
								'title' => 'Emoji Styles',
								'type' => 'checkbox',
								'description' => 'Remove emoji style tag.',
							],
							[
								'id' => 'emoji_dns_prefetch',
								'title' => 'Emoji Prefetch',
								'type' => 'checkbox',
								'description' => 'Remove emoji dns-prefetch link.',
							],
							[
								'id' => 'wp_staticize_emoji',
								'title' => 'Emoji Static images',
								'type' => 'checkbox',
								'description' => 'Disable convert emoji to a static img element in feeds and emails.',
							],
							[
								'id' => 'disable_emojis_tinymce',
								'title' => 'Emojis in TinyMCE',
								'type' => 'checkbox',
								'description' => 'Remove emojis from TinyMCE.',
							],
						]
					],
					[
						'id'          => 'disable_oembed',
						'title'       => 'oEmbed',
						'description' => 'Remove oEmbed functionality.',
						'fields'      => [
							[
								'id' => 'wp_oembed_add_discovery_links',
								'title' => 'oEmbed Discovery',
								'type' => 'checkbox',
								'description' => 'Remove oEmbed discovery links.',
							],
							[
								'id' => 'wp_oembed_add_host_js',
								'title' => 'oEmbed JavaScript',
								'type' => 'checkbox',
								'description' => 'Remove oEmbed-specific JavaScript used to communicate with embedded iframes.',
							],
							[
								'id' => 'disable_oembed_tinymce',
								'title' => 'oEmbed in TinyMCE',
								'type' => 'checkbox',
								'description' => 'Remove oEmbed-specific JavaScript from TinyMCE.',
							],
							[
								'id' => 'wp_oembed_register_route',
								'title' => 'oEmbed REST API',
								'type' => 'checkbox',
								'description' => 'Remove the oEmbed REST API endpoint.',
							],
							[
								'id' => 'embed_rewrite_rules',
								'title' => 'oEmbed Rewrite Rules',
								'type' => 'checkbox',
								'description' => 'Remove all embeds rewrite rules.',
							],
							[
								'id' => 'disable_oembed_discover',
								'title' => 'oEmbed Auto Discovery',
								'type' => 'checkbox',
								'description' => 'Turn off oEmbed auto discovery.',
							],
							[
								'id' => 'wp_filter_oembed_result',
								'title' => 'Filter oEmbed Results',
								'type' => 'checkbox',
								'description' => 'Disable filter oEmbed results.',
							],
							[
								'id' => 'wp_filter_pre_oembed_result',
								'title' => 'Pre-Filter oEmbed Results',
								'type' => 'checkbox',
								'description' => 'Remove filter of the oEmbed result before any HTTP requests are made.',
							],
						]
					],
					[
						'id'          => 'http_headers',
						'title'       => 'HTTP Headers',
						'description' => 'Remove extra HTTP headers added by WordPress.',
						'fields'      => [
							[
								'id' => 'rest_output_link_header',
								'title' => 'REST API header link',
								'type' => 'checkbox',
								'description' => 'Remove Link header for the REST API.',
							],
							[
								'id' => 'wp_shortlink_header',
								'title' => 'Shortlink HTTP header link',
								'type' => 'checkbox',
								'description' => 'Remove shortlink from HTTP header.',
							],
						]
					], // section
				] // sections
			] // settings
		];


		/**
		 * Wrap all section description headers in inline notice html
		 * 
		 * @since 1.0
		 */
		if ( $args[ 'render' ] == 'settings-page' ){
			array_walk(
				$args[ 'settings' ][ 'sections' ],
				fn( &$item ) => $item[ 'description' ] = sprintf(
					'<div class="notice notice-info inline"><p>%s</p></div>',
					$item[ 'description' ]
				)
			);
		}

		$this->admin_page = new AdminPage($args);

		add_filter( 'wphelper/settings_page/input_checkbox', [$this, 'custom_checkbox'], 10, 4 );

		
	}



	/**
	 * Clean WP-Head
	 * 
	 * Get option from database
	 * Disable what needs to be disabled
	 * 
	 * @since 1.0
	 */
	function clean_wp_head() {
		$options = get_option( $this->option_name );

		if ( empty( $options ) )
			return;

		foreach ( $options as $key => $option ){

			if ( $option ){
				switch( $key ){
					case 'feed_links':
						remove_action( 'wp_head', $key, 2 );
						break;
					case 'feed_links_extra':
						remove_action( 'wp_head', $key, 3 );
						break;

					//emojis
					case 'print_emoji_detection_script':
						remove_action( 'wp_head', $key, 7 );
						remove_action( 'admin_print_scripts', $key );
						break;
					case 'print_emoji_styles':
						remove_action( 'wp_print_styles', $key );
						remove_action( 'admin_print_styles', $key );
						break;
					case 'emoji_dns_prefetch':
						add_filter( 'wp_resource_hints', [ $this,'disable_emojis_remove_dns_prefetch' ], 10, 2 );
						break;
					case 'disable_emojis_tinymce':
						add_filter( 'tiny_mce_plugins', fn( $plugins ) => array_diff( $plugins, [ 'wpemoji' ] ) );
						break;
					case 'wp_staticize_emoji':
						remove_action( 'the_content_feed', $key );
						remove_action( 'comment_text_rss', $key );
						remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
						break;

					// oembed
					case 'wp_oembed_register_route':
						remove_action( 'rest_api_init', $key );
						break;
					case 'embed_rewrite_rules':
						add_filter( 'rewrite_rules_array', fn( $rules ) => array_filter( $rules, fn( $rewrite ) => ( false !== strpos( $rewrite, 'embed=true' ) ) ) );
						break;
					case 'disable_oembed_discover':
						add_filter( 'embed_oembed_discover', '__return_false' );
						break;
					case 'wp_filter_oembed_result':
						remove_filter( 'oembed_dataparse', $key );
						break;
					case 'wp_filter_pre_oembed_result':
						remove_filter( 'pre_oembed_result', $key );
						break;
					case 'disable_oembed_tinymce':
						add_filter( 'tiny_mce_plugins', fn( $plugins ) => array_diff( $plugins, [ 'wpembed' ] ) );
						break;
					case 'rest_output_link_header':
					case 'wp_shortlink_header':
						remove_action( 'template_redirect', $key, 11 );
						break;
					case 'remove_jquery_migrate':
						add_action( 'wp_default_scripts', [ $this, 'dequeue_jquery_migrate' ] );
						break;
					case 'wp-block-library':
						add_action( 'wp_print_styles', fn() => wp_dequeue_style( 'wp-block-library' ), 100 );
						break;
					default:
						remove_action( 'wp_head', $key );
						break;
				}
			}
		}
	}



	/**
	 * Remove jQuery-migrate script
	 * 
	 * @link https://github.com/cedaro/dequeue-jquery-migrate/blob/develop/dequeue-jquery-migrate.php
	 * @link https://wordpress.org/plugins/remove-jquery-migrate/
	 * 
	 * @since 1.0
	 */
	public function dequeue_jquery_migrate( $scripts ) {

		if ( ! is_admin() && isset( $scripts->registered['jquery'] ) ) {
			$script = $scripts->registered['jquery'];
			
			if ( $script->deps ) { // Check whether the script has any dependencies
				$script->deps = array_diff( $script->deps, array( 'jquery-migrate' ) );
			}
		}
	}



	/**
	 * Remove emoji CDN hostname from DNS prefetching hints.
	 *
	 * @link https://kinsta.com/knowledgebase/disable-emojis-wordpress/
	 * 
	 * @param array $urls URLs to print for resource hints.
	 * @param string $relation_type The relation type the URLs are printed for.
	 * @return array Difference between the two arrays.
	 * 
	 * @since 1.0
	 */
	public function disable_emojis_remove_dns_prefetch( $urls, $relation_type ) {

		// $emoji_svg_url = apply_filters( 'emoji_svg_url', 'https://s.w.org/images/core/emoji/2/svg/' );
		// $emoji_svg_url = apply_filters( 'emoji_svg_url', 'https://s.w.org/images/core/emoji/12.0.0-1/svg/' );
		// $urls = array_diff( $urls, array( $emoji_svg_url ) );
	
		return ( 'dns-prefetch' == $relation_type )
			? array_filter( $urls, fn( $url ) => ( strpos( $url, 's.w.org' ) === false ) )
			: $urls;
	}


	/**
	 * Custom display of checkboxes
	 * 
	 * @since 1.0
	 */
	public function custom_checkbox( $input_tag, $field, $option_name, $options ){
		extract($field);

		$input_tag = sprintf(
			'<fieldset>
				<legend class="screen-reader-text"><span>%3$s</span></legend>
				<label for="%1$s">
					<input name="%2$s[%1$s]" type="checkbox" id="%1$s" value="1"  %5$s />
					%4$s
					<p class="description">[%1$s]</p>
				</label>
			</fieldset>',
			$id,
			$option_name,
			$title,
			$description,
			checked( ( $options[$id] ?? false ), '1', false)
		);

		return $input_tag;
	}

}