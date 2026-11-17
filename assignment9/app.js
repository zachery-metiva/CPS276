/*
  CPS276 - Assignment 9: JavaScript Part 1
  A console gradebook: cleans emails, assigns letter grades, decides who
  passed, computes the class average, and logs a formatted report.

  ===========================================================================
  ASSIGNMENT 9 QUESTIONS AND ANSWERS
  ===========================================================================

  1. Why did you write letterGrade as a function instead of copying the same
     if / else for every student?

     Because the grading rules are the same for every student, the logic only
     needs to exist in one place. Writing letterGrade once and calling it for
     each student means less code, no copy/paste mistakes, and one single spot
     to update if the grade cutoffs ever change. If I had copied the if / else
     five times and the "B" cutoff changed, I would have to fix it in five
     places and could easily miss one.

  2. What is the difference between const and let? Where did you use each in
     this assignment, and why?

     const declares a variable that cannot be reassigned after it is given a
     value. let declares a variable whose value is allowed to change. In this
     assignment I used const for almost everything: the course name, the
     students array, the function parameters' derived values, and each loop's
     per-student values, because none of them get reassigned. I used let only
     inside classAverage for the running total (let total = 0), because the
     total must change as each score is added. The course rule is const by
     default, let only when a value must change, and no var at all.

  3. Why does this course prefer === over ==? Give a short example of how ==
     can surprise you.

     === compares both value and type with no conversion, so the result is
     predictable. == performs type coercion first, which can produce
     surprising matches. Example: 0 == "" is true and "70" == 70 is true,
     even though a string is not a number. With ===, 0 === "" is false and
     "70" === 70 is false, which is what you would expect. Using === means
     the comparison never silently converts types behind your back.

  4. In passingNames, explain what filter, map, and join each do to the
     student list.

     filter goes through the students array and keeps only the students whose
     score is 70 or higher, returning a new, smaller array of passing student
     objects. map takes that filtered array and transforms each student
     object into just its name string, returning a new array of names. join
     takes the array of names and glues them into one single string with
     ", " between each name. The original students array is never changed;
     each step returns a new value.

  5. Why is this assignment console-only? What will Chapter 12 add? Why would
     you still not store real course grades only in JavaScript?

     It is console-only because we are practicing core JavaScript (functions,
     arrays, objects, template literals) without the extra complexity of
     updating the page. Chapter 12 adds the DOM, which lets JavaScript select
     HTML elements and write output into the page itself instead of the
     console. Even then, you would not store real grades only in JavaScript:
     everything in the browser lives on the user's machine, so it disappears
     on reload, is not shared between users, and can be viewed or edited by
     anyone who opens DevTools. Real grades need a server and a database with
     authentication, where the data is persistent and protected.
  ===========================================================================
*/

const course = "CPS276";

const students = [
    { name: "Ava Chen", email: " Ava.Chen@WCCNet.edu ", score: 94 },
    { name: "Ben Ortiz", email: "BEN.ORTIZ@wccnet.edu", score: 81 },
    { name: "Cara Singh", email: " cara.singh@WCCNET.EDU", score: 73 },
    { name: "Drew Patel", email: " drew.patel@wccnet.edu ", score: 66 },
    { name: "Elena Brooks", email: "ELENA.BROOKS@WCCNet.edu", score: 88 }
];

// Return the email after trim() and toLowerCase().
function cleanEmail(rawEmail) {
    return rawEmail.trim().toLowerCase();
}

// Return "A" for 90+, "B" for 80-89, "C" for 70-79, otherwise "F".
function letterGrade(score) {
    if (score >= 90) {
        return "A";
    } else if (score >= 80) {
        return "B";
    } else if (score >= 70) {
        return "C";
    } else {
        return "F";
    }
}

// Return "pass" if the score is greater than or equal to 70, otherwise "fail".
function statusLabel(score) {
    if (score >= 70) {
        return "pass";
    }
    return "fail";
}

// Return the average of all student scores as a number.
function classAverage(list) {
    let total = 0;
    for (const student of list) {
        total += student.score;
    }
    return total / list.length;
}

// Return a comma-separated string of names for students who passed (score >= 70).
function passingNames(list) {
    return list
        .filter((student) => student.score >= 70)
        .map((student) => student.name)
        .join(", ");
}

// Log the full gradebook report to the console.
function printReport(list) {
    console.log(`=== ${course} Gradebook ===`);
    console.log("");

    for (const student of list) {
        console.log(
            `${student.name} | ${cleanEmail(student.email)} | ${student.score} | ${letterGrade(student.score)} | ${statusLabel(student.score)}`
        );
    }

    console.log("");

    const passedCount = list.filter((student) => student.score >= 70).length;
    const failedCount = list.length - passedCount;

    console.log(`Class average: ${classAverage(list)}`);
    console.log(`Passing students: ${passingNames(list)}`);
    console.log(`Passed: ${passedCount} | Failed: ${failedCount}`);
}

printReport(students);
