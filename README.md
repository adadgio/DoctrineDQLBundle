DoctrineDQL Bundle
====

A set of simple helper to help reduce the size of your Symfony repositories.

## Install

Install with composer.

`composer require adadgio/doctrine-dql-bundle`

Make the following change to your `AppKernel.php` file to the registered bundles array.

```
new Adadgio\DoctrineDQLBundle\AdadgioDoctrineDQLBundle(),
```

## Examples & usage

```
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

    // killer feature. No options resolver or if...then(s) !
    $builder = Where::andWhere($where);

    // order by statements
    $builder = OrderBy::orderBy($where);

    $builder->setMaxResults(Limit::enforce($limit)); // will never be more than 1000
    // $builder->setMaxResults(Limit::enforce($limit, Limit::NO_LIMIT)); // will eventually be more than 1000, depends on input
    $builder->setFirstResult(Offset::offset($limit)); // doesn't do a lot, just for style and integer conversion

    $collection = $builder->getQuery()->getResult(); // standard symfony common saying

    // now lets say i want the collection indexed by id (for any other further usage)
    $collection = IndexedCollection::indexBy($collection, '[id]'); // nb: would also work with pure arrays

    // if i also want a list of ids (its a free country no?)
    // $ids = ColumnAccessor::getColumnValues($collection, '[id]');

    return $collection;
}
```

... and the corresponding input

```
$where = array(
    'id'            => 2,               // will translate to e.id = 2
    'name($LIKE)'   => 'Tom Sawyer',    // will translate to e.name LIKE '%Tom Sawyer%'
    'id($IN)'       => array(2,2),      // will translate to e.id IN (2,3),
    'f.name($LIKE)' => 'Huckleberry Finn', // works with joins, will translate to f.name LIKE '%Huckleberry Finn%'
);

$bookCharacters = $em->...->findBy($whereOptions, array('id' => 'DESC', 15, 5));

```
