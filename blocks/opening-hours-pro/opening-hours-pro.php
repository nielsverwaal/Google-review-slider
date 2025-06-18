<?php

namespace WP_Lemon\Child\Blocks;

use HighGround\Bulldozer\BlockRendererV2 as BlockRenderer;
use StoutLogic\AcfBuilder\FieldsBuilder;
use RankMath\Helper;

class Opening_Hours_Pro_Block extends BlockRenderer
{
    public const NAME = 'opening-hours-pro';

    public function block_context($context): array
    {
        $output = [];
        global $wp_locale;
        $hash = [
            'Sunday'    => 0,
            'Monday'    => 1,
            'Tuesday'   => 2,
            'Wednesday' => 3,
            'Thursday'  => 4,
            'Friday'    => 5,
            'Saturday'  => 6,
        ];

        $hours = Helper::get_settings('titles.opening_hours');
        $today_id = intval(date('w'));
        $today_date = new \DateTimeImmutable('today');
        $special_notes = [];
        $special_days = get_field('special_opening_hours', 'option') ?? [];

        // ❗ Filter: alleen special days vanaf vandaag
        $special_days = array_filter($special_days, function ($day) use ($today_date) {
            if (empty($day['date'])) return false;
            $date = \DateTimeImmutable::createFromFormat('Y-m-d', $day['date']);
            return $date && $date >= $today_date;
        });

        $context['block_instance_id'] = uniqid('opening_hours_', true);

        foreach ($hours as $time) {
            $day = $time['day'];
            $day_id = $hash[$day];

            if (!isset($output[$day])) {
                $output[$day] = [
                    'day' => [
                        'key'   => strtolower($day),
                        'label' => $wp_locale->get_weekday($day_id),
                        'id'    => $day_id,
                    ],
                    'times' => [],
                    'is_today' => ($today_id === $day_id),
                    'is_special' => false,
                ];
            }

            $output[$day]['times'][] = $time['time'];
        }

        foreach ($special_days as $special_day) {
            $date_string = $special_day['date'] ?? null;
            if (!$date_string) continue;

            $date_obj = \DateTimeImmutable::createFromFormat('Y-m-d', $date_string);
            if (!$date_obj || $date_obj < new \DateTimeImmutable('today')) continue;


            $date = $special_day['date'];
            $label = $special_day['label'] ?? '';
            $override = $special_day['time'] ?? 'Gesloten';
            $weekday = date('l', strtotime($date));

            if (!isset($hash[$weekday])) continue;

            $weekday_id = $hash[$weekday];
            $weekday_nl = ucfirst($wp_locale->get_weekday($weekday_id));
            $normal_times = $output[$weekday]['times'] ?? [];
            $note_intro = ($date === date('Y-m-d')) ? 'Vandaag zijn wij' : "{$weekday_nl} zijn wij";

            // Notitie genereren
            if (strtolower($override) === 'gesloten') {
                $special_notes[] = "$note_intro gesloten vanwege {$label}.";
            } elseif (!empty($normal_times)) {
                $normal_range = explode('-', $normal_times[0]);
                $override_range = explode('-', $override);

                if (count($normal_range) === 2 && count($override_range) === 2) {
                    $n_open = strtotime($normal_range[0]);
                    $n_close = strtotime($normal_range[1]);
                    $o_open = strtotime($override_range[0]);
                    $o_close = strtotime($override_range[1]);

                    if (($o_close - $o_open) > ($n_close - $n_open)) {
                        $special_notes[] = "$note_intro zijn wij langer geopend dan normaal in verband met {$label}.";
                    } elseif (($o_close - $o_open) < ($n_close - $n_open)) {
                        $special_notes[] = "$note_intro zijn wij eerder gesloten dan normaal vanwege {$label}.";
                    } else {
                        $special_notes[] = "$note_intro gelden aangepaste openingstijden vanwege {$label}.";
                    }
                } else {
                    $special_notes[] = "$note_intro gelden aangepaste openingstijden vanwege {$label}.";
                }
            } else {
                $special_notes[] = "$note_intro gelden aangepaste openingstijden vanwege {$label}.";
            }

            $output[$weekday]['times'] = [$override];
            $output[$weekday]['is_special'] = true;
            if (!empty($label)) {
                $output[$weekday]['special_label'] = $label;
            }
        }

        return array_merge($context, [
            'hours' => $output,
            'special_note' => implode(' ', $special_notes),
        ]);
    }

    public function add_fields(): FieldsBuilder
    {
        return $this->registered_fields;
    }

    public function register_requirements(): bool
    {
        return class_exists('RankMath\\Helper');
    }
}

new Opening_Hours_Pro_Block();
