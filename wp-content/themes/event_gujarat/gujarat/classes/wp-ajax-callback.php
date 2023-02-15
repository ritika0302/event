<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'Anc_WP_Ajax_callback' ) ) {

	class Anc_WP_Ajax_callback {
		/**
		 * Constructor
		 *
		 * @mvc Controller
		 */
		public function __construct() {
			add_action( 'wp_ajax_filter_event', __CLASS__ . '::filter_event_callback' );
			add_action( 'wp_ajax_nopriv_filter_event', __CLASS__ . '::filter_event_callback' );

			add_action( 'wp_ajax_all_event_filters', __CLASS__ . '::all_event_filters_callback' );

			add_action( 'wp_ajax_add_event', __CLASS__ . '::add_event_callback' );

			add_action( 'wp_ajax_add_organiser', __CLASS__ . '::add_organiser_callback' );

			add_action( 'wp_ajax_add_comment', __CLASS__ . '::add_comment_callback' );

			add_action( 'wp_ajax_delete_event', __CLASS__ . '::delete_event_callback' );

			add_action( 'wp_ajax_login_user_call', __CLASS__ . '::login_user_call_callback' );
			add_action( 'wp_ajax_nopriv_login_user_call', __CLASS__ . '::login_user_call_callback' );

			add_action( 'wp_ajax_register_user_call', __CLASS__ . '::register_user_call_callback' );
			add_action( 'wp_ajax_nopriv_register_user_call', __CLASS__ . '::register_user_call_callback' );

			add_action( 'wp_ajax_update_user_call', __CLASS__ . '::update_user_call_callback' );

			add_action( 'wp_ajax_delete_user', __CLASS__ . '::delete_user_callback' );

			add_action( 'wp_ajax_delete_organiser', __CLASS__ . '::delete_organiser_callback' );

			add_action( 'wp_ajax_register_dash_user_call', __CLASS__ . '::register_dash_user_call_callback' );
			add_action( 'wp_ajax_nopriv_register_dash_user_call', __CLASS__ . '::register_dash_user_call_callback' );
		}

		/*
		* Filter event on google map callback  `
		*/		
		public static function filter_event_callback() {
			if ( ! wp_verify_nonce($_POST['nonce'], 'gujNonce' ) ) {
				$ret_data['status'] = 'error';
				$ret_data['html'] = __('Invalid Nonce');
				echo json_encode($ret_data);
				die();
			}
			$date_now = date('Y-m-d');
			$args = array(
			'post_type' => 'events',
			'post_status' => 'publish',
			'posts_per_page' => -1,
			'meta_query' => array(
	            array(
	                'key'           => 'start_date',
	                'compare'       => '>=',
	                'value'         => $date_now,
	                'type'          => 'DATETIME',
	            ),
	          ),
			);
				
			if((isset($_POST['eventname']) && $_POST['eventname'] != '')){
				$args['tax_query'] = array(
					array (
						'taxonomy' => 'events_organisers',
						'field' => 'slug',
						'terms' => $_POST['eventname'],
						'operator' => 'AND',
					),
				);
			}

			if(isset($_POST['eventconstituencies']) && $_POST['eventconstituencies'] != ''){
				$args['tax_query'] = array(
					'relation' => 'AND',
					array (
						'taxonomy' => 'events_constituencies',
						'field' => 'slug',
						'terms' => $_POST['eventconstituencies'],
						'operator' => 'AND',
					),
					array (
						'taxonomy' => 'events_organisers',
						'field' => 'slug',
						'terms' => $_POST['eventname'],
						'operator' => 'AND',
					),
				);
			}

			if(isset($_POST['eventstartdate']) && $_POST['eventstartdate'] != ''){
			    $args['meta_query'][] = array(
			        'key'       => 'start_date',
			        'value'     => date('Y-m-d', strtotime($_POST['eventstartdate'])),
			        'compare' 	=> '>=',
			        'type'      => 'date'
			      );
			   }
			   if(isset($_POST['eventenddate']) && $_POST['eventenddate'] != ''){
			      $args['meta_query'][] = array(
			        'key'       => 'end_date',
			        'value'     => date('Y-m-d', strtotime($_POST['eventenddate'])),
			        'compare' => '<=',
			        'type'        => 'date'
			      );
			   }
			$query = new WP_Query( $args );
			ob_start();
			if ( $query->have_posts() ) {
	  		 $pointerArray = [];
                while ( $query->have_posts() ) { $query->the_post();
                    $organisers = wp_get_post_terms( get_the_ID(), 'events_organisers', array( 'orderby' => 'meta_value_num', 'meta_key' => 'set_priority') );
                    $latitude_meta = get_field('latitude', get_the_ID());
                    $longitude_meta = get_field('longitude', get_the_ID());
                    $pointer_data = array(); 
                    foreach ( $organisers as $pin ) {
                        $pointer_data[] = get_field( 'pin_icon', 'events_organisers_' . $pin->term_id);     
                    } 
                    $attachment_url = $pointer_data[0];                          
                    // $attachment_url = site_url().'/wp-content/uploads/2022/08/localtion-pin.png';
                    $title_meta = get_the_title(get_the_ID());
                    $address_meta = get_field('address', get_the_ID());
                    $is_store_date = date('d-m-Y', strtotime(get_field('start_date', get_the_ID()))).' '.__('to', 'anc').' '.date('d-m-Y', strtotime(get_field('end_date', get_the_ID()))); 
					$is_store_time = date('g:i A', strtotime(get_field('start_time', get_the_ID()))).' '.__('to', 'anc').' '.date('g:i A', strtotime(get_field('end_time', get_the_ID())));
					$comment_meta = get_field('comment', get_the_ID());

                    $organiser_data = array(); 
					if((isset($_POST['eventname']) && $_POST['eventname'] != '')){
						foreach( $_POST['eventname'] as $slug ) {
						    $organiser = get_term_by( 'slug', $slug, 'events_organisers' );
						    $organiser_data[] = $organiser->name;
						}
					} else {
						foreach ( $organisers as $organiser ) {
	                        $organiser_data[] = $organiser->name;
	                    }
					}					
					$organiser_name = join( ", ", $organiser_data);

                    $constituency = wp_get_post_terms( get_the_ID(), 'events_constituencies', array( 'orderby' => 'desc') );
                    $constituency_data = array(); 
                    foreach ( $constituency as $constituenc ) {
                        $constituency_data[] = $constituenc->name;
                    }                                         
                    $constituency_name = join( ", ", $constituency_data );

                    $pointerArray[] = [$latitude_meta, $longitude_meta, $attachment_url, $title_meta, $address_meta, $is_store_date, $organiser_name, $constituency_name, $is_store_time,$comment_meta, 'id_'.get_the_ID()];
                }
	            
				$ret_data['status'] = 'success';
				$ret_data['html'] = $html;
				$ret_data['pointer_array'] = $pointerArray;
			} else { 
				$ret_data['status'] = 'error';
				$ret_data['html'] = 'Your search result match not found!';
				$ret_data['pointer_array'] = $pointerArray;
			}		
			// echo wp_send_json($ret_data);
			echo json_encode($ret_data);
			exit();
		}

		/*
		* All Events Filter callback
		*/		
		public static function all_event_filters_callback() {
			if ( ! wp_verify_nonce($_POST['nonce'], 'gujNonce' ) ) {
				$ret_data['status'] = 'error';
				$ret_data['html'] = __('Invalid Nonce');
				echo json_encode($ret_data);
				die();
			}

			$date_now = date('Y-m-d');
			$date_now_filter = date('F j, Y');
			$current_user = wp_get_current_user();            
            $roles = $current_user->roles[0];
			$args = array(
			'post_type' => 'events',
			'post_status' => 'publish',
			'posts_per_page' => -1,
			'meta_query' => array(
	            array(
	                'key'           => 'start_date',
	                'compare'       => '>=',
	                'value'         => $date_now,
	                'type'          => 'DATETIME',
	            ),
	          ),
			);
				
			if((isset($_POST['eventname']) && $_POST['eventname'] != '')){
				$args['tax_query'] = array(
					array (
						'taxonomy' => 'events_organisers',
						'field' => 'slug',
						'terms' => $_POST['eventname'],
						'operator' => 'AND',
					),
				);
			}

			if(isset($_POST['eventconstituencies']) && $_POST['eventconstituencies'] != ''){
				$args['tax_query'] = array(
					'relation' => 'AND',
					array (
						'taxonomy' => 'events_constituencies',
						'field' => 'slug',
						'terms' => $_POST['eventconstituencies'],
						'operator' => 'AND',
					),
					array (
						'taxonomy' => 'events_organisers',
						'field' => 'slug',
						'terms' => $_POST['eventname'],
						'operator' => 'AND',
					),
				);
			}

			if(isset($_POST['eventstartdate']) && $_POST['eventstartdate'] != ''){
			    $args['meta_query'][] = array(
			        'key'       => 'start_date',
			        'value'     => date('Y-m-d', strtotime($_POST['eventstartdate'])),
			        'compare' 	=> '>=',
			        'type'      => 'date'
			      );
			   }
			   if(isset($_POST['eventenddate']) && $_POST['eventenddate'] != ''){
			      $args['meta_query'][] = array(
			        'key'       => 'end_date',
			        'value'     => date('Y-m-d', strtotime($_POST['eventenddate'])),
			        'compare' => '<=',
			        'type'        => 'date'
			      );
			   }
			$query = new WP_Query( $args );
			// var_dump($query);
			// exit;
			ob_start();
			if ( $query->have_posts() ) { ?>
				<table id="example" class="display data-tables" cellspacing="0" width="100%">
                	<thead>
						<tr>
						  <th>Title</th>
						  <th>Constituencies</th>
						  <th>Organiser</th>
						  <th style="display:none"></th>
						  <th>Date</th>
						  <th>Time</th>
						  <th>Status</th>
						  <?php if($roles == 'administrator' || $roles == 'super-admin' || $roles == 'admin'){?>
						  <th>Action</th>
						  <?php } ?>
						</tr>
					</thead>
					<tfoot>
						<tr>
						  <th>Title</th>
						  <th>Constituencies</th>
						  <th>Organiser</th>
						  <th style="display:none"></th>
						  <th>Date</th>
						  <th>Time</th>
						  <th>Status</th>
						  <?php if($roles == 'administrator' || $roles == 'super-admin' || $roles == 'admin'){?>
						  <th>Action</th>
						  <?php } ?>
						</tr>
					</tfoot>
					<tbody>
	                <?php while ( $query->have_posts() ) { $query->the_post();
	                	$ev_date = date('F j, Y', strtotime(get_field('start_date', get_the_ID())));
                        if(strtotime($date_now_filter) <= strtotime($ev_date)){
                            $ev_class = 'ucomming-ev';
                            $ev_status = 'Active';
                        } else {
                            $ev_class = 'past-ev';
                            $ev_status = 'Inactive';
                        }      
                        $organisers = wp_get_post_terms( get_the_ID(), 'events_organisers', array( 'orderby' => 'meta_value_num', 'meta_key' => 'set_priority') );
                        $organiser_data = array();
                        foreach ( $organisers as $organiser ) {
                            $organiser_data[] = $organiser->name;
                        }     
                        $organiser_name = join( ", ", $organiser_data);  

                        $constituency = wp_get_post_terms( get_the_ID(), 'events_constituencies', array( 'orderby' => 'desc') );
                        $constituency_data = array(); 
                        foreach ( $constituency as $constituenc ) {
                            $constituency_data[] = $constituenc->name;
                        }                                         
                        $constituency_name = join( ", ", $constituency_data );

	                	?>
						<tr class="del_eve_<?php echo get_the_ID();?>">
						  <td><?php echo get_the_title();?></td>
						  <td><?php echo $constituency_name;?></td>
						  <td><?php echo $organiser_name;?></td>
						  <td style="display:none"><?php echo date('d-m-Y', strtotime(get_field('start_date', $media->ID)))?></td>
						  <td><?php echo date('d-m-Y', strtotime(get_field('start_date', $media->ID))).' to '.date('d-m-Y', strtotime(get_field('end_date', $media->ID)));?></td>
						  <td><?php echo date('g:i A', strtotime(get_field('start_time', $media->ID))).' to '.date('g:i A', strtotime(get_field('end_time', $media->ID)));?></td>
						  <td><?php echo $ev_status;?></td>
						  <?php if($roles == 'administrator' || $roles == 'super-admin' || $roles == 'admin'){?>
						  <td>                              
						  <a class="edit_event_btn" title="Edit" href="<?php echo site_url();?>/edit-event/?Evid=<?php echo get_the_ID();?>"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>
                          <a href="JavaScript:void(0);" title="Delete" id="event_post_delete" data-postid="<?php echo get_the_ID();?>"><i class="fa fa-trash" aria-hidden="true"></i></a>
						  <a href="JavaScript:void(0);" title="Comment" id="event_comment" data-postid="<?php echo get_the_ID();?>" data-toggle="modal" data-target="#addComment"><i class="fa fa-comments-o" aria-hidden="true"></i></a> 
						  </td>
						  <?php } ?>
						</tr>
	                <?php } ?>  
	                </tbody>
                </table>
            <?php   
				wp_reset_postdata();

				$html = ob_get_contents();
			  	ob_get_clean();

			  	$ret_data['status'] = 'success';
				$ret_data['html'] = $html;
			} else {
				$ret_data['status'] = 'error';
				$ret_data['html'] = '';
				$ret_data['message'] = __('Your search result match not found!');
			}

			echo json_encode($ret_data);
			exit();
		}

		/*
		* Add event callback
		*/		
		public static function add_event_callback() {
			if ( ! wp_verify_nonce($_POST['nonce'], 'gujNonce' ) ) {
				$ret_data['status'] = 'error';
				$ret_data['html'] = __('Invalid Nonce');
				echo json_encode($ret_data);
				die();
			}

			

			$start_date = date('Ymd', strtotime($_POST['startdate']));
			$start_time = date('H:i:s', strtotime($_POST['starttime']));

			$end_date = date('Ymd', strtotime($_POST['enddate']));
			$end_time = date('H:i:s', strtotime($_POST['endtime']));
			$comment = $_POST['comment'];
			// echo $start_date;
			// echo $end_date;

			// echo $start_time;
			// echo $end_time;
			// exit;
			
			$organiser = array_map( 'intval', $_POST['organiser'] );
			$organiser = array_unique( $organiser );

			$constituencies = array_map( 'intval', $_POST['eventconstituencies'] );
			$constituencies = array_unique( $constituencies );

			if(isset($_POST['event_post_edit']) && $_POST['event_post_edit'] == 'event_edit'){
				$args = array(
					'ID' => $_POST['event_post_id'],
					'post_type' => 'events',
					'post_title' => $_POST['eventname'],
					'tax_input' => ['events_organisers'=>$_POST['organiser'], 'events_constituencies'=>$_POST['eventconstituencies']]
				);
				$event_id = wp_update_post($args);
				if($event_id){
					$ret_data['type'] = 'update';
					$ret_data['name'] = $_POST['eventname'];
					$ret_data['message'] = 'Your event update successfully.';
				}
			} else {
				$args = array(
					'post_status' => 'publish',
					'post_title' => $_POST['eventname'],
					'post_type' => 'events',
				);
				$event_id = wp_insert_post($args);
				if($event_id){
					$ret_data['message'] = 'Your event added successfully.';
				}
				
			}			
			
			if($event_id){
				wp_set_object_terms($event_id, $organiser, 'events_organisers');
				wp_set_object_terms($event_id, $constituencies, 'events_constituencies');				
				update_post_meta($event_id,'address', $_POST['eventlocation']);
				update_post_meta($event_id,'start_date', $start_date);
				update_post_meta($event_id,'start_time', $start_time);
				update_post_meta($event_id,'end_date', $end_date);
				update_post_meta($event_id,'end_time', $end_time);
				update_post_meta($event_id,'latitude', $_POST['loc_lat']);
				update_post_meta($event_id,'longitude', $_POST['loc_long']);
				update_post_meta($event_id,'comment',$comment);

				$ret_data['status'] = 'success';
				
			} else { 
				$ret_data['status'] = 'error';
				$ret_data['message'] = 'Something went wrong!';
			}	
			
			echo json_encode($ret_data);
			exit();
		}

		/*
		* Add organiser callback
		*/		
		public static function add_organiser_callback() {
			if ( ! wp_verify_nonce($_POST['nonce'], 'gujNonce' ) ) {
				$ret_data['status'] = 'error';
				$ret_data['html'] = __('Invalid Nonce');
				echo json_encode($ret_data);
				die();
			}

			$highest_terms = get_terms( array(
			    'taxonomy' => 'events_organisers',
			    'hide_empty' => false,
			) );
			$highest_priority = [];
			foreach($highest_terms as $term){
				$highest_priority[] = get_term_meta( $term->term_id, 'set_priority', true);
			}
			$highest = max($highest_priority);
			$set_highest = $highest + 1;
			
			$cat  = get_term_by('name', $_POST['organisername'], 'events_organisers');			
			if($cat == false){
				$slug = str_replace(' ', '', $_POST['organisername']);
				$cat_slug  = get_term_by('slug', $slug, 'events_organisers');
				if($cat_slug == false){
					$cat = wp_insert_term($_POST['organisername'], 'events_organisers', array('slug' => $slug));
					$cat_id = $cat['term_id'];

					update_term_meta( $cat_id, 'set_priority', $set_highest);
					update_term_meta( $cat_id, 'pin_icon', 346);

					$ret_data['status'] = 'success';
					$ret_data['message'] = 'Organiser added successfully.';
				} else {
					$ret_data['status'] = 'error';
					$ret_data['message'] = 'Same name organiser already exist!';
				}
				
			} else {
				$cat_id = $cat->term_id ;

				$ret_data['status'] = 'error';
				$ret_data['message'] = 'Same name organiser already exist!';
			}

			echo json_encode($ret_data);
			exit();
		}

		/*
		* Comment callback
		*/	
		public static function add_comment_callback() {
			
			if ( ! wp_verify_nonce($_POST['nonce'], 'gujNonce' ) ) {
				$comment = $_POST['comment'];
				$ret_data['status'] = 'success';
			} else { 
				$ret_data['status'] = 'error';
				$ret_data['message'] = 'Something went wrong!';
			}	
			
			echo json_encode($ret_data);
			exit();

			}

		
		/*
		* Delete Event callback
		*/		
		public static function delete_event_callback() {
			if ( ! wp_verify_nonce($_POST['nonce'], 'gujNonce' ) ) {
				$ret_data['status'] = 'error';
				$ret_data['html'] = __('Invalid Nonce');
				echo json_encode($ret_data);
				die();
			}
			
			$post_id = $_POST['ev_id'];
			$event_data = wp_trash_post($post_id, false);
			// var_dump($event_data);

			if(NULL != $event_data){
				$ret_data['status'] = 'success';
				$ret_data['ids'] = $post_id;
				$ret_data['message'] = 'You event has successfully deleted.';
			} else {
				$ret_data['status'] = 'error';
				$ret_data['message'] = 'Something went wrong!!';
			}

			echo json_encode($ret_data);
			exit();
		}

		/*
		* Login user callback
		*/	
		public static function login_user_call_callback() {
			ob_start();
			if ( ! wp_verify_nonce($_POST['nonce'], 'gujNonce' ) ) {
				$ret_data['status'] = 'error';
				$ret_data['html'] = __('Invalid Nonce');
				echo json_encode($ret_data);
				die();
			}

			$email_address = $_POST['email_address'];
			$password = $_POST['password'];

			if($email_address !='' && $password != '') {            
		        $info = array();
		        $info['user_login'] = $email_address;
		        $info['user_password'] = $password;
		        $info['remember'] = true;
		        $user_signon = wp_signon( $info, false );
		        $error_string = $user_signon->get_error_message();
		        if ( !is_wp_error($user_signon) ){
		            wp_set_current_user($user_signon->ID);
		            wp_set_auth_cookie($user_signon->ID);
		            $ret_data['status'] = 'success';
		            $ret_data['message'] = 'Login is Successful';
		        } else {
		            $ret_data['status'] = 'error';
		            $ret_data['message'] = $error_string;
		        }  
		    } else {
		      $ret_data['status'] = 'error';
		      $ret_data['message'] = '<b>Something Went Wrong!</b>';
		    }
		    
		    echo json_encode($ret_data);
		    exit();
		}

		/*
		* Register user callback
		*/
		public static function register_user_call_callback() {
			ob_start();
			if ( ! wp_verify_nonce($_POST['nonce'], 'gujNonce' ) ) {
				$ret_data['status'] = 'error';
				$ret_data['html'] = __('Invalid Nonce');
				echo json_encode($ret_data);
				die();
			}

			$username = $_POST['username'];
		    $first_name = $_POST['first_name'];
		    $last_name = $_POST['last_name'];
		    $email_address = $_POST['email_address']; 
		    $password = $_POST['password'];

			$exists = email_exists($email_address);
		    if ($exists) {
		        $ret_data['status'] = 'error';
		        $ret_data['message'] = 'Email already exists!';
		    } else {  
		        if($username !='' && $first_name !='' && $last_name != '' && $email_address != '' && $password != '') {           
		            $user_id = wp_create_user($username, $password, $email_address);
		            if ($user_id) {
		                $u = new WP_User($user_id);
			            $u->remove_role('subscriber');
			            $u->add_role('event-viewer');
		                update_user_meta($user_id, 'first_name', $first_name);
		                update_user_meta($user_id, 'last_name', $last_name);

		             //    $info = array();
			            // $info['user_login'] = $email_address;
			            // $info['user_password'] = $password;
			            // $info['remember'] = true;
			            // $user_signon = wp_signon( $info, false );
			            // if ( !is_wp_error($user_signon) ){
			            //     wp_set_current_user($user_signon->ID);
			            //     wp_set_auth_cookie($user_signon->ID);
		                $ret_data['status'] = 'success';
		                $ret_data['message'] = 'Registration is Successful';
			            // } else {
			            //     $ret_data['status'] = 'error';
			            //     $ret_data['message'] = '<b>Your email does not exists!</b>';
			            // }   
		            } 
		        } else {
		          $ret_data['status'] = 'error';
		          $ret_data['message'] = '<b>Something Went Wrong!</b>';
		        }
		    } 
		    echo json_encode($ret_data);
		    exit();
		}

		/*
		* Register Dashboard user callback
		*/
		public static function register_dash_user_call_callback() {
			ob_start();
			if ( ! wp_verify_nonce($_POST['nonce'], 'gujNonce' ) ) {
				$ret_data['status'] = 'error';
				$ret_data['html'] = __('Invalid Nonce');
				echo json_encode($ret_data);
				die();
			}

			$userrole = $_POST['userrole'];
			$username = $_POST['username'];
		    $first_name = $_POST['first_name'];
		    $last_name = $_POST['last_name'];
		    $email_address = $_POST['email_address']; 
		    $password = $_POST['password'];

			$exists = email_exists($email_address);
		    if ($exists) {
		        $ret_data['status'] = 'error';
		        $ret_data['message'] = 'Email already exists!';
		    } else {  
		        if($username !='' && $first_name !='' && $last_name != '' && $email_address != '' && $password != '') {          
		            $user_id = wp_create_user($username, $password, $email_address);
		            if ($user_id) {
		                $u = new WP_User($user_id);
			            $u->remove_role('subscriber');
			            $u->add_role($userrole);
		                update_user_meta($user_id, 'first_name', $first_name);
		                update_user_meta($user_id, 'last_name', $last_name);
		                $ret_data['status'] = 'success';
		                $ret_data['message'] = 'Registration is Successful';  
		            } 
		        } else {
		          $ret_data['status'] = 'error';
		          $ret_data['message'] = '<b>Something Went Wrong!</b>';
		        }
		    } 
		    echo json_encode($ret_data);
		    exit();
		}


		/*
		* Update user callback
		*/
		public static function update_user_call_callback() {
			ob_start();
			if ( ! wp_verify_nonce($_POST['nonce'], 'gujNonce' ) ) {
				$ret_data['status'] = 'error';
				$ret_data['html'] = __('Invalid Nonce');
				echo json_encode($ret_data);
				die();
			}

		    $first_name = $_POST['first_name'];
		    $last_name = $_POST['last_name'];
		    $password = $_POST['upassword'];
 
	        if($first_name !='' && $last_name != '') {  
	        	$user_id =  $_POST['uid'];     
	            if ($user_id) {
	            	if($password){
	            		wp_set_password( $password, $user_id );
	            	}
	                update_user_meta($user_id, 'first_name', $first_name);
	                update_user_meta($user_id, 'last_name', $last_name);
	                $ret_data['status'] = 'success';
	                $ret_data['message'] = 'Update Successful';  
	            } 
	        } else {
	          $ret_data['status'] = 'error';
	          $ret_data['message'] = '<b>Something Went Wrong!</b>';
	        }
		    echo json_encode($ret_data);
		    exit();
		}


		/*
		* Delete User callback
		*/		
		public static function delete_user_callback() {
			if ( ! wp_verify_nonce($_POST['nonce'], 'gujNonce' ) ) {
				$ret_data['status'] = 'error';
				$ret_data['html'] = __('Invalid Nonce');
				echo json_encode($ret_data);
				die();
			}
			
			$user_id = $_POST['usr_id'];
			$user_data = wp_delete_user($user_id);
			// var_dump($event_data);

			if(NULL != $user_data){
				$ret_data['status'] = 'success';
				$ret_data['ids'] = $user_id;
				$ret_data['message'] = 'User Deleted Successfully!!';
			} else {
				$ret_data['status'] = 'error';
				$ret_data['message'] = 'Something went wrong!!';
			}

			echo json_encode($ret_data);
			exit();
		}

		/*
		* Delete Organiser callback
		*/		
		public static function delete_organiser_callback() {

			if ( ! wp_verify_nonce($_POST['nonce'], 'gujNonce' ) ) {
				$ret_data['status'] = 'error';
				$ret_data['html'] = __('Invalid Nonce');
				echo json_encode($ret_data);
				die();
			}
			
			if ( isset( $_POST['org_id'] ) && ! empty( $_POST['org_id'] ) ) {
		        // delete product_cat taxonomy term
		        $args = array(
				'post_type' => 'events',
				'post_status' => 'publish',
				'posts_per_page' => -1,
				'tax_query' => array(
		            array(
		                'taxonomy' => 'events_organisers',
		                'field'    => 'term_id',
		                'terms'    => array($_POST['org_id'] ),
		            ),
		          ),
				);
				$query = new WP_Query( $args );
				$count = $query->found_posts;

		        if($count > 0){
		        	$ret_data['status'] = 'error';
					$ret_data['message'] = "You can't delete organiser because he has already assign event.";
		        } else {
		        	$product_cat_id = intval( $_POST['org_id'] );
		        	wp_delete_term( $product_cat_id, 'events_organisers' );
		        	$ret_data['status'] = 'success';
		        	$ret_data['ids'] = $product_cat_id;
					$ret_data['message'] = 'Organiser Deleted Successfully!!';
		        }   
		        
		    } else {
		    	$ret_data['status'] = 'error';
				$ret_data['message'] = 'Something went wrong!!';
		    }

			echo json_encode($ret_data);
			exit();
		}

	}
	new Anc_WP_Ajax_callback();
}