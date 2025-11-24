<?php
include 'db_connect.php';
include 'header.php';

if (!isset($_SESSION['patient_id'])) {
    header('Location: login.php');
    exit;
}

$patient_id = $_SESSION['patient_id'];
$update_success = '';
$update_error = '';

if (isset($_POST['update_profile'])) {
    $age = $_POST['age'];
    $gender = $_POST['gender'];
    $contact = $_POST['contact']; 
    $height = $_POST['height'];
    $weight = $_POST['weight'];
    $blood_group = $_POST['blood_group'];
    $condition = $_POST['present_condition'];

    $stmt = $conn->prepare("UPDATE patients SET age=?, gender=?, phone=?, height=?, weight=?, blood_group=?, present_condition=? WHERE patient_id=?");
    $stmt->bind_param("isssddsi", $age, $gender, $contact, $height, $weight, $blood_group, $condition, $patient_id);

    if ($stmt->execute()) {
        $update_success = "Profile updated successfully!";
    } else {
        $update_error = "Error updating profile: " . $stmt->error;
    }
    $stmt->close();
}

$patient_data = [];
$stmt = $conn->prepare("SELECT * FROM patients WHERE patient_id = ?");
$stmt->bind_param("i", $patient_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    $patient_data = $result->fetch_assoc();
}
$stmt->close();

if (isset($_GET['action']) && $_GET['action'] == 'logout') {
    session_destroy();
    header('Location: index.php');
    exit;
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3">Welcome, <?php echo htmlspecialchars($patient_data['name'] ?? 'Patient'); ?>!</h1>
    <div>
        <span class="text-muted me-3">
            <i class="bi bi-person-badge"></i> Patient ID: 
            **<?php echo $patient_id; ?>**
        </span>
        <a href="?action=logout" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-box-arrow-right"></i> Logout
        </a>
    </div>
</div>

<?php if ($update_success): ?>
    <script>Swal.fire('Success', '<?php echo $update_success; ?>', 'success');</script>
<?php endif; ?>
<?php if ($update_error): ?>
    <script>Swal.fire('Error', '<?php echo $update_error; ?>', 'error');</script>
<?php endif; ?>

<div class="row">
    <div class="col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-header bg-primary text-white">
                <i class="bi bi-file-person"></i> Your Profile Details
            </div>
            <div class="card-body">
                <p><strong>Name:</strong> <?php echo htmlspecialchars($patient_data['name']); ?></p>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($patient_data['email']); ?></p>
                <p><strong>Phone:</strong> <?php echo htmlspecialchars($patient_data['phone']); ?></p>
                <hr>
                <p><strong>Age:</strong> <?php echo $patient_data['age'] ?? 'N/A'; ?></p>
                <p><strong>Gender:</strong> <?php echo $patient_data['gender'] ?? 'N/A'; ?></p>
                <p><strong>Height:</strong> <?php echo $patient_data['height'] ? $patient_data['height'] . ' cm' : 'N/A'; ?></p>
                <p><strong>Weight:</strong> <?php echo $patient_data['weight'] ? $patient_data['weight'] . ' kg' : 'N/A'; ?></p>
                <p><strong>Blood Group:</strong> <?php echo $patient_data['blood_group'] ?? 'N/A'; ?></p>
                <p><strong>Present Condition:</strong> <span class="badge bg-info"><?php echo htmlspecialchars($patient_data['present_condition']); ?></span></p>
                <button class="btn btn-sm btn-info mt-2" data-bs-toggle="modal" data-bs-target="#updateProfileModal">
                    <i class="bi bi-pencil-square"></i> Update Profile
                </button>
                <a href="appointments.php" class="btn btn-sm btn-success mt-2">
                    <i class="bi bi-calendar-check"></i> Manage Appointments
                </a>
            </div>
        </div>
    </div>

    <div class="col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-header bg-success text-white">
                <i class="bi bi-robot"></i> AI Health Chatbot
            </div>
            <div class="card-body d-flex flex-column" style="min-height: 350px;">
                <div id="chat-messages" class="flex-grow-1 overflow-auto mb-3 p-2 border rounded" style="max-height: 250px;">
                    <div class="text-start mb-2"><span class="badge bg-secondary">Chatbot:</span> Hello! Ask me basic health questions like "how to cure fever".</div>
                </div>
                <div class="input-group">
                    <input type="text" id="chat-input" class="form-control" placeholder="Ask a health question...">
                    <button class="btn btn-success" type="button" id="chat-send">Send</button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="updateProfileModal" tabindex="-1" aria-labelledby="updateProfileModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title" id="updateProfileModalLabel">Update Patient Profile</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Name</label>
                        <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($patient_data['name']); ?>" disabled>
                    </div>
                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="form-label">Age</label>
                            <input type="number" name="age" class="form-control" value="<?php echo $patient_data['age'] ?? ''; ?>" min="1" max="120">
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label">Gender</label>
                            <select name="gender" class="form-select">
                                <option value="" <?php echo ($patient_data['gender'] == null) ? 'selected' : ''; ?>>Select</option>
                                <option value="Male" <?php echo ($patient_data['gender'] == 'Male') ? 'selected' : ''; ?>>Male</option>
                                <option value="Female" <?php echo ($patient_data['gender'] == 'Female') ? 'selected' : ''; ?>>Female</option>
                                <option value="Other" <?php echo ($patient_data['gender'] == 'Other') ? 'selected' : ''; ?>>Other</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="form-label">Height (cm)</label>
                            <input type="number" name="height" class="form-control" step="0.01" value="<?php echo $patient_data['height'] ?? ''; ?>">
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label">Weight (kg)</label>
                            <input type="number" name="weight" class="form-control" step="0.01" value="<?php echo $patient_data['weight'] ?? ''; ?>">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Blood Group</label>
                        <input type="text" name="blood_group" class="form-control" value="<?php echo $patient_data['blood_group'] ?? ''; ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Present Condition</label>
                        <select name="present_condition" class="form-select" required>
                            <option value="Healthy" <?php echo ($patient_data['present_condition'] == 'Healthy') ? 'selected' : ''; ?>>Healthy</option>
                            <option value="Fever" <?php echo ($patient_data['present_condition'] == 'Fever') ? 'selected' : ''; ?>>Fever</option>
                            <option value="Diabetic" <?php echo ($patient_data['present_condition'] == 'Diabetic') ? 'selected' : ''; ?>>Diabetic</option>
                            <option value="Other" <?php echo ($patient_data['present_condition'] == 'Other') ? 'selected' : ''; ?>>Other</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Contact Phone (Update)</label>
                        <input type="tel" name="contact" class="form-control" value="<?php echo htmlspecialchars($patient_data['phone']); ?>" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" name="update_profile" class="btn btn-info">Save changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.getElementById('chat-send').addEventListener('click', sendMessage);
    document.getElementById('chat-input').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            sendMessage();
        }
    });

    function sendMessage() {
        const input = document.getElementById('chat-input');
        const message = input.value.trim();
        const chatBox = document.getElementById('chat-messages');

        if (message === '') return;

        chatBox.innerHTML += `<div class="text-end mb-2"><span class="badge bg-primary">You:</span> ${message}</div>`;
        input.value = '';
        chatBox.scrollTop = chatBox.scrollHeight;

        fetch('chatbot_handler.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: `message=${encodeURIComponent(message)}`
        })
        .then(response => response.json())
        .then(data => {
            chatBox.innerHTML += `<div class="text-start mb-2"><span class="badge bg-secondary">Chatbot:</span> ${data.reply}</div>`;
            chatBox.scrollTop = chatBox.scrollHeight;
        })
        .catch(error => {
            console.error('Chatbot error:', error);
            chatBox.innerHTML += `<div class="text-start mb-2"><span class="badge bg-danger">Chatbot:</span> Sorry, an error occurred.</div>`;
        });
    }
</script>

<?php 
mysqli_close($conn);
include 'footer.php'; 
?>