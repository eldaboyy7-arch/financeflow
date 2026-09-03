import api from './axios'
import type { AuthResponse, LoginPayload, RegisterPayload, User } from '@/types/auth'

export const authApi = {
  register: (payload: RegisterPayload) =>
    api.post<AuthResponse>('/auth/register', payload),

  login: (payload: LoginPayload) =>
    api.post<AuthResponse>('/auth/login', payload),

  logout: () =>
    api.post<{ message: string }>('/auth/logout'),

  me: () =>
    api.get<{ user: User }>('/auth/me'),

  updateProfile: (payload: Partial<{ name: string; currency: string }>) =>
    api.put<{ user: User }>('/auth/profile', payload),

  changePassword: (payload: { current_password: string; password: string; password_confirmation: string }) =>
    api.put<{ message: string }>('/auth/password', payload),

  forgotPassword: (email: string) =>
    api.post<{ message: string }>('/auth/forgot-password', { email }),

  resetPassword: (payload: { token: string; email: string; password: string; password_confirmation: string }) =>
    api.post<{ message: string }>('/auth/reset-password', payload),
}
