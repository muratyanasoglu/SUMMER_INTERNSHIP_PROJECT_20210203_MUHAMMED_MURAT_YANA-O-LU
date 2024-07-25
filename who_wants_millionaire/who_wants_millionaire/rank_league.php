<?php
// Veritabanı bağlantısı
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "who_wants_to_be_a_millionaire_db";
$port = 3307;

try {
    // Veritabanı bağlantısını oluşturun
    $dsn = "mysql:host=$servername;dbname=$dbname;port=$port;charset=utf8mb4";
    $conn = new PDO($dsn, $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Kullanıcılar ve kazançlarını al (sadece ilk 10)
    $stmt = $conn->query("
        SELECT users.first_name, users.last_name, winnings.amount 
        FROM winnings 
        JOIN users ON winnings.user_id = users.id 
        ORDER BY winnings.amount DESC 
        LIMIT 10
    ");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    echo "Bağlantı hatası: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Rank League</title>
<link rel="stylesheet" href="rank_league.css">
</head>
<body>
    <div class="header">
        <h1>Rank League</h1>
    </div>
    <div class="container">
        <?php foreach ($users as $index => $user): ?>
            <div class="card">
                <div class="rank"><?= $index + 1 ?></div>
                <div>
                    <h2><?= htmlspecialchars($user['first_name']) . ' ' . htmlspecialchars($user['last_name']) ?></h2>
                    <p>Winnings: $<?= htmlspecialchars($user['amount']) ?></p>
                </div>
                <?php if ($index == 0): ?>
                    <img src="trophy-emoji.png" alt="Gold Trophy" class="icon">
                <?php elseif ($index == 1): ?>
                    <img src="silver-medal.png" alt="Silver Trophy" class="icon">
                <?php elseif ($index == 2): ?>
                    <img src="bronze-medal.png" alt="Bronze Trophy" class="icon">
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
        <a href="index.php" class="button" onclick="playClickSound()">Back to Home</a>
    </div>
    <!-- Particles -->
    <?php for ($i = 0; $i < 50; $i++): ?>
        <div class="particle" style="left: <?= rand(0, 100) ?>%; animation-delay: <?= rand(0, 20) ?>s;"></div>
    <?php endfor; ?>
    <!-- Stars -->
    <?php for ($i = 0; $i < 30; $i++): ?>
        <div class="star" style="left: <?= rand(0, 100) ?>%; animation-delay: <?= rand(0, 30) ?>s;"></div>
    <?php endfor; ?>
    <!-- Sound Effects -->
    <audio id="clickSound" src="button.mp3" preload="auto"></audio>
    <script>
        function playClickSound() {
            document.getElementById('clickSound').play();
        }
    </script>
</body>
</html>
