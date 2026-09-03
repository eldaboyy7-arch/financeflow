import { defineStore } from 'pinia'
import { ref, watch } from 'vue'

type Theme = 'light' | 'dark'

export interface Toast {
  id: string
  type: 'success' | 'error' | 'info' | 'warning'
  message: string
  duration?: number
}

export const useUiStore = defineStore('ui', () => {
  const savedTheme = localStorage.getItem('theme') as Theme | null
  const theme = ref<Theme>(savedTheme ?? 'light')
  const sidebarOpen = ref(true)

  // ── Toasts ───────────────────────────────────────────────────
  const toasts = ref<Toast[]>([])

  function showToast(
    message: string,
    type: 'success' | 'error' | 'info' | 'warning' = 'success',
    duration = 3500
  ) {
    const id = Math.random().toString(36).substring(2, 9)
    toasts.value.push({ id, type, message, duration })

    if (duration > 0) {
      setTimeout(() => {
        removeToast(id)
      }, duration)
    }
  }

  function removeToast(id: string) {
    toasts.value = toasts.value.filter((t) => t.id !== id)
  }

  function applyTheme(t: Theme) {
    const html = document.documentElement
    if (t === 'dark') {
      html.classList.add('dark')
    } else {
      html.classList.remove('dark')
    }
  }

  function toggleTheme() {
    theme.value = theme.value === 'light' ? 'dark' : 'light'
  }

  // Apply theme on init
  applyTheme(theme.value)

  // Persist + apply on change
  watch(theme, (newTheme) => {
    localStorage.setItem('theme', newTheme)
    applyTheme(newTheme)
  })

  function toggleSidebar() {
    sidebarOpen.value = !sidebarOpen.value
  }

  return {
    theme,
    sidebarOpen,
    toasts,
    showToast,
    removeToast,
    toggleTheme,
    toggleSidebar,
  }
})
