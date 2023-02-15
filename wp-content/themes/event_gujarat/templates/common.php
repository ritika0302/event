<?php  
/* 
Template Name: Common Template 
*/    
get_header();?>  
   
<div class="page-main">
  <div class="container">
    <?php if ( have_posts() ) {
        the_content();
    }?> 
  </div> 
</div>
<?php get_footer(); ?>
