<?php

namespace ChamberOrchestra\DoctrineExtensionsBundle\Type\Exception;

class ConversionException extends \Doctrine\DBAL\Types\ConversionException
{
    static public function conversionFailed(string $value, string $toType): self
    {
        $value = (\strlen($value) > 32) ? \substr($value, 0, 20)."..." : $value;

        return new self('Could not convert database value "'.$value.'" to Doctrine Type '.$toType);
    }


    static public function conversionFailedFormat(string $value, string $toType, string $expectedFormat): self
    {
        $value = (strlen($value) > 32) ? substr($value, 0, 20)."..." : $value;

        return new self(
            'Could not convert database value "'.$value.'" to Doctrine Type '.
            $toType.'. Expected format: '.$expectedFormat
        );
    }


    static public function conversionFailedInvalidType(string $value, string $toType, array $expectedTypes = []): self
    {
        $value = (strlen($value) > 32) ? substr($value, 0, 20)."..." : $value;

        return new self(
            'Could not convert database value "'.$value.'" to Doctrine Type '.
            $toType.'. Expected types: '.\implode(', ', $expectedTypes)
        );
    }
}