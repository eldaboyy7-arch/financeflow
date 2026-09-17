import { ref, computed } from 'vue'

export type CurrencyCode = 'IDR' | 'SGD' | 'MYR'

export interface CurrencyConfig {
  code: CurrencyCode
  label: string
  symbol: string
  shortLabel: string
  flag: 'id' | 'sg' | 'my'
}

export const CURRENCIES: Record<CurrencyCode, CurrencyConfig> = {
  IDR: {
    code: 'IDR',
    label: 'Rupiah Indonesia',
    symbol: 'Rp',
    shortLabel: 'IDR',
    flag: 'id'
  },
  SGD: {
    code: 'SGD',
    label: 'Singapore Dollar',
    symbol: 'S$',
    shortLabel: 'SGD',
    flag: 'sg'
  },
  MYR: {
    code: 'MYR',
    label: 'Malaysian Ringgit',
    symbol: 'RM',
    shortLabel: 'MYR',
    flag: 'my'
  }
}

// Fallback rates jika offline atau API kurs eksternal lambat
// 1 SGD = 11.900 IDR, 1 MYR = 3.550 IDR
const DEFAULT_RATES: Record<'SGD' | 'MYR', number> = {
  SGD: 11900,
  MYR: 3550
}

const STORAGE_KEY_CURRENCY = '3pm_user_currency'
const STORAGE_KEY_RATES = '3pm_exchange_rates'
const STORAGE_KEY_RATES_TIME = '3pm_exchange_rates_time'
const RATES_CACHE_TTL_MS = 24 * 60 * 60 * 1000 // 24 Jam

// Global reactive state
const currentCurrency = ref<CurrencyCode>('IDR')
const rates = ref<Record<'SGD' | 'MYR', number>>({ ...DEFAULT_RATES })
const isInitialized = ref(false)

// Inisialisasi dari localStorage jika di sisi browser
function initCurrencyState() {
  if (isInitialized.value || typeof window === 'undefined') return
  isInitialized.value = true

  // 1. Muat pilihan mata uang sebelumnya
  try {
    const saved = localStorage.getItem(STORAGE_KEY_CURRENCY) as CurrencyCode | null
    if (saved && (saved === 'IDR' || saved === 'SGD' || saved === 'MYR')) {
      currentCurrency.value = saved
    }
  } catch {}

  // 2. Muat cache nilai kurs jika masih valid
  try {
    const savedRates = localStorage.getItem(STORAGE_KEY_RATES)
    const savedTime = localStorage.getItem(STORAGE_KEY_RATES_TIME)
    if (savedRates && savedTime && Date.now() - Number(savedTime) < RATES_CACHE_TTL_MS) {
      const parsed = JSON.parse(savedRates)
      if (parsed.SGD && parsed.MYR) {
        rates.value = parsed
        return
      }
    }
  } catch {}

  // 3. Ambil kurs terbaru di background (non-blocking)
  fetchLiveRates()
}

// Fetch kurs dari public exchange API (Open Exchange Rates / Frankfurter)
async function fetchLiveRates() {
  try {
    const res = await fetch('https://open.er-api.com/v6/latest/SGD')
    if (!res.ok) return
    const data = await res.json()
    if (data && data.rates && data.rates.IDR) {
      const sgdToIdr = Number(data.rates.IDR)
      const sgdToMyr = Number(data.rates.MYR) || 3.3
      const myrToIdr = sgdToIdr / sgdToMyr

      if (sgdToIdr > 8000 && myrToIdr > 2000) {
        rates.value = {
          SGD: Math.round(sgdToIdr),
          MYR: Math.round(myrToIdr)
        }
        localStorage.setItem(STORAGE_KEY_RATES, JSON.stringify(rates.value))
        localStorage.setItem(STORAGE_KEY_RATES_TIME, String(Date.now()))
      }
    }
  } catch {
    // Fallback otomatis digunakan tanpa mengganggu tampilan website
  }
}

export interface ConvertedPrice {
  amount: number
  symbol: string
  formatted: string
  originalFormatted: string
  isConverted: boolean
  currencyCode: CurrencyCode
}

export function useCurrency() {
  initCurrencyState()

  const setCurrency = (code: CurrencyCode) => {
    if (code === currentCurrency.value) return
    currentCurrency.value = code
    try {
      localStorage.setItem(STORAGE_KEY_CURRENCY, code)
    } catch {}
  }

  const activeConfig = computed(() => CURRENCIES[currentCurrency.value])

  /**
   * Konversi nominal Rupiah ke mata uang aktif saat ini.
   * Dilengkapi Smart Rounding (ke atas) agar tidak menghasilkan pecahan desimal kaku.
   */
  const convertPrice = (idrAmount: number | string | null | undefined): ConvertedPrice => {
    const num = Number(idrAmount) || 0
    const originalFormatted = `Rp ${num.toLocaleString('id-ID')}`

    if (num <= 0) {
      return {
        amount: 0,
        symbol: activeConfig.value.symbol,
        formatted: currentCurrency.value === 'IDR' ? 'Rp 0' : `${activeConfig.value.symbol} 0`,
        originalFormatted,
        isConverted: currentCurrency.value !== 'IDR',
        currencyCode: currentCurrency.value
      }
    }

    if (currentCurrency.value === 'IDR') {
      return {
        amount: num,
        symbol: 'Rp',
        formatted: originalFormatted,
        originalFormatted,
        isConverted: false,
        currencyCode: 'IDR'
      }
    }

    if (currentCurrency.value === 'SGD') {
      const rate = rates.value.SGD || DEFAULT_RATES.SGD
      // Smart rounding ke atas untuk kenyamanan turis dan margin aman bisnis
      const converted = Math.ceil(num / rate)
      return {
        amount: converted,
        symbol: 'S$',
        formatted: `S$ ${converted.toLocaleString('en-SG')}`,
        originalFormatted,
        isConverted: true,
        currencyCode: 'SGD'
      }
    }

    if (currentCurrency.value === 'MYR') {
      const rate = rates.value.MYR || DEFAULT_RATES.MYR
      const converted = Math.ceil(num / rate)
      return {
        amount: converted,
        symbol: 'RM',
        formatted: `RM ${converted.toLocaleString('ms-MY')}`,
        originalFormatted,
        isConverted: true,
        currencyCode: 'MYR'
      }
    }

    return {
      amount: num,
      symbol: 'Rp',
      formatted: originalFormatted,
      originalFormatted,
      isConverted: false,
      currencyCode: 'IDR'
    }
  }

  return {
    currentCurrency,
    activeConfig,
    currencies: CURRENCIES,
    rates,
    setCurrency,
    convertPrice
  }
}
