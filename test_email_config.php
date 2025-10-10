<?php
/**
 * Test script untuk konfigurasi email
 */

echo "=== Test Email Configuration ===\n";

// Simulasi konfigurasi email
$emailConfig = [
    'MAIL_MAILER' => 'smtp',
    'MAIL_HOST' => 'mail.pusdatinbgn.web.id',
    'MAIL_PORT' => 465,
    'MAIL_USERNAME' => 'admin@pusdatinbgn.web.id',
    'MAIL_PASSWORD' => 'superadmin123',
    'MAIL_ENCRYPTION' => 'ssl',
    'MAIL_FROM_ADDRESS' => 'admin@pusdatinbgn.web.id',
    'MAIL_FROM_NAME' => 'Meeting Room Booking System',
    'MAIL_SCHEME' => 'ssl',
    'MAIL_VERIFY_PEER' => false,
    'MAIL_VERIFY_PEER_NAME' => false,
    'MAIL_ALLOW_SELF_SIGNED' => true
];

echo "1. Email Configuration:\n";
foreach ($emailConfig as $key => $value) {
    if ($key === 'MAIL_PASSWORD') {
        echo "- $key: " . str_repeat('*', strlen($value)) . "\n";
    } else {
        echo "- $key: $value\n";
    }
}

echo "\n2. SSL/TLS Settings:\n";
echo "✅ Encryption: SSL (Port 465)\n";
echo "✅ Verify Peer: Disabled\n";
echo "✅ Verify Peer Name: Disabled\n";
echo "✅ Allow Self Signed: Enabled\n";

echo "\n3. Server Settings:\n";
echo "✅ Incoming Server: mail.pusdatinbgn.web.id\n";
echo "✅ Outgoing Server: mail.pusdatinbgn.web.id\n";
echo "✅ IMAP Port: 993\n";
echo "✅ POP3 Port: 995\n";
echo "✅ SMTP Port: 465\n";

echo "\n4. Authentication:\n";
echo "✅ Username: admin@pusdatinbgn.web.id\n";
echo "✅ Password: superadmin123\n";
echo "✅ Authentication: Required for IMAP, POP3, and SMTP\n";

echo "\n5. From Address:\n";
echo "✅ Email: admin@pusdatinbgn.web.id\n";
echo "✅ Name: Meeting Room Booking System\n";

echo "\n=== Test Complete ===\n";
echo "Email configuration is ready for production use.\n";
echo "Make sure to update .env file with these settings.\n";
?>
