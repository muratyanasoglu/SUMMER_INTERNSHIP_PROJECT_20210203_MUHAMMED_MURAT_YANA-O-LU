<?php
session_start();

// Kullanıcının kazandığı para
$winnings = $_SESSION['total_winnings'];

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

    // En son eklenen kullanıcıyı seçin
    $stmt = $conn->query("SELECT id FROM users ORDER BY id DESC LIMIT 1");
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    $userId = $user['id'];

    // Kullanıcının kazandığı parayı veritabanına kaydet
    $stmt = $conn->prepare("INSERT INTO winnings (user_id, amount) VALUES (:user_id, :amount)");
    $stmt->bindParam(':user_id', $userId);
    $stmt->bindParam(':amount', $winnings);
    $stmt->execute();

} catch(PDOException $e) {
    echo "Bağlantı hatası: " . $e->getMessage();
}

// Oturumu sıfırlayın
session_destroy();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Game Over</title>
    <style>
        body {
            background-image: url('welcomepage.webp');
            background-size: cover;
            background-repeat: no-repeat;
            background-position: center;
            color: #ffffff;
            font-family: Arial, sans-serif;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            position: relative;
        }

        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.7); /* Karartma efekti */
            z-index: -1;
        }

        .container {
            background: linear-gradient(135deg, rgba(0, 0, 255, 0.8), rgba(0, 0, 128, 0.8)); /* Degrade mavi */
            border: 2px solid gold;
            padding: 20px;
            border-radius: 10px;
            margin: 10px;
            width: 80%;
            max-width: 600px;
            text-align: center;
            box-shadow: 0 0 25px rgba(255, 215, 0, 0.8); /* Altın rengi gölge efekti */
            animation: glow 1.5s infinite alternate; /* Parlama animasyonu */
        }

        @keyframes glow {
            0% { box-shadow: 0 0 10px rgba(255, 215, 0, 0.5); }
            100% { box-shadow: 0 0 30px rgba(255, 215, 0, 1); }
        }

        h1 {
            font-size: 2.5em;
            font-weight: bold;
            color: gold;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5); /* Altın rengi ve gölge efekti */
            margin-bottom: 20px;
        }

        p {
            font-size: 1.5em;
            color: #ffffff;
            margin-bottom: 30px;
        }

        a {
            display: inline-block;
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1.2em;
            text-decoration: none;
            transition: background-color 0.3s ease, transform 0.3s ease, box-shadow 0.3s ease; /* Geçiş ve ölçekleme efekti */
        }

        a:hover {
            background-color: #45a049;
            transform: scale(1.05); /* Hover durumunda büyütme efekti */
            box-shadow: 0 0 15px #4CAF50; /* Yeşil gölge efekti */
        }

        .mute-button {
            position: fixed;
            top: 10px;
            right: 10px;
            background: none;
            border: none;
            cursor: pointer;
            z-index: 1000;
        }

        .mute-button img {
            width: 75px;
            height: 75px;
        }
    </style>
    <script>
        let farewellAudio, isMuted = false;

        function toggleMute() {
            isMuted = !isMuted;
            farewellAudio.muted = isMuted;
            document.getElementById('muteButton').src = isMuted ? 'muted.png' : 'unmuted.png';
            localStorage.setItem('isMuted', isMuted);
        }

        document.addEventListener('DOMContentLoaded', (event) => {
            farewellAudio = document.getElementById('farewellAudio');

            // Mute status from localStorage
            if (localStorage.getItem('isMuted') === 'true') {
                isMuted = true;
                document.getElementById('muteButton').src = 'muted.png';
            } else {
                document.getElementById('muteButton').src = 'unmuted.png';
            }

            farewellAudio.muted = isMuted;
            farewellAudio.play().catch(error => {
                console.log('Autoplay was prevented:', error);
                // Retry playback on user interaction
                document.addEventListener('click', () => {
                    farewellAudio.play();
                }, { once: true });
            });
        });
    </script>
</head>
<body>
    <div class="container">
        <h1>Game Over</h1>
        <p>Congratulations! You won $<?= $winnings ?>.</p>
        <a href="index.php">Play Again</a>
    </div>
    <button class="mute-button" onclick="toggleMute()">
        <img id="muteButton" src="unmuted.png" alt="Mute/Unmute">
    </button>
    <audio id="farewellAudio" src="farewell.mp3"></audio>
</body>
</html>
