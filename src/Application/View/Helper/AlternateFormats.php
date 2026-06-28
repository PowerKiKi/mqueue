<?php

namespace Application\View\Helper;

use Locale;

class AlternateFormats
{
    protected static $supportedFormats = [
        'rss' => [
            'name' => 'RSS',
        ],
        'csv' => [
            'name' => 'CSV',
        ],
    ];

    public function __construct(
        private readonly HeadLink $headLink,
    ) {}

    /**
     * Returns a string of HTML links for end-user, and also append 'alternate' links to HTML header.
     */
    public function __invoke(array $formats, ?string $title = ''): string
    {
        $formatLinks = [];
        foreach ($formats as $format => $url) {
            // Inject format and locale parameters
            if (str_contains($url, '?')) {
                $url .= '&';
            } else {
                $url .= '?';
            }

            $url .= 'format=' . $format . '&lang=' . mb_substr(Locale::getDefault(), 0, 2);

            $formatLinks[] = '<a class="' . $format . '" href="' . $url . '">' . self::$supportedFormats[$format]['name'] . '</a>';
            if ($title && isset(self::$supportedFormats[$format]['mime'])) {
                ($this->headLink)()->appendAlternate($url, self::$supportedFormats[$format]['mime'], $title);
            }
        }

        $result = '<p class="alternateFormats">' . _tr('Also available in:') . ' ' . implode(' | ', $formatLinks) . '</p>';

        return $result;
    }
}
