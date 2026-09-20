import { ref } from 'vue'
import { tourPackages as fallbackPackages, type TourPackage } from '@/config/tourPackages'
import { siteConfig } from '@/config/site'

const CACHE_KEY_DATA = 'financeflow_tour_packages_data_v1'
const CACHE_KEY_TIME = 'financeflow_tour_packages_time_v1'
const CACHE_TTL_MS = 90 * 1000 // 90 detik TTL cache

// Shared reactive state across components
const packages = ref<TourPackage[]>([])
const loading = ref<boolean>(false)
const isBackgroundUpdating = ref<boolean>(false)
const error = ref<string | null>(null)
const isInitialized = ref<boolean>(false)

// Deteksi browser reload (F5 / tombol reload browser)
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

function normalizeApiPackage(item: any): TourPackage {
  const priceNum = Number(item.price) || 0
  const formattedPrice = priceNum > 0
    ? (item.formatted_price || `Rp ${priceNum.toLocaleString('id-ID')}`)
    : 'Konsultasi Gratis'

  const coverPhoto = item.cover_photo_url || item.cover_photo_path || '/images/fleet/hiace-commuter-silver.jpg'
  const gallery = Array.isArray(item.gallery_photo_urls) && item.gallery_photo_urls.length > 0
    ? item.gallery_photo_urls
    : (Array.isArray(item.gallery_photos) && item.gallery_photos.length > 0
        ? item.gallery_photos.map((p: any) => typeof p === 'string' ? p : (p.path || p.url || ''))
        : [coverPhoto])

  const matchedFallback = fallbackPackages.find(f => f.id === item.id || f.slug === item.slug)

  return {
    id: String(item.slug || item.id),
    slug: item.slug || `tour-${item.id}`,
    title: item.title,
    titleEn: item.titleEn || item.title_en || matchedFallback?.titleEn,
    subtitle: item.subtitle || '',
    subtitleEn: item.subtitleEn || item.subtitle_en || matchedFallback?.subtitleEn,
    badge: item.badge || undefined,
    badgeEn: item.badgeEn || item.badge_en || matchedFallback?.badgeEn,
    description: item.description || '',
    descriptionEn: item.descriptionEn || item.description_en || matchedFallback?.descriptionEn,
    duration: item.duration || 'Full Day Tour (8 - 10 Jam)',
    durationEn: item.durationEn || item.duration_en || matchedFallback?.durationEn,
    price: formattedPrice,
    rawPrice: priceNum,
    originalPrice: item.originalPrice || item.original_price || matchedFallback?.originalPrice,
    rawOriginalPrice: Number(item.rawOriginalPrice || item.raw_original_price || item.original_price) || matchedFallback?.rawOriginalPrice,
    discountPercent: Number(item.discountPercent || item.discount_percent) || matchedFallback?.discountPercent,
    discountBadge: item.discountBadge || item.discount_badge || matchedFallback?.discountBadge,
    discountBadgeEn: item.discountBadgeEn || item.discount_badge_en || matchedFallback?.discountBadgeEn,
    priceLabel: item.price_label || (priceNum > 0 ? 'HARGA MULAI' : 'KONSULTASI GRATIS'),
    priceLabelEn: item.priceLabelEn || item.price_label_en || matchedFallback?.priceLabelEn,
    vehicle: item.vehicle_name || item.vehicle || 'Toyota HiAce',
    vehicleEn: item.vehicleEn || item.vehicle_en || matchedFallback?.vehicleEn,
    capacity: item.capacity || '15 Person',
    capacityEn: item.capacityEn || item.capacity_en || matchedFallback?.capacityEn,
    vehiclePhoto: coverPhoto,
    galleryPhotos: gallery,
    facilities: Array.isArray(item.facilities) ? item.facilities : [],
    facilitiesEn: Array.isArray(item.facilitiesEn) ? item.facilitiesEn : (Array.isArray(item.facilities_en) ? item.facilities_en : matchedFallback?.facilitiesEn),
    tourRoute: item.tour_route || item.tourRoute || '',
    tourRouteEn: item.tourRouteEn || item.tour_route_en || matchedFallback?.tourRouteEn,
    itinerary: Array.isArray(item.itinerary) ? item.itinerary : [],
    itineraryEn: Array.isArray(item.itineraryEn) ? item.itineraryEn : (Array.isArray(item.itinerary_en) ? item.itinerary_en : matchedFallback?.itineraryEn),
    included: Array.isArray(item.included) ? item.included : [],
    includedEn: Array.isArray(item.includedEn) ? item.includedEn : (Array.isArray(item.included_en) ? item.included_en : matchedFallback?.includedEn),
    excluded: Array.isArray(item.excluded) ? item.excluded : [],
    excludedEn: Array.isArray(item.excludedEn) ? item.excludedEn : (Array.isArray(item.excluded_en) ? item.excluded_en : matchedFallback?.excludedEn),
    ctaWhatsappText: item.cta_whatsapp_text || item.ctaWhatsappText || `Halo Admin Bintan Travel, saya ingin booking ${item.title}...`,
    ctaWhatsappTextEn: item.ctaWhatsappTextEn || item.cta_whatsapp_text_en || matchedFallback?.ctaWhatsappTextEn
  }
}

// Listener visibilitychange dan focus untuk background revalidation saat tab/jendela kembali aktif
let isRevalidateListenerAttached = false
let periodicTimerAttached = false

function setupRevalidationListeners(fetchFn: (force: boolean) => Promise<void>) {
  if (typeof window === 'undefined' || typeof document === 'undefined') return

  const checkAndRevalidate = () => {
    const cachedTime = sessionStorage.getItem(CACHE_KEY_TIME)
    const now = Date.now()
    // Jika tab/jendela aktif kembali dan data sudah berumur > 15 detik, revalidate diam-diam
    if (!cachedTime || now - Number(cachedTime) > 15 * 1000) {
      fetchFn(true)
    }
  }

  if (!isRevalidateListenerAttached) {
    isRevalidateListenerAttached = true
    document.addEventListener('visibilitychange', () => {
      if (document.visibilityState === 'visible') {
        checkAndRevalidate()
      }
    })
    window.addEventListener('focus', checkAndRevalidate)
  }

  // Timer idle: periksa setiap 30 detik apakah cache sudah melewati 90s saat user tetap di halaman
  if (!periodicTimerAttached) {
    periodicTimerAttached = true
    setInterval(() => {
      const cachedTime = sessionStorage.getItem(CACHE_KEY_TIME)
      const now = Date.now()
      if (!cachedTime || now - Number(cachedTime) >= CACHE_TTL_MS) {
        fetchFn(true)
      }
    }, 30 * 1000)
  }
}

export function useTourPackages() {
  /**
   * Mengambil paket tour dari API dengan TTL cache 90s dan silent background update
   */
  const fetchTourPackages = async (forceRefresh = false): Promise<void> => {
    setupRevalidationListeners(fetchTourPackages)

    const shouldBypassCache = forceRefresh || isBrowserReload()

    // 1. Cek Client-Side TTL Cache
    if (!shouldBypassCache && typeof window !== 'undefined') {
      try {
        const cachedData = sessionStorage.getItem(CACHE_KEY_DATA)
        const cachedTime = sessionStorage.getItem(CACHE_KEY_TIME)
        const now = Date.now()

        if (cachedData && cachedTime && (now - Number(cachedTime) < CACHE_TTL_MS)) {
          const parsed = JSON.parse(cachedData)
          if (Array.isArray(parsed) && parsed.length > 0) {
            packages.value = parsed
            isInitialized.value = true
            error.value = null
            return
          }
        }
      } catch (e) {
        console.warn('Gagal membaca sessionStorage cache tour:', e)
      }
    }

    // 2. Fetch dari backend
    // Jika data sudah pernah ada (misal dari cache / initial), jangan reset array dan jangan aktifkan full-page skeleton
    // untuk mencegah layout shift / flicker saat user sedang membaca kartu
    const isSilentUpdate = packages.value.length > 0
    if (isSilentUpdate) {
      isBackgroundUpdating.value = true
    } else {
      loading.value = true
    }
    error.value = null

    try {
      const response = await fetch(siteConfig.tourPackagesApiUrl, {
        headers: {
          'Accept': 'application/json',
        }
      })

      if (!response.ok) {
        throw new Error(`Server merespons status ${response.status}`)
      }

      const json = await response.json()
      const rawData = Array.isArray(json.data) ? json.data : (Array.isArray(json) ? json : [])
      const normalized = rawData.map(normalizeApiPackage)
      packages.value = normalized

      // Simpan ke sessionStorage
      if (typeof window !== 'undefined') {
        try {
          sessionStorage.setItem(CACHE_KEY_DATA, JSON.stringify(normalized))
          sessionStorage.setItem(CACHE_KEY_TIME, String(Date.now()))
        } catch (e) {
          console.warn('Gagal menyimpan cache tour ke sessionStorage:', e)
        }
      }
    } catch (err: any) {
      console.warn('Gagal memuat paket tour dari backend, menggunakan data fallback lokal:', err)
      // Graceful offline fallback: jangan biarkan halaman kosong
      if (packages.value.length === 0) {
        packages.value = fallbackPackages
      }
      error.value = err.message || 'Gagal menyinkronkan data paket tour.'
    } finally {
      loading.value = false
      isBackgroundUpdating.value = false
      isInitialized.value = true
    }
  }

  return {
    packages,
    loading,
    isBackgroundUpdating,
    isInitialized,
    error,
    fetchTourPackages,
  }
}
