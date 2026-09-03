import { defineStore } from 'pinia'
import { ref } from 'vue'
import api from '@/api/axios'

export interface VehicleSummary {
  income: number
  expense: number
  profit: number
  month: number
  year: number
}

export interface Vehicle {
  id: number
  name: string
  plate_number: string | null
  brand: string | null
  model_year: string | null
  status: 'available' | 'rented' | 'maintenance'
  daily_rate: number
  color: string
  notes: string | null
  created_at: string
  summary: VehicleSummary
}

export const useVehiclesStore = defineStore('vehicles', () => {
  const vehicles  = ref<Vehicle[]>([])
  const loading   = ref(false)
  const error     = ref<string | null>(null)

  async function fetchVehicles(month?: number, year?: number) {
    loading.value = true
    error.value   = null
    try {
      const params: Record<string, number> = {}
      if (month) params.month = month
      if (year)  params.year  = year
      const { data } = await api.get('/vehicles', { params })
      vehicles.value = data.data
    } catch (e: any) {
      error.value = e?.response?.data?.message ?? 'Gagal memuat data kendaraan.'
    } finally {
      loading.value = false
    }
  }

  async function createVehicle(payload: Partial<Vehicle>) {
    const { data } = await api.post('/vehicles', payload)
    vehicles.value.unshift(data.data)
    return data.data as Vehicle
  }

  async function updateVehicle(id: number, payload: Partial<Vehicle>) {
    const { data } = await api.put(`/vehicles/${id}`, payload)
    const idx = vehicles.value.findIndex(v => v.id === id)
    if (idx !== -1) vehicles.value[idx] = data.data
    return data.data as Vehicle
  }

  async function deleteVehicle(id: number) {
    await api.delete(`/vehicles/${id}`)
    vehicles.value = vehicles.value.filter(v => v.id !== id)
  }

  async function fetchReport(month: number, year: number) {
    const { data } = await api.get('/vehicles/report', { params: { month, year } })
    return data.data
  }

  return {
    vehicles,
    loading,
    error,
    fetchVehicles,
    createVehicle,
    updateVehicle,
    deleteVehicle,
    fetchReport,
  }
})
