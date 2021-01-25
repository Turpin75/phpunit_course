<?php


namespace Tests\AppBundle\Factory;


use AppBundle\Entity\Dinosaur;
use AppBundle\Service\DinosaurLengthDeterminator;
use PHPUnit\Framework\TestCase;
use AppBundle\Factory\DinosaurFactory;

class DinosaurFactoryTest extends TestCase
{
    /**
     * @var DinosaurFactory
     */
    private $factory;

    /**
     * @var DinosaurLengthDeterminator|\PHPUnit\Framework\MockObject\MockObject
     */
    private $lengthDeterminator;

    public function setUp(): void
    {
        $this->lengthDeterminator = $this->createMock(DinosaurLengthDeterminator::class);
        $this->factory = new DinosaurFactory($this->lengthDeterminator);
    }

    public function testItGrowsAVelociraptor()
    {
        $dinosaur = $this->factory->growVelociraptor(5);

        $this->assertInstanceOf(Dinosaur::class, $dinosaur);

        $this->assertIsString($dinosaur->getGenus());
        $this->assertSame('Velociraptor', $dinosaur->getGenus());
        $this->assertSame(5, $dinosaur->getLength());
    }
    
    public function testItGrowsATriceraptors()
    {
        $this->markTestIncomplete('Waiting for confirmation from Genlab');
    }

    public function testItGrowsABabyVelociraptor()
    {
        if (!class_exists('Nanny')) {
            $this->markTestSkipped('There is nobody to watch the baby');
        }

        $dinosaur = $this->factory->growVelociraptor(1);
        $this->assertSame(1, $dinosaur->getLength());
    }

    /**
     * @dataProvider getSpecificationsTests
     *
     * @param string $spec
     * @param bool $expectedIsLarge
     * @param bool $expectedIsCarnivorous
     */
    public function testItGrowsADinosaurFromSpecification(string $spec, bool $expectedIsCarnivorous)
    {
        $this->lengthDeterminator->expects($this->once())
            ->method('getLengthFromSpecification')
            ->with($spec)
            ->willReturn(20);

        $dinosaur = $this->factory->growFromSpecification($spec);

        $this->assertSame($expectedIsCarnivorous, $dinosaur->isCarnivorous(), 'Diets do not match');
        $this->assertSame(20, $dinosaur->getLength());
    }

  /*  public function testItGrowsAHugeDinosaur(string $specification)
    {
        $dinosaur = $this->factory->growFromSpecification($specification);

        $this->assertGreaterThanOrEqual(Dinosaur::HUGE, $dinosaur->getLength());
    }*/

    public function getSpecificationsTests()
    {
        return [
            // Specification, isCarnivorous
            ['large carnivorous dinosaur', true],
            ['give me all the cookies!!!', false],
            ['large herbivore', false]
        ];
    }
}
