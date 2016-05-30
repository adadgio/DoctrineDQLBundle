DoctrineDQL Bundle
====

A set of simple helper to help reduce the size of your Symfony repositories.

## Examples

### Where conditions

```
// assuming a Symfony repository
class MyRepository extends EntityRepository
{
    public function findBy(array $where = array(), array $orderBy = array(), $limit = null, $offset)
    {
        $builder = $this->createQueryBuilder('e');

        $builder->leftJoin('e.friends', 'f')

        $builder = $builder::Where($where);

        $builder->setMaxResults(Limit::enforce($limit)); // will never be more than 1000
        // $builder->setMaxResults(Limit::enforce($limit, Limit::NO_LIMIT)); // will eventualy be more than 1000, depends on input
        $builder->setFirstResult(Offset::offset($limit)); // doesn't do a lot, just for style and integer conversion

        return $builder->getQuery()->getResult(); // standard symfony common saying
    }
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

$characters = $em->...->findBy($whereOptions, array('id' => 'DESC', 15, 5));

```
