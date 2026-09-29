<?php
/*
============================================================
 ASSIGNMENT 2 QUESTIONS
============================================================

1. The assignment specifies that "all PHP written at the top above the
   HTML Doctype". Based on Chapter 4, explain how PHP is processed on the
   server before the resulting HTML is sent to the browser, and explain
   how the assignment's PHP variables can be created before they are
   echoed in the HTML body.

   ANSWER: When the browser requests index.php, the web server hands the
   file to the PHP interpreter. PHP runs top to bottom on the server:
   everything inside the PHP tags is executed, and everything outside them
   (the doctype, head, body) is passed through as plain text. The browser
   only ever receives the finished HTML, never the PHP code. Because the
   whole file is processed as one script, variables created at the top
   ($evenOutput, $form, $output) still exist when PHP reaches
   <?php echo $output; ?> in the body, so their values are dropped into
   the page at that spot.
   CODE: $output is built at lines 133-137 and echoed in the body at
   line 148.

2. Beyond simply finding even numbers, describe a scenario where you would
   use a similar foreach loop with a conditional (if) statement to filter
   or process elements from an array based on different criteria like
   finding all numbers divisible by 7.

   ANSWER: The same pattern works any time you only want some items from a
   list. Only the condition changes. For multiples of 7:
       foreach ($numbers as $number) {
           if ($number % 7 === 0) { $sevens[] = $number; }
       }
   Other examples: keeping products under a price limit from a shopping
   cart, listing students whose grade is below 70, or keeping only form
   fields that are not empty.
   CODE: the even-number loop is at lines 94-98. $number % 2 === 0
   means "the remainder after dividing by 2 is zero".

3. Explain when heredoc is useful for creating a multi-line PHP string such
   as the form in this assignment. How does heredoc allow you to write
   multiple lines of text and include variables in the string?

   ANSWER: Heredoc is useful when a string is a large block of HTML, like
   this form. It starts with <<<HTML and ends with HTML; on its own line.
   Everything between them is the string, line breaks included, so the
   HTML can be written exactly as it would look in an .html file. There is
   no need to wrap each line in quotes or escape double quotes in
   attributes like class="form-control". Heredoc works like a
   double-quoted string, so a variable such as $email or {$user['name']}
   placed inside it is replaced with its value.
   CODE: the form heredoc is at lines 103-112.

4. The createTable function uses nested for loops to build the table.
   Describe the role of each loop: which one is responsible for iterating
   through the rows, and which for the columns? How does the concatenation
   (.=) inside these loops incrementally build the complete HTML table
   string?

   ANSWER: The outer loop ($r from 1 to $rows) handles the rows. Each pass
   opens a <tr>, runs the inner loop, and closes the </tr>. The inner loop
   ($c from 1 to $cols) handles the columns. It adds one <td> cell per
   column to the current row. The .= operator appends text to the end of
   $table instead of replacing it. The string starts as the opening
   <table> tag, grows by one cell or row tag at a time, and gets its
   closing </table> tag after both loops finish. With 8 rows and 6
   columns, the inner line runs 48 times.
   CODE: the outer (row) loop is at line 119 and the inner (column)
   loop is at line 121.

5. The createTable() function returns a string that is later echoed.
   Explain what returning a value from a function does and how the
   returned table string can then be used by the code that calls
   createTable().

   ANSWER: return ends the function and sends a value back to the place
   where the function was called. The call createTable(8, 6) is replaced by
   the finished table string, just as if the string had been typed there.
   Because the function returns the table instead of echoing it, the caller
   decides what to do with it: here it is appended to $output and printed
   in the body later. It could also be stored in a variable, echoed twice,
   or built with different sizes (createTable(3, 4)) without changing the
   function.
   CODE: return $table; is at line 129 and the call is at line
   136.
============================================================
*/

// ---------- Even numbers ----------
$numbers = range(1, 50);
$evens = [];

foreach ($numbers as $number) {
    if ($number % 2 === 0) {
        $evens[] = $number;
    }
}

$evenOutput = '<p class="mb-3">Even Numbers: ' . implode(' - ', $evens) . '</p>';

// ---------- Bootstrap form ----------
$form = <<<HTML
<div class="mb-3">
    <label for="email" class="form-label">Email address</label>
    <input type="email" class="form-control" id="email" placeholder="name@example.com">
</div>
<div class="mb-3">
    <label for="textarea" class="form-label">Example textarea</label>
    <textarea class="form-control" id="textarea" rows="3"></textarea>
</div>
HTML;

// ---------- Table ----------
function createTable($rows, $cols)
{
    $table = '<table class="table table-bordered">';

    for ($r = 1; $r <= $rows; $r++) {
        $table .= '<tr>';
        for ($c = 1; $c <= $cols; $c++) {
            $table .= "<td>Row $r, Col $c</td>";
        }
        $table .= '</tr>';
    }

    $table .= '</table>';

    return $table;
}

// ---------- Page output ----------
$output = '<div class="container-fluid">';
$output .= $evenOutput;
$output .= $form;
$output .= createTable(8, 6);
$output .= '</div>';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Even Numbers, Form, and Table</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php echo $output; ?>
</body>
</html>
