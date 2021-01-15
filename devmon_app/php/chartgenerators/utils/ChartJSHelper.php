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

        $opts['scales']['xAxes'][] = array(
            'gridLines' => array(
                'display' => false
            ),
            'scaleLabel' => array(
                'display' => true,
                'fontColor' => 'blue',
                'fontSize' => 14,
                'labelString' => 'Time'
            )
        );

        foreach ($yAxesCfg as $cfg) {

            if (! array_key_exists('displayLines', $cfg))
                $cfg['displayLines'] = true;

            $opts['scales']['yAxes'][] = array(
                'gridLines' => array(
                    'display' => $cfg['displayLines']
                ),
                'ticks' => array(
                    'step' => 1
                ),

                'position' => $cfg['position'],
                'id' => $cfg['id'],

                'scaleLabel' => array(
                    'display' => true,
                    'fontColor' => 'blue',
                    'fontSize' => 14,
                    'labelString' => $cfg['name']
                )
            );

            // for performance
            $opts['animation'] = array(
                'duration' => 0
            );
            $opts['hover'] = array(
                'animationDuration' => 0
            );
            $opts['responsiveAnimationDuration'] = 0;
        }

        return $opts;
    }
}
