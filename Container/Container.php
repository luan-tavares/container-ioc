<?php

namespace Container;

use Closure;
use Exception;
use ReflectionClass;
use stdClass;

abstract class Container
{
    protected static $instances = []; /* chave bindTo valor */

    protected static $isSingleton = []; /*Instancia unica ou não*/

    protected static $lastInstance = [];

    private static $createdObjects = [];

    private static function getInstanceId($instance)
    {
        try {
            $in = self::get($instance);
            return "Object #" . spl_object_id($in);
        } catch (Exception $e) {
            return "Não está em Container";
        }
    }

    public static function getAllObjects()
    {
        return self::$createdObjects;
    }

    public static function getAll()
    {
        $json = [];

        foreach (self::$instances as $abstract => $concrete) {
            $singleton = (self::$isSingleton[$abstract]) ? ("Singleton: ") : ("");

            $concreteType = self::whatIs($concrete);

            $json[$abstract] = new stdClass;

            $json[$abstract]->referenceTo = ($concreteType == "Closure") ? ($concreteType) : ($concrete . " : " . $concreteType);

            $json[$abstract]->type = $singleton . (($abstract == $concrete) ? ("Self Instance") : (self::whatIs($abstract) . " bindTo " . self::whatIs($concrete)));

            $json[$abstract]->action = (self::$lastInstance[$abstract] == null) ? ("Just Bind") : ("Make a Instance of " . self::$lastInstance[$abstract]->class);

            $json[$abstract]->abstractObjectId = self::getInstanceId($abstract);
        }

        return $json;
    }

    public static function getAllInstances()
    {
        return self::$instances;
    }

    private static function set($abstract, $concrete = null, $shared = false)
    {
        if ($concrete === null) {
            $concrete = $abstract;
        }

        self::$instances[$abstract]    = $concrete;
        self::$lastInstance[$abstract] = null;

        self::$isSingleton[$abstract] = $shared;
    }

    public static function bind($abstract, $concrete = null)
    {
        return self::set($abstract, $concrete);
    }

    public static function singleton($abstract, $concrete = null)
    {
        return self::set($abstract, $concrete, true);
    }

    public static function get($abstract, $parameters = [])
    {
        // if we don't have it, just register it
        if (!isset(self::$instances[$abstract])) {
            self::set($abstract);
        }
        return self::resolve($abstract, $parameters);
    }

    private static function whatIs($abstract)
    {
        try {
            $teste = new ReflectionClass($abstract);

            if ($teste->isInterface()) {
                return "Interface";
            }

            if ($abstract instanceof Closure) {
                return "Closure";
            }

            return "Class";
        } catch (Exception $e) {
            return "Expression";
        }
    }

    private static function resolve($abstract, $parameters)
    {
        if (self::$lastInstance[$abstract] != null and self::$isSingleton[$abstract]) {
            return self::$lastInstance[$abstract]->instance;
        } else {
            $concrete = self::$instances[$abstract];

            if (self::whatIs($concrete) == "Closure" and self::$isSingleton[$abstract]) {
                throw new Exception("Method singleton(): Bind {$abstract} second param must be Class.");
            }

            $hasRef = true;

            while ($hasRef) {
                if (self::whatIs($concrete) == "Closure") {
                    return $concrete($parameters = []);
                }

                if (array_key_exists($concrete, self::$instances)) {
                    if ($concrete != self::$instances[$concrete]) {
                        $concrete = self::$instances[$concrete];
                    } else {
                        $hasRef = false;
                    }
                } else {
                    $hasRef = false;
                }
            }

            self::$lastInstance[$abstract] = new stdClass;

            self::$lastInstance[$abstract]->class = $concrete;

            $reflector = new \ReflectionClass($concrete);
            // check if class is instantiable
            if (!$reflector->isInstantiable()) {
                throw new Exception("Class {$concrete} is not instantiable");
            }

            //if (!$reflector->isSubclassOf($abstrato)) {
            //    throw new Exception("Class {$concrete} is not subClass of {$abstrato}");
            //}
            // get class constructor
            $constructor = $reflector->getConstructor();
            if (is_null($constructor)) {
                // get new instance from class

                self::$lastInstance[$abstract]->instance = $reflector->newInstance();
                $idInstance = spl_object_id(self::$lastInstance[$abstract]->instance);
                self::$createdObjects[$idInstance] = self::$lastInstance[$abstract]->class;
                return self::$lastInstance[$abstract]->instance;
            }
            // get constructor params

            // get new instance with dependencies resolved
            $parameters = $constructor->getParameters();

            $dependencies = self::getDependencies($parameters);

            self::$lastInstance[$abstract]->instance = $reflector->newInstanceArgs($dependencies);

            $idInstance = spl_object_id(self::$lastInstance[$abstract]->instance);

            self::$createdObjects[$idInstance] = self::$lastInstance[$abstract]->class;

            return self::$lastInstance[$abstract]->instance;
        }
    }
    /**
     * get all dependencies resolved
     *
     * @param $parameters
     *
     * @return array
     * @throws Exception
     */
    public static function getDependencies($parameters)
    {
        $dependencies = [];

        foreach ($parameters as $parameter) {
            $dependency = $parameter->getClass();

            if ($dependency === null) {
                if ($parameter->isDefaultValueAvailable()) {
                    $dependencies[] = $parameter->getDefaultValue();
                } else {
                    throw new Exception("Can not resolve class dependency {$parameter->name}");
                }
            } else {
                $dependencies[] = self::get($dependency->name);
            }
        }

        return $dependencies;
    }
}