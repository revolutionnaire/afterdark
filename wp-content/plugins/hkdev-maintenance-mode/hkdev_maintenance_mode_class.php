<?php
/**
 * This code implements the hkdev_maintenance_mode class.
 *
 * The hkdev_maintenance_mode class implements a maintenance mode
 * for the application.
 *
 * @package hkdev_maintenance_mode
 * @access public
 */

if( !class_exists("hkdev_maintenance_mode") ) {
    
	class hkdev_maintenance_mode {
		
		var $admin_options_name;
		var $maintenance_html_head;
		var $maintenance_html_foot;
		var $maintenance_html_body;
		
		// (php) constructor.
		function __construct() { 
			$this->admin_options_name		= "hkdev_mm";
			$this->maintenance_html_head	= '<!DOCTYPE html><html><head><link href="[[WP_STYLE]]" rel="stylesheet" type="text/css" /><title>[[WP_TITLE]]</title></head><body><div style="margin: auto;max-width: 800px;">';
			$this->maintenance_html_body	= _x( '<h1>Website Under Maintenance</h1>Our Website is currently undergoing scheduled maintenance. Please check back soon.', 'Maintenance message' , 'hkdev-maintenance-mode');
			$this->maintenance_html_foot	= '</div></body></html>';
		}
		
		// (php) initialize.
		function init() {
			global $wpdb;
			// create keys table if needed.
			$def_time = '0000-00-00 00:00:00';
			$tbl = $wpdb->prefix . $this->admin_options_name . "_access_keys";
			if( $wpdb->get_var( "SHOW TABLES LIKE '$tbl'" ) != $tbl ) {
				$sql = $wpdb->prepare("create table $tbl ( id int auto_increment primary key, name varchar(100), access_key varchar(20), email varchar(100), created_at datetime not null default %s, active int(1) not null default 1 )", $def_time);
				$wpdb->query($sql);
			}
			// create IPs table if needed
			$tbl = $wpdb->prefix . $this->admin_options_name . "_unrestricted_ips";
    			if( $wpdb->get_var( "SHOW TABLES LIKE '$tbl'" ) != $tbl ) {
				$sql = $wpdb->prepare("create table $tbl ( id int auto_increment primary key, name varchar(100), ip_address varchar(20), created_at datetime not null default %s, active int(1) not null default 1 )", $def_time);
				$wpdb->query($sql);
			}
			// setup options
			add_option("hkdev_maintenance_mode_version", "1.5");
			$tmp_opt = $this->get_admin_options();	
		}
		
		// (php) find user IP.
		function get_user_ip(){
            if (array_key_exists('HTTP_X_FORWARDED_FOR', $_SERVER)){
                return  $_SERVER["HTTP_X_FORWARDED_FOR"];  
            }else if (array_key_exists('REMOTE_ADDR', $_SERVER)) { 
                return $_SERVER["REMOTE_ADDR"]; 
            }else if (array_key_exists('HTTP_CLIENT_IP', $_SERVER)) {
                return $_SERVER["HTTP_CLIENT_IP"]; 
            } 
            return '';
		}

		// (php) determine user class c
		function get_user_class_c(){
			$ip = $this->get_user_ip();
			$ip_parts = explode( '.', $ip );
			$class_c = $ip_parts[0] . '.' . $ip_parts[1] . '.' .$ip_parts[2] . '.*';
			return $class_c;
		}

		// (php) get post pages
		function get_posts_ajax_callback(){
			$return = array();
			// you can use WP_Query, query_posts() or get_posts() here - it doesn't matter
			$search_results = new WP_Query( array( 
				's'=> $_GET['q'], // the search query
				'post_status' => 'publish', // if you don't want drafts to be returned
				'ignore_sticky_posts' => 1,
				'posts_per_page' => 10 // how much to show at once
			) );
			if( $search_results->have_posts() ) :
				while( $search_results->have_posts() ) : $search_results->the_post();	
					// shorten the title a little
					$title = ( mb_strlen( $search_results->post->post_title ) > 50 ) ? mb_substr( $search_results->post->post_title, 0, 49 ) . '...' : $search_results->post->post_title;
					$return[] = array( $search_results->post->ID, $title ); // array( Post ID, Post Title )
				endwhile;
			endif;
			echo json_encode( $return );
			die();
		}
		
		// (php) get and return an array of admin options. if no options set, initialize.
		function get_admin_options() {
			$hkdev_mm_options = array(
				'enable_mm'				=> 'no',
				'header_type'			=> '200',
				'method'				=> 'die_message',
				'maintenance_title'		=> '',
				'maintenance_message'	=> $this->maintenance_html_head . $this->maintenance_html_body . $this->maintenance_html_foot,
				'maintenance_html'		=> $this->maintenance_html_body,
				'static_page'			=> ''
			);
			$hkdev_mm_saved_options = get_option($this->admin_options_name);
			if( !empty($hkdev_mm_saved_options) ) {
				foreach($hkdev_mm_saved_options as $key => $option)
					$hkdev_mm_options[$key] = $option;
			}else{
				update_option($this->admin_options_name, $hkdev_mm_options);
			}
			return $hkdev_mm_options;
		}
		
		// (php) generate key
		function alphastring( $len = 20, $valid_chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789' ){
			$str  = '';
			$chrs = explode( ' ', $valid_chars );
			for( $i=0; $i<$len; $i++ ){
				$str .= $valid_chars[ rand( 1, strlen( $valid_chars ) - 1 ) ];
			}
			return $str;
		}


		// (php) generate maintenance page
		function generate_maintenance_page( $content_override = '', $method = 'die_message' ){
			$hkdev_mm_options = $this->get_admin_options();
			if( $hkdev_mm_options['header_type'] == "200" ) {
				header('HTTP/1.1 200 OK');
				header('Status: 200 OK');
			}else{
				header('HTTP/1.1 503 Service Temporarily Unavailable');
				header('Status: 503 Service Temporarily Unavailable');
			}
			header('Retry-After: 600');
			$site_title = $hkdev_mm_options['maintenance_title'];
			$site_title = ($site_title!='') ? $site_title: get_bloginfo('name');
			if( $method == 'html' ){
				echo ( $content_override != '' ) ? stripslashes( $content_override ) : $this->maintenance_html_body;
				exit();
			}else if( $method == 'site_message' ){
				$out  = $this->maintenance_html_head;
				$out  = str_replace( '[[WP_TITLE]]', $site_title, $out );
				$out  = str_replace( '[[WP_STYLE]]', get_bloginfo('stylesheet_url'), $out );
				$out .= ( $content_override != '' ) ? stripslashes( $content_override ) : $this->maintenance_html_body;
				$out .= $this->maintenance_html_foot;
				echo $out;
				exit();
			}else{
				$out  = ( $content_override != '' ) ? stripslashes( $content_override ) : $this->maintenance_html_body;
				wp_die($out, $site_title, ['response' => $hkdev_mm_options['header_type']]);
			}
		}

		function hkdev_get_page_id() {
			// get or make permalink
			$url = !empty(get_the_permalink()) ? get_the_permalink() : (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
			//$permalink = strtok($url, '?');
			// get post_id using url/permalink
			return  url_to_postid($url);
		}

		
		// (php) find out if we need to redirect or not.
		function process_redirect() {
			global $wpdb;
			$valid_ips      = array();
			$valid_class_cs = array();
			$valid_aks      = array();
			$hkdev_matches  = apply_filters( 'hkdev_matches', array() );
			// set cookie if needed
			if ( isset( $_GET['hkdev_temp_access_key'] ) && trim( $_GET['hkdev_temp_access_key'] ) != '' ) {
				// get valid access keys
				$sql = "select access_key from " . $wpdb->prefix . $this->admin_options_name . "_access_keys where active = 1";
				$aks = $wpdb->get_results($sql, OBJECT);
				if( $aks ){
					foreach( $aks as $ak ){
						$valid_aks[] = $ak->access_key;
					}
				}
				// set cookie if there's a match
				if( in_array( $_GET['hkdev_temp_access_key'], $valid_aks ) ){
					$hkdev_mm_cookie_time = time()+(60*60*24*365);
					setcookie( 'hkdev_mm_access_key', $_GET['hkdev_temp_access_key'], $hkdev_mm_cookie_time, '/' );
					$_COOKIE['hkdev_mm_access_key'] = $_GET['hkdev_temp_access_key'];
				}
			}
			
			// get plugin options
			$hkdev_mm_options = $this->get_admin_options();
			
			// skip admin pages by default
			$url_parts = explode( '/', $_SERVER['REQUEST_URI'] );
			
			if( in_array( 'wp-admin', $url_parts ) ) {
				$hkdev_matches[] = "<!-- WPHKDEV_MM: SKIPPING ADMIN -->";
			}else{
				// determine if user is admin.. if so, bypass all of this.
				if ( current_user_can( apply_filters( 'hkdev_user_can', 'manage_options' ) ) ) {
					$hkdev_matches[] = "<!-- WPHKDEV_MM: USER IS ADMIN -->";
				}else{
					//if page excluded
					if(isset($hkdev_mm_options['exclude_pages']) && in_array( $this->hkdev_get_page_id() , $hkdev_mm_options['exclude_pages'])){
						$hkdev_matches[] = "<!-- WPHKDEV_MM: PAGE EXCLUDED -->";
					}else{

						if( $hkdev_mm_options['enable_mm'] == "YES" ){
							// get valid unrestricted IPs
							$sql = "select ip_address from " . $wpdb->prefix . $this->admin_options_name . "_unrestricted_ips where active = 1";
							$ips = $wpdb->get_results($sql, OBJECT);
							if( $ips ){
								foreach( $ips as $ip ){
									$ip_parts = explode( '.', $ip->ip_address );
									if( $ip_parts[3] == '*' ){
										$valid_class_cs[] = $ip_parts[0] . '.' . $ip_parts[1] . '.' . $ip_parts[2];
									}else{
										$valid_ips[] = $ip->ip_address;
									}
								}
							}
							
							// get valid access keys
							$valid_aks = array();
							$sql = "select access_key from " . $wpdb->prefix . $this->admin_options_name . "_access_keys where active = 1";
							$aks = $wpdb->get_results($sql, OBJECT);
							if( $aks ){
								foreach( $aks as $ak ){
									$valid_aks[] = $ak->access_key;
								}
							}
							
							// manage cookie filtering
							if( isset( $_COOKIE['hkdev_mm_access_key'] ) && $_COOKIE['hkdev_mm_access_key'] != '' ){
								// check versus active codes
								if( in_array( $_COOKIE['hkdev_mm_access_key'], $valid_aks ) ){
									$hkdev_matches[] = "<!-- WPHKDEV_MM: COOKIE MATCH -->";
								}
							}
							
							// manage ip filtering 
							if( in_array( $this->get_user_ip(), $valid_ips ) ) {
								$hkdev_matches[] = "<!-- WPHKDEV_MM: IP MATCH -->";
							}else{
								// check for partial ( class c ) match
								$ip_parts     = explode( '.', $this->get_user_ip() );
								$user_class_c = $ip_parts[0] . '.' . $ip_parts[1] . '.' . $ip_parts[2];
								if( in_array( $user_class_c, $valid_class_cs ) ) {
									$hkdev_matches[] = "<!-- WPHKDEV_MM: CLASS C MATCH -->";
								}
							}
							
							if( count( $hkdev_matches ) == 0 ) {
								// no match found. show maintenance page / message
								if( $hkdev_mm_options['method'] == 'redirect' ){
									// redirect
									header( 'HTTP/1.1 307 Temporary Redirect' );
									header( 'Status: 307 Temporary Redirect' );
									header( 'Retry-After: 600' );
									header( 'Location:'.$hkdev_mm_options['static_page'] );
									exit();
								}else if( $hkdev_mm_options['method'] == 'html' ){
									$this->generate_maintenance_page( $hkdev_mm_options['maintenance_html'], 'html' );
								}else{
									$this->generate_maintenance_page( $hkdev_mm_options['maintenance_message'], $hkdev_mm_options['method'] );
								}
							}
						}else{
							$hkdev_matches[] = "<!-- WPHKDEV_MM: REDIR DISABLED -->";
						}
					}
				}
			}
		}
		
		// (php) add new IP
		function add_new_ip() {
			if ( !current_user_can('manage_options') ) wp_die("Oh no you don't!");
			check_ajax_referer( 'hkdev_nonce', 'security' );
			global $wpdb;
			$tbl        = $wpdb->prefix . $this->admin_options_name . '_unrestricted_ips';
			$name       = sanitize_text_field( $_POST['hkdev_mm_ip_name'] );
			$ip_address = sanitize_text_field( trim( $_POST['hkdev_mm_ip_ip'] ) );
			$sql        = $wpdb->prepare("insert into $tbl ( name, ip_address, created_at ) values ( %s, %s, NOW() )", $name, $ip_address);
			$rs         = $wpdb->query( $sql );
			if( $rs ){
				// send table data
				$this->print_unrestricted_ips();
			}else{
				echo '<div class="notice notice-error" id="hkdev_mm_error_notice"><p><strong>' . _x( 'Unable to add IP because of a database error. Please reload the page.', 'Error message adding new IP', 'hkdev-maintenance-mode' ) . '</strong></p></div>'; 
			}
			die();
		}

		// (php) toggle maintenance mode (hk)
		function toggle_maintenance_mode(){
			$hkdev_mm_options = $this->get_admin_options();
			if ( !current_user_can('manage_options') ) wp_die("Oh no you don't!");
			check_ajax_referer( 'hkdev_nonce', 'security' );
			$hkdev_mm_options['enable_mm'] = ( $hkdev_mm_options['enable_mm'] != "YES" ) ? "YES" : "NO" ;
			if( update_option( $this->admin_options_name, $hkdev_mm_options ) ){
				// $this->print_unrestricted_ips();
				echo 'SUCCESS' . '|' . $hkdev_mm_options['enable_mm'];
			}else{
				echo '<div class="notice notice-error" id="hkdev_mm_error_notice"><p><strong>' . _x( 'There was an unknown error. Please reload the page.', 'Error message when toggle Maintenance Mode ', 'hkdev-maintenance-mode' ) . '</strong></p></div>'; 
			}
			die();
		}

		// (php) toggle IP status
		function toggle_ip_status(){
			if ( !current_user_can('manage_options') ) wp_die("Oh no you don't!");
			check_ajax_referer( 'hkdev_nonce', 'security' );
			global $wpdb;
			$tbl       = $wpdb->prefix . $this->admin_options_name . '_unrestricted_ips';
			$ip_id     = esc_sql( $_POST['hkdev_mm_ip_id'] );
			$ip_active = ( $_POST['hkdev_mm_ip_active'] == 1 ) ? 1 : 0;
			$sql       = $wpdb->prepare("update $tbl set active = %s where id = %d", $ip_active, $ip_id);
			$rs        = $wpdb->query( $sql );
			if( $rs ){
				// $this->print_unrestricted_ips();
				echo 'SUCCESS' . '|' . $ip_id . '|' . $ip_active;
			}else{
				echo '<div class="notice notice-error" id="hkdev_mm_error_notice"><p><strong>' . _x( 'There was an unknown database error. Please reload the page.', 'Error message when toggle IP status', 'hkdev-maintenance-mode' ) . '</strong></p></div>'; 
			}
			die();
		}
		
		// (php) delete IP
		function delete_ip(){
			if ( !current_user_can('manage_options') ) wp_die("Oh no you don't!");
			check_ajax_referer( 'hkdev_nonce', 'security' );
			global $wpdb;
			$tbl       = $wpdb->prefix . $this->admin_options_name . '_unrestricted_ips';
			$ip_id     = esc_sql( $_POST['hkdev_mm_ip_id'] );
			$sql       = $wpdb->prepare("delete from $tbl where id = %d", $ip_id);
			$rs        = $wpdb->query( $sql );
			if( $rs ){
				$this->print_unrestricted_ips();
			}else{
				echo '<div class="notice notice-error" id="hkdev_mm_error_notice"><p><strong>' . _x( 'Unable to delete IP because of a database error. Please reload the page.', 'Error message deleting IP', 'hkdev-maintenance-mode') . '</strong></p></div>'; 
			}
			die();
		}
		
		// (php) add new Access Key
		function add_new_ak() {
			if ( !current_user_can('manage_options') ) wp_die("Oh no you don't!");
			check_ajax_referer( 'hkdev_nonce', 'security' );
			global $wpdb;
			$tbl        = $wpdb->prefix . $this->admin_options_name . '_access_keys';
			$name       = sanitize_text_field( $_POST['hkdev_mm_ak_name'] );
			$email      = sanitize_email( $_POST['hkdev_mm_ak_email'] );
			$access_key = esc_sql( $this->alphastring(20) );
			$sql        = $wpdb->prepare("insert into $tbl ( name, email, access_key, created_at ) values ( %s, %s, %s, NOW() )", $name, $email, $access_key);
			$rs         = $wpdb->query( $sql );
			if( $rs ){
				// email user
				$subject    = sprintf( _x( "Access Key Link for %s", 'Access Key Email Subject, %s = name of the website/blog', 'hkdev-maintenance-mode'), get_bloginfo() );
				$full_msg   = sprintf( _x( "The following link will provide you temporary access to %s:", 'Access Key Email Message, %s = name of the website/blog', 'hkdev-maintenance-mode'), get_bloginfo() ) . "\n\n"; 
				$full_msg  .= _x( "Please note that you must have cookies enabled for this to work.", 'Access Key Email Message (secong line)', 'hkdev-maintenance-mode' ) . "\n\n";
				$full_msg  .= get_bloginfo('url') . '?hkdev_temp_access_key=' . $access_key;
				$mail_sent  = wp_mail( $email, $subject, $full_msg );
				echo ( $mail_sent ) ? '<!-- SEND_SUCCESS -->' : '<!-- SEND_FAILURE -->';
				// send table data
				$this->print_access_keys();
			}else{
				echo '<div class="notice notice-error" id="hkdev_mm_error_notice"><p><strong>' . _x( "Unable to add Access Key because of a database error. Please reload the page.", 'Message error when unable to add Access Key', 'hkdev-maintenance-mode' ) . '</strong></p></div>'; 
			}
			die();
		}
		
		// (php) toggle Access Key status
		function toggle_ak_status(){
			if ( !current_user_can('manage_options') ) wp_die("Oh no you don't!");
			check_ajax_referer( 'hkdev_nonce', 'security' );
			global $wpdb;
			$tbl       = $wpdb->prefix . $this->admin_options_name . '_access_keys';
			$ak_id     = esc_sql( $_POST['hkdev_mm_ak_id'] );
			$ak_active = ( $_POST['hkdev_mm_ak_active'] == 1 ) ? 1 : 0;
			$sql       = $wpdb->prepare("update $tbl set active = %d where id = %d", $ak_active, $ak_id);
			$rs        = $wpdb->query( $sql );
			if( $rs ){
				// $this->print_access_keys();
				echo 'SUCCESS' . '|' . $ak_id . '|' . $ak_active;
			}else{
				echo '<div class="notice notice-error" id="hkdev_mm_error_notice"><p><strong>' . _x( "There was an unknown database error. Please reload the page.", 'Message error when toggle Access Key status', 'hkdev-maintenance-mode' ) . '</strong></p></div>'; 
			}
			die();
		}
		
		// (php) delete Access Key
		function delete_ak(){
			if ( !current_user_can('manage_options') ) wp_die("Oh no you don't!");
			check_ajax_referer( 'hkdev_nonce', 'security' );
			global $wpdb;
			$tbl       = $wpdb->prefix . $this->admin_options_name . '_access_keys';
			$ak_id     = esc_sql( $_POST['hkdev_mm_ak_id'] );
			$sql       = $wpdb->prepare("delete from $tbl where id = %d", $ak_id);
			$rs        = $wpdb->query( $sql );
			if( $rs ){
				$this->print_access_keys();
			}else{
				echo '<div class="notice notice-error" id="hkdev_mm_error_notice"><p><strong>' . _x( 'Unable to delete Access Key because of a database error. Please reload the page.', 'Message error when unable to delete Access Key', 'hkdev-maintenance-mode' ) . '</strong></p></div>'; 
			}
			die();
		}
		
		// (php) resend Access Key email
		function resend_ak(){
			if ( !current_user_can('manage_options') ) wp_die("Oh no you don't!");
			check_ajax_referer( 'hkdev_nonce', 'security' );
			global $wpdb;
			$tbl       = $wpdb->prefix . $this->admin_options_name . '_access_keys';
			$ak_id     = esc_sql( $_POST['hkdev_mm_ak_id'] );
			$sql       = $wpdb->prepare("select * from $tbl where id = %d", $ak_id);
			$ak        = $wpdb->get_row( $sql );
			if( $ak ){
				$subject    = sprintf( _x( "Access Key Link for %s", 'Resend Access Key Email Subject, %s = name of the website/blog', 'hkdev-maintenance-mode'), get_bloginfo() );
				$full_msg   = sprintf( _x( "The following link will provide you temporary access to %s:", 'Resend Access Key Email Message, %s = name of the website/blog', 'hkdev-maintenance-mode'), get_bloginfo() ) . "\n\n"; 
				$full_msg  .= _x( "Please note that you must have cookies enabled for this to work.", 'Resend Access Key Email Message (secong line)', 'hkdev-maintenance-mode' ) . "\n\n";
				$full_msg  .= get_bloginfo('url') . '?hkdev_temp_access_key=' . $ak->access_key;
				$mail_sent  = wp_mail( $ak->email, $subject, $full_msg );
				echo ( $mail_sent ) ? 'SEND_SUCCESS' : 'SEND_FAILURE';
			}else{
				__( 'ERROR', 'hkdev-maintenance-mode' );
			}
			die();
		}
		
		// (php) generate IP table data 
		function print_unrestricted_ips( ){
			global $wpdb;
			?>
			<table class="widefat fixed" cellspacing="0">
				<thead>
					<tr>
						<th class="column-hkdev-ip-name"  ><?php _e( "Name", 'hkdev-maintenance-mode' ); ?></th>
						<th class="column-hkdev-ip-ip"    ><?php _e( "IP", 'hkdev-maintenance-mode' ); ?></th>
						<th class="column-hkdev-ip-active"><?php _e( "Active", 'hkdev-maintenance-mode' ); ?></th>
						<th class="column-hkdev-actions"  ><?php _e( "Actions", 'hkdev-maintenance-mode' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php
					$sql = "select * from " . $wpdb->prefix . $this->admin_options_name . "_unrestricted_ips order by name";
					$ips = $wpdb->get_results($sql, OBJECT);
					$ip_row_class = 'alternate';
					if( $ips ){
						foreach( $ips as $ip ){
							?>
							<tr id="wphkdev-ip-<?php echo $ip->id; ?>" valign="middle"  class="<?php echo $ip_row_class; ?>">
								<td class="column-hkdev-ip-name"><?php echo $ip->name; ?></td>
								<td class="column-hkdev-ip-ip"><?php echo $ip->ip_address; ?></td>
								<td class="column-hkdev-ip-active" id="hkdev_mm_ip_status_<?php echo $ip->id; ?>" ><?php echo ( $ip->active == 1) ? '<span class="actived">'.__('Yes', 'hkdev-maintenance-mode').'</span>' : '<span class="deactived">'.__('No', 'hkdev-maintenance-mode').'</span>'; ?></td>
								<td class="column-hkdev-actions">
									<span class='edit' id="hkdev_mm_ip_status_<?php echo $ip->id; ?>_action">
										<?php if( $ip->active == 1 ){ ?>
											<a href="javascript:hkdev_mm_toggle_ip(0,<?php echo $ip->id; ?>);"><?php _e( "Disable" , 'hkdev-maintenance-mode'); ?></a> | 
										<?php }else{ ?>
											<a href="javascript:hkdev_mm_toggle_ip(1,<?php echo $ip->id; ?>);"><?php _e( "Enable", 'hkdev-maintenance-mode' ); ?></a> | 
										<?php } ?>
									</span>
									<span class='delete'>
										<a class='submitdelete' href="javascript:hkdev_mm_delete_ip(<?php echo $ip->id ?>,'<?php echo addslashes( $ip->ip_address ) ?>');" ><?php _e( "Delete", 'hkdev-maintenance-mode' ); ?></a>
									</span>
								</td>
							</tr>
							<?php
							$ip_row_class = ( $ip_row_class == '' ) ? 'alternate' : '';
						}
					} else {
                        ?>
                        <tr valign="middle" class="alternate">
                            <td class="no-results" colspan="4"><?php _e( "No IPs found!", 'hkdev-maintenance-mode' ); ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
                <tfoot>
					<tr id="wphkdev-ip-NEW" valign="middle">
						<td class="column-hkdev-ip-name">
							<input class="hkdev_mm_disabled_field" type="text" id="hkdev_mm_new_ip_name" name="hkdev_mm_new_ip_name" placeholder="<?php _e( "Enter Name:", 'hkdev-maintenance-mode' ); ?>">
						</td>
						<td class="column-hkdev-ip-ip">
							<input class="hkdev_mm_disabled_field" type="text" id="hkdev_mm_new_ip_ip" name="hkdev_mm_new_ip_ip" placeholder="<?php _e( "Enter IP:", 'hkdev-maintenance-mode' ); ?>">
						</td>
						<td class="column-hkdev-ip-active"><span class='button edit' id="hkdev_mm_add_ip_link">
								<a href="javascript:hkdev_mm_add_new_ip();"><?php _e( "Add New IP", 'hkdev-maintenance-mode' ); ?></a>
							</span></td>
						<td class="column-hkdev-actions">&nbsp;</td>
					</tr>
                </tfoot>
			</table>
			<?php
		}
		
		// (php) genereate Access Key table data
		function print_access_keys(){
			global $wpdb;
			?>
			<table class="widefat fixed" cellspacing="0">
				<thead>
					<tr>
						<th class="column-hkdev-ak-name"  ><?php _e( "Name", 'hkdev-maintenance-mode' ); ?></th>
						<th class="column-hkdev-ak-email" ><?php _e( "Email", 'hkdev-maintenance-mode' ); ?></th>
						<th class="column-hkdev-ak-key"   ><?php _e( "Access Key", 'hkdev-maintenance-mode' ); ?></th>
						<th class="column-hkdev-ak-active" style="width: 10%;white-space: nowrap;"><?php _e( "Active", 'hkdev-maintenance-mode' ); ?></th>
						<th class="column-hkdev-actions"  ><?php _e( "Actions", 'hkdev-maintenance-mode' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php
					$sql   = "select * from " . $wpdb->prefix . $this->admin_options_name . "_access_keys order by name";
					$codes = $wpdb->get_results($sql, OBJECT);
					$ak_row_class = 'alternate';
					if( $codes ){
						foreach( $codes as $code ){
							?>
							<tr id="wphkdev-ak-<?php echo $code->id; ?>" valign="middle"  class="<?php echo $ak_row_class; ?>">
								<td class="column-hkdev-ak-name"><?php echo $code->name; ?></td>
								<td class="column-hkdev-ak-email"><a href="mailto:<?php echo $code->email; ?>" title="email <?php echo $code->email; ?>"><?php echo $code->email; ?></a></td>
								<td class="column-hkdev-ak-key"><?php echo $code->access_key; ?></td>
								<td class="column-hkdev-ak-active" id="hkdev_mm_ak_status_<?php echo $code->id; ?>" ><?php echo ( $code->active == 1) ? '<span class="actived">'.__('Yes', 'hkdev-maintenance-mode').'</span>' : '<span class="deactived">'.__('No', 'hkdev-maintenance-mode').'</span>'; ?></td>
								<td class="column-hkdev-actions">
									<span class='edit' id="hkdev_mm_ak_status_<?php echo $code->id; ?>_action">
										<?php if( $code->active == 1 ){ ?>
											<a href="javascript:hkdev_mm_toggle_ak(0,<?php echo $code->id; ?>);"><?php _e( "Disable", 'hkdev-maintenance-mode' ); ?></a> | 
										<?php }else{ ?>
											<a href="javascript:hkdev_mm_toggle_ak(1,<?php echo $code->id; ?>);"><?php _e( "Enable", 'hkdev-maintenance-mode' ); ?></a> | 
										<?php } ?>
									</span>
									<span class='resend'>
										<a class='submitdelete' href="javascript:hkdev_mm_resend_ak(<?php echo $code->id ?>,'<?php echo addslashes( $code->name ) ?>','<?php echo addslashes( $code->email ) ?>');" ><?php _e( "Resend Code", 'hkdev-maintenance-mode' ); ?></a> | 
									</span>
									<?php if((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')|| $_SERVER['SERVER_PORT'] == 443){ ?>
									<span class='copy' id="hkdev_mm_submit_copy_<?php echo $code->id; ?>">
										<a class='submitdelete' href="javascript:hkdev_mm_copy_ak( <?php echo $code->id ?>, '<?php echo $code->access_key ?>' );" ><?php _e( "Copy", 'hkdev-maintenance-mode' ); ?></a> | 
									</span>
									<?php } ?>
									<span class='delete'>
										<a class='submitdelete' href="javascript:hkdev_mm_delete_ak(<?php echo $code->id ?>,'<?php echo addslashes( $code->name ) ?>');" ><?php _e( "Delete" , 'hkdev-maintenance-mode'); ?></a>
									</span>
								</td>
							</tr>
							<?php
							$ak_row_class = ( $ak_row_class == '' ) ? 'alternate' : '';
						}
					} else {
                        ?>
                        <tr valign="middle" class="alternate">
                            <td class="no-results" colspan="5"><?php _e( "No Access Codes found!", 'hkdev-maintenance-mode' ); ?></td>
                        </tr>
                        <?php
                    }
					/*
					?>
					<tr id="wphkdev-ak-NONE" valign="middle"  class="<?php echo $ak_row_class; ?>">
						<td colspan="5">Enter a New Access Code</td>
					</tr>
					<?php
					$ak_row_class = ( $ak_row_class == '' ) ? 'alternate' : '';
					*/
					?>
                </tbody>
                <tfoot>
					<tr id="wphkdev-ak-NEW" valign="middle">
						<td class="column-hkdev-ak-name">
							<input class="hkdev_mm_disabled_field" type="text" id="hkdev_mm_new_ak_name" name="hkdev_mm_new_ak_name" placeholder="<?php _e( "Enter Name:", 'hkdev-maintenance-mode' ); ?>">
						</td>
						<td class="column-hkdev-ak-email">
							<input class="hkdev_mm_disabled_field" type="text" id="hkdev_mm_new_ak_email" name="hkdev_mm_new_ak_email" placeholder="<?php _e( "Enter Email:", 'hkdev-maintenance-mode' ); ?>">
						</td>
						<td class="column-hkdev-ak-key">
							<span class='button edit' id="hkdev_mm_add_ak_link">
								<a href="javascript:hkdev_mm_add_new_ak();"><?php _e( "Add New Access Key", 'hkdev-maintenance-mode' ); ?></a>
							</span>
						</td>
						<td class="column-hkdev-ak-active">&nbsp;</td>
						<td class="column-hkdev-actions">&nbsp;</td>
					</tr>
                </tfoot>
			</table>
			<?php
		}
		
		// (php) display redirect status if active
		function display_status_if_active(){
			global $wpdb;
			$hkdev_mm_options = $this->get_admin_options();
			$show_notice = false;
			if( $hkdev_mm_options['enable_mm'] == 'YES' ) $show_notice = true;
			if( $show_notice ){
				//echo '<div class="notice notice-warning" id="hkdev_mm_enabled_notice"><p><strong>' . _x( "Maintenance Mode is Enabled", 'Notice when Maintenance Mode is active','hkdev-maintenance-mode'). '</strong></p></div>'; 
				$jq_indicator = '
				jQuery(".hkdev-indicator").addClass("hkdev-indicator--enabled");
				jQuery(".hkdev-indicator .hkdev-indicator-title").attr("title", "'. _x('Maintenance ON', 'Admin bar indicator', 'hkdev-maintenance-mode') .'");
				';
			}else{
				$jq_indicator = '
				jQuery(".hkdev-indicator").removeClass("hkdev-indicator--enabled");
				jQuery(".hkdev-indicator .hkdev-indicator-title").attr("title", "'. _x('Maintenance OFF', 'Admin bar indicator', 'hkdev-maintenance-mode') .'");
				';
			}
			echo '<script>'.$jq_indicator.'</script>';
		}
		

		//Admin bar indicator
		function ab_indicator(&$wp_admin_bar){
			if (!is_admin_bar_showing() || !is_blog_admin()) { return; }
			//$enabled = apply_filters('hkdev_admin_bar_indicator_enabled', $enabled = true);
			if (!current_user_can('delete_plugins')) { return false; }
			//if (!$enabled) { return false; }
			$hkdev_mm_options = $this->get_admin_options();
			$is_enabled = $hkdev_mm_options['enable_mm'];
			if ($is_enabled=='YES') {
				$status = _x('Maintenance ON', 'Admin bar indicator', 'hkdev-maintenance-mode');
			}else{
				$status = _x('Maintenance OFF', 'Admin bar indicator', 'hkdev-maintenance-mode');
			}
			$indicatorClasses = ($is_enabled=='YES') ? 'hkdev-indicator hkdev-indicator--enabled' : 'hkdev-indicator';
			$wp_admin_bar->add_menu( 
				array(
					'id' => 'hkdev-indicator',
					'title' => '<span class="icon_mm"><svg xmlns="http://www.w3.org/2000/svg" xmlns:bx="https://boxy-svg.com" version="1.0" viewBox="0 0 235 215"><defs/><g bx:origin="0 0"><path d="M33 132l30 32c4 4 6 8 5 14-3 10-15 14-23 7L3 140c-4-5-4-12 1-17l40-44c6-6 14-6 20-1s6 14 0 20l-31 34zM202 131l-30-32-5-9c-1-6 2-11 7-14 5-2 12-2 16 2l42 44c4 5 4 13-1 18l-40 44c-6 6-14 6-20 1-5-5-6-14-1-20l32-34zM82 212l7 2c8 3 14-1 17-12l34-116c16-2 31-13 36-30 5-18-1-38-16-48l-12 39-27-8 12-39c-18 1-34 13-40 31-5 17 0 35 13 45L73 191c-2 10-2 18 9 21z"/></g></svg></span>',
					'parent' => false,
					'href' => get_admin_url(null, 'options-general.php?page=hkdev_Maintenance_Mode'),
					'meta' => array(
						'title' => $status,
						'class' => $indicatorClasses
					)
				)
			);
			//$wp_admin_bar->add_node($indicator);
		}

		
		//Indicator styles
		function ab_indicator_style(){
			echo '<style type="text/css">
			#wp-admin-bar-hkdev-indicator.hkdev-indicator--enabled { background: rgba(159, 0, 0, 1) }
			.icon_mm{
				float:left;
				width:22px !important;
				height:22px !important;
				margin: 4px 0 !important;
			}
			.icon_mm svg{
				fill: rgba(255,255,255,.6) !important;
			}
			#wp-admin-bar-hkdev-indicator:hover .icon_mm svg {
				fill: #00b9eb !important;
			}</style>';
		}

		//Plugin action links
		function action_links($links){
		   $links[] = '<a href="' . get_admin_url(null, 'options-general.php?page=hkdev_Maintenance_Mode') . '">' . _x('Settings', 'Plugin Settings link','hkdev-maintenance-mode') . '</a>';   
		   return $links;
		}

		// (php) create the admin page
		function print_admin_page() {
			global $wpdb;
			global $ajax_nonce;
			$hkdev_mm_options = $this->get_admin_options();

			// process update
			if ( isset( $_POST['update_wp_maintenance_mode_settings']) ) {
			
				check_admin_referer( 'hkdev_nonce' );  

				function shapeSpace_allowed_html() {
					$allowed_tags = array(
						'a' => array(
							'class' => array(),
							'href'  => array(),
							'rel'   => array(),
							'title' => array(),
							'id' => array(),
							'target' => array(),
							'style' => array(),
						),
						'abbr' => array(
							'title' => array(),
						),
						'b' => array(),
						'br' => array(),
						'body' => array(
							'class' => array(),
							'style' => array(),
						),
						'blockquote' => array(
							'cite'  => array(),
						),
						'cite' => array(
							'title' => array(),
						),
						'code' => array(),
						'del' => array(
							'datetime' => array(),
							'title' => array(),
						),
						'dd' => array(),
						'div' => array(
							'id' => array(),
							'class' => array(),
							'title' => array(),
							'style' => array(),
							'data-*' => array(),
						),
						'dl' => array(),
						'dt' => array(),
						'em' => array(),
						'h1' => array(),
						'h2' => array(),
						'h3' => array(),
						'h4' => array(),
						'h5' => array(),
						'h6' => array(),
						'head' => array(),
						'link' => array(
							'href' => array(),
							'rel' => array(),
							'type' => array(),
						),
						'!DOCTYPE' => array(
							'html' => array(),
						),
						'html' => array(
							'class' => array(),
							'lang' => array(),
							'style' => array(),
						),
						'nav' => array(
							'id' => array(),
							'class' => array(),
						),
						'main' => array(
							'id' => array(),
							'class' => array(),
						),
						'meta' => array(
							'charset' => array(),
							'name' => array(),
							'content' => array(),
							'http-equiv' => array(),
						),
						'i' => array(
							'class'  => array(),
						),
						'img' => array(
							'id' => array(),
							'alt'    => array(),
							'class'  => array(),
							'height' => array(),
							'src'    => array(),
							'width'  => array(),
							'data-*' => array(),
							'style' => array(),
						),
						'li' => array(
							'class' => array(),
							'style' => array(),
						),
						'ol' => array(
							'id' => array(),
							'class' => array(),
							'style' => array(),
						),
						'ul' => array(
							'id' => array(),
							'class' => array(),
							'style' => array(),
						),
						'p' => array(
							'id' => array(),
							'class' => array(),
							'style' => array(),
						),
						'title' => array(),
						'q' => array(
							'cite' => array(),
							'title' => array(),
						),
						'script' => array(),
						'span' => array(
							'id' => array(),
							'class' => array(),
							'title' => array(),
							'style' => array(),
							'data-*' => array(),
						),
						'style' => array(
							'type' => array(),
						),
						'section' => array(
							'id' => array(),
							'class' => array(),
							'style' => array(),
						),
						'strike' => array(),
						'strong' => array(),
						'ul' => array(
							'id' => array(),
							'class' => array(),
						),
						'video' => array(
							'id' => array(),
							'class' => array(),
							'autoplay' => array(),
							'controls' => array(),
							'height' => array(),
							'loop' => array(),
							'muted' => array(),
							'poster' => array(),
							'preload' => array(),
							'src' => array(),
							'width' => array(),
						),
					);
					return $allowed_tags;
				}
				$allowed_html = shapeSpace_allowed_html();

				
				// prepare options
				$hkdev_mm_options['header_type']         = sanitize_text_field( trim( $_POST['hkdev_mm_header_type'] ) );
				$hkdev_mm_options['static_page']         = esc_url_raw( trim( $_POST['hkdev_mm_static_page'] ) );
				$hkdev_mm_options['method']              = sanitize_text_field( trim( $_POST['hkdev_mm_method'] ) );
				$hkdev_mm_options['maintenance_title']   = sanitize_text_field( trim( $_POST['hkdev_mm_maintenance_title'] ) );
				$hkdev_mm_options['maintenance_message'] = wp_kses_post( $_POST['hkdev_mm_maintenance_message'] );
				$hkdev_mm_options['maintenance_html']    = wp_kses( $_POST['hkdev_mm_maintenance_html'], $allowed_html );
				$hkdev_mm_options['exclude_pages']       = array_map( 'esc_attr', (isset( $_POST['hkdev_mm_exclude_pages'] ) ? (array) $_POST['hkdev_mm_exclude_pages'] : array()) );
				
				// update options
				update_option( $this->admin_options_name, $hkdev_mm_options );
				echo '<div class="notice notice-success is-dismissible"><p><strong>' . _x( "Settings Updated", 'Notice when settings updated', 'hkdev-maintenance-mode') . '</strong></p></div>';
				
			} ?>
			
			<script type="text/javascript" charset="utf-8">
				var hkdev_mm_codeEditor_is_ini = false;

				// bind actions
				jQuery(document).ready(function() {

					//(js)  sleep time expects milliseconds
					window.hkdev_sleep = function(time) {
						return new Promise((resolve) => setTimeout(resolve, time));
					}

					//active tab
					<?php if(isset($_POST['activeTab'])){
						$activeTab = esc_attr( $_POST['activeTab'] );
						echo 'jQuery(".nav-tab-wrapper a").removeClass("nav-tab-active");
					jQuery(".hkdev_mm_admin_section").addClass("hidden");
					jQuery("a[href=\''.$activeTab.'\']").addClass("nav-tab-active");
					jQuery("'.$activeTab.'").removeClass("hidden");';
						
					if($activeTab=='#unrestricted-ip'||$activeTab=='#access-keys'){ 
						echo "jQuery('#hkdev_submit_button').hide();";
					}else{
						echo "jQuery('#hkdev_submit_button').show();";
					}

					} ?>

					// method mode toggle
					jQuery( '#hkdev_mm_method' ).change( function(){ hkdev_mm_toggle_method_options(); });

					// nav tabs settings (hk)
					jQuery(".nav-tab-wrapper a").click(function() {
						event.preventDefault();
						jQuery(".nav-tab-wrapper a").removeClass("nav-tab-active");
						jQuery(this).addClass("nav-tab-active");
						jQuery(".hkdev_mm_admin_section").addClass("hidden");
						var activeTab = jQuery(this).attr("href");
						jQuery(activeTab).removeClass("hidden");
						jQuery('#activeTab').val(activeTab);
						if(jQuery('#hkdev_method_html' ).is(":visible") && activeTab=='#maintenance-message' && !hkdev_mm_codeEditor_is_ini){
							hkdev_mm_ini_codeEditor ();
						}
						if(activeTab=='#unrestricted-ip'||activeTab=='#access-keys'){ 
							jQuery('#hkdev_submit_button').hide();
						}else{
							jQuery('#hkdev_submit_button').show();
						}
					});

					if(jQuery('#hkdev_method_html' ).is(":visible") && !hkdev_mm_codeEditor_is_ini){
						hkdev_mm_ini_codeEditor ();
					}


					// multiple select with AJAX search
					jQuery('#hkdev_mm_exclude_pages').select2({
						ajax: {
								url: ajaxurl, // AJAX URL is predefined in WordPress admin
								dataType: 'json',
								delay: 250, // delay in ms while typing when to perform a AJAX search
								data: function (params) {
									return {
										q: params.term, // search query
										action: 'hkdev_mm_getposts' // AJAX action for admin-ajax.php
									};
								},
								processResults: function( data ) {
								var options = [];
								if ( data ) {
									// data is the array of arrays, and each of them contains ID and the Label of the option
									jQuery.each( data, function( index, text ) { // do not forget that "index" is just auto incremented value
										options.push( { id: text[0], text: text[1]  } );
									});
								}
								return {
									results: options
								};
							},
							//cache: true
						},
						multiple: true,
						minimumInputLength: 3, // the minimum of symbols to input before perform a search
						language: {
							errorLoading: function () {
								return '<?php _e( "The results could not be loaded.", "hkdev-maintenance-mode" ) ?>';
							},
							inputTooLong: function (args) {
								var overChars = args.input.length - args.maximum;
								var message = '<?php echo sprintf( _x( "Please delete %s characters", "Plural", "hkdev-maintenance-mode"), "{overChars}" ) ?>';
								if (overChars == 1) {
									message = '<?php _ex( "Please delete 1 character", "Singular", "hkdev-maintenance-mode") ?>';
								}
								return message.replace('{overChars}',overChars);
							},
							inputTooShort: function (args) {
								var remainingChars = args.minimum - args.input.length;
								var message = '<?php echo sprintf( __( "Please enter %s or more characters", "hkdev-maintenance-mode"), "{remainingChars}" ) ?>';
								return message.replace('{remainingChars}',remainingChars);
							},
							loadingMore: function () {
							return '<?php _e( "Loading more results…", "hkdev-maintenance-mode" ) ?>';
							},
							maximumSelected: function (args) {
								var message = '<?php echo sprintf( _x( "You can only select %s items", "Plural", "hkdev-maintenance-mode"), "{args.maximum}" ) ?>';
								if (args.maximum == 1) {
									message = '<?php  _ex( "You can only select 1 item", "Singular", "hkdev-maintenance-mode") ?>';
								}
								return message.replace('{args.maximum}',args.maximum);;
							},
							noResults: function () {
								return '<?php _e( "No results found", "hkdev-maintenance-mode" ) ?>';
							},
							searching: function () {
							return '<?php _e( "Searching…", "hkdev-maintenance-mode" ) ?>';
							},
							removeAllItems: function () {
								return '<?php _e( "Remove all items", "hkdev-maintenance-mode" ) ?>';
							},
							removeItem: function () {
								return '<?php _e( "Remove item", "hkdev-maintenance-mode" ) ?>';
							},
							search: function() {
								return '<?php _e( "Search", "hkdev-maintenance-mode" ) ?>';
							}
						}
					});

				});

				//ini wp code editor (hk)
				function hkdev_mm_ini_codeEditor () {
					wp.codeEditor.initialize( jQuery('#hkdev_mm_maintenance_html'), cm_settings );
					hkdev_mm_codeEditor_is_ini = true;
				}
				
				// (js) update form layout based on method option
				function hkdev_mm_toggle_method_options () {
					if( jQuery('#hkdev_mm_method').val() == 'redirect' ){
						jQuery('#hkdev_method_message' ).hide();
						jQuery('#hkdev_method_html' ).hide();
						jQuery('#hkdev_method_redirect').show();
					}else 
					if( jQuery('#hkdev_mm_method').val() == 'html' ){
						jQuery('#hkdev_method_message' ).hide();
						jQuery('#hkdev_method_html' ).show();
						jQuery('#hkdev_method_redirect').hide();
						if(!hkdev_mm_codeEditor_is_ini){
							hkdev_mm_ini_codeEditor ();
						}
					}else{
						jQuery('#hkdev_method_message' ).show();
						jQuery('#hkdev_method_html' ).hide();
						jQuery('#hkdev_method_redirect').hide();
					}
				}
				
				// (js) undim field
				function hkdev_mm_undim_field( field_id, default_text ) {
					if( jQuery('#'+field_id).val() == default_text ) jQuery('#'+field_id).val('');
					jQuery('#'+field_id).css('color','#000');
				}
				// (js) dim field
				function hkdev_mm_dim_field( field_id, default_text ) {
					if( jQuery('#'+field_id).val() == '' ) {
						jQuery('#'+field_id).val(default_text);
						jQuery('#'+field_id).css('color','#888');
					}
				}
				
				// (js) validate IP4 address
				function ValidateIPaddress(ipaddress) {  
					if (/^(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.((25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.((25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)|\*))$/.test(ipaddress)) {  
						return (true)  
					}  
				}
				

				// (js) toggle maintenance mode
				function hkdev_mm_toggle_maintenance_mode () {
					// prepare ajax data
					var data = {
						action: 'hkdev_mm_toggle_maintenance_mode',
						security: '<?php echo $ajax_nonce; ?>'
					};
					
					// (js) set status to loading
					//jQuery('#hkdev_mm_toggle-wrap').after('<span style="visibility:visible; float:none;" class="spinner switch_spinner"></span>');
					
					// send ajax request
					jQuery.post( ajaxurl, data, function(response) {
						var split_response = response.split('|');
						if( split_response[0] == 'SUCCESS' ){

							if( split_response[1] != 'YES' ){
								// disabled
								jQuery(".hkdev-switch").removeClass('on');
								jQuery(".hkdev-switch span.on").removeClass('checked');
								jQuery(".hkdev-switch span.off").addClass('checked');
								jQuery(".hkdev-indicator").removeClass("hkdev-indicator--enabled");
								jQuery(".hkdev-indicator .hkdev-indicator-title").attr("title", "<?php echo _x('Maintenance OFF', 'Admin bar indicator', 'hkdev-maintenance-mode'); ?>");
							}else{
								// active
								jQuery(".hkdev-switch").addClass('on');
								jQuery(".hkdev-switch span.on").addClass('checked');
								jQuery(".hkdev-switch span.off").removeClass('checked');
								jQuery(".hkdev-indicator").addClass("hkdev-indicator--enabled");
								jQuery(".hkdev-indicator .hkdev-indicator-title").attr("title", "<?php echo _x('Maintenance ON', 'Admin bar indicator', 'hkdev-maintenance-mode'); ?>");
							} 
						}else{
							alert( '<?php _e( "There was a database error. Please reload this page", 'hkdev-maintenance-mode' ); ?>' );
						}
						//jQuery('.switch_spinner').remove();
					});
				}

				// (js) add new IP
				function hkdev_mm_add_new_ip () {
					// validate entries before posting ajax call
					var error_msg = '';
					if( jQuery('#hkdev_mm_new_ip_name').val() == '' ) error_msg += '<?php _e( "You must enter a Name", 'hkdev-maintenance-mode'); ?>.\n';
					if( jQuery('#hkdev_mm_new_ip_name').val() == '<?php _e( "Enter Name:", 'hkdev-maintenance-mode' ); ?>' ) error_msg += '<?php _e( "You must enter a Name", 'hkdev-maintenance-mode'); ?>.\n';
					if( jQuery('#hkdev_mm_new_ip_ip'  ).val() == '' ) error_msg += '<?php _e( "You must enter an IP", 'hkdev-maintenance-mode'); ?>.\n';
					if( jQuery('#hkdev_mm_new_ip_ip'  ).val() == '<?php _e( "Enter IP:", 'hkdev-maintenance-mode' ); ?>'   ) error_msg += '<?php _e( "You must enter an IP", 'hkdev-maintenance-mode'); ?>.\n';
					if( ValidateIPaddress( jQuery('#hkdev_mm_new_ip_ip'  ).val() ) != true ) error_msg += '<?php _e( "IP address not valid", 'hkdev-maintenance-mode'); ?>.\n';
					if( error_msg != '' ){
						alert( '<?php _e( "There is a problem with the information you have entered", 'hkdev-maintenance-mode' ); ?>.\n\n' + error_msg );
					}else{
						// prepare ajax data
						var data = {
							action: 'hkdev_mm_add_ip',
							security: '<?php echo $ajax_nonce; ?>',
							hkdev_mm_ip_name: jQuery('#hkdev_mm_new_ip_name').val(),
							hkdev_mm_ip_ip: jQuery('#hkdev_mm_new_ip_ip').val() 
						};
						
						// set section to loading 
						jQuery( '#hkdev_mm_ip_tbl_container' ).html('<span style="visibility:visible; float:none;" class="spinner"></span>');
						
						// send ajax request
						jQuery.post( ajaxurl, data, function(response) {
							jQuery('#hkdev_mm_ip_tbl_container').html( response );
						});
					}
				}
				
				// (js) toggle IP status
				function hkdev_mm_toggle_ip ( status, ip_id ) {
					// prepare ajax data
					var data = {
						action: 'hkdev_mm_toggle_ip',
						security: '<?php echo $ajax_nonce; ?>',
						hkdev_mm_ip_active: status,
						hkdev_mm_ip_id: ip_id 
					};
					
					// (js) set status to loading
					jQuery( '#hkdev_mm_ip_status_' + ip_id ).html('<span style="visibility:visible; float:none;" class="spinner"></span>');
					
					// send ajax request
					jQuery.post( ajaxurl, data, function(response) {
						var split_response = response.split('|');
						if( split_response[0] == 'SUCCESS' ){
							var ip_id     = split_response[1];
							var ip_active = split_response[1];
							// update divs / 1 = id / 2 = status
							if( split_response[2] == '1' ){
								// active
								jQuery('#hkdev_mm_ip_status_' + split_response[1] ).html('<span class="actived"><?php _e( "Yes", 'hkdev-maintenance-mode' ); ?></span>');
								jQuery('#hkdev_mm_ip_status_' + split_response[1] + '_action' ).html( '<a href="javascript:hkdev_mm_toggle_ip(0,' + split_response[1] + ');"><?php _e( "Disable", 'hkdev-maintenance-mode' ); ?></a> | ' );
							}else{
								// disabled
								jQuery('#hkdev_mm_ip_status_' + split_response[1] ).html('<span class="deactived"><?php _e( "No", 'hkdev-maintenance-mode' ); ?></span>');
								jQuery('#hkdev_mm_ip_status_' + split_response[1] + '_action' ).html( '<a href="javascript:hkdev_mm_toggle_ip(1,' + split_response[1] + ');"><?php _e( "Enable", 'hkdev-maintenance-mode' ); ?></a> | ' );
							} 
						}else{
							alert( '<?php _e( "There was a database error. Please reload this page", 'hkdev-maintenance-mode' ); ?>' );
						}
					});
				}
				
				// (js) delete IP
				function hkdev_mm_delete_ip ( ip_id, ip_addr ) {
					if ( confirm('<?php _e( "You are about to delete the IP address:", 'hkdev-maintenance-mode'); ?>\n\n\'' + ip_addr + '\'\n\n') ) {
						// prepare ajax data
						var data = {
							action: 'hkdev_mm_delete_ip',
							security: '<?php echo $ajax_nonce; ?>',
							hkdev_mm_ip_id: ip_id
						};
						
						// set section to loading
						jQuery( '#hkdev_mm_ip_tbl_container' ).html('<span style="visibility:visible; float:none;" class="spinner"></span>');
						
						// send ajax request
						jQuery.post( ajaxurl, data, function(response) {
							jQuery('#hkdev_mm_ip_tbl_container').html( response );
						});
					}
				}
				
				// (js) add new Access Key
				function hkdev_mm_add_new_ak () {
					// validate entries before posting ajax call
					var error_msg = '';
					if( jQuery('#hkdev_mm_new_ak_name' ).val() == '') error_msg += '<?php _e( "You must enter a Name", 'hkdev-maintenance-mode'); ?>.\n';
					if( jQuery('#hkdev_mm_new_ak_name' ).val() == '<?php _e( "Enter Name:", 'hkdev-maintenance-mode' ); ?>'  ) error_msg += '<?php _e( "You must enter a Name", 'hkdev-maintenance-mode'); ?>.\n';
					if( jQuery('#hkdev_mm_new_ak_email').val() == '') error_msg += '<?php _e( "You must enter an Email", 'hkdev-maintenance-mode'); ?>.\n';
					if( jQuery('#hkdev_mm_new_ak_email').val() == '<?php _e( "Enter Email:", 'hkdev-maintenance-mode' ); ?>' ) error_msg += '<?php _e( "You must enter an Email", 'hkdev-maintenance-mode'); ?>.\n';
					if( error_msg != '' ){
						alert( '<?php _e( "There is a problem with the information you have entered", 'hkdev-maintenance-mode'); ?>.\n\n' + error_msg );
					}else{
						// prepare ajax data
						var data = {
							action: 'hkdev_mm_add_ak',
							security: '<?php echo $ajax_nonce; ?>',
							hkdev_mm_ak_name: jQuery('#hkdev_mm_new_ak_name').val(),
							hkdev_mm_ak_email: jQuery('#hkdev_mm_new_ak_email').val() 
						};

						// set section to loading
						jQuery( '#hkdev_mm_ak_tbl_container' ).html('<span style="visibility:visible; float:none;" class="spinner"></span>');

						// send ajax request
						jQuery.post( ajaxurl, data, function(response) {
							jQuery('#hkdev_mm_ak_tbl_container').html( response );
						});
					}
				}

				// (js) toggle Access Key status
				function hkdev_mm_toggle_ak ( status, ak_id ) {
					// prepare ajax data
					var data = {
						action: 'hkdev_mm_toggle_ak',
						security: '<?php echo $ajax_nonce; ?>',
						hkdev_mm_ak_active: status,
						hkdev_mm_ak_id: ak_id 
					};

					// set status to loading
					jQuery( '#hkdev_mm_ak_status_' + ak_id ).html('<span style="visibility:visible; float:none;" class="spinner"></span>');

					// send ajax request
					jQuery.post( ajaxurl, data, function(response) {
						var split_response = response.split('|');
						if( split_response[0] == 'SUCCESS' ){
							var ak_id     = split_response[1];
							var ak_active = split_response[1];
							// update divs / 1 = id / 2 = status
							if( split_response[2] == '1' ){
								// active
								jQuery('#hkdev_mm_ak_status_' + split_response[1] ).html( '<span class="actived"><?php _e( "Yes", 'hkdev-maintenance-mode' ); ?></span>' );
								jQuery('#hkdev_mm_ak_status_' + split_response[1] + '_action' ).html( '<a href="javascript:hkdev_mm_toggle_ak(0,' + split_response[1] + ');"><?php _e( "Disable", 'hkdev-maintenance-mode' ); ?></a> | ' );
							}else{
								// disabled
								jQuery('#hkdev_mm_ak_status_' + split_response[1] ).html( '<span class="deactived"><?php _e( "No", 'hkdev-maintenance-mode' ); ?></span>' );
								jQuery('#hkdev_mm_ak_status_' + split_response[1] + '_action' ).html( '<a href="javascript:hkdev_mm_toggle_ak(1,' + split_response[1] + ');"><?php _e( "Enable", 'hkdev-maintenance-mode' ); ?></a> | ' );
							} 
						}else{
							alert( '<?php _e( "There was a database error. Please reload this page", 'hkdev-maintenance-mode' ); ?>' );
						}
					});
				}

				// (js) delete Access Key
				function hkdev_mm_delete_ak ( ak_id, ak_name ) {
					if ( confirm('<?php _e( "You are about to delete this Access Key:", 'hkdev-maintenance-mode'); ?>\n\n\'' + ak_name + '\'\n\n') ) {
						// prepare ajax data
						var data = {
							action: 'hkdev_mm_delete_ak',
							security: '<?php echo $ajax_nonce; ?>',
							hkdev_mm_ak_id:	ak_id
						};

						// set section to loading
						jQuery( '#hkdev_mm_ak_tbl_container' ).html('<span style="visibility:visible; float:none;" class="spinner"></span>');

						// send ajax request
						jQuery.post( ajaxurl, data, function(response) {
							jQuery('#hkdev_mm_ak_tbl_container').html( response );
						});
					}
				}

				// (js) Copy Access Key
				function hkdev_mm_copy_ak ( ak_id, ak_code ) {
					var savedTxt = jQuery( '#hkdev_mm_submit_copy_' + ak_id + ' a').html();
					navigator.clipboard.writeText('<?php echo addslashes( get_bloginfo('url') ); ?>?hkdev_temp_access_key=' + ak_code).then(() => {
						jQuery( '#hkdev_mm_submit_copy_' + ak_id +' a').html( '<span style="color:green"><?php _e( "Copied", 'hkdev-maintenance-mode' ); ?></span>');
						window.hkdev_sleep( 5000 ).then(() => {
							jQuery( '#hkdev_mm_submit_copy_' + ak_id +' a').html( savedTxt );
						});
					});
				}

				
				// (js) re-send Access Key
				function hkdev_mm_resend_ak ( ak_id, ak_name, ak_email ) {
					if ( confirm('<?php _e( "You are about to email an Access Key link to ", 'hkdev-maintenance-mode'); ?>' + ak_email + '\n\n') ) {
						// prepare ajax data
						var data = {
							action: 'hkdev_mm_resend_ak',
							security: '<?php echo $ajax_nonce; ?>',
							hkdev_mm_ak_id: ak_id
						};
						
						// send ajax request
						jQuery.post( ajaxurl, data, function(response) {
							if( response == 'SEND_SUCCESS' ){
								alert( '<?php _e( "Notification Sent.", 'hkdev-maintenance-mode'); ?>' );
							}else{
								alert( '<?php _e( "Notification Failure. Please check your server settings.", 'hkdev-maintenance-mode' ); ?>' );
							}
						});
					}
				}
			
			</script>
			
			<style type="text/css" media="screen">
				.hkdev_mm_admin_section { margin: 20px 0; }
				.hkdev_mm_disabled_field { color: #888; }
				.hkdev_mm_small_dim { font-size: 11px; font-weight: normal; color: #444; }
				.hkdev_mm_admin_section dt { font-weight: bold; }
				.hkdev_mm_admin_section dd { margin-left: 0; }
				.CodeMirror { border: 1px solid #ddd; }
				.submit { padding: 0; }
				.actived, .deactived { color: #ffffff; padding: 2px 10px;width: 20%; min-width: 40px; display: block; text-align: center; border-radius: 3px;}
				.actived { background-color: #4caf50; }
				.deactived { background-color: #f44336; }

				.hkdev-switch-a{
					display: inline-flex;
					border-radius: 2px;
				}
				.hkdev-switch {
					position: relative;
					display: inline-block;
					padding: 2px;
					overflow: hidden;
					border-radius: 2px;
					background-color: #d4d3d3;
					cursor:pointer
				}
				.hkdev-switch.on { background-color: #6be67c }
				.hkdev-switch span {
					visibility: hidden;
					box-sizing: border-box;
					float: left;
					display: inline-block;
					height: 100%;
					font-size: 12px;
					line-height: 20px;
					border-radius: 2px;
					-webkit-border-radius: 2px;
					font-weight: 700;
					padding: 4px 8px;
					background: #fff;
					color: #8d8d8d;
					z-index: 1
				}
				.hkdev-switch span.on {color:#6be67c}
				.hkdev-switch span.checked {visibility:visible}
				.hkdev-switch.disabled {
					cursor: default;
					background:#e6e6e6
				}
				.hkdev-switch.disabled span {
					background: #f1f1f1;
					color: #d6d6d6
				}
				.hkdev-switch input[type="checkbox"] {
					position: absolute !important;
					top: 0;
					left: 0;
					opacity: 0;
					z-index: -1
				}
				.hkdev_mm_admin_section table input[type="text"],
				.hkdev_mm_admin_section table input[type="email"] { width: 100%; padding: 0 8px; }
				.hkdev_mm_admin_section a  { cursor: pointer; }
                .hkdev_mm_admin_section .no-results { color: #888; }
			</style>
			
			<div class=wrap>
				<form method="post" action="<?php echo $_SERVER["REQUEST_URI"]; ?>" >
					<h2><?php _ex( 'Maintenance Mode', 'Settings page title', 'hkdev-maintenance-mode' ); ?></h2>
					
					<div class="notice notice-info">
						<p><?php _e( "This plugin is intended primarily for developers that need to allow clients to preview sites before being available to the general public.<br>Any logged in user with WordPress administrator privileges will be allowed to view the site regardless of the settings below.", 'hkdev-maintenance-mode' ); ?></p>
					</div>
					
					<h3><?php _e( "Enable Maintenance Mode", 'hkdev-maintenance-mode' ); ?></h3>

					<a href="javascript:hkdev_mm_toggle_maintenance_mode();" class="hkdev-switch-a">
						<div id="hkdev_mm_toggle-wrap"  class="hkdev-switch<?php if( $hkdev_mm_options['enable_mm'] == "YES" ) echo " on"; ?>">
							<span class="off<?php if( $hkdev_mm_options['enable_mm'] != "YES" ) echo " checked"; ?>">OFF</span>
							<span class="on<?php if( $hkdev_mm_options['enable_mm'] == "YES" ) echo " checked"; ?>">ON</span>
						</div>
					</a>
					
					<p></p>
					
					<div id="hkdev_main_options">

						<h3 class="nav-tab-wrapper">
							<a href="#maintenance-message" class="nav-tab nav-tab-active"><?php _ex( 'Maintenance Message', 'Tab name', 'hkdev-maintenance-mode' ); ?></a>
							<a href="#exclude-pages" class="nav-tab"><?php _ex( 'Exclude Pages', 'Tab name', 'hkdev-maintenance-mode' ); ?></a>
							<a href="#header-type" class="nav-tab"><?php _ex( 'Header Type', 'Tab name', 'hkdev-maintenance-mode' ); ?></a>
							<a href="#unrestricted-ip" class="nav-tab"><?php _ex( 'Unrestricted IP', 'Tab name', 'hkdev-maintenance-mode' ); ?></a>
							<a href="#access-keys" class="nav-tab"><?php _ex( 'Access Keys', 'Tab name', 'hkdev-maintenance-mode' ); ?></a>
						</h3>

						<div id="maintenance-message" class="hkdev_mm_admin_section">
							<h3><?php _e( "Maintenance Message", 'hkdev-maintenance-mode' ); ?></h3>
							<p class="description"><?php _e( "You have four options for how to specify what you want to show users when your site is in maintenance mode.", 'hkdev-maintenance-mode' ); ?></p>
							<p><select name="hkdev_mm_method" id="hkdev_mm_method" style="width:30%" >
								<option value="die_message" <?php if( $hkdev_mm_options['method'] == "die_message" ) echo "selected"; ?> ><?php _e( "Message - wp_die() Method", 'hkdev-maintenance-mode' ); ?></option>
								<option value="site_message" <?php if( $hkdev_mm_options['method'] == "site_message" ) echo "selected"; ?> ><?php _e( "Message - Site syle Method", 'hkdev-maintenance-mode' ); ?></option>
								<option value="html" <?php if( $hkdev_mm_options['method'] == "html" ) echo "selected"; ?> ><?php _e( "HTML", 'hkdev-maintenance-mode' ); ?></option>
								<option value="redirect" <?php if( $hkdev_mm_options['method'] == "redirect" ) echo "selected"; ?> ><?php _e( "Redirect", 'hkdev-maintenance-mode' ); ?></option>
							</select></p>

							<br>

							<div id="hkdev_method_message" style="<?php if( $hkdev_mm_options['method'] == "redirect" || $hkdev_mm_options['method'] == "html" ) echo "display:none;"; ?>" >
								<strong><?php _e( "Site Title", 'hkdev-maintenance-mode' ); ?></strong>
								<?php $hkdev_mm_maintenance_title = esc_attr($hkdev_mm_options['maintenance_title']); ?>
								<p class="description"><?php _e('Overrides default site meta title.', 'hkdev-maintenance-mode'); ?></p>
								<p style="margin-top: 0;"><input name="hkdev_mm_maintenance_title" type="text" placeholder="<?php echo get_bloginfo('name'); ?>" value="<?php echo $hkdev_mm_maintenance_title; ?>" class="regular-text"></p>

								<strong><?php _e( "Message", 'hkdev-maintenance-mode' ); ?></strong>
								<p class="description"><?php _e( "This is the message that will be displayed while your site is in maintenance mode.", 'hkdev-maintenance-mode' ); ?></p>
								<p style="margin-top: 0;">
								 	<?php wp_editor(stripslashes( $hkdev_mm_options['maintenance_message'] ), 'hkdev_mm_maintenance_message'); ?>
								</p>
							</div>

							<div id="hkdev_method_html" style="<?php if( $hkdev_mm_options['method'] != "html" ) echo "display:none;"; ?>" >
								<strong><?php _e( "HTML", 'hkdev-maintenance-mode' ); ?></strong>
								<p class="description"><?php _e( "This is the HTML that will be displayed while your site is in maintenance mode." , 'hkdev-maintenance-mode'); ?></p>
								<p style="margin-top: 0;">
									<textarea id="hkdev_mm_maintenance_html" name="hkdev_mm_maintenance_html" rows="10" style="width:100%"><?php echo stripslashes( $hkdev_mm_options['maintenance_html'] ); ?></textarea>
								</p>
							</div>
						
							<div id="hkdev_method_redirect" style="<?php if( $hkdev_mm_options['method'] != "redirect" ) echo "display:none;"; ?>" >
								<strong><?php _e( "Static Maintenance Page", 'hkdev-maintenance-mode' ); ?></strong>
								<p class="description"><?php _e( "Insert a URL to redirect to an external page or to a static HTML page loaded on your site.", 'hkdev-maintenance-mode' ); ?></p>
								<p style="margin-top: 0;"><input type="text" name="hkdev_mm_static_page" value="<?php echo $hkdev_mm_options['static_page']; ?>" id="hkdev_mm_static_page" style="width:100%"></p>
							</div>
						</div>

						<div id="exclude-pages" class="hkdev_mm_admin_section hidden" >
							<h3><?php _e( "Exclude pages", 'hkdev-maintenance-mode' ); ?></h3>
							<p><?php _e( 'Select the page(s) to be displayed normally, excluded by maintenance mode.', 'hkdev-maintenance-mode'); ?></p>
							<select name="hkdev_mm_exclude_pages[]"  id="hkdev_mm_exclude_pages" multiple="multiple" style="width:30%" >
							<?php 
							if(isset($hkdev_mm_options['exclude_pages'])){
								$options ='';
								foreach( $hkdev_mm_options['exclude_pages'] as $post_id ) {
									$title = get_the_title( $post_id );
									// if the post title is too long, truncate it and add "..." at the end
									$title = ( mb_strlen( $title ) > 50 ) ? mb_substr( $title, 0, 49 ) . '...' : $title;
									$options .=  '<option value="' . $post_id . '" selected="selected">' . $title . '</option>';
								}
								echo $options;
							}
							?>
							</select>
						</div>
						
						<div id="header-type" class="hkdev_mm_admin_section hidden" >
							<h3><?php _e( "Header Type", 'hkdev-maintenance-mode' ); ?></h3>
							<p><?php _ex( 'We can send 2 different header types.<br>When "Redirect" is selected, this setting will be ignored and <strong>307 Temporary Redirect</strong> used instead.','DO NOT TRANSLATE <strong>307 Temporary Redirect</strong> - leave exactly as is', 'hkdev-maintenance-mode'); ?></p>
							<dl>
								<dt>200 OK</dt>
								<dd><?php _e( "Best used for when the site is under development.", 'hkdev-maintenance-mode' ); ?></dd>
								<dt>503 Service Temporarily Unavailable</dt> 
								<dd><?php _e( "Best for when the site is temporarily taken offline for small amendments.", 'hkdev-maintenance-mode' ); ?><br>
								<em><?php _e( "If used for a long period of time, 503 can damage your Google ranking.", 'hkdev-maintenance-mode' ); ?></em></dd>
							</dl>

							<select name="hkdev_mm_header_type" id="hkdev_mm_header_type" style="width:30%" >
								<option value="200" <?php if( $hkdev_mm_options['method'] != "redirect"&&$hkdev_mm_options['header_type'] == "200" ) echo "selected"; ?> <?php if( $hkdev_mm_options['method'] == "redirect" ) echo "disabled"; ?>>200 OK </option>
								<option value="503" <?php if( $hkdev_mm_options['method'] != "redirect"&&$hkdev_mm_options['header_type'] == "503" ) echo "selected"; ?> <?php if( $hkdev_mm_options['method'] == "redirect" ) echo "disabled"; ?>>503 Service Temporarily Unavailable</option>
								<option value="507" <?php echo ( $hkdev_mm_options['method'] == "redirect" )? "selected":"disabled"; ?>>507 Temporary Redirect </option>
							</select>
						</div>
						
						<div id="unrestricted-ip" class="hkdev_mm_admin_section hidden">
							<h3><?php _e( "Unrestricted IP addresses", 'hkdev-maintenance-mode' ); ?><br>
							<span class="hkdev_mm_small_dim"><?php _e( "Your IP address is:", 'hkdev-maintenance-mode' ); ?>&nbsp;<a id="hkdev_set_ip"><?php echo $this->get_user_ip(); ?></a> - <?php _e( "Your Class C is:", 'hkdev-maintenance-mode' ); ?>&nbsp;<a id="hkdev_set_ip_c"><?php echo $this->get_user_class_c(); ?></a></span></h3>
							<p><?php _e( "Users with unrestricted IP addresses will bypass maintenance mode entirely. Using this option is useful to an entire office of clients to view the site without needing to jump through any extra hoops.", 'hkdev-maintenance-mode' ); ?></p> 
							<script>
								jQuery( "#hkdev_set_ip" ).click( function(){ jQuery( "#hkdev_mm_new_ip_ip" ).val( "<?php echo $this->get_user_ip(); ?>" )});
								jQuery( "#hkdev_set_ip_c" ).click( function(){ jQuery( "#hkdev_mm_new_ip_ip" ).val( "<?php echo $this->get_user_class_c(); ?>" )});
							</script>
							<div id="hkdev_mm_ip_tbl_container">
								<?php $this->print_unrestricted_ips(); ?>
							</div>
						</div>
						
						<div id="access-keys" class="hkdev_mm_admin_section hidden">
							<h3><?php _e( "Access Keys", 'hkdev-maintenance-mode' ); ?></h3>
							<p><?php _e( "You can allow users temporary access by sending them the access key. When a new key is created, a link to create the access key cookie will be emailed to the email address provided. Access can then be revoked either by disabling or deleting the key.", 'hkdev-maintenance-mode' ); ?></p>
							
							<div id="hkdev_mm_ak_tbl_container">
								<?php $this->print_access_keys(); ?>
							</div>
						</div>

					</div>
					
					<div class="submit">
						<?php wp_nonce_field( 'hkdev_nonce' ); ?>
						<input id="hkdev_submit_button" type="submit" class="button button-primary" name="update_wp_maintenance_mode_settings" value="<?php _e( 'Update Settings', 'hkdev-maintenance-mode' ); ?>" />
					</div>
					<input type="hidden" id="activeTab" name="activeTab" value="#maintenance-message">
				</form>
			</div>
				
			<?php
			//var_dump(get_option('hkdev_mm'));
		} // end function print_admin_page()

	} // end class hkdev_maintenance_mode
}