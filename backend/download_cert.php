<?php
$certDir = __DIR__ . '/certs';
if (!is_dir($certDir)) {
    mkdir($certDir, 0777, true);
}
$cert = file_get_contents('https://curl.se/ca/cacert.pem');
if ($cert) {
    file_put_contents($certDir . '/cacert.pem', $cert);
    echo "CA Certificate downloaded successfully.\n";
} else {
    echo "Failed to download CA Certificate.\n";
}
