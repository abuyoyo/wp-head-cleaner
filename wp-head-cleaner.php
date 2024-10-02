<?php
/**
 * Plugin Name: WP-Head Cleaner
 * Description: Removes unnecessary clutter from wp_head.
 * Version: 1.4
 * Author: abuyoyo
 * Author URI: https://github.com/abuyoyo
 * Plugin URI: https://github.com/abuyoyo/wp-head-cleaner
 * Update URI: https://github.com/abuyoyo/wp-head-cleaner
 * Last Update: 2024_10_02
*/

use WPHelper\PluginCore;

new PluginCore(
	__FILE__,
	[
		'title' => 'WP-Head Cleaner',
		'slug' => 'wp-head-cleaner',
		'update_checker' => true,
	]
);

require_once 'src/WP_Head_Cleaner.php';

add_action('plugins_loaded', 'wp_head_cleaner_init' );
function wp_head_cleaner_init(){
	new WP_Head_Cleaner();
}
