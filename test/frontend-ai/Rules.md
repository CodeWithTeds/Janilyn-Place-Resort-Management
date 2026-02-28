# Frontend Rules & Architectural Guidelines (React Native)

This document serves as the single source of truth for all architectural and coding standards in the React Native project.

## 1. Architectural Pattern: Modular + Hooks + Services

The project follows a **Component-Based** architecture enhanced with **Custom Hooks** for logic and **Services** for API interaction.

### Layers Responsibility
1.  **Screens (`app/`)**: The View layer.
    -   *Responsibility*: Display UI, handle user interactions, and call Hooks.
    -   *Rule*: Keep screens clean. Logic should be delegated to Hooks.
2.  **Components (`components/`)**: Reusable UI blocks.
    -   *Rule*: Components should be pure and presentational whenever possible.
3.  **Hooks (`hooks/`)**: The Logic layer.
    -   *Responsibility*: Handle state management, side effects, and business logic.
    -   *Rule*: If a logic is complex or reusable, extract it into a custom hook.
4.  **Services (`services/`)**: The API/Data layer.
    -   *Responsibility*: Handle all network requests (Fetch/Axios).
    -   *Rule*: Components/Hooks should NEVER call `fetch` directly; they must use a Service.
5.  **Context (`context/`)**: Global State.
    -   *Responsibility*: Share state across the app (e.g., Auth, Theme).
6.  **Types (`types/`)**: TypeScript definitions.
    -   *Rule*: No `any`. Define interfaces for all data structures.

## 2. Decision Logic (Flow)

-   **Scenario A: UI-Only Interaction**
    -   *Flow*: Screen → Component → State (local).
-   **Scenario B: Data Fetching**
    -   *Flow*: Screen → Custom Hook → Service → API.
-   **Scenario C: Global State Change**
    -   *Flow*: Screen → Context Provider → State Update.

## 3. Coding Standards & Best Practices

### A. Styling
-   *Rule*: Use `StyleSheet.create` for styles. Avoid inline styles for performance.
-   *Theme*: Use `constants/theme.ts` (or similar) for colors and spacing consistency.

### B. Performance
-   *Rule*: Use `React.memo`, `useMemo`, and `useCallback` to prevent unnecessary re-renders.
-   *Rule*: Optimize images using `expo-image`.

### C. Navigation
-   *Rule*: Use **Expo Router** (file-based routing).
-   *Structure*: Group related screens in folders (e.g., `(tabs)`, `(auth)`).

### D. TypeScript
-   *Rule*: Strict typing. Define return types for functions and props interfaces for components.
-   *Naming*: `InterfaceName` (PascalCase), `type` (PascalCase).

### E. File Naming
-   **Components/Hooks**: `kebab-case.tsx` (e.g., `user-profile.tsx`, `use-auth.ts`).
-   **Directories**: `kebab-case` (e.g., `user-details`).
