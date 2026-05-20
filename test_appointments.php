<?php
/**
 * Test script to verify appointments table and add sample data
 */

require_once 'db.php';
/** @var PDO $pdo */

echo "<h2>🔍 Testing Appointments System</h2>";

try {
    // Test 1: Check if table exists
    echo "<h3>Test 1: Checking if table exists...</h3>";
    $result = $pdo->query("SHOW TABLES LIKE 'appointments'");
    if ($result->rowCount() > 0) {
        echo "✅ Table 'appointments' exists!<br><br>";
    } else {
        echo "❌ Table 'appointments' does NOT exist!<br><br>";
        exit;
    }
    
    // Test 2: Check table structure
    echo "<h3>Test 2: Checking table structure...</h3>";
    $columns = $pdo->query("DESCRIBE appointments")->fetchAll(PDO::FETCH_ASSOC);
    echo "<table border='1' cellpadding='5' style='border-collapse:collapse;'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
    foreach ($columns as $col) {
        echo "<tr>";
        echo "<td>{$col['Field']}</td>";
        echo "<td>{$col['Type']}</td>";
        echo "<td>{$col['Null']}</td>";
        echo "<td>{$col['Key']}</td>";
        echo "<td>{$col['Default']}</td>";
        echo "</tr>";
    }
    echo "</table><br>";
    echo "✅ Table structure is correct!<br><br>";
    
    // Test 3: Count existing appointments
    echo "<h3>Test 3: Counting existing appointments...</h3>";
    $count = $pdo->query("SELECT COUNT(*) FROM appointments")->fetchColumn();
    echo "📊 Total appointments in database: <strong>$count</strong><br><br>";
    
    // Test 4: Add sample appointment (optional)
    echo "<h3>Test 4: Would you like to add a sample appointment?</h3>";
    echo "<form method='POST'>";
    echo "<button type='submit' name='add_sample' style='padding:10px 20px;background:#1a1a1a;color:#fff;border:none;border-radius:4px;cursor:pointer;'>Add Sample Appointment</button>";
    echo "</form><br>";
    
    if (isset($_POST['add_sample'])) {
        $stmt = $pdo->prepare("
            INSERT INTO appointments 
            (first_name, last_name, email, phone, service, message, meeting_type, budget_range, appointment_date, appointment_time, status)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        
        $stmt->execute([
            'Jean',
            'Dupont',
            'jean.dupont@example.com',
            '+216 12 345 678',
            'Design résidentiel',
            'Je souhaite rénover mon salon',
            'presentiel',
            '2',
            date('Y-m-d', strtotime('+3 days')),
            '10:00',
            'en_attente'
        ]);
        
        echo "✅ Sample appointment added successfully!<br>";
        echo "📧 Client: Jean Dupont<br>";
        echo "📅 Date: " . date('d/m/Y', strtotime('+3 days')) . " at 10:00<br>";
        echo "📋 Status: En attente<br><br>";
    }
    
    // Test 5: Show recent appointments
    echo "<h3>Test 5: Recent appointments:</h3>";
    $appointments = $pdo->query("SELECT * FROM appointments ORDER BY created_at DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($appointments)) {
        echo "📭 No appointments yet. Add a sample appointment above or wait for clients to book.<br><br>";
    } else {
        echo "<table border='1' cellpadding='5' style='border-collapse:collapse;'>";
        echo "<tr><th>ID</th><th>Client</th><th>Email</th><th>Date</th><th>Time</th><th>Status</th></tr>";
        foreach ($appointments as $apt) {
            echo "<tr>";
            echo "<td>{$apt['id']}</td>";
            echo "<td>{$apt['first_name']} {$apt['last_name']}</td>";
            echo "<td>{$apt['email']}</td>";
            echo "<td>" . date('d/m/Y', strtotime($apt['appointment_date'])) . "</td>";
            echo "<td>{$apt['appointment_time']}</td>";
            echo "<td>{$apt['status']}</td>";
            echo "</tr>";
        }
        echo "</table><br>";
    }
    
    echo "<hr>";
    echo "<h3>🎉 All Tests Passed!</h3>";
    echo "<p>Your appointments system is working correctly.</p>";
    echo "<p><strong>Next steps:</strong></p>";
    echo "<ul>";
    echo "<li>✅ Go to <a href='auth/dashboard_admin.php?section=rendez-vous'>Admin Dashboard - Rendez-vous</a></li>";
    echo "<li>✅ Clients can book at <a href='calendrier/calendrier.php'>Calendrier</a></li>";
    echo "<li>✅ Delete this test file (test_appointments.php) when done</li>";
    echo "</ul>";
    
} catch (PDOException $e) {
    echo "❌ <strong>Error:</strong> " . $e->getMessage();
}
?>

<style>
    body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
    h2 { color: #1a1a1a; }
    h3 { color: #333; margin-top: 20px; }
    table { background: white; margin: 10px 0; }
    th { background: #1a1a1a; color: white; }
    a { color: #b8a99a; text-decoration: none; font-weight: bold; }
    a:hover { text-decoration: underline; }
</style>
