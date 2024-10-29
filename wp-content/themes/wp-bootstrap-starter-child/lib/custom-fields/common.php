<?php
/*
* Creating a function to create our CPT
*/

function getCustomFields($post_type = 'snack')
{
    switch($post_type)
    {
        case 'snack':
            return array(
                "user-friendly-name" => array(
                    "name" => "User Friendly Name",
                    "type" => "text"
                ),
                "internal-id-code" => array(
                    "name" => "Internal identification code",
                    "type" => "text"
                ),
                "ingredients" => array(
                    "name" => "Ingredients",
                    "type" => "text"
                ),
                "contains" => array(
                    "name" => "Contains",
                    "type" => "text"
                ),
                "meltable" => array(
                    "name" => "Meltable",
                    "type" => "yes_no"
                ),
                "discounts" => array(
                    "name" => "Discounts",
                    "type" => "multitext",
                    "subfields" => array(
                        array(
                            "name" => "Discount Type",
                            "slug" => "discount-type",
                            "type" => "radio",
                            "options" => array (
                                "Percentage" => "%",
                                "Fixed" => "$",
                            ),
                        ),
                    ),
                ),
                "minimum-price" => array(
                    "name" => "Minimum Price",
                    "type" => "text"
                ),
                "price" => array(
                    "name" => "Price",
                    "type" => "sap"
                ),
                "stock" => array(
                    "name" => "stock",
                    "type" => "sap"
                ),
                "sku" => array(
                    "name" => "SKU",
                    "type" => "sap"
                ),
                "small-thumbnail" => array(
                    "name" => "Small Thumbnail",
                    "type" => "image",
                    "extra_text" => "(205x130)"
                ),
                "medium-thumbnail" => array(
                    "name" => "Medium Thumbnail",
                    "type" => "image",
                    "extra_text" => "(233x189)"
                ),
                "nutrition-label" => array(
                    "name" => "Nutritional Label",
                    "type" => "image",
                    "extra_text" => "(311x500)",
                ),
                "preorder-shipping-date" => array(
                    "name" => "Preorder shipping date",
                    "type" => "date",
                    "extra_text" => "month/day e.g. 12/1"
                ),
                "included-in" => array(
                    "name" => "Included In (largest size that includes this snack)",
                    "type" => "included-in",
                    "options" => array(
                        "Mini" => "mini",
                        "Original" => "original",
                        "Family" => "family",
                        "Drink" => "drink"
                    )
                ),
            );
            break;

        case 'collection':
            return array(
                "bg-color" => array(
                    "name" => "Background Color (hexadecimal)",
                    "type" => "color"
                ),
                "text-color" => array(
                    "name" => "Text Color (hexadecimal)",
                    "type" => "color"
                ),
                "country-code" => array(
                    "name" => "SAP Country Code (2 Letters)",
                    "type" => "text"
                ),
                "crate-size" => array(
                    "name" => "SAP Crate Size (4Snack, 8Snack, 16Snack, etc.)",
                    "type" => "text"
                ),
                "contains" => array(
                    "name" => "Contains",
                    "type" => "text"
                ),
                "meltable" => array(
                    "name" => "Meltable",
                    "type" => "yes_no"
                ),
                "user-friendly-name" => array(
                    "name" => "User Friendly Name",
                    "type" => "text"
                ),
                "internal-id-code" => array(
                    "name" => "Internal identification code",
                    "type" => "text"
                ),
                "fulfillment-name" => array(
                    "name" => "Fulfillment Name (Alphanumeric characters only)",
                    "type" => "text",
                ),
                
                "country-taxonomy" => array(
                    "name" => "Country Taxonomy",
                    "type" => "taxonomy-select",
                ),
                "cost" => array(
                    "name" => "Cost (numeric values only, no $)",
                    "type" => "text",
                ),
                "stock" => array(
                    "name" => "Stock",
                    "type" => "text"
                ),
                "icon" => array(
                    "name" => "Country Icon",
                    "type" => "image",
                    "extra_text" => "(Wavy Flag SVG)"
                ),
                "featured-image" => array(
                    "name" => "Featured Image",
                    "type" => "image",
                    "extra_text" => "(Crate image)"
                ),
                "hero-background-position" => array(
                    "name" => "Hero Background Positioning (background-position css value, defaults to '".CollectionModel::$default_bg_position."')",
                    "type" => "text",
                ),
                "hero-type" => array(
                    "name" => "Hero Type",
                    "type" => "option",
                    "options" => array(
                        "Image" => "image",
                        "Video" => "video",
                    ),
                ),
                "hero-image" => array(
                    "name" => "Hero Image",
                    "type" => "image",
                    "extra_text" => ""
                ),
                "hero-video" => array(
                    "name" => "Hero Video Url",
                    "type" => "text"
                ),
                "preorder-shipping-date" => array(
                    "name" => "Preorder shipping date",
                    "type" => "date",
                    "extra_text" => "month/day e.g. 12/1"
                ),
            );
            break;
            
        case 'country':
            return array(
                "user-friendly-name" => array(
                    "name" => "User Friendly Name",
                    "type" => "text"
                ),
                "fulfillment-name" => array(
                    "name" => "Fulfillment Name (Alphanumeric characters only)",
                    "type" => "text",
                ),
                "country-code" => array(
                    "name" => "SAP Country Code (2 Letters)",
                    "type" => "text"
                ),
                "country-taxonomy" => array(
                    "name" => "Country Taxonomy",
                    "type" => "taxonomy-select"
                ),
                "internal-id-code" => array(
                    "name" => "Internal identification code",
                    "type" => "extendable",
                    "singular-fields" => array(
                        "4Snack" => array(
                            "name" => "Mini",
                            "type" => "text",
                        ),
                        "4SnackW" => array(
                            "name" => "Mini SnackClub",
                            "type" => "text",
                        ),
                        "8Snack" => array(
                            "name" => "Original",
                            "type" => "text",
                        ),
                        "8SnackSC" => array(
                            "name" => "Original SnackClub",
                            "type" => "text",
                        ),
                        "8SnackU" => array(
                            "name" => "Original Ultimate",
                            "type" => "text",
                        ),
                        "16SnackW" => array(
                            "name" => "Ultimate",
                            "type" => "text",
                        )
                    ),
                    "field-rows" => array(
                        "Others" => array(
                            "size" => array(
                                "name" => "Size",
                                "type" => "text",
                            ),
                            "code" => array(
                                "name" => "Internal Identification Code",
                                "type" => "text",
                            )
                        )
                    )
                ),
                "contains" => array(
                    "name" => "Contains",
                    "type" => "text"
                ),
                "cost" => array(
                    "name" => "Cost",
                    "type" => "sap"
                ),
                "stock" => array(
                    "name" => "Stock",
                    "type" => "text"
                ),
                "icon" => array(
                    "name" => "Country Icon",
                    "type" => "image",
                    "extra_text" => "(Wavy Flag SVG)"
                ),
                "featured-image" => array(
                    "name" => "Featured Image",
                    "type" => "image",
                    "extra_text" => "(Crate Image)"
                ),
                "hero-type" => array(
                    "name" => "Hero Type",
                    "type" => "option",
                    "options" => array(
                        "Image" => "image",
                        "Video" => "video",
                    ),
                ),
                "hero-image" => array(
                    "name" => "Hero Image",
                    "type" => "image",
                    "extra_text" => ""
                ),
                "hero-video" => array(
                    "name" => "Hero Video Url",
                    "type" => "text"
                ),
                "preorder-shipping-date" => array(
                    "name" => "Preorder shipping date",
                    "type" => "date",
                    "extra_text" => "month/day e.g. 12/1"
                ),
                "hide-drink" => array(
                    "name" => "Hide Drink",
                    "type" => "checkbox",
                ),
            );
            break;

        case 'unboxing':
            return array(
                "greeting" => array(
                    "name" => "Greeting (Defaults to \"Welcome To\")",
                    "type" => "text",
                ),
                "crate-type" => array(
                    "name" => "Crate Type",
                    "type" => "option",
                    "options" => array(
                        "Country" => "country",
                        "Collection" => "collection"
                    )
                ),
                "country-taxonomy" => array(
                    "name" => "Country Taxonomy",
                    "type" => "taxonomy-select",
                    "taxonomy" => ["countries","collections"]
                ),
                "video-preview" => array(
                    "name" => "Hero Video Preview",
                    "type" => "image"
                ),
                "hero-video" => array(
                    "name" => "Hero Video Url",
                    "type" => "text"
                ),
                "fun-facts" => array(
                    "name" => "Fun Facts",
                    "type" => "extendable",
                    "singular-fields" => array(
                        "opening-text" => array(
                            "name" => "Opening Text",
                            "type" => "text",
                            "element" => "textarea"
                        ),
                        "closing-text" => array(
                            "name" => "Closing Text",
                            "type" => "text",
                            "element" => "textarea"
                        )
                    ),
                    "field-rows" => array(
                        "facts" => array(
                            "fact" => array(
                                "name" => "Fact",
                                "type" => "text"
                            ),
                            "icon" => array(
                                "name" => "Icon",
                                "type" => "image"
                            )
                        )
                    )
                ),
                "trivia" => array(
                    "name" => "Trivia",
                    "type" => "extendable",
                    "singular-fields" => array(

                    ),
                    "field-rows" => array(
                        "questions" => array(
                            "question" => array(
                                "name" => "Question",
                                "type" => "text"   
                            ),
                            "answer-a" => array(
                                "name" => "A",
                                "type" => "text"   
                            ),
                            "answer-b" => array(
                                "name" => "B",
                                "type" => "text"
                            ),
                            "answer-c" => array(
                                "name" => "C",
                                "type" => "text"   
                            ),
                            "answer-d" => array(
                                "name" => "D",
                                "type" => "text"   
                            ),
                            "correct-answer" => array(
                                "name" => "Correct Answer",
                                "type" => "option",
                                "options" => array(
                                    "A" => "a",
                                    "B" => "b",
                                    "C" => "c",
                                    "D" => "d"
                                ),
                            )
                        )
                    )
                ),
                "recipe" => array(
                    "name" => "Recipe",
                    "type" => "extendable",
                    "singular-fields" => array(
                        "opening-text" => array(
                            "name" => "Opening Text",
                            "type" => "text",
                            "element" => "textarea"
                        ),
                        "video-thumbnail" => array(
                            "name" => "Video Thumbnail",
                            "type" => "image" 
                        ),
                        "video" => array(
                            "name" => "Recipe Video",
                            "type" => "text"
                        ),
                        "local-name" => array(
                            "name" => "Local Name",
                            "type" => "text"
                        ),
                        "english-name" => array(
                            "name" => "English Name",
                            "type" => "text"
                        ),
                        "description" => array(
                            "name" => "Description",
                            "type" => "text",
                            "element" => "textarea"
                        ),
                        "total-time" => array(
                            "name" => "Total time",
                            "type" => "text"
                        ),
                        "serves" => array(
                            "name" => "Serves",
                            "type" => "text"
                        ),
                        "difficulty" => array(
                            "name" => "Difficulty",
                            "type" => "text"
                        ),
                        "ingredients" => array(
                            "name" => "Ingredients",
                            "type" => "html"
                        ),
                        "ingredients-image" => array(
                            "name" => "Ingredients Image",
                            "type" => "image" 
                        ),
                        "downloadable-version" => array(
                            "name" => "Downloadable Printable Recipe",
                            "type" => "text"
                        )
                    ),
                    "field-rows" => array(
                        "steps" => array(
                            "image" => array(
                                "name" => "Image",
                                "type" => "image"   
                            ),
                            "description" => array(
                                "name" => "Description",
                                "type" => "text"   
                            ),
                        )
                    )
                ),
                "playlist" => array(
                    "name" => "Spotify Playlist Link",
                    "type" => "text"
                ),
                "coloring-pages" => array(
                    "name" => "Coloring Pages Link",
                    "type" => "text"
                ),
                "word-search" => array(
                    "name" => "Word Search Link",
                    "type" => "text"
                ),
                'still-hungry-image' => array(
                    "name" => "Still Hungry Footer Image",
                    "type" => "image"
                ),
                'multi-box-snacks' => array(
                    "name" => "Allow multiple size selection for snacks",
                    "type" => "checkbox",
                )
            );

        default:
            // do nothing
            // return false;
            break;
    }
}

if( is_admin() )
    add_action("the_post", "add_custom_fields_snacks", 11);

function add_custom_fields_snacks()
{
    
    add_meta_box(
        "snack-metabox", 
        "Snack Custom Fields", 
        "populate_snack_fields", 
        array("snack","country","collection","unboxing"), 
        "normal", 
        "default", 
        "post"
    );

}

function generateField($type, $value, $field_id, $field, $post)
{
    switch($type)
    {
        case "text":
            if(!empty($field['element']) && $field['element'] == 'textarea')
            {
                addTextCustomField($value, $field_id, $field, 'text', 'textarea');
            }
            else
            {
                addTextCustomField($value, $field_id, $field);
            }
            break;

        case "checkbox":
            addCheckboxCustomField($value, $field_id, $field, 'date');
            break;

        case "yes_no":
            addYesNoCustomField($value, $field_id, $field, 'yes');
            break;

        case "date":
            addTextCustomField($value, $field_id, $field, 'date');
            break;

        case "color":
            addTextCustomField($value, $field_id, $field, 'color');
            break;

        case "image":
            addImageCustomField($value, $field_id, $field);
            break;

        case "sap":
            addSapCustomField($post, $field_id, $field);
            break;

        case "multitext":
            addMultitextCustomField($value, $field_id, $field, $post->ID);
            break;

        case "taxonomy-select":
            addTaxonomySelectCustomField($value, $field_id, $field, $post->post_type);
            break;

        case "option":
            addOptionCustomField($value, $field_id, $field);
            break;

        case "function":
            addFunctionCustomField($value, $field_id, $field);
            break;

        case "extendable":
            addExtendableCustomField($value, $field_id, $field, $post);
            break;

        case "html":
            addHTMLCustomField($value, $field_id, $field);
            break;

        case "included-in":
            addIncludedInField( $field_id, $field, $post->ID );
            //addOptionCustomField($value, $field_id, $field);
            break;

        default:
            addTextCustomField($value, $field_id, $field);
            break;
    }
}

function populate_snack_fields($post)
{
    $fields = getCustomFields($post->post_type);
    
    foreach($fields as $field_id => $field)
    {
        $value = get_post_meta( $post->ID, $field_id, true );
        echo '<div style="border-bottom:1px solid #d3d4d7; padding:10px 0;">
            <label style="display:block;font-weight:bold;" for="'.$field_id.'">'.$field['name'].'</label>';

        generateField($field['type'], $value, $field_id, $field, $post);

        echo '</div>';
    }
}

function addSubFields($post, $field)
{
    echo '<div class="subfields">';
    foreach($field['subfields'] as $subfield)
    {
        $sub_value = get_post_meta( $post->ID, $subfield['slug'], true );
        echo '<div class="subfield_container">';
        switch($subfield['type'])
        {
            case 'radio':
                ?>
                <h5><?php echo $subfield['name'];?></h5>
                
                <?php foreach($subfield['options'] as $option_name => $option) :?>
                    <label for="<?php echo $option;?>"><?php echo $option;?></label>
                    <input <?php echo ($sub_value == $option) ? 'checked ' : '';?>type="radio" name="<?php echo $subfield['slug'];?>" value="<?php echo $option;?>" />
                <?php 
                endforeach;

                break;

            default:
                // do nothing
                break;
        }
        echo '</div>';
    }
    echo '</div>';
}

function addHTMLCustomField($value, $field_id, $field)
{
    wp_editor( $value, $field_id, array( 'textarea_name' => $field_id, 'media_buttons' => false, 'tinymce' => array() ) );
}

function addExtendableCustomField($value, $field_id, $field, $post)
{
    include( __DIR__ . "/extendable-field.php" );
}

function addTextCustomField($value, $field_id, $field, $type = 'text', $element = 'input')
{
    if($element == 'textarea'):
    ?>
        <textarea name="<?php echo $field_id;?>" rows="5" cols="80"><?php echo $value;?></textarea>
    <?php else: ?>
        <input style="display:block;width:100%;" name="<?php echo $field_id;?>" id="<?php echo $field_id;?>" type="<?php echo $type;?>" value="<?php echo $value;?>" />
    <?php
    endif;
}

function addCheckboxCustomField($value, $field_id, $field, $checkbox_value = 1)
{
    ?>
        <input name="<?php echo $field_id;?>" type="checkbox" id="<?php echo $option;?>" value="<?php echo $checkbox_value;?>" <?php echo ($value == 1) ? 'checked' : '';?> />
    <?php
}

function addYesNoCustomField($value, $field_id, $field, $checkbox_value = 1)
{
    ?>
    <label class="yes-no-checkbox">
        <input name="<?php echo $field_id;?>" type="checkbox" id="<?php echo $option;?>" <?php echo ($value == 'yes') ? 'checked' : '';?> />
        <div>
            <div>Yes</div>
            <div>No</div>
        </div>
    </label>
    <?php
}

function addOptionCustomField($value, $field_id, $field)
{
    foreach($field['options'] as $key => $option):
    ?>
    <div>
        <input name="<?php echo $field_id;?>" type="radio" id="<?php echo $option;?>" value="<?php echo $option;?>" <?php echo ($value === $option) ? 'checked' : '';?> />
        <label for="<?php echo $option;?>"><?php echo $key;?></label>
    </div>
    <?php
    endforeach;
}

function addImageCustomField($value, $field_id, $field)
{
    ?>
        
        <div id="<?php echo $field_id;?>_thumbnail" style='border-radius:4px;display:inline-block;padding:5px;margin:5px 10px 0 0;border:1px solid #8c8f94;width:80px;height:80px;'>
            <?php 
                if($value && !in_array( substr(wp_get_attachment_url($value), -3), array('png','jpg','svg') ) )
                    echo '<img src="'. get_stylesheet_directory_uri() .'/assets/default/video.png" class="attachment-thumbnail size-thumbnail" loading="lazy" width="80" height="80">';
                elseif($value)
                    echo '<img src="'.wp_get_attachment_url($value).'" class="attachment-thumbnail size-thumbnail" loading="lazy" width="80" height="80">';
                else
                    echo '<img src="#" class="attachment-thumbnail size-thumbnail" alt="no image chosen" loading="lazy" width="80" height="80">';
            ?>
        </div>
        
        <button style="vertical-align: bottom;" class="set_custom_images button"><?php echo ($value) ? 'Replace' : 'Add';?> Image</button>
        
        <?php if(array_key_exists('extra_text', $field)) : ?>
            <span style="vertical-align: bottom;"><?php echo $field['extra_text'];?></span>
        <?php endif;?>

        <button <?php echo $value ? '' : 'disabled';?> onClick="removeImageValue('<?php echo $field_id;?>');" style="float:right;" type="button" class="components-button is-link is-destructive" id="<?php echo $field_id;?>_rmv">Remove image</button>  
    
        <input type="hidden" class="regular-text process_custom_images" value="<?php echo $value;?>" id="<?php echo $field_id;?>_field" name="<?php echo $field_id;?>_field" >

    <?php
}

function addSapCustomField($post, $field_id, $field)
{
    if($field_id == 'stock' && in_array($post->post_type, array('country', 'collection')))
    {
        $stock = get_post_meta( $post->ID, 'in-stock', true );
        if( !empty($stock) && is_array($stock) )
        {
            foreach($stock as $size => $qty)
            {
                echo "{$size}: {$qty}<br />";
            }
        }
    }
    else
    {
        $value = get_post_meta( $post->ID, $field_id, true );
    ?>
        <input type="text" name="<?php echo $field_id;?>" id="<?php echo $field_id;?>" value="<?php echo $value;?>" />
        <span>(This value comes from SAP and cannot be edited)</span>
    <?php
    }
}

function addMultitextCustomField($value, $field_id, $field, $post_id)
{
    if(!array_key_exists('subfields', $field))
    {
        addTextCustomField($value, $field_id, $field);
        return;
    }
    ?>
    <table>
        <tr>
            <th>Type</th>
            <th>&nbsp;</th>
            <th>Value</th>
        </tr>
    <?php
    foreach($field['subfields'] as $subfield)
    {
        $sub_value = get_post_meta( $post_id, $subfield['slug'], true );
        foreach($subfield['options'] as $option_name => $option)
        {
            $option_sub_value = get_post_meta( $post_id, $field_id.'_'.$option_name, true );
            $form_input_name = $field_id.'_'.$option_name;
    ?>
            <tr>
                <td>
                    <input 
                        style="display:inline-block" 
                        name="<?php echo $subfield['slug'];?>" 
                        id="<?php echo $subfield['slug'];?>" 
                        type="radio" 
                        value="<?php echo $option;?>" 
                        <?php echo ($option == $sub_value) ? 'checked' : '';?>
                    />
                </td>
                <td>
                    <label 
                        style="display:inline-block;font-weight:bold;" 
                        for="<?php echo $subfield['slug'];?>"
                    >
                        <?php echo $option;?>
                    </label>
                </td>
                <td>
                    <input 
                        style="display:inline-block" 
                        name="<?php echo $form_input_name;?>" 
                        id="<?php echo $form_input_name;?>" 
                        type="text" 
                        value="<?php echo $option_sub_value;?>" 
                    />
                </td>
            </tr>
    <?php
        }
    }
    echo '</table>'; 
}

function addTaxonomySelectCustomField($value, $field_id, $field, $post_type)
{
    if( is_array($post_type) )
    {
        $taxonomy = array();
        foreach( $post_type as $type )
        {
            $object = get_post_type_object( $post_type );
            $taxonomy = empty($field['taxonomy']) ? strtolower($object->labels->name) : $field['taxonomy'];
        }
    }
    else
    {
        $object = get_post_type_object( $post_type );
        $taxonomy = empty($field['taxonomy']) ? strtolower($object->labels->name) : $field['taxonomy'];
    }

    $options = get_terms( array(
        'taxonomy' => $taxonomy,
        'hide_empty' => false,
    ) );

    echo "<select name='{$field_id}' id='{$field_id}'>";
        echo "<option value=''>-- Choose Taxonomy --</option>";
    foreach($options as $option)
    {
        if($value == $option->slug)
        {
            echo "<option selected value='{$option->slug}'>{$option->name}</option>";
        }
        else
        {
            echo "<option value='{$option->slug}'>{$option->name}</option>";
        }
    }
    echo '</select>';
}

function addIncludedInField($field_id, $field, $post_id)
{
    $country_terms = get_the_terms($post_id, 'countries');
    $collection_terms = get_the_terms($post_id, 'collections');

    if( !empty($country_terms) && !empty($collection_terms) )
    {
        $terms = array_merge($country_terms, $collection_terms);
    }
    elseif( !empty($country_terms) )
    {
        $terms = $country_terms;
    }
    elseif( !empty($collection_terms) )
    {
        $terms = $collection_terms;
    }
    else
    {
        $terms = array();
    }

    foreach($terms as $term)
    {
        $unboxing_post = UnboxingModel::getUnboxingPostByTerm($term);

        if( empty($unboxing_post) )
        {
            continue;
        }

        $multiple_selections = get_post_meta( $unboxing_post->ID, 'multi-box-snacks', true );

        if( empty( $multiple_selections ) )
        {
            $val = get_post_meta( $post_id, "{$field_id}_{$term->term_id}", true );

            echo "<div style='margin-top:15px;font-style:italic;'>{$term->name} ({$term->taxonomy})</div>";
            addOptionCustomField($val, "{$field_id}[{$term->term_id}]", $field);
            echo "<button type='button' onclick='clearSize({$term->term_id})'>Clear</button>";

            echo '<input type="hidden" name="included_in_type_'.$term->term_id.'" value="singular" />';
        }
        else
        {
            $val = get_post_meta( $post_id, "{$field_id}_{$term->term_id}" );

            echo "<div style='margin-top:15px;font-style:italic;'>{$term->name} ({$term->taxonomy})</div>";
            echo "<div>";
            foreach( $field['options'] as $option_name => $option )
            {
                echo "<div>";
                echo "<input type='checkbox' name='{$field_id}[{$term->term_id}][]' value='{$option}' ". ( in_array($option,$val) ? "checked" : "" ) ." />";
                echo "<label>{$option_name}</label>";                
                echo "</div>";
            }
            echo "</div>";

            echo '<input type="hidden" name="included_in_type_'.$term->term_id.'" value="multiple" />';
        }
    }
}

add_action('save_post', 'save_snack_meta');
function save_snack_meta()
{
    if( $_POST['action'] == 'inline-save' )  // don't update custom fields on Quick Edit
    {
        return;
    }
    $fields = getCustomFields($_POST['post_type']);
    if( is_array($fields) )
    {
        $post_id = $_POST['post_ID'];
        foreach($fields as $field_id => $field)
        {
            if( array_key_exists($field_id.'_field', $_POST) || array_key_exists($field_id, $_POST) || in_array($field['type'], array('multitext', 'extendable', 'included-in', 'checkbox', 'yes_no') ) )
            {
                switch($field['type'])
                {
                    case 'image':
                        $attach_id = $_POST[$field_id.'_field'];
                        update_post_meta($post_id, $field_id, $attach_id);
                        break;
    
                    case 'multitext':
                        foreach($field['subfields'] as $subfield)
                        {
                            if( array_key_exists($subfield['slug'], $_POST) )
                            {
                                update_post_meta($post_id, $subfield['slug'], $_POST[$subfield['slug']]);
                            }
    
                            foreach($subfield['options'] as $option_name => $option)
                            {
                                $form_input_name = $field_id.'_'.$option_name;
                                if( array_key_exists($form_input_name, $_POST) )
                                {
                                    update_post_meta($post_id, $form_input_name, $_POST[$form_input_name]);
                                }
                            }
                        }
                        break;

                    case 'checkbox':
                        if( array_key_exists($field_id, $_POST) )
                        {
                            update_post_meta($post_id, $field_id, $_POST[$field_id]);
                        }
                        else
                        {
                            delete_post_meta($post_id, $field_id);
                        }
                        break;
                    case 'yes_no':
                        update_post_meta($post_id, $field_id, $_POST[$field_id] == 'on' ? 'yes' : 'no');
                        break;
                
                    case "extendable":
                        UnboxingModel::saveExtendableField( $field, $post_id, $_POST, $field_id );
                        break;

                    case "included-in":
                        SnackModel::saveIncludedInField( $field, $post_id, $_POST, $field_id );
                        break;

                    default:
                        if( $field_id == 'fulfillment-name' )
                        {
                            $value = preg_replace("/[^A-Za-z0-9 ]/", '', $_POST[$field_id]);
                        }
                        else
                        {
                            $value = trim($_POST[$field_id]);
                        }
                        update_post_meta($post_id, $field_id, $value);
                        break;
                }
            }
        }

        if( $_POST['post_type'] == 'snack' )
        {
            $price = get_post_meta($post_id, 'price', true);
            $member_price = get_post_meta($post_id, 'member-price', true);
            if( !empty($_POST['internal-id-code']) && ( empty($price) || empty($member_price) ) )
            {
                $sap_item = new SAPItem($_POST['internal-id-code']);
                
                if( !empty($sap_item) )
                {
                    $get_price = $sap_item->getItemPrice();
                    update_post_meta($post_id, 'price', $get_price);
                    $member_price = $sap_item->getItemPrice( true );
                    update_post_meta($post_id, 'member-price', $member_price);
                }
            }
        }
    }
}

// enqueue scripts needed for our custom post types to work
add_action( 'admin_enqueue_scripts', function() 
{
    if ( is_admin() )
    {
        wp_enqueue_media();
        ?>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
        <script src="<?php echo get_stylesheet_directory_uri();?>/lib/custom-fields/custom-fields.js"></script>
        <style>
        .yes-no-checkbox {
            display: block;
            margin-top: 7px;
            width: -moz-max-content;
            width: max-content;
            max-width: 100%;
        }

        .yes-no-checkbox input {
            position: absolute;
            left: -999999px;
        }
        .yes-no-checkbox > div {
            width: 63px;
            position: relative;
            height: 27px;
            border-radius: 17px;
            background: #748c97;
            transition: background-color .3s ease-in-out;
        }
        .yes-no-checkbox > div > div {
            position: absolute;
            top: 50%;
            -webkit-transform: translateY(-50%);
            -ms-transform: translateY(-50%);
            transform: translateY(-50%);
            left: 10px;
            -webkit-transition: opacity 0.3s ease-in-out;
            transition: opacity 0.3s ease-in-out;
            opacity: 0;
            font-weight: 600;
            color: #fff;
        }
        .yes-no-checkbox > div > div:nth-child(2) {
            left: unset;
            right: 10px;
            opacity: 1;
        }
        .yes-no-checkbox > div::after {
            content: "";
            position: absolute;
            top: 50%;
            -webkit-transform: translateY(-50%);
            -ms-transform: translateY(-50%);
            transform: translateY(-50%);
            left: 5px;
            height: 20px;
            background: #fff;
            width: 20px;
            border-radius: 50%;
            -webkit-transition: left 0.3s ease-in-out;
            transition: left 0.3s ease-in-out;
        }
        .yes-no-checkbox input:checked + div::after {
            left: 37px;
        }
        .yes-no-checkbox input:checked + div > div:nth-child(1) {
            opacity: 1;
        }
        .yes-no-checkbox input:checked + div > div:nth-child(2) {
            opacity: 0;
        }
        .yes-no-checkbox input:checked + div {
            background-color: #27a243;
        }
        </style>
        <?php
    }

} );
