<?php

declare(strict_types=1);

namespace App\Helper\Attribute\Constraints\Enum;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 * @Target({"PROPERTY", "METHOD", "ANNOTATION"})
 */
#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class Enum extends Constraint
{
    final public const NO_SUCH_CASE_ERROR = '8e6b7234-171a-4389-a4fc-9324586a6869';

    public function __construct(
        public string      $enumType,
        public bool        $multiple = false,
        public string|null $message = null,
        mixed              $options = null,
        array|null         $groups = null,
        mixed              $payload = null
    )
    {
        parent::__construct($options, $groups, $payload);
    }
}