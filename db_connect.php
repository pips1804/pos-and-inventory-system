<?php
$db = new mysqli('localhost', 'root', '', 'pos_system');

if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}
