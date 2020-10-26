<?php
namespace conta\Utility;

trait StringUtility
{
    public static function snakeToCamel(string $snakeCase): string
    {
        $trimmed = trim($snakeCase);
        $lowered = mb_strtolower($trimmed);
        $spans = explode('_', $lowered);
        $camelCase = '';
        foreach ($spans as $index => $span) {
            if ($index === 0) {
                $camelCase .= $span;
                continue;
            }
            $camelCase .= ucfirst($span);
        }
        return $camelCase;
    }
}