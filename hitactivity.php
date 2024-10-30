<?php
defined('ABSPATH') or die('No script kiddies please!');
/*
  Plugin Name: HITActivity - Automated Hook/Filter Monitoring System
 * Plugin URI: https://hitstacks.com/
 * Description: Monitor all hooks activities in wordpress.
 * Version: 1.0.2
 * Author: HITShipo
 * Author URI: https://wpdrone.com/
 * Developer: hitshipo
 * Developer URI: https://hitshipo.com/
 * WC requires at least: 2.6
 * WC tested up to: 5.8
 
 */

 if(!class_exists('HITActivity_Main_Class')){
    class HITActivity_Main_Class{
        public function __construct() {
            add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'wpdrone_links' ) );
            add_action( 'admin_menu', array($this, 'wpdone_menu' ));			
            include_once('notify.php');	
        }
        function wpdrone_links($links){
				$plugin_links = array(
					'<a href="'. admin_url( '/options-general.php?page=wpdrone&action=settings' ) .'" style="color:green;">' . __( 'Configure', 'wpdrone' ) . '</a>',
					'<a href="#" target="_blank" >' . __('Support', 'wpdrone') . '</a>'
					);
				return array_merge( $plugin_links, $links );
        }
        function wpdone_menu(){
            add_submenu_page( 'options-general.php', 'WPDrone', 'WPDrone', 'manage_options', 'wpdrone', array($this, 'wpdrone_content') ); 

        }

        function wpdrone_content(){
            require_once 'views/settings.php';
        }
    }
 }

 new HITActivity_Main_Class();
