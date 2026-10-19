<?php

use App\Models\Tenant;
use App\Models\User;
use App\Support\Help\HelpCenter;
use App\Tenancy\TenantContext;

beforeEach(function () {
    useTestHosts();
    HelpCenter::flush();

    $this->school = Tenant::factory()->create(['slug' => 'acme']);
    $teacher = makeSchoolUser($this->school, [], 'teacher');
    $this->teacher = TenantContext::runAsSystem(fn () => User::findOrFail($teacher->id));
});

/** @return list<string> category/slug of every file in a language */
function helpFiles(string $locale): array
{
    $root = resource_path("docs/{$locale}");
    $found = [];

    foreach (glob("{$root}/*/*.md") ?: [] as $file) {
        $found[] = basename(dirname($file)).'/'.basename($file, '.md');
    }
    sort($found);

    return $found;
}

it('has the same articles in English and Swahili', function () {
    expect(helpFiles('en'))->not->toBeEmpty()
        ->and(helpFiles('sw'))->toBe(helpFiles('en'));
});

it('puts every article in a configured category and gives each category a title in both languages', function (string $locale) {
    foreach (helpFiles($locale) as $path) {
        expect(array_keys(config('help.categories')))->toContain(explode('/', $path)[0]);
    }

    foreach (array_keys(config('help.categories')) as $category) {
        expect(trans("help.categories.{$category}.title", [], $locale))->not->toBe("help.categories.{$category}.title")
            ->and(trans("help.categories.{$category}.description", [], $locale))->not->toBe("help.categories.{$category}.description");
    }
})->with(['en', 'sw']);

it('gives every article valid front matter and links that resolve', function (string $locale) {
    app()->setLocale($locale);
    $help = HelpCenter::forCurrentLocale();

    foreach (helpFiles($locale) as $path) {
        [$category, $slug] = explode('/', $path);
        $article = $help->find($category, $slug);

        expect($article)->not->toBeNull("{$path} has no title")
            ->and($article->summary)->not->toBe('', "{$path} has no summary")
            ->and($article->keywords)->not->toBeEmpty("{$path} has no keywords")
            ->and($article->body)->not->toBe('');

        preg_match_all('/\(help:([a-z0-9-]+)\/([a-z0-9-]+)\)/', $article->body, $links, PREG_SET_ORDER);
        foreach ($links as $link) {
            expect($help->find($link[1], $link[2]))->not->toBeNull("{$path} links to missing {$link[1]}/{$link[2]}");
        }
    }
})->with(['en', 'sw']);

it('lists the categories and articles for a signed-in person', function () {
    $response = $this->actingAs($this->teacher)->get('http://acme.somaplus.test/help')->assertOk();

    foreach (array_keys(config('help.categories')) as $category) {
        $response->assertSee(__("help.categories.{$category}.title"));
    }

    foreach (HelpCenter::forCurrentLocale()->all() as $article) {
        $response->assertSee($article->title);
    }
});

it('shows an article with its table of contents and neighbours', function () {
    $this->actingAs($this->teacher)
        ->get('http://acme.somaplus.test/help/academics/classes-and-streams')
        ->assertOk()
        ->assertSee('Classes and streams')
        ->assertSee('id="add-a-class"', false)
        ->assertSee(route('help.show', ['academics', 'academic-years-and-terms'], false), false)
        ->assertSee(route('help.show', ['academics', 'subjects'], false), false);
});

it('turns help links into real links', function () {
    $this->actingAs($this->teacher)
        ->get('http://acme.somaplus.test/help/getting-started/first-steps')
        ->assertOk()
        ->assertSee(route('help.show', ['account', 'forgot-password'], false), false)
        ->assertDontSee('help:account', false);
});

it('shows an article in Swahili when that language is chosen', function () {
    $this->actingAs($this->teacher)
        ->withSession(['locale' => 'sw'])
        ->get('http://acme.somaplus.test/help/plans/subscription-states')
        ->assertOk()
        ->assertSee('Usajili na mpango')
        ->assertDontSee('Subscription and plan');
});

it('finds articles by search in the current language', function () {
    $this->actingAs($this->teacher)
        ->get('http://acme.somaplus.test/help?q=stream')
        ->assertOk()
        ->assertSee('Classes and streams');

    $this->actingAs($this->teacher)
        ->withSession(['locale' => 'sw'])
        ->get('http://acme.somaplus.test/help?q=mkondo')
        ->assertOk()
        ->assertSee('Madarasa na mikondo');
});

it('says so when nothing matches and asks for at least two letters', function () {
    $this->actingAs($this->teacher)->get('http://acme.somaplus.test/help?q=zzzzqq')->assertOk()->assertSee('No guide found');
    $this->actingAs($this->teacher)->get('http://acme.somaplus.test/help?q=a')->assertOk()->assertSee('at least two letters');
});

it('shows the contact card', function () {
    $this->actingAs($this->teacher)
        ->get('http://acme.somaplus.test/help')
        ->assertSee('hello@odessalab.tech');
});

it('answers 404 for an article that does not exist', function () {
    $this->actingAs($this->teacher)->get('http://acme.somaplus.test/help/academics/nothing-here')->assertNotFound();
    $this->actingAs($this->teacher)->get('http://acme.somaplus.test/help/nothing/subjects')->assertNotFound();
});

it('does not read other files through the article name', function () {
    $this->actingAs($this->teacher)->get('http://acme.somaplus.test/help/academics/..%2Fsubjects')->assertNotFound();
    $this->actingAs($this->teacher)->get('http://acme.somaplus.test/help/%2E%2E/en')->assertNotFound();
});

it('sends a guest to sign in', function () {
    $this->get('http://acme.somaplus.test/help')->assertRedirect('http://acme.somaplus.test/login');
});

it('is also available on the central address to a platform administrator', function () {
    $this->actingAs(User::factory()->superAdmin()->create())
        ->get('http://somaplus.test/help')
        ->assertOk();
});

it('shows Help in the sidebar', function () {
    $this->actingAs($this->teacher)
        ->get('http://acme.somaplus.test/')
        ->assertSee('/help"', false);
});
