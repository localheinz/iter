<?php

namespace iter\fn;

function index($index) {
    return function($array) use ($index) {
        return $array[$index];
    };
}

/**
 * Returns a callable which returns an item from a nested array corresponding
 * to the given path.
 *
 * Examples:
 *
 *     $array = [
 *         'foo' => [
 *             'bar' => [
 *                 'baz' => 42
 *             ]
 *         ]
 *     ];
 *
 *     $getIndexFooBar = fn\nested_index('foo', 'bar');
 *     $getIndexFooBarBaz = fn\nested_index('foo', 'bar', 'baz');
 *
 *     $getIndexFooBar($array)
 *     => ['baz' => 42]
 *
 *     $getIndexFooBarBaz($array)
 *     => 42
 *
 * @param mixed[] ...$indices Path of indices
 *
 * @return callable
 */
function nested_index(...$indices) {
    return function($array) use ($indices) {
        foreach ($indices as $index) {
            $array = $array[$index];
        }

        return $array;
    };
}

function property($propertyName) {
    return function($object) use ($propertyName) {
        return $object->$propertyName;
    };
}

function method($methodName, $args = []) {
    return function($object) use ($methodName, $args) {
        return $object->$methodName(...$args);
    };
}

function operator($operator, $arg = null) {
    $functions = [
        'instanceof' => function($a, $b) { return $a instanceof $b; },
        '*'   => function($a, $b) { return $a *   $b; },
        '/'   => function($a, $b) { return $a /   $b; },
        '%'   => function($a, $b) { return $a %   $b; },
        '+'   => function($a, $b) { return $a +   $b; },
        '-'   => function($a, $b) { return $a -   $b; },
        '.'   => function($a, $b) { return $a .   $b; },
        '<<'  => function($a, $b) { return $a <<  $b; },
        '>>'  => function($a, $b) { return $a >>  $b; },
        '<'   => function($a, $b) { return $a <   $b; },
        '<='  => function($a, $b) { return $a <=  $b; },
        '>'   => function($a, $b) { return $a >   $b; },
        '>='  => function($a, $b) { return $a >=  $b; },
        '=='  => function($a, $b) { return $a ==  $b; },
        '!='  => function($a, $b) { return $a !=  $b; },
        '===' => function($a, $b) { return $a === $b; },
        '!==' => function($a, $b) { return $a !== $b; },
        '&'   => function($a, $b) { return $a &   $b; },
        '^'   => function($a, $b) { return $a ^   $b; },
        '|'   => function($a, $b) { return $a |   $b; },
        '&&'  => function($a, $b) { return $a &&  $b; },
        '||'  => function($a, $b) { return $a ||  $b; },
        '**'  => function($a, $b) { return $a **  $b; },
        '<=>' => function($a, $b) { return $a <=> $b; },
    ];

    if (!isset($functions[$operator])) {
        throw new \InvalidArgumentException("Unknown operator \"$operator\"");
    }

    $fn = $functions[$operator];
    if (func_num_args() === 1) {
        return $fn;
    } else {
        return function($a) use ($fn, $arg) {
            return $fn($a, $arg);
        };
    }
}

function not($function) {
    return function(...$args) use ($function) {
        return !$function(...$args);
    };
}