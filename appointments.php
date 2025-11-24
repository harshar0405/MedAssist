<?php
include 'db_connect.php';
include 'header.php';

if (!isset($_SESSION['patient_id'])) {
    header('Location: login.php');
    exit;
}
$patient_id = $_SESSION['patient_id'];

$hospitals_json = '[
    {"name": "Manipal Hospital", "location": "Old Airport Road, Bangalore"},
    {"name": "Fortis Hospital", "location": "Bannerghatta Road, Bangalore"},
    {"name": "Apollo Hospitals", "location": "Sheshadripuram, Bangalore"},
    {"name": "Narayana Health City", "location": "Bommasandra, Bangalore"},
    {"name": "Aster CMI Hospital", "location": "Hebbal, Bangalore"},
    {"name": "Columbia Asia Hospital", "location": "Yeshwanthpur, Bangalore"},
    {"name": "BGS Gleneagles Global Hospital", "location": "Kengeri, Bangalore"},
    {"name": "Sakra World Hospital", "location": "Outer Ring Road, Bellandur, Bangalore"},
    {"name": "Cloudnine Hospital", "location": "Jayanagar, Bangalore"},
    {"name": "Rainbow Children’s Hospital", "location": "Marathahalli, Bangalore"},
    {"name": "St. John’s Medical College Hospital", "location": "Koramangala, Bangalore"},
    {"name": "Vikram Hospital", "location": "Millers Road, Bangalore"},
    {"name": "Hosmat Hospital", "location": "Magarath Road, Bangalore"},
    {"name": "Kidwai Memorial Institute of Oncology", "location": "Hosur Road, Bangalore"},
    {"name": "Bangalore Baptist Hospital", "location": "Hebbal, Bangalore"},
    {"name": "MS Ramaiah Memorial Hospital", "location": "MSR Nagar, Bangalore"},
    {"name": "Vydehi Institute of Medical Sciences & Hospital", "location": "Whitefield, Bangalore"},
    {"name": "SPARSH Hospital", "location": "Narayana Health City Campus, Bommasandra, Bangalore"},
    {"name": "Victoria Hospital", "location": "KR Market, Bangalore"},
    {"name": "Bowring and Lady Curzon Hospital", "location": "Shivajinagar, Bangalore"}
]';

$hospitals = json_decode($hospitals_json, true);

if (isset($_POST['book_appointment'])) {
    $hospital_details = explode('|', $_POST['hospital']);
    $hospital_name = $hospital_details[0];
    $hospital_location = $hospital_details[1];
    $date = $_POST['appointment_date'];
    $time = $_POST['appointment_time'];
    $status = 'Upcoming';

    $stmt = $conn->prepare("INSERT INTO appointments (patient_id, hospital_name, hospital_location, appointment_date, appointment_time, status) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isssss", $patient_id, $hospital_name, $hospital_location, $date, $time, $status);

    if ($stmt->execute()) {
        echo '<script>Swal.fire("Booked!", "Appointment successfully booked!", "success");</script>';
    } else {
        echo '<script>Swal.fire("Error", "Could not book appointment: ' . $stmt->error . '", "error");</script>';
    }
    $stmt->close();
}

if (isset($_GET['action']) && isset($_GET['id'])) {
    $appointment_id = (int)$_GET['id'];
    $action = $_GET['action'];
    $new_date = $_GET['new_date'] ?? null;
    $new_time = $_GET['new_time'] ?? null;

    if ($action == 'cancel') {
        $stmt = $conn->prepare("UPDATE appointments SET status = 'Cancelled' WHERE appointment_id = ? AND patient_id = ?");
        $stmt->bind_param("ii", $appointment_id, $patient_id);
        if ($stmt->execute()) {
             echo '<script>Swal.fire("Cancelled!", "Your appointment has been cancelled.", "warning").then(() => { window.location.href = "appointments.php"; });</script>';
        } else {
            echo '<script>Swal.fire("Error", "Could not cancel appointment.", "error");</script>';
        }
        $stmt->close();
    }
    if ($action == 'reschedule' && $new_date && $new_time) {
        $stmt = $conn->prepare("UPDATE appointments SET appointment_date = ?, appointment_time = ?, status = 'Rescheduled' WHERE appointment_id = ? AND patient_id = ?");
        $stmt->bind_param("ssii", $new_date, $new_time, $appointment_id, $patient_id);
        if ($stmt->execute()) {
             echo '<script>Swal.fire("Rescheduled!", "Appointment has been moved.", "info").then(() => { window.location.href = "appointments.php"; });</script>';
        } else {
            echo '<script>Swal.fire("Error", "Could not reschedule appointment.", "error");</script>';
        }
        $stmt->close();
    }
}

$upcoming_appointments = [];
$stmt = $conn->prepare("SELECT * FROM appointments WHERE patient_id = ? AND status IN ('Upcoming', 'Rescheduled') ORDER BY appointment_date ASC, appointment_time ASC");
$stmt->bind_param("i", $patient_id);
$stmt->execute();
$upcoming_result = $stmt->get_result();
while ($row = $upcoming_result->fetch_assoc()) {
    $upcoming_appointments[] = $row;
}
$stmt->close();

?>

<h1 class="h3 mb-4">🗓️ Appointment Management</h1>

<div class="row">
    <div class="col-md-5 mb-4">
        <div class="card">
            <div class="card-header bg-success text-white">
                Book New Appointment
            </div>
            <div class="card-body">
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                    <div class="mb-3">
                        <label class="form-label">Hospital (Live Location Feature Simulation)</label>
                        <select name="hospital" class="form-select" required>
                            <option value="">-- Select Nearest Hospital (Bangalore) --</option>
                            <?php foreach ($hospitals as $hospital): ?>
                                <option value="<?php echo htmlspecialchars($hospital['name'] . '|' . $hospital['location']); ?>">
                                    <?php echo htmlspecialchars($hospital['name']); ?> (<?php echo htmlspecialchars($hospital['location']); ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <small class="form-text text-muted">Hospitals in Bangalore, Karnataka, India.</small>
                    </div>
                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="form-label">Date</label>
                            <input type="date" name="appointment_date" class="form-control" required min="<?php echo date('Y-m-d'); ?>">
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label">Time</label>
                            <input type="time" name="appointment_time" class="form-control" required>
                        </div>
                    </div>
                    <button type="submit" name="book_appointment" class="btn btn-success w-100">Book Appointment</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-7 mb-4">
        <div class="card">
            <div class="card-header bg-primary text-white">
                Upcoming Appointments
            </div>
            <div class="card-body">
                <?php if (empty($upcoming_appointments)): ?>
                    <div class="alert alert-info text-center">No upcoming appointments found.</div>
                <?php else: ?>
                    <ul class="list-group">
                        <?php foreach ($upcoming_appointments as $appt): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-1 text-primary"><?php echo htmlspecialchars($appt['hospital_name']); ?></h6>
                                    <small class="text-muted"><?php echo date('F j, Y', strtotime($appt['appointment_date'])); ?> at <?php echo date('g:i A', strtotime($appt['appointment_time'])); ?> | <span class="badge bg-secondary"><?php echo htmlspecialchars($appt['status']); ?></span></small>
                                </div>
                                <div>
                                    <button 
                                        class="btn btn-sm btn-info me-2" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#rescheduleModal" 
                                        data-id="<?php echo $appt['appointment_id']; ?>"
                                        data-date="<?php echo $appt['appointment_date']; ?>"
                                        data-time="<?php echo $appt['appointment_time']; ?>"
                                        >
                                        Reschedule
                                    </button>
                                    <button 
                                        class="btn btn-sm btn-danger" 
                                        onclick="confirmCancel(<?php echo $appt['appointment_id']; ?>)"
                                        >
                                        Cancel
                                    </button>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="rescheduleModal" tabindex="-1" aria-labelledby="rescheduleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="rescheduleForm" action="appointments.php" method="get">
                <input type="hidden" name="action" value="reschedule">
                <input type="hidden" name="id" id="reschedule-appt-id">

                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title" id="rescheduleModalLabel">Reschedule Appointment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Current time: <strong id="current-time-display"></strong></p>
                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="form-label">New Date</label>
                            <input type="date" name="new_date" id="new-date" class="form-control" required min="<?php echo date('Y-m-d'); ?>">
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label">New Time</label>
                            <input type="time" name="new_time" id="new-time" class="form-control" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-info">Save Reschedule</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.getElementById('rescheduleModal').addEventListener('show.bs.modal', function (event) {
    const button = event.relatedTarget; 
    const appointmentId = button.getAttribute('data-id');
    const currentDate = button.getAttribute('data-date');
    const currentTime = button.getAttribute('data-time');

    document.getElementById('reschedule-appt-id').value = appointmentId;
    document.getElementById('new-date').value = currentDate;
    document.getElementById('new-time').value = currentTime;
    document.getElementById('current-time-display').innerText = `${currentDate} at ${currentTime}`;
});

function confirmCancel(id) {
    Swal.fire({
        title: 'Are you sure?',
        text: "Do you really want to cancel this appointment?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, cancel it!'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = `appointments.php?action=cancel&id=${id}`;
        }
    });
}
</script>

<?php 
mysqli_close($conn);
include 'footer.php'; 
?>