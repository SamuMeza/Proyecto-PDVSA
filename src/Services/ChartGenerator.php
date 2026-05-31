<?php
namespace App\Services;

class ChartGenerator
{
    public static function barChartData(array $labels, array $values, string $label = 'Datos'): array
    {
        return [
            'type' => 'bar',
            'data' => [
                'labels' => $labels,
                'datasets' => [
                    [
                        'label' => $label,
                        'data' => $values,
                        'backgroundColor' => '#7BA7D9',
                    ],
                ],
            ],
        ];
    }

    public static function pieChartData(array $labels, array $values): array
    {
        $colors = ['#7BA7D9', '#A8D5BA', '#F4D03F', '#E8837B', '#C39BD3'];
        return [
            'type' => 'pie',
            'data' => [
                'labels' => $labels,
                'datasets' => [
                    [
                        'data' => $values,
                        'backgroundColor' => array_slice($colors, 0, count($labels)),
                    ],
                ],
            ],
        ];
    }
}
