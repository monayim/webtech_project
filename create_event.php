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

if (!isset($_SESSION['user']) || !in_array($_SESSION['user']['role'], ['admin','organizer'])) {
    header("Location: login.php");
    exit;
}

$message = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!validateCsrfToken($_POST['csrf_token'])) {
        $message = "Invalid request";
    } else {
        $title = sanitizeInput($_POST['title']);
        $desc  = sanitizeInput($_POST['description']);
        $date  = $_POST['event_date'];
        
        // Validate date
        if (strtotime($date) < strtotime('today')) {
            $message = "Event date cannot be in the past";
        } else {
            $stmt = $pdo->prepare("INSERT INTO events (title, description, event_date, created_by) VALUES (?,?,?,?)");
            if ($stmt->execute([$title, $desc, $date, $_SESSION['user']['id']])) {
                $message = "Event created successfully!";
                // Clear form
                $_POST = array();
            } else {
                $message = "Error creating event.";
            }
        }
    }
}
?>

<?php include 'includes/header.php'; ?>
<div class="container">
  <h2>Create Event</h2>
  
  <?php if ($message): ?>
    <div class="alert <?= strpos($message, 'Error') !== false ? 'alert-danger' : 'alert-success'; ?>">
        <?= e($message); ?>
    </div>
  <?php endif; ?>
  
  <form method="post">
    <input type="hidden" name="csrf_token" value="<?= generateCsrfToken(); ?>">
    <input type="text" name="title" placeholder="Event Title" value="<?= isset($_POST['title']) ? e($_POST['title']) : ''; ?>" required><br>
    <textarea name="description" placeholder="Event Description" required><?= isset($_POST['description']) ? e($_POST['description']) : ''; ?></textarea><br>
    <input type="date" name="event_date" value="<?= isset($_POST['event_date']) ? e($_POST['event_date']) : ''; ?>" min="<?= date('Y-m-d'); ?>" required><br>
    <button type="submit">Create Event</button>
  </form>
</div>
<?php include 'includes/footer.php'; ?>