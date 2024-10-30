<?php
if(!class_exists('HITActivity_NOTIFY')){
    class HITActivity_NOTIFY{
        public function __construct() {
            

            add_action( 'user_register', array($this,'wpdrone_registration_save'), 10, 1 );
            add_action( 'profile_update', array($this, 'edit_user_profile'), 10, 2);
            add_action( 'delete_user', array($this, 'wpdrone_delete_user') );
            add_action( 'activated_plugin', array($this, 'wpdrone_wpdocs_detect_plugin_activation'), 10, 2 );
            add_action( 'deactivated_plugin', array($this, 'wpdrone_wpdocs_detect_plugin_deactivation'), 10, 2 );
            add_action( 'after_switch_theme', array($this, 'hit_wpdrone_switch_theme'), 10, 1 );
            //add_action( 'upgrader_process_complete', array($this, 'wpdrone_upgrade_function'),10, 2);		
        }

        function hit_wpdrone_switch_theme( $old_theme_name, $old_theme = false ) {
            $saved_hooks = get_option('wpdrone_hooks_notify_list');
            if($saved_hooks){
                $saved_hooks = json_decode($saved_hooks,true);
                if(isset($saved_hooks['after_switch_theme'])){
                    // plugin activated
                    $msg = $saved_hooks['after_switch_theme']['msg'];
                    
                    
                    $msg = str_replace("{new_theme}",get_option('stylesheet'), $msg);
                    $msg = str_replace("{old_theme}", $old_theme_name, $msg);
                    $msg = str_replace("{timestramp}",date("F j, Y, g:i a"), $msg);
        
                    $this->wp_drone_fire_action($msg, $saved_hooks['after_switch_theme']['name'], $saved_hooks['after_switch_theme']['status'], 'after_switch_theme', $saved_hooks['after_switch_theme']['type']);
                }
                
            }
        }

        function wpdrone_wpdocs_detect_plugin_activation( $plugin, $network_activation ) {
            $saved_hooks = get_option('wpdrone_hooks_notify_list');
            if($saved_hooks){
                $saved_hooks = json_decode($saved_hooks,true);
                if(isset($saved_hooks['activated_plugin'])){
                    // plugin activated
                    $msg = $saved_hooks['activated_plugin']['msg'];
                    
                    
                    $plugin_info = get_plugin_data( plugin_dir_path( __DIR__ ) . $plugin );
                    $msg = str_replace("{plugin}",$plugin_info['Name'] . ' - V.' . $plugin_info['Version'], $msg);
                    $msg = str_replace("{timestramp}",date("F j, Y, g:i a"), $msg);
                    $msg = str_replace("{network_wide}",$network_activation, $msg);
        
                    $this->wp_drone_fire_action($msg, $saved_hooks['activated_plugin']['name'], $saved_hooks['activated_plugin']['status'], 'activated_plugin', $saved_hooks['activated_plugin']['type']);
                }
                
            }
            
        }
        
        function wpdrone_wpdocs_detect_plugin_deactivation( $plugin, $network_deactivation ) {
            $saved_hooks = get_option('wpdrone_hooks_notify_list');
            if($saved_hooks){
                $saved_hooks = json_decode($saved_hooks,true);
                if(isset($saved_hooks['deactivated_plugin'])){
                    // plugin activated
                    $msg = $saved_hooks['deactivated_plugin']['msg'];
                    
                    $plugin_info = get_plugin_data( plugin_dir_path( __DIR__ ) . $plugin );
                    $msg = str_replace("{plugin}",$plugin_info['Name'] . ' - V.' . $plugin_info['Version'], $msg);
                    $msg = str_replace("{timestramp}",date("F j, Y, g:i a"), $msg);
                    $msg = str_replace("{network_wide}",$network_deactivation, $msg);
        
                    $this->wp_drone_fire_action($msg, $saved_hooks['deactivated_plugin']['name'], $saved_hooks['deactivated_plugin']['status'], 'deactivated_plugin',$saved_hooks['deactivated_plugin']['type']);
                }
            }
        }
        
        function wpdrone_upgrade_function( $upgrader_object, $options ) {
            //upgrade plugin, theme, core, translations
         
            if ($options['action'] == 'update' && $options['type'] == 'plugin' ) {
                $current_plugin_path_name = plugin_basename( __FILE__ );
               foreach($options['plugins'] as $each_plugin) {
                  if ($each_plugin==$current_plugin_path_name) {
                    $msg = '';
                    $this->wp_drone_fire_action($msg);
                  }
               }
            }else if ($options['action'] == 'update' && $options['type'] == 'theme' ) {
                $msg = '';
                $this->wp_drone_fire_action($msg);
            }else if ($options['action'] == 'update' && $options['type'] == 'core' ) {
                $msg = '';
                $this->wp_drone_fire_action($msg);
            }
        }
        
        function wpdrone_delete_user( $user_id ) {
            // delete user
            global $wpdb;
         
            $saved_hooks = get_option('wpdrone_hooks_notify_list');
            if($saved_hooks){
                $saved_hooks = json_decode($saved_hooks,true);
                if(isset($saved_hooks['delete_user'])){
                    // plugin activated
                    $msg = $saved_hooks['delete_user']['msg'];
                    $user = get_userdata( $user_id );

                    $msg = str_replace("{user_email}",$user->user_email, $msg);
                    $msg = str_replace("{deleted_user}",$user->user_login, $msg);
                    $msg = str_replace("{timestramp}",$user->user_registered, $msg);
                    $msg = str_replace("{role}",implode(', ', $user->roles), $msg);

                    $this->wp_drone_fire_action($msg, $saved_hooks['delete_user']['name'], $saved_hooks['delete_user']['status'], 'delete_user',$saved_hooks['delete_user']['type']);
                }
            }
        
        }
        
        function edit_user_profile($user_id, $old_user_data) {

            // echo "<pre>";
            // print_r($old_user_data);
            // die();

            $saved_hooks = get_option('wpdrone_hooks_notify_list');
            if($saved_hooks){
                $saved_hooks = json_decode($saved_hooks,true);
                if(isset($saved_hooks['profile_update'])){
                    // plugin activated
                    $msg = $saved_hooks['profile_update']['msg'];
                    $user = get_userdata( $user_id );

                    $msg = str_replace("{user_email}",$user->user_email, $msg);
                    $msg = str_replace("{updated_user_name}",$user->user_login, $msg);
                    $msg = str_replace("{timestramp}",$user->user_registered, $msg);
                    $msg = str_replace("{role}",implode(', ', $user->roles), $msg);

                    $this->wp_drone_fire_action($msg, $saved_hooks['profile_update']['name'], $saved_hooks['profile_update']['status'], 'profile_update',$saved_hooks['profile_update']['type']);
                }
            }
        }
         
        function wpdrone_registration_save( $user_id ) {
         // New User register
        $saved_hooks = get_option('wpdrone_hooks_notify_list');
        if($saved_hooks){
            $saved_hooks = json_decode($saved_hooks,true);
            if(isset($saved_hooks['user_register'])){
                // plugin activated
                $msg = $saved_hooks['user_register']['msg'];
                $user = get_userdata( $user_id );

                $msg = str_replace("{user_email}",$user->user_email, $msg);
                $msg = str_replace("{crerated_username}",$user->user_login, $msg);
                $msg = str_replace("{timestramp}",$user->user_registered, $msg);
                $msg = str_replace("{role}",implode(', ', $user->roles), $msg);

                $this->wp_drone_fire_action($msg, $saved_hooks['user_register']['name'], $saved_hooks['user_register']['status'], 'user_register',$saved_hooks['user_register']['type']);
            }
        }
        
        }

        function wp_drone_fire_action($msg, $heading = '', $status = '', $hook = '', $type = '', $meta = array()){

            $user  = wp_get_current_user();
            $current_user_id = 0;
            $user_name = '';
            if($user->user_login){
                $msg = str_replace("{username}",$user->user_login, $msg);
                $current_user_id = get_current_user_id();
                $user_name = $user->user_login;
            }else{
                $msg = str_replace("{username}",'System', $msg);
                $user_name = 'System';
            }

            $current_post_id = 0;
            $post_name = '';

            if(get_the_ID()){
                $current_post_id = get_the_ID();
                $post_name = get_the_title();
            }
            

            $plugin_key = get_option('hitwpdrone_pluginkey', '');


            if($plugin_key !=''){
                $response = wp_remote_post( 'https://app.wpdrone.com/api/notify.php', array(
                    'method'      => 'POST',
                    'timeout'     => 45,
                    'redirection' => 5,
                    'httpversion' => '1.0',
                    'blocking'    => true,
                    'headers'     => array(),
                    'body'        => json_encode(array('key'=> $plugin_key, 'msg' => $msg, 'heading' => $heading, 'action_user_id' => $current_user_id, 'post_id' => $current_post_id, 'status' => $status, 'hook' => $hook, 'type' => $type, 'user_name' => $user_name, 'post_name' => $post_name, 'meta' => $meta)),
                    'cookies'     => array()
                    )
                );

                // echo "<pre>";
                // print_r($response);
                // die();

            }
        }

    }
}
add_action( 'init', 'hit_wpdrone_class_init' );

function hit_wpdrone_class_init() {
    new HITActivity_NOTIFY();
}

?>