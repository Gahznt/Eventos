<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 * @Target({"PROPERTY", "METHOD", "ANNOTATION"})
 */
class Dependent extends Constraint
{
    /*
     * Any public properties become valid options for the annotation.
     * Then, use these in your validator class.
     */
    public $message = 'Another values required';
    public $options;

    public function validatedBy()
    {
        return \get_class($this).'Validator';
    }

    public function getDefaultOption()
    {
        return [];
    }

    public function __construct(?array $options = [])
    {
        parent::__construct($options);
    }

    public function getOptions()
    {
        return $this->options;
    }

    public function setMessage($message)
    {
        $this->message = $message;
    }

}
