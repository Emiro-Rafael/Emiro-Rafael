<?php

class ShopAllModel extends PageModel
{
    public $taxonomies;

    function __construct($post_id, $taxonomies = array())
    {
        parent::__construct($post_id);
        $this->taxonomies = array();
        $this->_setTaxonomies($taxonomies);
    }

    private function _setTaxonomies($taxonomy_filter)
    {
        $taxonomies = get_taxonomies();
        if(count($taxonomy_filter) > 0)
        {
            $taxonomies = array_filter(
                $taxonomies,
                function($tax) use ($taxonomy_filter)
                {
                    return in_array($tax, $taxonomy_filter);
                }
            );
        }

        foreach($taxonomies as $slug => $name)
        {
            $this->taxonomies[$slug] = (object)array(
                'name' => $name,
                'label' => $this->_taxonomyLabels($name),
            );
        }
        $this->_setTaxonomyTerms();
    }

    private function _setTaxonomyTerms()
    {
        foreach($this->taxonomies as $key => $taxonomy)
        {
            $this->terms[$key] = get_terms([
                'taxonomy' => $taxonomy->name,
                'hide_empty' => true,
            ]);
        }
    }

    private function _taxonomyLabels($name)
    {
        $tax = get_taxonomy($name);
        $labels = get_taxonomy_labels($tax);
        return $labels->singular_name;
    }
    
    public function getTaxonomies()
    {
        return $this->taxonomies;
    }

    public function getTerms($tax = null)
    {
        if(is_null($tax))
        {
            return $this->terms;
        }
        else
        {
            return $this->terms[$tax];
        }
    }

    public function checkSnacksExist($taxonomy, $terms)
    {
        $queryargs = array(
            'post_type' => 'snack',
            'posts_per_page' => -1,
            'tax_query' => array(
                array(
                    'taxonomy' => $taxonomy,
                    'field' => 'slug',
                    'terms' => $terms,
                ),
            )
        );
        $query = new WP_Query($queryargs);

        return $query->have_posts();
    }

    public function getOutOfStockSnacks($search = null)
    {
        $queryargs = array(
            'post_type' => 'snack',
            'posts_per_page' => -1,
            'meta_key' => 'user-friendly-name',
            'orderby' => 'meta_value',
            'order' => 'ASC',
            'meta_query' => array(
                array(
                    'key'     => 'in-stock',
                    'value'   => 0,
                    'compare' => '=',
                    'type'    => 'signed'
                )
            )
        );
        
        if( !empty($search) )
        {
            $queryargs['s'] = $search;
        }

        return new WP_Query($queryargs);
    }

    public function getSnacks($search = null)
    {

        $queryargs = array(
            'post_type' => 'snack',
            'posts_per_page' => -1,
            'meta_key' => 'user-friendly-name',
            'orderby' => 'meta_value',
            'order' => 'ASC',
            'meta_query' => array(
                array(
                    'key'     => 'in-stock',
                    'value'   => 0,
                    'compare' => '>',
                    'type'    => 'signed'
                )
            )
        );
        
        if( !empty($search) )
        {
            $queryargs['s'] = $search;
        }

        return new WP_Query($queryargs);
    }

    public static function getSnackThumbnail($post_id, $size)
    {
        $thumbnail_id = get_post_meta($post_id, "{$size}-thumbnail", true); // get_post_meta( int $post_id, string $key = '', bool $single = false )
        if(empty($thumbnail_id))
        {
            $thumbnail_id = get_post_meta($post_id, "_thumbnail_id", true);
        }
        return wp_get_attachment_url($thumbnail_id);
    }

    public static function getSnackCountryFlag($post_id)
    {
        $country_slug = get_the_terms($post_id, 'countries')[0]->slug;
        
        $args = [
            'post_type'      => 'country',
            'posts_per_page' => 1,
            'post_name__in'  => [$country_slug]
        ];
        $q = get_posts( $args );

        $country_post_id = $q[0]->ID;

        $icon_attachment_id = get_post_meta($country_post_id, 'icon', true);

        if(!empty($icon_attachment_id))
        {
            return wp_get_attachment_url($icon_attachment_id);
        }
        else
        {
            return  get_stylesheet_directory_uri() . '/assets/svg/' .  $q[0]->post_title . 'WavyFlag.svg';
        }
        
    }
}