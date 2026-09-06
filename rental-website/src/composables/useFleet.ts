import { ref, computed } from 'vue'
import type { PublicVehicle, TransmissionFilter } from '@/types/fleet'
import { siteConfig } from '@/config/site'
import { getVehicleCategoryBracket } from '@/config/fleetCategories'

const CACHE_KEY_DATA = 'financeflow_public_fleet_data_v6'
const CACHE_KEY_TIME = 'financeflow_public_fleet_timestamp_v6'
const CACHE_KEY_FEATURED_DATA = 'financeflow_featured_fleet_data_v6'
const CACHE_KEY_FEATURED_TIME = 'financeflow_featured_fleet_timestamp_v6'
const CACHE_TTL_MS = 90 * 1000 // 90 detik TTL cache

const vehicles = ref<PublicVehicle[]>([])
const featuredVehicles = ref<PublicVehicle[]>([])
const totalFleetCount = ref<number>(0)
const loading = ref<boolean>(false)
const featuredLoading = ref<boolean>(false)
const error = ref<string | null>(null)
const featuredError = ref<string | null>(null)
const searchQuery = ref<string>('')
const transmissionFilter = ref<TransmissionFilter>('all')
const categoryFilter = ref<string>('all')

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
  /**
   * Mengambil SELURUH armada untuk halaman etalase lengkap (/armada)
   */
  const fetchVehicles = async (forceRefresh = false): Promise<void> => {
    setupVisibilityListener(fetchVehicles)

    const shouldBypassCache = forceRefresh || isBrowserReload()

    // 1. Cek Client-Side TTL Cache
    if (!shouldBypassCache && typeof window !== 'undefined') {
      try {
        const cachedData = sessionStorage.getItem(CACHE_KEY_DATA)
        const cachedTime = sessionStorage.getItem(CACHE_KEY_TIME)
        const now = Date.now()

        if (cachedData && cachedTime && (now - Number(cachedTime) < CACHE_TTL_MS)) {
          vehicles.value = JSON.parse(cachedData)
          const cachedTotal = sessionStorage.getItem('financeflow_total_fleet_count')
          if (cachedTotal) totalFleetCount.value = Number(cachedTotal)
          error.value = null
          return
        }
      } catch (e) {
        console.warn('Gagal membaca sessionStorage cache:', e)
      }
    }

    // 2. Fetch seluruh armada dari API
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
      if (typeof json.meta?.total_fleet === 'number') {
        totalFleetCount.value = json.meta.total_fleet
      } else if (data.length > 0) {
        totalFleetCount.value = data.length
      }

      // 3. Simpan hasil response ke sessionStorage
      if (typeof window !== 'undefined') {
        try {
          sessionStorage.setItem(CACHE_KEY_DATA, JSON.stringify(data))
          sessionStorage.setItem(CACHE_KEY_TIME, String(Date.now()))
          sessionStorage.setItem('financeflow_total_fleet_count', String(totalFleetCount.value))
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

  /**
   * Mengambil armada pilihan (Featured) khusus untuk Beranda (limit 3, backend filtering)
   */
  const fetchFeaturedVehicles = async (forceRefresh = false): Promise<void> => {
    const shouldBypassCache = forceRefresh || isBrowserReload()

    if (!shouldBypassCache && typeof window !== 'undefined') {
      try {
        const cachedData = sessionStorage.getItem(CACHE_KEY_FEATURED_DATA)
        const cachedTime = sessionStorage.getItem(CACHE_KEY_FEATURED_TIME)
        const now = Date.now()

        if (cachedData && cachedTime && (now - Number(cachedTime) < CACHE_TTL_MS)) {
          featuredVehicles.value = JSON.parse(cachedData)
          const cachedTotal = sessionStorage.getItem('financeflow_total_fleet_count')
          if (cachedTotal) totalFleetCount.value = Number(cachedTotal)
          featuredError.value = null
          return
        }
      } catch (e) {
        console.warn('Gagal membaca featured cache:', e)
      }
    }

    featuredLoading.value = true
    featuredError.value = null

    try {
      const url = `${siteConfig.apiUrl}?featured=true&limit=3`
      const response = await fetch(url, {
        headers: {
          'Accept': 'application/json',
        }
      })

      if (!response.ok) {
        throw new Error(`Server merespons dengan status ${response.status}`)
      }

      const json = await response.json()
      const data: PublicVehicle[] = Array.isArray(json.data) ? json.data : (Array.isArray(json) ? json : [])
      
      featuredVehicles.value = data
      if (typeof json.meta?.total_fleet === 'number') {
        totalFleetCount.value = json.meta.total_fleet
      } else if (data.length > 0 && totalFleetCount.value === 0) {
        totalFleetCount.value = data.length
      }

      if (typeof window !== 'undefined') {
        try {
          sessionStorage.setItem(CACHE_KEY_FEATURED_DATA, JSON.stringify(data))
          sessionStorage.setItem(CACHE_KEY_FEATURED_TIME, String(Date.now()))
          if (totalFleetCount.value > 0) {
            sessionStorage.setItem('financeflow_total_fleet_count', String(totalFleetCount.value))
          }
        } catch (e) {
          console.warn('Gagal menyimpan ke featured cache:', e)
        }
      }
    } catch (err: any) {
      console.error('Error fetching featured fleet:', err)
      featuredError.value = err.message || 'Gagal memuat armada pilihan.'
    } finally {
      featuredLoading.value = false
    }
  }

  // Filter reaktif: nama/brand, transmisi, dan kategori kapasitas kursi
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

      // 3. Cocokkan filter kategori kapasitas kursi
      if (categoryFilter.value !== 'all') {
        const bracket = getVehicleCategoryBracket(vehicle.capacity)
        if (bracket.id !== categoryFilter.value) return false
      }

      return true
    })
  })

  // Ringkasan status ketersediaan armada riil
  const stats = computed(() => {
    const total = vehicles.value.length > 0 ? vehicles.value.length : totalFleetCount.value
    const available = vehicles.value.filter(v => v.status === 'available').length
    const rented = vehicles.value.filter(v => v.status === 'rented').length
    const maintenance = vehicles.value.filter(v => v.status === 'maintenance').length
    return { total, available, rented, maintenance }
  })

  return {
    vehicles,
    featuredVehicles,
    totalFleetCount,
    filteredVehicles,
    loading,
    featuredLoading,
    error,
    featuredError,
    searchQuery,
    transmissionFilter,
    categoryFilter,
    stats,
    fetchVehicles,
    fetchFeaturedVehicles
  }
}
