<?php

namespace Adadgio\DoctrineDQLBundle\Common;

class Where
{
    public static function hello()
    {
        echo "Hello";
    }

    public static function andWhere($baseAlias, \Doctrine\ORM\QueryBuilder $builder, array $where = array())
    {
        $uniq = 0;

        foreach ($where as $f => $value) {
            // $field = $field.$uniq;

            $ex = explode('.', $f);
            if (count($ex) === 2) {
                // the alias + field(s) is overriden !
                $field = $ex[1];
                $alias = $ex[0];
            } else {
                $field = $f;
                $alias = $baseAlias;
            }

            if ($field === '($OR)') {

                // add query builder andXOr statement
                $builderExprs = array();

                foreach ($value as $nestedField => $nestedValue) {
                    $subCondition = self::expressionStatement($builder, $alias, $nestedField, $uniq);
                    $builderExprs[] = $subCondition['builder_expression'];
                    $builder->setParameter($subCondition['field'].$uniq, $nestedValue);

                    $uniq++;
                }

                $orX = $builder->expr()->orX();
                $orX->addMultiple($builderExprs);
                $builder->andWhere($orX);

            } else {
                // add a scalar statement
                $type = self::scalarStatement($field);
                $condition = self::createBuilderCondition($alias, $type, $value);

                // transform value for LIKE(s)
                if ($type['operator'] === 'LIKE') {
                    $value = '%'. str_replace('%', '', $value) .'%';
                }
                // transform value for LIKE(s)
                if ($type['operator'] === 'NOT LIKE') {
                    $value = '%'. str_replace('%', '', $value) .'%';
                }

                // special treatment for IS condition (IS NULL|NOT NULL)
                if ($type['operator'] === 'IS') {
                    $builder
                        ->andWhere($condition);
                } else if ($type['operator'] === 'BETWEEN') {
                    $builder
                        ->andWhere($condition)
                        ->setParameter($type['parameter'][0], $value[0])
                        ->setParameter($type['parameter'][1], $value[1]);
                } else {
                    $builder
                        ->andWhere($condition)
                        ->setParameter($type['field'], $value);
                }
            }

            $uniq++;
        }

        return $builder;
    }

    public static function createBuilderCondition($alias, $type, $value)
    {
        if ($type['operator'] === 'IS') {

            $condition = sprintf('%s.%s %s %s', $alias, $type['field'], $type['operator'], $value);

        } else if ($type['operator'] === 'BETWEEN') {

            $condition = sprintf('%s.%s BETWEEN %s AND %s', $alias, $type['field'], $type['parameter'][0], $type['parameter'][1]);

        } else {

            $condition = sprintf('%s.%s %s %s', $alias, $type['field'], $type['operator'], $type['parameter']);
        }

        return $condition;
    }

    /**
     * @todo Handles all mysql operator(s) expression(s)
     */
    public static function expressionStatement($builder, $alias, $field, $uniq)
    {
        $type = self::scalarStatement($field);

        $condition = sprintf('%s.%s %s %s', $alias, $type['field'], $type['operator'], $type['parameter']);

        switch ($type['operator']) {
            case 'LIKE':
                $builderExpr = $builder->expr()->like(sprintf('%s.%s', $alias, $type['field']), sprintf(':%s', $type['field'].$uniq));
            break;
            case 'NOT LIKE':
                $builderExpr = $builder->expr()->like(sprintf('%s.%s', $alias, $type['field']), sprintf(':%s', $type['field'].$uniq));
            break;
            case 'BETWEEN':
                // @todo...
            break;
            default:
                $builderExpr = $builder->expr()->eq(sprintf('%s.%s', $alias, $type['field']), sprintf(':%s', $type['field'].$uniq));
            break;
        }

        return array(
            'field' => $type['field'],
            'builder_expression' => $builderExpr,
        );
    }

    public static function scalarStatement($field)
    {
        $operator = '='; // default operator
        $parameter = sprintf(':%s', $field); // default param form

        $operators = array('>', '<', '>=', '<=', 'IN', 'NOT IN', 'LIKE', 'IS', 'LLIKE', 'RLIKE', 'BETWEEN', 'NOT LIKE'); // possible other operators

        $regex = sprintf('~[a-zA-Z0-9_]+\((\$(?:%s){1})\)~', implode('|', $operators));

        if (preg_match($regex, $field, $matches)) {

            $field = str_replace('('.$matches[1].')', '', $matches[0]); // remove the special expression found
            $operator = str_replace(array('(', ')', '$'), '', $matches[1]);

            // "IN" required parenthesis (the only special value)
            if ($operator === 'IN' OR $operator === 'NOT IN') {

                $parameter = sprintf('(:%s)', $field); // default param form

            } else if ($operator === 'IS') {

                $parameter = null;

            } else if ($operator === 'BETWEEN') {

                $parameter = array(
                    sprintf(':%s', $field.'1'),
                    sprintf(':%s', $field.'2'),
                );

            } else {
                $parameter = sprintf(':%s', $field); // default param form
            }
        }

        $data = array(
            'field'     => $field,
            'operator'  => $operator,
            'parameter' => $parameter,
        );

        return $data;
    }
}
