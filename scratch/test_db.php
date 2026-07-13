<?php
try {
    $db = new PDO('mysql:host=127.0.0.1;port=3306', 'root', '');
    echo "CONNECTED\n";
    foreach($db->query('SHOW DATABASES') as $row) {
        echo $row[0]."\n";
    }
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
