<!-- Footer -->
<?php 
    $fsFooter =  FashionistOptions::get( 'footer_v' );    
?>
<footer id="footer" class="<?php if($fsFooter == 2 || $fsFooter == null){ echo esc_html__('bordertop','fashionist'); } ?>">
    <div class="container">
        <div class="col-xs-12 col-lg-4">
            <div class="row">
                <?php if ( is_active_sidebar( 'footer-left' ) ) : ?>
                    <?php dynamic_sidebar('footer-left'); ?>
                <?php endif; ?>
                <div class="social">
                    <?php 
                        $facebook = FashionistOptions::get( 'facebook_link');
                        $twitter = FashionistOptions::get( 'twitter_link');
                        $instagram = FashionistOptions::get( 'instagram_link');
                        $pinteresr = FashionistOptions::get( 'pinterest_link');
                    ?>                  
                    <a href="<?php if($twitter != null ) { echo esc_html($twitter); } else { echo esc_html__('#','fashionist'); } ?>"><span class="icon-twitter-square"></span><span class="hidden-xs"><?php echo esc_html__('Twitter','fashionist'); ?></span></a>                    

                    <a href="<?php if($facebook != null ) { echo esc_html($facebook); } else { echo esc_html__('#','fashionist'); } ?>"><span class="icon-facebook-square"></span><span class="hidden-xs"><?php echo esc_html__('Facebook','fashionist'); ?></span></a>
                    
                    <a href="<?php if($instagram != null ) { echo esc_html($instagram); } else { echo esc_html__('#','fashionist'); } ?>"><span class="icon-instagram-square"></span><span class="hidden-xs"><?php echo esc_html__('Instagram','fashionist'); ?></span></a>
                    
                    <a href="<?php if($pinteresr != null ) { echo esc_html($pinteresr); } else { echo esc_html__('#','fashionist'); } ?>"><span class="icon-pinterest-square"></span><span class="hidden-xs"><?php echo esc_html__('Pinterest','fashionist'); ?></span></a>

                </div>
            </div>
        </div>
        <?php if ( is_active_sidebar( 'footer-right' ) ) : ?>
            <?php dynamic_sidebar('footer-right'); ?>
        <?php endif; ?>
    </div>
    <div class="container">
        <div class="col-xs-12 col-sm-6">
            <div class="row">
                <span class="copy">
                    <?php  
                        $copyright_text = FashionistOptions::get( 'copyright_text'); 
                        if($copyright_text != null ){
                            echo esc_html($copyright_text);
                        } else {
                            echo esc_html(''.date("Y").' by Fashionist | All reserved','fashionist');
                        }
                    ?>                    
                </span>
            </div>
        </div>
        
        <div class="col-xs-12 col-sm-6">
            <div class="row">
                <div class="cards">
                    <?php if ( is_active_sidebar( 'footer-cards' ) ) : ?>
                        <?php dynamic_sidebar('footer-cards'); ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</footer>