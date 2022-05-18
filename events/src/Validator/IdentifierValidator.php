<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class IdentifierValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        /* @var $constraint \App\Validator\Identifier */

        if (null === $value || '' === $value) {
            return;
        }

        $formData = $this->context->getObject()->getParent()->getData();
        $recordType = $formData->getRecordType();

        if ($recordType <= 1 && !$this->cpfCnpjValidate($value)) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ value }}', $value)
                ->addViolation();
        }
    }

    private function clear($value)
    {
        return preg_replace('/[^\d]/', '', $value);
    }

    private function cnpjValidate($value): bool
    {
        $c = $this->clear($value);

        if (mb_strlen($c) != 14 || preg_match("/^{$c[0]}{14}$/", $c)) {
            return false;
        }

        $b = [6, 5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];

        for (
            $i = 0, $n = 0; $i < 12; $n += $c[$i] * $b[++$i]
        ) {
        }

        if ($c[12] != ((($n %= 11) < 2) ? 0 : 11 - $n)) {
            return false;
        }

        for (
            $i = 0, $n = 0; $i <= 12; $n += $c[$i] * $b[$i++]
        ) {
        }

        if ($c[13] != ((($n %= 11) < 2) ? 0 : 11 - $n)) {
            return false;
        }

        return true;
    }

    public function cpfValidate($value) : bool
    {
        $c = $this->clear($value);

        if (mb_strlen($c) != 11 || preg_match("/^{$c[0]}{11}$/", $c)) {
            return false;
        }

        for (
            $s = 10, $n = 0, $i = 0; $s >= 2; $n += $c[$i++] * $s--
        ) {
        }

        if ($c[9] != ((($n %= 11) < 2) ? 0 : 11 - $n)) {
            return false;
        }

        for (
            $s = 11, $n = 0, $i = 0; $s >= 2; $n += $c[$i++] * $s--
        ) {
        }

        if ($c[10] != ((($n %= 11) < 2) ? 0 : 11 - $n)) {
            return false;
        }

        return true;
    }

    public function cpfCnpjValidate($value) :bool
    {
        return $this->cpfValidate($value) || $this->cnpjValidate($value);
    }
}
