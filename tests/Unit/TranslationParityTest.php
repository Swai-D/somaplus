<?php

/**
 * Flatten a translation array into dotted keys.
 *
 * @param  array<string, mixed>  $messages
 * @return array<int, string>
 */
function translationKeys(array $messages, string $prefix = ''): array
{
    $keys = [];

    foreach ($messages as $key => $value) {
        $path = $prefix.$key;

        if (is_array($value)) {
            $keys = array_merge($keys, translationKeys($value, $path.'.'));
        } else {
            $keys[] = $path;
        }
    }

    sort($keys);

    return $keys;
}

function translationFiles(string $locale): array
{
    $files = array_map('basename', glob(dirname(__DIR__, 2).'/lang/'.$locale.'/*.php') ?: []);
    sort($files);

    return $files;
}

it('has the same translation files in English and Swahili', function () {
    expect(translationFiles('sw'))->toBe(translationFiles('en'));
});

it('has the same keys in every English and Swahili file', function (string $file) {
    $root = dirname(__DIR__, 2).'/lang';

    $english = translationKeys(require $root.'/en/'.$file);
    $swahili = translationKeys(require $root.'/sw/'.$file);

    expect($swahili)->toBe($english);
})->with(fn () => translationFiles('en'));
