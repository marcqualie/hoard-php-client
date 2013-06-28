<?php

namespace Hoard\Driver;
use Hoard\Utils;
use Hoard\Exception;
use Hoard\Event\Payload;
use Hoard\Event\Response;

class HttpDriver extends BaseDriver
{

    public function track($event, array $data = array(), array $options = array())
    {

        // Get external interfaces
        $debug = array();
        $options = $this->setOptions($options);
        $client = $this->client;
        $server = $client->getServer();
        $apikey = $client->getApiKey();

        // Payload (will verify input)
        $payload = new Payload($client->getBucket(), $event, $data);
        $debug['payload'] = $payload->asArray();
        $post = $payload->asJSON();

        // API Endpoint
        $url_string = $server . '/api/track?apikey=' . $apikey;
        $url = parse_url($url_string);
        $debug['url'] = $url_string;

        // Make Request
        $fp = fsockopen(
            $url['host'],
            array_key_exists('port', $url) ? $url['port'] : 80,
            $errno,
            $errstr,
            1);
        $response = null;
        if ($fp !== 0) {
            $out = "POST " . $url['path'] . "?" . $url['query'] . " HTTP/1.1\r\n"
                 . "Host: " . $url['host'] . "\r\n"
                 . "User-Agent: Hoard PHP Client " . $client::VERSION . "\r\n"
                 . "Connection: close\r\n"
                 . "Content-Type: application/x-www-form-urlencoded; charset=UTF-8\r\n"
                 . "Content-Length: " . strlen($post) . "\r\n"
                 . "\r\n" . $post . "\r\n";
            fwrite($fp, $out);
            if ($options['async'] === false) {
                $response = '';
                while (! feof($fp)) {
                    $response .= fread($fp, 4096);
                }
            }
            fclose($fp);
        }

        // Decode Response
        $debug['async'] = $options['async'] ? 1 : 0;
        if ($response) {
            list($headers, $content) = explode("\n\n", str_replace("\r", '', $response), 2);
            $decoded = Utils::http_chunked_decode($content);
            $response = json_decode($decoded, true);
            if (! $response) {
                return new Response(Response::ERROR, 'No Response Data');
            }

            // Debug
            $debug['response'] = $decoded;
        }

        // Response
        $response = new Response(Response::OK, 'Event Tracked', isset($options['debug']) ? $debug : array());
        return $response;
    }

}
