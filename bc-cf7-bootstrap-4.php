<?php
/*
Author: Beaver Coffee
Author URI: https://beaver.coffee
Description: Add Bootstrap 4 to Contact Form 7 fields.
Domain Path:
License: GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Network: true
Plugin Name: BC CF7 Bootstrap 4
Plugin URI: https://github.com/beavercoffee/bc-cf7-bootstrap-4
Requires at least: 5.7
Requires PHP: 5.6
Text Domain: bc-cf7-bootstrap-4
Version: 1.6.20
*/

if(defined('ABSPATH')){
    require_once(plugin_dir_path(__FILE__) . 'classes/class-bc-cf7-bootstrap-4.php');
    BC_CF7_Bootstrap_4::get_instance(__FILE__);
}
