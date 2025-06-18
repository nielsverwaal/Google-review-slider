<?php

namespace WP_Lemon\Child\Blocks;

use HighGround\Bulldozer\BlockRendererV2 as BlockRenderer;
use StoutLogic\AcfBuilder\FieldsBuilder;
use Timber\Timber;

class Google_Review_Slider_Block extends BlockRenderer
{
    const NAME = 'google-review-slider';

    public function add_fields(): FieldsBuilder
    {
        $this->registered_fields
            ->addText('place_id', [
                'label' => 'Google Place ID',
                'required' => 1,
            ])
            ->addText('api_key', [
                'label' => 'Google API Key',
                'required' => 1,
            ])
            ->addNumber('review_limit', [
                'label' => 'Aantal reviews tonen',
                'default_value' => 5,
                'min' => 1,
                'max' => 50,
                'step' => 1,
            ])
            ->addSelect('minimum_rating', [
                'label' => 'Minimale beoordeling (sterren)',
                'choices' => [
                    '5' => '5 sterren',
                    '4' => '4 sterren of hoger',
                    '3' => '3 sterren of hoger',
                    '2' => '2 sterren of hoger',
                    '1' => '1 ster of hoger',
                ],
                'default_value' => '4',
                'allow_null' => 0,
                'ui' => 1,
            ]);

        return $this->registered_fields;
    }

    public function block_context($context): array
    {
        $place_id = \get_field('place_id');
        $api_key = \get_field('api_key');
        $limit = (int) get_field('review_limit') ?: 5;
        $min_rating = (int) get_field('minimum_rating') ?: 4;

        $context['data']['reviews'] = $this->get_google_reviews($place_id, $api_key, $limit, $min_rating);
        $context['data']['place_id'] = $place_id;
        return $context;
    }

    private function get_google_reviews($place_id, $api_key, $limit = 5, $min_rating = 4): array
    {
        if (!$place_id || !$api_key) {
            return [];
        }

        $transient_key = 'google_reviews_' . md5($place_id . '|' . $limit . '|' . $min_rating);
        $cached = get_transient($transient_key);

        if ($cached !== false) {
            return $cached;
        }

        $fields = 'name,rating,user_ratings_total,reviews';
        $lang = 'nl';
        $url = "https://maps.googleapis.com/maps/api/place/details/json?placeid={$place_id}&fields={$fields}&language={$lang}&key={$api_key}";
        $response = wp_remote_get($url);

        if (is_wp_error($response)) {
            return [];
        }

        $body_raw = wp_remote_retrieve_body($response);
        $body = json_decode($body_raw, true);

        $all_reviews = $body['result']['reviews'] ?? [];
        $filtered = array_filter($all_reviews, function ($review) use ($min_rating) {
            return isset($review['rating'], $review['text']) &&
                $review['rating'] >= $min_rating &&
                trim($review['text']) !== '';
        });

        usort($filtered, fn($a, $b) => ($b['time'] ?? 0) <=> ($a['time'] ?? 0));

        $final = array_slice($filtered, 0, $limit);


        // ✅ Cache 24u
        set_transient($transient_key, $final, DAY_IN_SECONDS);

        return $final;
    }
}

new Google_Review_Slider_Block();
