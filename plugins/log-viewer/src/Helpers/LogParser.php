<?php

namespace Botble\LogViewer\Helpers;

use Botble\LogViewer\Utilities\LogLevels;

class LogParser
{
    /**
     * Parsed data.
     *
     * @var array
     */
    protected static $parsed = [];

    /**
     * Parse file content.
     *
     * @param  string $raw
     *
     * @return array
     * @author ARCANEDEV <arcanedev.maroc@gmail.com>
     */
    public static function parse($raw)
    {
        self::$parsed = [];
        list($headings, $data) = self::parseRawData($raw);

        // @codeCoverageIgnoreStart
        if (!is_array($headings)) {
            return self::$parsed;
        }
        // @codeCoverageIgnoreEnd

        foreach ($headings as $heading) {
            for ($i = 0, $j = count($heading); $i < $j; $i++) {
                self::populateEntries($heading, $data, $i);
            }
        };

        unset($headings, $data);

        return array_reverse(self::$parsed);
    }

    /**
     * Parse raw data.
     *
     * @param  string $raw
     *
     * @return array
     * @author ARCANEDEV <arcanedev.maroc@gmail.com>
     */
    protected static function parseRawData($raw)
    {
        $pattern = '/\[' . REGEX_DATE_PATTERN . ' ' . REGEX_TIME_PATTERN . '\].*/';
        preg_match_all($pattern, $raw, $headings);
        $data = preg_split($pattern, $raw);

        if ($data[0] < 1) {
            $trash = array_shift($data);
            unset($trash);
        }

        return [$headings, $data];
    }

    /**
     * Populate entries.
     *
     * @param  array $heading
     * @param  array $data
     * @param  int $key
     * @author ARCANEDEV <arcanedev.maroc@gmail.com>
     */
    protected static function populateEntries($heading, $data, $key)
    {
        foreach (LogLevels::all() as $level) {
            if (self::hasLogLevel($heading[$key], $level)) {
                self::$parsed[] = [
                    'level' => $level,
                    'header' => $heading[$key],
                    'stack' => $data[$key],
                ];
            }
        }
    }

    /**
     * Check if header has a log level.
     *
     * @param  string $heading
     * @param  string $level
     *
     * @return bool
     * @author ARCANEDEV <arcanedev.maroc@gmail.com>
     */
    protected static function hasLogLevel($heading, $level)
    {
        return str_contains(strtolower($heading), strtolower('.' . $level));
    }
}
