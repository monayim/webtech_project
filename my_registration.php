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

// Only students can view this page
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'student') {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user']['id'];

// Fetch all registrations for this student
// FIXED: Changed 'events.date' to 'events.event_date'
$stmt = $pdo->prepare("
    SELECT events.title, events.description, events.event_date 
    FROM registrations 
    JOIN events ON registrations.event_id = events.id 
    WHERE registrations.user_id = ?
    ORDER BY events.event_date ASC
");

$stmt->execute([$user_id]);
$registrations = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<?php include 'includes/header.php'; ?>

<div class="container">
    <h2>My Registrations</h2>

    <?php if (count($registrations) > 0): ?>
        <table>
            <tr>
                <th>Event Title</th>
                <th>Description</th>
                <th>Date</th>
            </tr>
            <?php foreach ($registrations as $r): ?>
            <tr>
                <td><?= e($r['title']); ?></td>
                <td><?= e($r['description']); ?></td>
                <!-- Also change the key here from 'date' to 'event_date' -->
                <td><?= e($r['event_date']); ?></td>
            </tr>
            <?php endforeach; ?>
        </table>
    <?php else: ?>
        <p>You haven’t registered for any events yet.</p>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
