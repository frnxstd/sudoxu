<?php

class sudoxu
{

    public  $sudoku,
            $result,
            $number;
    private $limit,
            $sq,
            $chars,
            $stack = array('1','2','3','4','5','6','7','8','9','0','A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');

    public function __construct()
    {
        ini_set('memory_limit', -1);
        set_time_limit(0);

        ini_set('display_errors',1);
        error_reporting(E_ALL);

        $this->number = 0;
        $this->limit  = 16;
        $this->sq     = (int)sqrt($this->limit);

        self::logic();

        return $this;

    }

    public function logic()
    {
        if(count($this->stack) < $this->limit)
        {
            echo 'error';
            exit;
        }
    }

    static function printr($write)
    {
        echo "<pre>";
        print_r($write);
        echo "</pre>";

    }

    public function set($number)
    {
        $this->limit = $number;
        $this->sq = (int)sqrt($this->limit);

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
        return floor(self::return_row($cell) / $this->sq) * $this->sq + floor(self::return_col($cell) / $this->sq);

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
        $row   = self::return_row($cell);
        $col   = self::return_col($cell);
        $block = self::return_block($cell);

        return (self::is_possible_row($number, $row) && self::is_possible_col($number, $col) && self::is_possible_block($number, $block));
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
            if (!self::is_correct_block($x) or !self::is_correct_row($x) or !self::is_correct_col($x))
            {
                return false;
                break;
            }
        }
        return true;
    }

    private function determine_possible_values($cell)
    {
        $possible = array();
        for ($x = 0; $x < $this->limit; $x++)
        {
            if (self::is_possible_number($cell, $this->chars[$x]))
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
                $possible[$x] = self::determine_possible_values($x, $this->sudoku);
                if (count($possible[$x]) == 0)
                {
                    return (false);
                    break;
                }
            }
        }

        return $possible;
    }

    private function remove_attempt($attempt_array, $number)
    {
        $new_array = array();
        for ($x = 0; $x < count($attempt_array); $x++)
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
        $max = $this->limit;
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
        $start     = self::microtime();
        self::build();

        $x         = 0;
        $saved     = array();
        $saved_sud = array();
        while (!self::is_solved_sudoku())
        {
            $x++;
            $next_move = self::scan_sudoku_for_unique();

            if ($next_move == false)
            {
                $next_move    = array_pop($saved);
                $this->sudoku = array_pop($saved_sud);
            }

            $what_to_try = self::next_random($next_move);
            $attempt     = self::determine_random_possible_value($next_move, $what_to_try);


            if (count($next_move[$what_to_try]) > 1)
            {
                $next_move[$what_to_try] = self::remove_attempt($next_move[$what_to_try], $attempt);
                array_push($saved, $next_move);
                array_push($saved_sud, $this->sudoku);
            }
            $this->sudoku[$what_to_try] = $attempt;
        }

        $timing       = self::microtime() - $start;
        $this->result = array(
            'created_in' => round($timing,2),
            'difficulty' => 'N/A'
        );

        self::draw();
    }


    public function draw()
    {


        echo "<table cellpadding='0' cellspacing='0' style='margin:0 auto;font:30px Arial;border:3px solid black'>";

        for ($u = 0; $u < $this->limit; $u++)
        {


            if (($u + 1) % $this->sq == '0')
            {
                $border = 3;
            } else {
                $border = 1;
            }

            echo '<tr>';

            for ($v = 0; $v < $this->limit; $v++)
            {


                if (($v + 1) % $this->sq == '0')
                {
                    $border2 = 3;
                } else {
                    $border2 = 1;
                }

                echo '<td
                            data-row="'.$u.'"
                            data-col="'.$v.'"
                            style="border: 1px solid black;border-bottom:' . $border . 'px solid black;border-right:' . $border2 . 'px solid black;line-height: 50px; width: 50px; text-align: center; vertical-align: middle;">';
                echo($this->sudoku[$v * $this->limit + $u]);
                echo '</td>';

            }

            echo '</tr>';

        }

        echo "</table>";

        echo $this->result['created_in'].' seconds';

    }

}