<?php

class SnackModel extends PageModel
{
    private $taxonomies;
    private $terms;

    private static $minimum_stock = 0;
    private static $stock_alert_level = 20;

    private static $default_discount = array(
        'type' => '%',
        'value' => 30
    );

    public function __construct($post_id)
    {
        parent::__construct($post_id);

        $this->taxonomies = get_object_taxonomies($this->post_type);

        foreach($this->taxonomies as $taxonomy)
        {
            $this->terms[$taxonomy] = get_the_terms($post_id, $taxonomy);
        }
    }

    public static function getUserSnackRating($user_id, $snack_id)
    {
        $reviews = get_user_meta($user_id, 'review');

        foreach( $reviews as $review )
        {
            if( $review['post_id'] == $snack_id )
            {
                return $review['rating'];
            }
        }

        return 0;
    }

    public static function userCanReview($user_id, $snack_id)
    {
        $reviews = get_user_meta($user_id, 'review');

        if( empty($reviews) ) return true;

        $snack_review_ids = array_map(
            function($review)
            {
                return $review['post_id'];
            },
            $reviews
        );

        return !in_array($snack_id, $snack_review_ids);
    }

    public function approveReview( $comment )
    {
        $review_array = array(
            "user_id" => $comment->user_id,
            "comment" => $comment->comment_content,
            "rating" => get_comment_meta( $comment->comment_ID, 'rating', true )
        );

        $add_post_meta = add_post_meta($this->post_id, 'review', $review_array);
        if($add_post_meta)
        {
            $this->_updateAverageRating();
        }
    }

    public function addRatingFromUnboxing($rating)
    {
        $user_meta_array = array(
            "post_id" => $this->post_id,
            "comment" => "Unboxing Review",
            "rating" => $rating
        );
        $add_user_meta = add_user_meta( wp_get_current_user()->ID, 'review', $user_meta_array );


        $review_array = array(
            "user_id" => wp_get_current_user()->ID,
            "comment" => "Unboxing Review",
            "rating" => $rating
        );
        $add_post_meta = add_post_meta($this->post_id, 'review', $review_array);
        
        if($add_post_meta)
        {
            $this->_updateAverageRating();
        }
    }

    /**
     * function to post review for current snack
     * int $user_id
     * string $review_text
     * int $review_rating 
     */
    public function addReview($user_id, $review_text, $review_rating, $user_email = null)
    {
        /**
         * add review meta to post
         */
        // MOVED TO $this->approveReview(), runs after comment is approved
        
        /**
         * add review meta to user
         */
        $user_meta_array = array(
            "post_id" => $this->post_id,
            "comment" => $review_text,
            "rating" => $review_rating
        );
        $add_user_meta = add_user_meta($user_id, 'review', $user_meta_array);
        
        /**
         * add comment for moderation purposes
         */
        $data = array(
            'comment_post_ID' => $this->post_id,
            'comment_content' => $review_text,
            'comment_author_email' => $user_email,
            'user_id' => $user_id,
            'comment_approved' => 0,
            'comment_type' => 'snack-review',
            'comment_meta' => array(
                //"post_meta_id" => $add_post_meta,
                'rating' => $review_rating
            )
        );
        wp_insert_comment($data);

        return $add_user_meta;
    }

    private function _updateAverageRating()
    {
        $reviews = get_post_meta($this->post_id, 'review');
        
        $ratings = array_map(
            function($review)
            {
                return $review['rating'];
            },
            $reviews
        );

        $average_rating = round(array_sum($ratings) / count($ratings), 1);
        update_post_meta($this->post_id, 'average_rating', $average_rating);
    }

    public function getThumbnail($size = 'small')
    {
        $thumbnail_id = get_post_meta($this->post_id, "{$size}-thumbnail", true); // get_post_meta( int $post_id, string $key = '', bool $single = false )
        if(empty($thumbnail_id))
        {
            $thumbnail_id = get_post_meta($this->post_id, "_thumbnail_id", true);
            if(empty($thumbnail_id))
            {
                return 'https://place-hold.it/264x264';
            }
        }
        return wp_get_attachment_url($thumbnail_id);
    }

    public function getBrand()
    {
        if( empty($this->terms['brands']) )
        {
            return '';
        }
        return $this->terms['brands'][0]->name;
    }

    public function getDiscount($price = null)
    {
        return get_post_meta($this->post_id, 'member-price', true);
    }

    // return price w/ unit as a string based on if user is subscribed or not
    public function getCurrentUserPrice()
    {
        $unit = "$";

        $meta_key = User::checkHasSubscription() ? 'member-price' : 'price';

        $price_str = $unit . get_post_meta($this->post_id, $meta_key, true);

        return $price_str;
    }

    public function getDiscountType()
    {
        return "({$_ENV['default_snack_discount']}% OFF)";
    }

    public function getCountryFlag()
    {
        $country_slug = $this->_getSnackCountryTerm()->slug;
        
        $args = [
            'post_type'      => 'country',
            'posts_per_page' => 1,
            'post_name__in'  => [$country_slug]
        ];
        $q = get_posts( $args );

        if( empty($q) )
        {
            return  get_stylesheet_directory_uri() . '/assets/svg/BlankFlag.svg';
        }
        else
        {
            $country = new CountryModel($q[0]->ID);
            return $country->getIcon();
        }
    }

    public function getMinimumStock()
    {
        return self::$minimum_stock;
    }

    public function getStockAlertLevel()
    {
        return self::$stock_alert_level;
    }

    public function getReviews()
    {
        $review_comments = get_comments(
            array(
                'post_id' => $this->post_id,
                'number' => 20,
                'orderby' => 'comment_date',
                'order' => 'DESC',
                'status' => 'approve',
                'type' => 'snack-review'
            )
        );
        $reviews = array();
        foreach( $review_comments as $comment )
        {
            $post_meta_id = get_comment_meta( $comment->comment_ID, 'post_meta_id', true );
            $review_meta = get_metadata_by_mid( 'post', $post_meta_id );

            $review = array(
                'rating' => $review_meta->meta_value['rating'],
                'comment' => $comment->comment_content,
                'user_name' => get_user_meta($review_meta->meta_value['user_id'], 'first_name', true)
            );

            if( !empty($comment->comment_content) )
            {
                array_push($reviews, $review);
            }
        }
        return $reviews;
    }

    public function hasReviews()
    {
        return !empty( get_post_meta($this->post_id, 'average_rating', true) );
    }

    public function getStarPct($rating)
    {
        if(empty($this->meta['review']))
            return 0;

        $star_count = 0;
        foreach($this->meta['review'] as $serialized_review)
        {
            $review = unserialize($serialized_review);
            if($review['rating'] == $rating)
                $star_count++;
        }
        return round(100 * $star_count / $this->getRatingsCount(),1);
    }

    public function getRatingsCount()
    {
        if(empty($this->meta['review']))
            return 0;

        return count($this->meta['review']);
    }

    public function getTerms()
    {
        return $this->terms;
    }

    public function getSnackId()
    {
        return $this->post_id;
    }

    public function getSnackType($field = 'name')
    {
        if(empty($this->terms['snack_types']))
        {
            return '';
        }
        
        return $this->terms['snack_types'][0]->{$field};
    }

    public function getGeography($field = 'name')
    {
        return $this->terms['geography'][0]->{$field};
    }

    private function _getSnackCountryTerm()
    {
        return current(
            array_filter(
                $this->terms['countries'],
                function ($country_term)
                {
                    return $country_term->slug != 'world-tour';
                }
            )
        );
    }

    public function getAllCountries($field = 'name')
    {
        $countries = array_map(
            function ($country_term) use($field)
            {
                return trim($country_term->{$field});
            },
            $this->terms['countries']
        );

        return json_encode($countries);
    }

    public function getCountryName($field = 'name')
    {
        return $this->_getSnackCountryTerm()->{$field};
    }

    public function getCountryNameLink($field = 'name')
    {
        $name = $this->_getSnackCountryTerm()->{$field};
        $countryName = str_replace( ' ', '-', $name );
        return $countryName;
    }

    public function getNutritionalLabel()
    {
        if(!empty($this->meta['nutrition-label']))
        {
            return wp_get_attachment_url($this->meta['nutrition-label'][0]);
        }
    }

    /**
     * @param string $crate_size - only present here to match the parameters of this function in parent class
     * @param boolean $ignore_current_cart - pass true if we want to get the available stock without accounting for the current user's cart
     * @return integer
     */ 
    public function getStock( $crate_size = null, $ignore_current_cart = true )
    {
        $reserved_count = General::getCurrentCartReserves( $this->post_id );

        if( $ignore_current_cart && !empty($_SESSION['cart']) && !empty($_SESSION['cart'][$this->post_id]) )
        {
            $reserved_count -= $_SESSION['cart'][$this->post_id];
        }

        if(empty($this->meta['in-stock']))
        {
            return 0;
        }
        elseif(is_array($this->meta['in-stock']))
        {
            return $this->meta['in-stock'][0] - $reserved_count;
        }

        return $this->meta['in-stock'] - $reserved_count;
    }

    public function checkForCompletion()
    {
        if
        (
            empty( $this->meta['small-thumbnail'] ) ||
            empty( $this->meta['medium-thumbnail'] ) ||
            empty( $this->meta['user-friendly-name'] ) ||
            empty( $this->meta['internal-id-code'] ) ||
            empty( $this->meta['price'] ) ||
            empty( $this->meta['nutrition-label'] ) ||
            empty( $this->terms['countries'] ) ||
            empty( $this->terms['snack_types'] ) ||
            !has_post_thumbnail( $this->post_id ) || 
            empty( get_post($this->post_id)->post_content ) ||
            (
                empty( $this->meta['discounts_Percentage'] ) &&
                empty( $this->meta['discounts_Fixed'] ) &&
                empty( $this->meta['discount-type'] ) 
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

    public function getFeaturedImage($size = 482)
    {
        return get_the_post_thumbnail_url( $this->post_id, 'large' );
    }

    public function getShippingDate()
    {
        if(!empty($this->meta['preorder-shipping-date'][0]))
        {
            return $this->meta['preorder-shipping-date'][0];
        }
    }

    public static function saveIncludedInField( $field, $post_id, $data, $field_key )
    {
        $meta = array_map(
            function( $m )
            {
                return $m[0];
            },
            get_post_meta( $post_id )
        );

        $included_in_meta = array_filter(
            $meta,
            function( $value, $key )
            {
                return strpos( $key, "included-in" ) !== false;
            },
            ARRAY_FILTER_USE_BOTH
        );
        
        foreach( $included_in_meta as $key => $value )
        {
            $box_id = explode('_', $key)[1];
            if( empty($data[$field_key][$box_id]) )
            {
                delete_post_meta( $post_id, $key );
            }
        }

        foreach( $data[$field_key] as $key => $value )
        {
            if( $data['included_in_type_'.$key] == 'singular' )
            {
                update_post_meta( $post_id, $field_key.'_'.$key, $value );
            }
            else
            {
                delete_post_meta( $post_id, $field_key.'_'.$key );
                foreach($data[$field_key][$key] as $size)
                {
                    add_post_meta( $post_id, $field_key.'_'.$key, $size );
                }
            }
        }        
    }
}