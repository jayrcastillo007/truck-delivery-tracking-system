<h2>New Trip Assigned</h2>

<p>Hello {{ $transaction->driver->first_name }} {{ $transaction->driver->last_name }},</p>

<p>You have been assigned a new trip.</p>

<ul>
    <li><strong>Transaction Code:</strong> {{ $transaction->transaction_code }}</li>
    <li><strong>Pickup:</strong> {{ $transaction->pickup_location }}</li>
    <li><strong>Drop-off:</strong> {{ $transaction->dropoff_location }}</li>
    <li><strong>Scheduled Date:</strong> {{ $transaction->scheduled_date }}</li>
    <li><strong>Vehicle:</strong> {{ $transaction->vehicle->vehicle_type }} ({{ $transaction->vehicle->plate_number }})</li>
</ul>

<p>Login to: </p><a href="https://overcheaply-petrogenetic-demetrius.ngrok-free.dev/login">click here</a>

<p>Please log in to view trip details.</p>
