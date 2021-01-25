<?php


namespace Tests\AppBundle\Service;


use AppBundle\Entity\Dinosaur;
use AppBundle\Entity\Enclosure;
use AppBundle\Entity\Security;
use AppBundle\Factory\DinosaurFactory;
use AppBundle\Service\EnclosureBuilderService;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class EnclosureBuilderServiceIntegrationTest extends KernelTestCase
{
    protected function setUp(): void
    {
        self::bootKernel();

        $this->truncateEntities();

        /*$this->truncateEntities([
            Enclosure::class,
            Security::class,
            Dinosaur::class
        ]);*/
    }


    protected function tearDown(): void
    {
    }

    /**
     * @return EntityManager
     */
    public function getEntityManager(): EntityManager
    {
        return self::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();
    }

    public function testItBuildsEnclosureWithDefaultSpecification()
    {
        /** @var EnclosureBuilderService $enclosureBuilderService */
        /*$enclosureBuilderService = self::$kernel->getContainer()
            ->get('test.'.EnclosureBuilderService::class);*/

        $dinosaurFactory = $this->createMock(DinosaurFactory::class);
        $dinosaurFactory->expects($this->any())
            ->method('growFromSpecification')
            ->willReturnCallback(function ($spec) {
                return new Dinosaur();
            })
        ;

        $enclosureBuilderService = new EnclosureBuilderService(
            $this->getEntityManager(),
            $dinosaurFactory
        );

        $enclosureBuilderService->buildEnclosure();

        $em = $this->getEntityManager();

        $count = (int) $em->getRepository(Security::class)
            ->createQueryBuilder('s')
            ->select('COUNT(s.id)')
            ->getQuery()
            ->getSingleScalarResult();

        $this->assertSame(1, $count, 'Amount of security system is not the same');

        $count = (int) $em->getRepository(Dinosaur::class)
            ->createQueryBuilder('d')
            ->select('COUNT(d.id)')
            ->getQuery()
            ->getSingleScalarResult();

        $this->assertSame(3, $count, 'Amount of dinosaurs system is not the same');
    }

    private function truncateEntities(/*array $entities*/)
    {
        /** Purge need doctrine/data-fixtures to be install */
        $purger = new ORMPurger($this->getEntityManager());
        $purger->purge();

        /*$connection = $this->getEntityManager()->getConnection();
        $databasePlatform = $connection->getDatabasePlatform();

        if ($databasePlatform->supportsForeignKeyConstraints()) {
            $connection->query('SET FOREIGN_KEY_CHECKS=0');
        }

        foreach ($entities as $entity) {
            $query = $databasePlatform->getTruncateTableSQL(
                $this->getEntityManager()->getClassMetadata($entity)->getTableName()
            );

            $connection->executeUpdate($query);
        }

        if ($databasePlatform->supportsForeignKeyConstraints()) {
            $connection->query('SET FOREIGN_KEY_CHECKS=1');
        }*/
    }
}
