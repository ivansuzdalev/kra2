<?php
namespace App\Service;

class TranslitToCyr
{
    private $mapComponent = array(
        'ё' => 'yo',
        'ж' => 'zh',
        'х' => 'kh',
        'ц' => 'ts',
        'ч' => 'ch',
        'ш' => 'sh',
        'щ' => 'shch',
        'ю' => 'yu',
        'я' => 'ya',
        'Ё' => 'Yo',
        'Ж' => 'Zh',
        'Х' => 'Kh',
        'Ц' => 'Ts',
        'Ч' => 'Ch',
        'Ш' => 'Sh',
        'Щ' => 'Shch',
        'Ю' => 'Yu',
        'Я' => 'Ya'
    );

    private $mapSimple =         array (
        'а' => 'a',
        'б' => 'b',
        'в' => 'v',
        'г' => 'g',
        'д' => 'd',
        'е' => 'e',
        'з' => 'z',
        'и' => 'i',
        'й' => 'y',
        'к' => 'k',
        'л' => 'l',
        'м' => 'm',
        'н' => 'n',
        'о' => 'o',
        'п' => 'p',
        'р' => 'r',
        'с' => 's',
        'т' => 't',
        'у' => 'u',
        'ф' => 'f',
        'ъ' => '\'',
        'ы' => 'y',
        'кс' => 'x',
        'ь' => '\'',
        'э' => 'e',
        'А' => 'A',
        'Б' => 'B',
        'В' => 'V',
        'Г' => 'G',
        'Д' => 'D',
        'Е' => 'E',
        'З' => 'Z',
        'И' => 'I',
        'Й' => 'Y',
        'К' => 'K',
        'Л' => 'L',
        'М' => 'M',
        'Н' => 'N',
        'О' => 'O',
        'П' => 'P',
        'Р' => 'R',
        'С' => 'S',
        'Т' => 'T',
        'У' => 'U',
        'Ф' => 'F',
        'Ъ' => '\'',
        'Ы' => 'Y',
        'Кс' => 'X',
        'Ь' => '\'',
        'Э' => 'E'
    );

    public function translitToCyr($input) {
        $chars = str_split($input);
        $output = '';
        for($i = 0; $i<count($chars);$i++){
            if(($i<count($chars)-1) && array_search($chars[$i].$chars[$i+1], $this->mapComponent)) {
                $output = $output .array_search($chars[$i].$chars[$i+1], $this->mapComponent);
                $i++;
            } elseif(array_search($chars[$i], $this->mapSimple)) {
                $output = $output . array_search($chars[$i], $this->mapSimple);
            }
        }
        return $output;
    }
}

