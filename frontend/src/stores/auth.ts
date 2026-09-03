import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import type { User } from '@/types/auth'
import { authApi } from '@/api/auth'
import api from '@/api/axios'

export const useAuthStore = defineStore('auth', () => {
  const user = ref<User | null>(null)
  const token = ref<string | null>(localStorage.getItem('token'))

  const isAuthenticated = computed(() => !!token.value && !!user.value)

  function setAuth(userData: User, tokenValue: string) {
    user.value = userData
    token.value = tokenValue
    localStorage.setItem('token', tokenValue)
    api.defaults.headers.common['Authorization'] = `Bearer ${tokenValue}`
  }

  function clearAuth() {
    user.value = null
    token.value = null
    localStorage.removeItem('token')
    delete api.defaults.headers.common['Authorization']
  }

  async function fetchMe() {
    try {
      const { data } = await authApi.me()
      user.value = data.user
    } catch {
      clearAuth()
    }
  }

  async function logout() {
    try {
      await authApi.logout()
    } catch (e) {
      console.warn('Logout API failed:', e)
    } finally {
      clearAuth()
    }
  }

  return { user, token, isAuthenticated, setAuth, clearAuth, fetchMe, logout }
})
