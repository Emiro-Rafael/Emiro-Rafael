<?php
$page = new PageModel(get_the_ID());
?>
<div class="countries-menu-modal modal" id="countriesMenuModal" tabindex="-1" aria-labelledby="countriesMenuModalLabel" aria-hidden="true">
    <div class="modal-dialog" id="countriesMenuModalDialog">
        <div class="modal-content">
            
            <!-- >= tablet only -->
            <div class="countries-menu container py-4">
                <?php foreach($page->getGeographyTerms() as $continent) :?>
                    <div class="geography">
                        <h4 class="font-weight-bold text-primary"><?php echo $continent->name;?></h4>
                        <?php if($continent->count == 0 || count($page->getCountriesFromGeography($continent->slug)) == 0): ?>
                            <div>Coming Soon</div>
                        <?php else: ?>
                            <?php foreach($page->getCountriesFromGeography($continent->slug) as $country): ?>
                                <a class="country-link" href="<?php echo get_post_permalink($country->ID);?>"><?php echo $country->post_title;?></a>
                            <?php endforeach; ?>
                        <?php endif;?>
                    </div>
                <?php endforeach; ?>
            </div> <!-- end >= tablet only -->

            <!-- <= mobile-only -->
            <div class="modal-content-wrapper d-md-none">
                <div class="modal-header border-0 d-flex align-items-center justify-content-between">
                    <h4 class="modal-title font-weight-bold" id="countriesMenuModalLabel">Countries</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span class="text-dark h2" aria-hidden="true"><i class="fas fa-times"></i></span>
                    </button>
                </div>

                <ul class="countries-menu-mobile position-relative p-0" style="transform: translate3d(0%, 0px, 0px);">
                    <?php foreach($page->getGeographyTerms() as $continent) :?>
                        <li class="geography">
                            
                            <a class="btn" href="#"><?php echo $continent->name;?> <span><i class="fas fa-chevron-right"></i></span></a>

                            <ul class="sub-menu-mobile">
                                <li class="menu-back">
                                    <a href="#"><span><i class="fas fa-arrow-left"></i></span></a>
                                </li>
                                <?php if($continent->count == 0 || count($page->getCountriesFromGeography($continent->slug)) == 0) :?>
                                    <li class="country-link h5">
                                        Coming Soon
                                    </li>
                                <?php else : ?>
                                    <?php foreach($page->getCountriesFromGeography($continent->slug) as $country): ?>
                                        <li class="country-link h5">
                                            <a href="<?php echo get_post_permalink($country->ID);?>"><?php echo $country->post_title;?></a>
                                        </li>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </ul>
                        </li>
                    <?php endforeach; ?>
                </ul> <!-- end <= mobile-only -->
            </div>
        </div>
    </div>
</div>