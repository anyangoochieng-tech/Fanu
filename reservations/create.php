<?php

session_start();

require_once "../config/db.php";

$stmt = $pdo->query("
    SELECT room_id, room_number, room_type,
           price_per_night, capacity
    FROM rooms
    WHERE status = 'available'
    ORDER BY room_number
");

$rooms = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html>
<head>

    <title>Create Reservation</title>

    <link rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css">

</head>

<body class="bg-light">

<div class="container mt-5">

    <div class="card shadow p-4">

        <h2 class="mb-4">Book a Hotel Room</h2>

        <form action="store.php" method="POST">

            <div class="mb-3">

                <label class="form-label">Select Room</label>

                <select name="room_id" class="form-select" required>

                    <option value="">Choose a room</option>

                    <?php foreach ($rooms as $room): ?>

                        <option value="<?= $room['room_id'] ?>">

                            Room <?= htmlspecialchars($room['room_number']) ?>
                            -
                            <?= htmlspecialchars($room['room_type']) ?>
                            -
                            KSh <?= number_format($room['price_per_night'], 2) ?>
                            per night

                        </option>

                    <?php endforeach; ?>

                </select>

            </div>


            <div class="mb-3">

                <label class="form-label">Check-in Date</label>

                <input type="date"
                       name="check_in"
                       class="form-control"
                       min="<?= date('Y-m-d') ?>"
                       required>

            </div>


            <div class="mb-3">

                <label class="form-label">Check-out Date</label>

                <input type="date"
                       name="check_out"
                       class="form-control"
                       min="<?= date('Y-m-d', strtotime('+1 day')) ?>"
                       required>

            </div>


            <div class="mb-3">

                <label class="form-label">Number of Guests</label>

                <input type="number"
                       name="guests"
                       class="form-control"
                       min="1"
                       value="1"
                       required>

            </div>


            <button type="submit" class="btn btn-primary">

                Reserve Room

            </button>

        </form>

    </div>

</div>

</body>
</html>