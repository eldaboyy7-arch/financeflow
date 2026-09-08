import { defineStore } from 'pinia'
import { ref } from 'vue'

export type AppMode = 'general' | 'rental'

export const useAppModeStore = defineStore('appMode', () => {
  const mode = ref<AppMode>('rental')

  function setMode(newMode: AppMode = 'rental') {
    mode.value = newMode
    localStorage.setItem('financeflow_mode', newMode)
  }

  function toggleMode() {
    setMode('rental')
  }

  const isRental  = () => true
  const isGeneral = () => false

  return { mode, setMode, toggleMode, isRental, isGeneral }
})
