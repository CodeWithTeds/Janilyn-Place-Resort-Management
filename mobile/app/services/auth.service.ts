import api from './api';
import { LoginPayload, RegisterPayload, AuthResponse, User } from '../types/auth';
import { storage } from '../utils/storage';

export const authService = {
  async login(payload: LoginPayload): Promise<AuthResponse> {
    const response = await api.post<AuthResponse>('/login', payload);
    return response.data;
  },

  async register(payload: RegisterPayload): Promise<AuthResponse> {
    const response = await api.post<AuthResponse>('/register', payload);
    return response.data;
  },

  async logout(): Promise<void> {
    await api.post('/logout');
    await storage.removeItem('auth_token');
  },

  async getUser(): Promise<User> {
    const response = await api.get<User>('/user');
    return response.data;
  },
};
