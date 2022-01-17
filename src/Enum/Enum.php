<?php


namespace App\Enum;


class Enum
{
    private $enum;

    private function __construct($var)
    {
        $this->enum = $var;
    }

    /**
     * @param $var
     * @return static
     */
    static public function getInstance($var)
    {
        return new static($var);
    }

    /**
     * @return array
     */
    static public function all()
    {
        try {
            $refl = new \ReflectionClass(get_called_class());
            return $refl->getConstants();
        } catch (\ReflectionException $e) {
            return [];
        }
    }

    /**
     * @return array
     */
    static public function values()
    {
        return array_values(self::all());
    }

    /**
     * @return array
     */
    static public function keys()
    {
        return array_keys(self::all());
    }

    /**
     * @param $var
     * @return bool
     */
    public function is($var)
    {
        return ($this->enum == $var);
    }
}