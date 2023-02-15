<?php  
/* 
Template Name: Edit Event Template 
*/    
if ( !is_user_logged_in()) {
  wp_redirect(home_url()); 
  exit;
}
get_header();
$event_id = $_GET['Evid'];
$content_post = get_post($event_id);
?> 

<div class="page-main">
  <div class="container">
    <?php if (is_user_logged_in()) { ?>
      <div class="event_inner">
        <h2>Edit Event</h2>
        <div class="event-addForm event-edit">
          <div class="alert alert-success addevent-messages" role="alert" style="display: none;">
          <div class="error-messages"></div> 
          <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="alert alert-danger addevent-error" role="alert" style="display: none;">
          <div class="error-messages"></div> 
          <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <form id="eventFrm">
          <div class="form-group">
            <label for="eventName">Event Name</label>
            <input type="text" class="form-control" name="eventname" id="eventName" value="<?php echo $content_post->post_title;?>" placeholder="Enter event name">
          </div>
          <div class="form-group">
            <label for="eventOrganiser">Select Organiser</label>
            <!-- <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addOrganiser">Add organiser</button> -->
            <div class="org-label">
                <?php 
                $events = get_terms( array(
                    'taxonomy' => 'events_organisers',
                    'hide_empty' => false
                ) );
                                 
                if ( !empty($events) ) :
                    
                    foreach( $events as $event ) {
                      $selected = '';
                      $termssel = get_the_terms($event_id, 'events_organisers' );
                      $mainterm_id = $event->term_id;
                      foreach ( $termssel as $termt ) {
                        $selected_id = $termt->term_id;
                        if($mainterm_id == $selected_id){
                          $selected = 'checked';
                        }
                      }
                      $output.= '<label><input type="checkbox" name="organiser[]" value="'. esc_attr( $event->term_id ) .'" '.$selected.'> '. esc_html( $event->name ) .'</label>';
                    }
                    echo $output;
                endif;
              ?>
            </div>            
          </div>
          <div class="form-group">
            <label for="event-constituencies">Select Constituencies</label>
            <div class="constituencies-label">
                <?php 
                $constituencies = get_terms( array(
                    'taxonomy' => 'events_constituencies',
                    'hide_empty' => false
                ) );
                 
                if ( !empty($constituencies) ) :?>
                  <select name="eventconstituencies[]" id="event-constituencies">  
                    <option value="">--- Select constituencies ---</option>                  
                    <?php foreach( $constituencies as $constituencie ) {
                      $selected = '';
                      $termssel = get_the_terms($event_id, 'events_constituencies' );
                      $mainterm_id = $constituencie->term_id;
                      foreach ( $termssel as $termt ) {
                        $selected_id = $termt->term_id;
                        if($mainterm_id == $selected_id){
                          $selected = 'selected';
                        }
                      }
                      ?>
                        <option value="<?php echo $constituencie->term_id;?>" <?php echo $selected;?>><?php echo $constituencie->name;?></option>
                    <?php } ?>
                    </select>
                <?php endif;
              ?>
            </div>            
          </div>
          <div class="form-group">
            <label for="search_input">Location</label>
            <input type="text" class="form-control" name="eventlocation" id="search_input" value="<?php echo get_field('address', $event_id);?>" placeholder="Type address..." />
            <input type="hidden" name="loc_lat" value="<?php echo get_field('latitude', $event_id);?>" id="loc_lat" />
            <input type="hidden" name="loc_long" value="<?php echo get_field('longitude', $event_id);?>" id="loc_long" />
          </div>
          <div id="google_pin_drop_location" style="height: 500px;"></div>
          <div class="form-group">
            <label for="datepicker">Start Date</label>
            <div class="date-input">
              <input type="text" class="form-control datepicker" name="startdate" id="datepicker" value="<?php echo date('d-m-Y', strtotime(get_field('start_date', $event_id)));?>" placeholder="Select Start Date" readonly='true'>
            </div>
            <div class="time-input">
              <input type="text" class="form-control timepicker" name="starttime" id="timepicker" value="<?php echo date('g:i A', strtotime(get_field('start_time', $event_id)));?>" placeholder="Select Time" readonly='true'>
            </div>
          </div>
          <div class="form-group">
            <label for="datepicker1">End Date</label>
            <div class="date-input">
              <input type="text" class="form-control datepicker1" name="enddate" id="datepicker1" value="<?php echo date('d-m-Y', strtotime(get_field('end_date', $event_id)));?>" placeholder="Select End Date" readonly='true'>
            </div>
            <div class="time-input">
              <input type="text" class="form-control timepicker1" name="endtime" id="timepicker1" value="<?php echo date('g:i A', strtotime(get_field('end_time', $event_id)));?>" placeholder="Select Time" readonly='true'>
            </div>
          </div> 
          <div class="form-group">
          <label for="commenttext">Comment</label>
          <textarea id="commenttext" name="comment" maxlength="100"><?php echo get_field('comment', $event_id);?></textarea>
          </div>
          <input type="hidden" name="event_post_id" value="<?php echo $event_id;?>">
          <input type="hidden" name="event_post_edit" value="event_edit">        
          <button type="submit" class="btn btn-primary" id="event_btn">Update</button>
        </form>
        </div>
      </div>


      <!-- <div class="modal fade" id="addOrganiser" tabindex="-1" role="dialog" aria-labelledby="addOrganiserTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="addOrganiserTitle">Add Organiser</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
                <div class="alert alert-success addorg-messages" role="alert" style="display: none;">
                  <div class="error-messages"></div> 
                  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>
                <div class="alert alert-danger addorg-error" role="alert" style="display: none;">
                  <div class="error-messages"></div> 
                  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>
                <form id="addOrg">
                  <label for="organisername">Organiser Name</label>
                  <input type="text" class="form-control" name="organisername" id="organisername" placeholder="Enter organiser name">

                  <button type="submit" class="btn btn-primary" id="org_btn">Submit</button>
                </form>
            </div>
          </div>
        </div>
      </div> -->
      <?php } else { ?>
        <h3 class="text-center"> Please <a href="<?php echo site_url();?>/login">Login</a></h3>
      <?php } ?>
  </div>  
</div>
<script src="https://maps.googleapis.com/maps/api/js?v=3.exp&libraries=places&key=AIzaSyApIp2M7IlMuKoYe4DfY891V5iZs51K8WM"></script>
<script type="text/javascript">
  var latLng = new google.maps.LatLng(<?php echo get_field('latitude', $event_id);?>,<?php echo get_field('longitude', $event_id);?>)
  var mapOptions = {
      center: latLng,
      zoom: 14,
      zoomControl: true,
      fullscreenControl: true,
      scrollwheel: true,
      draggable: true,
      scaleControl: false,
      disableDefaultUI: true,
      keyboardShortcuts: false,
      clickable: false,
      minZoom: 7, 
  }
  var mapElement = document.getElementById('google_pin_drop_location');
  var map = new google.maps.Map(mapElement, mapOptions);
  marker = new google.maps.Marker({
      position: latLng,
      map,
      draggable:true,
  });
  marker.setMap(map);
  map.setZoom(16);
  map.setCenter(marker.getPosition());
</script>
<?php get_footer();?>