<?php

session_start();

require_once "../config/db.php";

if (!isset($_SESSION['customer_id'])) {
    die("Please log in first.");
}

$customerId = $_SESSION['customer_id'];

$status = $_GET['status'] ?? '';

$sql = "
    SELECT
        b.booking_id,
        b.booking_reference,
        b.check_in,
        b.check_out,
        b.total_amount,
        b.booking_status,
        r.room_number,
        r.room_type

    FROM bookings b

    JOIN rooms r ON b.room_id = r.room_id

    WHERE b.customer_id = :customer_id
";

$params = ['customer_id' => $customerId];

if (in_array($status, [
    'pending',
    'confirmed',
    'completed',
    'cancelled'
], true)) {

    $sql .= " AND b.booking_status = :status";

    $params['status'] = $status;
}

$sql .= " ORDER BY b.booking_date DESC";

$stmt = $pdo->prepare($sql);

$stmt->execute($params);

$reservations = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html>
<head>

    <title>My Reservations</title>

    <link rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css">

</head>

<body class="bg-light">

<div class="container mt-5">

    <h2>My Reservations</h2>

    <form method="GET" class="mb-4">

        <select name="status" class="form-select w-25 d-inline">

            <option value="">All Reservations</option>

            <option value="pending">Pending</option>

            <option value="confirmed">Confirmed</option>

            <option value="completed">Completed</option>

            <option value="cancelled">Cancelled</option>

        </select>

        <button class="btn btn-primary">Filter</button>

    </form>


    <table class="table table-bordered table-striped">

        <thead class="table-dark">

            <tr>
                <th>Reference</th>
                <th>Room</th>
                <th>Check-in</th>
                <th>Check-out</th>
                <th>Total</th>
                <th>Status</th>
                <th>Action</th>
            </tr>

        </thead>

        <tbody>

        <?php foreach ($reservations as $reservation): ?>

            <tr>

                <td><?= htmlspecialchars($reservation['booking_reference']) ?></td>

                <td><?= htmlspecialchars($reservation['room_number']) ?></td>

                <td><?= htmlspecialchars($reservation['check_in']) ?></td>

                <td><?= htmlspecialchars($reservation['check_out']) ?></td>

                <td>KSh <?= number_format($reservation['total_amount'], 2) ?></td>

                <td><?= htmlspecialchars($reservation['booking_status']) ?></td>

                <td>

                    <a href="view.php?id=<?= $reservation['booking_id'] ?>"
                       class="btn btn-sm btn-info">
                        View
                    </a>

                </td>

            </tr>

        <?php endforeach; ?>

        </tbody>

    </table>

</div>

</body>
</html>