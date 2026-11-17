<?php
require_once __DIR__ . '/../config/db.php';

// Create a new reservation
function createReservation($pdo, $customerId, $roomId, $checkIn, $checkOut)
{
    try {

        // Validate dates
        $start = new DateTime($checkIn);
        $end = new DateTime($checkOut);

        if ($start >= $end) {
            throw new Exception("Check-out date must be after check-in date.");
        }

        if ($start < new DateTime('today')) {
            throw new Exception("Check-in date cannot be in the past.");
        }

        $pdo->beginTransaction();

        // Lock the room while checking and creating the reservation
        $stmt = $pdo->prepare("
            SELECT r.room_id, rt.price_per_night
            FROM rooms r
            JOIN room_types rt ON r.room_type_id = rt.room_type_id
            WHERE r.room_id = ?
            FOR UPDATE
        ");

        $stmt->execute([$roomId]);

        $room = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$room) {
            throw new Exception("Room does not exist.");
        }

        // Check availability using the shared function from Tamara
        // Confirm its file location and implementation with her.
        if (!function_exists('isRoomAvailable')) {
            throw new Exception("Room availability function is not available.");
        }

        if (!isRoomAvailable($roomId, $checkIn, $checkOut)) {
            throw new Exception("This room is not available for the selected dates.");
        }

        // Calculate number of nights
        $nights = $start->diff($end)->days;

        // Calculate total cost
        $total = $nights * $room['price_per_night'];

        // Save reservation as Pending
        $stmt = $pdo->prepare("
            INSERT INTO reservations
            (customer_id, room_id, check_in, check_out,
             total_amount, status, created_at)
            VALUES (?, ?, ?, ?, ?, 'Pending', NOW())
        ");

        $stmt->execute([
            $customerId,
            $roomId,
            $checkIn,
            $checkOut,
            $total
        ]);

        // Get generated reservation ID
        $reservationId = $pdo->lastInsertId();

        $pdo->commit();

        return $reservationId;

    } catch (Exception $e) {

        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }

        throw $e;
    }
}


// Confirm reservation after successful payment
function confirmReservation($pdo, $reservationId)
{
    $stmt = $pdo->prepare("
        UPDATE reservations
        SET status = 'Confirmed'
        WHERE reservation_id = ?
        AND status = 'Pending'
    ");

    $stmt->execute([$reservationId]);

    return $stmt->rowCount() > 0;
}


// Cancel reservation
function cancelReservation($pdo, $reservationId, $customerId)
{
    $stmt = $pdo->prepare("
        UPDATE reservations
        SET status = 'Cancelled'
        WHERE reservation_id = ?
        AND customer_id = ?
        AND status IN ('Pending', 'Confirmed')
    ");

    $stmt->execute([$reservationId, $customerId]);

    return $stmt->rowCount() > 0;
}

?>