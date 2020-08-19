<!-- Footer -->

<?php 
    $fsFooter =  FashionistOptions::get( 'footer_v' );    
?>
<footer id="footer" class="<?php if($fsFooter == 2 || $fsFooter == null){ echo esc_html__('bordertop','fashionist'); } ?>">
    <div class="container">
        <div class="col-xs-12 col-lg-3">
            <div class="row">
                <?php if ( is_active_sidebar( 'footer-left' ) ) : ?>
                    <?php dynamic_sidebar('footer-left'); ?>
                <?php endif; ?>
                <div class="social">
                 

                </div>
            </div>
        </div>
        <?php if ( is_active_sidebar( 'footer-right' ) ) : ?>                   
                   
            <?php dynamic_sidebar('footer-right'); ?>
                <div><h3>Social</h3>
<a href="https://www.facebook.com/eleikotunisie/" target="_blank"><i class="fa fa-facebook" style="padding: 6px;font-size: 16px;width: 28px;text-align: center;text-decoration: none;margin: 5px 2px;background: #3B5998;color: white;border-radius: 50%;"></i></a>&nbsp;&nbsp;<a href="https://www.instagram.com/eleikotunisie/" target="_blank"><i class="fa fa-instagram" style="padding: 1px;display: inline-block;width: 28px;height: 28px;text-align: center;border-radius: 40px;color: #fff;font-size: 22px;line-height: 25px;vertical-align: middle;background: #d6249f;background: radial-gradient(circle at 30% 107%, #fdf497 0%, #fdf497 5%, #fd5949 45%,#d6249f 60%,#285AEB 90%);box-shadow: 0px 3px 10px rgba(0,0,0,.25);
"></i></a>
</div>
        <?php endif; ?>
    </div>


<script id="mcjs">!function(c,h,i,m,p){m=c.createElement(h),p=c.getElementsByTagName(h)[0],m.async=1,m.src=i,p.parentNode.insertBefore(m,p)}(document,"script","https://chimpstatic.com/mcjs-connected/js/users/fca314b4d5f4ee2a766eaf563/02c48428b96e317d52075bbcc.js");</script>
    <div class="container">
        <div class="col-xs-12 col-sm-6">
            <div class="row">
                <span class="copy">
                    <?php  
                        $copyright_text = FashionistOptions::get( 'copyright_text'); 
                        if($copyright_text != null ){
                            echo esc_html($copyright_text);
                        } else {
                            echo esc_html(''.date("Y").'| Tous les droits sont réservés','fashionist');
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