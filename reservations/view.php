<?php

session_start();

require_once "../config/db.php";

if (!isset($_SESSION['customer_id'])) {
    die("Please log in first.");
}

$id = (int) ($_GET['id'] ?? 0);

$stmt = $pdo->prepare("
    SELECT b.*, r.room_number, r.room_type
    FROM bookings b
    JOIN rooms r ON b.room_id = r.room_id
    WHERE b.booking_id = :id
    AND b.customer_id = :customer_id
");

$stmt->execute([
    'id' => $id,
    'customer_id' => $_SESSION['customer_id']
]);

$reservation = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$reservation) {
    die("Reservation not found.");
}

?>

<!DOCTYPE html>
<html>
<head>

    <title>Reservation Details</title>

    <link rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css">

</head>

<body>

<div class="container mt-5">

    <div class="card p-4 shadow">

        <h2>Reservation Details</h2>

        <hr>

        <p><strong>Booking Reference:</strong>
            <?= htmlspecialchars($reservation['booking_reference']) ?>
        </p>

        <p><strong>Room:</strong>
            <?= htmlspecialchars($reservation['room_number']) ?>
        </p>

        <p><strong>Room Type:</strong>
            <?= htmlspecialchars($reservation['room_type']) ?>
        </p>

        <p><strong>Check-in:</strong>
            <?= htmlspecialchars($reservation['check_in']) ?>
        </p>

        <p><strong>Check-out:</strong>
            <?= htmlspecialchars($reservation['check_out']) ?>
        </p>

        <p><strong>Total:</strong>
            KSh <?= number_format($reservation['total_amount'], 2) ?>
        </p>

        <p><strong>Status:</strong>
            <?= htmlspecialchars($reservation['booking_status']) ?>
        </p>

        <?php if (in_array($reservation['booking_status'], ['pending', 'confirmed'], true)): ?>

            <a href="edit.php?id=<?= $id ?>" class="btn btn-warning">
                Modify Reservation
            </a>

            <a href="cancel.php?id=<?= $id ?>" class="btn btn-danger mt-2">
                Cancel Reservation
            </a>

        <?php endif; ?>

    </div>

</div>

</body>
</html>