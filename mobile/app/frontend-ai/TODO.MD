# TODO: Authentication System – Frontend Plan

This plan follows strict architectural guidelines defined in `frontend-ai/Rules.md` and `frontend-ai/agents/PROJECT_PLANNER.md`.

## Goal
Implement a modern follow my color palette, responsive, and secure authentication flow in the mobile app using React Native, Expo, and Laravel Sanctum.

## 1. Setup & Dependencies
- [ ] Install necessary libraries
    - `npx expo install expo-secure-store axios`
- [ ] Configure Axios instance with base URL and interceptors (for Authorization header)

## 2. Type Definitions (TypeScript)
- [ ] Create `types/auth.ts`
    - `User` interface
    - `LoginPayload` interface
    - `RegisterPayload` interface
    - `AuthResponse` interface

## 3. Service Layer (API)
- [ ] Create `services/api.ts` (Axios instance setup)
- [ ] Create `services/auth.service.ts`
    - `login(credentials)`
    - `register(data)`
    - `logout()`
    - `user()`

## 4. State Management (Context + Hooks)
- [ ] Create `context/AuthContext.tsx`
    - Manage `user` state and `isLoading`
    - Implement `signIn`, `signUp`, `signOut` methods
    - Persist token using `expo-secure-store`
- [ ] Create `hooks/useAuth.ts` for easy access to context

## 5. UI Components (Reusable)
- [ ] Create `components/ui/Button.tsx` (if not exists)
- [ ] Create `components/ui/Input.tsx` (if not exists)

## 6. Screens (Views)
- [ ] Create `app/(auth)/login.tsx`
- [ ] Create `app/(auth)/register.tsx`
- [ ] Create `app/(auth)/forgot-password.tsx`
- [ ] Create `app/(auth)/_layout.tsx` (Stack navigator for auth screens)

## 7. Navigation & Protection
- [ ] Update root `app/_layout.tsx` to handle authentication state
    - Redirect to `(auth)/login` if not authenticated
    - Redirect to `(tabs)` if authenticated
