<!-- Main header -->
<?php $logoText = FashionistOptions::get( 'logo_text' ); ?>
<header id="mainh" class="container-fluid hidden-xs hidden-sm hidden-md">
    <div class="logo"><a href="<?php echo site_url(); ?>">
    <?php if($logoText != null ) { echo esc_html($logoText); } else { echo esc_html__('Fashionist','fashionist'); } ?>        
    </a></div>
    <div class="container">
        <nav id="main-nav">
            <?php wp_nav_menu( array( 'theme_location' => 'primary') ); ?>            
        </nav>
    </div>    
  
    <div id="right-menu">
        <div class="lang">
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
    
    <nav id="mobile-nav" class="col-xs-10 col-sm-5">
        <?php wp_nav_menu( array( 'theme_location' => 'mobile') ); ?>
    </nav>
</header>
<!-- /Mobile header -->