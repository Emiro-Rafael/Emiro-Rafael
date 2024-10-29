<?php

class PageModel
{
    protected $post_id;
    protected $post_type;
    public $meta;

    public const SIZES = array(
        'mini' => 'Mini',
        'original' => 'Original',
        'family' => 'Family'
    );

    public function __construct($post_id)
    {
        $this->post_id = $post_id;

        $this->post_type = get_post_type($post_id);
        $this->meta = get_post_meta($post_id);
    }

    public function getMeta()
    {
        return $this->meta;
    }

    public function getSinglePostMetaByKey( $key )
    {
        return get_post_meta( $this->post_id, $key, true );
    }

    protected function setLoop()
    {
        $taxonomy_to_check = ($this->post_type == 'country') ? 'countries' : 'collections';
        $args = array(
            'post_type' => 'snack',
            'posts_per_page' => -1,
            'tax_query' => array(
                array(
                    'taxonomy' => $taxonomy_to_check,
                    'field' => 'slug',
                    'terms' => $this->taxonomy,
                ),
            ),
            'meta_key' => 'in-stock',
        );
        
        if( !function_exists('filter_case') )
        {
            function filter_case($orderby = '')
            {
                $orderby = '(CASE WHEN meta_value = 0 THEN 0 ELSE 1 END) DESC, (SELECT meta_value FROM wp_postmeta WHERE post_id = wp_posts.ID AND meta_key = "user-friendly-name" LIMIT 1) ASC';
                return $orderby;
            }
        }
        
        add_filter( 'posts_orderby', 'filter_case' );
        $this->loop = new WP_Query($args);
        remove_filter( 'posts_orderby', 'filter_case' );
    }

    public function getLoop()
    {
        return $this->loop;
    }

    public function getGeographyTerms()
    {
        return get_terms( array(
            'taxonomy' => 'geography',
            'hide_empty' => false,
        ) );
    }

    public function getId()
    {
        return $this->post_id;
    }

    public function getCountriesFromGeography($geography_slug)
    {
        $countries = get_posts(
            array(
                'posts_per_page' => -1,
                'post_type' => 'country',
                'order' => 'ASC',
                'orderby' => 'title',
                'tax_query' => array(
                    array(
                        'taxonomy' => 'geography',
                        'field' => 'slug',
                        'terms' => $geography_slug,
                    )
                )
            )
        );

        foreach($countries as $key => $country)
        {
            $country_taxonomy = get_post_meta( $country->ID, 'country-taxonomy', true );

            if(empty($country_taxonomy))
            {
                unset($countries[$key]);
                continue;
            }
            
            $term = get_term_by('slug', $country_taxonomy , 'countries');
            
            if($term->count == 0)
            {
                unset($countries[$key]);
            }
        }

        return $countries;
    }

    public static function getCartCounter()
    {
        $cart_counter = Cart::getCartNumber();

        if($cart_counter > 0)
        {
            echo "<div id='cart-counter' class='cart-counter position-absolute d-flex align-items-center justify-content-center'>
                    <p class='h9 h7-xxl font-weight-semibold text-white mb-0'>{$cart_counter}</p>
                </div>";
        }
    }

    public static function getTermMeta($taxonomy, $term_id, $meta)
    {
        $term_meta = get_option( "{$taxonomy}_{$term_id}" );
        return empty($term_meta[$meta]) ? 0 : $term_meta[$meta];
    }

    public static function getAllPostmetaByKey( $key, $type = 'post', $limit = 10, $sort = 'DESC', $status = 'publish' )
    {
        global $wpdb;

        if( empty( $key ) )
            return;
    
        $r = $wpdb->get_results( $wpdb->prepare( "
            SELECT pm.post_id, pm.meta_value FROM {$wpdb->postmeta} pm
            LEFT JOIN {$wpdb->posts} p ON p.ID = pm.post_id
            WHERE pm.meta_key = %s 
            AND p.post_status = %s 
            AND p.post_type = %s
            ORDER BY pm.meta_value {$sort}
            LIMIT {$limit}
        ", $key, $status, $type ) );
    
        return $r;
    }

    protected static function getPostsByTerm($term, $taxonomy, $post_type = 'snack', $limit = 10)
    {
        return get_posts(
            array(
                'posts_per_page' => $limit,
                'post_type' => $post_type,
                'exclude' => get_the_ID(),
                'tax_query' => array(
                    array(
                        'taxonomy' => $taxonomy,
                        'field' => 'slug',
                        'terms' => $term,
                    )
                ),
                'meta_query' => array(
                    array(
                        'key'     => 'in-stock',
                        'value'   => 0,
                        'compare' => '>',
                        'type'    => 'signed'
                    )
                )
            )
        );
    }

    protected static function getPostsByIds($ids, $limit)
    {
        return get_posts(
            array(
                'numberposts' => $limit,
                'post__in' => $ids,
                'orderby' => 'post__in'
            )
        );
    }
    
    protected static function getRandomPosts( $post_type, $limit )
    {
        return get_posts(
            array( 
                'post_type' => $post_type, 
                'posts_per_page' => $limit,
                'exclude' => get_the_ID(),
                'orderby' => 'rand'
            )
        );
    }

    protected static function getPostsForSlider($arguments)
    {
        switch($arguments['identifier'])
        {
            case 'hidden-american-gems':
                $posts_meta = self::getPostsByTerm('hidden-american-gems', 'collections', 'snack', $arguments['max']);
                break;

            case 'country':
                $posts_meta = self::getPostsByTerm($arguments['country'], 'countries', 'snack', $arguments['max']);
                break;

            case 'related':
                // gather by brand, then snack_type, then random
                $posts_meta = get_posts(
                    array(
                        'post_type' => 'snack',
                        'post_status' => 'publish',
                        'exclude' => get_the_ID(),
                        'tax_query' => array(
                            array(
                                'taxonomy' => 'brands',
                                'field'    => 'slug',
                                'terms'    => $arguments['brand']
                            ),
                            array(
                                'taxonomy' => 'snack_types',
                                'field'    => 'slug',
                                'terms'    => $arguments['snack_type']
                            )
                        )
                    )
                );
                
                $posts_meta = array_merge( 
                    $posts_meta,
                    self::getPostsByTerm($arguments['brand'], 'brands', 'snack', $arguments['max']), 
                    self::getPostsByTerm($arguments['snack_type'], 'snack_types', 'snack', $arguments['max']),
                    self::getRandomPosts( 'snack', $arguments['max'] )
                );
                break;

            case 'top-selling':
                $post_ids = General::getWeeklyBestSellers();
                $posts_meta = self::getPostsByIds($post_ids, $arguments['max']);
                break;

            default:
                $posts_meta = self::getAllPostmetaByKey($arguments['identifier'], 'snack', $arguments['max']);
        }

        $posts = array();
        $used_ids = array();
        foreach($posts_meta as $item)
        {
            $post_id = empty($item->post_id) ? $item->ID : $item->post_id;
            $snack_obj = new SnackModel($post_id);

            if( !$snack_obj->checkForCompletion() || empty( $snack_obj->getStock() ) )
            {
                continue;
            }

            $snack = (object) array(
                'ID' => $post_id,
                'name' => get_post_meta($post_id, 'user-friendly-name', true),
                'thumbnail' => $snack_obj->getThumbnail('small'),
                'link' => get_permalink($post_id),
                'flag' => $snack_obj->getCountryFlag(),
                'rating' => get_post_meta($post_id, 'average_rating', true),
                'brand' => $snack_obj->getBrand(),
                'type' => $snack_obj->getSnackType()
            );

            if( !in_array($post_id, $used_ids) )
            {
                array_push($posts, $snack);
            }
            array_push($used_ids, $post_id);
            
            if( count($posts) >= $arguments['max'])
            {
                break;
            }
        }

        return $posts;
    }

    protected static function getTermsForSlider($arguments)
    {
        $terms = get_terms( array(
            'taxonomy' => $arguments['identifier'],
            'hide_empty' => true,
            'number' => $arguments['max']
        ) );
            
        return array_map(
            function ($term)
            {
                return (object) array(
                    'name' => $term->name,
                    'link' => get_term_link($term->term_id),
                    'thumbnail' => wp_get_attachment_image( self::getTermMeta( $term->taxonomy, $term->term_id, 'thumbnail'), array(205, 130), false, array('class' => 'img-fluid') ),
                );
            },
            $terms
        );
    }
    
    public static function getCategoricalData($arguments)
    {
        switch ($arguments['type'])
        {
            case 'post':
                return self::getPostsForSlider($arguments);
                break;

            case 'taxonomy':
                return self::getTermsForSlider($arguments);
                break;
        }
    }

    public static function getAccountLink()
    {
        $account_link = get_option('account-page-url');
        if( empty($account_link) )
        {
            $host = $_SERVER['HTTP_HOST'];
            preg_match("/[^\.\/]+\.[^\.\/]+$/", $host, $matches);
            $account_link = $_SERVER['REQUEST_SCHEME'] . '://account.' . $matches[0];
        }
        return $account_link;
    }

    public function getUserFriendlyName()
    {
        if( metadata_exists('post', $this->post_id, 'user-friendly-name') )
        {
            return get_post_meta($this->post_id, 'user-friendly-name', true);
        }
        else
        {
            return get_the_title( $this->post_id );
        }
    }

    /**
     * @param string $crate_size this function only applies to Countries and Collections, so the size is needed (8Snack, 8SnackW, 16Snack, 16SnackW are possible values)
     * @param boolean $ignore_current_cart pass true if we want to get the available stock without accounting for the current user's cart
     * @return integer
     */ 
    public function getStock( $crate_size, $ignore_current_cart = true )
    {
        $reserved_count = General::getCurrentCartReserves( $this->post_id, $crate_size );
        
        if( $ignore_current_cart && !empty($_SESSION['cart']) && !empty($_SESSION['cart'][$this->post_id]) && !empty($_SESSION['cart'][$this->post_id][$crate_size]) )
        {
            $reserved_count -= $_SESSION['cart'][$this->post_id][$crate_size];
        }

        if(empty($this->meta['in-stock']))
        {
            return 0;
        }
        elseif(is_array($this->meta['in-stock']))
        {
            $stock = unserialize($this->meta['in-stock'][0]);
        }
        else
        {
            $stock = unserialize($this->meta['in-stock']);
        }
        return $stock[$crate_size] - $reserved_count;
    }

    /**
     * returns total stock, mostly used to check that stock is greater than 0
     */
    public function checkInStock()
    {
        $stock = get_post_meta( $this->post_id, 'in-stock', true );

        if( empty($stock) )
        {
            return 0;
        }
        elseif( !is_array($stock) )
        {
            return $stock;
        }
        return array_sum( $stock );
    }

    public function checkIfPreorder()
    {
        if( empty($this->meta['preorder-shipping-date']) )
        {
            return false;
        }
        else
        {
            $preorder_date = $this->meta['preorder-shipping-date'][0];
            return $preorder_date > date('Y-m-d');
        }
    }

    public function getShippingDate()
    {
        if(!empty($this->meta['preorder-shipping-date'][0]))
        {
            return date( 'n/j', strtotime($this->meta['preorder-shipping-date'][0]) );
        }
    }

    public function getBoxesInStock()
    {
        return get_post_meta( $this->post_id, 'in-stock', true );
    }
}