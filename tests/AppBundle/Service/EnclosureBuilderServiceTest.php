<?php


namespace Tests\AppBundle\Service;


use AppBundle\Entity\Dinosaur;
use AppBundle\Entity\Enclosure;
use AppBundle\Factory\DinosaurFactory;
use AppBundle\Service\EnclosureBuilderService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class EnclosureBuilderServiceTest extends TestCase
{
    public function testItBuildsAndPersistsEnclosure()
    {
        $em = $this->createMock(EntityManagerInterface::class);

        $em->expects($this->once())
            ->method('persist')
            ->with($this->isInstanceOf(Enclosure::class))
        ;

        $em->expects($this->atLeastOnce())
            ->method('flush')
        ;

        $dinosaurFactory = $this->createMock(DinosaurFactory::class);

        $dinosaurFactory->expects($this->exactly(2))
            ->method('growFromSpecification')
            ->with($this->isType('string'))
            ->willReturn(new Dinosaur())
        ;

        $builder = new EnclosureBuilderService($em, $dinosaurFactory);
        $enclosure = $builder->buildEnclosure(1, 2);

        $this->assertCount(1, $enclosure->getSecurities());
        $this->assertCount(2, $enclosure->getDinosaurs());
    }
}
