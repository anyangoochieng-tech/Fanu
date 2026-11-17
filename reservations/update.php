<?php

session_start();

require_once "functions.php";

if (!isset($_SESSION['customer_id'])) {
    die("Please log in first.");
}

$id = (int) ($_POST['booking_id'] ?? 0);

$checkIn = $_POST['check_in'] ?? '';
$checkOut = $_POST['check_out'] ?? '';

try {

    $pdo->beginTransaction();

    $stmt = $pdo->prepare("
        SELECT room_id
        FROM bookings
        WHERE booking_id = :id
        AND customer_id = :customer_id
        AND booking_status IN ('pending', 'confirmed')
        FOR UPDATE
    ");

    $stmt->execute([
        'id' => $id,
        'customer_id' => $_SESSION['customer_id']
    ]);

    $booking = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$booking) {
        throw new Exception("Reservation not found.");
    }

    $roomId = $booking['room_id'];

    $lock = $pdo->prepare(
        "SELECT pg_advisory_xact_lock(:room_id)"
    );

    $lock->execute(['room_id' => $roomId]);

    if ($checkIn < date('Y-m-d') || $checkOut <= $checkIn) {
        throw new Exception("Invalid reservation dates.");
    }

    if (!isRoomAvailable($roomId, $checkIn, $checkOut, $id)) {
        throw new Exception("The selected dates are unavailable.");
    }

    $cost = calculateReservationTotal($roomId, $checkIn, $checkOut);

    $stmt = $pdo->prepare("
        UPDATE bookings
        SET check_in = :check_in,
            check_out = :check_out,
            total_amount = :total
        WHERE booking_id = :id
    ");

    $stmt->execute([
        'check_in' => $checkIn,
        'check_out' => $checkOut,
        'total' => $cost['total'],
        'id' => $id
    ]);

    $pdo->commit();

    header("Location: view.php?id=" . $id);
    exit;

} catch (Exception $e) {

    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    echo "Error: " . htmlspecialchars($e->getMessage());

}

?>