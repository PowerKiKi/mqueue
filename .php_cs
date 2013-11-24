<?php

$finder = Symfony\CS\Finder\DefaultFinder::create()
		->exclude('library')
		->in(__DIR__)
;

return Symfony\CS\Config\Config::create()
				->fixers(array(
					'indentation',
					'elseif',
					'linefeed',
					'trailing_spaces',
					'unused_use',
					'visibility',
					'return',
					'short_tag',
                    'braces',
//                    'include', // This breaks usage of include within function call
					'php_closing_tag',
					'extra_empty_lines',
//                    'psr0',
					'controls_spaces',
					'eof_ending',
				))
				->finder($finder)
;
