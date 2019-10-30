<?php
namespace App\Tests\Helper;

// here you can define custom actions
// all public methods declared in helper class will be available in $I

use Codeception\Exception\ModuleException;
use Codeception\Module\Symfony;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Symfony\Component\DependencyInjection\Container;

class Unit extends \Codeception\Module
{
    public function getUtility($classname)
    {
        $u = new $classname;
        /** @var Symfony $symfonyModule */
        $symfonyModule = $this->getModule('Symfony');
        $doctrineModule = $this->getModule('Doctrine2');
        $u->symfony = $symfonyModule;
        $u->doctrine = $doctrineModule;

        return $u;
    }

    /**
     * @param string $entityName
     * @param array $updateFields
     * @param int|array $id
     * @throws ModuleException
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws \ReflectionException
     */
    public function updateEntity(string $entityName, array $updateFields, $id)
    {
        /** @var EntityManager $em */
        $em = $this->getModule('Doctrine2')->em;
        if (is_array($id)) {
            $entity = $em->getRepository($entityName)->findOneBy($id);
        } else {
            $entity = $em->getRepository($entityName)->findOneBy(['id' => $id]);
        }
        $reflectionClass = new \ReflectionClass($entityName);

        foreach ($updateFields as $name => $value) {
            try {
                $property = $reflectionClass->getProperty($name);
                $property->setAccessible(true);
                $property->setValue($entity, $value);
            } catch (\ReflectionException $e) {
                $method = 'set' . ucfirst($name);
                $entity->$method($value);
            }
        }

        $em->persist($entity);
        $em->flush();
    }

    /**
     * @param string $alias
     * @param stdClass $service
     * @throws ModuleException
     * @throws \ReflectionException
     */
    public function haveService(string $alias, $service)
    {
        $symfonyModule = $this->getModule('Symfony');
        /** @var Container $container */
        $container = $symfonyModule->_getContainer();

        $r = new ReflectionObject($container);
        $p = $r->getProperty('services');
        $p->setAccessible(true);

        $services = $p->getValue($container);

        $services[$alias] = $service;

        $p->setValue($container, $services);
    }

    /**
     * @param string $table
     * @param array $criteria
     * @throws ModuleException
     */
    public function removeFromDatabase(string $table, array $criteria)
    {
        $sql = 'DELETE FROM ' . $table;
        $params = [];
        foreach ($criteria as $k => $v) {
            $params[] = $k . " = ? ";
        }
        if ($params) {
            $sql .= ' WHERE ' . implode(' AND ', $params);
        }
        $this->getModule('Db')->_getDriver()->executeQuery($sql, array_values($criteria));

    }
}
