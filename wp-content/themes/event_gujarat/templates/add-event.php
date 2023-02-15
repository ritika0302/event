<?php  
/* 
Template Name: Add Event Template 
*/    

if ( !is_user_logged_in()) {
  wp_redirect(home_url()); 
  exit;
}
get_header();?>  
   
<div class="page-main">
  <div class="container">
    <?php if (is_user_logged_in()) { ?>
      <div class="event_inner">
        <h2>Add Event</h2>
        <div class="event-addForm"> 
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
            <input type="text" class="form-control" name="eventname" id="eventName" placeholder="Enter event name">
          </div>
          <div class="form-group">
            <label for="eventOrganiser">Select Organiser</label>
            
            <div class="org-label">
                <?php 
                $events = get_terms( array(
                    'taxonomy' => 'events_organisers',
                    'hide_empty' => false
                ) );
                 
                if ( !empty($events) ) :
                    
                    foreach( $events as $event ) {
                        $output.= '<label id="event_'. esc_attr( $event->term_id ) .'"><input id="event_'. esc_attr( $event->term_id ) .'" type="checkbox" name="organiser[]" value="'. esc_attr( $event->term_id ) .'"> <span>'. esc_html( $event->name ) .'</span></label>';
                    }
                    echo $output;
                endif;
              ?>
            </div>            
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addOrganiser">Click here to add new organizer</button>
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
                    <?php foreach( $constituencies as $constituencie ) {?>
                        <option value="<?php echo $constituencie->term_id;?>"><?php echo $constituencie->name;?></option>
                    <?php } ?>
                    </select>
                <?php endif;
              ?>
            </div>            
          </div>
          <div class="form-group">
            <label for="search_input">Location</label>
            <input type="text" class="form-control" name="eventlocation" id="search_input" placeholder="Type address..." />
            <input type="hidden" name="loc_lat" id="loc_lat" />
            <input type="hidden" name="loc_long" id="loc_long" />
          </div>
          <a href="javascript:void();" id="google_pin_drop">Pin Drop Location</a>
          <div id="google_pin_drop_location" style="height: 500px;"></div>
          <div class="form-group">
            <label for="datepicker">Start Date</label>
            <div class="date-input">
              <input type="text" class="form-control datepicker" name="startdate" id="datepicker" placeholder="Select Start Date" readonly='true'>
            </div>
            <div class="time-input">
              <input type="text" class="form-control timepicker" name="starttime" id="timepicker" placeholder="Select Time" readonly='true'>
            </div>
          </div>
          <div class="form-group">
            <label for="datepicker1">End Date</label>
            <div class="date-input">
              <input type="text" class="form-control datepicker1" name="enddate" id="datepicker1" placeholder="Select End Date" readonly='true'>
            </div>
            <div class="time-input">
              <input type="text" class="form-control timepicker1" name="endtime" id="timepicker1" placeholder="Select Time" readonly='true'>
            </div>
          </div>      
          <div class="form-group">
          <label for="commenttext">Comment</label>
          <textarea id="commenttext" name="comment" maxlength="100"></textarea>
          </div>
          <input type="hidden" name="event_post_add" value="event_add">    
          <button type="submit" class="btn btn-primary" id="event_btn">Submit</button>
        </form>
      </div>
      </div>


      <div class="modal fade" id="addOrganiser" tabindex="-1" role="dialog" aria-labelledby="addOrganiserTitle" aria-hidden="true">
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
      </div>
      <?php } else { ?>
        <h3 class="text-center"> Please <a href="<?php echo site_url();?>/login">Login</a></h3>
      <?php } ?>
  </div>  
</div>
<script src="https://maps.googleapis.com/maps/api/js?v=3.exp&libraries=places&key=AIzaSyApIp2M7IlMuKoYe4DfY891V5iZs51K8WM"></script>
<?php get_footer(); ?>
