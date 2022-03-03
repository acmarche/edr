<?php

namespace AcMarche\Edr\Tests\Pest;

Class ExampleTest {
function do() {
    test('example', function () {
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
