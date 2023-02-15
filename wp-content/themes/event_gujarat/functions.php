<?php
/**
 * Twenty Twenty functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package WordPress
 * @subpackage Twenty_Twenty
 * @since Twenty Twenty 1.0
 */

/**
 * Table of Contents:
 * Theme Support
 * Required Files
 * Register Styles
 * Register Scripts
 * Register Menus
 * Custom Logo
 * WP Body Open
 * Register Sidebars
 * Enqueue Block Editor Assets
 * Enqueue Classic Editor Styles
 * Block Editor Settings
 */

/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 *
 * @since Twenty Twenty 1.0
 */

require( 'gujarat/controller.php' );

add_action('init', 'event_export_data_callback');
function event_export_data_callback() {
    if (isset($_POST) && !empty($_POST['export_event_data'])) {
        ob_end_clean();
        header('Content-type: text/csv');
        header('Content-Disposition: attachment; filename="event_data_'.date('d-m-Y H:i:s').'.csv"');
         
        // do not cache the file
        header('Pragma: no-cache');
        header('Expires: 0');
         
        // create a file pointer connected to the output stream
        $file = fopen('php://output', 'w');
        fputcsv($file, array('Event Name', 'Organiser', 'Constituency', 'Address', 'Start Date', 'End Date', 'Time', 'Status'));

        $args = array(
            'post_type' => 'events',
            'post_status' => 'publish',
            'posts_per_page' => -1,
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
                array (
                    'taxonomy' => 'events_constituencies',
                    'field' => 'slug',
                    'terms' => $_POST['eventconstituencies'],
                    'operator' => 'AND',
                ),
            );
        }

        if(isset($_POST['eventstartdate']) && $_POST['eventstartdate'] != ''){
            $args['meta_query'][] = array(
                'key'       => 'start_date',
                'value'     => date('Y-m-d', strtotime($_POST['eventstartdate'])),
                'compare'   => '>=',
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
        if ( $query->have_posts() ) {
            while ( $query->have_posts() ) { $query->the_post();
                $event_name = get_the_title(get_the_ID());
                $organisers = wp_get_post_terms( get_the_ID(), 'events_organisers', array( 'orderby' => 'meta_value_num', 'meta_key' => 'set_priority') );
                $organiser_data = array(); 
                if($_POST['eventname']){
                    foreach( $_POST['eventname'] as $slug ) {
                        $organiser = get_term_by( 'slug', $slug, 'events_organisers' );
                        $organiser_data[] = $organiser->name;
                    }
                } else {
                    foreach ( $organisers as $organiser ) {
                        $organiser_data[] = $organiser->name;
                    }
                }                                                         
                $organiser_name = join( ", ", $organiser_data );

                $constituency = wp_get_post_terms( get_the_ID(), 'events_constituencies', array( 'orderby' => 'desc') );
                $constituency_data = array(); 
                foreach ( $constituency as $constituenc ) {
                    $constituency_data[] = $constituenc->name;
                }                                         
                $constituency_name = join( ", ", $constituency_data ); 
                
                $address = get_field('address', get_the_ID());
                $start_date = date('d-m-Y', strtotime(get_field('start_date', get_the_ID()))); 
                $end_date = date('d-m-Y', strtotime(get_field('end_date', get_the_ID())));
                $time = date('g:i A', strtotime(get_field('start_time', get_the_ID()))).' '.__('to', 'anc').' '.date('g:i A', strtotime(get_field('end_time', get_the_ID())));

                $date_now = date('F j, Y');
                $ev_date = date('F j, Y', strtotime(get_field('start_date', get_the_ID())));
                  if(strtotime($date_now) <= strtotime($ev_date)){
                      $ev_status = 'Active';
                  } else {
                      $ev_status = 'Inactive';
                  }

                $rowArray = [$event_name, $organiser_name, $constituency_name, $address, $start_date, $end_date, $time, $ev_status];
                fputcsv($file, $rowArray);
            }
        }
        exit();
    }
}