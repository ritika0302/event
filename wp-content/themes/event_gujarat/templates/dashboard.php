<?php  
/* 
Template Name: Dashboard Template 
*/    

if ( !is_user_logged_in()) {
  wp_redirect(home_url()); 
  exit;
}
get_header();?>  
   
<div class="page-main">
  <div class="container">
    <?php if (is_user_logged_in()) { 
      $current_user = wp_get_current_user();
      $user_id =  $current_user->ID;  
      $roles = $current_user->roles[0];
      ?>
      <div class="user-dashboard">
        <div class="row">
              <div class="col-md-3"> <!-- required for floating -->
                <!-- Nav tabs -->
                <ul class="nav nav-tabs tabs-left sideways">
                  <li class="active"><a href="#home-v" data-toggle="tab">Home</a></li>
                  <li><a href="#profile-v" data-toggle="tab">Profile</a></li>
                  <?php if($roles == 'administrator' || $roles == 'super-admin' || $roles == 'admin'){ ?>
                    <li><a href="#adduser-v" data-toggle="tab">Add User</a></li>
                    <li><a href="#alluser-v" data-toggle="tab">All User</a></li>
                  <?php } ?>
                  <?php if($roles == 'administrator' || $roles == 'super-admin'){ ?>
                    <li><a href="#deleteorg-v" data-toggle="tab">Delete Organiser</a></li>
                  <?php } ?>
                </ul>
              </div>

              <div class="col-md-9">
                <!-- Tab panes -->
                <div class="tab-content">
                  <div class="tab-pane active" id="home-v">
                    <h5>Welcome, <?php echo $current_user->user_firstname;?> <?php echo $current_user->user_lastname;?></h5>
                    <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.</p>
                  </div>
                  <div class="tab-pane" id="profile-v">
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
                      <?php 
                      $u    = get_userdata($current_user->ID);
                      $role = array_shift($u->roles);
                      ?>
                        <div class="form-group">
                            <label class="control-label">Your Account Role</label>
                            <input class="form-control" type="text" id="your_role" value="<?php echo $wp_roles->roles[$role]['name'];?>" maxlength="50" readonly>
                        </div>
                        <div class="form-group">
                            <label class="control-label">First Name <span class="red-text">*</span></label>
                            <input class="form-control" type="text" name="first_name" id="first_name" value="<?php echo $current_user->user_firstname;?>" maxlength="50">
                        </div>
                        <div class="form-group">
                            <label class="control-label">Last Name <span class="red-text">*</span></label>
                            <input class="form-control" type="text" name="last_name" id="last_name" value="<?php echo $current_user->user_lastname;?>" maxlength="50">
                        </div>
                        <div class="form-group">
                          <label class="control-label">Password</label>
                          <input class="form-control" type="password" name="upassword" id="upassword" maxlength="30">
                          <i class="toggle-password fa fa-fw fa-eye-slash"></i>
                        </div>
                        <div class="form-group">
                          <label class="control-label">Confirm Password</label>
                          <input class="form-control" type="password" name="ucpassword" id="ucpassword" maxlength="30">
                          <i class="toggle-password fa fa-fw fa-eye-slash"></i>
                        </div>
                        <input type="hidden" name="uid" value="<?php echo $user_id;?>">
                        <div class="submit-btn">
                            <input type="submit" class="update_btn" value="Update">
                        </div>
                    </form>
                  </div>
                  <?php if($roles == 'administrator' || $roles == 'super-admin' || $roles == 'admin'){ ?>
                  <div class="tab-pane" id="adduser-v">
                    <div class="alert alert-success reg-messages" role="alert" style="display: none;">
                      <div class="error-messages"></div> 
                      <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                      </button>
                    </div>
                    <div class="alert alert-danger reg-error" role="alert" style="display: none;">
                      <div class="error-messages"></div> 
                      <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                      </button>
                    </div>
                    <form class="registration-form" id="dash_registration_frm" novalidate="novalidate">
                        <div class="form-group">
                            <label class="control-label">User Role <span class="red-text">*</span></label>
                            <select class="form-control" name="userrole" id="userrole">
                              <option value="">--- Select Role ---</option>
                              <?php if($roles == 'administrator' || $roles == 'super-admin'){?>
                                <option value="super-admin">Super Admin</option>
                              <?php } ?>
                              <option value="admin">Admin</option>
                              <option value="event-creater">Event Creater</option>
                              <option value="event-viewer">Event Viewer</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="control-label">Username <span class="red-text">*</span></label>
                            <input class="form-control" type="text" name="username" id="username" maxlength="50">
                        </div>
                        <div class="form-group">
                            <label class="control-label">First Name <span class="red-text">*</span></label>
                            <input class="form-control" type="text" name="first_name" id="first_name" maxlength="50">
                        </div>
                        <div class="form-group">
                            <label class="control-label">Last Name <span class="red-text">*</span></label>
                            <input class="form-control" type="text" name="last_name" id="last_name" maxlength="50">
                        </div>
                        <div class="form-group">
                          <label class="control-label">Email Address <span class="red-text">*</span></label>
                          <input class="form-control" type="email" name="email_address" id="email_address" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" maxlength="50">
                        </div>
                        <div class="form-group">
                          <label class="control-label">Password <span class="red-text">*</span></label>
                          <input class="form-control" type="password" name="password" id="password" maxlength="30">
                          <i class="toggle-password fa fa-fw fa-eye-slash"></i>
                        </div>
                        <div class="form-group">
                          <label class="control-label">Confirm Password <span class="red-text">*</span></label>
                          <input class="form-control" type="password" name="cpassword" id="cpassword" maxlength="30">
                          <i class="toggle-password fa fa-fw fa-eye-slash"></i>
                        </div>
                        <div class="submit-btn">
                            <input type="submit" class="reg_btn" value="Submit">
                        </div>
                    </form>
                  </div>
                  <div class="tab-pane" id="alluser-v">
                    <div class="alert alert-success delete-user-messages" role="alert" style="display: none;">
                      <div class="error-messages"></div> 
                      <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                      </button>
                    </div>
                    <table id="example" class="data-tables" cellspacing="0" width="100%">
                      <thead>
                          <tr>
                              <th>Name</th>
                              <th>Email</th>
                              <th>Action</th>
                          </tr>
                      </thead>

                      <tfoot>
                          <tr>
                              <th>Name</th>
                              <th>Email</th>
                              <th>Action</th>
                          </tr>
                      </tfoot>

                      <tbody>
                        <?php 
                        if($roles == 'administrator' || $roles == 'super-admin'){
                          $roles_usr = ['event-viewer', 'admin'];
                        } else {
                          $roles_usr = ['event-viewer'];
                        }
                        $args = array(
                            'role__in' => $roles_usr,
                            'orderby' => 'user_nicename',
                            'order'   => 'ASC'
                        );
                        $users = get_users( $args );
                        // echo "<pre>";
                        // print_r($users);
                        ?>
                        <?php foreach ( $users as $user ) { ?>
                          <tr class="del_usr_<?php echo $user->ID;?>">                              
                              <td><?php echo esc_html( $user->first_name );?> <?php echo esc_html( $user->last_name );?></td>
                              <td><?php echo esc_html( $user->user_email );?></td>
                              <td>
                              <a class="edit_usr_btn" title="Edit" href="<?php echo home_url();?>/edit-user/?usrid=<?php echo $user->ID;?>"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>

                                <a href="JavaScript:void(0);" title="Delete" id="usr_delete" data-userid="<?php echo $user->ID;?>"><i class="fa fa-trash" aria-hidden="true"></i></a>
                              </td>                            
                          </tr>
                          <?php } ?>
                      </tbody>
                    </table>
                  </div>
                  <?php } ?>

                  <?php if($roles == 'administrator' || $roles == 'super-admin'){ ?>
                    <div class="tab-pane" id="deleteorg-v">
                    <div class="alert alert-success delete-org-messages" role="alert" style="display: none;">
                      <div class="error-messages"></div> 
                      <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                      </button>
                    </div>
                    <div class="alert alert-danger cant-delete-org-error" role="alert" style="display: none;">
                      <div class="error-messages"></div> 
                      <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                      </button>
                    </div>
                      <table id="example" class="data-tables" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Action</th>
                            </tr>
                        </thead>

                        <tfoot>
                            <tr>
                                <th>Name</th>
                                <th>Action</th>
                            </tr>
                        </tfoot>

                        <tbody>
                          <?php 
                          $organisers = get_terms( array(
                            'taxonomy' => 'events_organisers',
                            'hide_empty' => false,
                            'orderby'     => 'meta_value_num',
                            'meta_key'    => 'set_priority',
                          ) );
                          ?>
                          <?php foreach ( $organisers as $organiser ) { ?>
                            <tr class="del_ord_<?php echo $organiser->term_id;?>">                              
                                <td><?php echo $organiser->name;?></td>
                                <td>
                                  <a href="JavaScript:void(0);" title="Delete" id="org_delete" data-orgid="<?php echo $organiser->term_id;?>"><i class="fa fa-trash" aria-hidden="true"></i></a>
                                </td>                            
                            </tr>
                            <?php } ?>
                        </tbody>
                      </table>
                    </div>
                  <?php } ?>
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
