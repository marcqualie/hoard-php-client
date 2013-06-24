<?php

namespace Hoard\Driver;
use Hoard\Utils;
use Hoard\Exception;

class HttpDriver implements DriverInterface {

    public function track($event, array $data = array(), array $options = array())
    {

        // Apply Options
        $options = array_merge(
            array(
                'async' => true
            ),
            $options
        );

        // Verify Input
        $client = $this->client;
        $server = $client->getServer();
        $apikey = $client->getApiKey();
        $event = trim(str_replace(' ', '-', strtolower($event)));
        if (! $event) {
            throw new Exception('Event name is required');
        }

        // API Endpoint
        $url_string = $server . '/api/track?apikey=' . $apikey;
        $url = parse_url($url_string);
        $post = json_encode(
            array(
                'meta' => array(
                    'time' => time()
                ),
                'event' => $event,
                'bucket' => $client->getBucket()->getName(),
                'data' => $data
            )
        );

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
        if ($response) {
            list($headers, $content) = explode("\n\n", str_replace("\r", '', $response), 2);
            $decoded = Utils::http_chunked_decode($content);
            $response = json_decode($decoded, true);
            if ( ! $response) {
                throw new Exception($decoded);
            }
        }

        return array(
            'ok' => 1,
            'response' => $response
        );
    }

}
