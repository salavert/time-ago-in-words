<?php
namespace Salavert\Tests\Mocks;

class IdentityTranslator {

    public function trans($text, $options = array()) {
        echo "***************";
        return $text;
    }
    public function transchoice($text, $count, $options = array()) {
        echo "***************";
        $text = explode('|]1,Inf[ ', $text);

        if ($count == 1) {
            $text[0] = preg_replace('/\{1\}/', '', $text[0]);
            return $text[0];
        }
        foreach ($options as $k => $v) {
            $text[1] = preg_replace($k, $v, $text[1]);
        }

        return $text[1];
    }

}
