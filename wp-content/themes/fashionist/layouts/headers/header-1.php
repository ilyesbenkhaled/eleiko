<!-- Main header -->
<?php if ( function_exists( 'mltlngg_display_switcher' ) ) mltlngg_display_switcher(); ?>
<?php $logoText = FashionistOptions::get( 'logo_text' ); ?>
<header id="mainh" class="container-fluid hidden-xs hidden-sm hidden-md">
    
    <div class="logo"><a href="<?php echo site_url(); ?>">
    <?php if($logoText != null ) { echo esc_html(); } else { echo esc_html__('Fashionist','fashionist'); } ?> <img src="http://www.eleiko.tn/wp-content/themes/fashionist/logo-strong-grey.png">       
    </a></div>
    <div class="container">
        <nav id="main-nav">
            <?php wp_nav_menu( array( 'theme_location' => 'primary') ); ?>            
        </nav>
    </div>    
  
    <div id="right-menu">
                  <!-- <?php echo '<div style="line-height: 4px; top: 24px !important;ul li {
    background-image: url(http://png-5.findicons.com/files/icons/2222/gloss_basic/32/bullet_black.png);
    background-repeat: no-repeat;
    line-height: 30px;
    padding-left: 30px;
}

ul {
    margin: 50px;
}">';qtrans_generateLanguageSelectCode( 'image' );
echo '</div>';?>-->
        <div class="lang">
           
            <!--<?php qtrans_generateLanguageSelectCode( 'image' ); ?>-->
            <?php if ( is_active_sidebar( 'languages' ) ) : ?>
                <?php dynamic_sidebar('languages'); ?>
            <?php endif; ?>               
        </div>
    
        <div class="currency">
            <?php if ( is_active_sidebar( 'currency' ) ) : ?>
                <?php dynamic_sidebar('currency'); ?>
            <?php endif; ?>
        </div>

        <?php if(fashionist_checkPlugin('woocommerce/woocommerce.php') ){ ?>
            <div class="cart">
                <?php do_action('fashionist_ajax_cart','true'); ?>
            </div>
        <?php } ?>
        
        <div class="slash">/</div>
        
        <div class="search">
            <span class="icon-search search-open"></span>
            <div id="bigsearch">
                <div class="close-search"></div>
                <div class="searchform">
                    <form role="search" method="get" class="searchform" action="<?php echo esc_url(site_url()); ?>">
                        <input type="text" class="search-field" placeholder="Search for something..." value="" name="s">
                       <input type="submit" value="Search" />
                    </form>
                </div>
            </div>
        </div>
    </div>
</header>
<!-- /Main header -->

<!-- Mobile header -->
<header id="mobile-header" class="container-fluid hidden-lg">
    <span class="nav-open"><span class="icon-navicon"></span></span>
    <div>
        <div class="logo"><a href="<?php echo site_url(); ?>">
        <?php if($logoText != null ) { echo esc_html($logoText); } else { echo esc_html__('Fashionist','fashionist'); } ?>
        </a></div>
    </div>
<a href="<?php echo site_url(); ?>/checkout"><?php if(fashionist_checkPlugin('woocommerce/woocommerce.php') ){ ?>
            <div class="cart">
                <?php do_action('fashionist_ajax_cart','true'); ?>
            </div>
        <?php } ?></a>
    <nav id="mobile-nav" class="col-xs-10 col-sm-5">
        <?php wp_nav_menu( array( 'theme_location' => 'mobile') ); ?>
    </nav>
</header>
<!-- /Mobile header -->