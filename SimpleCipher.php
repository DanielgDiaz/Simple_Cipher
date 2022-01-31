<?php

/*
 * By adding type hints and enabling strict type checking, code can become
 * easier to read, self-documenting and reduce the number of potential bugs.
 * By default, type declarations are non-strict, which means they will attempt
 * to change the original type to match the type specified by the
 * type-declaration.
 *
 * In other words, if you pass a string to a function requiring a float,
 * it will attempt to convert the string value to a float.
 *
 * To enable strict mode, a single declare directive must be placed at the top
 * of the file.
 * This means that the strictness of typing is configured on a per-file basis.
 * This directive not only affects the type declarations of parameters, but also
 * a function's return type.
 *
 * For more info review the Concept on strict type checking in the PHP track
 * <link>.
 *
 * To disable strict typing, comment out the directive below.
 */

declare(strict_types=1);

class SimpleCipher
{
    public $key;

    const KEY_RAN_MIN_LENGTH = 100;
    const KEY_RAN_MAX_LENGTH = 256;

    public function __construct(string $key = null){                                          //Si no se proporciona la key se da una aleatoria            
        if ($key === null) {
            $this->key = $this->generateRandomKey();
            return;
        }

        if ($key === '') {
            throw new InvalidArgumentException('Empty key is not valid');                    //Invalidar string vacio        
        }

        $keyDismiss = preg_replace('/[^-a-z\\/]+/', '', $key);                              //Reemplazar caracteres que no esten entre a-z
        if (strlen($keyDismiss) !== strlen($key)) {                                         //comprobamos la longitud de string key, si es diferente es que hay caracteres invalidos
             throw new InvalidArgumentException('Only a-z are valid characters');
        }

        $this->key = $key;
    }

    public function generateRandomKey(): string{
        $keyLength = rand(SimpleCipher::KEY_RAN_MIN_LENGTH, SimpleCipher::KEY_RAN_MAX_LENGTH);     //Crear una length key aleatoria
        $keyValues = 'abcdefghijklmnopqrstuvwxyz';                                                 //Caracteres validos
        $cipherKey = '';                                                                           //Inicializar string cifrado
        $i = 0;                                                                                    //Inicializar Iterador
        while($i < $keyLength) {
            $cipherKey .= $keyValues[rand(0, 25)];                                                 //Añadir elementos aleatorios de la lista de caracteres validos
            $i++;
        }
        return $cipherKey;
    }

    public function encode(string $plainText): string{
        $plainTextArray = str_split($plainText);                                                //Convertir string en array
        $encodedString  = '';

        array_map(
            function ($index, $plainTextChar) use (&$encodedString) {                                       //Calcula valor ASCII. Restar 97 para asegurar el calculo
                $newAsciiValue = ord($plainTextChar) + $this->getCipherKeyCharacter($index) - 97;

                if ($newAsciiValue > 122) {                                                                 //Si el valor resultante es mayor que z (122), restamos 122                                                                                                           
                    $newAsciiValue = $newAsciiValue - 122 + 96;                                             //Sumamos 96 para corregir (a)
                }
                $encodedString .= chr($newAsciiValue);
            },
            array_keys($plainTextArray),
            $plainTextArray
        );
        return $encodedString;
    }

    public function decode(string $cipherText): string{
        $cipherTextArray = str_split($cipherText);
        $decodedString   = '';
        array_map(
            function ($index, $cipherTextChar) use (&$decodedString) {
                $newAsciiValue = ord($cipherTextChar) - $this->getCipherKeyCharacter($index) + 97;       //Calcular valor ASCII, sumamos 97
                if ($newAsciiValue < 97) {                                                               //Si obtenemos valor menos que a (97) restamos 96 y le sumamos 122 (z)
                    $newAsciiValue = 122 + ($newAsciiValue - 96);
                }
                $decodedString .= chr($newAsciiValue);
            },
            array_keys($cipherTextArray),
            $cipherTextArray
        );
        return $decodedString;
    }
    protected function getCipherKeyCharacter(int $index): int{                                        //Obtener el valor de index
        $keyIndex = $index % strlen($this->key);
        return ord($this->key[$keyIndex]);
    }
}
