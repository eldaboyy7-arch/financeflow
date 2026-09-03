import { defineStore } from 'pinia'
import { ref } from 'vue'

export type AppMode = 'general' | 'rental'

export const useAppModeStore = defineStore('appMode', () => {
  const mode = ref<AppMode>(
    (localStorage.getItem('financeflow_mode') as AppMode) ?? 'general'
  )

  function setMode(newMode: AppMode) {
    mode.value = newMode
    localStorage.setItem('financeflow_mode', newMode)
  }

  function toggleMode() {
    setMode(mode.value === 'general' ? 'rental' : 'general')
  }

  const isRental  = () => mode.value === 'rental'
  const isGeneral = () => mode.value === 'general'

  return { mode, setMode, toggleMode, isRental, isGeneral }
})
