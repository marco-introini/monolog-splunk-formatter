<?php

namespace Mintdev\Monolog\Formatter;

use Monolog\Formatter\LineFormatter;

class SplunkLineFormatter extends LineFormatter
{
    const SPLUNK_FORMAT = "%datetime% %channel%.%level_name% L=%level% %message% %context% %extra%\n";

    protected $quoteReplacement;

    public function __construct(
        ?string $format = null,
        ?string $dateFormat = null,
        bool $allowInlineLineBreaks = false,
        bool $ignoreEmptyContextAndExtra = false,
        string $quoteReplacement = '^'
    ) {
        if ($format === null) {
            $format = self::SPLUNK_FORMAT;
        }
        // By default we just put the Unix timestamp to save Splunk processing costs;
        // We'll never actually search on this via Splunk, it has its own timestamp data.
        if ($dateFormat === null) {
            $dateFormat = 'U';
        }
        $this->quoteReplacement = $quoteReplacement;
        parent::__construct($format, $dateFormat, $allowInlineLineBreaks, $ignoreEmptyContextAndExtra);
    }

    public function setQuoteReplacement($quoteReplacement)
    {
        $this->quoteReplacement = $quoteReplacement;
    }

    protected function jsonEncode($data): string
    {
        if (version_compare(PHP_VERSION, '5.4.0', '>=')) {
            return $this->toJson($data, true);
        }

        return str_replace('\\/', '/', @json_encode($data));
    }

    /**
     * Public so we can run unit tests against it
     */
    public function publicConvertToString($data): string
    {
        return $this->convertToString($data);
    }

    protected function convertToString($data): string
    {
        if (null === $data || is_bool($data)) {
            return var_export($data, true);
        }

        if (is_scalar($data)) {
            return (string)$data;
        }

        if (is_array($data)) {
            $vals = array();

            foreach ($data as $n => $v) {
                if (null === $v || is_bool($v)) {
                    $v = var_export($v, true);
                } else {
                    if (is_numeric($v)) {
                        $v = (string)$v;
                    } else {
                        if (is_scalar($v)) {
                            // If this consists of simple characters with no spaces, we don't need quotes.
                            // This saves 2 bytes per n=v pair, which can add up to lots of money.
                            if (!preg_match("/^[a-z0-9\-_\.]*$/i", $v)) {
                                $v = '"'.$this->toQuoteSafeString($v).'"';
                            }
                        } else {
                            $v = '"'.$this->toQuoteSafeString($this->jsonEncode($v)).'"';
                        }
                    }
                }

                $vals[] = "$n=$v";
            }

            return implode(' ', $vals);
        }

        return $this->jsonEncode($data);
    }

    protected function toQuoteSafeString($string): string
    {
        return str_replace('"', $this->quoteReplacement, (string)$string);
    }

}