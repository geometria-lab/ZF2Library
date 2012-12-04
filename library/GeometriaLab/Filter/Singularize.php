<?php

namespace GeometriaLab\Filter;

use \Zend\Filter\FilterInterface as ZendFilterInterface;

class Singularize implements ZendFilterInterface
{
    /**
     * @var array
     */
    protected $singular = array (
        '/(quiz)zes$/i' => '\1',
        '/(matr)ices$/i' => '\1ix',
        '/(vert|ind)ices$/i' => '\1ex',
        '/^(ox)en/i' => '\1',
        '/(alias|status)es$/i' => '\1',
        '/([octop|vir])i$/i' => '\1us',
        '/(cris|ax|test)es$/i' => '\1is',
        '/(shoe)s$/i' => '\1',
        '/(o)es$/i' => '\1',
        '/(bus)es$/i' => '\1',
        '/([m|l])ice$/i' => '\1ouse',
        '/(x|ch|ss|sh)es$/i' => '\1',
        '/(m)ovies$/i' => '\1ovie',
        '/(s)eries$/i' => '\1eries',
        '/([^aeiouy]|qu)ies$/i' => '\1y',
        '/([lr])ves$/i' => '\1f',
        '/(tive)s$/i' => '\1',
        '/(hive)s$/i' => '\1',
        '/([^f])ves$/i' => '\1fe',
        '/(^analy)ses$/i' => '\1sis',
        '/((a)naly|(b)a|(d)iagno|(p)arenthe|(p)rogno|(s)ynop|(t)he)ses$/i' => '\1\2sis',
        '/([ti])a$/i' => '\1um',
        '/(n)ews$/i' => '\1ews',
        '/status/i'  => 'status',
        '/s$/i' => '',
    );
    /**
     * @var array
     */
    protected $uncountable = array(
        'equipment',
        'information',
        'rice',
        'money',
        'species',
        'series',
        'fish',
        'sheep'
    );
    /**
     * @var array
     */
    protected $irregular = array(
        'person' => 'people',
        'man' => 'men',
        'child' => 'children',
        'sex' => 'sexes',
        'move' => 'moves'
    );
    /**
     * @var array
     */
    protected static $cachedWords = array();

    /**
     * Returns the result of filtering $value
     *
     * @param string $word
     * @return string
     */
    function filter($word)
    {
        if (isset(static::$cachedWords[$word])) {
            return static::$cachedWords[$word];
        }

        $lowercaseWord = strtolower($word);
        foreach ($this->uncountable as $uncountable){
            if (substr($lowercaseWord,(-1 * strlen($uncountable))) == $uncountable) {
                return static::$cachedWords[$word] = $word;
            }
        }

        foreach ($this->irregular as $plural=> $singular) {
            $arr = array();
            if (preg_match('/(' . $singular . ')$/i', $word, $arr)) {
                return static::$cachedWords[$word] = preg_replace('/(' . $singular . ')$/i', substr($arr[0], 0, 1) . substr($plural, 1), $word);
            }
        }

        foreach ($this->singular as $rule => $replacement) {
            if (preg_match($rule, $word)) {
                return static::$cachedWords[$word] = preg_replace($rule, $replacement, $word);
            }
        }

        return $word;
    }
}