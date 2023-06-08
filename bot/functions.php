<?php

use Google\Client;
use Google\Service\Customsearch;


// Hàm tìm kiếm thông tin trên Google
function searchOnGoogle($query) {
    global $googleAPIKey, $googleCX;
    
    $searchURL = "https://www.googleapis.com/customsearch/v1?key={$googleAPIKey}&cx={$googleCX}&q=" . urlencode($query);
    $searchData = file_get_contents($searchURL);
    
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

// Hàm gửi tin nhắn về cho người dùng trên Telegram
function sendMessage($telegramBotToken, $telegramChatID, $message) {
    $message = urlencode($message);
    $telegramURL = "https://api.telegram.org/bot{$telegramBotToken}/sendMessage?chat_id={$telegramChatID}&parse_mode=HTML&text={$message}";
    file_get_contents($telegramURL);
}
