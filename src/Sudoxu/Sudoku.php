<?php
namespace Sudoxu;

/**
 * Class Sudoxu
 *
 * @package Sudoxu
 * @author  Burak <burak@myself.com>
 */

class Sudoku
{

    public  $sudoku,
            $result,
            $number;
    private $limit,
            $sq,
            $chars,
            $stack = array('1','2','3','4','5','6','7','8','9','0','A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');

    /**
     * Sudoku constructor
     *
     * @param int $limit
     */
    public function __construct($limit = 9)
    {
        ini_set('memory_limit', -1);
        set_time_limit(0);

        $this->number = 0;
        $this->limit  = $limit;
        $this->sq     = (int)sqrt($this->limit);

        $this->logic();

        return $this;

    }
    public static function world()
    {
        return 'Hello World, Composer!';
    }

    public function logic()
    {
        if(count($this->stack) < $this->limit)
        {
            echo 'error';
            exit;
        }
    }

    public function set($number)
    {
        $this->limit = $number;
        $this->sq    = (int)sqrt($this->limit);

        return $this;

    }

    private function return_row($cell)
    {
        return floor($cell / $this->limit);

    }

    private function return_col($cell)
    {
        return $cell % $this->limit;

    }

    private function return_block($cell)
    {
        return floor($this->return_row($cell) / $this->sq) * $this->sq + floor($this->return_col($cell) / $this->sq);

    }

    private function is_possible_row($number, $row)
    {
        $possible = true;
        for ($x = 0; $x < $this->limit; $x++)
        {
            if (isset($this->sudoku[$row * $this->limit + $x]) && $this->sudoku[$row * $this->limit + $x] == $number)
            {
                $possible = false;
            }
        }

        return $possible;
    }

    private function is_possible_col($number, $col)
    {
        $possible = true;
        for ($x = 0; $x < $this->limit; $x++)
        {
            if (isset($this->sudoku[$col + $this->limit * $x]) && $this->sudoku[$col + $this->limit * $x] == $number)
            {
                $possible = false;
            }
        }

        return $possible;
    }

    private function is_possible_block($number, $block)
    {
        $possible = true;
        for ($x = 0; $x < $this->limit; $x++)
        {
            $index = floor($block / $this->sq) * $this->sq * $this->limit + $x % $this->sq + $this->limit * floor($x / $this->sq) + $this->sq * ($block % $this->sq);
            if (isset($this->sudoku[$index]) && $this->sudoku[$index] == $number)
            {
                $possible = false;
            }
        }

        return $possible;
    }

    private function is_possible_number($cell, $number)
    {
        $row   = $this->return_row($cell);
        $col   = $this->return_col($cell);
        $block = $this->return_block($cell);

        return ($this->is_possible_row($number, $row) && $this->is_possible_col($number, $col) && $this->is_possible_block($number, $block));
    }

    private function is_correct_row($row)
    {
        $row_temp = array();
        for ($x = 0; $x < $this->limit; $x++)
        {
            if(!isset($this->sudoku[$row * $this->limit + $x]))
            {
                $this->sudoku[$row * $this->limit + $x] = null;
            }
            $row_temp[$x] = $this->sudoku[$row * $this->limit + $x];
        }

        return count(array_diff($this->chars, $row_temp)) == 0;
    }

    private function is_correct_col($col)
    {
        $col_temp = array();
        for ($x = 0; $x < $this->limit; $x++)
        {
            $col_temp[$x] = $this->sudoku[$col + $x * $this->limit];
        }
        return count(array_diff($this->chars, $col_temp)) == 0;
    }

    private function is_correct_block($block)
    {
        $block_temp = array();
        for ($x = 0; $x < $this->limit; $x++)
        {
            $lookingfor = floor($block / $this->sq) * ($this->sq * $this->limit) + ($x % $this->sq) + $this->limit * floor($x / $this->sq) + $this->sq * ($block % $this->sq);

            if (!isset($this->sudoku[$lookingfor]))
            {
                $this->sudoku[$lookingfor] = null;
            }

            $block_temp[$x] = $this->sudoku[$lookingfor];
        }
        return count(array_diff($this->chars, $block_temp)) == 0;
    }

    private function is_solved_sudoku()
    {
        for ($x = 0; $x < $this->limit; $x++)
        {
            if (!$this->is_correct_block($x) or !$this->is_correct_row($x) or !$this->is_correct_col($x))
            {
                return false;
            }
        }
        return true;
    }

    private function determine_possible_values($cell)
    {
        $possible = array();
        for ($x = 0; $x < $this->limit; $x++)
        {
            if ($this->is_possible_number($cell, $this->chars[$x]))
            {
                array_unshift($possible, $this->chars[$x]);
            }
        }

        return $possible;
    }

    private function determine_random_possible_value($possible, $cell)
    {
        return $possible[$cell][rand(0, count($possible[$cell]) - 1)];
    }

    private function scan_sudoku_for_unique()
    {
        $possible = false;
        for ($x = 0; $x < $this->limit * $this->limit; $x++)
        {
            if (!isset($this->sudoku[$x]))
            {
                $possible[$x] = $this->determine_possible_values($x, $this->sudoku);
                if (count($possible[$x]) == 0)
                {
                    return (false);
                }
            }
        }

        return $possible;
    }

    private function remove_attempt($attempt_array, $number)
    {
        $new_array = array();
        $count     = count($attempt_array);
        for ($x = 0; $x < $count; $x++)
        {
            if ($attempt_array[$x] != $number)
            {
                array_unshift($new_array, $attempt_array[$x]);
            }
        }
        return $new_array;
    }


    private function next_random($possible)
    {
        $min_choices = null;
        $max         = $this->limit;
        for ($x = 0; $x < $this->limit * $this->limit; $x++)
        {
            if (!isset($possible[$x]))
            {
                $possible[$x] = null;
            }

            if ((count($possible[$x]) <= $max) && (count($possible[$x]) > 0))
            {
                $max         = count($possible[$x]);
                $min_choices = $x;
            }
        }
        return $min_choices;
    }


    private function build()
    {
        $this->sudoku = array();
        $this->chars  = array();

        for ($i = 0; $i < $this->limit; $i++)
        {
            $this->chars[] = $this->stack[$i];
        }
    }

    public function microtime()
    {
        return microtime(true);
    }

    public function generate()
    {
        $start     = $this->microtime();
        $this->build();

        $x         = 0;
        $saved     = array();
        $saved_sud = array();
        while (!$this->is_solved_sudoku())
        {
            $x++;
            $next_move = $this->scan_sudoku_for_unique();

            if ($next_move === false)
            {
                $next_move    = array_pop($saved);
                $this->sudoku = array_pop($saved_sud);
            }

            $what_to_try = $this->next_random($next_move);
            $attempt     = $this->determine_random_possible_value($next_move, $what_to_try);


            if (count($next_move[$what_to_try]) > 1)
            {
                $next_move[$what_to_try] = $this->remove_attempt($next_move[$what_to_try], $attempt);
                array_push($saved, $next_move);
                array_push($saved_sud, $this->sudoku);
            }
            $this->sudoku[$what_to_try] = $attempt;
        }

        $timing       = $this->microtime() - $start;
        $this->result = array(
            'created_in' => round($timing,2),
            'difficulty' => 'N/A'
        );

        return $this;
    }

    private function array2export()
    {
        return array(
            'sudoku' => $this->sudoku,
            'limit'  => $this->limit,
            'result' => $this->result,

        );
    }

    public function to($to)
    {
        if($to == 'json')
        {
            return json_encode($this->array2export());
        }
        else if($to == 'serialize')
        {
            return serialize($this->array2export());
        }
        else if($to == 'html')
        {
            return serialize($this->array2export());
        }
    }


    public function draw($echo = true)
    {


        $string = "<table cellpadding='0' cellspacing='0' style='margin:0 auto;font:30px Arial;border:3px solid black'>";

        for ($u = 0; $u < $this->limit; $u++)
        {


            if (($u + 1) % $this->sq == '0')
            {
                $border = 3;
            } else {
                $border = 1;
            }

            $string .= '<tr>';

            for ($v = 0; $v < $this->limit; $v++)
            {


                if (($v + 1) % $this->sq == '0')
                {
                    $border2 = 3;
                } else {
                    $border2 = 1;
                }

                $string .= '<td
                            data-row="'.$u.'"
                            data-col="'.$v.'"
                            style="border: 1px solid black;border-bottom:' . $border . 'px solid black;border-right:' . $border2 . 'px solid black;line-height: 50px; width: 50px; text-align: center; vertical-align: middle;">';
                $string .=($this->sudoku[$v * $this->limit + $u]);
                $string .= '</td>';

            }

            $string .= '</tr>';

        }

        $string .= "</table>";


        if($echo === true)
        {
            echo $string;
        }
        else
        {
            return $string;
        }

    }

}