<?php

namespace Adadgio\DoctrineDQLBundle\DQL;

class Where
{
    private $alias;

    private $parameters;

    private $conditions;

    private $defaultOperator = '=';

    private $operators = array('>', '<', '>=', '<=', 'IN', 'NOT IN', 'LIKE', 'IS', 'LLIKE', 'RLIKE', 'BETWEEN', 'NOT LIKE');

    /**
     * Constructor, also sets default variables
     *
     * @param string Default alias used in the repository base builder
     * @param array  Where condition with (possibly) special operators
     */
    public function __construct($alias, array $where = array())
    {
        $this->alias = $alias;
        $this->parameters = array();
        $this->conditions = array();

        $this->conditions = $this->createNestedConditions($alias, $where);
    }

    /**
     * Get conditions, for testing and debug purposes.
     *
     * @param array \Conditions(s)
     */
    public function getConditions()
    {
        return $this->conditions;
    }

    /**
     * Get parameters, for each condition.
     *
     * @param array \Conditions(s)
     */
    public function getParametersValues(array $conditions = array())
    {
        $parameters = array();
        // if (empty($conditions)) {
        //     $conditions = $this->conditions;
        // }
        //
        // foreach ($this->conditions as $condition) {
        //     if (is_array($condition)) {
        //
        //     } else {
        //         $key = $condition->getParam();
        //         $parameters[$key] = $condition->getValue();
        //     }
        // }
        //
        // return $parameters;
    }

    /**
     * Returns conditions, for testing and debug purposes.
     *
     * @param array \Conditions(s)
     */
    public function getConditionsStatements(array $conditions = array())
    {
        $statements = array();
        if (empty($conditions)) {
            $conditions = $this->conditions;
        }

        foreach ($conditions as $condition) {
            if (is_array($condition)) {
                // handle nested conditions as recursive
                $statements[] = $this->getConditionsStatements($condition);
            } else {
                // scalar conditions
                $statements[] = $condition->getStatement();
            }
        }

        return $statements;
    }

    /**
     * Create conditions, as possible nested recursive structure.
     * The array of conditions means that each condition is separated later
     * by the "AND" keyword, and nested conditions by the "OR" keyword.
     *
     * @param  string Default alias (my be overrien later by field naming with dot "e.field")
     * @param  array  Array of conditions with possibly special operators
     * @return array Nested (or not) \Condition(s) objects
     */
    public function createNestedConditions($alias, array $where = array())
    {
        $conditions = array();

        foreach ($where as $field => $value) {
            if ($field === '($OR)') {
                // nested conditions, use OR, and value is another array of conditions
                $conditions[] = $this->createNestedConditions($alias, $value);

            } else {
                // scalar conditions, use AND
                $conditions[] = $this->createScalarCondition($alias, $field, $value);
            }
        }

        return $conditions;
    }

    /**
     * Create a scalar condition (opposed to nested).
     *
     * @param  string Special operator, like ($LIKE), ($>=), etc
     * @param  mixed  Condition value
     * @return object \Condition
     */
    public function createScalarCondition($alias, $field, $value)
    {
        $regex = $this->getRegex();

        // remove "e.field" dots when the key contains a custom alias
        // and also get a clean field name then
        $alias = $this->getCustomAlias($field); // get possible custom alias
        $field = $this->getUnaliasedFieldName($field); // removes the field custom alias

        if (preg_match($regex, $field, $matches)) {

            $fieldName = $this->getFieldName($matches);
            $operator = $this->getOperator($matches);
            $param = $this->createIndexedParameter($fieldName); // create unique abstract param name

            $condition = new Condition($alias, $fieldName, $operator, $param, $value);

        } else {

            $param = $this->createIndexedParameter($field); // create unique abstract param name

            $condition = new Condition($alias, $field, $this->defaultOperator, $param, $value);
        }

        // set "AND" or "OR" glue
        //$condition->setGlue($glue);

        return $condition;
    }

    /**
     * Get the regex that guesses the comparison(s) operator(s).
     *
     * @return string Pcre regex
     */
    public function getRegex()
    {
        return sprintf('~[a-zA-Z0-9_\.]+\((\$(?:%s){1})\)~', implode('|', $this->operators));
    }

    /**
     * Create a numbered index unique parameter name,
     * also needs a clean field name without alias.
     *
     * @param  string Field name (unaliased)
     * @return string Parameter name, suffixed by a unique index
     */
    public function createIndexedParameter($field)
    {
        $index = count($this->parameters);
        
        $parameter = $field.$index;
        $this->parameters[] = $parameter;

        return $parameter;
    }

    /**
     * Normalizes the field name if it contains a custom alias "e.field" to "field".
     *
     * @param string  Possibly aliased field name
     * @return string Unaliased field name
     */
    public function getUnaliasedFieldName($field)
    {
        $exp = explode('.', $field);

        return end($exp);
    }

    /**
     * Explode a custom alias field name form "e.field".
     *
     * @param  string  Custom (or not) alias and field name
     * @return string Clean field name
     */
    public function getCustomAlias($field)
    {
        $exp = explode('.', $field);

        return (count($exp) === 2) ? $exp[0] : $this->alias;
    }

    /**
     * Get field name from custom expression "field($LIKE)".
     *
     * @param  array  Regex matches
     * @return string Normalized simple field name
     */
    public function getFieldName(array $regexMatches)
    {
        $field = str_replace('('.$regexMatches[1].')', '', $regexMatches[0]);

        return $field;
    }

    /**
     * Get operator found in the special expression.
     *
     * @param  string Special expression like "($LIKE)"
     * @return string Operator such as "LIKE"
     */
    public function getOperator(array $regexMatches)
    {
        $operator = str_replace(array('(', ')', '$'), '', $regexMatches[1]);

        return $operator;
    }
}
