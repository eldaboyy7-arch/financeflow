import { ref, computed } from 'vue'
import type { PublicVehicle, TransmissionFilter } from '@/types/fleet'
import { siteConfig } from '@/config/site'

const CACHE_KEY_DATA = 'financeflow_public_fleet_data_v5'
const CACHE_KEY_TIME = 'financeflow_public_fleet_timestamp_v5'
const CACHE_TTL_MS = 90 * 1000 // 90 detik TTL cache

const vehicles = ref<PublicVehicle[]>([])
const loading = ref<boolean>(false)
const error = ref<string | null>(null)
const searchQuery = ref<string>('')
const transmissionFilter = ref<TransmissionFilter>('all')

// Deteksi apakah pemanggilan berasal dari browser reload (F5 / tombol reload browser)
function isBrowserReload(): boolean {
  if (typeof window === 'undefined' || typeof performance === 'undefined') return false
  try {
    const navEntries = performance.getEntriesByType('navigation') as PerformanceNavigationTiming[]
    if (navEntries && navEntries.length > 0) {
      return navEntries[0].type === 'reload'
    }
    return (performance as any).navigation?.type === 1
  } catch {
    return false
  }
}

// Inisialisasi listener visibilitychange sekali di client
let isVisibilityListenerAttached = false
function setupVisibilityListener(fetchFn: (force: boolean) => Promise<void>) {
  if (isVisibilityListenerAttached || typeof window === 'undefined' || typeof document === 'undefined') return
  isVisibilityListenerAttached = true

  document.addEventListener('visibilitychange', () => {
    if (document.visibilityState === 'visible') {
      const cachedTime = sessionStorage.getItem(CACHE_KEY_TIME)
      const now = Date.now()
      // Jika tab diaktifkan kembali dan data sudah berumur lebih dari 15 detik, revalidate di background
      if (!cachedTime || now - Number(cachedTime) > 15 * 1000) {
        fetchFn(true)
      }
    }
  })
}

export function useFleet() {
  const fetchVehicles = async (forceRefresh = false): Promise<void> => {
    setupVisibilityListener(fetchVehicles)

    // Deteksi reload: jika user menekan F5 / reload, otomatis paksa refresh agar perubahan terbaru langsung terlihat
    const shouldBypassCache = forceRefresh || isBrowserReload()

    // 1. Cek Client-Side TTL Cache pada sessionStorage jika tidak force refresh
    if (!shouldBypassCache && typeof window !== 'undefined') {
      try {
        const cachedData = sessionStorage.getItem(CACHE_KEY_DATA)
        const cachedTime = sessionStorage.getItem(CACHE_KEY_TIME)
        const now = Date.now()

        if (cachedData && cachedTime && (now - Number(cachedTime) < CACHE_TTL_MS)) {
          vehicles.value = JSON.parse(cachedData)
          error.value = null
          return
        }
      } catch (e) {
        console.warn('Gagal membaca sessionStorage cache:', e)
      }
    }

    // 2. Cache kedaluwarsa, reload, atau force refresh -> panggil Public Fleet API
    loading.value = true
    error.value = null

    try {
      const response = await fetch(siteConfig.apiUrl, {
        headers: {
          'Accept': 'application/json',
        }
      })

      if (!response.ok) {
        throw new Error(`Server merespons dengan status ${response.status}`)
      }

      const json = await response.json()
      const data: PublicVehicle[] = Array.isArray(json.data) ? json.data : (Array.isArray(json) ? json : [])
      
      vehicles.value = data

      // 3. Simpan hasil response baru ke sessionStorage
      if (typeof window !== 'undefined') {
        try {
          sessionStorage.setItem(CACHE_KEY_DATA, JSON.stringify(data))
          sessionStorage.setItem(CACHE_KEY_TIME, String(Date.now()))
        } catch (e) {
          console.warn('Gagal menyimpan ke sessionStorage cache:', e)
        }
      }
    } catch (err: any) {
      console.error('Error fetching public fleet:', err)
      error.value = err.message || 'Gagal memuat katalog armada. Silakan coba kembali.'
    } finally {
      loading.value = false
    }
  }

  // Filter reaktif: hanya berdasarkan nama/brand dan transmisi (tanpa kategori fiktif)
  const filteredVehicles = computed(() => {
    return vehicles.value.filter((vehicle) => {
      // 1. Cocokkan pencarian nama atau brand
      if (searchQuery.value.trim()) {
        const query = searchQuery.value.toLowerCase().trim()
        const matchName = vehicle.name.toLowerCase().includes(query)
        const matchBrand = vehicle.brand ? vehicle.brand.toLowerCase().includes(query) : false
        if (!matchName && !matchBrand) return false
      }

      // 2. Cocokkan filter transmisi
      if (transmissionFilter.value !== 'all') {
        if (vehicle.transmission !== transmissionFilter.value) return false
      }

      return true
    })
  })

  // Ringkasan status ketersediaan armada riil
  const stats = computed(() => {
    const total = vehicles.value.length
    const available = vehicles.value.filter(v => v.status === 'available').length
    const rented = vehicles.value.filter(v => v.status === 'rented').length
    const maintenance = vehicles.value.filter(v => v.status === 'maintenance').length
    return { total, available, rented, maintenance }
  })

  return {
    vehicles,
    filteredVehicles,
    loading,
    error,
    searchQuery,
    transmissionFilter,
    stats,
    fetchVehicles
  }
}
