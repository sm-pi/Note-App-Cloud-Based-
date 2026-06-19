<?php
// telemetry.php
function log_security_event($event_type, $details) {
    $log_file = __DIR__ . '/security_telemetry.log';
    $timestamp = date("c"); 
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'UNKNOWN_IP';
    
    $log_entry = json_encode([
        "timestamp" => $timestamp,
        "event_type" => $event_type,
        "ip" => $ip,
        "details" => $details
    ]) . "\n";
    
    file_put_contents($log_file, $log_entry, FILE_APPEND);
}
?>
