import React, { createContext, useContext, useEffect, useState } from 'react';
import { storage } from '../utils/storage';
import { User, LoginPayload, RegisterPayload } from '../types/auth';
import { authService } from '../services/auth.service';
import { router } from 'expo-router';

interface AuthContextType {
    user: User | null;
    isLoading: boolean;
    signIn: (payload: LoginPayload) => Promise<void>;
    signUp: (payload: RegisterPayload) => Promise<void>;
    signOut: () => Promise<void>;
}

const AuthContext = createContext<AuthContextType | undefined>(undefined);

export function AuthProvider({ children }: { children: React.ReactNode }) {
    const [user, setUser] = useState<User | null>(null);
    const [isLoading, setIsLoading] = useState(true);

    useEffect(() => {
        checkAuth();
    }, []);

    const checkAuth = async () => {
        try {
            const token = await storage.getItem('auth_token');
            if (token) {
                const userData = await authService.getUser();
                setUser(userData);
            }
        } catch (error) {
            console.log('Auth check failed:', error);
            await storage.removeItem('auth_token');
        } finally {
            setIsLoading(false);
        }
    };

    const signIn = async (payload: LoginPayload) => {
        try {
            const response = await authService.login(payload);
            if (response.data?.token && response.data?.user) {
                await storage.setItem('auth_token', response.data.token);
                setUser(response.data.user);
                router.replace('/(tabs)' as any);
            }
        } catch (error) {
            throw error;
        }
    };

    const signUp = async (payload: RegisterPayload) => {
        try {
            const response = await authService.register(payload);
            if (response.data?.token && response.data?.user) {
                await storage.setItem('auth_token', response.data.token);
                setUser(response.data.user);
                router.replace('/(tabs)' as any);
            }
        } catch (error) {
            throw error;
        }
    };

    const signOut = async () => {
        try {
            await authService.logout();
        } catch (error) {
            console.log('Logout error:', error);
        } finally {
            await storage.removeItem('auth_token');
            setUser(null);
            router.replace('/(auth)/login' as any);
        }
    };

    return (
        <AuthContext.Provider value={{ user, isLoading, signIn, signUp, signOut }}>
            {children}
        </AuthContext.Provider>
    );
}

export function useAuth() {
    const context = useContext(AuthContext);
    if (context === undefined) {
        throw new Error('useAuth must be used within an AuthProvider');
    }
    return context;
}
