<?php
session_start();

if (!isset($_SESSION['quiz_results']) || !$_SESSION['quiz_results']['passed'] || empty($_SESSION['user_name'])) {
    header('Location: index.php');
    exit();
}

$results = $_SESSION['quiz_results'];
$userName = trim($_SESSION['user_name']);
$adminName = "YABSIRA DEJENE";
$score = round($results['score']);
$date = $results['date'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Modern Luxury Certificate</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://fonts.googleapis.com/css2?family=League+Spartan:wght@700&family=Parisienne&family=Outfit:wght@300;600&display=swap" rel="stylesheet">
  <style>
    body {
      margin: 0;
      background: linear-gradient(120deg, #11121a, #1e1f2b);
      color: #fff;
      font-family: 'Outfit', sans-serif;
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
      padding: 30px;
    }
    .certificate-container {
      background: #fefefe;
      border: 12px double #ffb703;
      padding: 60px;
      border-radius: 30px;
      box-shadow: 0 0 80px rgba(255, 215, 0, 0.3);
      text-align: center;
      max-width: 1400px;
      width: 100%;
      position: relative;
    }
    canvas {
      width: 100%;
      height: auto;
      border-radius: 20px;
      box-shadow: 0 8px 40px rgba(0, 0, 0, 0.3);
    }
    .btn {
      margin-top: 40px;
      padding: 14px 36px;
      background: linear-gradient(135deg, #fca311, #e76f51);
      color: #fff;
      border: none;
      border-radius: 40px;
      font-weight: bold;
      font-size: 20px;
      cursor: pointer;
      font-family: 'Outfit', sans-serif;
      transition: all 0.3s ease;
    }
    .btn:hover {
      transform: scale(1.05);
      box-shadow: 0 5px 20px rgba(255, 183, 3, 0.6);
    }
  </style>
</head>
<body>
  <div class="certificate-container">
    <canvas id="certificateCanvas" width="1600" height="1100"></canvas>
    
    <button id="downloadBtn" class="btn">🎓 Download Your Premium Certificate</button>
    <a href="index.php" class="btn home-btn">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                    <polyline points="9 22 9 12 15 12 15 22"></polyline>
                </svg>
                Home
            </a>
  </div>
  <script>
    document.addEventListener("DOMContentLoaded", function () {
      const canvas = document.getElementById("certificateCanvas");
      const ctx = canvas.getContext("2d");

      const name = "<?= addslashes($userName) ?>";
      const score = <?= $score ?>;
      const correct = <?= $results['correct'] ?>;
      const total = <?= $results['total'] ?>;
      const date = "<?= $date ?>";
      const admin = "<?= addslashes($adminName) ?>";

      function drawModernLuxuryCertificate() {
        // Gradient background
        const gradient = ctx.createLinearGradient(0, 0, canvas.width, canvas.height);
        gradient.addColorStop(0, '#fdfcfb');
        gradient.addColorStop(1, '#e2d1c3');
        ctx.fillStyle = gradient;
        ctx.fillRect(0, 0, canvas.width, canvas.height);

        // Outer border
        ctx.strokeStyle = '#ffb703';
        ctx.lineWidth = 18;
        ctx.strokeRect(30, 30, canvas.width - 60, canvas.height - 60);

        // Title
        ctx.fillStyle = '#023047';
        ctx.font = "80px 'League Spartan'";
        const title = "Certificate of Achievement";
        ctx.fillText(title, (canvas.width - ctx.measureText(title).width) / 2, 150);

        // Watermark
        ctx.save();
        ctx.globalAlpha = 0.05;
        ctx.font = "bold 200px 'League Spartan'";
        ctx.fillText("CERTIFIED", 200, canvas.height / 2);
        ctx.restore();

        // Subtitle
        ctx.fillStyle = '#333';
        ctx.font = "34px 'Outfit'";
        const subtitle = "This certificate is proudly presented to";
        ctx.fillText(subtitle, (canvas.width - ctx.measureText(subtitle).width) / 2, 250);

        // Name
        ctx.fillStyle = '#e63946';
        ctx.font = "70px 'Parisienne'";
        ctx.fillText(name, (canvas.width - ctx.measureText(name).width) / 2, 350);

        // Description
        ctx.fillStyle = '#264653';
        ctx.font = "28px 'Outfit'";
        const lines = [
          `For outstanding performance in the General Knowledge Quiz`,
          `Scoring ${score}% with ${correct} out of ${total} correct answers`,
          `Awarded on ${date}`
        ];
        lines.forEach((line, i) => {
          ctx.fillText(line, (canvas.width - ctx.measureText(line).width) / 2, 420 + i * 40);
        });

        // Decorative badge
        ctx.beginPath();
        ctx.arc(150, 950, 60, 0, Math.PI * 2);
        ctx.fillStyle = '#fb8500';
        ctx.fill();
        ctx.fillStyle = '#fff';
        ctx.font = "bold 20px 'Outfit'";
        ctx.fillText("PREMIUM", 105, 955);

        // Signature
        ctx.strokeStyle = '#6a4c93';
        ctx.lineWidth = 1.5;
        ctx.beginPath();
        ctx.moveTo(canvas.width - 400, 930);
        ctx.lineTo(canvas.width - 200, 930);
        ctx.stroke();

        ctx.fillStyle = '#6a4c93';
        ctx.font = "20px 'Outfit'";
        ctx.fillText("Authorized by", canvas.width - 370, 960);
        ctx.font = "28px 'League Spartan'";
        ctx.fillText(admin, canvas.width - 370, 995);
      }

      drawModernLuxuryCertificate();

      document.getElementById("downloadBtn").addEventListener("click", function () {
        const link = document.createElement("a");
        link.download = "certificate_<?= preg_replace('/[^a-zA-Z0-9]/', '_', $userName) ?>.png";
        link.href = canvas.toDataURL("image/png");
        link.click();
      });
    });
  </script>
</body>
</html>