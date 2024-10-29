<?php

class UnboxingModel extends PageModel
{
    public $country_model;
    public $country_slug;
    public $crate_type;

    const SUPERLATIVE_TYPES = array(
        'best', 'worst', 'weird'
    );

    public function __construct($post_id)
    {
        parent::__construct($post_id);

        $this->country_slug = get_post_meta($this->post_id, 'country-taxonomy', true);

        $this->setCountryModel();

        $this->setTaxonomyId();
    }

    public function setTaxonomyId()
    {
        $taxonomy = $this->crate_type == 'collection' ? 'collections' : 'countries';
        
        $terms = get_terms(
            array(
                'taxonomy' => $taxonomy,
            )
        );

        $this->term_id = current(
            array_filter(
                $terms,
                function($term) use($taxonomy)
                {
                    return $term->taxonomy == $taxonomy && $term->slug == $this->country_slug;
                }
            )
        )->term_id;
    }

    public function setCountryModel()
    {
        $this->crate_type = $this->meta['crate-type'][0] ?? 'country';
        
        $country_posts = get_posts(
            array(
                'posts_per_page' => 1,
                'post_type' => $this->crate_type,
                'post_status' => ['publish','draft'],
                'meta_query' => array(
                    array(
                        'key' => 'country-taxonomy',
                        'value' => $this->country_slug,
                        'compare' => '=',
                    )
                )
            )
        );

        $linked_post_id = current($country_posts)->ID;

        switch($this->crate_type)
        {
            case 'country':
                $this->country_model = new CountryModel($linked_post_id);
                break;

            case 'collection':
                $this->country_model = new CollectionModel($linked_post_id);
                break;
        }
    }

    public function getSnacksBySize( $size = null )
    {
        $taxonomy = $this->crate_type == 'collection' ? 'collections' : 'countries';
        
        $args = array(
            'posts_per_page' => -1,
            'post_type' => 'snack',
            'tax_query' => array(
                array(
                    'taxonomy' => $taxonomy,
                    'field' => 'slug',
                    'terms' => $this->country_slug,
                )
            ),
        );
        
        
        if( !empty($size) )
        {
            $args['meta_query'] = array(
                array(
                    'key'     => 'included-in_' . $this->term_id,
                    'value'   => $size,
                    'compare' => '=',
                ),
            );
        }
        else
        {
            $args['meta_query'] = array(
                array(
                    array(
                        'key'     => 'included-in_' . $this->term_id,
                        'compare' => 'EXISTS',
                    ),
                    array(
                        'key'     => 'included-in_' . $this->term_id,
                        'value'   => array(''),
                        'compare' => 'NOT IN'
                    )
                ),
            );
        }
        
        return get_posts( $args );
    }

    public function getUserSnackSuperlatives()
    {
        $user_id = get_current_user_id();

        if( empty($user_id) )
        {
            throw new Exception( "User not found" );
        }

        $user_snack_superlatives = get_user_meta( $user_id, "snack_superlatives_{$this->post_id}", true );

        if( empty($user_snack_superlatives) )
        {
            $values = array_fill(0, count(self::SUPERLATIVE_TYPES), null);
            $user_snack_superlatives = array_combine(self::SUPERLATIVE_TYPES, $values);
        }

        return $user_snack_superlatives;
    }

    public function getSuperlativeSnackData( $snack_id )
    {
        return array(
            "name" => get_post_meta( $snack_id, 'user-friendly-name', true ),
            "image" => get_the_post_thumbnail_url( $snack_id, 'full' ),
            "rating" => get_post_meta( $snack_id, 'average_rating', true ),
        );
    }

    public function saveUserSnackSuperlative( $data )
    {
        $user_id = get_current_user_id();

        if( empty($user_id) )
        {
            throw new Exception( "User not found" );
        }

        $user_snack_superlatives = $this->getUserSnackSuperlatives();

        $decrementable_snack_id = empty($user_snack_superlatives[ $data['type'] ]) ? null : $user_snack_superlatives[ $data['type'] ];
        
        $user_snack_superlatives[ $data['type'] ] = $data['snack_id'];

        update_user_meta( $user_id, "snack_superlatives_{$this->post_id}", $user_snack_superlatives );

        $this->_updateSuperlativeCounts( $data['type'], $data['snack_id'], $decrementable_snack_id );

        $popular_choice_snack_id = $this->getMaxSuperlatives( true, $data['type'] );
        return array(
            "user_pick" => $this->getSuperlativeSnackData( $data['snack_id'] ),
            "highest_rated" => $this->getSuperlativeSnackData( $popular_choice_snack_id )
        );
    }

    private function _updateSuperlativeCounts( $type, $snack_id, $decrementable_snack_id )
    {
        $superlative_count = $this->getSinglePostMetaByKey( 'snack-superlative-count' );

        if( empty($superlative_count) )
        {
            $values = array_fill( 0, count(self::SUPERLATIVE_TYPES), array() );
            $superlative_count = array_combine(self::SUPERLATIVE_TYPES, $values);
        }

        if( empty($superlative_count[$type][$snack_id]) )
        {
            $superlative_count[$type][$snack_id] = 0;
        }

        if( !empty( $decrementable_snack_id ) && !empty( $superlative_count[$type][$decrementable_snack_id] ) )
        {
            $superlative_count[$type][$decrementable_snack_id]--;
        }
 
        $superlative_count[$type][$snack_id]++;
        
        update_post_meta( $this->post_id, 'snack-superlative-count', $superlative_count );
    }

    public function getMaxSuperlatives( $by_type = false, $type = 'best' )
    {
        $superlative_count = $this->getSinglePostMetaByKey( 'snack-superlative-count' );

        if( empty($superlative_count) )
        {
            return false;
        }

        if($by_type)
        {
            if( empty($superlative_count[$type]) )
            {
                return false;
            }

            $max_count = max( $superlative_count[$type] );

            return array_search( $max_count, $superlative_count[$type] );
        }
        else
        {
            return array_combine(
                self::SUPERLATIVE_TYPES,
                array_map(
                    function($type) use($superlative_count)
                    {
                        if( empty($superlative_count[$type]) )
                        {
                            return false;
                        }

                        $max_count = max( $superlative_count[$type] );

                        return array_search( $max_count, $superlative_count[$type] );
                    },
                    self::SUPERLATIVE_TYPES
                )
            );
        }
    }

    public function getAnswerClass( $answer, $correct_answer, $previous_answer )
    {
        $class = "";
        if( $correct_answer == $answer )
        {
            $class .= ' correct correct-answer';
        }
        elseif( $previous_answer == $answer )
        {
            $class .= ' incorrect-answer text-white';
        }

        return $class;
    }

    public function calculateAnswerPercentages( $question_key )
    {
        $raw_numbers = get_post_meta( $this->post_id, "trivia-answer-count_{$question_key}", true );
        $total_count = array_sum($raw_numbers);

        $percentages = array();
        foreach( $raw_numbers as $answer => $count )
        {
            $count = empty($count) ? 0 : $count;
            $percentages[$answer] = round( ($count / $total_count) * 100, 1 );
        }

        return $percentages;
    }

    public function addTriviaAnswer( $data )
    {
        $user_id = get_current_user_id();

        if( empty($user_id) )
        {
            throw new Exception( "User not found" );
        }

        update_user_meta( $user_id, "trivia_{$this->post_id}_{$data['question_key']}", $data['answer'] );

        $answer_count_post_meta_key = "trivia-answer-count_{$data['question_key']}";
        $answer_count = get_post_meta( $this->post_id, $answer_count_post_meta_key, true );

        if( empty($answer_count) )
        {
            $answer_count = array(
                'a' => 0,
                'b' => 0,
                'c' => 0,
                'd' => 0,
            );
        }
        elseif( empty($answer_count[$data['answer']]) )
        {
            $answer_count[$data['answer']] = 0;
        }
        
        $answer_count[$data['answer']]++;

        update_post_meta( $this->post_id, $answer_count_post_meta_key, $answer_count );

        return $this->calculateAnswerPercentages( $data['question_key'] );
    }

    public static function saveExtendableField( $field, $post_id, $data, $field_key )
    {
        foreach( $field['singular-fields'] as $key => $singular_field )
        {
            $meta_key = $field_key . '_' . $key;
            
            if( $singular_field['type'] == 'image' && array_key_exists( $meta_key.'_field', $data ) )
            {
                update_post_meta( $post_id, $meta_key, $data[$meta_key.'_field'] );
            }
            elseif( array_key_exists( $meta_key, $data ) )
            {
                update_post_meta( $post_id, $meta_key, $data[$meta_key] );
            }
        }

        foreach( $field['field-rows'] as $key => $field_row )
        {
            if( count($data[$field_key][$key]) == 1 )
            {
                $continue = false;
                foreach( $data[$field_key][$key][0] as $value )
                {
                    if( !empty($value) )
                    {
                        $continue = true;
                        break;
                    }
                }
            }
            else
            {
                $continue = true;
            }

            if( $continue && array_key_exists( $field_key, $data ) && array_key_exists( $key, $data[$field_key] ) )
            {
                update_post_meta( $post_id, $field_key.'_'.$key, $data[$field_key][$key] );
            }
            elseif( $continue === false )
            {
                update_post_meta( $post_id, $field_key.'_'.$key, NULL );
            }
        }
    }

    public static function getUnboxingPostByTerm($term)
    {
        $singular_type = $term->taxonomy == 'countries' ? 'country' : substr($term->taxonomy, 0, -1);

        $unboxing_post = get_posts(
            array(
                'post_type' => 'unboxing',
                'posts_per_page' => 1,
                'post_status' => ['publish','draft'],
                'meta_query' => array(
                    array(
                        'key' => 'country-taxonomy',
                        'value' => $term->slug,
                        'compare' => '=',
                    ),
                    array(
                        'key' => 'crate-type',
                        'value' => $singular_type,
                        'compare' => '=',
                    )
                )
            )
        );
        return current($unboxing_post);
    }
}