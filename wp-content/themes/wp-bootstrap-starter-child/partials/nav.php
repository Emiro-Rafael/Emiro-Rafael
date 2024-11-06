<?php global $user_data;?>
<div class="container position-relative py-xxl-1">

    <nav class="navbar navbar-expand-lg navbar-light px-0 py-lg-3 d-flex flex-column flex-lg-row justify-content-lg-start align-items-center position-lg-relative">
        <div class="nav-row-1 d-flex align-items-center justify-content-between justify-content-lg-start flex-wrap order-lg-1">
            <button class="navbar-toggler text-primary border-0 pl-0 mb-0 mr-5 h3" type="button" data-toggle="collapse" data-target="#navbarToggler" aria-controls="navbarToggler" aria-expanded="false" aria-label="Toggle navigation">
                <i class="fas fa-bars"></i>
            </button>

            <a class="navbar-brand py-0 mr-md-0 mr-lg-3 mr-xl-4 mr-xxl-5 ml-md-5 ml-lg-0 d-flex align-items-center" href="<?php echo get_bloginfo('url')?>">
                <img class="logo" src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/logo/candybar-logo.svg" alt="CandyBar logo" width="100" />
            </a>

            <!-- mobile only -->
            <div class="collapse navbar-collapse bg-white" id="navbarToggler">
                <div class="position-relative">
                    <ul class="navbar-nav mr-auto mt-2 mt-lg-0">
                    <?php if( empty($user_data) ):?>
                        <li class="nav-item border-bottom d-flex align-items-center justify-content-start h4 mb-0 pt-1 pb-3">
                            <i class="fas fa-user-circle mr-2"></i>
                            <p class="font-weight-bold text-primary mb-0">Hello, Sign In</p>
                        </li>
                    <?php else:?>
                        <li class="nav-item border-bottom d-flex align-items-center justify-content-start h4 mb-0 pt-1 pb-3">
                            <i class="fas fa-user-circle mr-2"></i>
                            <p class="font-weight-bold text-primary mb-0">Hello, <?php echo $user_data->firstname?>!</p>
                        </li>
                    <?php endif;?>
                        <li class="nav-item border-bottom">
                            <a class="nav-link" href="<?php echo get_bloginfo('url')?>">Home</a>
                        </li>

                        <li class="nav-item border-bottom">
                            <?php if(empty($user_data)) :?>
                                <a id="signInTrigger" class="nav-link" data-toggle="modal" data-target="#signinModal">Login</a>
                            <?php else:?>
                                <a class="nav-link" href="<?php echo PageModel::getAccountLink();?>/my-orders" target="_blank">Account</a>
                            <?php endif;?>
                        </li>

                        <li class="nav-item border-bottom">
                            <?php if(empty($user_data)) :?>
                                <a class="nav-link" target="_blank" href="<?php echo PageModel::getAccountLink();?>/login?redirect=<?php echo urlencode(PageModel::getAccountLink() . '/my-orders');?>">Orders</a>
                            <?php else :?>
                                <a class="nav-link" target="_blank" href="<?php echo PageModel::getAccountLink()?>/payments">Orders</a>
                            <?php endif;?>
                        </li>

                        <li class="nav-item border-bottom">
                            <a class="nav-link" href="https://www.snackcrate.com/" target="_blank">Visit SnackCrate.com</a>
                        </li>

                        <!-- <li class="nav-item border-bottom">
                            <a class="nav-link" href="/browsing-history">Your Browsing History</a>
                        </li> -->

                        <li class="nav-item border-bottom">
                            <a class="nav-link" target="_blank" href="https://www.snackcrate.com//help">Help</a>
                        </li>

                        <!-- <li class="nav-item">
                            <a class="nav-link" href="/login">Sign In</a>
                        </li> -->

                    </ul>

                    <button class="btn h2 text-white position-absolute" style="top:-11px; right: -65px; z-index: 2;" id="collapse-close" data-toggle="collapse" data-target="#navbarToggler">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

            </div> <!-- end mobile only -->

            <div class="user-icons position-lg-absolute d-xxl-flex align-items-center">
                <?php if( empty($user_data) ):?>
                    <a 
                    aria-label="user account"
                    id="userAccount"
                    href="#"
                    class="user-account mr-3 mr-lg-2 mr-xl-3 mr-xxl-4 mb-0 h5 h3-xxl d-xxl-flex align-items-center" 
                    data-toggle="modal" 
                    data-target="#signinModal"
                    >
                        <i alt="user account" class="fas fa-user mr-xxl-2"></i>
                        <p class="font-weight-semibold mb-0 d-none d-md-inline-block h6"><?php echo empty($user_data) ? 'Login' : 'Account';?></p>
                    </a>
                <?php else:?>
                    <a 
                    aria-label="user account"
                    id="userAccount" 
                    class="user-account mr-3 mr-lg-2 mr-xl-3 mr-xxl-4 mb-0 h5 h3-xxl d-xxl-flex align-items-center" 
                    href="<?php echo PageModel::getAccountLink();?>/my-orders" 
                    target="_blank">
                        <i alt="user account" class="fas fa-user mr-xxl-2"></i>
                        <p class="font-weight-semibold mb-0 d-none d-md-inline-block h6">Account</p>
                    </a>
                <?php endif;?>
                <a 
                    aria-label="user shopping cart"
                    href="<?php echo get_permalink( get_page_by_path( 'shopping-cart' ) );?>" 
                    id="userCart" 
                    class="user-cart h5 h3-xxl mb-0 position-relative"
                >
                    <i alt="user shopping cart" class="fas fa-shopping-cart"></i>
                    <?php PageModel::getCartCounter();?>
                </a>
            </div>
        </div>

        <div class="nav-row-2 w-100 order-lg-3 ml-lg-4">
            <form class="form-inline my-2 my-lg-0 position-relative">
                <input class="form-control border-0 bg-gray-light h7 w-100" id="keyword" onkeyup="fetch2()" type="search" placeholder="Search" aria-label="Search" value="<?php echo $_GET['search'] ?? '';?>">
                <button aria-label="search" id="searchSubmit" class="text-gray bg-transparent border-0 position-absolute p-0" type="submit"><i class="fas fa-search"></i></button>
            </form>
            <div id="predictive-search" class="px-3 py-1"></div>
        </div>

        <div class="nav-row-3 mt-2 mt-lg-0 order-lg-2">
            <div class="nav-row-3-wrapper d-flex align-items-center justify-content-between">
                <a class="" href="#" type="button" data-toggle="modal" data-target="#countriesMenuModal" style="-webkit-appearance: none;">Countries<span class="d-none d-md-inline-block ml-1"><i class="fas fa-caret-down"></i></span></a>
                <!-- <a class="" href="#">Deals</a> -->
                <a class="" href="<?php echo get_permalink( get_page_by_path( 'collections' ) );?>">Collections</a>
                <a class="" href="<?php echo get_permalink( get_page_by_path( 'shop-all' ) );?>">Shop All</a>
                <div class="dropdown dropdown-more d-none d-lg-block" style="margin-bottom: 1px;">
                    <a class="" href="#" id="moreDropdownMenuToggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">More<span class="d-none d-lg-inline-block ml-1"><i class="fas fa-caret-down"></i></a>

                    <div class="dropdown-menu border-0 py-xl-3" aria-labelledby="moreDropdownMenuToggle" id="moreDropdownMenu">
                        <div class="dropdown-items-wrapper d-xl-flex align-items-center justify-content-around">
                            <div class="d-flex flex-column align-items-start justify-content-center">                              
                                <?php if(empty($user_data)) :?>
                                    
                                <?php else :?>
                                    <a class="dropdown-item" target="_blank" href="<?php echo PageModel::getAccountLink()?>/my-orders">Orders</a>
                                <?php endif;?>

                                <a class="dropdown-item" href="https://www.snackcrate.com/" target="_blank">Visit SnackCrate.com</a>
                                <a class="dropdown-item" href="https://www.snackcrate.com/help" target="_blank">Help</a>
                            </div>
                            
                            <!-- <div class="d-flex flex-column align-items-start justify-content-center">
                                <a class="dropdown-item" href="https://www.snackcrate.com/" target="_blank">Visit SnackCrate.com</a>
                                <a class="dropdown-item" href="https://www.snackcrate.com/help" target="_blank">Help</a>
                            </div> -->

                            <!-- <div class="d-flex flex-column align-items-start justify-content-center">
                                <a class="dropdown-item" href="/login">Sign In</a>
                                <a class="dropdown-item" href="/browsing-history">Your Browsing History</a>
                            </div> -->
                        </div>
                    </div>

                </div>
            </div>
        </div>

    </nav>
    <?php get_template_part( 'modals/countries-menu' ); ?>
</div>