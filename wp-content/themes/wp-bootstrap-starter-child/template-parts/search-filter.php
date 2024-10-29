<?php
/**
 * Template part for sidebar that filters items on the page
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package WP_Bootstrap_Starter
 */
$data = new shopAllModel(array_keys($args->taxonomies));
?>
<?php foreach($args->taxonomies as $slug => $taxonomy) : ?>
    <div class="filter-category pl-1 pl-md-0">
        <h5 class="h6 h5-lg font-weight-semibold">
            <?php echo $taxonomy->label;?>
        </h5>
        <div id="<?php echo 'search-filter'.preg_replace('/[[:space:]]+/', '-', strtolower($taxonomy->label));?>" class="taxonomy_container linGradient">

            <?php 
            foreach($args->terms[$slug] as $term) :
                if( $data->checkSnacksExist($taxonomy->name, $term->slug) ) :
            ?>
                    <label class="term_container h7 h6-lg d-flex align-items-center">
                        <input type="radio" name="<?php echo $slug;?>_filter" id="<?php echo $term->slug.'_'.$slug;?>_filter" />
                        <span class="tax_name ml-4"><?php echo $term->name;?></span>
                        <span class="checkmark"></span>
                    </label>
            <?php 
                endif; 
            endforeach;
            ?>
            

            <button class="seeMoreBtn seeMoreCat h7 h6-lg">
                <span class="mr-2"><i class="seeMoreIcon fas fa-plus"></i></span>
                <span class="seeMoreCatText">Show More</span>
            </button>

            <button disabled id="<?php echo $slug;?>RemoveFilter" class="btn btn-primary btn-sm mt-2 h6-lg" onclick="removeFilter(this);" data-taxonomy="<?php echo $slug;?>">
                Remove Filter
            </button>
        </div>
    </div>

<?php endforeach; ?>




