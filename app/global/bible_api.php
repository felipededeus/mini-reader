<?php
class BibleAPI {
    private $api_key;
    private $base_url = 'https://api.biblebrain.com/v1/';

    public function __construct($api_key = null) {
        $this->api_key = $api_key;
    }

    public function getVerses($book, $chapter) {
        $url = $this->base_url . 'verses/' . $book . '/' . $chapter;

        if ($this->api_key) {
            $url .= '?api_key=' . $this->api_key;
        }

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($ch);

        if(curl_errno($ch)) {
            echo 'Erro no cURL: ' . curl_error($ch);
        }
        curl_close($ch);

        return json_decode($response, true);
    }
}
?>
