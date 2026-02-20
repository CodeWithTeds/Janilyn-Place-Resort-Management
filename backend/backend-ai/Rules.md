# Project Rules & Architectural Guidelines

This document serves as the single source of truth for all architectural and coding standards in this project. All AI agents (Backend, Planner, Reviewer) must strictly adhere to these rules.

## 1. Architectural Pattern: MVC + Service + Repository
The project follows a strict **Model-View-Controller (MVC)** architecture enhanced with **Service** and **Repository** layers.

### Layers Responsibility
1.  **Routes**: Define endpoints and map them to Controller methods.
2.  **Form Requests**: Handle **ALL** input validation.
    -   *Rule*: NEVER perform validation inside a Controller.
3.  **Controllers**: "Thin" entry points.
    -   *Responsibility*: Receive request -> Delegate to Service/Repository -> Return Response.
    -   *Rule*: NO business logic allowed in Controllers.
4.  **Services**: The domain/business logic layer.
    -   *Responsibility*: Handle complex business rules, calculations, external API calls, and transaction management.
    -   *Condition*: If a task involves business logic, a Service MUST be used.
5.  **Repositories**: The data access layer.
    -   *Responsibility*: Handle all database interactions (Eloquent queries).
    -   *Rule*: Controllers and Services should not access Models directly for complex queries; they must use Repositories.
6.  **Models**: Represent database tables and relationships.

## 2. Decision Logic (Flow)
When implementing a feature, determine the flow based on complexity:

-   **Scenario A: Simple CRUD (No Business Logic)**
    -   *Flow*: Controller → Form Request (Validation) → Repository → Response.
-   **Scenario B: Complex Logic (Business Rules Involved)**
    -   *Flow*: Controller → Form Request (Validation) → Service → Repository → Response.

## 3. Coding Standards & Best Practices

### A. Reusability (Traits)
-   *Rule*: If logic is repeated in more than one place (e.g., file uploading, slug generation), extract it into a **Trait**.

### B. Type Safety (Enums)
-   *Rule*: Use **PHP Enums** for any field that has a fixed set of values (e.g., `status`, `role`, `type`, `category`).
-   *Prohibition*: Avoid "magic strings" in code.

### C. Naming Conventions
-   **Controllers**: `SingularNameController` (e.g., `UserController`)
-   **Services**: `SingularNameService` (e.g., `UserService`)
-   **Repositories**: `SingularNameRepository` (e.g., `UserRepository`)
-   **Requests**: `ActionNameRequest` (e.g., `StoreUserRequest`)
