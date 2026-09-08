import { defineStore } from 'pinia'
import { ref } from 'vue'
import api from '@/api/axios'

export interface TourPackage {
  id: number
  user_id?: number
  vehicle_id?: number | null
  vehicle?: any
  title: string
  slug: string
  subtitle: string | null
  badge: string | null
  badge_color: 'blue' | 'indigo' | 'amber' | 'emerald'
  price: number
  formatted_price?: string
  price_label: string
  duration: string | null
  capacity: string | null
  vehicle_name: string | null
  description: string | null
  tour_route: string | null
  cover_photo_path: string | null
  cover_photo_url: string | null
  gallery_photos: (string | { id?: string; label?: string; path?: string; url?: string })[]
  gallery_photo_urls: string[]
  facilities: string[]
  itinerary: string[]
  included: string[]
  excluded: string[]
  cta_whatsapp_text: string | null
  sort_order: number
  is_active: boolean
  created_at?: string
  updated_at?: string
}

export const useTourPackagesStore = defineStore('tourPackages', () => {
  const packages = ref<TourPackage[]>([])
  const loading = ref(false)
  const error = ref<string | null>(null)

  async function fetchPackages() {
    loading.value = true
    error.value = null
    try {
      const { data } = await api.get('/tour-packages')
      packages.value = data.data
    } catch (e: any) {
      error.value = e?.response?.data?.message ?? 'Gagal memuat paket tour.'
    } finally {
      loading.value = false
    }
  }

  async function createPackage(payload: Partial<TourPackage>) {
    const { data } = await api.post('/tour-packages', payload)
    packages.value.push(data.data)
    return data.data as TourPackage
  }

  async function updatePackage(id: number, payload: Partial<TourPackage>) {
    const { data } = await api.put(`/tour-packages/${id}`, payload)
    const idx = packages.value.findIndex(p => p.id === id)
    if (idx !== -1) packages.value[idx] = data.data
    return data.data as TourPackage
  }

  async function deletePackage(id: number) {
    await api.delete(`/tour-packages/${id}`)
    packages.value = packages.value.filter(p => p.id !== id)
  }

  async function toggleStatus(id: number) {
    const { data } = await api.patch(`/tour-packages/${id}/toggle-status`)
    const idx = packages.value.findIndex(p => p.id === id)
    if (idx !== -1) packages.value[idx] = data.data
    return data.data as TourPackage
  }

  return {
    packages,
    loading,
    error,
    fetchPackages,
    createPackage,
    updatePackage,
    deletePackage,
    toggleStatus,
  }
})
