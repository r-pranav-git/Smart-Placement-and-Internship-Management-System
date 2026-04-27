<?php
$ch = curl_init('http://localhost/placement/auth/register.php');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
$rand = rand();
curl_setopt($ch, CURLOPT_POSTFIELDS, [
    'name' => 'API Test 2',
    'email' => "api$rand@test.com",
    'password' => '123',
    'roll_no' => "API$rand",
    'department' => 'CS',
    'semester' => '6'
]);
$response = curl_exec($ch);
if(strpos($response, 'Registration successful') !== false) {
    echo "Success!\n";
} else {
    echo "Failed!\n";
    // Check if there is an error shown in HTML
    preg_match('/<div class=\'message error\'>(.*?)<\/div>/s', $response, $matches);
    if(isset($matches[1])) {
        echo "Error: " . strip_tags($matches[1]) . "\n";
    }
}
?>
