<?php
session_start();
if (empty($_SESSION['csrf_token_clean'])) {
    $_SESSION['csrf_token_clean'] = bin2hex(random_bytes(32));
}
