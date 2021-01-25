<?php


namespace Tests\AppBundle\Entity;

use AppBundle\Entity\Dinosaur;
use AppBundle\Exception\DinosaurAreRunningRampantException;
use AppBundle\Exception\NotABuffetException;
use http\Encoding\Stream\Enbrotli;
use PHPUnit\Framework\TestCase;
use AppBundle\Entity\Enclosure;

class EnclosureTest extends TestCase
{
    public function testItHasNoDinosaursByDEfault()
    {
        $enclosure = new Enclosure();

        $this->assertEmpty($enclosure->getDinosaurs());
    }

    public function testItAddsDinosaurs()
    {
        $enclosure = new Enclosure(true);

        $enclosure->addDinosaur(new Dinosaur());
        $enclosure->addDinosaur(new Dinosaur());

        $this->assertCount(2, $enclosure->getDinosaurs());
    }

    public function testItDoesNotAllowCarnivorousToMixWithHerbivores()
    {
        $enclosure = new Enclosure(true);
        $enclosure->addDinosaur(new Dinosaur());

        $this->expectException(NotABuffetException::class);

        $enclosure->addDinosaur(new Dinosaur('Velociraptor', true));
    }

    public function testItDoesNotAllowToAddNonCarnivorousDinosaursToCarnivorousEnclosure()
    {
        $enclosure = new Enclosure(true);
        $enclosure->addDinosaur(new Dinosaur('Velociraptor', true));

        $this->expectException(NotABuffetException::class);

        $enclosure->addDinosaur(new Dinosaur());
    }

    public function testItDoesNotAllowToAddDinosaursToUnsecureEnclosures()
    {
        $enclosure = new Enclosure();

        $this->expectException(DinosaurAreRunningRampantException::class);
        $this->expectExceptionMessage('Are you crazy?');

        $enclosure->addDinosaur(new Dinosaur());
    }
}
