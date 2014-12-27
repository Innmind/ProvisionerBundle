<?php

namespace Innmind\ProvisionerBundle;

/**
 * Small helper to linearize data points
 */
class Math
{
    /**
     * Determine slope and intercept variables of the affine function
     * that best fit the given datapoints
     *
     * @param array $data Array keys as x axis and values as y axis
     *
     * @return array Like ['slope' => float, 'intercept' => float]
     */
    public static function linearRegression(array $data)
    {
        $count = count($data);
        $x = array_keys($data);
        $y = array_values($data);

        foreach ($x as &$value) {
            $value = (float) $value;
        }

        foreach ($y as &$value) {
            $value = (float) $value;
        }

        $xSum = array_sum($x);
        $ySum = array_sum($y);
        $xxSum = 0;
        $xySum = 0;

        for ($i = 0; $i < $count; $i++) {
            $xySum += $x[$i] * $y[$i];
            $xxSum += $x[$i] * $x[$i];
        }

        $slope = (($count * $xySum) - ($xSum * $ySum)) / (($count * $xxSum) - ($xSum * $xSum));
        $intercept = ($ySum - ($slope * $xSum)) / $count;

        return [
            'slope' => $slope,
            'intercept' => $intercept
        ];
    }
}
