<?php
/*
    index.php - Assignment 3: Name List
    Displays the form. If processNames.php included this file after a submit,
    $namesList will already hold the sorted names; otherwise it starts empty.
*/
if (!isset($namesList)) {
    $namesList = '';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Add Names</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container-fluid mt-2">
        <h1>Add Names</h1>
        <form action="processNames.php" method="post">
            <button type="submit" name="addName" class="btn btn-primary">Add Name</button>
            <button type="submit" name="clearNames" class="btn btn-primary">Clear Names</button>

            <div class="mb-3 mt-2">
                <label for="name" class="form-label">Enter Name</label>
                <input type="text" class="form-control" id="name" name="name">
            </div>

            <div class="mb-3">
                <label for="nameList" class="form-label">List of Names</label>
                <textarea class="form-control" id="nameList" name="nameList" rows="20"><?php echo htmlspecialchars($namesList); ?></textarea>
            </div>
        </form>
    </div>
</body>
</html>
