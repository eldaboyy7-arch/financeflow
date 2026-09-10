<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { useVehiclesStore, type Vehicle } from '@/stores/vehicles'
import { useUiStore } from '@/stores/ui'
import { useAuthStore } from '@/stores/auth'
import { uploadFleetPhoto } from '@/services/supabaseStorage'
import { useFormatCurrency } from '@/composables/useFormatCurrency'
import MoneySpinner from '@/components/MoneySpinner.vue'
import CurrencyInput from '@/components/CurrencyInput.vue'
import SelectInput, { type SelectOption } from '@/components/SelectInput.vue'
import {
  PlusIcon,
  PencilSquareIcon,
  TrashIcon,
  XMarkIcon,
  TruckIcon,
  CheckIcon,
  MagnifyingGlassIcon,
  CameraIcon,
  FilmIcon,
  ArrowPathIcon,
  PhotoIcon,
  BanknotesIcon
} from '@heroicons/vue/24/outline'
import QuickVehicleIncomeModal from '@/components/rental/QuickVehicleIncomeModal.vue'

const store = useVehiclesStore()
const uiStore = useUiStore()
const authStore = useAuthStore()
const { formatCurrency } = useFormatCurrency()

const showQuickIncomeModal = ref(false)
const selectedVehicleForIncome = ref<Vehicle | null>(null)

function openQuickIncome(vehicle: Vehicle) {
  selectedVehicleForIncome.value = vehicle
  showQuickIncomeModal.value = true
}

const searchQuery = ref('')
const selectedStatus = ref<'all' | 'available' | 'rented' | 'maintenance'>('all')

const availableCount = computed(() => store.vehicles.filter(v => v.status === 'available').length)
const rentedCount = computed(() => store.vehicles.filter(v => v.status === 'rented').length)
const maintenanceCount = computed(() => store.vehicles.filter(v => v.status === 'maintenance').length)

const filteredVehicles = computed(() => {
  let list = store.vehicles

  if (selectedStatus.value !== 'all') {
    list = list.filter(v => v.status === selectedStatus.value)
  }

  if (searchQuery.value.trim()) {
    const q = searchQuery.value.toLowerCase().trim()
    list = list.filter(v =>
      (v.name || '').toLowerCase().includes(q) ||
      (v.plate_number || '').toLowerCase().includes(q) ||
      (v.brand || '').toLowerCase().includes(q)
    )
  }

  return list
})

function resetFilter() {
  searchQuery.value = ''
  selectedStatus.value = 'all'
}

const showModal = ref(false)
const editingId = ref<number | null>(null)
const submitting = ref(false)
const modalError = ref('')

const statusOptions: SelectOption[] = [
  { value: 'available', label: 'Tersedia', icon: '🟢' },
  { value: 'rented', label: 'Sedang Disewa', icon: '🔵' },
  { value: 'maintenance', label: 'Di Bengkel / Servis', icon: '🟡' },
]

const presetColors = [
  '#2563EB', '#0D9488', '#16A34A', '#D97706',
  '#DC2626', '#475569', '#0284C7', '#0F172A'
]

const transmissionOptions: SelectOption[] = [
  { value: 'matic', label: 'Matic (Otomatis)' },
  { value: 'manual', label: 'Manual' },
]

const fuelOptions: SelectOption[] = [
  { value: 'bensin', label: 'Bensin' },
  { value: 'diesel', label: 'Diesel' },
]

const defaultForm = {
  name: '',
  plate_number: '',
  brand: '',
  model_year: '',
  status: 'available' as 'available' | 'rented' | 'maintenance',
  daily_rate: 0 as number,
  color: '#2563EB',
  transmission: 'matic' as 'matic' | 'manual',
  capacity: 7,
  fuel_type: 'bensin' as 'bensin' | 'diesel',
  description: '',
  photo_path: '',
  photo_url: '',
  video_url: '',
  notes: '',
}
const form = ref({ ...defaultForm })
const uploadingPhoto = ref(false)
const photoFileInput = ref<HTMLInputElement | null>(null)

onMounted(() => store.fetchVehicles())

function openCreate() {
  editingId.value = null
  form.value = { ...defaultForm }
  modalError.value = ''
  showModal.value = true
}

function openEdit(v: Vehicle) {
  editingId.value = v.id
  form.value = {
    name: v.name,
    plate_number: v.plate_number ?? '',
    brand: v.brand ?? '',
    model_year: v.model_year ?? '',
    status: v.status,
    daily_rate: Number(v.daily_rate) || 0,
    color: v.color || '#2563EB',
    transmission: v.transmission || 'matic',
    capacity: Number(v.capacity) || 7,
    fuel_type: v.fuel_type || 'bensin',
    description: v.description ?? '',
    photo_path: v.photo_path ?? '',
    photo_url: v.photo_url ?? '',
    video_url: v.video_url ?? '',
    notes: v.notes ?? '',
  }
  modalError.value = ''
  showModal.value = true
}

async function handlePhotoChange(e: Event) {
  const target = e.target as HTMLInputElement
  const file = target.files?.[0]
  if (!file) return

  uploadingPhoto.value = true
  modalError.value = ''

  try {
    const userId = authStore.user?.id || 1
    const vehicleId = editingId.value ?? 'new'
    const result = await uploadFleetPhoto(file, userId, vehicleId)
    form.value.photo_path = result.path
    form.value.photo_url = result.url
    uiStore.showToast('Foto berhasil diunggah ke Storage!')
  } catch (err: any) {
    modalError.value = err.message || 'Gagal mengunggah foto ke storage.'
  } finally {
    uploadingPhoto.value = false
    if (photoFileInput.value) photoFileInput.value.value = ''
  }
}

function removePhoto() {
  form.value.photo_path = ''
  form.value.photo_url = ''
}

async function submitVehicle() {
  if (!form.value.name.trim()) {
    modalError.value = 'Nama kendaraan wajib diisi.'
    return
  }
  submitting.value = true
  modalError.value = ''
  try {
    if (editingId.value) {
      await store.updateVehicle(editingId.value, form.value)
      uiStore.showToast('Data armada berhasil diperbarui!')
    } else {
      await store.createVehicle(form.value)
      uiStore.showToast('Unit mobil baru berhasil ditambahkan!')
    }
    showModal.value = false
  } catch (err: any) {
    const data = err.response?.data
    if (data?.errors) {
      modalError.value = Object.values(data.errors as Record<string, string[]>).flat().join(' ')
    } else {
      modalError.value = data?.message ?? 'Gagal menyimpan armada.'
    }
  } finally {
    submitting.value = false
  }
}

async function deleteVehicle(id: number) {
  if (!confirm('Hapus unit kendaraan ini? Transaksi terkait tidak akan terhapus.')) return
  try {
    await store.deleteVehicle(id)
    uiStore.showToast('Kendaraan berhasil dihapus.', 'info')
  } catch (err: any) {
    uiStore.showToast(err.response?.data?.message ?? 'Gagal menghapus kendaraan.', 'error')
  }
}

function statusBadge(s: string) {
  if (s === 'available') return { label: 'Tersedia', class: 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-400' }
  if (s === 'rented') return { label: 'Disewa', class: 'bg-blue-50 text-blue-700 dark:bg-blue-950/40 dark:text-blue-400' }
  return { label: 'Di Bengkel', class: 'bg-amber-50 text-amber-700 dark:bg-amber-950/40 dark:text-amber-400' }
}
</script>

<template>
  <div class="space-y-5">
    <!-- Header -->
    <div class="flex items-center justify-between gap-3">
      <div>
        <h1 class="text-lg sm:text-xl font-bold text-slate-900 dark:text-white">Armada Mobil</h1>
        <p class="text-[11px] sm:text-xs text-slate-500 dark:text-slate-400 mt-0.5">
          {{ store.vehicles.length }} unit mobil terdaftar dalam sistem rental
        </p>
      </div>
      <button
        @click="openCreate"
        class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 dark:bg-white dark:hover:bg-slate-100 text-white dark:text-slate-900 text-xs sm:text-sm font-semibold shadow-sm transition-all shrink-0"
      >
        <PlusIcon class="w-4 h-4" />
        <span>Tambah Mobil</span>
      </button>
    </div>

    <!-- Loading -->
    <div v-if="store.loading" class="card p-8 flex items-center justify-center">
      <MoneySpinner size="md" text="Memuat daftar armada..." subtext="Mengambil data unit mobil dan laporan keuangan" />
    </div>

    <!-- Clean Empty State -->
    <div v-else-if="store.vehicles.length === 0" class="card p-12 flex flex-col items-center text-center">
      <div class="w-14 h-14 bg-slate-100 dark:bg-slate-700/60 rounded-2xl flex items-center justify-center mb-4">
        <TruckIcon class="w-7 h-7 text-slate-400" />
      </div>
      <p class="text-sm font-semibold text-slate-700 dark:text-slate-300">Belum ada armada mobil</p>
      <p class="text-xs text-slate-400 dark:text-slate-500 mt-1 mb-4 max-w-xs">
        Tambahkan unit mobil rental pertama Anda untuk mulai mencatat pendapatan sewa dan biaya operasional.
      </p>
      <button
        @click="openCreate"
        class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 dark:bg-white dark:hover:bg-slate-100 text-white dark:text-slate-900 text-xs sm:text-sm font-semibold shadow-sm transition-all"
      >
        <PlusIcon class="w-4 h-4" />
        Tambah Kendaraan Pertama
      </button>
    </div>

    <!-- Vehicle Content -->
    <div v-else class="space-y-4">
      <!-- Search Bar & Filter Status Pills -->
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2.5">
        <!-- Search Input -->
        <div class="relative flex-1 max-w-md">
          <MagnifyingGlassIcon class="w-4 h-4 text-slate-400 dark:text-slate-500 absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none" />
          <input
            v-model="searchQuery"
            type="text"
            placeholder="Cari nama mobil atau plat nomor..."
            class="input !pl-10 !pr-9 py-2 text-xs sm:text-sm w-full bg-white dark:bg-slate-800 border-slate-200 dark:border-slate-700 rounded-xl placeholder:text-slate-400 dark:placeholder:text-slate-500"
            style="padding-left: 2.5rem !important; padding-right: 2.25rem !important;"
          />
          <button
            v-if="searchQuery"
            @click="searchQuery = ''"
            class="absolute right-2.5 top-1/2 -translate-y-1/2 p-1 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors"
            title="Hapus pencarian"
          >
            <XMarkIcon class="w-4 h-4" />
          </button>
        </div>

        <!-- Status Filter Pills -->
        <div class="flex items-center gap-1.5 overflow-x-auto pb-1 sm:pb-0 text-xs font-semibold shrink-0">
          <button
            @click="selectedStatus = 'all'"
            :class="[
              'px-3 py-1.5 rounded-xl transition-all shrink-0',
              selectedStatus === 'all'
                ? 'bg-slate-900 text-white dark:bg-white dark:text-slate-900 shadow-sm font-bold'
                : 'bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-400 border border-slate-200 dark:border-slate-700 hover:bg-slate-50'
            ]"
          >
            Semua ({{ store.vehicles.length }})
          </button>
          <button
            @click="selectedStatus = 'available'"
            :class="[
              'px-3 py-1.5 rounded-xl transition-all shrink-0 flex items-center gap-1.5',
              selectedStatus === 'available'
                ? 'bg-emerald-600 text-white shadow-sm font-bold'
                : 'bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-400 border border-slate-200 dark:border-slate-700 hover:bg-slate-50'
            ]"
          >
            <span>🟢 Tersedia ({{ availableCount }})</span>
          </button>
          <button
            @click="selectedStatus = 'rented'"
            :class="[
              'px-3 py-1.5 rounded-xl transition-all shrink-0 flex items-center gap-1.5',
              selectedStatus === 'rented'
                ? 'bg-blue-600 text-white shadow-sm font-bold'
                : 'bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-400 border border-slate-200 dark:border-slate-700 hover:bg-slate-50'
            ]"
          >
            <span>🔵 Disewa ({{ rentedCount }})</span>
          </button>
          <button
            @click="selectedStatus = 'maintenance'"
            :class="[
              'px-3 py-1.5 rounded-xl transition-all shrink-0 flex items-center gap-1.5',
              selectedStatus === 'maintenance'
                ? 'bg-amber-600 text-white shadow-sm font-bold'
                : 'bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-400 border border-slate-200 dark:border-slate-700 hover:bg-slate-50'
            ]"
          >
            <span>🟡 Di Bengkel ({{ maintenanceCount }})</span>
          </button>
        </div>
      </div>

      <!-- Search / Filter Empty State -->
      <div v-if="filteredVehicles.length === 0" class="card p-10 flex flex-col items-center justify-center text-center">
        <div class="w-12 h-12 bg-slate-100 dark:bg-slate-700/60 rounded-2xl flex items-center justify-center mb-3">
          <MagnifyingGlassIcon class="w-6 h-6 text-slate-400" />
        </div>
        <p class="text-sm font-bold text-slate-700 dark:text-slate-200">Mobil tidak ditemukan</p>
        <p class="text-xs text-slate-400 mt-1 max-w-sm">
          Tidak ada armada yang cocok dengan kata kunci "{{ searchQuery }}" atau filter status yang dipilih.
        </p>
        <button
          @click="resetFilter"
          class="mt-3.5 px-3.5 py-1.5 text-xs font-semibold rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 transition-colors"
        >
          Reset Pencarian & Filter
        </button>
      </div>

      <!-- Vehicle Grid: 2 Kolom di Mobile ala Marketplace -->
      <div v-else class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-2.5 sm:gap-4">
        <div
          v-for="v in filteredVehicles"
          :key="v.id"
          class="card p-3 sm:p-4 hover:shadow-md transition-all group relative flex flex-col justify-between overflow-hidden"
        >
          <div>
            <!-- Top: Status Badge & Quick Actions -->
            <div class="flex items-center justify-between gap-1 mb-2">
              <span class="badge text-[9px] sm:text-[10px] px-1.5 sm:px-2 py-0.5 font-semibold" :class="statusBadge(v.status).class">
                {{ statusBadge(v.status).label }}
              </span>

              <div class="flex items-center gap-0.5">
                <button
                  @click="openEdit(v)"
                  class="p-1 rounded-lg text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700/60 transition-colors"
                  title="Edit Mobil"
                >
                  <PencilSquareIcon class="w-3.5 h-3.5" />
                </button>
                <button
                  @click="deleteVehicle(v.id)"
                  class="p-1 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-900/20 transition-colors"
                  title="Hapus Mobil"
                >
                  <TrashIcon class="w-3.5 h-3.5" />
                </button>
              </div>
            </div>

            <!-- Vehicle Visual Avatar or Real Photo (Marketplace Showcase) -->
            <div
              class="w-full h-20 sm:h-24 rounded-xl overflow-hidden mb-2 transition-transform group-hover:scale-[1.02] flex items-center justify-center relative bg-slate-100 dark:bg-slate-800"
              :style="{ backgroundColor: (v.color || '#2563EB') + '15' }"
            >
              <img
                v-if="v.photo_url"
                :src="v.photo_url"
                :alt="v.name"
                loading="lazy"
                decoding="async"
                class="w-full h-full object-cover"
              />
              <TruckIcon v-else class="w-8 h-8 sm:w-10 sm:h-10" :style="{ color: v.color || '#2563EB' }" />
            </div>

            <!-- Vehicle Details -->
            <div class="space-y-0.5">
              <span class="text-[9px] sm:text-[10px] font-bold text-slate-400 uppercase tracking-wider block truncate">
                {{ v.plate_number || 'Tanpa Plat' }}
              </span>
              <h3 class="font-bold text-slate-900 dark:text-white text-xs sm:text-sm truncate leading-snug">
                {{ v.name }}
              </h3>
              <div class="flex items-center gap-1.5 flex-wrap pt-0.5">
                <span v-if="v.transmission" class="text-[9px] px-1.5 py-0.2 rounded bg-slate-100 dark:bg-slate-700/70 text-slate-600 dark:text-slate-300 font-medium">
                  {{ v.transmission === 'matic' ? 'Matic' : 'Manual' }}
                </span>
                <span v-if="v.capacity" class="text-[9px] px-1.5 py-0.2 rounded bg-slate-100 dark:bg-slate-700/70 text-slate-600 dark:text-slate-300 font-medium">
                  {{ v.capacity }} Seat
                </span>
              </div>
            </div>

          <!-- Marketplace Daily Rate Price Tag & Quick Income Button -->
          <div class="mt-2.5 pt-2 border-t border-slate-100 dark:border-slate-700/60 flex items-center justify-between gap-1.5">
            <div>
              <div v-if="v.daily_rate > 0" class="flex items-baseline gap-0.5">
                <span class="text-xs sm:text-sm font-black text-slate-900 dark:text-white tabular-nums">
                  {{ formatCurrency(v.daily_rate) }}
                </span>
                <span class="text-[9px] text-slate-400">/hari</span>
              </div>
              <span v-else class="text-[10px] text-slate-400 italic">Tarif belum diatur</span>
            </div>

            <!-- 1-Klik Catat Pemasukan Sewa -->
            <button
              type="button"
              @click.stop="openQuickIncome(v)"
              class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg bg-emerald-50 hover:bg-emerald-100 dark:bg-emerald-950/40 dark:hover:bg-emerald-900/60 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800/50 text-[10px] sm:text-xs font-bold transition-all shadow-2xs shrink-0 active:scale-95"
              title="Catat Pemasukan Sewa Mobil Ini (Pilih Hari 1-Klik)"
            >
              <BanknotesIcon class="w-3.5 h-3.5 text-emerald-600 dark:text-emerald-400" />
              <span>Sewa</span>
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>

    <!-- Modal Form: Clean Neutral Styling -->
    <Teleport to="body">
      <div v-if="showModal" class="fixed inset-0 z-50 flex items-end sm:items-center justify-center p-0 sm:p-4">
        <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" @click="showModal = false"></div>
        <div class="relative bg-white dark:bg-slate-850 dark:bg-[#182234] rounded-t-3xl sm:rounded-2xl p-5 sm:p-6 w-full max-w-md shadow-2xl border border-slate-200/80 dark:border-slate-700 max-h-[92vh] overflow-y-auto">
          <!-- Drag bar for mobile -->
          <div class="w-10 h-1 bg-slate-200 dark:bg-slate-700 rounded-full mx-auto mb-3 sm:hidden"></div>

          <div class="flex items-center justify-between mb-5">
            <h3 class="text-base sm:text-lg font-bold text-slate-900 dark:text-white">
              {{ editingId ? 'Ubah Data Kendaraan' : 'Tambah Kendaraan Baru' }}
            </h3>
            <button @click="showModal = false" class="p-1.5 rounded-lg text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
              <XMarkIcon class="w-5 h-5" />
            </button>
          </div>

          <form @submit.prevent="submitVehicle" class="space-y-4">
            <!-- Nama Kendaraan -->
            <div>
              <label class="block text-xs font-semibold text-slate-600 dark:text-slate-300 mb-1.5">
                Nama Kendaraan <span class="text-rose-500">*</span>
              </label>
              <input
                v-model="form.name"
                type="text"
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800/80 text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 text-sm focus:outline-none focus:ring-2 focus:ring-slate-400 dark:focus:ring-slate-500 focus:bg-white dark:focus:bg-slate-800 transition-all"
                placeholder="cth. Toyota Hiace Premio"
                required
              />
            </div>

            <!-- Plat Nomor & Tahun -->
            <div class="grid grid-cols-2 gap-3">
              <div>
                <label class="block text-xs font-semibold text-slate-600 dark:text-slate-300 mb-1.5">Plat Nomor</label>
                <input
                  v-model="form.plate_number"
                  type="text"
                  class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800/80 text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 text-sm uppercase tracking-wider focus:outline-none focus:ring-2 focus:ring-slate-400 dark:focus:ring-slate-500 focus:bg-white dark:focus:bg-slate-800 transition-all"
                  placeholder="B 1234 XYZ"
                />
              </div>
              <div>
                <label class="block text-xs font-semibold text-slate-600 dark:text-slate-300 mb-1.5">Tahun Pembuatan</label>
                <input
                  v-model="form.model_year"
                  type="text"
                  class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800/80 text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 text-sm focus:outline-none focus:ring-2 focus:ring-slate-400 dark:focus:ring-slate-500 focus:bg-white dark:focus:bg-slate-800 transition-all"
                  placeholder="2023"
                />
              </div>
            </div>

            <!-- Status & Tarif Sewa -->
            <div class="grid grid-cols-2 gap-3">
              <div>
                <label class="block text-xs font-semibold text-slate-600 dark:text-slate-300 mb-1.5">Status Armada</label>
                <SelectInput
                  v-model="form.status"
                  :options="statusOptions"
                  placeholder="Pilih Status..."
                />
              </div>

              <div>
                <label class="block text-xs font-semibold text-slate-600 dark:text-slate-300 mb-1.5">Tarif Sewa / Hari (Rp)</label>
                <CurrencyInput
                  v-model="form.daily_rate"
                  placeholder="cth. 350.000"
                  class="w-full"
                />
              </div>
            </div>

            <!-- Foto Armada (Direct Upload to Supabase Storage) -->
            <div>
              <label class="block text-xs font-semibold text-slate-600 dark:text-slate-300 mb-1.5">
                Foto Kendaraan <span class="text-slate-400 font-normal">(Direct Upload)</span>
              </label>

              <!-- Photo Preview if exists -->
              <div v-if="form.photo_url" class="relative mb-2 rounded-xl overflow-hidden border border-slate-200 dark:border-slate-700 aspect-[16/9] bg-slate-100 dark:bg-slate-800">
                <img :src="form.photo_url" alt="Foto Mobil" class="w-full h-full object-cover" />
                <button
                  type="button"
                  @click="removePhoto"
                  class="absolute top-2 right-2 p-1.5 rounded-lg bg-black/60 hover:bg-black/80 text-white text-xs backdrop-blur-sm transition-all flex items-center gap-1"
                >
                  <TrashIcon class="w-3.5 h-3.5 text-rose-300" />
                  <span class="text-[10px]">Hapus Foto</span>
                </button>
              </div>

              <!-- Upload Button & File Input -->
              <div class="flex items-center gap-2">
                <input
                  ref="photoFileInput"
                  type="file"
                  accept="image/jpeg,image/png,image/webp,image/avif"
                  class="hidden"
                  @change="handlePhotoChange"
                />
                <button
                  type="button"
                  :disabled="uploadingPhoto"
                  @click="photoFileInput?.click()"
                  class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50/60 dark:bg-slate-800/80 hover:bg-slate-100 dark:hover:bg-slate-700 text-xs font-semibold text-slate-700 dark:text-slate-200 transition-all disabled:opacity-60"
                >
                  <ArrowPathIcon v-if="uploadingPhoto" class="w-4 h-4 animate-spin text-slate-500" />
                  <CameraIcon v-else class="w-4 h-4 text-slate-500" />
                  <span>{{ uploadingPhoto ? 'Mengunggah ke Storage...' : (form.photo_url ? 'Ganti Foto' : 'Unggah Foto Mobil') }}</span>
                </button>
                <span class="text-[11px] text-slate-400">Maks. 3 MB (JPG, PNG, WebP)</span>
              </div>
            </div>

            <!-- Spesifikasi Publik: Transmisi, Kursi, BBM -->
            <div class="grid grid-cols-3 gap-2.5">
              <div>
                <label class="block text-xs font-semibold text-slate-600 dark:text-slate-300 mb-1.5">Transmisi</label>
                <SelectInput
                  v-model="form.transmission"
                  :options="transmissionOptions"
                  placeholder="Transmisi"
                />
              </div>

              <div>
                <label class="block text-xs font-semibold text-slate-600 dark:text-slate-300 mb-1.5">Kapasitas</label>
                <div class="relative">
                  <input
                    v-model.number="form.capacity"
                    type="number"
                    min="1"
                    max="50"
                    class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800/80 text-slate-900 dark:text-slate-100 text-sm focus:outline-none focus:ring-2 focus:ring-slate-400 dark:focus:ring-slate-500 focus:bg-white dark:focus:bg-slate-800 transition-all"
                  />
                  <span class="absolute right-2.5 top-1/2 -translate-y-1/2 text-[10px] text-slate-400 pointer-events-none">Seat</span>
                </div>
              </div>

              <div>
                <label class="block text-xs font-semibold text-slate-600 dark:text-slate-300 mb-1.5">Bahan Bakar</label>
                <SelectInput
                  v-model="form.fuel_type"
                  :options="fuelOptions"
                  placeholder="BBM"
                />
              </div>
            </div>

            <!-- Media Video: YouTube / TikTok / Reels -->
            <div>
              <label class="block text-xs font-semibold text-slate-600 dark:text-slate-300 mb-1.5">
                Link Video Review <span class="text-slate-400 font-normal">(YouTube / TikTok / IG Reels)</span>
              </label>
              <div class="relative">
                <input
                  v-model="form.video_url"
                  type="url"
                  class="w-full pl-9 pr-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800/80 text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 text-xs focus:outline-none focus:ring-2 focus:ring-slate-400 dark:focus:ring-slate-500 focus:bg-white dark:focus:bg-slate-800 transition-all"
                  placeholder="https://www.youtube.com/watch?v=..."
                />
                <FilmIcon class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2" />
              </div>
              <p class="text-[10px] text-slate-400 mt-1">Tempel link resmi YouTube, TikTok, atau Instagram</p>
            </div>

            <!-- Deskripsi Publik Website -->
            <div>
              <label class="block text-xs font-semibold text-slate-600 dark:text-slate-300 mb-1.5">
                Deskripsi Publik <span class="text-slate-400 font-normal">(Tampil di Website Rental)</span>
              </label>
              <textarea
                v-model="form.description"
                rows="2"
                class="w-full px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800/80 text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 text-sm resize-none focus:outline-none focus:ring-2 focus:ring-slate-400 dark:focus:ring-slate-500 focus:bg-white dark:focus:bg-slate-800 transition-all"
                placeholder="cth. Kondisi prima, AC dingin, kabin wangi, bebas asap rokok, gratis charger HP..."
              ></textarea>
            </div>

            <!-- Warna Label -->
            <div>
              <label class="block text-xs font-semibold text-slate-600 dark:text-slate-300 mb-2">Warna Identitas Armada</label>
              <div class="flex items-center gap-2.5 flex-wrap">
                <button
                  v-for="c in presetColors"
                  :key="c"
                  type="button"
                  @click="form.color = c"
                  :style="{ backgroundColor: c }"
                  :class="[
                    'w-7 h-7 rounded-full flex items-center justify-center transition-all hover:scale-110 relative',
                    form.color === c ? 'ring-2 ring-slate-800 dark:ring-white ring-offset-2 dark:ring-offset-slate-900 scale-105 shadow-sm' : 'opacity-80 hover:opacity-100'
                  ]"
                >
                  <CheckIcon v-if="form.color === c" class="w-3.5 h-3.5 text-white stroke-[3]" />
                </button>
              </div>
            </div>

            <!-- Catatan Internal -->
            <div>
              <label class="block text-xs font-semibold text-slate-600 dark:text-slate-300 mb-1.5">
                Catatan Internal <span class="text-slate-400 font-normal">(Rahasia / Hanya Admin)</span>
              </label>
              <textarea
                v-model="form.notes"
                rows="2"
                class="w-full px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800/80 text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 text-sm resize-none focus:outline-none focus:ring-2 focus:ring-slate-400 dark:focus:ring-slate-500 focus:bg-white dark:focus:bg-slate-800 transition-all"
                placeholder="cth. Servis ganti oli tiap 10.000 km, STNK perpanjang Mei..."
              ></textarea>
            </div>

            <div v-if="modalError" class="text-xs text-rose-600 dark:text-rose-400 bg-rose-50 dark:bg-rose-950/40 rounded-xl px-3.5 py-2.5 border border-rose-200/60 dark:border-rose-800/40">
              {{ modalError }}
            </div>

            <!-- Action Buttons: Race Condition Protected (disabled during submit or upload) -->
            <div class="flex gap-2.5 pt-2">
              <button
                type="button"
                @click="showModal = false"
                :disabled="submitting || uploadingPhoto"
                class="flex-1 py-2.5 px-4 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 text-sm font-semibold transition-all disabled:opacity-60"
              >
                Batal
              </button>
              <button
                type="submit"
                :disabled="submitting || uploadingPhoto"
                class="flex-1 py-2.5 px-4 rounded-xl bg-slate-900 hover:bg-slate-800 dark:bg-white dark:hover:bg-slate-100 text-white dark:text-slate-900 text-sm font-semibold transition-all shadow-sm disabled:opacity-60 flex items-center justify-center gap-2"
              >
                <ArrowPathIcon v-if="submitting" class="w-4 h-4 animate-spin" />
                <span>{{ submitting ? 'Menyimpan...' : (editingId ? 'Simpan Perubahan' : 'Simpan Kendaraan') }}</span>
              </button>
            </div>
          </form>
        </div>
      </div>
    </Teleport>

    <!-- 1-Click Quick Vehicle Income Modal -->
    <QuickVehicleIncomeModal
      v-model:show="showQuickIncomeModal"
      :vehicle="selectedVehicleForIncome"
      @saved="store.fetchVehicles()"
    />
  </div>
</template>
