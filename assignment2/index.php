<?php
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
