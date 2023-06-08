<?php
require_once 'vendor/autoload.php';

use Google\Client;
use Google\Service\Customsearch;

//bot Telegram
$telegramBotToken = '5767090979:AAFyHPOf7AA4YZTH8hcerFyRz6A9hihoS1M';
$telegramChatID = '-923660507';

//API Google Search
$googleAPIKey = 'AIzaSyDWfmdHdFTwsVOlSJdIkmf0q-gIR7mvqCQ';
$googleCX = 'a0390e5337ea64b5e';

//set Webhook với bot Telegram
$webhookURL = "https://00d5-113-160-198-198.ngrok-free.app/huy/bot/index.php";
$telegramAPIURL = "https://api.telegram.org/bot{$telegramBotToken}/setWebhook?url={$webhookURL}";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $telegramAPIURL);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);

echo $response;

$input = file_get_contents('php://input');
$update = json_decode($input, true);

//kiếm tra tin nhắn
if (isset($update['message'])) {
    $message = $update['message'];
    
    if (isset($message['text']) && strpos($message['text'], '/t') === 0) { //tin nhắn với /search
        $searchQuery = trim(substr($message['text'], 3));
        
        if (!empty($searchQuery)) {
            $searchResults = searchOnGoogle($searchQuery);            
            if (!empty($searchResults)) {
                sendMessage($telegramBotToken, $telegramChatID, $searchResults);
            } else {
                sendMessage($telegramBotToken, $telegramChatID, 'Không tìm thấy kết quả.');
            }
        } else {
            sendMessage($telegramBotToken, $telegramChatID, 'Nhập từ khóa tìm kiếm.');
        }
    }
}

//////////////functions//////////////////
function searchOnGoogle($query) {
    global $googleAPIKey, $googleCX;
    
    $searchURL = "https://www.googleapis.com/customsearch/v1?key={$googleAPIKey}&cx={$googleCX}&q=" . urlencode($query);
    $searchData = file_get_contents($searchURL);
    var_dump($searchData);
    if ($searchData) {
        $searchData = json_decode($searchData, true);
        
        if (isset($searchData['items'])) {
            $results = $searchData['items'];
            $output = "Kết quả tìm kiếm trên Google cho '{$query}':\n\n";
            
            foreach ($results as $result) {
                $title = $result['title'];
                $link = $result['link'];
                $output .= "<a href=\"{$link}\">{$title}</a>\n";
            }
            return $output;
        }
    }
    return '';
}

function sendMessage($telegramBotToken, $telegramChatID, $message) {
    $message = urlencode($message);
    // $telegramURL = "https://api.telegram.org/bot{$telegramBotToken}/sendMessage?chat_id={$telegramChatID}&parse_mode=HTML&text={$message}";
    $telegramURL = "https://api.telegram.org/bot5767090979:AAFyHPOf7AA4YZTH8hcerFyRz6A9hihoS1M/sendMessage?chat_id=-923660507&parse_mode=HTML&text={$message}";
    file_get_contents($telegramURL);
}

