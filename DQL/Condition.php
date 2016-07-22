<?php

namespace Adadgio\DoctrineDQLBundle\DQL;

class Condition
{
    private $alias;

    private $glue; // before condition, "AND" or "OR"

    private $field;

    private $operator;

    private $param;

    private $value;

    public function __construct($alias, $field, $operator, $param, $value, $glue = null)
    {
        $this->glue = $glue;
        $this->alias = $alias;
        $this->field = $field;
        $this->param = $param;
        $this->isBetween = false;
        $this->operator = $operator;
        
        // setting the value, use a modifier when special operators are found
        switch ($operator) {
            case 'LIKE':
                $this->value = '%'.$value.'%';
            break;
            case 'RLIKE':
                $this->operator = 'LIKE';
                $this->value = $value.'%';
            break;
            case 'LLIKE':
                $this->operator = 'LIKE';
                $this->value = '%'.$value;
            break;
            case 'IN':
                $this->value = implode(',', $value);
            break;
            case 'NOT IN':
                $this->value = implode(',', $value);
            break;
            case 'BETWEEN':
                $this->isBetween = true;
                $this->value = (array) $value; // is now an array
                $this->param = array($param.'a' => $value[0], $param.'b' => $value[1]);
            break;
            default;
                $this->value = $value;
            break;
        }
    }

    public function isBetween()
    {
        return $this->isBetween;
    }

    public function getGlue()
    {
        return $this->glue;
    }

    public function setGlue($glue)
    {
        $this->glue = $glue;

        return $this;
    }

    public function getAlias()
    {
        return $this->alias;
    }

    public function getField()
    {
        return $this->field;
    }

    public function getAliasAndField()
    {
        return $this->alias.'.'.$this->field;
    }

    public function getParam($index = null)
    {
        return (null !== $index) ? array_values($this->param)[$index] : $this->param;
    }

    public function getParamKey($index)
    {
        return array_keys($this->param)[$index];
    }

    public function getAbstractParam($index = null)
    {
        return (null !== $index) ? ':'.$this->param[$index] : ':'.$this->param;
    }

    public function getOperator()
    {
        return $this->operator;
    }

    public function getValue($index = null)
    {
        return (null !== $index) ? $this->value[$index] : $this->value;
    }

    public function getStatement()
    {
        switch ($this->operator) {
            case 'BETWEEN':
                // we need to create two params... (but unique!)
                $paramKeys = array_keys($this->param);
                $stmt = sprintf('%s.%s BETWEEN', $this->alias, $this->field);
                $stmt .= sprintf(' :%s AND :%s', $paramKeys[0], $paramKeys[1]);
                return $stmt;
            break;
            case 'IN':
                return sprintf('%s.%s %s (:%s)', $this->alias, $this->field, $this->operator, $this->param);
            break;
            case 'NOT IN':
                return sprintf('%s.%s %s (:%s)', $this->alias, $this->field, $this->operator, $this->param);
            break;
            default:
                return sprintf('%s.%s %s :%s', $this->alias, $this->field, $this->operator, $this->param);
            break;
        }
    }
}
