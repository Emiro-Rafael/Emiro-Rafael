<?php

class CountryModel extends PageModel
{
    public $taxonomy;
    public $loop;

    public static $price = array(
        "4Snack" => 20,
        "8Snack" => 35,
        "16Snack" => 59,
        "Ultimate" => 55,
    );

    public static $pretty_names = array(
        "4Snack" => "Mini",
        "8Snack" => "Original",
        "16Snack" => "Family",
        "4SnackW" => "Mini w/ Drink",
        "8SnackW" => "Original w/ Drink",
        "16SnackW" => "Family w/ Drink",
        "Ultimate" => "Ultimate"
    );

    public static $button_names = array(
        "4Snack" => "Mini",
        "8Snack" => "Original",
        "16Snack" => "Family",
        "Ultimate" => "Ultimate"
    );

    public static $drink_price = 5.99;

    public function __construct($post_id)
    {
        parent::__construct($post_id);

        $this->taxonomy = get_post_meta($post_id, 'country-taxonomy', true);

        $this->setLoop();
    }

    public function getPrice($crate_size)
    {
        if( substr($crate_size, -1) == 'W' )
        {
            $base_size = substr($crate_size, 0, -1);
            return self::$price[$base_size] + self::$drink_price;
        }
        return self::$price[$crate_size];
    }

    public static function getAllCountries()
    {
        return get_posts(
            array(
                'posts_per_page' => -1,
                'post_type' => 'country',
                'post_status' => 'publish'
            )
        );
    }

    public function getIcon($size = 482)
    {
        if(!empty($this->meta['icon'][0]))
        {
            return wp_get_attachment_url($this->meta['icon'][0]);
        }
        else
        {
            $title = str_replace( ' ', '', get_the_title($this->post_id) );
            if( file_exists( get_stylesheet_directory() . "/assets/svg/{$title}WavyFlag.svg" ) )
            {
                return get_stylesheet_directory_uri() . "/assets/svg/{$title}WavyFlag.svg";
            }
            else
            {
                return get_stylesheet_directory_uri() . "/assets/svg/BlankFlag.svg";
            }
        }
    }

    public function getFeaturedImage($size = 482)
    {
        if(!is_null($this->meta['featured-image'][0]) && $this->meta['featured-image'][0] != '')
        {
            return wp_get_attachment_url($this->meta['featured-image'][0]);
        }
        else
        {
            return get_stylesheet_directory_uri() . "/assets/default/MysteryCrateHero-min.png";
        }
    }

    public function getHero()
    {
        if( array_key_exists('hero-type', $this->meta) || array_key_exists('hero-image', $this->meta) )
        {
            if( (empty($this->meta['hero-type'][0]) || $this->meta['hero-type'][0] == 'image') && !empty($this->meta['hero-image'][0]) )
            {
                $img_url = wp_get_attachment_url($this->meta['hero-image'][0]);
                return $img_url;
            }
            elseif( $this->meta['hero-type'][0] == 'video' && !empty($this->meta['hero-video'][0]) )
            {
                return '<video id="herovid" class="v-desktop" preload="auto" playsinline="" autoplay="" muted="" loop="">
                    <source src="'.$this->meta['hero-video'][0].'" type="video/mp4" codecs="hvc1">
                </video>';
            }
        }
        return '';
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
            empty( get_the_terms( $this->post_id, 'geography' ) ) ||
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

    public static function getPrettyName( $size )
    {
        switch($size)
        {
            case '4Snack':
                return 'Mini';
                break;

            case '4SnackW':
                return 'Mini w/ Drink';
                break;
            
            default:
                return self::$pretty_names[$size];
        }
    }
}
