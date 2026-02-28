# Coding Standards & Best Practices (Laravel 12)

This document outlines the coding standards and best practices for our Laravel 12 application. Our goal is to maintain a codebase that is **readable**, **maintainable**, **non-repetitive (DRY)**, and **modern**.

## 1. General Principles

### 1.1. DRY (Don't Repeat Yourself)
-   **Avoid duplicating logic.** If you find yourself copying and pasting code, extract it into a Service, Trait, or Helper.
-   Use **Eloquent Scopes** for reusable query logic.
-   Use **Form Requests** for validation logic instead of validating directly in controllers.

### 1.2. Readability & Clean Code
-   **Descriptive Naming**: Use meaningful names for variables, methods, and classes.
    -   *Bad*: `$d`, `$u`, `calc()`
    -   *Good*: `$date`, `$user`, `calculateTotalRevenue()`
-   **Short Methods**: Keep methods focused on a single task. Ideally, a method should fit on one screen.
-   **Comments**: Write self-documenting code. Use comments only to explain "why", not "what".

### 1.3. Modern PHP & Laravel
-   Target **PHP 8.4+** features (e.g., Constructor Property Promotion, Readonly Classes, Enums).
-   **Type Hinting**: Always use return types and parameter types.

## 2. Laravel 12 Specifics

### 2.1. Controllers
-   Keep controllers skinny. Move business logic to **Service Classes** or **Action Classes**.
-   Use **Resource Controllers** where applicable.
-   **Dependency Injection**: Inject dependencies via the constructor or method signature.

```php
// Good
public function store(StoreUserRequest $request, CreateUserService $service): RedirectResponse
{
    $service->execute($request->validated());
    return to_route('users.index');
}
```

### 2.2. Models & Eloquent
-   **Mass Assignment**: Use `$fillable` carefully.
-   **Relationships**: Define relationships explicitly.
-   **Scopes**: Use local scopes for complex queries.

```php
// Good
public function scopeActive(Builder $query): void
{
    $query->where('status', 'active');
}
```

### 2.3. Routing
-   Name your routes.
-   Group routes by controller or functionality.
-   Use route model binding.

```php
// Good
Route::get('/users/{user}', [UserController::class, 'show'])->name('users.show');
```

## 3. Formatting
-   Follow **PSR-12** / **PER Coding Style**.
-   Use 4 spaces for indentation.
-   Ensure files end with a single newline.
-   Remove unused imports.

## 4. Anti-Patterns to Avoid
-   ❌ **God Objects**: Classes that do too much.
-   ❌ **Magic Numbers**: Use constants or Enums instead.
-   ❌ **Logic in Views**: Blade files should only contain presentation logic.
-   ❌ **Old Array Syntax**: Use `[]` instead of `array()`.
