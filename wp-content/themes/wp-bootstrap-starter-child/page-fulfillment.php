<?php
/**
 * The template for displaying all single posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package WP_Bootstrap_Starter
 */
?>
<style>
    #header, #footer, #secondary-header{
        display:none;
    }
</style>
<?php
get_header();
if($_GET['logout'])
{
    unset($_SESSION['allow_fulfill']);
}
if( empty($_SESSION['allow_fulfill']) )
{
    if( @$_GET['pack'] )
    {
    ?>
    <br/><br/>
    <form class="ajax_form" method="POST" action="<?php echo admin_url( 'admin-ajax.php' );?>"> 
        To view this protected post, enter the password below:
        <input type="password" name="pwd" value="" />
        <input type="hidden" name="action" value="allow_fulfill" />
        <button type="submit">Submit</button>
    </form>
    <?php
    }
    else 
    {
        ?>
        <div class="container">
            <form class="ajax_form register-form input-form col-6 offset-3" id="form" action="<?php echo admin_url( 'admin-ajax.php' );?>" method="POST">
                <label for="email" class="input-label visuallyHidden col-4">Email</label>
                <input class="emailInput col-6" type="text" name="email" id="email" placeholder="Email" />
                <div id="status"></div>
                <div class="seamless-divider"></div>

                <div class="input-group-wrap">
                    <label for="password" class="col-4 input-label visuallyHidden">Password</label>
                    <input class="passwordInput col-6" type="password" name="password" id="password" placeholder="Password" />  
                </div>
                        
                <input class="btn btn-secondary w-100 text-white" type="submit" value="Login" id="enabled" /> 
                <input type="hidden" name="action" value="login_picker"  />
                <p class="text-center" id="packError"></p>
            </form>
        </div>
        <?php
        exit;
    }
}
else
{
    $fulfillment = new Fulfillment();
    $orders_left = $fulfillment->getOrderCount();
    if( @$_GET['pack'] )
    {
        if( @$_GET['order_id'])
        {
            $items = $fulfillment->getNextOrder( true, $_GET['order_id'] );
        }
        else 
        {
            ?>
            <section id="primary" class="content-area mb-5">
                <div class="container fulfillment">
                    <div class="pt-5 text-center">
                        <h3>Enter an order # to start packing</h3>
                        <form class="ajax_form mx-auto mt-5" method="POST" action="<?php echo admin_url( 'admin-ajax.php' );?>" >
                            <input type="text" name="order_id" value="" />
                            <input type="hidden" name="action" value="pack_order" />
                            <button type="submit" class="btn btn-sm btn-primary">Find Order</button>
                        </form>
                    </div>
                    <p class="text-center" id="packError"></p>
                </div>


            <?php 
                foreach($fulfillment->getPrintables() as $printable_order):
            ?>
                <div class="fulfillment container">
                    <div class="row">
                        <div class="offset-4 col-4 my-1">
                            <a class="btn btn-sm btn-primary text-white" href="/fulfillment?pack=1&order_id=<?php echo $printable_order;?>"><?php echo $printable_order;?></a> - 
                            <a class="btn btn-sm btn-secondary text-white" href="/wp-content/themes/wp-bootstrap-starter-child/assets/generated_files/candybar_order_<?php echo $printable_order;?>.pdf">Invoice</a>
                        </div>
                    </div>
                </div>
            <?php 
                endforeach;
            ?>
            <div class="scanforms container">
                <h5>Unprinted Scanforms</h5>
                <?php foreach( $fulfillment->getScanforms( false ) as $scanform ) :?>
                    <form class="ajax_form row py-2" style="border-top:1px solid;border-bottom:1px solid;" method="POST" action="<?php echo admin_url( 'admin-ajax.php' );?>">
                        <div class="col-3">
                            id: <?php echo $scanform->id; ?>
                        </div>
                        <div class="col-3">
                            created: <?php echo $scanform->created_at; ?>
                        </div>
                        <div class="col-6">
                            <button class="col-12 btn btn-lg btn-secondary text-white" type="submit" id="print_scanform">
                                Print
                            </button>

                            <input type="hidden" name="action" value="print_scanform" />
                            <input type="hidden" name="id" value="<?php echo $scanform->id; ?>" />
                        </div>
                    </form>
                <?php endforeach;?>
            </div>

            <?php
            exit;
        }
    }
    else
    {
        $fulfillment->adjustPickerNumber();
        $items = $fulfillment->getNextOrder( false );

    }
    
    if( count($items['items']) > 0 )
    {
        $customer_info = $fulfillment->getCustomerInformation();
        if( empty($customer_info->zip) )
        {
            $customer_info->zip = $customer_info->zipcode;
        }
    }
    $skip_to = empty($_GET['skip']) ? 1 : $_GET['skip'] + 1;
    ?>

    <section id="primary" class="content-area mb-5">
        <div class="container fulfillment">
            <div class="row">
                <h1 class="col-6">Next Order (<em>#<?php echo $items['ids'];?></em>)</h1>
                <div class="col-3 text-right">
                    Remaining in queue: <?php echo $orders_left;?>
                </div>
                <?php if(!@$_GET['pack']):?>
                    <div class="col-3 text-right">
                        <a class="btn btn-primary px-4" href="/fulfillment?skip=<?php echo $skip_to;?>">Skip</a>
                    </div>
                <?php else:?>
                    <div class="col-3 text-right">
                        <a class="btn btn-primary px-4" href="/fulfillment?pack=1">Back</a>
                    </div>
                <?php endif;?>
            </div>
            <?php if( count($items['items']) == 0 ):?>
                <div class="row">
                    <div class="col-12">
                        No orders left to fulfill.
                    </div>
                </div>
            <?php else: ?>
                <div class="row">
                    <div class="col-4">
                        <?php $order_dates = explode(",", $items['order_dates']);
                        foreach ($order_dates as $index => $item) {
                        ?>
                        <div class="row">
                            <h6 class="col-4 p-1 pl-4">
                                <?php if($index == 0){ ?>
                                Order Date 
                                <?php } ?>
                            </h6>
                            <h6 class="col-8 p-1"><?php echo $item?></h6>
                        </div>
                        <?php } ?>
                    </div>
                </div>
                <div class="row">
                    
                    <div class="col-4 bg-gray-light p-4">
                        <h4>Customer Information</h4>
                        <div class="row">
                            <div class="col-12"><?php echo $customer_info->email;?></div>
                            <div class="col-12"><?php echo $customer_info->shipping_name;?></div>
                            <div class="col-12"><?php echo $customer_info->address_1;?></div>
                            <?php if( !empty($customer_info->address_2) ):?>
                                <div class="col-12"><?php echo $customer_info->address_2;?></div>
                            <?php endif;?>
                            <div class="col-12">
                                <?php echo $customer_info->city;?>, 
                                <?php echo $customer_info->state;?> 
                                <?php echo $customer_info->zip;?>
                            </div>
                            <div class="col-12"><?php echo $customer_info->country;?></div>
                        </div>
                    </div>
                    <div class="col-8 p-4">
                        <?php if(isset($items['customization_notes']) && !empty($items['customization_notes'])): ?>
                            <div class="row">
                                <div class="col-12">
                                    <h5>Customization Notes</h5>
                                </div>
                            </div>
                            <div class="row">
                            <?php foreach($items['customization_notes'] as $customization_note): ?>
                                    <div class="col-12">
                                        <div class="alert alert-primary" role="alert"><?php echo str_replace("\\n", '<br>', $customization_note); ?></div>
                                    </div>
                            <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                        <!--<h4>Items</h4>-->
                        <div class="form-error"></div>
                        <div class="row">
                            <div class="col-9 py-3">
                                <h5>Item Name</h5>
                            </div>
                            <div class="col-3 py-3 text-right">
                                <h5>Qty.</h5>
                            </div>
                        </div>
                        <?php 
                        $i=0;
                        $sorted_items = $fulfillment->sortItemsByCountry($items['items']);
                        $total_items_count = 0;
                        foreach($sorted_items as $post_id => $item) :?>

                            <div class="row py-3 border-top fulfillment-item-row<?php echo $i%2 ? ' even' : ' bg-gray-light odd';?>" data-postid="<?php echo $post_id;?>">
                                <input type="checkbox" class="d-none" />
                                <?php 
                                switch( get_post_type($post_id) )
                                {
                                    case 'snack':
                                        get_template_part( 'template-parts/fulfillment-item', get_post_format(), $item );
                                        $total_items_count += $item->quantity;
                                        break;
                                    case 'country':
                                    case 'collection':
                                        foreach($item as $size => $box)
                                        {
                                            get_template_part( 'template-parts/fulfillment-item', get_post_format(), $box );
                                            $total_items_count += $box->quantity;
                                        }
                                        break;
                                }
                                ?>
                            </div>

                        <?php 
                            $i++;
                        endforeach;?>
                        
                    </div>
                </div>
                
                <form class="ajax_form row" method="POST" action="<?php echo admin_url( 'admin-ajax.php' );?>">
                    <div class="col-4 p-4">
                        <?php if( @$_GET['pack'] ):?>
                            <div class="box-block" data-blockcount="0">
                                <div class="row form-group px-3">
                                    <label class="w-50" for="boxsize">Select Box Size:</label>
                                    <select class="w-50 size-select" name="boxsize[0]">
                                        
                                        <?php foreach( Fulfillment::$box_sizes as $id => $size ):?>
                                            <option value="<?php echo $id;?>"><?php echo $size['name'];?></option>
                                        <?php endforeach;?>

                                    </select>
                                </div>
                                <div class="row form-group py-4">
                                    <div class="col-6 px-3 form-group">
                                        <label class="w-25 col-form-label" for="weight_lb">Lb.</label>
                                        <input class="w-75 float-right form-control lb-select" type="number" name="weight_lb[0]" min="0" value="0" />
                                    </div>
                                    <div class="col-6 px-3 form-group">
                                        <label class="w-25 col-form-label" for="weight_oz">Oz.</label>
                                        <input class="w-75 float-right form-control oz-select" type="number" name="weight_oz[0]" min="0" max="15" value="0" />
                                    </div>
                                </div>
                                <!--
                                <div class="row form-group py-4">
                                    <div class="col-4 px-3 form-group">
                                        <label class="w-25 col-form-label" for="height">Height</label>
                                        <input class="w-75 float-right form-control ht-select" type="number" name="height[0]" min="0" value="0" />
                                    </div>
                                    <div class="col-4 px-3 form-group">
                                        <label class="w-25 col-form-label" for="width">Width</label>
                                        <input class="w-75 float-right form-control wd-select" type="number" name="width[0]" min="0" value="0" />
                                    </div>
                                    <div class="col-4 px-3 form-group">
                                        <label class="w-25 col-form-label" for="length">Length</label>
                                        <input class="w-75 float-right form-control ln-select" type="number" name="length[0]" min="0" value="0" />
                                    </div>
                                </div>
                                -->
                                <div class="row">
                                    <div class="col-12 px-3">
                                        <button type="button" data-block="0" class="d-none w-100 remove-box btn-sm btn btn-danger">Remove Above Box</button>
                                    </div>
                                </div>
                                <hr/>
                            </div>

                            <div>
                                <div class="col-12 px-3">
                                    <button type="button" id="addBox" class="d-none w-100 add-box btn-sm btn btn-success">Add Box</button>
                                </div>
                            </div>
                        <?php endif;?>
                    </div>
                    <div class="col-8 p-4">
                        <?php if( @$_GET['pack'] && empty($items['shipment_ids']) ):?>
                            
                            <button class="col-12 btn btn-lg btn-secondary text-white" type="submit" disabled id="print_label">
                                Generate Label
                            </button>
                            <input type="hidden" name="action" value="generate_shipping_label" />
                            <input type="hidden" name="number_of_items" value="<?php echo $total_items_count;?>" />
                            <input type="hidden" name="shipping_name" value="<?php echo $customer_info->shipping_name;?>" />
                            <input type="hidden" name="address_1" value="<?php echo $customer_info->address_1;?>" />
                            <input type="hidden" name="address_2" value="<?php echo $customer_info->address_2;?>" />
                            <input type="hidden" name="city" value="<?php echo $customer_info->city;?>" />
                            <input type="hidden" name="state" value="<?php echo $customer_info->state;?>" />
                            <input type="hidden" name="zip" value="<?php echo $customer_info->zip;?>" />
                            <input type="hidden" name="country" value="<?php echo $customer_info->country;?>" />
                            <input type="hidden" name="email" value="<?php echo $customer_info->email;?>" />
                            <input type="hidden" name="phone" value="<?php echo $customer_info->phone;?>" />
                            <input type="hidden" name="payment_ids" value="<?php echo $items['payment_ids'];?>" />

                            <button class="btn btn-small btn-success float-right mt-1" type="button" onclick="nextOrder(0)">Next &rarr;</button>
                        <?php elseif( @$_GET['pack'] && !empty($items['shipment_ids']) ):?>

                            <button class="col-12 btn btn-lg btn-primary text-white" type="submit">
                                Print Label
                            </button>
                            <input type="hidden" name="action" value="print_shipping_label" />
                            <input type="hidden" name="shipment_ids" value="<?php echo $items['shipment_ids'];?>" />

                            <?php
                                $labels = $fulfillment->printLabel( $items );
                                foreach($labels as $label)
                                {
                                    echo "<p class='mt-2'><a href='{$label}'>{$label}</a></p>";
                                }
                            ?>
                            <hr />

                            <button class="btn btn-small btn-success float-right mt-1" type="button" onclick="nextOrder(1)">Next &rarr;</button>
                            
                        <?php else:?>
                            <button type="submit" class="col-12 btn btn-primary">Print Invoice(s)</button>
                            <input type="hidden" name="shipping_name" value="<?php echo $customer_info->shipping_name;?>" />
                            <input type="hidden" name="address_1" value="<?php echo $customer_info->address_1;?>" />
                            <input type="hidden" name="address_2" value="<?php echo $customer_info->address_2;?>" />
                            <input type="hidden" name="city" value="<?php echo $customer_info->city;?>" />
                            <input type="hidden" name="state" value="<?php echo $customer_info->state;?>" />
                            <input type="hidden" name="zipcode" value="<?php echo $customer_info->zipcode;?>" />
                            <input type="hidden" name="country" value="<?php echo $customer_info->country;?>" />
                            <input type="hidden" name="email" value="<?php echo $customer_info->email;?>" />
                            <input type="hidden" name="phone" value="<?php echo $customer_info->phone;?>" />
                            <input type="hidden" name="payment_ids" value="<?php echo $items['payment_ids'];?>" />
                            <input type="hidden" name="order_dates" value="<?php echo $items['order_dates'];?>" />
                            <input type="hidden" name="action" value="generate_invoice" />
                        <?php endif;?>
                    </div>

                    <input type="hidden" name="ids" value="<?php echo $items['ids'];?>" />
                </form>
            <?php endif; ?>
            <?php if( !@$_GET['pack'] ): ?>
                <a href="<?php echo get_site_url();?>/wp-login.php?action=logout" class="btn btn-sm btn-danger">Logout</a>
                <!-- //get_template_part("template-parts/pickers-number", get_post_format(), $fulfillment); -->
            <?php endif; ?>
        </div>
    </section>

    <?php get_footer();?>
<?php 
}
?>