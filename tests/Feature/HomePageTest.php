<?php

it('shows the Somaplus home page built on the template assets', function () {
    $this->get('/')
        ->assertOk()
        ->assertSee('Somaplus')
        ->assertSee('assets/css/somaplus.css');
});

it('shows the tagline in both languages', function (string $locale) {
    app()->setLocale($locale);

    $this->get('/')->assertOk()->assertSee(__('app.tagline'));
})->with(['en', 'sw']);

it('keeps the template stylesheet in public/assets', function () {
    expect(file_exists(public_path('assets/css/somaplus.css')))->toBeTrue();
});
