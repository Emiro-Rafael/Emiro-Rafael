<?php

class CollectionModel extends PageModel
{
    private static $default_cost = 39.99;
    private static $default_bg_color = '#EF3E36';
    public static $default_bg_position = 'left center';

    public function __construct($post_id)
    {
        parent::__construct($post_id);

        $this->taxonomy = get_post_meta($post_id, 'country-taxonomy', true);

        $this->setLoop();
    }

    public static function getAllCollections()
    {
        $collections =  get_posts(
            array(
                'posts_per_page' => -1,
                'post_type' => 'collection',
                'post_status' => 'publish',
                'order_by' => 'post_title',
                'order' => 'DESC',
            )
        );
        
        /*
        foreach($collections as $key => $collection)
        {
            $collection_taxonomy = get_post_meta( $collection->ID, 'country-taxonomy', true );
            
            if(empty($collection_taxonomy))
            {
                unset($collections[$key]);
                continue;
            }
            
            /* Uncomment this block if we ever want to hide out of stock collections
            $term = get_term_by('slug', $collection_taxonomy, 'collections');
            
            $stock = get_post_meta( $collection->ID, 'in-stock', true );
            if(
                $term->count == 0 ||
                empty( $stock ) ||
                array_sum( $stock ) == 0
            )
            {
                unset( $collections[$key] );
            }
            
        }
        */

        return $collections;
    }

    public function getFeaturedImage($size = 482)
    {
        if(!is_null($this->meta['featured-image'][0]) && $this->meta['featured-image'][0] != '')
        {
            return wp_get_attachment_url($this->meta['featured-image'][0]);
        }
        else
        {
            return "https://place-hold.it/{$size}x{$size}";
        }
    }

    public function getHero()
    {
        if(array_key_exists('hero-type', $this->meta))
        {
            if($this->meta['hero-type'][0] == 'image' && !is_null($this->meta['hero-image'][0]) && $this->meta['hero-image'][0] != '')
            {
                $img_url = wp_get_attachment_url($this->meta['hero-image'][0]);
                return $img_url;
                // return '<img id="heroimg" src="'.$img_url.'" />';
            }
            elseif($this->meta['hero-type'][0] == 'video' && $this->meta['hero-video'][0] != '')
            {
                return '
                <video id="herovid" class="v-desktop" preload="auto" playsinline="" autoplay="" muted="" loop="">
                    <source src="'.$this->meta['hero-video'][0].'" type="video/mp4" codecs="hvc1">
                </video>
                ';
            }
        }
        return '';
    }

    public function getPrice()
    {
        if( !empty( $this->meta['cost'][0] ) )
        {
            return round($this->meta['cost'][0], 2);
        }
        else
        {
            return round(self::$default_cost, 2);
        }
    }

    public function getIcon($size = 482)
    {
        if( !empty( $this->meta['icon'][0] ) )
        {
            return wp_get_attachment_url($this->meta['icon'][0]);
        }
        else
        {
            return "https://place-hold.it/{$size}x{$size}";
        }
    }

    public function getHeroBackgroundPosition()
    {
        if(!empty($this->meta['hero-background-position'][0]))
        {
            return $this->meta['hero-background-position'][0];
        }
        else
        {
            return self::$default_bg_position;
        }
    }

    public function getBackgroundColor()
    {
        if(!empty($this->meta['bg-color'][0]))
        {
            return $this->meta['bg-color'][0];
        }
        else
        {
            return self::$default_bg_color;
        }
    }

    public function getCrateSize()
    {
        $size = empty( $this->meta['crate-size'] ) ? '8Snack' : current( $this->meta['crate-size'] );
        return $size;
    }

    public function checkForCompletion()
    {
        if
        (
            empty( get_post_meta( $this->post_id, 'user-friendly-name' ) ) ||
            empty( get_post_meta( $this->post_id, 'country-taxonomy' ) ) ||
            empty( get_post_meta( $this->post_id, 'icon' ) ) ||
            empty( get_post_meta( $this->post_id, 'featured-image' ) ) ||
            empty( get_post_meta( $this->post_id, 'country-code' ) ) ||
            empty( get_post_meta( $this->post_id, 'bg-color' ) ) ||
            empty( get_post_meta( $this->post_id, 'text-color' ) ) ||
            !has_post_thumbnail( $this->post_id ) || 
            empty( get_post($this->post_id)->post_content ) ||
            (
                empty( get_post_meta( $this->post_id, 'hero-type' ) ) &&
                empty( get_post_meta( $this->post_id, 'hero-image' ) ) &&
                empty( get_post_meta( $this->post_id, 'hero-video' ) ) 
            )
        )
        {
            return false;
        }
        else
        {
            return true;
        }
    }
}