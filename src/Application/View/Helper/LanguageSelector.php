<?php

namespace Application\View\Helper;

class LanguageSelector
{
    public function __construct(
        private readonly UrlParams $urlParams,
    ) {}

    /**
     * Return a div to select language.
     */
    public function __invoke(): string
    {

        $languages = [
            'en' => 'English',
            'ko' => '한국어',
            'fr' => 'Français',
        ];

        $result = '<div class="language_selector">';
        $params = $_GET;
        foreach ($languages as $val => $name) {
            $params['lang'] = $val;
            $result .= '<a class="language language_' . $val . '" href="' . ($this->urlParams)($params) . '" title="' . $name . '"><span>' . $name . '</span></a> ';
        }
        $result .= '</div>';

        return $result;
    }
}
