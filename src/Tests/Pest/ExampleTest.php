<?php

namespace AcMarche\Edr\Tests\Pest;

class ExampleTest
{
    public function do()
    {
        test('example', static function () {
            expect(true)->toBeTrue();
        });

        it('as a homepage')
            ->get('/')
            ->assertSee('Bienvenue');

        it('asserts true is true', function () {
            $this->assertTrue(true);
            expect(true)->toBeTrue();
        });
    }
}
