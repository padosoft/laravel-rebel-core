<?php

declare(strict_types=1);

use Padosoft\Rebel\Core\Identifiers\GenericIdentifier;

it('normalizes and masks a generic identifier', function (): void {
    $id = GenericIdentifier::from('  Mario_Rossi ');

    expect($id->normalized())->toBe('mario_rossi')
        ->and($id->masked())->toBe('m***')
        ->and($id->type())->toBe('generic');
});

it('rejects an empty identifier', function (): void {
    GenericIdentifier::from('   ');
})->throws(InvalidArgumentException::class);
