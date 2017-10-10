# [Sudoxu](http://sudoxu.com)

Sudoxu is a light and easy to use PHP sudoku genarator class. It's able to create up to 6^2sq sudokus easily.

http://sudoxu.com

### Example usage

You can harshly insert an HTML table via this code or you can export it as JSON, HTML, SERIALIZED.

###### Example 1
```php
require_once 'class.sudoxu.php';

/** @var $sudoxu sudoxu */
$sudoxu = new sudoxu();

$sudoxu->generate()->draw();
```

or

###### Example 2
```php
require_once 'class.sudoxu.php';

/** @var $sudoxu sudoxu */
$sudoxu = new sudoxu();

$export = $sudoxu->generate()->to('json');

var_dump($export);
```