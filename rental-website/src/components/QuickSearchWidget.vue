<script setup lang="ts">
import { ref } from 'vue'

const emit = defineEmits<{
  (e: 'search', params: { vehicleType: string; startDate: string; endDate: string; passengers: number }): void
}>()

const vehicleType = ref('all')
const startDate = ref('')
const endDate = ref('')
const passengers = ref(1)

const vehicleOptions = [
  { value: 'all', label: 'Semua Jenis' },
  { value: 'city-car', label: 'City Car (Agya / 4-5 Seat)' },
  { value: 'mpv', label: 'MPV Keluarga (Avanza, Veloz / 7 Seat)' },
  { value: 'hiace', label: 'HiAce Minibus (11-15 Seat)' },
]

const passengerOptions = [
  { value: 1, label: '1 - 2 Orang' },
  { value: 4, label: '3 - 4 Orang' },
  { value: 7, label: '5 - 7 Orang' },
  { value: 15, label: '8 - 15 Orang (Rombongan)' },
]

// Default to today and tomorrow
const todayStr = new Date().toISOString().split('T')[0]

function handleSearch() {
  emit('search', {
    vehicleType: vehicleType.value,
    startDate: startDate.value,
    endDate: endDate.value,
    passengers: passengers.value
  })

  // Scroll smoothly to armada catalog
  const armadaEl = document.getElementById('armada')
  if (armadaEl) {
    armadaEl.scrollIntoView({ behavior: 'smooth' })
  }
}
</script>

<template>
  <div class="relative z-20 max-w-5xl mx-auto px-4 sm:px-6">
    <!-- Elevated Card Container -->
    <div class="bg-white rounded-2xl sm:rounded-3xl shadow-xl shadow-slate-900/10 border border-slate-100 p-3.5 sm:p-4 md:p-5">
      
      <!-- DESKTOP / TABLET: 1 Horizontal Row with separators (>= sm) -->
      <div class="hidden sm:grid sm:grid-cols-12 gap-3 items-center divide-x divide-slate-100">
        
        <!-- 1. Pilih Armada (Cols 3) -->
        <div class="col-span-3 pr-2">
          <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1 flex items-center gap-1.5">
            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
            </svg>
            Pilih Armada
          </label>
          <select
            v-model="vehicleType"
            class="w-full text-xs md:text-sm font-bold text-slate-800 bg-transparent border-0 p-0 focus:ring-0 cursor-pointer"
          >
            <option v-for="opt in vehicleOptions" :key="opt.value" :value="opt.value">
              {{ opt.label }}
            </option>
          </select>
        </div>

        <!-- 2. Tanggal Mulai (Cols 2) -->
        <div class="col-span-2 px-3">
          <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1 flex items-center gap-1.5">
            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            Tgl Mulai
          </label>
          <input
            v-model="startDate"
            type="date"
            :min="todayStr"
            class="w-full text-xs md:text-sm font-semibold text-slate-800 bg-transparent border-0 p-0 focus:ring-0 cursor-pointer"
          />
        </div>

        <!-- 3. Tanggal Selesai (Cols 2) -->
        <div class="col-span-2 px-3">
          <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1 flex items-center gap-1.5">
            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            Tgl Selesai
          </label>
          <input
            v-model="endDate"
            type="date"
            :min="startDate || todayStr"
            class="w-full text-xs md:text-sm font-semibold text-slate-800 bg-transparent border-0 p-0 focus:ring-0 cursor-pointer"
          />
        </div>

        <!-- 4. Jumlah Penumpang (Cols 2) -->
        <div class="col-span-2 px-3">
          <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1 flex items-center gap-1.5">
            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
            </svg>
            Penumpang
          </label>
          <select
            v-model="passengers"
            class="w-full text-xs md:text-sm font-bold text-slate-800 bg-transparent border-0 p-0 focus:ring-0 cursor-pointer"
          >
            <option v-for="opt in passengerOptions" :key="opt.value" :value="opt.value">
              {{ opt.label }}
            </option>
          </select>
        </div>

        <!-- 5. Tombol Cari Armada (Cols 3) -->
        <div class="col-span-3 pl-3">
          <button
            @click="handleSearch"
            type="button"
            class="w-full h-12 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-sm inline-flex items-center justify-center gap-2 shadow-md transition-all active:scale-95 cursor-pointer"
          >
            <span>Cari Armada</span>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
          </button>
        </div>
      </div>

      <!-- MOBILE VIEW (< sm): Clean vertical form list like mockup -->
      <div class="sm:hidden space-y-2.5">
        
        <!-- Field 1: Pilih Armada -->
        <div class="flex items-center justify-between p-3 rounded-xl border border-slate-200/80 bg-slate-50/50">
          <div class="flex items-center gap-2.5 min-w-0 flex-1">
            <svg class="w-5 h-5 text-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
            </svg>
            <div class="min-w-0 flex-1">
              <span class="block text-[10px] font-bold uppercase tracking-wider text-slate-400">Pilih Armada</span>
              <select
                v-model="vehicleType"
                class="w-full text-xs font-bold text-slate-800 bg-transparent border-0 p-0 focus:ring-0"
              >
                <option v-for="opt in vehicleOptions" :key="opt.value" :value="opt.value">
                  {{ opt.label }}
                </option>
              </select>
            </div>
          </div>
          <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
          </svg>
        </div>

        <!-- Field 2: Tanggal Mulai -->
        <div class="flex items-center justify-between p-3 rounded-xl border border-slate-200/80 bg-slate-50/50">
          <div class="flex items-center gap-2.5 min-w-0 flex-1">
            <svg class="w-5 h-5 text-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            <div class="min-w-0 flex-1">
              <span class="block text-[10px] font-bold uppercase tracking-wider text-slate-400">Tanggal Mulai</span>
              <input
                v-model="startDate"
                type="date"
                :min="todayStr"
                class="w-full text-xs font-semibold text-slate-800 bg-transparent border-0 p-0 focus:ring-0"
              />
            </div>
          </div>
          <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
          </svg>
        </div>

        <!-- Field 3: Tanggal Selesai -->
        <div class="flex items-center justify-between p-3 rounded-xl border border-slate-200/80 bg-slate-50/50">
          <div class="flex items-center gap-2.5 min-w-0 flex-1">
            <svg class="w-5 h-5 text-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            <div class="min-w-0 flex-1">
              <span class="block text-[10px] font-bold uppercase tracking-wider text-slate-400">Tanggal Selesai</span>
              <input
                v-model="endDate"
                type="date"
                :min="startDate || todayStr"
                class="w-full text-xs font-semibold text-slate-800 bg-transparent border-0 p-0 focus:ring-0"
              />
            </div>
          </div>
          <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
          </svg>
        </div>

        <!-- Field 4: Jumlah Penumpang -->
        <div class="flex items-center justify-between p-3 rounded-xl border border-slate-200/80 bg-slate-50/50">
          <div class="flex items-center gap-2.5 min-w-0 flex-1">
            <svg class="w-5 h-5 text-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
            </svg>
            <div class="min-w-0 flex-1">
              <span class="block text-[10px] font-bold uppercase tracking-wider text-slate-400">Jumlah Penumpang</span>
              <select
                v-model="passengers"
                class="w-full text-xs font-bold text-slate-800 bg-transparent border-0 p-0 focus:ring-0"
              >
                <option v-for="opt in passengerOptions" :key="opt.value" :value="opt.value">
                  {{ opt.label }}
                </option>
              </select>
            </div>
          </div>
          <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
          </svg>
        </div>

        <!-- Mobile Submit Button -->
        <button
          @click="handleSearch"
          type="button"
          class="w-full py-3.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-sm inline-flex items-center justify-center gap-2 shadow-md transition-all active:scale-95 cursor-pointer"
        >
          <span>Cari Armada</span>
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
          </svg>
        </button>
      </div>

    </div>
  </div>
</template>
