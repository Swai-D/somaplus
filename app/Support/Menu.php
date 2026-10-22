<?php

namespace App\Support;

use App\Models\User;
use App\Tenancy\TenantContext;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/**
 * Turns the entries of config/menu.php into what the signed-in person may see.
 */
final class Menu
{
    /**
     * @param  array<int, array<string, mixed>>  $sections
     */
    public function __construct(private readonly array $sections) {}

    public static function fromConfig(): self
    {
        $sections = config('menu.sections', []);

        return new self(is_array($sections) ? array_values($sections) : []);
    }

    /**
     * @return list<array{label: string, items: list<array<string, mixed>>}>
     */
    public function build(?User $user, Request $request): array
    {
        $built = [];

        foreach ($this->sections as $section) {
            $items = [];

            foreach ($this->list($section['items'] ?? []) as $item) {
                $resolved = $this->resolve($item, $user, $request);

                if ($resolved !== null) {
                    $items[] = $resolved;
                }
            }

            if ($items !== []) {
                $built[] = ['label' => $this->text($section['label'] ?? ''), 'items' => $items];
            }
        }

        return $built;
    }

    /**
     * The "Add new" menu of the top bar: the pages a person reaches for most often. An
     * entry shows only when the route exists and the person holds the permission that
     * guards it, exactly like the entries of the sidebar.
     *
     * @return list<array{label: string, icon: string, url: string}>
     */
    public function quick(?User $user): array
    {
        $actions = [];

        foreach ($this->list(config('menu.quick', [])) as $item) {
            if (! $this->allowed($item, $user)) {
                continue;
            }

            $route = $item['route'] ?? null;

            if (! is_string($route) || ! Route::has($route)) {
                continue;
            }

            $actions[] = [
                'label' => $this->text($item['label'] ?? ''),
                'icon' => is_string($item['icon'] ?? null) ? $item['icon'] : '',
                'url' => route($route),
            ];
        }

        return $actions;
    }

    /**
     * The settings page of the school, for the person who may manage it. The first page of
     * the list that exists and that the person may open is used, so the link always leads
     * somewhere real.
     */
    public function settingsUrl(?User $user): ?string
    {
        $routes = config('menu.settings_routes', []);

        foreach (is_array($routes) ? $routes : [] as $route) {
            if (! is_string($route) || ! Route::has($route)) {
                continue;
            }

            if (! $this->allowed(['route' => $route, 'permission' => 'settings.manage'], $user)) {
                continue;
            }

            return route($route);
        }

        return null;
    }

    /**
     * @param  array<string, mixed>  $item
     * @return array<string, mixed>|null
     */
    private function resolve(array $item, ?User $user, Request $request): ?array
    {
        if (! $this->allowed($item, $user)) {
            return null;
        }

        $label = $this->text($item['label'] ?? '');
        $icon = is_string($item['icon'] ?? null) ? $item['icon'] : '';

        if (array_key_exists('children', $item)) {
            $children = [];

            foreach ($this->list($item['children']) as $child) {
                $resolved = $this->resolve($child, $user, $request);

                if ($resolved !== null) {
                    $children[] = $resolved;
                }
            }

            if ($children === []) {
                return null;
            }

            return [
                'label' => $label,
                'icon' => $icon,
                'url' => null,
                'active' => in_array(true, array_column($children, 'active'), true),
                'children' => $children,
            ];
        }

        $route = $item['route'] ?? null;
        $patterns = array_values(array_filter((array) ($item['active'] ?? []), 'is_string'));

        if (! is_string($route) || ! Route::has($route)) {
            return null;
        }

        return [
            'label' => $label,
            'icon' => $icon,
            'url' => route($route),
            'active' => $request->routeIs($route, $route.'.*', ...$patterns),
            'children' => [],
        ];
    }

    /**
     * @param  array<string, mixed>  $item
     */
    private function allowed(array $item, ?User $user): bool
    {
        $inSchool = app(TenantContext::class)->currentId() !== null;
        $scope = $item['scope'] ?? 'school';

        if ($scope !== 'both' && $scope !== ($inSchool ? 'school' : 'central')) {
            return false;
        }

        $permission = $item['permission'] ?? null;

        if (is_string($permission) && ($user === null || ! $user->can($permission))) {
            return false;
        }

        $feature = $item['feature'] ?? null;

        return ! is_string($feature) || Plans::current()->allows($feature);
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function list(mixed $value): array
    {
        return is_array($value) ? array_values(array_filter($value, 'is_array')) : [];
    }

    private function text(mixed $key): string
    {
        return is_string($key) && $key !== '' ? (string) __($key) : '';
    }
}
