<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useVehiclesStore, type Vehicle } from '@/stores/vehicles'
import {
  TruckIcon,
  PlusIcon,
  PencilIcon,
  TrashIcon,
  CheckCircleIcon,
} from '@heroicons/vue/24/outline'

const store = useVehiclesStore()

const showModal  = ref(false)
const editTarget = ref<Vehicle | null>(null)
const saving     = ref(false)
const deleting   = ref<number | null>(null)

const form = ref({
  name: '',
  plate_number: '',
  brand: '',
  model_year: '',
  status: 'available' as 'available' | 'rented' | 'maintenance',
  daily_rate: 0,
  color: '#3B82F6',
  notes: '',
})

const presetColors = [
  '#3B82F6', '#10B981', '#F59E0B', '#EF4444',
  '#8B5CF6', '#EC4899', '#06B6D4', '#F97316',
]

onMounted(() => store.fetchVehicles())

function openAdd() {
  editTarget.value = null
  form.value = { name: '', plate_number: '', brand: '', model_year: '', status: 'available', daily_rate: 0, color: '#3B82F6', notes: '' }
  showModal.value = true
}

function openEdit(v: Vehicle) {
  editTarget.value = v
  form.value = {
    name: v.name,
    plate_number: v.plate_number ?? '',
    brand: v.brand ?? '',
    model_year: v.model_year ?? '',
    status: v.status,
    daily_rate: v.daily_rate,
    color: v.color,
    notes: v.notes ?? '',
  }
  showModal.value = true
}

async function save() {
  if (!form.value.name.trim()) return
  saving.value = true
  try {
    if (editTarget.value) {
      await store.updateVehicle(editTarget.value.id, form.value)
    } else {
      await store.createVehicle(form.value)
    }
    showModal.value = false
  } finally {
    saving.value = false
  }
}

async function remove(id: number) {
  if (!confirm('Hapus kendaraan ini? Transaksi yang terkait tidak akan ikut terhapus.')) return
  deleting.value = id
  try {
    await store.deleteVehicle(id)
  } finally {
    deleting.value = null
  }
}

function statusLabel(s: string) {
  return s === 'available' ? 'Tersedia' : s === 'rented' ? 'Disewa' : 'Servis'
}
function statusColor(s: string) {
  return s === 'available'
    ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300'
    : s === 'rented'
    ? 'bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300'
    : 'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300'
}
function formatRp(n: number) {
  return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(n)
}
</script>

<template>
  <div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-2xl font-bold text-slate-800 dark:text-white flex items-center gap-2">
          <TruckIcon class="w-7 h-7 text-blue-600" />
          Armada Mobil
        </h1>
        <p class="text-sm text-slate-500 dark:text-slate-400 mt-0.5">{{ store.vehicles.length }} unit terdaftar</p>
      </div>
      <button
        @click="openAdd"
        class="flex items-center gap-1.5 px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold transition-colors shadow-sm"
      >
        <PlusIcon class="w-4 h-4" />
        Tambah Mobil
      </button>
    </div>

    <!-- Loading -->
    <div v-if="store.loading" class="text-center py-16 text-slate-400">Memuat data armada...</div>

    <!-- Empty -->
    <div v-else-if="store.vehicles.length === 0" class="text-center py-16 bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700">
      <TruckIcon class="w-14 h-14 text-slate-300 dark:text-slate-600 mx-auto mb-4" />
      <p class="text-slate-500 dark:text-slate-400 font-medium text-lg">Belum ada armada mobil</p>
      <p class="text-slate-400 dark:text-slate-500 text-sm mt-1">Tambahkan unit pertama untuk mulai mencatat keuangan per mobil</p>
      <button @click="openAdd" class="mt-5 px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-sm font-semibold transition-colors">
        + Tambah Mobil Sekarang
      </button>
    </div>

    <!-- Vehicle Cards Grid -->
    <div v-else class="grid grid-cols-1 sm:grid-cols-2 gap-4">
      <div
        v-for="v in store.vehicles"
        :key="v.id"
        class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-5 shadow-sm hover:shadow-md transition-shadow"
      >
        <!-- Card Header -->
        <div class="flex items-start justify-between mb-4">
          <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-xl flex items-center justify-center shrink-0" :style="{ backgroundColor: v.color + '22' }">
              <TruckIcon class="w-6 h-6" :style="{ color: v.color }" />
            </div>
            <div>
              <h3 class="font-bold text-slate-800 dark:text-white">{{ v.name }}</h3>
              <p class="text-xs text-slate-400">{{ v.plate_number ?? 'Plat belum diisi' }}</p>
            </div>
          </div>
          <span :class="['text-[11px] font-semibold px-2.5 py-1 rounded-full', statusColor(v.status)]">
            {{ statusLabel(v.status) }}
          </span>
        </div>

        <!-- Financials -->
        <div class="grid grid-cols-3 gap-2 mb-4 bg-slate-50 dark:bg-slate-700/40 rounded-xl p-3">
          <div class="text-center">
            <p class="text-[10px] text-slate-400 mb-1">Sewa Masuk</p>
            <p class="text-xs font-bold text-emerald-600">{{ formatRp(v.summary.income) }}</p>
          </div>
          <div class="text-center border-x border-slate-200 dark:border-slate-600">
            <p class="text-[10px] text-slate-400 mb-1">Biaya Keluar</p>
            <p class="text-xs font-bold text-red-500">{{ formatRp(v.summary.expense) }}</p>
          </div>
          <div class="text-center">
            <p class="text-[10px] text-slate-400 mb-1">Laba Bersih</p>
            <p :class="['text-xs font-bold', v.summary.profit >= 0 ? 'text-blue-600' : 'text-red-500']">
              {{ formatRp(v.summary.profit) }}
            </p>
          </div>
        </div>

        <!-- Daily Rate -->
        <p v-if="v.daily_rate > 0" class="text-xs text-slate-400 mb-3">
          Tarif/hari: <span class="font-semibold text-slate-600 dark:text-slate-300">{{ formatRp(v.daily_rate) }}</span>
        </p>

        <!-- Actions -->
        <div class="flex gap-2">
          <button
            @click="openEdit(v)"
            class="flex-1 flex items-center justify-center gap-1.5 py-2 rounded-lg border border-slate-200 dark:border-slate-600 text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 text-xs font-medium transition-colors"
          >
            <PencilIcon class="w-3.5 h-3.5" />
            Edit
          </button>
          <button
            @click="remove(v.id)"
            :disabled="deleting === v.id"
            class="flex items-center justify-center gap-1.5 px-3 py-2 rounded-lg border border-red-200 dark:border-red-900/50 text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 text-xs font-medium transition-colors disabled:opacity-50"
          >
            <TrashIcon class="w-3.5 h-3.5" />
          </button>
        </div>
      </div>
    </div>

    <!-- Modal Add/Edit Vehicle -->
    <Teleport to="body">
      <Transition name="fade">
        <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm">
          <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-2xl w-full max-w-md max-h-[90vh] overflow-y-auto">
            <div class="sticky top-0 bg-white dark:bg-slate-800 px-6 py-4 border-b border-slate-100 dark:border-slate-700 flex items-center justify-between z-10">
              <h2 class="text-lg font-bold text-slate-800 dark:text-white">
                {{ editTarget ? 'Edit Kendaraan' : 'Tambah Kendaraan' }}
              </h2>
              <button @click="showModal = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 text-2xl leading-none">&times;</button>
            </div>

            <div class="p-6 space-y-4">
              <!-- Name -->
              <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">Nama Kendaraan *</label>
                <input v-model="form.name" placeholder="cth. Toyota Avanza 2022" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/50" />
              </div>

              <!-- Plate -->
              <div class="grid grid-cols-2 gap-3">
                <div>
                  <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">Plat Nomor</label>
                  <input v-model="form.plate_number" placeholder="B 1234 XYZ" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/50" />
                </div>
                <div>
                  <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">Tahun</label>
                  <input v-model="form.model_year" placeholder="2022" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/50" />
                </div>
              </div>

              <!-- Status -->
              <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">Status</label>
                <select v-model="form.status" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/50">
                  <option value="available">🟢 Tersedia</option>
                  <option value="rented">🔵 Sedang Disewa</option>
                  <option value="maintenance">🔧 Di Bengkel / Servis</option>
                </select>
              </div>

              <!-- Daily Rate -->
              <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">Tarif Sewa / Hari (Rp)</label>
                <input v-model.number="form.daily_rate" type="number" min="0" placeholder="350000" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/50" />
              </div>

              <!-- Color -->
              <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Warna Kartu</label>
                <div class="flex gap-2 flex-wrap">
                  <button
                    v-for="c in presetColors"
                    :key="c"
                    @click="form.color = c"
                    :style="{ backgroundColor: c }"
                    :class="['w-8 h-8 rounded-full transition-transform hover:scale-110 border-2', form.color === c ? 'border-slate-800 dark:border-white scale-110' : 'border-transparent']"
                  >
                    <CheckCircleIcon v-if="form.color === c" class="w-4 h-4 text-white mx-auto" />
                  </button>
                </div>
              </div>

              <!-- Notes -->
              <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">Catatan (Opsional)</label>
                <textarea v-model="form.notes" rows="2" placeholder="cth. Ban baru ganti Juni 2026..." class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/50 resize-none"></textarea>
              </div>
            </div>

            <div class="px-6 py-4 border-t border-slate-100 dark:border-slate-700 flex gap-3">
              <button @click="showModal = false" class="flex-1 py-2.5 rounded-xl border border-slate-200 dark:border-slate-600 text-slate-600 dark:text-slate-300 text-sm font-medium hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">Batal</button>
              <button
                @click="save"
                :disabled="saving || !form.name.trim()"
                class="flex-1 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 disabled:opacity-50 text-white text-sm font-semibold transition-colors"
              >
                {{ saving ? 'Menyimpan...' : editTarget ? 'Simpan Perubahan' : 'Tambah Kendaraan' }}
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
