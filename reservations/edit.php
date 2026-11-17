<?php

session_start();

require_once "../config/db.php";

if (!isset($_SESSION['customer_id'])) {
    die("Please log in first.");
}

$id = (int) ($_GET['id'] ?? 0);

$stmt = $pdo->prepare("
    SELECT *
    FROM bookings
    WHERE booking_id = :id
    AND customer_id = :customer_id
    AND booking_status IN ('pending', 'confirmed')
");

$stmt->execute([
    'id' => $id,
    'customer_id' => $_SESSION['customer_id']
]);

$reservation = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$reservation) {
    die("Reservation not found or cannot be modified.");
}

?>

<!DOCTYPE html>
<html>
<head>

    <title>Modify Reservation</title>

    <link rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css">

</head>

<body>

<div class="container mt-5">

    <div class="card p-4">

        <h2>Modify Reservation</h2>

        <form action="update.php" method="POST">

            <input type="hidden" name="booking_id"
                   value="<?= $id ?>">

            <div class="mb-3">

                <label>Check-in Date</label>

                <input type="date"
                       name="check_in"
                       class="form-control"
                       min="<?= date('Y-m-d') ?>"
                       value="<?= htmlspecialchars($reservation['check_in']) ?>"
                       required>

            </div>

            <div class="mb-3">

                <label>Check-out Date</label>

                <input type="date"
                       name="check_out"
                       class="form-control"
                       min="<?= date('Y-m-d', strtotime('+1 day')) ?>"
                       value="<?= htmlspecialchars($reservation['check_out']) ?>"
                       required>

            </div>

            <button class="btn btn-primary">
                Update Reservation
            </button>

        </form>

    </div>

</div>

</body>
</html>