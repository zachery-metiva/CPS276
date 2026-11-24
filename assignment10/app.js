/*
  CPS276 - Assignment 10: JavaScript Part 2 (DOM, Events, and JSON)
  A gradebook on a Bootstrap page: the starter roster renders on load,
  the form adds students with validation, the table can be filtered,
  and the summary and JSON preview stay in sync with the array.

  ===========================================================================
  ASSIGNMENT 10 QUESTIONS AND ANSWERS
  ===========================================================================

  1. Why must the submit handler call event.preventDefault()? What would the
     user see if you left it out?

     The default action of a form submit is to send the form data to the
     server and load a new page. Calling event.preventDefault() first stops
     that default action so our JavaScript can handle the submit instead.
     If it were left out, the user would see the page reload: the address
     bar would gain a query string, the script would start over from the
     beginning, and the roster would reset to the five starter students.
     Any student they had just added, and any error message, would be gone,
     because all of our added data lives only in the students array in
     memory, and a reload throws that memory away.

  2. Why does this course use textContent for names, emails, and the JSON
     preview instead of innerHTML?

     textContent treats whatever string it is given as plain text, never as
     markup. innerHTML parses the string as HTML, so if a user typed
     something like an <img> tag with an onerror attribute or a <script>
     tag into the name field, innerHTML would turn that text into live
     elements. That is a cross-site scripting (XSS) hole. With textContent
     the same input is just shown literally as characters on the page, so
     user-entered data can never run code or break the page structure. It
     is also simpler and faster because the browser skips HTML parsing.

  3. In renderGradebook, why clear the table body and rebuild the rows from
     the array instead of only appending the newest student?

     Because the table is a view of the array, and what should be visible
     can change in ways appending cannot handle. When the filter changes to
     Passing or Failing, rows have to be removed as well as added; just
     appending could never remove Drew Patel from a Passing view. Clearing
     and rebuilding also prevents duplicate rows (re-rendering twice would
     otherwise show every student twice) and guarantees the table always
     matches the current array plus the current filter, no matter what
     happened before. Rebuilding from the data is the simple way to keep
     the page and the data from drifting apart.

  4. Why is the students array the source of truth, rather than whatever
     happens to be on the page?

     The array is real data: objects with a string name, a string email,
     and a numeric score. The page is only a picture of that data, and it
     is a lossy picture: the table shows the cleaned email instead of the
     original, scores have been turned into text, and the grade and status
     badges are derived values. If we tried to read the roster back out of
     the DOM we would be scraping strings out of table cells, which breaks
     the moment the layout changes and loses the original types and values.
     Because every feature (the filter, the average, the counts, and the
     JSON preview) computes from the same array, they always agree with
     each other. Change the array, re-render, and every view updates.

  5. What does JSON.stringify do here, and why show it on the page? Why
     would you still not store real course grades only in JavaScript?

     JSON.stringify(students, null, 2) converts the students array into a
     JSON-formatted string; the null, 2 arguments pretty-print it with
     2-space indentation. Showing that string in the <pre> element lets us
     watch the actual data structure change as students are added, which
     proves the array is the source of truth without opening the console.
     But real grades could never live only in JavaScript: the array exists
     only in this one browser tab's memory, so a refresh erases it, no one
     else can see it, and any student could open DevTools and rewrite their
     own score. Real grade data has to be stored on a server, in a
     database, behind authentication, with JavaScript only displaying it.
*/

const students = [
  { name: "Ava Chen", email: "  Ava.Chen@WCCNet.edu ", score: 94 },
  { name: "Ben Ortiz", email: "BEN.ORTIZ@wccnet.edu", score: 81 },
  { name: "Cara Singh", email: "  cara.singh@WCCNET.EDU", score: 73 },
  { name: "Drew Patel", email: " drew.patel@wccnet.edu ", score: 66 },
  { name: "Elena Brooks", email: "ELENA.BROOKS@WCCNet.edu", score: 88 }
];

const studentForm = document.querySelector("#studentForm");
const nameInput = document.querySelector("#nameInput");
const emailInput = document.querySelector("#emailInput");
const scoreInput = document.querySelector("#scoreInput");
const formError = document.querySelector("#formError");
const filterSelect = document.querySelector("#filterSelect");
const studentTableBody = document.querySelector("#studentTableBody");
const averageDisplay = document.querySelector("#averageDisplay");
const passedDisplay = document.querySelector("#passedDisplay");
const failedDisplay = document.querySelector("#failedDisplay");
const jsonPreview = document.querySelector("#jsonPreview");

function cleanEmail(rawEmail) {
  return rawEmail.trim().toLowerCase();
}

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

function statusLabel(score) {
  if (score >= 70) {
    return "pass";
  } else {
    return "fail";
  }
}

function classAverage(list) {
  let total = 0;
  for (const student of list) {
    total += student.score;
  }
  return total / list.length;
}

function getFilteredStudents(list, filterValue) {
  if (filterValue === "pass") {
    return list.filter((student) => student.score >= 70);
  }
  if (filterValue === "fail") {
    return list.filter((student) => student.score < 70);
  }
  return list;
}

function addStudentRow(student) {
  const row = document.createElement("tr");

  const nameCell = document.createElement("td");
  nameCell.textContent = student.name;

  const emailCell = document.createElement("td");
  emailCell.textContent = cleanEmail(student.email);

  const scoreCell = document.createElement("td");
  scoreCell.textContent = student.score;

  const gradeCell = document.createElement("td");
  const gradeBadge = document.createElement("span");
  const grade = letterGrade(student.score);
  const gradeClasses = {
    A: "badge text-bg-success",
    B: "badge text-bg-primary",
    C: "badge text-bg-warning",
    F: "badge text-bg-danger"
  };
  gradeBadge.className = gradeClasses[grade];
  gradeBadge.textContent = grade;
  gradeCell.appendChild(gradeBadge);

  const statusCell = document.createElement("td");
  const statusBadge = document.createElement("span");
  const status = statusLabel(student.score);
  if (status === "pass") {
    statusBadge.className = "badge text-bg-success";
  } else {
    statusBadge.className = "badge text-bg-danger";
  }
  statusBadge.textContent = status;
  statusCell.appendChild(statusBadge);

  row.appendChild(nameCell);
  row.appendChild(emailCell);
  row.appendChild(scoreCell);
  row.appendChild(gradeCell);
  row.appendChild(statusCell);

  studentTableBody.appendChild(row);
}

function updateSummary(list) {
  averageDisplay.textContent = classAverage(list).toFixed(1);

  let passed = 0;
  let failed = 0;
  for (const student of list) {
    if (statusLabel(student.score) === "pass") {
      passed++;
    } else {
      failed++;
    }
  }
  passedDisplay.textContent = passed;
  failedDisplay.textContent = failed;
}

function updateJson(list) {
  jsonPreview.textContent = JSON.stringify(list, null, 2);
}

function renderGradebook() {
  studentTableBody.textContent = "";

  const filtered = getFilteredStudents(students, filterSelect.value);
  for (const student of filtered) {
    addStudentRow(student);
  }

  updateSummary(students);
  updateJson(students);
}

studentForm.addEventListener("submit", (event) => {
  event.preventDefault();
  formError.textContent = "";

  const name = nameInput.value.trim();
  const email = emailInput.value.trim();
  const scoreText = scoreInput.value.trim();
  const score = Number(scoreText);

  if (name === "") {
    formError.textContent = "Please enter a name.";
    return;
  }

  if (email === "" || !email.includes("@")) {
    formError.textContent = "Please enter an email that includes @.";
    return;
  }

  if (scoreText === "" || Number.isNaN(score) || score < 0 || score > 100) {
    formError.textContent = "Please enter a score from 0 to 100.";
    return;
  }

  students.push({ name: name, email: cleanEmail(email), score: score });

  nameInput.value = "";
  emailInput.value = "";
  scoreInput.value = "";

  renderGradebook();
});

filterSelect.addEventListener("change", () => {
  renderGradebook();
});

renderGradebook();
