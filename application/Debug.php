<?php

class Debug
{
    /**
     * Export variables omitting array keys that are strictly numeric
     *
     * By default will output result
     *
     * @param mixed $data
     * @param bool $return
     * @param int $level
     *
     * @return string string representation of variable
     */
    public static function export($data, bool $return = false, int $level = 0)
    {
        $result = '';
        if (is_array($data)) {
            $needKey = array_keys($data) !== range(0, count($data) - 1);
            $result .= '[' . PHP_EOL;
            foreach ($data as $key => $value) {
                $result .= str_repeat(' ', 4 * ($level + 1));
                if ($needKey) {
                    $result .= self::export($key, true, $level + 1);
                    $result .= ' => ';
                }

                $result .= self::export($value, true, $level + 1);
                $result .= ',' . PHP_EOL;
            }
            $result .= str_repeat(' ', 4 * $level) . ']';
        } else {
            $result .= var_export($data, true);
        }

        if (!$return) {
            echo $result;
        }

        return $result;
    }
}

function ve($a, bool $return = false)
{
    return Debug::export($a, $return);
}

function v(): void
{
    var_dump(func_get_args());
}

function w(): void
{
    $isHtml = (PHP_SAPI !== 'cli');
    echo "\n_________________________________________________________________________________________________________________________" . ($isHtml ? '</br>' : '') . "\n";
    var_dump(func_get_args());
    echo "\n" . ($isHtml ? '</br>' : '') . '_________________________________________________________________________________________________________________________' . ($isHtml ? '<pre>' : '') . "\n";
    debug_print_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
    echo '' . ($isHtml ? '</pre>' : '') . '_________________________________________________________________________________________________________________________' . ($isHtml ? '</br>' : '') . "\n";
    die("script aborted on purpose.\n");
}
