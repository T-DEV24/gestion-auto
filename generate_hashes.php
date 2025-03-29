<?php
$password1 = "admin123";
$password2 = "clo123";

$hash1 = password_hash($password1, PASSWORD_BCRYPT);
$hash2 = password_hash($password2, PASSWORD_BCRYPT);

echo "Hachage pour admin123 : $hash1\n";
echo "Hachage pour clo123 : $hash2\n";
?>