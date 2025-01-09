<?php
class Language {
    private static $instance = null;
    private $translations = [];
    private $defaultLang = 'us';
    private $currentLang;
    private $availableLangs = ['us', 'ru'];

    private function __construct() {
        $this->currentLang = $this->detectLanguage();
        $this->loadTranslations();
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new Language();
        }
        return self::$instance;
    }

    private function detectLanguage() {
        // Check if language is set in session
        if (isset($_SESSION['lang']) && in_array($_SESSION['lang'], $this->availableLangs)) {
            return $_SESSION['lang'];
        }
        
        // Check browser language
        if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
            $browserLang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
            if ($browserLang === 'ru') {
                return 'ru';
            }
        }
        return $this->defaultLang;
    }

    private function loadTranslations() {
        $langFile = __DIR__ . '/' . $this->currentLang . '.cfg';
        if (file_exists($langFile)) {
            $content = file_get_contents($langFile);
            $this->translations = json_decode($content, true);
        } else {
            // Fallback to default language
            $defaultFile = __DIR__ . '/' . $this->defaultLang . '.cfg';
            $content = file_get_contents($defaultFile);
            $this->translations = json_decode($content, true);
        }
    }

    public function get($key) {
        $keys = explode('.', $key);
        $value = $this->translations;
        
        foreach ($keys as $k) {
            if (isset($value[$k])) {
                $value = $value[$k];
            } else {
                return $key; // Return key if translation not found
            }
        }
        return $value;
    }

    public function getCurrentLang() {
        return $this->currentLang;
    }

    public function setLanguage($lang) {
        if (in_array($lang, $this->availableLangs)) {
            $this->currentLang = $lang;
            $_SESSION['lang'] = $lang;
            $this->loadTranslations();
            return true;
        }
        return false;
    }

    public function getLanguageSelector() {
        $output = '<div class="language-selector">';
        foreach ($this->availableLangs as $lang) {
            $active = $this->currentLang === $lang ? ' active' : '';
            $langName = $lang === 'us' ? 'English' : 'Русский';
            $output .= sprintf(
                '<a href="javascript:void(0)" class="lang-btn%s" data-lang="%s">%s</a>',
                $active,
                $lang,
                $langName
            );
        }
        $output .= '</div>';
        return $output;
    }
}
?>
