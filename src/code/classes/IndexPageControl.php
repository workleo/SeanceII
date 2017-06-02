<?php

namespace Code\Classes;


class IndexPageControl
{

    public function getTitle(): string
    {
        return "НАЧАЛО ТЕСТА";
    }

    public function getSubmitValue(): string
    {
        return 'Пройти тест?';
    }

    public function getDescription(): string
    {
        $description = '';
        $fileName = __DIR__ . '/../../res/test_txt/start.txt';
        if (file_exists($fileName) === true) {
            $json = file_get_contents($fileName);
            $description = json_decode($json, true)['description'];

            if ($description == null) {
                $description = '';
            } else {
                $pattern = ['/\n/', '/\t/'];
                $replacement = ["<br>", "&nbsp;&nbsp;&nbsp;&nbsp;"];
                $description = (preg_replace($pattern, $replacement, $description));
            }
        }
        return $description;
    }
}