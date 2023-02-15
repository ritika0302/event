<?php  
/* 
Template Name: Edit User Template 
*/    
if ( !is_user_logged_in()) {
  wp_redirect(home_url()); 
  exit;
}
get_header();
$user_id = $_GET['usrid'];
$content_user= $author_obj = get_user_by('id', $user_id);;
?> 

<div class="page-main">
  <div class="container">
    <?php if (is_user_logged_in()) { ?>
      <div class="event_inner">
        <h2>Edit User</h2>
        <a href="<?php echo home_url();?>/dashboard"><i class="fa fa-long-arrow-left" aria-hidden="true"></i> Back to Dashboard</a>
        <div class="user-editForm">
            <div class="alert alert-success update-messages" role="alert" style="display: none;">
                <div class="error-messages"></div> 
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="alert alert-danger update-error" role="alert" style="display: none;">
                <div class="error-messages"></div> 
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
            <form class="update-user" id="update_user" novalidate="novalidate">
              <div class="form-group">
                  <label class="control-label">First Name <span class="red-text">*</span></label>
                  <input class="form-control" type="text" name="first_name" id="first_name" value="<?php echo $content_user->first_name;?>" maxlength="50">
              </div>
              <div class="form-group">
                  <label class="control-label">Last Name <span class="red-text">*</span></label>
                  <input class="form-control" type="text" name="last_name" id="last_name" value="<?php echo $content_user->last_name;?>" maxlength="50">
              </div>
              <div class="form-group">
                <label class="control-label">Password <span class="red-text">*</span></label>
                <input type="password" class="form-control" name="upassword" id="upassword" maxlength="30">
                <i class="toggle-password fa fa-fw fa-eye-slash"></i>
              </div>
              <div class="form-group">
                <label class="control-label">Confirm Password <span class="red-text">*</span></label>
                <input type="password" class="form-control" name="ucpassword" id="ucpassword" maxlength="30">
                <i class="toggle-password fa fa-fw fa-eye-slash"></i>
              </div>
              <input type="hidden" name="uid" value="<?php echo $user_id;?>">
              <div class="submit-btn">
                  <input type="submit" class="update_btn" value="Update">
              </div>
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
<?php get_footer();?>