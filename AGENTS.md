# Agent Guidelines for Dev Process Manager

This is a Laravel 12 application using Livewire 4, NativePHP for Desktop, Pest 4 for testing, and Tailwind CSS v4.

## Build, Lint, and Test Commands

### PHP Commands
```bash
# Run all tests
php artisan test

# Run a single test
php artisan test --filter=testName

# Run tests with compact output
php artisan test --compact

# Run tests with filter and compact output
php artisan test --compact --filter=testName

# Format PHP code (required before committing)
vendor/bin/pint --dirty --format=agent

# Check code style without fixing
composer run test:lint

# Run full test suite including linting
composer run test
```

### NPM Commands
```bash
# Build assets for production
npm run build

# Development server with hot reload
npm run dev
```

### NativePHP Commands
```bash
# Run the desktop application in development mode
composer run native:dev
```

## Code Style Guidelines

### PHP Standards
- **Preset**: Laravel Pint with `laravel` preset (configured in `pint.json`)
- **Formatting**: Always run `vendor/bin/pint --dirty --format=agent` before committing
- **Types**: Use explicit return type declarations and parameter type hints
- **Constructors**: Use PHP 8 constructor property promotion
- **Control Structures**: Always use curly braces, even for single-line bodies

### Imports
- Order imports: Laravel/Framework first, then third-party, then app-specific
- Use fully qualified class names in docblocks
- Group related imports together

Example:
```php
use App\Models\Command;
use App\Models\Project;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\On;
use Livewire\Component;
```

### Naming Conventions
- **Classes**: PascalCase (e.g., `Dashboard`, `ProcessRunner`)
- **Methods**: camelCase (e.g., `selectCommand`, `openProjectModal`)
- **Variables**: camelCase (e.g., `$selectedCommandId`, `$projectName`)
- **Database Columns**: snake_case
- **Boolean Variables**: Use prefixes like `is`, `has`, `show` (e.g., `$showProjectModal`)

### Livewire Components
- Use single-file components (SFC) with `new class extends Component` pattern
- Public properties for state management
- Use `#[On]` attribute for event listeners
- Dispatch events with `$this->dispatch('event-name', params)`
- Return type hint `View` on render method

### Database & Models
- Use Eloquent relationships with return type hints
- Use `$fillable` arrays on models
- Create factories for all models
- Prefer `Model::query()` over `DB::` facade
- Use eager loading to prevent N+1 queries

### Blade Templates
- Use Tailwind CSS v4 classes
- Use `@if`, `@foreach` with proper spacing
- Component keys: `wire:key="unique-id"`
- Use `wire:click`, `wire:model` for Livewire interactions

### Error Handling
- Validate in component methods using `$this->validate()`
- Reset error bags with `$this->resetErrorBag()`
- Use early returns for guard clauses

## Testing Guidelines

### Pest PHP
- Use Pest 4 syntax with closures
- Extend `Tests\TestCase::class` in `Pest.php`
- Use `RefreshDatabase` trait for feature tests
- Use factories for model creation
- Test naming: descriptive with backticks (e.g., `` test('returns successful response') ``)

Example:
```php
 test('returns successful response', function () {
     $response = $this->get('/');
     $response->assertOk();
 });
```

### Test Organization
- **Feature tests**: Test HTTP endpoints and Livewire components
- **Unit tests**: Test individual classes and methods
- Most tests should be feature tests

## Skills Activation

This project has domain-specific skills. Activate them when working in these areas:

- **livewire-development**: Creating/updating Livewire components, wire: directives, reactivity
- **pest-testing**: Writing tests, assertions, debugging test failures
- **tailwindcss-development**: Styling components, responsive design, dark mode

## Key Technologies & Versions

- PHP 8.3.30
- Laravel Framework v12
- Livewire v4
- NativePHP Desktop v2
- Pest v4
- Tailwind CSS v4
- SQLite (default database)

## Important Notes

- Always check existing code conventions before adding new code
- Use `php artisan make:` commands to generate files
- Do not create new base folders without approval
- Use environment variables only in config files (never use `env()` outside config)
- For URLs, prefer named routes and `route()` helper
- If Vite manifest error occurs, run `npm run build`
