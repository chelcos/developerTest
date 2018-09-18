<?php
// Если нужно изменить количество новостей то ставим параметр. Пример: php getLastNews.php -n=10
// Если параметр не стоит, по умолчанию выводим 5 первых новостей
$options = getopt("n::");
//print_r($options['n']);
$count_news = isset($options['n']) ? $options['n'] : 5;



$url = "https://lenta.ru/rss";
$headers = get_headers($url); // Если удаленный сервер не доступен
if (stripos($headers[0],"200 OK")) {
    $xml = simplexml_load_file($url, 'SimpleXMLElement', LIBXML_NOCDATA); // Отрезаем CDATA иначе описание будет пустым.
    if ($xml && isset($xml->channel->item)) {
        $array_xml = json_decode(json_encode($xml->channel), TRUE); // Для удобства и использования array_slice

        if (isset($array_xml['item']) && !empty($array_xml['item'])) { // На всякий случай что новости в переменной
            $output_xml = array_slice($array_xml['item'], 0, $count_news); // Берем нужное кол-во
            foreach ($output_xml as $item) {;
                echo trim($item['title']).PHP_EOL;
                echo trim($item['link']).PHP_EOL;
                echo trim($item['description']).PHP_EOL;
                echo PHP_EOL;
            }
        } else {
            echo "RSS object to array error";
        }
    } else {
        echo "RSS item no found";
    }
} else {
    echo "RSS headers error";
}