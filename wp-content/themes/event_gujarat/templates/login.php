<?php  
/* 
Template Name: Login Template 
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
            <div class="alert alert-danger login-error" role="alert" style="display: none;">
              <div class="error-messages"></div> 
              <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <form class="login-form" id="login_frm" novalidate="novalidate">
                <div class="form-group">
                    <label class="control-label">Username / Email Address <span class="red-text">*</span></label>
                    <input class="form-control" type="text" name="email_address" id="email_address" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" maxlength="50">
                </div>
                <div class="form-group">
                    <label class="control-label" for="password">Password <span class="red-text">*</span></label>
                    <input type="password" name="password" id="password" class="form-control" maxlength="16">
                    <i class="toggle-password fa fa-fw fa-eye-slash"></i>
                </div>
                <div class="submit-btn"> 
                    <input type="submit" class="login_btn" value="Submit">
                </div>
                <!-- <div class="forgot_passlink">
                   <a href="<?php //echo site_url();?>/forgot-password/">Forgot Password?</a>
               </div> -->
            </form>   
        </div>
    </div>
  </div> 
</div>
<?php get_footer(); ?>
