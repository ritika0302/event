<?php  
/* 
Template Name: All Event Template 
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
        <h1>All Events</h1>
        <?php echo do_shortcode('[events_latest]');?>
      </div>
      <?php } else { ?>
         <h3 class="text-center"> Please <a href="<?php echo site_url();?>/login">Login</a></h3>
    <?php } ?>
  </div>  
</div>
<?php get_footer(); ?>
