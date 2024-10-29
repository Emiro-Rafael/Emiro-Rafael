<?php
/* Template Name: warehouse */

global $is_redesign_page;
global $hide_footer;

$is_redesign_page = true;
$hide_footer = true;

if(isset($_GET['logout'])){
    unset($_COOKIE['warehouse_id']);
    unset($_GET['logout']);
}

$user = isset($_COOKIE['warehouse_id']) ? get_user_by('id', $_COOKIE['warehouse_id']) : false;

$template_path = get_stylesheet_directory_uri();

echo "<script>let template_path = '$template_path'</script>";

get_header();
?>

<?php if(!$user): ?>
    <div class="auth">
        <div class="container">
            <div class="auth__box">
                <div class="auth__title title-h3">
                    <h1>Login</h1>
                </div>
                <form class="auth__form form form--no-errors" action="<?php echo admin_url( 'admin-ajax.php' );?>" method="post">
                    <input type="hidden" name="action" value="login_warehouse">
                    <div class="form__message">
                    </div>
                    <div class="auth__form-row">
                        <div class="input input--round">
                            <input type="email" name="email" placeholder="Email Address"  required>
                        </div>
                    </div>
                    <div class="auth__form-row">
                        <div class="input input--round">
                            <input type="password" name="password" placeholder="********" data-validation="password" required>
                        </div>
                    </div>
                    <div class="auth__form-btn">
                        <button type="submit" class="btn">Login</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php else: ?>
    <?php
    $active_order = get_user_meta($user->ID, 'active_order', true);
    $active_pack_order = get_user_meta($user->ID, 'active_pack_order', true);
    ?>
    <script>
      let warehouse_active_pick_order = <?= (!empty($active_order) ? json_encode($active_order, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) : '{}') ?>;
      let warehouse_active_pack_order = <?= (!empty($active_pack_order) ? json_encode($active_pack_order, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) : '{}') ?>;
    </script>
    <div id="barcode-input" contenteditable="true" inputmode="none" style="width: 1px; height: 100vh; opacity: 0; position: fixed; top: 0"></div>
    <div class="warehouse">
        <div class="container">
            <div class="warehouse__page" data-page="main">
                <div class="warehouse__title">
                    <h1>Welcome, <?= $user->first_name ?: $user->display_name ?>!</h1>
                </div>
                <div class="warehouse__btns">
                    <button type="button" class="btn" data-page-link="pick">POWER PICK</button>
                    <button type="button" class="btn" data-page-link="pack">POWER PACK</button>
                    <button type="button" class="btn" data-page-link="utilities">UTILITIES</button>
                </div>
            </div>
            <div class="warehouse__page" data-page="pick">
                <div class="warehouse__title">
                    <h2>POWER PICK</h2>
                </div>
                
            </div>
            <div class="warehouse__page" data-page="pack">
                <div class="warehouse__title">
                    <h2>POWER PACK</h2>
                </div>
                <div class="warehouse__scanner">
                    <div class="warehouse__scanner-title">
                        <span>Scan Packing List to begin</span>
                    </div>
                    <div class="warehouse__scanner-img">
                        <img src="<?=$template_path?>/assets/images/ico-barcode.png" alt="Barcode">
                    </div>
                </div>
                <div class="warehouse__pack" style="display: none">
                    <div class="warehouse__pack-inner">
                        <div class="warehouse__pack-visual">
                            <div class="warehouse__pack-title">
                                <span>Box size:</span>
                            </div>
                            <img src="<?=$template_path?>/assets/images/ico-box-small.svg" class="warehouse__pack-img small" alt="SMALL">
                            <img src="<?=$template_path?>/assets/images/ico-box-medium.svg" class="warehouse__pack-img medium" alt="MEDIUM">
                            <img src="<?=$template_path?>/assets/images/ico-box-large.svg" class="warehouse__pack-img large" alt="LARGE">
                            <img src="<?=$template_path?>/assets/images/ico-box-xl.svg" class="warehouse__pack-img xl" alt="XL">
                            <img src="<?=$template_path?>/assets/images/ico-box-brown-lil.svg" class="warehouse__pack-img lil-brown" alt="LIL BROWN">
                            <img src="<?=$template_path?>/assets/images/ico-box-brown-big.svg" class="warehouse__pack-img big-brown" alt="BIG BROWN">
                        </div>
                        <div class="warehouse__pack-content">
                            <div class="warehouse__pick-info">
                                <p><strong>Order #${order.id}</strong></p>
                                <p>${order.name}</p>
                                <p>${order.address_1}</p>
                                <p>${order.address_2}</p>
                            </div>
                            <div class="warehouse__pack-btn">
                                <button type="button" class="btn btn--black js-pack-reprint">REPRINT SHIPPING LABEL</button>
                                <button type="button" class="btn js-pack-complete">COMPLETE ORDER</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="warehouse__page" data-page="utilities">
                <div class="warehouse__title">
                    <h2>UTILITIES</h2>
                </div>
                
            </div>
        </div>
    </div>
    
    <div style="display: none">
        <div class="popup popup--medium popup--no-close" id="warehouse-error">
            <div class="popup__inner">
                <div class="auth__title title-h1 small js-error-title">
                </div>
                <div class="warehouse__pick-name js-error-text">
                </div>
                <div class="auth__btn">
                    <button type="button" class="btn" data-fancybox-close>RETRY</button>
                </div>
            </div>
        </div>
        
        
        <div class="popup popup--small popup--no-close" id="warehouse-pick-barcode">
            <div class="popup__inner">
                <div class="auth__title title-h1 small">
                    <span>INCORRECT BARCODE</span>
                </div>
                <div class="auth__btn">
                    <button type="button" class="btn" data-fancybox-close>TRY AGAIN</button>
                </div>
            </div>
        </div>
        <div class="popup popup--small popup--no-close" id="warehouse-pick-quantity">
            <div class="popup__inner">
                <div class="auth__title title-h1 small">
                    <span>CONFIRM Quantity</span>
                </div>
                <div class="warehouse__quantity warehouse__quantity--red js-pick-quantity">
                
                </div>
                <div class="auth__btn">
                    <button type="button" class="btn" data-fancybox-close>Confirm</button>
                </div>
            </div>
        </div>
        <div class="popup popup--small popup--no-close" id="warehouse-pick-customization">
            <div class="popup__inner">
                <div class="auth__title title-h1 small">
                    <span>CONFIRM Customization</span>
                </div>
                <div class="warehouse__warning js-pick-customization">
                
                </div>
                <div class="auth__btn">
                    <button type="button" class="btn" data-fancybox-close>Confirm</button>
                </div>
            </div>
        </div>
        <div class="popup popup--small popup--no-close" id="warehouse-pick-queue">
            <div class="popup__inner">
                <div class="auth__title title-h1 small">
                    <span>READY For THE NEXT ORDER?</span>
                </div>
                <div class="auth__subtitle title-h5" style="text-decoration: underline">
                    <span>ORDERS REMAINING:</span>
                </div>
                <div class="warehouse__remaining">
                    <span class="js-pick-num">0</span>
                </div>
                
                <div class="auth__btn">
                    <button type="button" class="btn js-pick-start">GET STARTED</button>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php
get_footer();