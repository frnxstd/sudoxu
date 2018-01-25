# [Sudoxu](http://sudoxu.com)

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/frnxstd/sudoxu/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/frnxstd/sudoxu/?branch=master)
[![Build Status](https://scrutinizer-ci.com/g/frnxstd/sudoxu/badges/build.png?b=master)](https://scrutinizer-ci.com/g/frnxstd/sudoxu/build-status/master)
[![Code Intelligence Status](https://scrutinizer-ci.com/g/frnxstd/sudoxu/badges/code-intelligence.svg?b=master)](https://scrutinizer-ci.com/code-intelligence)

Sudoxu is a light and easy to use PHP sudoku genarator class. It's able to create up to 6^2sq sudokus easily.

http://sudoxu.com

### Installation

Run the command in your project folder:

```
composer require frnxstd/sudoxu
```


### Example usage

You can create it as ARRAY, JSON or SERIALIZED.

```php
<?php
require_once __DIR__ . '/../vendor/autoload.php'; // Autoload files using Composer autoload

use Sudoxu\Sudoku;

/** @var Sudoku $Sudoku */
$Sudoku    = new Sudoku();

// Example 1 Returns JSON
$json      = $Sudoku->generate('json');

// Example 2 Returns an array
$array     = $Sudoku->generate('array');

// Example 3 Returns Serialized
$serialize = $Sudoku->generate('serialize');
```