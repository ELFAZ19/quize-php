@echo off
:: Set root directory
set "ROOT=quiz-app"

:: Create directories
mkdir %ROOT%\assets\css
mkdir %ROOT%\assets\js
mkdir %ROOT%\controllers
mkdir %ROOT%\models
mkdir %ROOT%\views\components
mkdir %ROOT%\views\errors
mkdir %ROOT%\views\quizzes
mkdir %ROOT%\data

:: Create empty files
type nul > %ROOT%\assets\css\style.css
type nul > %ROOT%\assets\js\script.js
type nul > %ROOT%\controllers\QuizController.php
type nul > %ROOT%\models\QuizModel.php
type nul > %ROOT%\views\components\footer.php
type nul > %ROOT%\views\components\header.php
type nul > %ROOT%\views\errors\database_error.php
type nul > %ROOT%\views\quizzes\create.php
type nul > %ROOT%\views\quizzes\edit.php
type nul > %ROOT%\views\quizzes\index.php
type nul > %ROOT%\views\layout.php
type nul > %ROOT%\data\quizzes.json
type nul > %ROOT%\index.php

echo Folder structure for %ROOT% created successfully.
pause
