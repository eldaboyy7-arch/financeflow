<script setup lang="ts">
import { ref, onMounted, computed } from 'vue'
import { useVehiclesStore } from '@/stores/vehicles'
import api from '@/api/axios'
import {
  ArrowsRightLeftIcon,
  PlusIcon,
  TruckIcon,
} from '@heroicons/vue/24/outline'

const vehiclesStore = useVehiclesStore()

// ── Form state ─────────────────────────────────────────────
const showModal = ref(false)
const saving    = ref(false)
const errorMsg  = ref('')

const form = ref({
  type: 'income' as 'income' | 'expense',
  vehicle_id: '' as string | number,
  amount: '' as string | number,
  date: new Date().toISOString().slice(0, 10),
  description: '',
  account_id: '' as string | number,
  category_id: '' as string | number,
})

// ── Recent transactions for this rental context ─────────────
const transactions = ref<any[]>([])
const loadingTx    = ref(false)

// ── Accounts & Categories from API ─────────────────────────
const accounts   = ref<any[]>([])
const categories = ref<any[]>([])

onMounted(async () => {
  vehiclesStore.fetchVehicles()
  const [accs, cats] = await Promise.all([
    api.get('/accounts'),
    api.get('/categories'),
  ])
  accounts.value   = accs.data.data ?? accs.data
  categories.value = cats.data.data ?? cats.data

  // Set defaults
  if (accounts.value.length)   form.value.account_id   = accounts.value[0].id
  if (vehiclesStore.vehicles.length) form.value.vehicle_id = vehiclesStore.vehicles[0].id
  fetchTx()
})

const filteredCategories = computed(() =>
  categories.value.filter((c: any) => c.type === form.value.type)
)

async function fetchTx() {
  loadingTx.value = true
  try {
    const { data } = await api.get('/transactions', { params: { per_page: 20 } })
    transactions.value = data.data ?? []
  } finally {
    loadingTx.value = false
  }
}

function openModal(type: 'income' | 'expense') {
  form.value.type = type
  form.value.amount = ''
  form.value.description = ''
  form.value.date = new Date().toISOString().slice(0, 10)
  if (filteredCategories.value.length) form.value.category_id = filteredCategories.value[0].id
  errorMsg.value = ''
  showModal.value = true
}

async function save() {
  if (!form.value.amount || !form.value.vehicle_id || !form.value.account_id || !form.value.category_id) {
    errorMsg.value = 'Mohon lengkapi semua kolom wajib.'
    return
  }
  saving.value = true
  errorMsg.value = ''
  try {
    await api.post('/transactions', {
      type:        form.value.type,
      vehicle_id:  form.value.vehicle_id || null,
      amount:      parseFloat(String(form.value.amount)),
      date:        form.value.date,
      description: form.value.description || (form.value.type === 'income' ? 'Sewa Mobil' : 'Biaya Operasional'),
      account_id:  form.value.account_id,
      category_id: form.value.category_id,
    })
    showModal.value = false
    vehiclesStore.fetchVehicles()
    fetchTx()
  } catch (e: any) {
    errorMsg.value = e?.response?.data?.message ?? 'Gagal menyimpan transaksi.'
  } finally {
    saving.value = false
  }
}

function formatRp(n: number) {
  return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(n)
}
</script>

<template>
  <div class="max-w-3xl mx-auto space-y-6">
    <!-- Header -->
    <div>
      <h1 class="text-2xl font-bold text-slate-800 dark:text-white flex items-center gap-2">
        <ArrowsRightLeftIcon class="w-7 h-7 text-blue-600" />
        Catat Transaksi Rental
      </h1>
      <p class="text-sm text-slate-500 dark:text-slate-400 mt-0.5">Catat uang sewa masuk atau biaya operasional per kendaraan</p>
    </div>

    <!-- Big Action Buttons -->
    <div class="grid grid-cols-2 gap-4">
      <button
        @click="openModal('income')"
        class="flex flex-col items-center gap-3 p-6 rounded-2xl bg-emerald-500 hover:bg-emerald-600 text-white shadow-lg shadow-emerald-500/30 transition-all hover:scale-[1.02] active:scale-95"
      >
        <div class="w-12 h-12 rounded-full bg-white/20 flex items-center justify-center">
          <PlusIcon class="w-7 h-7" />
        </div>
        <div class="text-center">
          <p class="text-base font-bold">+ Uang Sewa Masuk</p>
          <p class="text-emerald-100 text-xs mt-0.5">Catat pendapatan dari sewa</p>
        </div>
      </button>

      <button
        @click="openModal('expense')"
        class="flex flex-col items-center gap-3 p-6 rounded-2xl bg-red-500 hover:bg-red-600 text-white shadow-lg shadow-red-500/30 transition-all hover:scale-[1.02] active:scale-95"
      >
        <div class="w-12 h-12 rounded-full bg-white/20 flex items-center justify-center">
          <PlusIcon class="w-7 h-7" />
        </div>
        <div class="text-center">
          <p class="text-base font-bold">+ Biaya Operasional</p>
          <p class="text-red-100 text-xs mt-0.5">Servis, bensin, oli, dll</p>
        </div>
      </button>
    </div>

    <!-- Recent Transactions -->
    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
      <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-700">
        <h2 class="font-semibold text-slate-700 dark:text-slate-200">Transaksi Terbaru</h2>
      </div>
      <div v-if="loadingTx" class="p-8 text-center text-slate-400">Memuat...</div>
      <div v-else-if="transactions.length === 0" class="p-8 text-center text-slate-400">Belum ada transaksi</div>
      <div v-else>
        <div
          v-for="tx in transactions"
          :key="tx.id"
          class="flex items-center gap-3 px-5 py-3.5 border-b border-slate-50 dark:border-slate-700/50 last:border-0"
        >
          <div :class="['w-9 h-9 rounded-xl flex items-center justify-center shrink-0', tx.type === 'income' ? 'bg-emerald-100 dark:bg-emerald-900/40' : 'bg-red-100 dark:bg-red-900/40']">
            <ArrowsRightLeftIcon :class="['w-4 h-4', tx.type === 'income' ? 'text-emerald-600' : 'text-red-500']" />
          </div>
          <div class="flex-1 min-w-0">
            <p class="text-sm font-medium text-slate-800 dark:text-white truncate">{{ tx.description || '-' }}</p>
            <div class="flex items-center gap-2 mt-0.5">
              <p class="text-xs text-slate-400">{{ tx.date }}</p>
              <span v-if="tx.vehicle" class="flex items-center gap-1 text-xs text-slate-400">
                · <TruckIcon class="w-3 h-3" /> {{ tx.vehicle.name }}
              </span>
            </div>
          </div>
          <p :class="['text-sm font-bold shrink-0', tx.type === 'income' ? 'text-emerald-600' : 'text-red-500']">
            {{ tx.type === 'income' ? '+' : '-' }}{{ formatRp(tx.amount) }}
          </p>
        </div>
      </div>
    </div>

    <!-- Modal -->
    <Teleport to="body">
      <Transition name="fade">
        <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm">
          <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-2xl w-full max-w-md">
            <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-700 flex items-center justify-between">
              <h2 class="text-lg font-bold" :class="form.type === 'income' ? 'text-emerald-600' : 'text-red-500'">
                {{ form.type === 'income' ? '+ Uang Sewa Masuk' : '+ Biaya Operasional' }}
              </h2>
              <button @click="showModal = false" class="text-slate-400 hover:text-slate-600 text-2xl leading-none">&times;</button>
            </div>

            <div class="p-6 space-y-4">
              <!-- Error -->
              <div v-if="errorMsg" class="text-sm text-red-600 bg-red-50 dark:bg-red-900/20 rounded-xl px-4 py-3">{{ errorMsg }}</div>

              <!-- Pilih Mobil -->
              <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">Pilih Mobil *</label>
                <select v-model="form.vehicle_id" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/50">
                  <option value="">-- Pilih Kendaraan --</option>
                  <option v-for="v in vehiclesStore.vehicles" :key="v.id" :value="v.id">
                    {{ v.name }} {{ v.plate_number ? `(${v.plate_number})` : '' }}
                  </option>
                </select>
              </div>

              <!-- Nominal -->
              <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">Nominal (Rp) *</label>
                <input v-model.number="form.amount" type="number" min="1" placeholder="cth. 1500000" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/50" />
              </div>

              <!-- Tanggal -->
              <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">Tanggal *</label>
                <input v-model="form.date" type="date" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/50" />
              </div>

              <!-- Rekening -->
              <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">Rekening *</label>
                <select v-model="form.account_id" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/50">
                  <option value="">-- Pilih Rekening --</option>
                  <option v-for="a in accounts" :key="a.id" :value="a.id">{{ a.name }}</option>
                </select>
              </div>

              <!-- Kategori -->
              <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">Kategori *</label>
                <select v-model="form.category_id" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/50">
                  <option value="">-- Pilih Kategori --</option>
                  <option v-for="c in filteredCategories" :key="c.id" :value="c.id">{{ c.name }}</option>
                </select>
              </div>

              <!-- Catatan -->
              <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">
                  {{ form.type === 'income' ? 'Nama Penyewa / Catatan' : 'Keterangan Biaya' }}
                </label>
                <input v-model="form.description" :placeholder="form.type === 'income' ? 'cth. Sewa 3 hari – Pak Budi' : 'cth. Ganti oli + filter'" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/50" />
              </div>
            </div>

            <div class="px-6 py-4 border-t border-slate-100 dark:border-slate-700 flex gap-3">
              <button @click="showModal = false" class="flex-1 py-2.5 rounded-xl border border-slate-200 dark:border-slate-600 text-slate-600 dark:text-slate-300 text-sm font-medium hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">Batal</button>
              <button
                @click="save"
                :disabled="saving"
                :class="['flex-1 py-2.5 rounded-xl text-white text-sm font-semibold transition-colors disabled:opacity-50', form.type === 'income' ? 'bg-emerald-500 hover:bg-emerald-600' : 'bg-red-500 hover:bg-red-600']"
              >
                {{ saving ? 'Menyimpan...' : 'Simpan Transaksi' }}
              </button>
            </div>
          </div>
        </div>
      </Transition>
    </Teleport>
  </div>
</template>

<style scoped>
.fade-enter-active, .fade-leave-active { transition: opacity 0.2s; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
</style>
