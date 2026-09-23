<script setup lang="ts">
import { ref, computed, onMounted, onBeforeUnmount, nextTick } from 'vue'
import { type Vehicle } from '@/stores/vehicles'
import { useFormatCurrency } from '@/composables/useFormatCurrency'
import {
  ChevronUpDownIcon,
  CheckIcon,
  MagnifyingGlassIcon,
  TruckIcon
} from '@heroicons/vue/24/outline'

const props = withDefaults(
  defineProps<{
    modelValue: string | number
    vehicles: Vehicle[]
    placeholder?: string
    disabled?: boolean
  }>(),
  {
    placeholder: 'Pilih Armada Mobil...',
    disabled: false
  }
)

const emit = defineEmits<{
  (e: 'update:modelValue', val: string | number): void
  (e: 'change', vehicle: Vehicle): void
}>()

const { formatCurrency } = useFormatCurrency()

const isOpen = ref(false)
const searchQuery = ref('')
const containerRef = ref<HTMLElement | null>(null)
const searchInputRef = ref<HTMLInputElement | null>(null)

const selectedVehicle = computed(() =>
  props.vehicles.find((v) => String(v.id) === String(props.modelValue))
)

const filteredVehicles = computed(() => {
  if (!searchQuery.value.trim()) return props.vehicles
  const q = searchQuery.value.toLowerCase().trim()
  return props.vehicles.filter(
    (v) =>
      v.name.toLowerCase().includes(q) ||
      (v.plate_number && v.plate_number.toLowerCase().includes(q)) ||
      (v.brand && v.brand.toLowerCase().includes(q))
  )
})

function toggleOpen() {
  if (props.disabled) return
  isOpen.value = !isOpen.value
  if (isOpen.value) {
    searchQuery.value = ''
    nextTick(() => {
      searchInputRef.value?.focus()
    })
  }
}

function selectVehicle(v: Vehicle) {
  emit('update:modelValue', v.id)
  emit('change', v)
  isOpen.value = false
}

function onOutsideClick(e: MouseEvent) {
  if (containerRef.value && !containerRef.value.contains(e.target as Node)) {
    isOpen.value = false
  }
}

onMounted(() => {
  document.addEventListener('mousedown', onOutsideClick)
})

onBeforeUnmount(() => {
  document.removeEventListener('mousedown', onOutsideClick)
})
</script>

<template>
  <div ref="containerRef" class="relative w-full">
    <!-- Trigger Button (Tampilan Kartu Armada Terpilih) -->
    <button
      type="button"
      @click="toggleOpen"
      :disabled="disabled"
      class="w-full flex items-center justify-between gap-3 p-2.5 sm:p-3 rounded-2xl bg-white dark:bg-slate-900 border transition-all text-left group shadow-2xs hover:shadow-xs focus:outline-none"
      :class="[
        isOpen
          ? 'ring-2 ring-primary-500/30 border-primary-500 bg-primary-50/20 dark:bg-primary-950/20'
          : 'border-slate-200 dark:border-slate-700 hover:border-primary-400 dark:hover:border-primary-600',
        disabled ? 'opacity-60 cursor-not-allowed' : 'cursor-pointer'
      ]"
    >
      <!-- Selected State -->
      <div v-if="selectedVehicle" class="flex items-center gap-3 min-w-0 flex-1">
        <!-- Thumbnail Foto Mobil -->
        <div class="w-12 h-10 rounded-xl overflow-hidden bg-slate-100 dark:bg-slate-800 shrink-0 border border-slate-200/80 dark:border-slate-700/80 flex items-center justify-center">
          <img
            v-if="selectedVehicle.photo_url"
            :src="selectedVehicle.photo_url"
            :alt="selectedVehicle.name"
            class="w-full h-full object-cover"
          />
          <TruckIcon v-else class="w-5 h-5 text-slate-400" />
        </div>

        <!-- Detail Armada -->
        <div class="min-w-0 flex-1">
          <div class="flex items-center gap-1.5 flex-wrap">
            <span class="text-xs sm:text-sm font-bold text-slate-900 dark:text-white truncate">
              {{ selectedVehicle.name }}
            </span>
            <span
              v-if="selectedVehicle.plate_number"
              class="px-1.5 py-0.5 rounded text-[10px] font-semibold bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 border border-slate-200/70 dark:border-slate-700/70"
            >
              {{ selectedVehicle.plate_number }}
            </span>
          </div>

          <div class="flex items-center gap-2 text-[11px] text-slate-500 dark:text-slate-400 mt-0.5 flex-wrap">
            <span class="font-bold text-emerald-600 dark:text-emerald-400">
              {{ formatCurrency(selectedVehicle.daily_rate) }}/hari
            </span>
            <span class="opacity-50">&bull;</span>
            <span>{{ selectedVehicle.capacity || 7 }} Kursi</span>
            <span class="opacity-50">&bull;</span>
            <span class="inline-flex items-center gap-1 font-medium">
              <span
                class="w-1.5 h-1.5 rounded-full"
                :class="selectedVehicle.status === 'available' ? 'bg-emerald-500' : (selectedVehicle.status === 'rented' ? 'bg-blue-500' : 'bg-amber-500')"
              />
              <span :class="selectedVehicle.status === 'available' ? 'text-emerald-700 dark:text-emerald-400' : (selectedVehicle.status === 'rented' ? 'text-blue-700 dark:text-blue-400' : 'text-amber-700 dark:text-amber-400')">
                {{ selectedVehicle.status === 'available' ? 'Siap Disewa' : (selectedVehicle.status === 'rented' ? 'Sedang Disewa' : 'Di Bengkel') }}
              </span>
            </span>
          </div>
        </div>
      </div>

      <!-- Placeholder State -->
      <div v-else class="flex items-center gap-3 text-slate-400 text-xs sm:text-sm flex-1">
        <div class="w-10 h-10 rounded-xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center shrink-0">
          <TruckIcon class="w-5 h-5 text-slate-400" />
        </div>
        <span>{{ placeholder }}</span>
      </div>

      <!-- Tombol Aksi / Indikator Ganti Unit -->
      <div class="flex items-center gap-1.5 shrink-0 pl-1">
        <span class="text-[11px] font-semibold text-primary-600 dark:text-primary-400 hidden xs:inline sm:inline group-hover:underline">
          Ganti
        </span>
        <div class="w-7 h-7 rounded-lg bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-500 group-hover:text-primary-600 dark:group-hover:text-primary-400 transition-colors">
          <ChevronUpDownIcon
            class="w-4 h-4 transition-transform duration-200"
            :class="{ 'rotate-180 text-primary-600': isOpen }"
          />
        </div>
      </div>
    </button>

    <!-- Custom Dropdown Popover Panel -->
    <Transition
      enter-active-class="transition duration-150 ease-out"
      enter-from-class="transform -translate-y-2 opacity-0 scale-[0.98]"
      enter-to-class="transform translate-y-0 opacity-100 scale-100"
      leave-active-class="transition duration-100 ease-in"
      leave-from-class="transform translate-y-0 opacity-100 scale-100"
      leave-to-class="transform -translate-y-2 opacity-0 scale-[0.98]"
    >
      <div
        v-if="isOpen"
        class="absolute left-0 right-0 top-full mt-2 z-[60] bg-white dark:bg-slate-850 border border-slate-200 dark:border-slate-700 rounded-2xl shadow-2xl overflow-hidden ring-1 ring-black/5 dark:ring-white/10 flex flex-col max-h-[300px]"
      >
        <!-- Search Bar -->
        <div class="p-2.5 border-b border-slate-100 dark:border-slate-800 bg-slate-50/90 dark:bg-slate-900/80 sticky top-0 z-10 backdrop-blur-xs">
          <div class="relative">
            <MagnifyingGlassIcon class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none" />
            <input
              ref="searchInputRef"
              v-model="searchQuery"
              type="text"
              placeholder="Cari nama mobil, plat, atau merk..."
              class="w-full pl-9 pr-7 py-1.5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-primary-500 transition"
            />
            <button
              v-if="searchQuery"
              type="button"
              @click="searchQuery = ''"
              class="absolute right-2.5 top-1/2 -translate-y-1/2 w-4 h-4 rounded-full bg-slate-200 dark:bg-slate-700 text-slate-500 hover:text-slate-700 flex items-center justify-center text-[10px] leading-none"
            >
              &times;
            </button>
          </div>
        </div>

        <!-- List Armada Mobil -->
        <div class="overflow-y-auto p-1.5 space-y-1 flex-1 divide-y divide-slate-100/50 dark:divide-slate-800/50">
          <div
            v-for="v in filteredVehicles"
            :key="v.id"
            @click="selectVehicle(v)"
            class="flex items-center justify-between gap-2.5 p-2 rounded-xl cursor-pointer transition-all border"
            :class="[
              String(v.id) === String(modelValue)
                ? 'bg-primary-50/80 dark:bg-primary-950/40 border-primary-200 dark:border-primary-800/80 shadow-2xs'
                : 'bg-white dark:bg-slate-850 border-transparent hover:bg-slate-50 dark:hover:bg-slate-800/80 hover:border-slate-200/60 dark:hover:border-slate-700/60'
            ]"
          >
            <!-- Left Info -->
            <div class="flex items-center gap-2.5 min-w-0 flex-1">
              <!-- Thumbnail -->
              <div class="w-11 h-9 rounded-lg overflow-hidden bg-slate-100 dark:bg-slate-800 shrink-0 border border-slate-200/80 dark:border-slate-700/80 flex items-center justify-center">
                <img
                  v-if="v.photo_url"
                  :src="v.photo_url"
                  :alt="v.name"
                  class="w-full h-full object-cover"
                />
                <TruckIcon v-else class="w-4 h-4 text-slate-400" />
              </div>

              <!-- Title & Meta -->
              <div class="min-w-0 flex-1">
                <div class="flex items-center gap-1.5 flex-wrap">
                  <span
                    class="text-xs font-bold truncate"
                    :class="String(v.id) === String(modelValue) ? 'text-primary-900 dark:text-primary-200 font-extrabold' : 'text-slate-900 dark:text-white'"
                  >
                    {{ v.name }}
                  </span>
                  <span
                    v-if="v.plate_number"
                    class="px-1.5 py-0.5 rounded text-[10px] font-semibold bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 border border-slate-200/70 dark:border-slate-700/70"
                  >
                    {{ v.plate_number }}
                  </span>
                </div>

                <div class="flex items-center gap-2 text-[10px] text-slate-400 mt-0.5 flex-wrap">
                  <span>{{ v.capacity || 7 }} Kursi</span>
                  <span class="opacity-50">&bull;</span>
                  <span class="inline-flex items-center gap-1 font-medium">
                    <span
                      class="w-1.5 h-1.5 rounded-full"
                      :class="v.status === 'available' ? 'bg-emerald-500' : (v.status === 'rented' ? 'bg-blue-500' : 'bg-amber-500')"
                    />
                    <span :class="v.status === 'available' ? 'text-emerald-600 dark:text-emerald-400' : (v.status === 'rented' ? 'text-blue-600 dark:text-blue-400' : 'text-amber-600 dark:text-amber-400')">
                      {{ v.status === 'available' ? 'Siap Disewa' : (v.status === 'rented' ? 'Sedang Disewa' : 'Di Bengkel') }}
                    </span>
                  </span>
                </div>
              </div>
            </div>

            <!-- Right: Tarif & Checkmark -->
            <div class="flex items-center gap-2 shrink-0 pl-1">
              <div class="text-right">
                <span class="text-xs font-bold text-emerald-600 dark:text-emerald-400 block tabular-nums">
                  {{ formatCurrency(v.daily_rate) }}
                </span>
                <span class="text-[9px] text-slate-400 block text-right">/hari</span>
              </div>
              <div class="w-5 h-5 flex items-center justify-center">
                <CheckIcon
                  v-if="String(v.id) === String(modelValue)"
                  class="w-4 h-4 text-primary-600 dark:text-primary-400 stroke-2"
                />
              </div>
            </div>
          </div>

          <!-- Empty State Pencarian -->
          <div v-if="filteredVehicles.length === 0" class="py-8 px-4 text-center">
            <TruckIcon class="w-8 h-8 text-slate-300 dark:text-slate-600 mx-auto mb-1.5" />
            <p class="text-xs font-medium text-slate-600 dark:text-slate-300">Tidak ada armada yang cocok</p>
            <p class="text-[10px] text-slate-400 mt-0.5">Coba kata kunci pencarian yang lain.</p>
          </div>
        </div>
      </div>
    </Transition>
  </div>
</template>
