<script setup lang="ts">
import { ref, computed, watch, onMounted } from 'vue'
import { type Vehicle } from '@/stores/vehicles'
import { useAccountsStore } from '@/stores/accounts'
import { useCategoriesStore } from '@/stores/categories'
import { useVehiclesStore } from '@/stores/vehicles'
import { useUiStore } from '@/stores/ui'
import { useFormatCurrency } from '@/composables/useFormatCurrency'
import CurrencyInput from '@/components/CurrencyInput.vue'
import SelectInput, { type SelectOption } from '@/components/SelectInput.vue'
import DateInput from '@/components/DateInput.vue'
import api from '@/api/axios'
import {
  XMarkIcon,
  BanknotesIcon,
  CalendarDaysIcon,
  UserIcon,
  PhoneIcon,
  TruckIcon,
  ArrowPathIcon
} from '@heroicons/vue/24/outline'

const props = defineProps<{
  show: boolean
  vehicle: Vehicle | null
}>()

const emit = defineEmits<{
  (e: 'update:show', value: boolean): void
  (e: 'saved', transaction: any): void
}>()

const accountsStore = useAccountsStore()
const categoriesStore = useCategoriesStore()
const vehiclesStore = useVehiclesStore()
const uiStore = useUiStore()
const { formatCurrency } = useFormatCurrency()

const submitting = ref(false)
const errorMessage = ref('')

// Form State
const durationDays = ref(1)
const dailyRate = ref(0)
const totalAmount = ref(0)
const isCustomAmount = ref(false)
const startDate = ref(new Date().toISOString().slice(0, 10))
const rentalType = ref<'lepas_kunci' | 'dengan_supir'>('lepas_kunci')
const customerName = ref('')
const customerPhone = ref('')
const selectedAccountId = ref<string | number>('')
const selectedCategoryId = ref<string | number>('')
const updateStatusToRented = ref(true)
const note = ref('')

// Quick duration presets: 1, 2, 3, 4, 5, 7, 10, 14, 30 hari
const durationPresets = [1, 2, 3, 4, 5, 7, 10, 14, 30]

onMounted(async () => {
  await Promise.all([
    accountsStore.fetchAccounts(),
    categoriesStore.fetchCategories()
  ])
})

// Accounts dropdown options
const accountOptions = computed<SelectOption[]>(() =>
  accountsStore.activeAccounts.map(a => ({
    value: String(a.id),
    label: `${a.name} (${formatCurrency(a.current_balance)})`,
    icon: '💳'
  }))
)

// Categories dropdown options (filter income)
const categoryOptions = computed<SelectOption[]>(() =>
  categoriesStore.incomeCategories.map(c => ({
    value: String(c.id),
    label: c.name,
    icon: '💰'
  }))
)

// Calculated return date
const returnDateFormatted = computed(() => {
  if (!startDate.value || durationDays.value < 1) return ''
  const start = new Date(startDate.value)
  if (isNaN(start.getTime())) return ''
  const end = new Date(start)
  end.setDate(start.getDate() + (durationDays.value - 1))
  
  const opts: Intl.DateTimeFormatOptions = { day: 'numeric', month: 'short', year: 'numeric' }
  const startStr = start.toLocaleDateString('id-ID', opts)
  const endStr = end.toLocaleDateString('id-ID', opts)
  
  if (durationDays.value === 1) {
    return `${startStr} (1 Hari)`
  }
  return `${startStr} s/d ${endStr} (${durationDays.value} Hari)`
})

// Sync state when vehicle changes or modal opens
watch(
  () => [props.show, props.vehicle],
  ([isOpen]) => {
    if (isOpen && props.vehicle) {
      const v = props.vehicle
      dailyRate.value = Number(v.daily_rate) || 200000
      durationDays.value = 1
      totalAmount.value = dailyRate.value
      isCustomAmount.value = false
      startDate.value = new Date().toISOString().slice(0, 10)
      customerName.value = ''
      customerPhone.value = ''
      note.value = ''
      errorMessage.value = ''
      updateStatusToRented.value = v.status !== 'rented'

      // Detect service type based on vehicle capacity / model
      const isLargeFleet = (v.capacity && v.capacity >= 10) || 
                           v.name.toLowerCase().includes('hiace') || 
                           v.name.toLowerCase().includes('bus')
      
      rentalType.value = isLargeFleet ? 'dengan_supir' : 'lepas_kunci'

      // Pre-select account (first active account)
      if (accountsStore.activeAccounts.length > 0) {
        selectedAccountId.value = String(accountsStore.activeAccounts[0].id)
      }

      // Pre-select category matching rental type
      syncCategoryWithRentalType(rentalType.value)
    }
  },
  { immediate: true }
)

function syncCategoryWithRentalType(type: 'lepas_kunci' | 'dengan_supir') {
  const incCats = categoriesStore.incomeCategories
  let matched = null

  if (type === 'lepas_kunci') {
    matched = incCats.find(c => c.name.toLowerCase().includes('lepas kunci'))
  } else {
    matched = incCats.find(c => c.name.toLowerCase().includes('supir'))
  }

  if (matched) {
    selectedCategoryId.value = String(matched.id)
  } else if (incCats.length > 0) {
    selectedCategoryId.value = String(incCats[0].id)
  }
}

watch(rentalType, (newType) => {
  syncCategoryWithRentalType(newType)
})

function setDuration(days: number) {
  durationDays.value = Math.max(1, days)
  totalAmount.value = dailyRate.value * durationDays.value
  isCustomAmount.value = false
}

function adjustDuration(delta: number) {
  setDuration(durationDays.value + delta)
}

function onAmountManualInput() {
  isCustomAmount.value = true
}

function resetToCalculatedRate() {
  totalAmount.value = dailyRate.value * durationDays.value
  isCustomAmount.value = false
}

async function handleSubmit() {
  if (!props.vehicle) return
  if (!selectedAccountId.value) {
    errorMessage.value = 'Silakan pilih rekening / dompet penerima.'
    return
  }
  if (!selectedCategoryId.value) {
    errorMessage.value = 'Silakan pilih kategori pemasukan sewa.'
    return
  }
  if (totalAmount.value <= 0) {
    errorMessage.value = 'Nominal sewa harus lebih dari Rp 0.'
    return
  }

  submitting.value = true
  errorMessage.value = ''

  try {
    const v = props.vehicle
    const typeLabel = rentalType.value === 'lepas_kunci' ? 'Lepas Kunci' : 'Mobil + Supir'
    
    // Format description: [Penyewa] ([No WA]) — Sewa [Tipe] [X] Hari [Mobil] ([Plat])
    let finalDesc = ''
    if (customerName.value.trim()) {
      const phonePart = customerPhone.value.trim() ? ` (${customerPhone.value.trim()})` : ''
      finalDesc = `${customerName.value.trim()}${phonePart} — `
    }
    finalDesc += `Sewa ${typeLabel} ${durationDays.value} Hari ${v.name} (${v.plate_number || 'Tanpa Plat'})`
    if (note.value.trim()) {
      finalDesc += ` · ${note.value.trim()}`
    }

    // 1. Create income transaction
    const { data: txData } = await api.post('/transactions', {
      type: 'income',
      amount: totalAmount.value,
      date: startDate.value,
      account_id: Number(selectedAccountId.value),
      category_id: Number(selectedCategoryId.value),
      vehicle_id: v.id,
      description: finalDesc,
    })

    // 2. Optionally update vehicle status to 'rented'
    if (updateStatusToRented.value && v.status !== 'rented') {
      try {
        await api.put(`/vehicles/${v.id}`, {
          status: 'rented'
        })
        const idx = vehiclesStore.vehicles.findIndex(item => item.id === v.id)
        if (idx !== -1) {
          vehiclesStore.vehicles[idx].status = 'rented'
        }
      } catch (err) {
        console.warn('Gagal update status armada otomatis:', err)
      }
    }

    // 3. Refresh stores
    accountsStore.fetchAccounts(true)
    vehiclesStore.fetchVehicles()

    uiStore.showToast(`Pemasukan sewa ${formatCurrency(totalAmount.value)} (${v.name} · ${durationDays.value} Hari) berhasil dicatat!`)
    emit('saved', txData)
    emit('update:show', false)
  } catch (err: any) {
    errorMessage.value = err.response?.data?.message || err.message || 'Gagal menyimpan transaksi sewa armada.'
  } finally {
    submitting.value = false
  }
}
</script>

<template>
  <Teleport to="body">
    <div
      v-if="show && vehicle"
      class="fixed inset-0 z-50 flex items-end sm:items-center justify-center p-0 sm:p-4 bg-slate-950/70 backdrop-blur-sm overflow-y-auto"
    >
      <div
        class="w-full max-w-xl bg-white dark:bg-slate-800 rounded-t-3xl sm:rounded-2xl shadow-2xl border border-slate-200 dark:border-slate-700 overflow-hidden my-auto max-h-[92vh] flex flex-col animate-in fade-in zoom-in-95 duration-150"
      >
        <!-- Modal Header (shrink-0) -->
        <div class="px-5 py-4 border-b border-slate-200 dark:border-slate-700 flex items-center justify-between shrink-0 bg-slate-50/70 dark:bg-slate-850">
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-emerald-600 text-white flex items-center justify-center shrink-0 shadow-xs">
              <BanknotesIcon class="w-5 h-5" />
            </div>
            <div>
              <div class="flex items-center gap-2">
                <h3 class="text-base sm:text-lg font-bold text-slate-900 dark:text-white">
                  Catat Pemasukan Sewa Mobil
                </h3>
                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300">
                  1-Klik
                </span>
              </div>
              <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                Pilih durasi hari, otomatis hitung tarif & langsung masuk laporan sewa.
              </p>
            </div>
          </div>
          <button
            type="button"
            @click="emit('update:show', false)"
            class="p-1.5 rounded-xl text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700 transition"
          >
            <XMarkIcon class="w-5 h-5" />
          </button>
        </div>

        <!-- Modal Body Content (min-h-0 flex-1 overflow-y-auto) -->
        <div class="p-4 sm:p-6 overflow-y-auto min-h-0 flex-1 space-y-4">
          <!-- Error Alert -->
          <div v-if="errorMessage" class="p-3 rounded-xl bg-rose-500/10 border border-rose-500/30 text-rose-700 dark:text-rose-300 text-xs font-medium">
            {{ errorMessage }}
          </div>

          <!-- Vehicle Info Card (Live Target) -->
          <div class="p-3.5 rounded-2xl bg-gradient-to-r from-slate-50 via-slate-50 to-primary-50/30 dark:from-slate-850 dark:to-slate-800 border border-slate-200 dark:border-slate-700 flex items-center justify-between gap-3">
            <div class="flex items-center gap-3 min-w-0">
              <div class="w-14 h-12 rounded-xl overflow-hidden bg-slate-200 dark:bg-slate-700 shrink-0 flex items-center justify-center">
                <img
                  v-if="vehicle.photo_url"
                  :src="vehicle.photo_url"
                  :alt="vehicle.name"
                  class="w-full h-full object-cover"
                />
                <TruckIcon v-else class="w-6 h-6 text-slate-400" />
              </div>
              <div class="min-w-0">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block truncate">
                  {{ vehicle.plate_number || 'Tanpa Plat' }} &bull; {{ vehicle.capacity || 7 }} Kursi
                </span>
                <h4 class="text-sm sm:text-base font-bold text-slate-900 dark:text-white truncate">
                  {{ vehicle.name }}
                </h4>
                <div class="flex items-center gap-1.5 text-xs text-slate-500 dark:text-slate-400">
                  <span>Tarif dasar:</span>
                  <span class="font-bold text-primary-600 dark:text-primary-400">{{ formatCurrency(dailyRate) }}</span>
                  <span class="text-[10px]">/hari</span>
                </div>
              </div>
            </div>

            <div class="text-right shrink-0">
              <span
                class="inline-block px-2.5 py-1 rounded-full text-[10px] font-bold"
                :class="vehicle.status === 'available' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300' : (vehicle.status === 'rented' ? 'bg-blue-100 text-blue-800 dark:bg-blue-950/60 dark:text-blue-300' : 'bg-amber-100 text-amber-800 dark:bg-amber-950/60 dark:text-amber-300')"
              >
                {{ vehicle.status === 'available' ? '🟢 Siap Disewa' : (vehicle.status === 'rented' ? '🔵 Sedang Disewa' : '🟡 Di Bengkel') }}
              </span>
            </div>
          </div>

          <!-- Section: Durasi Sewa (1-Klik Pilih Hari) -->
          <div class="space-y-2">
            <div class="flex items-center justify-between">
              <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">
                Durasi Sewa (Pilih Hari) <span class="text-rose-500">*</span>
              </label>
              <div class="flex items-center gap-2">
                <!-- Stepper (-) and (+) -->
                <button
                  type="button"
                  @click="adjustDuration(-1)"
                  :disabled="durationDays <= 1"
                  class="w-6 h-6 rounded-lg bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 disabled:opacity-40 text-slate-700 dark:text-slate-200 text-xs font-bold flex items-center justify-center transition"
                >
                  -
                </button>
                <span class="text-xs font-bold tabular-nums px-1.5 text-slate-900 dark:text-white">
                  {{ durationDays }} Hari
                </span>
                <button
                  type="button"
                  @click="adjustDuration(1)"
                  class="w-6 h-6 rounded-lg bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 text-xs font-bold flex items-center justify-center transition"
                >
                  +
                </button>
              </div>
            </div>

            <!-- Quick Duration Preset Pills -->
            <div class="grid grid-cols-5 gap-1.5 sm:gap-2">
              <button
                v-for="days in durationPresets.slice(0, 5)"
                :key="days"
                type="button"
                @click="setDuration(days)"
                class="py-2 px-1 rounded-xl text-xs font-bold transition flex flex-col items-center justify-center border"
                :class="durationDays === days ? 'bg-primary-600 text-white border-primary-600 shadow-xs scale-[1.02]' : 'bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300 border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700/60'"
              >
                <span>{{ days }} Hari</span>
                <span class="text-[9px] opacity-75 font-normal">
                  {{ formatCurrency(dailyRate * days) }}
                </span>
              </button>
            </div>

            <div class="grid grid-cols-4 gap-1.5 sm:gap-2 pt-0.5">
              <button
                v-for="days in durationPresets.slice(5)"
                :key="days"
                type="button"
                @click="setDuration(days)"
                class="py-1.5 px-1 rounded-xl text-xs font-bold transition flex items-center justify-center gap-1 border"
                :class="durationDays === days ? 'bg-primary-600 text-white border-primary-600 shadow-xs' : 'bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300 border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700/60'"
              >
                <span>{{ days === 7 ? '1 Mgg (7h)' : (days === 14 ? '2 Mgg (14h)' : (days === 30 ? '1 Bln (30h)' : `${days} Hari`)) }}</span>
              </button>
            </div>
          </div>

          <!-- Section: Tipe Sewa (Lepas Kunci vs +Driver) -->
          <div class="space-y-1.5">
            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300">
              Jenis Layanan Sewa
            </label>
            <div class="grid grid-cols-2 gap-2">
              <button
                type="button"
                @click="rentalType = 'lepas_kunci'"
                class="p-2.5 rounded-xl border text-xs font-bold transition flex items-center justify-center gap-2"
                :class="rentalType === 'lepas_kunci' ? 'bg-primary-50 dark:bg-primary-950/40 border-primary-500 text-primary-700 dark:text-primary-300 ring-1 ring-primary-500' : 'bg-white dark:bg-slate-800 border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400 hover:bg-slate-50'"
              >
                <span>🔑 Lepas Kunci</span>
              </button>
              <button
                type="button"
                @click="rentalType = 'dengan_supir'"
                class="p-2.5 rounded-xl border text-xs font-bold transition flex items-center justify-center gap-2"
                :class="rentalType === 'dengan_supir' ? 'bg-primary-50 dark:bg-primary-950/40 border-primary-500 text-primary-700 dark:text-primary-300 ring-1 ring-primary-500' : 'bg-white dark:bg-slate-800 border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400 hover:bg-slate-50'"
              >
                <span>👨‍✈️ Dengan Supir (+Driver)</span>
              </button>
            </div>
          </div>

          <!-- Section: Total Nominal & Perhitungan Harga -->
          <div class="p-3.5 rounded-2xl bg-emerald-50/70 dark:bg-emerald-950/30 border border-emerald-200 dark:border-emerald-800/50 space-y-2">
            <div class="flex items-center justify-between">
              <div>
                <span class="text-[11px] font-bold uppercase tracking-wider text-emerald-800 dark:text-emerald-300 block">
                  Total Pemasukan Sewa (Rp)
                </span>
                <span class="text-xs text-slate-500 dark:text-slate-400">
                  {{ durationDays }} Hari &times; {{ formatCurrency(dailyRate) }}
                </span>
              </div>
              <button
                v-if="isCustomAmount"
                type="button"
                @click="resetToCalculatedRate"
                class="text-[10px] text-emerald-700 dark:text-emerald-300 underline font-semibold hover:text-emerald-900"
              >
                Reset ke hitungan sistem
              </button>
            </div>

            <div class="relative">
              <CurrencyInput
                v-model="totalAmount"
                @update:modelValue="onAmountManualInput"
                placeholder="0"
                class="text-lg sm:text-xl font-extrabold text-emerald-700 dark:text-emerald-300 bg-white dark:bg-slate-900"
              />
            </div>
            <p class="text-[10px] text-slate-500 dark:text-slate-400">
              *Harga otomatis terhitung dari tarif harian &times; durasi. Anda tetap bisa mengubah nominal jika ada diskon atau nego khusus.
            </p>
          </div>

          <!-- Section: Tanggal Sewa & Estimasi Selesai -->
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <div>
              <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                Tanggal Mulai Sewa <span class="text-rose-500">*</span>
              </label>
              <DateInput
                v-model="startDate"
                placeholder="Pilih tanggal mulai sewa"
              />
            </div>
            <div>
              <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                Periode / Estimasi Kembali
              </label>
              <div class="px-3 py-2 bg-slate-100 dark:bg-slate-700/60 border border-slate-200 dark:border-slate-700 rounded-xl text-xs sm:text-sm font-semibold text-slate-800 dark:text-slate-200 truncate flex items-center gap-1.5">
                <CalendarDaysIcon class="w-4 h-4 text-primary-600 shrink-0" />
                <span class="truncate">{{ returnDateFormatted }}</span>
              </div>
            </div>
          </div>

          <!-- Section: Data Penyewa / Tamu -->
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <div>
              <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                Nama Penyewa / Tamu
              </label>
              <div class="relative">
                <UserIcon class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2" />
                <input
                  v-model="customerName"
                  type="text"
                  placeholder="Contoh: Bpk. Hendra"
                  class="w-full pl-9 pr-3 py-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 text-slate-900 dark:text-white"
                />
              </div>
            </div>
            <div>
              <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                No. WhatsApp Penyewa
              </label>
              <div class="relative">
                <PhoneIcon class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2" />
                <input
                  v-model="customerPhone"
                  type="tel"
                  placeholder="Contoh: 08123456789"
                  class="w-full pl-9 pr-3 py-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 text-slate-900 dark:text-white"
                />
              </div>
            </div>
          </div>

          <!-- Section: Rekening Kas & Kategori -->
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <div>
              <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                Masuk ke Rekening / Kas <span class="text-rose-500">*</span>
              </label>
              <SelectInput
                v-model="selectedAccountId"
                :options="accountOptions"
                placeholder="Pilih Rekening Kas..."
              />
            </div>
            <div>
              <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                Kategori Pemasukan <span class="text-rose-500">*</span>
              </label>
              <SelectInput
                v-model="selectedCategoryId"
                :options="categoryOptions"
                placeholder="Pilih Kategori..."
              />
            </div>
          </div>

          <!-- Section: Status Armada & Catatan -->
          <div class="space-y-3 pt-1">
            <label class="flex items-center gap-2.5 p-3 rounded-xl bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 cursor-pointer">
              <input
                v-model="updateStatusToRented"
                type="checkbox"
                class="w-4 h-4 rounded text-primary-600 focus:ring-primary-500 border-slate-300 dark:border-slate-600"
              />
              <div>
                <span class="text-xs font-bold text-slate-800 dark:text-slate-200 block">
                  Tandai armada ini sebagai "Sedang Disewa"
                </span>
                <span class="text-[11px] text-slate-400 block">
                  Status mobil akan berubah menjadi disewa di dashboard & halaman armada mobil.
                </span>
              </div>
            </label>

            <div>
              <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                Catatan Tambahan (Opsional)
              </label>
              <input
                v-model="note"
                type="text"
                placeholder="Misal: DP 50% atau antar unit ke Pelabuhan SBP"
                class="w-full px-3 py-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 text-slate-900 dark:text-white"
              />
            </div>
          </div>
        </div>

        <!-- Modal Footer (shrink-0) -->
        <div class="px-5 py-4 border-t border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/80 flex items-center justify-between gap-3 shrink-0">
          <div class="text-xs text-slate-500 dark:text-slate-400 hidden sm:block">
            Total: <strong class="text-slate-900 dark:text-white">{{ formatCurrency(totalAmount) }}</strong> ({{ durationDays }} Hari)
          </div>
          <div class="flex items-center justify-end gap-2 w-full sm:w-auto">
            <button
              type="button"
              @click="emit('update:show', false)"
              class="flex-1 sm:flex-initial px-4 py-2.5 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 rounded-xl text-xs sm:text-sm font-medium transition"
            >
              Batal
            </button>
            <button
              type="button"
              :disabled="submitting"
              @click="handleSubmit"
              class="flex-1 sm:flex-initial px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 disabled:opacity-50 text-white rounded-xl text-xs sm:text-sm font-bold shadow-sm transition flex items-center justify-center gap-2"
            >
              <ArrowPathIcon v-if="submitting" class="w-4 h-4 animate-spin" />
              <span>{{ submitting ? 'Menyimpan...' : 'Simpan Pemasukan Sewa' }}</span>
            </button>
          </div>
        </div>
      </div>
    </div>
  </Teleport>
</template>
