<?php

require_once __DIR__ . '/../vendor/autoload.php'; // Autoload files using Composer autoload

use Sudoxu\Sudoku;

/** @var Sudoku $Sudoku */
$Sudoku = new Sudoku();

$array = $Sudoku->generate('array');

$string = "<table cellpadding='0' cellspacing='0' style='margin:0 auto;font:30px Arial;border:3px solid black'>";

for ($u = 0; $u < 9; $u++)
{

    if (($u + 1) % 3 == '0')
    {
        $border = 3;
    } else {
        $border = 1;
    }

    $string .= '<tr>';

    for ($v = 0; $v < 9; $v++)
    {

        if (($v + 1) % 3 == '0')
        {
            $border2 = 3;
        } else {
            $border2 = 1;
        }

        $string .= '<td
                        data-row="'.$u.'"
                        data-col="'.$v.'"
                        style="border: 1px solid black;border-bottom:' . $border . 'px solid black;border-right:' . $border2 . 'px solid black;line-height: 50px; width: 50px; text-align: center; vertical-align: middle;">';
        $string .= ($array['sudoku'][$v * 9 + $u]);
        $string .= '</td>';

    }

    $string .= '</tr>';

}

$string .= "</table>";


echo $string;