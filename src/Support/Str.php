<?php
namespace SideKit\Config\Support;

/**
 * Class Str
 *
 * Helper class to work with strings that includes *only* the functionality required on the library.
 */
class Str
{
    /**
     * @param $pattern
     * @param $value
     *
     * @return bool
     */
    public function is($pattern, $value)
    {
        if ($pattern === $value) {
            return true;
        }

        $pattern = preg_quote($pattern, '#');

        // Asterisks are translated into zero-or-more regular expression wildcards
        // to make it convenient to check if the strings starts with the given
        // pattern such as "library/*", making any string check convenient.
        $pattern = str_replace('\*', '.*', $pattern);

        return (bool) preg_match('#^' . $pattern . '\z#u', $value);
    }

    /**
     * Convert the given string to upper-case.
     *
     * @param  string  $value
     *
     * @return string
     */
    public static function upper($value)
    {
        return mb_strtoupper($value, 'UTF-8');
    }

    /**
     * Convert the given string to lower-case.
     *
     * @param  string  $value
     *
     * @return string
     */
    public static function lower($value)
    {
        return mb_strtolower($value, 'UTF-8');
    }
}
