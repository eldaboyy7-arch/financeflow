<script setup lang="ts">
import { useLanguage, type LanguageCode } from '@/composables/useLanguage'

const { currentLang, setLanguage } = useLanguage()

const handleSelect = (code: LanguageCode) => {
  setLanguage(code)
}
</script>

<template>
  <div class="inline-flex items-center rounded-xl bg-slate-900 border border-slate-700 p-0.5 text-xs font-bold shadow-xs select-none">
    <button
      type="button"
      @click="handleSelect('en')"
      class="px-2 py-1 sm:px-2.5 sm:py-1 rounded-lg transition-all cursor-pointer"
      :class="currentLang === 'en'
        ? 'bg-blue-600 text-white shadow-xs font-black'
        : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/60 font-semibold'"
      aria-label="Switch to English"
      title="Switch to English"
    >
      EN
    </button>
    <button
      type="button"
      @click="handleSelect('id')"
      class="px-2 py-1 sm:px-2.5 sm:py-1 rounded-lg transition-all cursor-pointer"
      :class="currentLang === 'id'
        ? 'bg-blue-600 text-white shadow-xs font-black'
        : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/60 font-semibold'"
      aria-label="Ganti ke Bahasa Indonesia"
      title="Ganti ke Bahasa Indonesia"
    >
      ID
    </button>
  </div>
</template>
