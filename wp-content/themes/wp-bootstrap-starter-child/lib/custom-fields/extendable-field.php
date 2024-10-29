<div
    style="
        padding:10px 20px;
        margin:20px 0;
        border:1px solid #d3d4d7;
        border-radius:10px;
        background:#f9f9f9;
    "
>

<?php
foreach( $field['singular-fields'] as $key => $singular_field )
{
    $field_key = $field_id . '_' . $key;

    $value = get_post_meta( $post->ID, $field_key, true );

    echo '<label style="display:block;font-weight:bold;margin-top:20px;" for="'.$field_key.'">'.$singular_field['name'].'</label>';
    generateField($singular_field['type'], $value, $field_key, $singular_field, $post);
}


foreach( $field['field-rows'] as $group_key => $rows )
{
    
    $meta_array = get_post_meta( $post->ID, $field_id."_".$group_key, true );
    
    if( empty($meta_array) )
    {
        $meta_array = array(
            array_combine(
                array_keys($rows),
                array_fill(0, count($rows), null)
            )
        );
        $count = 1;
    }
    else
    {
        $count = count($meta_array);
    }

    ?>
        <fieldset 
            style="
                padding:10px 20px;
                margin:20px 0;
                border:1px solid #d3d4d7;
                border-radius:10px;
                background:#f4f4f4;
            "
        >
        
    <?php
    echo "<h3><em>" . ucwords($group_key) . "</em></h3>";
    foreach($meta_array as $i => $row_values)
    {
        ?>
            <fieldset 
                data-field="<?php echo $field_id."_".$group_key;?>"
                data-subfields="<?php echo implode(",", array_keys($rows));?>"
                data-position="<?php echo $i;?>"
                style="
                    margin:20px 0;
                    border-bottom:1px solid #d4d4d4;
                    background:transparent;
                "
            >
        <?php
        foreach( array_keys($rows) as $key )
        {
            $value = empty($row_values[$key]) ? NULL : $row_values[$key];
            $key_arr = array($field_id, $group_key);
            $field_key = implode("_", $key_arr);

            $field_key = "{$field_id}[{$group_key}][{$i}][{$key}]";
            

            $row_field = $field['field-rows'][$group_key][$key];

            $label = $row_field['name'];
            $type = $row_field['type'];

            echo "
                <div>
                <label style='display:block;font-weight:bold;margin-top:20px;' for='{$field_key}'>
                    {$label}
                </label>";

            generateField($type, $value, $field_key, $row_field, $post);
            
            echo '</div>';
        }
        ?>
            <button
                <?php if($i==0):?>disabled<?php endif;?>
                onclick='removeExtendableRow(this)'
                class='components-button button is-destructive remove-row' 
                style='margin:20px auto;display:block;min-width:300px'>
                    Remove Row
            </button>
        </fieldset>
        <?php
    }

    echo "
            <button class='components-button button' style='margin:20px auto;display:block;min-width:450px' onclick='addExtendableRow(this)'>Add Row</button>
            <input type='hidden' name='{$field_id}_{$group_key}_count' value='{$count}' />
        </fieldset>
    ";
}

?>


</div>