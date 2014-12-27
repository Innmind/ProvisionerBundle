<?php

namespace Innmind\ProvisionerBundle;

/**
 * Helper to build symfony command
 */
class CommandHelper
{
    /**
     * Transform an arguments array to its string representation
     * that can be used to run the actual command
     *
     * @param array $args
     *
     * @return string
     */
    public static function getArgumentsAsString(array $args)
    {
        $parts = [];

        foreach ($args as $key => $value) {
            if (is_array($value)) {
                foreach ($value as $subValue) {
                    if (is_numeric($key)) {
                        $parts[] = $subValue;
                    } else {
                        $parts[] = sprintf(
                            '--%s=%s',
                            $key,
                            $subValue
                        );
                    }
                }
            } else {
                if (is_numeric($key)) {
                    $parts[] = $value;
                } else {
                    $parts[] = sprintf(
                        '--%s=%s',
                        $key,
                        $value
                );
                }
            }
        }

        return implode(' ', $parts);
    }
}
