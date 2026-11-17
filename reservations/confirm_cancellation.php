<?php

session_start();

require_once "functions.php";

if (!isset($_SESSION['customer_id'])) {
    die("Please log in first.");
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: my-reservations.php");
    exit;
}

$id = (int) ($_POST['booking_id'] ?? 0);

try {

    $cancelled = cancelReservation(
        $id,
        $_SESSION['customer_id']
    );

    if (!$cancelled) {
        throw new Exception("Reservation could not be cancelled.");
    }

    header("Location: my-reservations.php");
    exit;

} catch (Exception $e) {

    echo "Error: " . htmlspecialchars($e->getMessage());

}

?>
