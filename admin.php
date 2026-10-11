<?php
require_once 'config.php';
if(!isset($_SESSION['user']) || $_SESSION['user']['role']!=='admin') redirect('index.php');
require __DIR__ . '/supervisor.php';
?>