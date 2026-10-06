<?php
/*
    processNames.php - Assignment 3: Name List
    Handles the form submit: adds a name (converted to "Last, First"),
    sorts the list, or clears the list. Then it includes index.php to
    redisplay the form with the updated textarea.

    ---------------------------------------------------------------
    VIDEO QUESTIONS AND ANSWERS
    ---------------------------------------------------------------
    1. What is the purpose of separating the functionality between
       index.php and processNames.php in this assignment?

       It separates the presentation (the HTML form in index.php) from
       the processing logic (this file). index.php only worries about
       displaying the page, while processNames.php handles the POST
       data, builds the sorted list, and then includes index.php to
       show the result. This makes each file simpler, easier to read,
       and easier to maintain or reuse.

    2. How does the $_SERVER["REQUEST_METHOD"] variable help determine
       when to process form submissions in PHP?

       $_SERVER["REQUEST_METHOD"] tells you how the page was requested.
       When someone just types the URL or follows a link it is "GET".
       When the form is submitted it is "POST". By checking for "POST"
       we only run the processing code on an actual form submit, and we
       can send anyone who opens processNames.php directly back to the
       form instead of running logic with no data.

    3. How does PHP handle string-to-array conversion using the explode
       function, and why is this useful in this application?

       explode(delimiter, string) splits a string into an array at each
       occurrence of the delimiter. We use it twice here: once to split
       the typed name on the space (" ") so we get the first and last
       name as separate array elements and can flip them to
       "Last, First", and once to split the textarea contents on the
       newline ("\n") so each existing name becomes its own array
       element that we can sort with sort().

    4. What role does the implode function play in formatting the
       output for the textarea?

       implode is the opposite of explode: it joins the array of names
       back into one string, putting "\n" between each element. A
       textarea can only display a string, not an array, so implode
       turns the sorted array into the multi-line text that appears in
       the textarea, one name per line.

    5. How does processNames.php determine whether to add a new name or
       clear all names based on which button was clicked?

       Both buttons are submit buttons with different name attributes
       (name="addName" and name="clearNames"). Only the button that was
       clicked gets sent in the POST data, so we check
       isset($_POST['addName']) and isset($_POST['clearNames']) to know
       which action to perform.
    ---------------------------------------------------------------
*/

$namesList = '';

// Only process if the form was actually submitted (POST),
// not if someone just opened this page directly in the browser.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (isset($_POST['clearNames'])) {
        // Clear Names clicked: leave the list empty.
        $namesList = '';

    } elseif (isset($_POST['addName'])) {

        // Start with the names already in the textarea.
        $existing = trim($_POST['nameList'] ?? '');
        $namesArray = [];
        if ($existing !== '') {
            // Split the textarea into one array element per line.
            $namesArray = explode("\n", $existing);
            // Clean up any stray whitespace/carriage returns.
            $namesArray = array_map('trim', $namesArray);
            $namesArray = array_filter($namesArray);
        }

        // Convert the typed name from "First Last" to "Last, First".
        $typedName = trim($_POST['name'] ?? '');
        if ($typedName !== '') {
            $nameParts = explode(' ', $typedName);
            if (count($nameParts) >= 2) {
                $firstName = array_shift($nameParts);          // first word
                $lastName  = implode(' ', $nameParts);         // rest = last name
                $namesArray[] = $lastName . ', ' . $firstName;
            } else {
                // Only one word typed: just add it as is.
                $namesArray[] = $typedName;
            }
        }

        // Sort alphabetically and rebuild the textarea string.
        sort($namesArray);
        $namesList = implode("\n", $namesArray);
    }

    // Redisplay the form with the updated list.
    include 'index.php';

} else {
    // Page was opened directly with GET: send the user to the form.
    header('Location: index.php');
    exit;
}
