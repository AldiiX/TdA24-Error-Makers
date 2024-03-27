<?php

class ChatGPT
{
    private $API_KEY = "¯\_(ツ)_/¯";
    private $textURL = "https://api.openai.com/v1/chat/completions";
    private $imageURL = "https://api.openai.com/v1/images/generations";

    public $curl;

    public function __construct()
    {
        $this->curl = curl_init();
    }

    public function initialize($requestType = "text" || "image")
    {
        $this->curl = curl_init();

        if ($requestType === 'image')
            curl_setopt($this->curl, CURLOPT_URL, $this->imageURL);
        if ($requestType === 'text')
            curl_setopt($this->curl, CURLOPT_URL, $this->textURL);

        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->curl, CURLOPT_POST, true);

        $headers = array(
            "Content-Type: application/json",
            "Authorization: Bearer $this->API_KEY"
        );

        curl_setopt($this->curl, CURLOPT_HTTPHEADER, $headers);
    }

    /**
     * Generates a text response based on the given prompt using the specified parameters.
     *
     * @param string $prompt The prompt for generating the text response.
     * @param string $model The GPT-3 model to use for text generation.
     * @param float $temperature The temperature parameter for controlling randomness (default: 0.7).
     * @param int $maxTokens The maximum number of tokens in the generated text (default: 1000).
     * @return array An array containing 'data' and 'error' keys, representing the generated text and any errors.
     */
    public function findActivity(string $prompt, string $activities, string $model = "gpt-3.5-turbo", float $temperature = 0.5, int $maxTokens = 1000): array {
        curl_reset($this->curl);
        $this->initialize('text');

        $activities = addslashes($activities);
        $data["messages"] = [ ["role" => "system", "content" => "Teď ti budu psát něco jako popis (input). Ty z tohoto arraye vytáhneš objekty, které mají něco společného s tím, co jsem napsal jako input. Můžeš hledat naprosto všude a hledat podobné výsledky, případně shrň to, o čem ten objekt je a porovnej to s tím, o čem je input. To co najdeš pošli nazpátek jako json, ale pouze ty nalezené objekty, nic víc. V případě, že nenajdeš nic, vrať null. V případě, že najdeš pouze jeden objekt, vrať ten objekt v arrayi.\nZde máš array, ze kterého budeš hledat:' . $activities . '\n\n\n"], ["role" => "user", "content" => $prompt] ];
        $data["model"] = $model;
        $data["temperature"] = $temperature;
        $data["max_tokens"] = $maxTokens;

        curl_setopt($this->curl, CURLOPT_POSTFIELDS, json_encode($data));

        $response = curl_exec($this->curl);
//        echo "<br>Odpověď api: " . $response;
        $response = json_decode($response, true);

        $output['data'] = $response['choices'][0]['message']["content"] ?? null;
        $output['error'] = $response['error']['code'] ?? null;
        return $output;
    }

    /**
     * Generates an image URL based on the given prompt and parameters.
     *
     * @param string $prompt The prompt for generating the image URL.
     * @param string $imageSize The desired image size (default: '512x512').
     * @param int $numberOfImages The number of images to generate (default: 1).
     * @return array An array containing ['data'] and ['error'] keys, representing the generated image URL and any errors.
     */
    public function generateImage($prompt, $imageSize = '512x512', $numberOfImages = 1)
    {
        curl_reset($this->curl);
        $this->initialize('image');

        $data["prompt"] = $prompt;
        $data["n"] = $numberOfImages;
        $data["size"] = $imageSize;

        curl_setopt($this->curl, CURLOPT_POSTFIELDS, json_encode($data));

        $response = curl_exec($this->curl);
        $response = json_decode($response, true);

        $output['data'] = $response['data'][0]['url'] ?? null;
        $output['error'] =  $response['error']['code'] ?? null;
        return $output;
    }
}
