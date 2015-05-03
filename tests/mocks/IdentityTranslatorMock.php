<?php
namespace Salavert\Tests\Mocks;

class IdentityTranslator {

    public function trans($text, $options = array()) {
        echo "***************";
        return $text;
    }

}
