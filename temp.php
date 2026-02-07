here<?php
header("Content-Type: application/json; charset=UTF-8");

/* ===== إعدادات ثابتة ===== */
$BASE = "https://mob2.temp-mail.org";

/* ===== دالة طلب ===== */
function call_api($url, $method = "POST", $token = null) {

    $headers = [
        "User-Agent: 4.09",
        "Accept: application/json"
    ];

    if ($token) {
        $headers[] = "authorization: $token";
    }

    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CUSTOMREQUEST  => $method,
        CURLOPT_HTTPHEADER     => $headers,
        CURLOPT_TIMEOUT        => 20,
        CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
        CURLOPT_ENCODING       => ""   // مهم جدًا
    ]);

    $response = curl_exec($ch);
    $error    = curl_error($ch);
    curl_close($ch);

    if ($error) {
        echo json_encode([
            "status" => false,
            "error"  => $error
        ]);
        exit;
    }

    // لو رجع HTML → حظر
    if (stripos($response, "<!DOCTYPE html>") !== false) {
        echo json_encode([
            "status" => false,
            "msg" => "cloudflare_blocked"
        ]);
        exit;
    }

    echo $response;
    exit;
}

/* ===== إنشاء إيميل + توكن ===== */
if (isset($_GET["create"])) {
    call_api("$BASE/mailbox", "POST");
}

/* ===== جلب الرسائل ===== */
if (isset($_GET["messages"]) && isset($_GET["token"])) {
    call_api("$BASE/messages", "GET", $_GET["token"]);
}

/* ===== خطأ ===== */
echo json_encode([
    "status" => false,
    "use" => [
        "?create",
        "?messages&token=TOKEN"
    ]
]);
