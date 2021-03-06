<?php
namespace Tests\Boekkooi\Bundle\JqueryValidationBundle\Functional\TestBundle\Form;

use Boekkooi\Bundle\JqueryValidationBundle\Form\Util\FormHelper;
use Symfony\Component\Form\ResolvedFormTypeInterface;

class TypeHelper
{
    public static function type($fqcn)
    {
        if (!FormHelper::isSymfony3Compatible() && is_string($fqcn)) {
            if (strpos($fqcn, 'Symfony\Component\Form\Extension\Core\Type\\') === 0) {
                return strtolower(substr($fqcn, 43, -4));
            }

            return new $fqcn();
        }

        return $fqcn;
    }

    public static function fixCollectionOptions(array $options)
    {
        if (FormHelper::isSymfony3Compatible() && !FormHelper::isSymfony2Compatible()) {
            return $options;
        }

        if (isset($options['entry_type'])) {
            $options['type'] = TypeHelper::type($options['entry_type']);
            unset($options['entry_type']);
        }

        if (isset($options['entry_options'])) {
            $options['options'] = TypeHelper::type($options['entry_options']);

            // Due to a bug in 2.8 and 2.8.1 we need to ensure we also set options when we run 2.8.x
            if (!FormHelper::isSymfony3Compatible()) {
                unset($options['entry_options']);
            }
        }

        return $options;
    }

    public static function fixChoices(array $options)
    {
        if (!method_exists(ResolvedFormTypeInterface::class, 'getName')) {
            return $options;
        }

        if (isset($options['choices'])) {
            $choices = array();
            foreach ($options['choices'] as $k => $v) {
                $choices[$v] = $k;
            }
            $options['choices'] = $choices;
        }

        return $options;
    }
}
