<?php
namespace Sudoxu;

/**
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
            $stack = [
                '1','2','3','4','5','6','7','8','9',
                '0','A','B','C','D','E','F','G','H',
                'I','J','K','L','M','N','O','P','Q',
                'R','S','T','U','V','W','X','Y','Z'
            ];

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

        return $this;

    }

    /**
     * Limit setter function
     *
     * @param int $number
     * @return $this
     */
    public function set($number)
    {
        $this->limit = $number;
        $this->sq    = (int)sqrt($this->limit);

        return $this;
    }

    /**
     * Position of the cell in X-axis
     *
     * @param int $cell
     * @return int
     */
    private function return_row($cell)
    {
        return (int) floor($cell / $this->limit);
    }

    /**
     * Position of the cell in Y-axis
     *
     * @param int $cell
     * @return int
     */
    private function return_col($cell)
    {
        return (int) $cell % $this->limit;
    }

    /**
     * Position of the block
     *
     * @param int $cell
     * @return int
     */
    private function return_block($cell)
    {
        return (int) floor($this->return_row($cell) / $this->sq) * $this->sq + floor($this->return_col($cell) / $this->sq);
    }

    /**
     * Determine if this item unique in the row
     *
     * @param string $item
     * @param int    $row
     * @return bool
     */
    private function is_possible_row($item, $row)
    {
        $possible = true;
        for ($x = 0; $x < $this->limit; $x++)
        {
            if (isset($this->sudoku[$row * $this->limit + $x]) && $this->sudoku[$row * $this->limit + $x] == $item)
            {
                $possible = false;
            }
        }

        return $possible;
    }

    /**
     * Determine if this item unique in the column
     *
     * @param string $item
     * @param int    $col
     * @return bool
     */
    private function is_possible_col($item, $col)
    {
        $possible = true;
        for ($x = 0; $x < $this->limit; $x++)
        {
            if (isset($this->sudoku[$col + $this->limit * $x]) && $this->sudoku[$col + $this->limit * $x] == $item)
            {
                $possible = false;
            }
        }

        return $possible;
    }

    /**
     * Determine if this item unique in the square block
     *
     * @param string $item
     * @param int    $block
     * @return bool
     */
    private function is_possible_block($item, $block)
    {
        $possible = true;
        for ($x = 0; $x < $this->limit; $x++)
        {
            $index = floor($block / $this->sq) * $this->sq * $this->limit + $x % $this->sq + $this->limit * floor($x / $this->sq) + $this->sq * ($block % $this->sq);
            if (isset($this->sudoku[$index]) && $this->sudoku[$index] == $item)
            {
                $possible = false;
            }
        }

        return $possible;
    }

    /**
     * Determine if this item ok to place here
     *
     * @param $cell
     * @param $item
     * @return bool
     */
    private function is_possible_number($cell, $item)
    {
        $row   = $this->return_row($cell);
        $col   = $this->return_col($cell);
        $block = $this->return_block($cell);

        return ($this->is_possible_row($item, $row) && $this->is_possible_col($item, $col) && $this->is_possible_block($item, $block));
    }

    /**
     * Check if the row is unique
     *
     * @param int $row
     * @return bool
     */
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

    /**
     * Check if the column is unique
     *
     * @param int $col
     * @return bool
     */
    private function is_correct_col($col)
    {
        $col_temp = array();
        for ($x = 0; $x < $this->limit; $x++)
        {
            $col_temp[$x] = $this->sudoku[$col + $x * $this->limit];
        }
        return count(array_diff($this->chars, $col_temp)) == 0;
    }

    /**
     * Check if the block is unique
     *
     * @param int $block
     * @return bool
     */
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

    /**
     * Determine if the sudoku is created successfully
     *
     * @return bool
     */
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

    /**
     * Find possible items
     *
     * @param int $cell
     * @return array
     */
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

    /**
     * Random item from the possible item box
     *
     * @param array $possible
     * @param int   $cell
     * @return array
     */
    private function determine_random_possible_value($possible, $cell)
    {
        return $possible[$cell][rand(0, count($possible[$cell]) - 1)];
    }

    /**
     * Determine if everything goes well
     *
     * @return bool
     */
    private function scan_sudoku_for_unique()
    {
        $possible = false;
        for ($x = 0; $x < $this->limit * $this->limit; $x++)
        {
            if (!isset($this->sudoku[$x]))
            {
                $possible[$x] = $this->determine_possible_values($x);
                if (count($possible[$x]) == 0)
                {
                    return false;
                }
            }
        }

        return $possible;
    }

    /**
     * Remove used item
     *
     * @param array  $attempt_array
     * @param string $item
     * @return array
     */
    private function remove_attempt($attempt_array, $item)
    {
        $new_array = array();
        $count     = count($attempt_array);
        for ($x = 0; $x < $count; $x++)
        {
            if ($attempt_array[$x] != $item)
            {
                array_unshift($new_array, $attempt_array[$x]);
            }
        }
        return $new_array;
    }

    /**
     * Next move
     *
     * @param $possible
     * @return int|null
     */
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

    /**
     * Basically prepares the variables we are going to use
     */
    private function build()
    {
        $this->sudoku = array();
        $this->chars  = array();

        for ($i = 0; $i < $this->limit; $i++)
        {
            $this->chars[] = $this->stack[$i];
        }
    }


    /**
     * Microtime to calculate time difference
     *
     * @return float
     */
    public function microtime()
    {
        return microtime(true);
    }

    /**
     * Kickstarter function
     *
     * @param string $to
     * @return array|string
     */
    public function generate($to = 'json')
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
            'timestamp'  => time(),
            'difficulty' => 'N/A'
        );

        return $this->to($to);
    }

    /**
     * Prepares the defaults of the returned value
     *
     * @return array
     */
    private function array2export()
    {
        return array(
            'sudoku' => $this->sudoku,
            'limit'  => $this->limit,
            'result' => $this->result,
        );
    }

    /**
     * Return type. JSON, SERIALIZED or an ARRAY
     *
     * @param string $to
     * @return array|string
     */
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
        // else if($to == 'array')
        return (array) $this->array2export();
    }
}