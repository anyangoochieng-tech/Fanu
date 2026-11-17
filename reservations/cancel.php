<?php

session_start();

require_once "../config/db.php";

if (!isset($_SESSION['customer_id'])) {
    die("Please log in first.");
}

$id = (int) ($_GET['id'] ?? 0);

$stmt = $pdo->prepare("
    SELECT booking_reference
    FROM bookings
    WHERE booking_id = :id
    AND customer_id = :customer_id
    AND booking_status IN ('pending', 'confirmed')
");

$stmt->execute([
    'id' => $id,
    'customer_id' => $_SESSION['customer_id']
]);

$booking = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$booking) {
    die("Reservation not found.");
}

?>

<!DOCTYPE html>
<html>
<head>

    <title>Cancel Reservation</title>

    <link rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css">

</head>

<body>

<div class="container mt-5">

    <div class="card p-4 shadow">

        <h3>Confirm Cancellation</h3>

        <p>
            Are you sure you want to cancel reservation
            <strong><?= htmlspecialchars($booking['booking_reference']) ?></strong>?
        </p>

        <form action="confirm-cancellation.php" method="POST">

            <input type="hidden" name="booking_id"
                   value="<?= $id ?>">

            <button class="btn btn-danger">
                Yes, Cancel Reservation
            </button>

            <a href="view.php?id=<?= $id ?>" class="btn btn-secondary">
                Go Back
            </a>

        </form>

    </div>

</div>

</body>
</html>