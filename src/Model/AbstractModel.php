<?php

namespace Apirone\Invoice\Model;

use stdClass;

abstract class AbstractModel
{
    /**
     * @param string $name
     *
     * @return mixed
     */
    public function __get($name)
    {
        $name = static::convertToCamelCase($name);
        if (\property_exists($this, $name)) {

            $class = new \ReflectionClass(static::class);

            $property = $class->getProperty($name);
            $property->setAccessible(true);

            if(!$property->isInitialized($this)) {
                return null;
                // $property->setValue($this, null);
            }

            return $property->getValue($this);
        }

        $trace = \debug_backtrace();
        \trigger_error(
            'Undefined property '.$name.
            ' in '.$trace[0]['file'].
            ' on line '.$trace[0]['line'],
            \E_USER_NOTICE
        );

        return null;
    }

    // protected function classLoader($json, $instance)
    protected function classLoader($json)
    {
        $json = gettype($json) == 'string' ? json_decode($json) : $json;

        $class = new \ReflectionClass(static::class);

        // $this->toArray();

        foreach ($json as $key => $value) {
            $name = static::convertToCamelCase($key);
            if (\property_exists($this, $name)) {
                $property = $class->getProperty($name);
                $property->setAccessible(true);
                if (gettype($value) == 'object' || gettype($value) == 'array') {
                    $parser = 'parse' . ucfirst($name);
                    if ($class->hasMethod($parser)) {
                        $property->setValue($this, $this->$parser($value));
                    }
                    else {
                        $property->setValue($this, $value);
                    }
                }
                else {
                    $property->setValue($this, $value);
                }
            }
        }

        return $this;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        $settings = [];    
        $class = new \ReflectionClass(static::class);

        foreach ($class->getProperties() as $property) {
            $prop = $property->getName();
            $settings[self::convertToSnakeCase($prop)] = $this->{$prop};
        }

        return $settings;
    }

    public function toJson(): \stdClass
    {
        return json_decode(json_encode($this->toArray()));
    }

    /**
     * @param string $date DateTime string
     *
     * @return string
     */
    protected static function convertToIso8601(string $date): string
    {
        $date = new \DateTime($date);
        $date->setTimezone(new \DateTimeZone(\date_default_timezone_get()));

        return $date->format(\DateTime::ATOM);
    }

    /**
     * @param string $str
     *
     * @return string
     */
    protected static function convertToCamelCase(string $str): string
    {
        $callback = function ($match): string {
            return \strtoupper($match[2]);
        };

        $replaced = \preg_replace_callback('/(^|-)([a-z])/', $callback, $str);

        if (null === $replaced) {
            throw new RuntimeException(\sprintf('preg_replace_callback error: %s', \preg_last_error_msg()));
        }
        return \lcfirst($replaced);
    }

    /**
     * @param string $str
     *
     * @return string
     */
    protected static function convertToSnakeCase(string $str): string
    {
        $replaced = \preg_split('/(?=[A-Z])/', $str);

        if (false === $replaced) {
            throw new RuntimeException(\sprintf('preg_split error: %s', \preg_last_error_msg()));
        }

        return \strtolower(\implode('-', $replaced));
    }
}
