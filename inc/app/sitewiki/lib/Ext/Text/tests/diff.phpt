--TEST--
Text_Diff: Basic diff operation
--FILE--
<?php
include_once 'Text/Diff.php';
include_once 'Text/Diff/Renderer.php';
include_once 'Text/Diff/Renderer/unified.php';

$lines1 = file(dirname(__FILE__) . '/1.txt');
$lines2 = file(dirname(__FILE__) . '/2.txt');

$diff = &new Text_Diff($lines1, $lines2);

print_r($diff);
?>
--EXPECT--
text_diff Object
(
    [_edits] => Array
        (
            [0] => text_diff_op_copy Object
                (
                    [orig] => Array
                        (
                            [0] => This line is the same.
                        )

                    [final] => Array
                        (
                            [0] => This line is the same.
                        )

                )

            [1] => text_diff_op_change Object
                (
                    [orig] => Array
                        (
                            [0] => This line is different in 1.txt
                        )

                    [final] => Array
                        (
                            [0] => This line is different in 2.txt
                        )

                )

            [2] => text_diff_op_copy Object
                (
                    [orig] => Array
                        (
                            [0] => This line is the same.
                        )

                    [final] => Array
                        (
                            [0] => This line is the same.
                        )

                )

        )

)
