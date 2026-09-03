<script setup lang="ts">
import { ref, onMounted, computed } from 'vue'
import { useVehiclesStore } from '@/stores/vehicles'
import { useUiStore } from '@/stores/ui'
import { useFormatCurrency } from '@/composables/useFormatCurrency'
import MoneySpinner from '@/components/MoneySpinner.vue'
import CurrencyInput from '@/components/CurrencyInput.vue'
import SelectInput, { type SelectOption } from '@/components/SelectInput.vue'
import api from '@/api/axios'
import {
  ArrowsRightLeftIcon,
  PlusIcon,
  TruckIcon,
  XMarkIcon,
  ArrowUpRightIcon,
  ArrowDownRightIcon,
} from '@heroicons/vue/24/outline'

const vehiclesStore = useVehiclesStore()
const uiStore = useUiStore()
const { formatCurrency } = useFormatCurrency()

function formatDate(dateStr: string) {
  if (!dateStr) return '-'
  try {
    const d = new Date(dateStr)
    if (isNaN(d.getTime())) return dateStr
    return d.toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' })
  } catch {
    return dateStr
  }
}

// ── Modal State ──────────────────────────────────────────────
const showModal = ref(false)
const submitting = ref(false)
const modalError = ref('')

const form = ref({
  type: 'income' as 'income' | 'expense',
  vehicle_id: '' as string | number,
  amount: 0 as number,
  date: new Date().toISOString().slice(0, 10),
  description: '',
  account_id: '' as string | number,
  category_id: '' as string | number,
})

// ── Accounts & Categories ────────────────────────────────────
const accounts = ref<any[]>([])
const categories = ref<any[]>([])

// ── Recent Transactions ──────────────────────────────────────
const transactions = ref<any[]>([])
const loadingTx = ref(false)

onMounted(async () => {
  await vehiclesStore.fetchVehicles()
  const [accs, cats] = await Promise.all([
    api.get('/accounts'),
    api.get('/categories', { params: { mode: 'rental' } }),
  ])
  accounts.value = accs.data.data ?? accs.data
  categories.value = cats.data.data ?? cats.data

  if (accounts.value.length) form.value.account_id = accounts.value[0].id
  if (vehiclesStore.vehicles.length) form.value.vehicle_id = vehiclesStore.vehicles[0].id
  fetchTx()
})

const filteredCategories = computed(() =>
  categories.value.filter((c: any) => c.type === form.value.type)
)

const vehicleOptions = computed<SelectOption[]>(() =>
  vehiclesStore.vehicles.map((v) => ({
    value: v.id,
    label: v.plate_number ? `${v.name} (${v.plate_number})` : v.name,
    icon: '🚗',
  }))
)

const categoryOptions = computed<SelectOption[]>(() =>
  filteredCategories.value.map((c: any) => ({
    value: c.id,
    label: c.name,
    icon: c.icon || '🏷️',
  }))
)

const accountOptions = computed<SelectOption[]>(() =>
  accounts.value.map((a: any) => ({
    value: a.id,
    label: a.name,
    icon: a.icon || '💳',
  }))
)

async function fetchTx() {
  loadingTx.value = true
  try {
    const { data } = await api.get('/transactions', { params: { mode: 'rental', per_page: 50 } })
    transactions.value = data.data ?? []
  } finally {
    loadingTx.value = false
  }
}

function openModal(type: 'income' | 'expense') {
  form.value.type = type
  form.value.amount = 0
  form.value.description = ''
  form.value.date = new Date().toISOString().slice(0, 10)
  if (vehiclesStore.vehicles.length) form.value.vehicle_id = vehiclesStore.vehicles[0].id
  if (accounts.value.length) form.value.account_id = accounts.value[0].id
  const cats = categories.value.filter((c: any) => c.type === type)
  if (cats.length) form.value.category_id = cats[0].id
  modalError.value = ''
  showModal.value = true
}

async function submitTransaction() {
  if (!form.value.amount || !form.value.vehicle_id || !form.value.account_id || !form.value.category_id) {
    modalError.value = 'Mohon lengkapi semua kolom yang bertanda *.'
    return
  }
  submitting.value = true
  modalError.value = ''
  try {
    await api.post('/transactions', {
      type: form.value.type,
      vehicle_id: form.value.vehicle_id || null,
      amount: parseFloat(String(form.value.amount)),
      date: form.value.date,
      description: form.value.description || (form.value.type === 'income' ? 'Sewa Mobil' : 'Biaya Operasional'),
      account_id: form.value.account_id,
      category_id: form.value.category_id,
    })
    uiStore.showToast('Transaksi rental berhasil dicatat!')
    showModal.value = false
    vehiclesStore.fetchVehicles()
    fetchTx()
  } catch (e: any) {
    const data = e?.response?.data
    if (data?.errors) {
      modalError.value = Object.values(data.errors as Record<string, string[]>).flat().join(' ')
    } else {
      modalError.value = data?.message ?? 'Gagal menyimpan transaksi.'
    }
  } finally {
    submitting.value = false
  }
}
</script>

<template>
  <div class="space-y-5">
    <!-- Header -->
    <div class="flex items-center justify-between gap-2">
      <div>
        <h1 class="text-lg sm:text-xl font-bold text-slate-900 dark:text-white">Transaksi Rental</h1>
        <p class="text-[11px] sm:text-xs text-slate-500 dark:text-slate-400 mt-0.5">
          Catat uang sewa masuk dan biaya operasional armada
        </p>
      </div>

      <!-- Action Buttons in Desktop Header -->
      <div class="hidden sm:flex items-center gap-2">
        <button
          @click="openModal('income')"
          class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs sm:text-sm font-semibold shadow-sm transition-all"
        >
          <PlusIcon class="w-4 h-4" />
          Sewa Masuk
        </button>
        <button
          @click="openModal('expense')"
          class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 dark:bg-white dark:hover:bg-slate-100 text-white dark:text-slate-900 text-xs sm:text-sm font-semibold shadow-sm transition-all"
        >
          <PlusIcon class="w-4 h-4" />
          Biaya Keluar
        </button>
      </div>
    </div>

    <!-- 2 Quick Action Cards (Clean, Matching Dashboard Style) -->
    <div class="grid grid-cols-2 gap-3.5">
      <button
        @click="openModal('income')"
        class="card p-4 hover:shadow-md transition-all text-left flex items-center gap-3 group"
      >
        <div class="w-10 h-10 rounded-xl bg-emerald-50 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0">
          <ArrowUpRightIcon class="w-5 h-5 stroke-[2.5]" />
        </div>
        <div>
          <p class="text-sm font-bold text-slate-900 dark:text-white group-hover:text-emerald-600 transition-colors">
            + Sewa Masuk
          </p>
          <p class="text-xs text-slate-400 mt-0.5">Lepas kunci, supir, drop-off</p>
        </div>
      </button>

      <button
        @click="openModal('expense')"
        class="card p-4 hover:shadow-md transition-all text-left flex items-center gap-3 group"
      >
        <div class="w-10 h-10 rounded-xl bg-rose-50 dark:bg-rose-950/50 text-rose-600 dark:text-rose-400 flex items-center justify-center shrink-0">
          <ArrowDownRightIcon class="w-5 h-5 stroke-[2.5]" />
        </div>
        <div>
          <p class="text-sm font-bold text-slate-900 dark:text-white group-hover:text-rose-600 transition-colors">
            + Biaya Operasional
          </p>
          <p class="text-xs text-slate-400 mt-0.5">BBM, servis, cuci, sparepart</p>
        </div>
      </button>
    </div>

    <!-- Recent Transactions -->
    <div class="card overflow-hidden">
      <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-700 flex items-center justify-between">
        <h2 class="text-sm font-bold text-slate-900 dark:text-white">Riwayat Transaksi Rental</h2>
        <span class="text-xs text-slate-400 font-medium">{{ transactions.length }} Catatan</span>
      </div>

      <div v-if="loadingTx" class="p-12 flex items-center justify-center">
        <MoneySpinner size="sm" text="Memuat riwayat transaksi rental..." />
      </div>

      <!-- Clean Standard Empty State -->
      <div v-else-if="transactions.length === 0" class="p-12 flex flex-col items-center text-center">
        <div class="w-14 h-14 bg-slate-100 dark:bg-slate-700/60 rounded-2xl flex items-center justify-center mb-4">
          <ArrowsRightLeftIcon class="w-7 h-7 text-slate-400" />
        </div>
        <p class="text-sm font-semibold text-slate-700 dark:text-slate-300">Belum ada transaksi rental</p>
        <p class="text-xs text-slate-400 dark:text-slate-500 mt-1 mb-4 max-w-xs">
          Catat uang sewa masuk atau biaya operasional pertama untuk armada mobil Anda.
        </p>
        <div class="flex gap-2">
          <button
            @click="openModal('income')"
            class="px-3.5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold shadow-sm transition-all"
          >
            + Catat Sewa
          </button>
          <button
            @click="openModal('expense')"
            class="px-3.5 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 dark:bg-white dark:hover:bg-slate-100 text-white dark:text-slate-900 text-xs font-semibold shadow-sm transition-all"
          >
            + Catat Biaya
          </button>
        </div>
      </div>

      <!-- Transaction Items -->
      <div v-else class="divide-y divide-slate-100 dark:divide-slate-700/60">
        <div
          v-for="tx in transactions"
          :key="tx.id"
          class="flex items-center gap-3.5 px-5 py-3.5 hover:bg-slate-50/70 dark:hover:bg-slate-700/30 transition-colors"
        >
          <div
            :class="[
              'w-10 h-10 rounded-xl flex items-center justify-center shrink-0',
              tx.type === 'income'
                ? 'bg-emerald-50 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400'
                : 'bg-rose-50 dark:bg-rose-950/50 text-rose-600 dark:text-rose-400'
            ]"
          >
            <ArrowUpRightIcon v-if="tx.type === 'income'" class="w-5 h-5 stroke-[2.5]" />
            <ArrowDownRightIcon v-else class="w-5 h-5 stroke-[2.5]" />
          </div>

          <div class="flex-1 min-w-0">
            <p class="text-sm font-semibold text-slate-900 dark:text-white truncate">
              {{ tx.description || (tx.type === 'income' ? 'Sewa Masuk' : 'Biaya Operasional') }}
            </p>
            <div class="flex items-center gap-2 mt-0.5 text-xs text-slate-400">
              <span>{{ formatDate(tx.date) }}</span>
              <template v-if="tx.vehicle">
                <span>·</span>
                <span class="inline-flex items-center gap-1 font-medium text-slate-700 dark:text-slate-300">
                  <TruckIcon class="w-3 h-3 text-slate-400" />
                  {{ tx.vehicle.name }}
                </span>
              </template>
              <template v-if="tx.category">
                <span>·</span>
                <span class="text-slate-500">{{ tx.category.name }}</span>
              </template>
            </div>
          </div>

          <div class="text-right shrink-0">
            <p :class="['text-sm font-bold tabular-nums', tx.type === 'income' ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-500 dark:text-rose-400']">
              {{ tx.type === 'income' ? '+' : '−' }}{{ formatCurrency(tx.amount) }}
            </p>
            <span class="text-[11px] text-slate-400 block mt-0.5">{{ tx.account?.name || 'Kas' }}</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal Form: Clean Neutral Styling (No Purple!) -->
    <Teleport to="body">
      <div v-if="showModal" class="fixed inset-0 z-50 flex items-end sm:items-center justify-center p-0 sm:p-4">
        <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" @click="showModal = false"></div>
        <div class="relative bg-white dark:bg-[#182234] rounded-t-3xl sm:rounded-2xl p-5 sm:p-6 w-full max-w-md shadow-2xl border border-slate-200/80 dark:border-slate-700 max-h-[92vh] overflow-y-auto">
          <!-- Drag bar for mobile -->
          <div class="w-10 h-1 bg-slate-200 dark:bg-slate-700 rounded-full mx-auto mb-3 sm:hidden"></div>

          <div class="flex items-center justify-between mb-5">
            <h3 class="text-base sm:text-lg font-bold text-slate-900 dark:text-white">
              {{ form.type === 'income' ? 'Catat Sewa Masuk (+)' : 'Catat Biaya Operasional (−)' }}
            </h3>
            <button @click="showModal = false" class="p-1.5 rounded-lg text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
              <XMarkIcon class="w-5 h-5" />
            </button>
          </div>

          <form @submit.prevent="submitTransaction" class="space-y-4">
            <!-- Pilih Kendaraan -->
            <div>
              <label class="block text-xs font-semibold text-slate-600 dark:text-slate-300 mb-1.5">
                Pilih Kendaraan <span class="text-rose-500">*</span>
              </label>
              <SelectInput
                v-model="form.vehicle_id"
                :options="vehicleOptions"
                placeholder="— Pilih Mobil —"
              />
            </div>

            <!-- Kategori Rental -->
            <div>
              <label class="block text-xs font-semibold text-slate-600 dark:text-slate-300 mb-1.5">
                Kategori {{ form.type === 'income' ? 'Pemasukan' : 'Pengeluaran' }} <span class="text-rose-500">*</span>
              </label>
              <SelectInput
                v-model="form.category_id"
                :options="categoryOptions"
                placeholder="— Pilih Kategori Rental —"
              />
            </div>

            <!-- Nominal -->
            <div>
              <label class="block text-xs font-semibold text-slate-600 dark:text-slate-300 mb-1.5">
                Nominal Transaksi (Rp) <span class="text-rose-500">*</span>
              </label>
              <CurrencyInput
                v-model="form.amount"
                placeholder="cth. 500.000"
                class="w-full"
              />
            </div>

            <!-- Tanggal & Rekening -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
              <div>
                <label class="block text-xs font-semibold text-slate-600 dark:text-slate-300 mb-1.5">
                  Tanggal <span class="text-rose-500">*</span>
                </label>
                <input
                  v-model="form.date"
                  type="date"
                  class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800/80 text-slate-900 dark:text-slate-100 text-sm focus:outline-none focus:ring-2 focus:ring-slate-400 dark:focus:ring-slate-500 focus:bg-white dark:focus:bg-slate-800 transition-all"
                  required
                />
              </div>

              <div>
                <label class="block text-xs font-semibold text-slate-600 dark:text-slate-300 mb-1.5">
                  Metode Pembayaran <span class="text-rose-500">*</span>
                </label>
                <SelectInput
                  v-model="form.account_id"
                  :options="accountOptions"
                  placeholder="— Pilih Kas / Akun —"
                />
              </div>
            </div>

            <!-- Catatan / Keterangan -->
            <div>
              <label class="block text-xs font-semibold text-slate-600 dark:text-slate-300 mb-1.5">
                {{ form.type === 'income' ? 'Nama Penyewa / Keterangan' : 'Keterangan Pengeluaran' }}
                <span class="text-slate-400 font-normal">(opsional)</span>
              </label>
              <input
                v-model="form.description"
                type="text"
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800/80 text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 text-sm focus:outline-none focus:ring-2 focus:ring-slate-400 dark:focus:ring-slate-500 focus:bg-white dark:focus:bg-slate-800 transition-all"
                :placeholder="form.type === 'income' ? 'cth. Sewa 2 hari Pak Budi ke luar kota' : 'cth. Bensin Pertamax 30 liter'"
              />
            </div>

            <div v-if="modalError" class="text-xs text-rose-600 dark:text-rose-400 bg-rose-50 dark:bg-rose-950/40 rounded-xl px-3.5 py-2.5 border border-rose-200/60 dark:border-rose-800/40">
              {{ modalError }}
            </div>

            <!-- Action Buttons: Neutral (No Purple!) -->
            <div class="flex gap-2.5 pt-2">
              <button
                type="button"
                @click="showModal = false"
                class="flex-1 py-2.5 px-4 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 text-sm font-semibold transition-all"
              >
                Batal
              </button>
              <button
                type="submit"
                :disabled="submitting"
                class="flex-1 py-2.5 px-4 rounded-xl bg-slate-900 hover:bg-slate-800 dark:bg-white dark:hover:bg-slate-100 text-white dark:text-slate-900 text-sm font-semibold transition-all shadow-sm disabled:opacity-60"
              >
                {{ submitting ? 'Menyimpan...' : 'Simpan Transaksi' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </Teleport>
  </div>
</template>
