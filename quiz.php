<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['name'])) {
    $_SESSION['user_name'] = trim(htmlspecialchars($_POST['name']));
    $_SESSION['user_email'] = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    
    $questions = json_decode(file_get_contents('questions.json'), true);
    $_SESSION['total_questions'] = count($questions);
    $_SESSION['current_question'] = 0;
    $_SESSION['answers'] = array_fill(0, $_SESSION['total_questions'], null);
    $_SESSION['start_time'] = time();
    $_SESSION['question_start_time'] = time();
    
    header('Location: quiz.php');
    exit();
}

$questions = json_decode(file_get_contents('questions.json'), true);
$totalQuestions = count($questions);

if (!isset($_SESSION['current_question']) || !isset($_SESSION['user_name'])) {
    header('Location: index.php');
    exit();
}

$currentQuestionIndex = $_SESSION['current_question'];
$currentQuestion = $questions[$currentQuestionIndex];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['answer'])) {
    $_SESSION['answers'][$currentQuestionIndex] = (int)$_POST['answer'];
    
    if ($currentQuestionIndex < $totalQuestions - 1) {
        $_SESSION['current_question']++;
        $_SESSION['question_start_time'] = time();
        header('Location: quiz.php');
        exit();
    } else {
        $_SESSION['end_time'] = time();
        header('Location: results.php');
        exit();
    }
}

if (isset($_GET['prev']) && $currentQuestionIndex > 0) {
    $_SESSION['current_question']--;
    $_SESSION['question_start_time'] = time();
    header('Location: quiz.php');
    exit();
}

$progress = (($currentQuestionIndex + 1) / $totalQuestions) * 100;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz - Question <?= $currentQuestionIndex + 1 ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Montserrat:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="stars"></div>
    <div class="twinkling"></div>
    
    <div class="container quiz-runner animate-in">
        <div class="quiz-header">
            <h1>General Knowledge Quiz</h1>
            <p class="user-greeting">Welcome, <span><?= $_SESSION['user_name'] ?></span></p>
        </div>
        
        <div class="progress-container">
            <div class="progress-info">
                <span>Question <?= $currentQuestionIndex + 1 ?> of <?= $totalQuestions ?></span>
                <span><?= round($progress) ?>%</span>
                <span id="timer">Time left: 30s</span>
            </div>
            <div class="progress-bar">
                <div class="progress-fill" style="width: <?= $progress ?>%"></div>
            </div>
        </div>
        
        <form action="quiz.php" method="post" id="quizForm">
            <div class="question-card">
                <div class="question-number">Question <?= $currentQuestionIndex + 1 ?></div>
                <h3 class="question-text"><?= $currentQuestion['question'] ?></h3>
                
                <div class="choices">
                    <?php foreach ($currentQuestion['choices'] as $index => $choice): ?>
                        <label class="choice <?= $_SESSION['answers'][$currentQuestionIndex] === $index ? 'selected' : '' ?>">
                            <input type="radio" name="answer" value="<?= $index ?>" 
                                <?= $_SESSION['answers'][$currentQuestionIndex] === $index ? 'checked' : '' ?> required>
                            <div class="choice-key"><?= chr(65 + $index) ?></div>
                            <div class="choice-text"><?= $choice ?></div>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <div class="navigation-buttons">
                <?php if ($currentQuestionIndex > 0): ?>
                    <button type="button" onclick="window.location.href='quiz.php?prev=true'" class="btn prev-btn">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="15 18 9 12 15 6"></polyline>
                        </svg>
                        Previous
                    </button>
                <?php endif; ?>
                
                <button type="submit" class="btn next-btn">
                    <?= $currentQuestionIndex < $totalQuestions - 1 ? 'Next' : 'Submit' ?>
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="9 18 15 12 9 6"></polyline>
                    </svg>
                </button>
            </div>
        </form>
    </div>

    <script>
        const timeLimit = 30;
        let timeLeft = timeLimit;
        const timerElement = document.getElementById('timer');
        const quizForm = document.getElementById('quizForm');
        
        const questionStartTime = <?= time() - $_SESSION['question_start_time'] ?>;
        timeLeft = Math.max(0, timeLimit - questionStartTime);
        
        const timer = setInterval(() => {
            timeLeft--;
            timerElement.textContent = `Time left: ${timeLeft}s`;
            
            if (timeLeft <= 0) {
                clearInterval(timer);
                if (<?= $currentQuestionIndex < $totalQuestions - 1 ? 'true' : 'false' ?>) {
                    if (!document.querySelector('input[name="answer"]:checked')) {
                        document.querySelector('input[name="answer"]').checked = true;
                    }
                    quizForm.submit();
                } else {
                    quizForm.submit();
                }
            }
        }, 1000);
        
        document.querySelectorAll('.choice input').forEach(input => {
            input.addEventListener('change', function() {
                document.querySelectorAll('.choice').forEach(choice => {
                    choice.classList.remove('selected');
                });
                this.closest('.choice').classList.add('selected');
            });
        });
    </script>
</body>
</html>