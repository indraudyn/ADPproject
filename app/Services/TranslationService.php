<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TranslationService
{
    /**
     * Translate text using free Google Translate API
     *
     * @param string $text
     * @param string $sourceLang
     * @param string $targetLang
     * @return string
     */
    public static function translateText($text, $sourceLang = 'id', $targetLang = 'en')
    {
        if (empty(trim($text))) {
            return $text;
        }

        try {
            $url = "https://translate.googleapis.com/translate_a/single";
            
            $response = Http::get($url, [
                'client' => 'gtx',
                'sl' => $sourceLang,
                'tl' => $targetLang,
                'dt' => 't',
                'q' => $text,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                $translated = '';
                // Google Translate returns an array of sentences
                if (isset($data[0]) && is_array($data[0])) {
                    foreach ($data[0] as $segment) {
                        if (isset($segment[0])) {
                            $translated .= $segment[0];
                        }
                    }
                    return $translated;
                }
            }

            Log::error('Translation failed', ['response' => $response->body()]);
            return $text; // Fallback to original text if translation fails
            
        } catch (\Exception $e) {
            Log::error('Translation exception: ' . $e->getMessage());
            return $text; // Fallback
        }
    }
}
