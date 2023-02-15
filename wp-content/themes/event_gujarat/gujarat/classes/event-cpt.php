<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

if ( ! class_exists( 'Anc_Events_CPT' ) ) {
    /**
     * Handles functionality for particular custom post type
     */
    class Anc_Events_CPT {
        const POST_TYPE_NAME = 'Events';
        const POST_TYPE_SLUG = 'events';
        const POST_META_PREFIX = 'events_';

        /**
         * Constructor
         *
         * @mvc Controller
         */
        public function __construct() {
            add_action( 'init', __CLASS__ . '::create_post_type' );
            add_shortcode( 'search_events', __CLASS__ . '::render_event_locator_callback' );
            add_shortcode( 'events_latest', __CLASS__ . '::render_events_latest_callback' );
        }

        /**
         * Registers the custom post type
         *
         * @mvc Controller
         */
        public static function create_post_type() {
            if ( ! post_type_exists( self::POST_TYPE_SLUG ) ) {
                $post_type_params = self::get_post_type_params();
                register_post_type( self::POST_TYPE_SLUG, $post_type_params );
            }
        }

        /**
         * Defines the parameters for the custom post type
         *
         * @mvc Model
         *
         * @return array
         */
        private static function get_post_type_params() {
            $labels = [
                'name'               => self::POST_TYPE_NAME,
                'singular_name'      => self::POST_TYPE_NAME,
                'add_new'            => 'Add New',
                'add_new_item'       => 'Add New ' . self::POST_TYPE_NAME,
                'edit'               => 'Edit',
                'edit_item'          => 'Edit ' . self::POST_TYPE_NAME,
                'new_item'           => 'New ' . self::POST_TYPE_NAME,
                'view'               => 'View ' . self::POST_TYPE_NAME,
                'all_items'          => 'All ' . self::POST_TYPE_NAME,
                'view_item'          => 'View ' . self::POST_TYPE_NAME,
                'search_items'       => 'Search ' . self::POST_TYPE_NAME,
                'not_found'          => 'No ' . self::POST_TYPE_NAME . 'found',
                'not_found_in_trash' => 'No ' . self::POST_TYPE_NAME . 'found in Trash',
                'parent'             => 'Parent ' . self::POST_TYPE_NAME
            ];

            $post_type_params = [
                'labels'              => $labels,
                'singular_label'      => self::POST_TYPE_NAME,
                'public'              => true,
                'exclude_from_search' => false,
                'publicly_queryable'  => true,
                'show_ui'             => true,
                'show_in_menu'        => true,
                'menu_position'       => 35,
                'menu_icon'           => 'dashicons-location-alt',
                'hierarchical'        => true,
                'capability_type'     => 'post',
                'has_archive'         => false,
                'rewrite'             => [ 'slug' => 'events' ],
                'query_var'           => true,
                'supports'            => [ 'title', 'author','thumbnail','revisions' ]
            ];
            
            $labels_organisers = array(
              'name' => _x( 'Organisers', 'taxonomy general name' ),
              'singular_name' => _x(  ' Organisers', 'taxonomy singular name' ),
              'search_items' =>  __( 'Search Organisers' ),
              'all_items' => __( 'All Organisers' ),
              'parent_item' => __( 'Parent Organisers' ),
              'parent_item_colon' => __( 'Parent Organisers:' ),
              'edit_item' => __( 'Edit Organisers' ),
              'update_item' => __( 'Update Organisers' ),
              'add_new_item' => __( 'Add New Organisers' ),
              'new_item_name' => __( 'New Organisers Name' ),
              'menu_name' => __('Organisers' ),
            );
            register_taxonomy(self::POST_TYPE_SLUG.'_organisers',array(self::POST_TYPE_SLUG), array(
              'hierarchical' => true,
              'labels' => $labels_organisers,
              'show_ui' => true,
              'show_admin_column' => true,
              'query_var' => true,
              'rewrite' => array( 'slug' => self::POST_TYPE_SLUG.'_organisers' ),
            ));

            $labels_constituencies = array(
              'name' => _x( 'Constituencies', 'taxonomy general name' ),
              'singular_name' => _x(  ' Constituencies', 'taxonomy singular name' ),
              'search_items' =>  __( 'Search Constituencies' ),
              'all_items' => __( 'All Constituencies' ),
              'parent_item' => __( 'Parent Constituencies' ),
              'parent_item_colon' => __( 'Parent Constituencies:' ),
              'edit_item' => __( 'Edit Constituencies' ),
              'update_item' => __( 'Update Constituencies' ),
              'add_new_item' => __( 'Add New Constituencies' ),
              'new_item_name' => __( 'New Constituencies Name' ),
              'menu_name' => __('Constituencies' ),
            );
            register_taxonomy(self::POST_TYPE_SLUG.'_constituencies',array(self::POST_TYPE_SLUG), array(
              'hierarchical' => true,
              'labels' => $labels_constituencies,
              'show_ui' => true,
              'show_admin_column' => true,
              'query_var' => true,
              'rewrite' => array( 'slug' => self::POST_TYPE_SLUG.'_constituencies' ),
            ));

            return apply_filters( 'cat_post-type-params', $post_type_params );
        }


        /* Search event locator callback action */
        public static function render_event_locator_callback( $atts ){
          $atts = shortcode_atts( array(
                'lat' => '22.2587',
                'lng' => '71.1924',
                'height' => '510px',
                'key' => '',
            ), $atts );
          ob_start();
          $date_now = date('Y-m-d');
          $media_args = [
              'post_type'      => self::POST_TYPE_SLUG,
              'post_status'    => 'publish',
              'posts_per_page' => -1,
              'orderby'        => 'date',
              'order'          => 'DESC',
              'meta_query' => array(
                array(
                    'key'           => 'start_date',
                    'compare'       => '>=',
                    'value'         => $date_now,
                    'type'          => 'DATETIME',
                ),
              ),
          ];

          $media = new WP_Query( $media_args );?>
          <div class="event-locator">
                <div class="event-filter">
                    <h2>Find Events</h2>
                    <form id="filter-event" class="filter-event" method="POST">
                            <?php
                            $constituencies = get_terms( array(
                              'taxonomy' => 'events_constituencies',
                              'hide_empty' => false
                            ) );
                            $all_constituencies = [];
                            foreach ($constituencies as $constituencie) {
                              $all_constituencies[] = [
                                'slug' => $constituencie->slug,
                                'name' => $constituencie->name,
                              ];
                            }

                            $organisers = get_terms( array(
                              'taxonomy' => 'events_organisers',
                              'hide_empty' => false,
                              'orderby'     => 'meta_value_num',
                              'meta_key'      => 'set_priority',
                            ) );
                            $all_organisers = [];
                            foreach ($organisers as $organiser) {
                              $all_organisers[] = [
                                'slug' => $organiser->slug,
                                'name' => $organiser->name,
                              ];
                              // echo $organiser->term_id.' ';
                              $term_image = get_field( 'pin_icon', 'events_organisers_' . $organiser->term_id );
                              // var_dump($term_image);
                              // echo $term_image;
                            }
                            ?>
                            <div class="form-group multi-dropdownbox">
                                <label>Organiser</label>
                                <div class="select-boxMain">
                                    <select name="eventname[]" id="event-name" multiple="multiple">
                                            <?php foreach($all_organisers as $organiser){?>
                                            <option value="<?php echo $organiser['slug'];?>"><?php echo $organiser['name'];?></option>
                                            <?php } ?>
                                    </select>
                                </div>        
                            </div>   
                            <div class="form-group multi-dropdownbox">
                                <label>Constituencies</label>
                                <div class="select-boxMain">
                                    <select name="eventconstituencies[]" id="event-constituencies" multiple="multiple">
                                            <?php foreach($all_constituencies as $constituencie){?>
                                            <option value="<?php echo $constituencie['slug'];?>"><?php echo $constituencie['name'];?></option>
                                            <?php } ?>
                                    </select>
                                </div>
                            </div>       
                            <div class="form-group">
                                <label>Start Date</label>
                                <input type="text" class="datepicker" name="eventstartdate" id="datepicker" readonly='true'>
                            </div>
                            <div class="form-group">
                                <label>End Date</label>
                                <input type="text" class="datepicker1" name="eventenddate" id="datepicker1" readonly='true'>
                            </div>
                            <div class="form-group">
                                <a href="javascript:void(0);" id="search_event">Search</a>
                                <button id="resetFilter"> Reset</button>
                                <input type="submit" name="export_event_data" value="Download Data" class="button button-primary export-btn">
                            </div>
                        </form>
                        <div class="alert alert-danger filter-search-error" role="alert" style="display: none;">
                          <div class="error-messages"></div> 
                          <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                          </button>
                        </div>
                    </div>
                <div class="col-md-12">
                <?php            
                $locationArgs  = get_posts( array(
                    'post_type'         => self::POST_TYPE_SLUG,
                    'post_status'       => 'publish',
                    'orderby'           => 'date',
                    'order'             => 'DESC',
                    'posts_per_page'    => -1,
                    'meta_query' => array(
                      array(
                          'key'           => 'start_date',
                          'compare'       => '>=',
                          'value'         => $date_now,
                          'type'          => 'DATETIME',
                      ),
                    ),
                ));
                // print_r($locationArgs);
                $pointerArr = '[';

                if( !empty( $locationArgs ) ) {
                    foreach( $locationArgs as $locationArg ) {
                        $organisers = wp_get_post_terms( $locationArg->ID, 'events_organisers', array( 'orderby' => 'meta_value_num', 'meta_key' => 'set_priority') );
                        $latitude_meta = get_field('latitude', $locationArg->ID);
                        $longitude_meta = get_field('longitude', $locationArg->ID);
                        $pointer_data = array(); 
                        foreach ( $organisers as $pin ) {
                            $pointer_data[] = get_field( 'pin_icon', 'events_organisers_' . $pin->term_id);     
                        } 
                        $attachment_url = $pointer_data[0];                          
                        // $attachment_url = site_url().'/wp-content/uploads/2022/08/localtion-pin.png';
                        $title_meta = get_the_title($locationArg->ID);
                        $address_meta = get_field('address', $locationArg->ID);
                        $is_store_date = date('d-m-Y', strtotime(get_field('start_date', $locationArg->ID))).' '.__('to', 'anc').' '.date('d-m-Y', strtotime(get_field('end_date', $locationArg->ID))); 
                        $is_store_time = date('g:i A', strtotime(get_field('start_time', $locationArg->ID))).' '.__('to', 'anc').' '.date('g:i A', strtotime(get_field('end_time', $locationArg->ID))); 
                        $comment_meta = get_field('comment', $locationArg->ID);

                        $organiser_data = array(); 
                        foreach ( $organisers as $organiser ) {
                            $organiser_data[] = $organiser->name;
                        }                                         
                        $organiser_name = join( ", ", $organiser_data );

                        $constituency = wp_get_post_terms( $locationArg->ID, 'events_constituencies', array( 'orderby' => 'desc') );
                        $constituency_data = array(); 
                        foreach ( $constituency as $constituenc ) {
                            $constituency_data[] = $constituenc->name;
                        }                                         
                        $constituency_name = join( ", ", $constituency_data );

                        $pointerArr .= "[".$latitude_meta.",".$longitude_meta.",'".$attachment_url."','".$title_meta."','".$address_meta."','".$is_store_date."','".$organiser_name."','".$constituency_name."','".$is_store_time."','".$comment_meta."','id_".$locationArg->ID."'],";
                    }
                }
                $pointerArr .= ']';
                // print_r($pointerArr);
                ?>
                <div id="map" style="height: <?php echo $atts['height']; ?>;"></div>
                <script>
                    var InforObj = [];
                    var is_map_style = [
                        {
                            "featureType": "administrative",
                            "elementType": "labels",
                            "stylers": [
                                {
                                    "visibility": "on"
                                }
                            ]
                        },
                        {
                            "featureType": "administrative.country",
                            "elementType": "geometry.stroke",
                            "stylers": [
                                {
                                    "visibility": "off"
                                }
                            ]
                        },
                        {
                            "featureType": "administrative.province",
                            "elementType": "geometry.stroke",
                            "stylers": [
                                {
                                    "visibility": "off"
                                }
                            ]
                        },
                        {
                            "featureType": "landscape",
                            "elementType": "geometry",
                            "stylers": [
                                {
                                    "visibility": "on"
                                },
                                {
                                    "color": "#e3e3e3"
                                }
                            ]
                        },
                        {
                            "featureType": "landscape.natural",
                            "elementType": "labels",
                            "stylers": [
                                {
                                    "visibility": "off"
                                }
                            ]
                        },
                        {
                            "featureType": "poi",
                            "elementType": "all",
                            "stylers": [
                                {
                                    "visibility": "off"
                                }
                            ]
                        },
                        {
                            "featureType": "road",
                            "elementType": "all",
                            "stylers": [
                                {
                                    "color": "#cccccc"
                                }
                            ]
                        },
                        {
                            "featureType": "road",
                            "elementType": "labels",
                            "stylers": [
                                {
                                    "visibility": "off"
                                }
                            ]
                        },
                        {
                            "featureType": "transit",
                            "elementType": "labels.icon",
                            "stylers": [
                                {
                                    "visibility": "off"
                                }
                            ]
                        },
                        {
                            "featureType": "transit.line",
                            "elementType": "geometry",
                            "stylers": [
                                {
                                    "visibility": "off"
                                }
                            ]
                        },
                        {
                            "featureType": "transit.line",
                            "elementType": "labels.text",
                            "stylers": [
                                {
                                    "visibility": "off"
                                }
                            ]
                        },
                        {
                            "featureType": "transit.station.airport",
                            "elementType": "geometry",
                            "stylers": [
                                {
                                    "visibility": "off"
                                }
                            ]
                        },
                        {
                            "featureType": "transit.station.airport",
                            "elementType": "labels",
                            "stylers": [
                                {
                                    "visibility": "off"
                                }
                            ]
                        },
                        {
                            "featureType": "water",
                            "elementType": "geometry",
                            "stylers": [
                                {
                                    "color": "#FFFFFF"
                                }
                            ]
                        },
                        {
                            "featureType": "water",
                            "elementType": "labels",
                            "stylers": [
                                {
                                    "visibility": "off"
                                }
                            ]
                        }
                    ];

                    function initMap() {   
                        var mapOptions = {
                            center: {lat: <?php echo $atts['lat']; ?>, lng: <?php echo $atts['lng']; ?>},
                            zoom: 7,
                            zoomControl: true,
                            fullscreenControl: true,
                            scrollwheel: true,
                            draggable: true,
                            scaleControl: false,
                            disableDefaultUI: true,
                            keyboardShortcuts: false,
                            clickable: false,
                            minZoom: 7, 
                            // maxZoom: 8
                            styles: is_map_style
                        }
                        var mapElement = document.getElementById('map');
                        var map = new google.maps.Map(mapElement, mapOptions);
                        map.data.setStyle({
                            fillColor: 'white',
                            strokeWeight: 2,
                            strokeColor: '#448CCB',
                            fillOpacity: 1
                        });
                        // here is the magic
                        var mapCoordinates = "<?php echo get_stylesheet_directory_uri();?>/gujarat/classes/custom-post-types/guj-cordinates.json";
                        map.data.loadGeoJson(mapCoordinates);
                        setMarkers(map);
                    }

                    var points = <?php echo $pointerArr; ?>;
                    // var bounds = new google.maps.LatLngBounds();

                    // console.log(points);
                    var markersC = [];
                    function setMarkers(map) {
                     var infowindow = new google.maps.InfoWindow();
                        for (var i = 0; i < points.length; i++) {
                            var point = points[i];
                            // console.log(points);
                            var marker = new google.maps.Marker({
                                position: {lat: point[0], lng: point[1]},
                                map: map,
                                title: point[3],
                                
                                icon: {url:point[2], scaledSize: new google.maps.Size(25, 35)},
                            });
                            markersC.push(marker);
                            var contentString = '<div class="marker-infowindow"><ul>' +
                                                '<h2>'+point[3]+'</h1>'+
                                                '<li><span class="user"><i class="fa fa-user-circle-o" aria-hidden="true"></i> '+point[6]+'</span></li>'+
                                                '<li><span class="constituency"><i class="fa fa-university" aria-hidden="true"></i> <strong>Constituency:</strong> '+point[7]+'</span></li>'+
                                                '<li><span class="address"><i class="fa fa-map-marker" aria-hidden="true"></i> <strong>Event Address:</strong> '+point[4]+'</span></li>'+
                                                '<li><span class="date"><i class="fa fa-calendar-check-o" aria-hidden="true"></i> <strong>Date:</strong> '+point[5]+'</span></li>'+
                                                '<li><span class="date"><i class="fa fa-clock-o" aria-hidden="true"></i> <strong>Time:</strong> '+point[8]+'</span></li>'+
                                                '<li><span class="comment"><i class="fa fa-comment-o" aria-hidden="true"></i> <strong>Comment</strong> '+point[9]+' </span></li>'+
                                                '<li><span class="direction"><i class="fa fa-map" aria-hidden="true"></i> <a href="http://maps.google.com/maps?z=12&t=m&q='+point[0]+','+point[1]+'"target="_blank" tabindex="0">Get Directions</a></span></li></ul></div>';


                            google.maps.event.addListener(marker, 'click', (function(marker, i, contentString) {
                                return function() {
                                    infowindow.setContent(contentString);
                                    infowindow.open(map, marker);
                                }
                            })(marker, i, contentString));
                        }
                      
                        // Add a marker clusterer to manage the markers.
                        // var markerCluster = new MarkerClusterer(map, markersC,
                        //   {imagePath: 'https://developers.google.com/maps/documentation/javascript/examples/markerclusterer/m'});
                    }

                  (function($){
                    $(document).on('click','#search_event', function( e ){
                        e.preventDefault();
                        var passData = jQuery('#filter-event').serialize();
                        passData = passData+'&action=filter_event&nonce='+ajaxPar.gujNonce;
                        if (passData) {
                            $.ajax({
                                beforeSend: function(){
                                    $('.ajax-loader').css("visibility", "visible");
                                },
                                url: ajaxPar.ajaxUrl,
                                type: 'post',
                                data: passData,
                                dataType: 'JSON',
                                success: function (responce) {
                                    if ( responce.status === 'success' ) {
                                        // jQuery('.all-event-location').html( responce.html );
                                        if( typeof responce.pointer_array != 'undefined' ) {  
                                            var points = responce.pointer_array;                              
                                            var mapOptions = {
                                                center: {lat: <?php echo $atts['lat']; ?>, lng: <?php echo $atts['lng']; ?>},
                                                zoom: 7,
                                                zoomControl: true,
                                                fullscreenControl: true,
                                                scrollwheel: true,
                                                draggable: true,
                                                scaleControl: false,
                                                disableDefaultUI: true,
                                                keyboardShortcuts: false,
                                                clickable: false,
                                                minZoom: 7, 
                                                // maxZoom: 8
                                                styles: is_map_style
                                            }

                                            var mapElement = document.getElementById('map');
                                            var map = new google.maps.Map(mapElement, mapOptions);
                                            map.data.setStyle({
                                                fillColor: 'white',
                                                strokeWeight: 2,
                                                strokeColor: '#448CCB',
                                                fillOpacity: 1
                                            });
                                            // here is the magic
                                            var mapCoordinates = "<?php echo get_stylesheet_directory_uri();?>/gujarat/classes/custom-post-types/guj-cordinates.json";
                                            map.data.loadGeoJson(mapCoordinates);

                                            
                                            var markersC = [];
                                            var infowindow = new google.maps.InfoWindow();
                                            for (var i = 0; i < points.length; i++) {
                                                var point = points[i];
                                                // console.log(points);
                                                var marker = new google.maps.Marker({
                                                    position: {lat: parseFloat(point[0]), lng: parseFloat(point[1])},
                                                    map: map,
                                                    title: point[3],
                                                    
                                                    icon: {url:point[2], scaledSize: new google.maps.Size(25, 35)}
                                                });
                                                markersC.push(marker);
                                                var contentString = '<div class="marker-infowindow"><ul>' +
                                                '<h2>'+point[3]+'</h1>'+
                                                '<li><span class="user"><i class="fa fa-user-circle-o" aria-hidden="true"></i> '+point[6]+'</span></li>'+
                                                '<li><span class="constituency"><i class="fa fa-university" aria-hidden="true"></i> <strong>Constituency:</strong> '+point[7]+'</span></li>'+
                                                '<li><span class="address"><i class="fa fa-map-marker" aria-hidden="true"></i> <strong>Event Address:</strong> '+point[4]+'</span></li>'+
                                                '<li><span class="date"><i class="fa fa-calendar-check-o" aria-hidden="true"></i> <strong>Date:</strong> '+point[5]+'</span></li>'+
                                                '<li><span class="date"><i class="fa fa-clock-o" aria-hidden="true"></i> <strong>Time:</strong> '+point[8]+'</span></li>'+
                                                '<li><span class="comment"><i class="fa fa-comment-o" aria-hidden="true"></i> <strong>Comment</strong> '+point[9]+'</span></li>'+
                                                '<li><span class="direction"><i class="fa fa-map" aria-hidden="true"></i> <a href="http://maps.google.com/maps?z=12&t=m&q='+point[0]+','+point[1]+'"target="_blank" tabindex="0">Get Directions</a></span></li></ul></div>';


                                                google.maps.event.addListener(marker, 'click', (function(marker, i, contentString) {
                                                    return function() {
                                                        infowindow.setContent(contentString);
                                                        infowindow.open(map, marker);
                                                    }
                                                })(marker, i, contentString));
                                            }                                      
                                            // Add a marker clusterer to manage the markers.
                                            // var markerCluster = new MarkerClusterer(map, markersC,
                                            //   {imagePath: 'https://developers.google.com/maps/documentation/javascript/examples/markerclusterer/m'});
                                        }  
                                    } else {
                                        // jQuery('.all-event-location').html( responce.html );
                                        // alert(responce.html);
                                        $('.ajax-loader').css("visibility", "hidden");
                                        $(".filter-search-error").css("display", "block");
                                        $('.filter-search-error .error-messages').html(responce.html); 
                                        $('html, body').animate({ scrollTop: 0 }, 'slow');
                                        setTimeout(function () {
                                            $(".filter-search-error").css("display", "none");
                                        }, 5000);
                                    }
                                },
                                complete: function(){
                                    $('.ajax-loader').css("visibility", "hidden");
                                }
                            });
                        }
                    });    
                  })(jQuery);

                </script>
                <script src="https://developers.google.com/maps/documentation/javascript/examples/markerclusterer/markerclusterer.js"></script>
                <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyApIp2M7IlMuKoYe4DfY891V5iZs51K8WM&callback=initMap" defer></script>
            </div>
              </div>
          </div>
          <?php
          wp_reset_postdata();
          $html = ob_get_contents();
          ob_get_clean();
          return $html;
        }

        /* Latest events */
        public static function render_events_latest_callback (){
            ob_start();
            $date_now = date('F j, Y');
            $current_user = wp_get_current_user();            
            $roles = $current_user->roles[0];
            // if($roles == 'administrator' || $roles == 'super-admin' || $roles == 'admin'){
            //     $user_id = '';
            // } else {
            //     $user_id = $current_user->ID;
            // }
            $media_args = [
                'post_type'      => self::POST_TYPE_SLUG,
                'post_status'    => 'publish',
                'posts_per_page' => -1,
                'order'     => 'DESC',
                // 'author' => $user_id,              
            ];

            $media = new WP_Query( $media_args );?>
            <div class="events-listing">
                
                <?php
                if( $media->have_posts() ) {
                    $organisers = get_terms( array(
                      'taxonomy' => 'events_organisers',
                      'hide_empty' => false
                    ) );
                    $constituencies = get_terms( array(
                      'taxonomy' => 'events_constituencies',
                      'hide_empty' => false
                    ) );
                ?>
                <div id="toast_message" class="tost_cls"></div>
                <div class="listing-filter">
                <span><strong>Filter By: </strong></span>
                <form id="filter-all-event" class="filter-all-event" method="POST">
                <?php
                    $constituencies = get_terms( array(
                      'taxonomy' => 'events_constituencies',
                      'hide_empty' => false
                    ) );
                    $all_constituencies = [];
                    foreach ($constituencies as $constituencie) {
                      $all_constituencies[] = [
                        'slug' => $constituencie->slug,
                        'name' => $constituencie->name,
                      ];
                    }

                    $organisers = get_terms( array(
                      'taxonomy' => 'events_organisers',
                      'hide_empty' => false,
                      'orderby'     => 'meta_value_num',
                      'meta_key'      => 'set_priority',
                    ) );
                    $all_organisers = [];
                    foreach ($organisers as $organiser) {
                      $all_organisers[] = [
                        'slug' => $organiser->slug,
                        'name' => $organiser->name,
                      ];
                      // echo $organiser->term_id.' ';
                      $term_image = get_field( 'pin_icon', 'events_organisers_' . $organiser->term_id );
                      // var_dump($term_image);
                      // echo $term_image;
                    }
                    ?>
                    <div class="form-group multi-dropdownbox">
                        <label>Organiser</label>
                        <div class="select-boxMain">
                            <select name="eventname[]" id="event-name" multiple="multiple">
                                    <?php foreach($all_organisers as $organiser){?>
                                    <option value="<?php echo $organiser['slug'];?>"><?php echo $organiser['name'];?></option>
                                    <?php } ?>
                            </select>
                        </div>        
                    </div>   
                    <div class="form-group multi-dropdownbox">
                        <label>Constituencies</label>
                        <div class="select-boxMain">
                            <select name="eventconstituencies[]" id="event-constituencies" multiple="multiple">
                                    <?php foreach($all_constituencies as $constituencie){?>
                                    <option value="<?php echo $constituencie['slug'];?>"><?php echo $constituencie['name'];?></option>
                                    <?php } ?>
                            </select>
                        </div>
                    </div>       
                    <div class="form-group left-input">
                        <label>Start Date</label>
                        <input type="text" class="datepicker" name="eventstartdate" id="datepicker" readonly='true'>
                    </div>
                    <div class="form-group right-input">
                        <label>End Date</label>
                        <input type="text" class="datepicker1" name="eventenddate" id="datepicker1" readonly='true'>
                    </div>
                    <div class="form-group">
                        <a href="javascript:void(0);" id="filters_event">Filter</a>
                        <button id="resetFilter"> Reset</button>                        
                    </div>
                </form>
                <div class="alert alert-danger filter-event-error" role="alert" style="display: none;">
                  <div class="error-messages"></div> 
                  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>
                </div>
                <div class="main-filter-data">
                    <div class="alert alert-success delete-event-messages" role="alert" style="display: none;">
                      <div class="error-messages"></div> 
                      <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                      </button>
                    </div>
                    <div class="filter_table">
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
                              <?php while( $media->have_posts() ) { $media->the_post();
                                $ev_date = date('F j, Y', strtotime(get_field('start_date', get_the_ID())));
                                if(strtotime($date_now) <= strtotime($ev_date)){
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
                                  </td>
                                  <?php } ?>
                              </tr>
                              <?php } ?>
                          </tbody>
                      </table>
                    </div>                    
                </div>
                <?php } else {
                    echo "<h3 class='text-center'>You don't have any evnets.</h3>";
                } ?>
            </div>
            <?php
            wp_reset_postdata();
            $html = ob_get_contents();
            ob_get_clean();
            return $html;
        }
    } // end Anc_Events_CPT
    new Anc_Events_CPT();
}