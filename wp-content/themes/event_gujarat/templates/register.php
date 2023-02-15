<?php  
/* 
Template Name: Register Template 
*/    
if (is_user_logged_in()) {
  wp_redirect(home_url()); 
  exit;
}
get_header();?>  
   
<div class="page-main">
  <div class="container">
    <div class="signup-page">
        <div class="login-main">
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
            <form class="registration-form" id="registration_frm" novalidate="novalidate">
                <div class="form-group">
                    <label class="control-label">Username <span class="red-text">*</span></label>
                    <input class="form-control" type="text" name="username" id="username" maxlength="10">
                </div>
                <div class="form-group">
                    <label class="control-label">First Name <span class="red-text">*</span></label>
                    <input class="form-control" type="text" name="first_name" id="first_name" maxlength="10">
                </div>
                <div class="form-group">
                    <label class="control-label">Last Name <span class="red-text">*</span></label>
                    <input class="form-control" type="text" name="last_name" id="last_name" maxlength="10">
                </div>
                <div class="form-group">
                  <label class="control-label">Email Address <span class="red-text">*</span></label>
                  <input class="form-control" type="email" name="email_address" id="email_address" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" maxlength="36">
                </div>
                <div class="form-group">
                  <label class="control-label">Password <span class="red-text">*</span></label>
                  <input type="password" name="password" id="password" maxlength="16">
                  <i class="toggle-password fa fa-fw fa-eye-slash"></i>
                </div>
                <div class="form-group">
                  <label class="control-label">Confirm Password <span class="red-text">*</span></label>
                  <input type="password" name="cpassword" id="cpassword" maxlength="16">
                  <i class="toggle-password fa fa-fw fa-eye-slash"></i>
                </div>
                <div class="submit-btn">
                    <input type="submit" class="reg_btn" value="Submit">
                </div>
            </form>
        </div>
    </div>
  </div>  
</div>
<?php get_footer(); ?>
