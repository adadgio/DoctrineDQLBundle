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
        $this->operator = $operator;

        // setting the value, use a modifier when special operators are found
        switch ($operator) {
            case 'LIKE':
                $this->value = '%'.$this->value.'%';
            break;
            case 'RLIKE':
                $this->operator = 'LIKE';
                $this->value = $this->value.'%';
            break;
            case 'LLIKE':
                $this->operator = 'LIKE';
                $this->value = '%'.$this->value;
            break;
            case 'IN':
                //$this->value = implode(',', $this->value);
                $this->value = implode(',', $value);
            break;
            case 'NOT IN':
                //$this->value = implode(',', $this->value);
                $this->value = implode(',', $value);
            break;
            default;
                // no modification
                $this->value = $value;
            break;
        }
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

    public function getParam()
    {
        return $this->param;
    }

    public function getOperator()
    {
        return $this->operator;
    }

    public function getValue()
    {
        return $this->value;
    }

    // create statement ?
    public function getStatement()
    {
        switch ($this->operator) {
            case 'BETWEEN':
                // we need to create two params... (but unique!)
                $paramA = $this->param.'a';
                $paramB = $this->param.'b';

                $stmt = sprintf('%s.%s BETWEEN', $this->alias, $this->field);
                $stmt .= sprintf(' :%s AND :%s', $paramA, $paramB);
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
