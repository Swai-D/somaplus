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

        if (! is_string($route) || ! Route::has($route)) {
            return null;
        }

        return [
            'label' => $label,
            'icon' => $icon,
            'url' => route($route),
            'active' => $request->routeIs($route, $route.'.*'),
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
