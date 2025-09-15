<?php
// 1. Functions first
require_once 'includes/functions.php';

// 2. Start session safely
safe_session_start();

// 3. Database connection
require_once 'config/db.php';

// 4. CSRF functions
require_once 'includes/csrf.php';

// Initialize database connection
$database = new Database();
$pdo = $database->getConnection();

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'student') {
    header("Location: login.php");
    exit;
}

$message = ''; // Add a message variable

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $event_id = $_POST['event_id'];
    $user_id  = $_SESSION['user']['id'];

    try {
        $check = $pdo->prepare("SELECT * FROM registrations WHERE event_id=? AND user_id=?");
        $check->execute([$event_id, $user_id]);

        if ($check->rowCount() > 0) {
            $message = "⚠️ You have already registered for this event.";
        } else {
            // INSERT query with status 'pending'
            $stmt = $pdo->prepare("INSERT INTO registrations (event_id, user_id, status) VALUES (?,?, 'pending')");
            $stmt->execute([$event_id, $user_id]);
            $message = "✅ Registration successful!";
        }
    } catch (PDOException $e) {
        $message = "❌ Error registering: " . e($e->getMessage());
    }
}
?>
<!-- Add HTML to show the message -->
<?php include 'includes/header.php'; ?>
<div class="container">
    <h2>Registration</h2>
    <p><?= $message; ?></p>
    <a href="events.php">Back to Events</a> |
    <a href="my_registration.php">View My Registrations</a>
</div>
<?php include 'includes/footer.php'; ?>

