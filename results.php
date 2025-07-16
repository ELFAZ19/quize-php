<?php
session_start();

if (!isset($_SESSION['answers']) || !isset($_SESSION['user_name'])) {
    header('Location: index.php');
    exit();
}

$questions = json_decode(file_get_contents('questions.json'), true);
$totalQuestions = count($questions);

$correctAnswers = 0;
$results = [];

foreach ($_SESSION['answers'] as $index => $userAnswer) {
    $isCorrect = $userAnswer === $questions[$index]['correct'];
    if ($isCorrect) $correctAnswers++;
    
    $results[] = [
        'question' => $questions[$index]['question'],
        'user_answer' => $userAnswer,
        'correct_answer' => $questions[$index]['correct'],
        'choices' => $questions[$index]['choices'],
        'is_correct' => $isCorrect
    ];
}

$score = ($correctAnswers / $totalQuestions) * 100;
$passed = $score >= 75;

$timeTaken = $_SESSION['end_time'] - $_SESSION['start_time'];
$minutes = floor($timeTaken / 60);
$seconds = $timeTaken % 60;

$_SESSION['quiz_results'] = [
    'score' => $score,
    'correct' => $correctAnswers,
    'total' => $totalQuestions,
    'time_taken' => "$minutes min $seconds sec",
    'passed' => $passed,
    'date' => date('F j, Y')
];

unset($_SESSION['current_question']);
unset($_SESSION['answers']);
unset($_SESSION['start_time']);
unset($_SESSION['end_time']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz Results</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Montserrat:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="stars"></div>
    <div class="twinkling"></div>
    
    <div class="container results-page animate-in">
        <div class="results-header">
            <h1>Quiz Results</h1>
            <h2>Thank you for completing the quiz, <?= $_SESSION['user_name'] ?>!</h2>
        </div>
        
        <div class="score-summary">
            <div class="score-circle">
                <svg class="circle-chart" viewBox="0 0 36 36">
                    <path class="circle-bg" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                    <path class="circle-fill" stroke-dasharray="<?= $score ?>, 100" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                </svg>
                <div class="score-text">
                    <span class="score-percent"><?= round($score) ?>%</span>
                    <span class="score-fraction"><?= $correctAnswers ?> / <?= $totalQuestions ?></span>
                </div>
            </div>
            
            <div class="score-details">
                <div class="detail-item">
                    <span class="detail-label">Time Taken</span>
                    <span class="detail-value"><?= "$minutes min $seconds sec" ?></span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Date Completed</span>
                    <span class="detail-value"><?= date('F j, Y') ?></span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Correct Answers</span>
                    <span class="detail-value"><?= $correctAnswers ?></span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Total Questions</span>
                    <span class="detail-value"><?= $totalQuestions ?></span>
                </div>
            </div>
        </div>
        
        <div class="performance-message">
            <div class="message <?= $passed ? 'success' : 'improve' ?>">
                <h3><?= $passed ? 'Congratulations!' : 'Keep Practicing!' ?></h3>
                <p>
                    <?= $passed 
                        ? "You've passed the quiz with a score of " . round($score) . "%. You can now download your certificate of achievement!" 
                        : "You scored " . round($score) . "%. To earn a certificate, you need to score 75% or higher. Try again!" ?>
                </p>
            </div>
        </div>
        
        <div class="question-results">
            <h3>Question Breakdown</h3>
            
            <?php foreach ($results as $index => $result): ?>
                <div class="question-result <?= $result['is_correct'] ? 'correct' : 'incorrect' ?>">
                    <div class="result-status <?= $result['is_correct'] ? 'correct' : 'incorrect' ?>">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <?= $result['is_correct'] 
                                ? '<polyline points="20 6 9 17 4 12"></polyline>' 
                                : '<line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line>' ?>
                        </svg>
                        <span>Question <?= $index + 1 ?>: <?= $result['is_correct'] ? 'Correct' : 'Incorrect' ?></span>
                    </div>
                    
                    <p class="question-text"><?= $result['question'] ?></p>
                    
                    <div class="answer-comparison">
                        <div class="user-answer">
                            <span>Your Answer</span>
                            <strong><?= $result['choices'][$result['user_answer']] ?></strong>
                        </div>
                        
                        <?php if (!$result['is_correct']): ?>
                            <div class="correct-answer">
                                <span>Correct Answer</span>
                                <strong><?= $result['choices'][$result['correct_answer']] ?></strong>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <div class="result-actions">
            <?php if ($passed): ?>
                <a href="certificate.php" class="btn certificate-btn">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                        <polyline points="7 10 12 15 17 10"></polyline>
                        <line x1="12" y1="15" x2="12" y2="3"></line>
                    </svg>
                    Download Certificate
                </a>
            <?php endif; ?>
            
            <a href="index.php" class="btn home-btn">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                    <polyline points="9 22 9 12 15 12 15 22"></polyline>
                </svg>
                Home
            </a>
            
            <a href="quiz.php?retake=true" class="btn retake-btn">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="23 4 23 10 17 10"></polyline>
                    <polyline points="1 20 1 14 7 14"></polyline>
                    <path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"></path>
                </svg>
                Retake Quiz
            </a>
        </div>
    </div>
</body>
</html>