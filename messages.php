<?php
require_once 'functions.php';
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$user = $_SESSION['user'];

// Fetch logged-in user profile details
$profileStmt = $pdo->prepare("SELECT first_name, last_name, title, profile_picture FROM members WHERE user = ?");
$profileStmt->execute([$user]);
$profileData = $profileStmt->fetch();

// Fetch all conversations
$conversationsStmt = $pdo->prepare("
    SELECT DISTINCT
        CASE WHEN sender = ? THEN receiver ELSE sender END AS contact
    FROM messages
    WHERE sender = ? OR receiver = ?
");
$conversationsStmt->execute([$user, $user, $user]);
$conversations = $conversationsStmt->fetchAll(PDO::FETCH_COLUMN);

// Fetch all users for starting a new conversation
$usersStmt = $pdo->prepare("SELECT user, first_name, last_name FROM members WHERE user != ?");
$usersStmt->execute([$user]);
$users = $usersStmt->fetchAll();

// Handle sending a message
$receiver = isset($_GET['user']) ? sanitizeString($_GET['user']) : null;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['message'], $_POST['receiver'])) {
    $message = sanitizeString($_POST['message']);
    $receiver = sanitizeString($_POST['receiver']);
    if ($receiver !== '' && $message !== '') {
        $stmt = $pdo->prepare("INSERT INTO messages (sender, receiver, message) VALUES (?, ?, ?)");
        $stmt->execute([$user, $receiver, $message]);
    }
}

// Fetch messages with the selected user
$messages = [];
if ($receiver) {
    $receiverStmt = $pdo->prepare("SELECT first_name, last_name FROM members WHERE user = ?");
    $receiverStmt->execute([$receiver]);
    $receiverData = $receiverStmt->fetch();

    $messagesStmt = $pdo->prepare("
        SELECT messages.*, members.first_name, members.last_name
        FROM messages
        JOIN members ON messages.sender = members.user
        WHERE (sender = ? AND receiver = ?) OR (sender = ? AND receiver = ?)
        ORDER BY created_at ASC
    ");
    $messagesStmt->execute([$user, $receiver, $receiver, $user]);
    $messages = $messagesStmt->fetchAll();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
</head>
<body>
    <div class="container">
        <!-- Left Column -->
        <div class="left-column">
            <!-- User Profile Section -->
            <img src="img/profiles/<?php echo htmlspecialchars($profileData['profile_picture']); ?>" alt="Your Profile Picture" class="profile-picture">
            <h2><?php echo htmlspecialchars($profileData['first_name'] . ' ' . $profileData['last_name']); ?></h2>
            <?php if (!empty($profileData['title'])): ?>
                <p class="member-title"><?php echo htmlspecialchars($profileData['title']); ?></p>
            <?php endif; ?>
            <div style="width: 70px; height: 1px; background-color: #000; margin: 10px auto;"></div>
            <nav>
                <a href="index.php" class="nav-link">Home</a>
                <a href="logout.php" class="nav-link">Logout</a>
            </nav>
            <hr>
         
            <!-- Conversations Section -->
            <h2>Conversations</h2>
               <!-- New Conversation Section -->
               <button id="start-new-conversation" class="new-conversation-button">Start New Conversation</button>
            <form id="new-conversation-form" method="get" action="messages.php" style="display: none;">
                <select name="user" required>
                    <option value="" disabled selected>Select a user</option>
                    <?php foreach ($users as $userOption): ?>
                        <option value="<?php echo htmlspecialchars($userOption['user']); ?>">
                            <?php echo htmlspecialchars($userOption['first_name'] . ' ' . $userOption['last_name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <button type="submit">Start</button>
            </form>
            </br>
            <?php foreach ($conversations as $contact): ?>
                <?php
                $contactStmt = $pdo->prepare("SELECT first_name, last_name FROM members WHERE user = ?");
                $contactStmt->execute([$contact]);
                $contactData = $contactStmt->fetch();
                ?>
                <nav>
                <a href="messages.php?user=<?php echo urlencode($contact); ?>" class="nav-link">
                    <?php echo htmlspecialchars($contactData['first_name'] . ' ' . $contactData['last_name']); ?>
                </a>
                </nav>
            <?php endforeach; ?>
        </div>

        <!-- Right Column -->
        <div class="right-column">
                <h1 class="centered">Messages</h1>
                <button onclick="location.reload();" class="refresh-button">Refresh</button>
            <?php if ($receiver): ?>
                <div class="messages-container">
                    <?php foreach ($messages as $message): ?>
                        <div class="<?php echo $message['sender'] === $user ? 'sent-message' : 'received-message'; ?>" style="background-color: <?php echo $message['sender'] === $user ? ' #f9f9f9' : '#f0f0f0'; ?>">
                            <p><strong><?php echo htmlspecialchars($message['first_name']); ?></strong></p>
                            <p><?php echo htmlspecialchars($message['message']); ?></p>
                            <small><?php echo $message['created_at']; ?></small>
                        </div>
                    <?php endforeach; ?>
                </div>
                <form method="post">
                    <input type="hidden" name="receiver" value="<?php echo htmlspecialchars($receiver); ?>">
                    <textarea name="message" placeholder="Type your message" required></textarea>
                    <button type="submit">Send</button>
                </form>
            <?php else: ?>
                <p>Select a conversation or start a new one.</p>
            <?php endif; ?>
        </div>
    </div>

    <script>
        document.getElementById('start-new-conversation').addEventListener('click', function () {
            document.getElementById('new-conversation-form').style.display = 'block';
        });
    </script>
</body>
</html>
