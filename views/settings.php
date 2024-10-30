<?php

if(isset($_POST['hitwpdronesubmit'])){
   $save_plugin_key = isset($_POST['save_plugin_key']) ? sanitize_text_field($_POST['save_plugin_key']) : '';
   $email_sent = isset($_POST['wp_email']) ? sanitize_email($_POST['wp_email']) : '';
    update_option('hitwpdrone_pluginkey', $save_plugin_key);
    update_option('hitwpdrone_email', $email_sent);
}

if(isset($_GET['delete'])){
    $delete_hook = ($_GET['delete'] != '') ? sanitize_text_field($_GET['delete']) : '';
    $saved_hooks = get_option('wpdrone_hooks_notify_list','');
    $saved_hooks = json_decode($saved_hooks, true);
    unset($saved_hooks[$delete_hook]);
    update_option('wpdrone_hooks_notify_list', json_encode($saved_hooks));
    wp_redirect( admin_url( '/options-general.php?page=wpdrone&action=notification' ) );
    exit;
}


$all_hooks = array(
    "activated_plugin" => array('name' => 'Plugin Activated','status'=> 'success', 'type' => 'Plugin',"msg" => '{plugin} Activated by {username} at {timestramp}.', "short_code" => 'plugin, network_wide, username, timestramp', "email" => 'no'), // plugin path, multisite enabled
    "deactivated_plugin" => array('name' => 'Plugin Deactivated','status'=> 'danger', 'type' => 'Plugin', "msg" => '{plugin} Deactivated by {username} at {timestramp}.', "short_code" => 'plugin, network_wide, username, timestramp', "email" => 'no'), 
    "upgrader_process_complete_plugin" => array('name' => 'Plugin Upgraded','status'=> 'info', 'type' => 'Plugin', "msg" => '{plugin} Upgraded by {username} at {timestramp}.', "short_code" => 'plugins, bulk, username, timestramp', "email" => 'no'), // type 'plugin', 'theme', 'translation', or 'core'.
    "user_register" => array('name' => 'New User Created', 'status'=> 'success', 'type' => 'User', "msg" => 'New User {crerated_username} Created at {timestramp}.', "short_code" => 'user_email, crerated_username, username, timestramp, role', "email" => 'no'),
    "profile_update" => array('name' => 'User Profile Updated','status'=> 'info', 'type' => 'User', "msg" => '{updated_user_name} Edited by {username} at {timestramp}.', "short_code" => 'user_email, updated_user_name, username, timestramp,  role', "email" => 'no'),
    "delete_user" => array('name' => 'User Deleted','status'=> 'danger', 'type' => 'User', "msg" => '{deleted_user} Deleted by {username} at {timestramp}.', "short_code" => 'user_email, deleted_user, username, timestramp, role', "email" => 'no'),
    //"switch_theme" => array("name" => "Switch theme", "msg" => 'User (<username>) Deleted by <current_user> at <timestramp>.', "short_code" => 'user_email,current_user,username,timestramp,email_address'),
    "after_switch_theme" => array("name" => "Theme Changed",'status'=> 'info', 'type' => 'Theme', "msg" => 'Theme switched to {new_theme} from {old_theme} by {username} at {timestramp}.', "short_code" => 'old_theme, new_theme, username, timestramp', "email" => 'no'),
    "upgrader_process_complete_theme" => array("name" => "Theme Upgraded",'status'=> 'info', 'type' => 'Theme', "msg" => 'Theme ({themes}) Upgraded by {username} at {timestramp}.', "short_code" => 'themes, bulk, username, timestramp', "email" => 'no'), // type 'plugin', 'theme', 'translation', or 'core'.
    "upgrader_process_complete_core" => array("name" => "WordPress Core Upgraded",'status'=> 'info', 'type' => 'Core', "msg" => 'Wordpress Core Upgraded to {version} by {username} at {timestramp}.', "short_code" => 'themes, bulk, username, timestramp, version', "email" => 'no') // type 'plugin', 'theme', 'translation', or 'core'.
);

if(isset($_POST['wp_submit'])){
   
    $saved_hooks = get_option('wpdrone_hooks_notify_list','');
    $saved_hooks = json_decode($saved_hooks, true);
   
    $new_hook = array();
    $wp_hook = isset($_POST['wp_hooks']) ? sanitize_text_field($_POST['wp_hooks']) : '';
    $wp_heading = isset($_POST['wp_heading']) ? sanitize_text_field($_POST['wp_heading']) : '';
    $wp_msg = isset($_POST['wp_msg']) ? sanitize_text_field($_POST['wp_msg']) : '';
    $wp_notify_type = isset($_POST['wp_notify_type']) ? sanitize_text_field($_POST['wp_notify_type']) : '';
    $wp_email = isset($_POST['sent_wp_email']) ? sanitize_text_field($_POST['sent_wp_email']) : '';
    
    $new_hook[$wp_hook] = $all_hooks[$wp_hook];
    $new_hook[$wp_hook]['name'] = $wp_heading;
    $new_hook[$wp_hook]['msg'] = $wp_msg;
    $new_hook[$wp_hook]['status'] = $wp_notify_type;
    $new_hook[$wp_hook]['email'] = $wp_email;

    $saved_hooks[$wp_hook] = $new_hook[$wp_hook];
    update_option('wpdrone_hooks_notify_list', json_encode($saved_hooks));

}

$plugin_key = get_option('hitwpdrone_pluginkey', '');
$email = get_option('hitwpdrone_email', '');

if($email == ''){
    $email = get_option('admin_email');
}


$saved_hooks = get_option('wpdrone_hooks_notify_list','');

if($saved_hooks){
    $saved_hooks = json_decode($saved_hooks, true);

}else{
    $saved_hooks = array(
        "activated_plugin" => array('name' => 'Plugin Activated','status'=> 'success', 'type' => 'Plugin',"msg" => '{plugin} Activated by {username} at {timestramp}.', "short_code" => 'plugin,network_wide,username,timestramp', "email" => 'no'), // plugin path, multisite enabled
        "deactivated_plugin" => array('name' => 'Plugin Deactivated','status'=> 'danger', 'type' => 'Plugin', "msg" => '{plugin} Deactivated by {username} at {timestramp}.', "short_code" => 'plugin,network_wide,username,timestramp', "email" => 'no'), 
        "upgrader_process_complete_plugin" => array('name' => 'Plugin Upgraded','status'=> 'info', 'type' => 'Plugin', "msg" => '{plugin} Upgraded by {username} at {timestramp}.', "short_code" => 'plugins,bulk,username,timestramp', "email" => 'no'), // type 'plugin', 'theme', 'translation', or 'core'.
        "upgrader_process_complete_theme" => array("name" => "Theme Upgrade",'status'=> 'info', 'type' => 'Theme', "msg" => 'Theme ({themes}) Upgraded by {username} at {timestramp}.', "short_code" => 'themes,bulk,username,timestramp', "email" => 'no'), // type 'plugin', 'theme', 'translation', or 'core'.
        "upgrader_process_complete_core" => array("name" => "WordPress Core Upgraded",'status'=> 'info', 'type' => 'Core', "msg" => 'Wordpress Core Upgraded to {version} by {username} at {timestramp}.', "short_code" => 'themes,bulk,username,timestramp,version', "email" => 'no') // type 'plugin', 'theme', 'translation', or 'core'.

    );
    update_option('wpdrone_hooks_notify_list', json_encode($saved_hooks));
}

foreach($saved_hooks as $key => $hook){
    unset($all_hooks[$key]);
}

// $comment = array(
//     "edit_comment",
//     "delete_comment",
//     "deleted_comment",
//     "spam_comment",
//     "spammed_comment",
//     "trash_comment",
//     "trashed_comment",
//     "untrash_comment",
//     "untrashed_comment",
//     "comment_closed",
//     "comment_post",
//     "wp_insert_comment",
//     "wp_set_comment_status",
// );

// $post = array(
//     "wp_insert_comment",
//     "wp_set_comment_status",
//     "wp_trash_post",
//     "trashed_post",
//     "untrash_post",
//     "before_delete_post",
//     "delete_post",
//     "deleted_post",
//     "edit_attachment",
//     "edit_category",
//     "edit_post",
//     "pre_post_update",
//     "post_updated",
//     "transition_post_status",
//     "publish_post",
//     "publish_page",
//     "publish_phone",
//     "publish_future_post",
//     "save_post",
//     "wp_insert_post",
//     "xmlrpc_publish_post",
//     "clean_post_cache"
// );

// $logins = array(
//     "wp_login",
//     "wp_logout",
//     "password_reset",
// );

// $attachment = array(
//     "add_attachment",
//     "delete_attachment",
//     "password_reset",
// );

// $category = array( 
    
//     "add_category",
//     "create_category",
//     "delete_category",
//     "created_term",
//     "edited_terms",
//     "edited_term_taxonomy"
// );

// $hooks = array(
//     "upgrader_process_complete" => array("msg" => 'WordPress Core Upgraded by <username> at <timestramp>.', "short_code" => 'core,bulk,username,timestramp') // type 'plugin', 'theme', 'translation', or 'core'.
// );

global $wp;
?>


<style>.notice{display:none;}</style>
<div>
    <ul class="subsubsub">
    <li class="notifications"><a href="<?php admin_url( 'options-general.php') ?>?page=wpdrone&action=notification" <?php echo (!isset($_GET['action']) || $_GET['action'] == 'notification' ) ?  'class="current"' : '' ?> aria-current="page">Notifications <span class="count">(<span class="all-count"><?php echo count($saved_hooks); ?></span>)</span></a> |</li>
    <li class="connections"><a href="<?php admin_url( 'options-general.php') ?>?page=wpdrone&action=connection" <?php echo (isset($_GET['action']) && $_GET['action'] == 'connection') ?  'class="current"' : '' ?>>Connections <span class="count">(<span class="mine-count">0</span>)</span></a> |</li>
    <li class="Settings"><a href="<?php admin_url( 'options-general.php') ?>?page=wpdrone&action=settings" <?php echo (isset($_GET['action']) && $_GET['action'] == 'settings') ?  'class="current"' : '' ?>>Settings</a></li>
    </ul>
</div>
<?php
if(isset($_GET['action']) && $_GET['action'] == 'settings'){
    echo '<form method="post" style="margin-top:50px;">
                <hr>
                <p>Plugin Key:</p><input type="text" class="form-control" name="save_plugin_key" style="width:300px;" value="'. esc_html($plugin_key) . '"/>
                <p>Email address:</p> <input type="text" class="form-control" name="wp_email" style="width:300px;"  value="'. esc_html($email) . '"/>
                <br/><br/><button type="submit" name="hitwpdronesubmit" class="button-primary button">Save Changes</button>
            </form>';
}else if( isset($_GET['action']) && $_GET['action'] == 'connection' ){
    echo '<p style="margin-top:50px;"><hr>Coming soon</p>';
}else{
?>
<div id="col-container" class="wp-clearfix" style="margin-top:50px;">
<hr>
   <div id="col-left">
      <div class="col-wrap">
         <div class="form-wrap">
            <h2>Add New Notification</h2>
            <form method="post">
               <div class="form-field term-parent-wrap">
                  <label for="wp_hooks">Hooks</label>
                  <select name="wp_hooks" id="wp_hooks" class="postform" style="width:100%;">
                      <?php
                      if($all_hooks){
                         
                        foreach($all_hooks as $key => $hook){
                           echo '<option value="'. esc_html($key) .'">'. esc_html($hook['name']) .'</option>';
                        }

                     }
                      ?>
                  </select>
               </div>
                
               <?php
               if(!$all_hooks){
                  
               }else{
                  $firstKey = array_key_first($all_hooks);
               
                
               ?>
               <div class="form-field form-required term-name-wrap">
                  <label for="wp_heading">Heading</label>
                  <input name="wp_heading" id="wp_heading" required="true" type="text" value="<?php echo esc_html($all_hooks[$firstKey]['name']); ?>" size="40" aria-required="true">
               </div>
               
               <div class="form-field term-description-wrap">
                  <label for="wp_msg">Message</label>
                  <textarea name="wp_msg" id="wp_msg" rows="5" required="true" cols="40"><?php echo esc_html($all_hooks[$firstKey]['msg']);?></textarea>
                  <p id="wp_short_code">ShortCodes: <?php echo esc_html($all_hooks[$firstKey]['short_code']); ?></p>
                  <a href="#">More Shortcodes? Contact us.</a>

               </div>
               <div class="form-field term-parent-wrap">
                  <label for="wp_notify_type">Notification Type</label>
                  <select name="wp_notify_type" id="wp_notify_type" class="postform" style="width:100%;">
                  <option value="success">Success</option>
                  <option value="danger">Danger</option>
                  <option value="info">Info</option>
                  <option value="Normal">Normal</option>
                  </select>
               </div>
               <div class="form-field term-parent-wrap">
                  <label for="sent_wp_email">Sent Email</label>
                  <select name="sent_wp_email" id="sent_wp_email" class="postform" style="width:100%;">
                  <option value="yes">True</option>
                  <option value="no">Flase</option>
                  </select>
               </div>
               <p class="submit">
                  <input type="submit" name="wp_submit" id="wp_submit" class="button button-primary" value="Add New Notification">
                  <span class="spinner"></span>
               </p>
               <?php
               }
               ?>
            </form>
         </div>
      </div>
   </div>
   <!-- /col-left -->
   <div id="col-right">
      <div class="col-wrap">
         <form id="posts-filter" method="post">
            <div class="tablenav top">
              
            </div>
            <h2 class="screen-reader-text">Notification list</h2>
            <table class="wp-list-table widefat fixed striped table-view-list tags" style="margin-top:24px;">
               <thead>
                  <tr>
                     <th scope="col" style="padding:10px;" id="name" class="manage-column column-name column-primary sortable desc"><span>Name</span></th>
                     <th scope="col" style="padding:10px;" id="description" class="manage-column column-description sortable desc"><span>Message</span></th>
                     <th scope="col" style="padding:10px;" id="slug" class="manage-column column-slug"><span>Notify Type</span></th>
                     <th scope="col" style="padding:10px;" id="posts" class="manage-column column-posts"><span>Email</span></th>
                  </tr>
               </thead>
               <tbody id="the-list" data-wp-lists="list:tag">
                <?php
                    foreach($saved_hooks as $key => $data){
                        echo '<tr id="'. esc_html($key) .'" class="level-0">
                                <td class="name column-name has-row-actions column-primary" data-colname="Name">
                                <strong><a class="row-title" href="#" >'. esc_html($data['name']) .'</a></strong><br>
                                <div class="row-actions"><span class="delete"><a href="'. admin_url( 'options-general.php') .'?page=wpdrone&action=notification&delete='.esc_html($key) .'" >Delete</a></span></div>
                                <button type="button" class="toggle-row"><span class="screen-reader-text">Show more details</span></button>
                                </td>
                                <td class="description column-description" ><span aria-hidden="true">'. esc_html($data['msg']) .'</span></td>
                                <td style="text-align:left;" class="slug column-slug">'. esc_html($data['status']) .'</td>
                                <td style="text-align:left;" class="posts column-posts" >'. (isset($data['email']) ? $data['email'] : 'no') .'</td>
                            </tr>';
                    }
                ?>
                 
               </tbody>
               <tfoot>
                  <tr>
                     <th scope="col" style="padding:10px;" id="name" class="manage-column column-name column-primary sortable desc"><span>Name</span></th>
                     <th scope="col" style="padding:10px;" id="description" class="manage-column column-description sortable desc"><span>Message</span></th>
                     <th scope="col" style="padding:10px;" id="slug" class="manage-column column-slug sortable desc"><span>Notify Type</span></th>
                     <th scope="col" style="padding:10px;" id="posts" class="manage-column column-posts num sortable desc"><span>Email</span></th>
                  </tr>
               </tfoot>
            </table>
            
         </form>
         
      </div>
   </div>
   <!-- /col-right -->
</div>

<?php
}
?>

<script>
    var hooks = <?php echo json_encode($all_hooks); ?>;
    jQuery(document).ready(function() {
        jQuery('#wp_hooks').on('change', function (e) {
            var optionSelected = jQuery("option:selected", this);
            var valueSelected = this.value;
            //console.log(hooks);
            jQuery('#wp_heading').val(hooks[valueSelected].name);
            jQuery('#wp_msg').val(hooks[valueSelected].msg);
            jQuery('#wp_short_code').text('ShortCodes: ' + hooks[valueSelected].short_code);
            jQuery('#wp_notify_type').val(hooks[valueSelected].status);
        });
    });
</script>
