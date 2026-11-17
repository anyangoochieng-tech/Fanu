<?php

session_start();

require_once "functions.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: create.php");
    exit;
}

/*
Assumption:
The customer must be logged in.
Angela will provide the authentication/session implementation.
*/

if (!isset($_SESSION['customer_id'])) {
    die("Please log in before making a reservation.");
}

try {

    $reservationId = createReservation(
        $_SESSION['customer_id'],
        (int) $_POST['room_id'],
        $_POST['check_in'],
        $_POST['check_out'],
        (int) $_POST['guests']
    );

    header("Location: view.php?id=" . $reservationId);
    exit;

} catch (Exception $e) {

    echo "<script>
        alert(" . json_encode($e->getMessage()) . ");
        window.location.href = 'create.php';
    </script>";

}