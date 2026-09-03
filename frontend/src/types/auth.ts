export interface User {
  id: number
  name: string
  email: string
  avatar: string | null
  currency: string
  default_account_id: number | null
  created_at: string
}

export interface LoginPayload {
  email: string
  password: string
}

export interface RegisterPayload {
  name: string
  email: string
  password: string
  password_confirmation: string
}

export interface AuthResponse {
  user: User
  token: string
  message?: string
}
