<?php

class ChartJSHelper
{

    static $COLORS = array(
        array(
            'bgColor' => 'rgba(51, 102, 204, 0.2)',
            'brColor' => 'rgba(51, 102, 204, 1)'
        ),
        array(
            'bgColor' => 'rgba(220, 57, 18, 0.2)',
            'brColor' => 'rgba(220, 57, 18, 1)'
        ),
        array(
            'bgColor' => 'rgba(255, 153, 0, 0.2)',
            'brColor' => 'rgba(255, 153, 0, 1)'
        ),
        array(
            'bgColor' => 'rgba(16, 150, 24, 0.2)',
            'brColor' => 'rgba(16, 150, 24, 1)'
        ),
        array(
            'bgColor' => 'rgba(98, 0, 238, 0.2)',
            'brColor' => 'rgba(98, 0, 238, 1)'
        ),
        array(
            'bgColor' => 'rgba(3, 218, 198, 0.2)',
            'brColor' => 'rgba(3, 218, 198, 1)'
        ),
        array(
            'bgColor' => 'rgba(1, 87, 155, 0.2)',
            'brColor' => 'rgba(1, 87, 155, 1)'
        )
    );

    public static function GetDataSet($label, $yAxisID, $colorIdx)
    {
        return array(
            'borderWidth' => 2,
            'label' => $label,
            'backgroundColor' => self::$COLORS[$colorIdx]['bgColor'],
            'borderColor' => self::$COLORS[$colorIdx]['brColor'],
            'yAxisID' => $yAxisID,
            'radius' => 0,
            'pointHitRadius' => 8,
            'data' => array()
        );
    }

    public static function GetOptions($yAxesCfg)
    {
        $opts['scales'] = array();

        $opts['scales']['x']['grid']['display'] = false;

        foreach ($yAxesCfg as $cfg) {
            $opts['scales'][$cfg['id']] = array(
                'title' => array(
                    'display' => true,
                    'color' => 'blue',
                    'text' => $cfg['name'],
                    'font' => array(
                        'size' => 14
                    )
                ),
                'position' => $cfg['position'],
                'grid' => array(
                    'display' => $cfg['displayLines']
                )
            );

            // for performance
            $opts['responsiveAnimationDuration'] = 0;
            $opts['animation'] = false;
            $opts['snapGaps'] = true;
        }

        return $opts;
    }
}
