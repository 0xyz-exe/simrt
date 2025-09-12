<?php

// Forward Vercel requests to Laravel
$_GET['_url'] = $_SERVER['REQUEST_URI'];
require_once __DIR__ . '/../public/index.php';
