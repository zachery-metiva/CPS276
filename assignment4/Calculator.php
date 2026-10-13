<?php
/*
    Calculator.php - Calculator class
    The calc method takes an operator (+, -, *, /) and two numbers,
    validates them, runs the matching private method, and returns the
    result as an HTML paragraph.

    ---------------------------------------------------------------
    VIDEO QUESTIONS AND ANSWERS
    ---------------------------------------------------------------
    1. Explain the purpose of require_once "Calculator.php"; in the
       index.php page. What would be the difference if include or
       require were used instead of require_once?

       require_once pulls the Calculator class file into index.php so
       the class can be used there. The "require" part means the file
       is essential: if it cannot be loaded, PHP stops with a fatal
       error instead of running a page that is guaranteed to break.
       The "once" part means PHP remembers the file was already loaded
       and will not load it a second time, which prevents a fatal
       "cannot redeclare class Calculator" error if the file were
       included again. With include, a missing file only produces a
       warning and the page keeps running, then dies anyway when
       "new Calculator()" fails. With require, a missing file is still
       fatal (good), but loading the file twice would redeclare the
       class and crash. require_once gives us both protections.

    2. How does the divide method specifically prevent and report an
       error for division by zero? Why is this a critical
       consideration in calculator applications?

       Before dividing, the divide method checks if ($num2 == 0) and,
       when it is, returns the string "cannot divide a number by zero"
       instead of ever running the division. The division only happens
       in the else branch, so the dangerous operation is never reached
       with a zero. This matters because dividing by zero is
       mathematically undefined: in PHP 8 it throws a fatal
       DivisionByZeroError that would kill the whole page. A
       calculator must accept any numbers a user supplies, so zero as
       a divisor is guaranteed to happen eventually, and the
       application has to handle it gracefully with a message rather
       than crashing.

    3. Explain the difference between the Calculator class and the
       $Calculator object. Why do we create an instance of the class?

       The Calculator class is the blueprint: the code that defines
       what properties and methods (calc, add, subtract, multiply,
       divide) a calculator has. It does not do anything by itself.
       The $Calculator object is a concrete instance of that blueprint
       created with "new Calculator()": an actual thing in memory we
       can call methods on, like $Calculator->calc("*", 10, 2). We
       create an instance because the methods belong to objects, not
       to the class definition itself; instantiating also means we
       could create several independent calculators from the one
       blueprint if we needed them.

    4. Why is it important to check that the last two parameters are
       numbers in our Calculator class?

       The class validates with is_int() and is_float() so that only
       real numbers reach the math. Without that check, a value like
       "a" would reach an arithmetic operator, and in PHP 8 doing math
       on a non-numeric string throws a fatal TypeError that crashes
       the page. Validating first lets the class return a friendly
       error message instead. It is also a data-integrity issue: a
       calculator that silently treated bad input as 0 would produce
       wrong answers that look right, which is worse than an error.

    5. Index.php handles the display of the results using HTML, while
       Calculator.php contains the core calculation logic. Discuss the
       importance of separating user interface (presentation) concerns
       from business logic.

       Separating presentation from business logic keeps each file
       focused on one job. Calculator.php can be tested, fixed, and
       reused on any page (or in a future form-based version) without
       touching HTML, and index.php can be redesigned without any risk
       of breaking the math. Changes stay local: a bug in rounding is
       fixed in one place, a layout change in another. It also makes
       the code easier to read and lets different people work on the
       logic and the page at the same time. This is the same principle
       behind larger patterns like MVC, just at the scale of two
       files.
    ---------------------------------------------------------------
*/

class Calculator
{
    // Takes an operator string and two numbers. Defaults of null let
    // us detect missing arguments instead of PHP throwing an error.
    public function calc($operator = null, $num1 = null, $num2 = null)
    {
        // All three arguments must be valid: a string operator that is
        // one of + - * /, and two values that are integers or floats.
        $validOperator = is_string($operator)
            && in_array($operator, ["+", "-", "*", "/"], true);
        $validNumbers = (is_int($num1) || is_float($num1))
            && (is_int($num2) || is_float($num2));

        if (!$validOperator || !$validNumbers) {
            return "<p>Cannot perform operation. You must have three arguments. "
                . "A string for the operator (+,-,*,/) and two integers or floats "
                . "for the numbers.</p>";
        }

        // Dispatch to the method that matches the operator.
        switch ($operator) {
            case "+":
                $answer = $this->add($num1, $num2);
                break;
            case "-":
                $answer = $this->subtract($num1, $num2);
                break;
            case "*":
                $answer = $this->multiply($num1, $num2);
                break;
            case "/":
                $answer = $this->divide($num1, $num2);
                break;
        }

        return "<p>The calculation is $num1 $operator $num2. "
            . "The answer is $answer.</p>";
    }

    private function add($num1, $num2)
    {
        return $num1 + $num2;
    }

    private function subtract($num1, $num2)
    {
        return $num1 - $num2;
    }

    private function multiply($num1, $num2)
    {
        return $num1 * $num2;
    }

    private function divide($num1, $num2)
    {
        // Never divide by zero: report it instead of crashing.
        if ($num2 == 0) {
            return "cannot divide a number by zero";
        }
        return $num1 / $num2;
    }
}
