<?php

namespace Oguzhankrcb\DataMigrator\Traits;

trait FieldTokenizer
{
    private function addToken(array &$tokens, string &$token): void
    {
        if ($token !== '') {
            $tokens[] = $token;
            $token = '';
        }
    }

    public function tokenizeField(string $string): array
    {
        $tokens = [];
        $token = '';

        for ($i = 0, $iMax = strlen($string); $i < $iMax; $i++) {
            if ($string[$i] === '[') {
                $this->addToken($tokens, $token);

                continue;
            }

            if ($string[$i] === ']') {
                $this->addToken($tokens, $token);

                continue;
            }

            if ($string[$i] === ' ') {
                $this->addToken($tokens, $token);
                $tokens[] = ' ';

                continue;
            }

            $token .= $string[$i];
        }

        $this->addToken($tokens, $token);

        return $tokens;
    }
}
