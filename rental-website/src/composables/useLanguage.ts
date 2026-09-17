import { ref, computed } from 'vue'
import { en, type LocaleSchema } from '@/locales/en'
import { id } from '@/locales/id'

export type LanguageCode = 'en' | 'id'

export interface LanguageOption {
  code: LanguageCode
  label: string
  shortLabel: string
  flag: string
}

export const LANGUAGES: Record<LanguageCode, LanguageOption> = {
  en: {
    code: 'en',
    label: 'English',
    shortLabel: 'EN',
    flag: '🇬🇧'
  },
  id: {
    code: 'id',
    label: 'Bahasa Indonesia',
    shortLabel: 'ID',
    flag: '🇮🇩'
  }
}

const STORAGE_KEY_LANG = '3pm_user_lang'

// Global reactive state, default is ENGLISH ('en')
const currentLang = ref<LanguageCode>('en')
const isInitialized = ref(false)

const dictionaries: Record<LanguageCode, LocaleSchema> = {
  en,
  id
}

function initLanguageState() {
  if (isInitialized.value || typeof window === 'undefined') return
  isInitialized.value = true

  try {
    const saved = localStorage.getItem(STORAGE_KEY_LANG) as LanguageCode | null
    if (saved === 'en' || saved === 'id') {
      currentLang.value = saved
    } else {
      // Default to English as requested
      currentLang.value = 'en'
    }
  } catch {
    currentLang.value = 'en'
  }
}

export function useLanguage() {
  initLanguageState()

  const setLanguage = (lang: LanguageCode) => {
    if (lang === currentLang.value) return
    currentLang.value = lang
    try {
      localStorage.setItem(STORAGE_KEY_LANG, lang)
    } catch {}
  }

  const isEnglish = computed(() => currentLang.value === 'en')

  /**
   * Helper function to retrieve nested translation keys.
   * Example: t('nav.home'), t('search.paxCount', { count: 4 })
   */
  const t = (path: string, params?: Record<string, string | number>): string => {
    const dict = dictionaries[currentLang.value] || dictionaries.en
    const keys = path.split('.')

    let result: any = dict
    for (const key of keys) {
      if (result && typeof result === 'object' && key in result) {
        result = result[key]
      } else {
        // Fallback to English dictionary if key is missing in active language
        let fallbackResult: any = dictionaries.en
        for (const fbKey of keys) {
          if (fallbackResult && typeof fallbackResult === 'object' && fbKey in fallbackResult) {
            fallbackResult = fallbackResult[fbKey]
          } else {
            return path
          }
        }
        result = fallbackResult
        break
      }
    }

    if (typeof result !== 'string') {
      return path
    }

    // Replace template parameters like {count}
    if (params) {
      return Object.entries(params).reduce((acc, [k, v]) => {
        return acc.replace(new RegExp(`\\{${k}\\}`, 'g'), String(v))
      }, result)
    }

    return result
  }

  /**
   * Helper function to retrieve array translations (e.g. list of tour inclusions)
   */
  const tList = (path: string): string[] => {
    const dict = dictionaries[currentLang.value] || dictionaries.en
    const keys = path.split('.')

    let result: any = dict
    for (const key of keys) {
      if (result && typeof result === 'object' && key in result) {
        result = result[key]
      } else {
        let fallbackResult: any = dictionaries.en
        for (const fbKey of keys) {
          if (fallbackResult && typeof fallbackResult === 'object' && fbKey in fallbackResult) {
            fallbackResult = fallbackResult[fbKey]
          } else {
            return []
          }
        }
        result = fallbackResult
        break
      }
    }

    return Array.isArray(result) ? result : []
  }

  return {
    currentLang,
    languages: LANGUAGES,
    isEnglish,
    setLanguage,
    t,
    tList
  }
}
