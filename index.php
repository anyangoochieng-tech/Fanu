<?php

$pageTitle = "Home | Hotel Room Reservation System";

require_once "config/db.php";
require_once "includes/header.php";

$availableRooms = 0;
$rooms = [];

try {

    // Count available rooms
    $countQuery = $pdo->query(
        "SELECT COUNT(*) FROM rooms WHERE status = 'available'"
    );

    $availableRooms = $countQuery->fetchColumn();

    // Retrieve available rooms
    $roomQuery = $pdo->query(
        "SELECT * FROM rooms
         WHERE status = 'available'
         ORDER BY room_id DESC
         LIMIT 6"
    );

    $rooms = $roomQuery->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {

    echo "<div class='alert alert-danger'>
            Unable to retrieve room information.
          </div>";
}

?>

<!-- Welcome Section -->
<section class="p-5 mb-4 bg-light rounded-3 text-center">

    <div class="container">

        <h1 class="display-5 fw-bold">
            Welcome to Our Hotel
        </h1>

        <p class="fs-5">
            Find your perfect room and enjoy a comfortable stay.
            Our reservation system makes booking simple and convenient.
        </p>

        <a href="reservations/create.php" class="btn btn-primary btn-lg">
            Book a Room
        </a>

    </div>

</section>


<!-- Statistics Section -->
<section class="row text-center mb-5">

    <div class="col-md-4 mx-auto">

        <div class="card shadow-sm p-4">

            <h2 class="text-primary">
                <?php echo htmlspecialchars((string)$availableRooms); ?>
            </h2>

            <p class="mb-0">Available Rooms</p>

        </div>

    </div>

</section>


<!-- Available Rooms -->
<section class="mb-5">

    <h2 class="text-center mb-4">
        Our Available Rooms
    </h2>

    <div class="row">

        <?php if (!empty($rooms)): ?>

            <?php foreach ($rooms as $room): ?>

                <div class="col-md-4 mb-4">

                    <div class="card h-100 shadow-sm">

                        <div class="card-body">

                            <h5 class="card-title">
                                <?php echo htmlspecialchars($room['room_type']); ?>
                            </h5>

                            <p class="card-text">

                                <strong>Room Number:</strong>
                                <?php echo htmlspecialchars($room['room_number']); ?>

                                <br>

                                <strong>Price per Night:</strong>
                                KSh <?php echo number_format((float)$room['price_per_night'], 2); ?>

                                <br>

                                <strong>Capacity:</strong>
                                <?php echo htmlspecialchars((string)$room['capacity']); ?>
                                guests

                            </p>

                            <p>
                                <?php echo htmlspecialchars(
                                    $room['description'] ?? 'Comfortable accommodation.'
                                ); ?>
                            </p>

                            <a href="reservations/create.php"
                               class="btn btn-primary">
                                Reserve Room
                            </a>

                        </div>

                    </div>

                </div>

            <?php endforeach; ?>

        <?php else: ?>

            <div class="col-12">

                <div class="alert alert-info text-center">
                    No available rooms at the moment.
                </div>

            </div>

        <?php endif; ?>

    </div>

</section>


<!-- Why Choose Us -->
<section class="bg-light p-5 rounded-3 text-center mb-4">

    <h2 class="mb-4">Why Choose Our Hotel?</h2>

    <div class="row">

        <div class="col-md-4 mb-3">
            <h5>Easy Booking</h5>
            <p>Reserve your preferred room with a simple booking process.</p>
        </div>

        <div class="col-md-4 mb-3">
            <h5>Room Availability</h5>
            <p>View available rooms before making a reservation.</p>
        </div>

        <div class="col-md-4 mb-3">
            <h5>Manage Reservations</h5>
            <p>View, modify and cancel your reservations conveniently.</p>
        </div>

    </div>

</section>


<?php
require_once "includes/footer.php";
?>