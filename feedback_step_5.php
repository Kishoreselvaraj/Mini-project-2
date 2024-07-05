<?php
include "configASL.php";
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST)) {
    // Collect input data
    $roll = $_POST['roll'];
    $sub = $_POST['subject'];
    $faculty_id = $_POST['faculty_id'];
    $name = $_SESSION['name'];
    $q1 = $_POST['q1'];
    $q2 = $_POST['q2'];
    $q3 = $_POST['q3'];
    $q4 = $_POST['q4'];
    $q5 = $_POST['q5'];
    $q6 = $_POST['q6'];
    $q7 = $_POST['q7'];
    $q8 = $_POST['q8'];
    $q9 = $_POST['q9'];
    $q10 = $_POST['q10'];

    $total = $q1 + $q2 + $q3 + $q4 + $q5 + $q6 + $q7 + $q8 + $q9 + $q10;
    $percent = ($total / 50) * 100;
    $comment = $_POST['comment'];

    // Check if the feedback already exists
    $checkStmt = $al->prepare("SELECT * FROM feeds WHERE faculty_id = ? AND roll = ? AND subject = ?");
    $checkStmt->bind_param("sss", $faculty_id, $roll, $sub);
    $checkStmt->execute();
    $result = $checkStmt->get_result();
    
    if ($result->num_rows > 0) {
        // If feedback exists, update it
        $updateStmt = $al->prepare("UPDATE feeds SET q1 = ?, q2 = ?, q3 = ?, q4 = ?, q5 = ?, q6 = ?, q7 = ?, q8 = ?, q9 = ?, q10 = ?, total = ?, percent = ? WHERE faculty_id = ? AND roll = ? AND subject = ?");
        $updateStmt->bind_param("iiiiiiiiiiiidsss", $q1, $q2, $q3, $q4, $q5, $q6, $q7, $q8, $q9, $q10, $total, $percent, $faculty_id, $roll, $sub);
        $success = $updateStmt->execute();
        $updateStmt->close();
    } else {
        // If feedback does not exist, insert a new record
        $insertStmt = $al->prepare("INSERT INTO feeds (faculty_id, roll, name, subject, q1, q2, q3, q4, q5, q6, q7, q8, q9, q10, total, percent) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $insertStmt->bind_param("ssssiiiiiiiiiiid", $faculty_id, $roll, $name, $sub, $q1, $q2, $q3, $q4, $q5, $q6, $q7, $q8, $q9, $q10, $total, $percent);
        $success = $insertStmt->execute();
        $insertStmt->close();
    }
    $checkStmt->close();

    // Insert the comment (always add new comments)
    $commentStmt = $al->prepare("INSERT INTO comments (faculty_id, comment) VALUES (?, ?)");
    $commentStmt->bind_param("ss", $faculty_id, $comment);
    $commentStmt->execute();
    $commentStmt->close();

    if ($success) {
        echo "Feedback submitted";
    } else {
        echo "Error submitting feedback: " . $al->error;
    }
} else {
    echo "Invalid request.";
}
?>

		