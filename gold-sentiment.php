<?php
// بيانات حسابك على Myfxbook
$email = 'wissam.karahman@gmail.com';
$password = 'urf2h@sMk4LmeFS';

// تسجيل الدخول إلى Myfxbook
$login_url = "https://www.myfxbook.com/api/login.json?email=$email&password=$password";

// تنفيذ طلب تسجيل الدخول باستخدام cURL
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $login_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // تخطي التحقق من SSL لو فيه مشاكل
$response = curl_exec($ch);
curl_close($ch);

$data = json_decode($response, true);

// تحقق من نجاح تسجيل الدخول
if (isset($data['session'])) {
    $session = $data['session'];

    // طلب بيانات المجتمع (Outlook)
    $outlook_url = "https://www.myfxbook.com/api/get-community-outlook.json?session=$session";

    $ch2 = curl_init();
    curl_setopt($ch2, CURLOPT_URL, $outlook_url);
    curl_setopt($ch2, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch2, CURLOPT_SSL_VERIFYPEER, false);
    $outlook_response = curl_exec($ch2);
    curl_close($ch2);

    $outlook_data = json_decode($outlook_response, true);

    // البحث عن الذهب (XAUUSD)
    if (isset($outlook_data['symbols'])) {
        foreach ($outlook_data['symbols'] as $symbol) {
            if ($symbol['name'] === 'XAUUSD') {
                header('Content-Type: application/json');
                echo json_encode([
                    "symbol" => "XAUUSD",
                    "long" => $symbol['longPercentage'],
                    "short" => $symbol['shortPercentage']
                ]);
                exit;
            }
        }
    }

    // لو لم يجد XAUUSD
    header('Content-Type: application/json');
    echo json_encode(["error" => "XAUUSD not found"]);
} else {
    // لو فشل تسجيل الدخول
    header('Content-Type: application/json');
    echo json_encode(["error" => "Login failed"]);
}
?>
