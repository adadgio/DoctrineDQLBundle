DoctrineDQL Bundle
====

A set of simple helper to help reduce the size of your Symfony repositories.

## Install

Install with composer.

`composer require adadgio/doctrine-dql-bundle`

Make the following change to your `AppKernel.php` file to the registered bundles array.

```php
new Adadgio\DoctrineDQLBundle\AdadgioDoctrineDQLBundle(),
```

## Examples & usage

```php
use Adadgio\DoctrineDQLBundle\DQL\Where;
use Adadgio\DoctrineDQLBundle\DQL\Limit;
use Adadgio\DoctrineDQLBundle\DQL\Offset;
use Adadgio\DoctrineDQLBundle\DQL\OrderBy;

use Adadgio\DoctrineDQLBundle\Collection\IndexedCollection;
use Adadgio\DoctrineDQLBundle\Collection\ColumnAccessor;

// assuming a Symfony repository: and I am a thin repository method
public function findBy(array $where = array(), array $orderBy = array(), $limit = null, $offset)
{
    $builder = $this->createQueryBuilder('e');
    $builder->leftJoin('e.friends', 'f')

    // killer feature, no options resolver or if...then(s) !
    $builder = (new Where('e', $where))->digest($builder);

    // order by statements
    $builder = OrderBy::orderBy($where);

    $builder->setMaxResults(Limit::enforce($limit)); // will never be more than 1000
    // $builder->setMaxResults(Limit::enforce($limit, Limit::NO_LIMIT)); // could eventually be more than 1000
    $builder->setFirstResult(Offset::offset($offset)); // doesn't do a lot, just for integer conversion

    $collection = $builder->getQuery()->getResult(); // standard symfony common saying

    // now lets say i want the collection indexed by id (for any other further usage)
    $collection = IndexedCollection::indexBy($collection, '[id]'); // nb: would also work with pure arrays

    // if i also want a list of ids (its a free country no?)
    // $ids = ColumnAccessor::getColumnValues($collection, '[id]');

    return $collection;
}
```

... and the corresponding input

```php
$where = array(
    'id'            => 2,               // will translate to e.id = 2
    'name($LIKE)'   => 'Tom Sawyer',    // will translate to e.name LIKE '%Tom Sawyer%'
    'id($IN)'       => array(2,2),      // will translate to e.id IN (2,3),
    'f.name($LIKE)' => 'Huckleberry Finn', // works with joins, will translate to f.name LIKE '%Huckleberry Finn%'
);

$bookCharacters = $em->...->findBy($whereOptions, array('id' => 'DESC', 15, 5));

```

Other advanced less common usages are possible (these are the full available options)

```php
$where = array(
    'id'              => 2,
    'name($LIKE)'     => 'Tom Sawyer',
    'name($NOT LIKE)' => 'Tom Sawyer',
    'id($IN)'         => array(2,2),
    'f.name($LIKE)'   => 'Huckleberry Finn',
    'age($IS)'        => null,
    'age($IS NOT)'    => null,
    'date($BETWEEN)'  => array('2016-01-01', '2016-02-25'), // including both start and end dates
    '($OR)' => array(
        //'name($LLIKE)' => 'Tom', // LIKE '%Tom', // left like
        //'name($RLIKE)' => 'Sawyer', // LIKE 'Sawyer%', // right like
        'name($IS)'   => null,
        'age($<)' => 4,
    ),
    'age($>=)' => 4,
    'age($<=)' => 10,
    'age($>)'  => 3,
    'age($<)'  => 11,
);
```

## Javascript http query input component

Available in the next release.
