<script setup lang="ts">
import { ref, onMounted, onUnmounted } from 'vue'
import { useCurrency, type CurrencyCode, CURRENCIES } from '@/composables/useCurrency'

const { currentCurrency, setCurrency } = useCurrency()

const isOpen = ref(false)
const dropdownRef = ref<HTMLElement | null>(null)

const toggleDropdown = () => {
  isOpen.value = !isOpen.value
}

const closeDropdown = () => {
  isOpen.value = false
}

const handleSelect = (code: CurrencyCode) => {
  setCurrency(code)
  closeDropdown()
}

// Click outside listener
const handleClickOutside = (event: MouseEvent) => {
  if (dropdownRef.value && !dropdownRef.value.contains(event.target as Node)) {
    closeDropdown()
  }
}

// Escape key listener
const handleKeydown = (event: KeyboardEvent) => {
  if (event.key === 'Escape') {
    closeDropdown()
  }
}

onMounted(() => {
  document.addEventListener('click', handleClickOutside)
  document.addEventListener('keydown', handleKeydown)
})

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside)
  document.removeEventListener('keydown', handleKeydown)
})
</script>

<template>
  <div ref="dropdownRef" class="relative inline-block text-left">
    <!-- Trigger Button -->
    <button
      @click.stop="toggleDropdown"
      type="button"
      class="inline-flex items-center gap-1.5 sm:gap-2 px-2.5 py-1.5 sm:px-3 sm:py-1.5 rounded-xl bg-slate-800/80 hover:bg-slate-800 text-slate-100 hover:text-white border border-slate-700/70 hover:border-slate-600 transition-all shadow-xs active:scale-95 text-xs font-semibold focus:outline-none"
      :aria-expanded="isOpen"
      aria-label="Pilih Mata Uang / Currency"
    >
      <!-- Circular Flag Avatar -->
      <span class="w-5 h-5 rounded-full overflow-hidden shrink-0 shadow-xs border border-white/20 flex items-center justify-center">
        <!-- Indonesia Flag -->
        <svg v-if="currentCurrency === 'IDR'" viewBox="0 0 512 512" class="w-full h-full">
          <circle cx="256" cy="256" r="256" fill="#f0f0f0" />
          <path d="M0 256C0 114.6 114.6 0 256 0s256 114.6 256 256H0z" fill="#e11d48" />
        </svg>

        <!-- Singapore Flag -->
        <svg v-else-if="currentCurrency === 'SGD'" viewBox="0 0 512 512" class="w-full h-full">
          <circle cx="256" cy="256" r="256" fill="#f0f0f0" />
          <path d="M0 256C0 114.6 114.6 0 256 0s256 114.6 256 256H0z" fill="#ef4444" />
          <!-- Stylized Crescent & Stars in top left quadrant -->
          <path d="M120 70 A60 60 0 1 0 210 170 A55 55 0 1 1 120 70 Z" fill="#ffffff" />
          <polygon points="190,95 194,105 205,105 196,112 199,122 190,116 181,122 184,112 175,105 186,105" fill="#ffffff" />
        </svg>

        <!-- Malaysia Flag -->
        <svg v-else-if="currentCurrency === 'MYR'" viewBox="0 0 512 512" class="w-full h-full">
          <circle cx="256" cy="256" r="256" fill="#f0f0f0" />
          <!-- Red stripes -->
          <path d="M0 36h512v36H0zm0 72h512v36H0zm0 72h512v36H0zm0 72h512v36H0zm0 72h512v36H0zm0 72h512v36H0z" fill="#dc2626" />
          <!-- Blue canton -->
          <path d="M0 256C0 114.6 114.6 0 256 0v256H0z" fill="#1e3a8a" />
          <!-- Yellow Crescent & 14-pointed Star -->
          <circle cx="120" cy="128" r="58" fill="#facc15" />
          <circle cx="140" cy="128" r="48" fill="#1e3a8a" />
          <circle cx="180" cy="128" r="24" fill="#facc15" />
        </svg>
      </span>

      <!-- Currency Code Text -->
      <span class="tracking-wide font-bold">{{ currentCurrency }}</span>

      <!-- Small Chevron Down -->
      <svg
        class="w-3.5 h-3.5 text-slate-300 transition-transform duration-200"
        :class="{ 'rotate-180': isOpen }"
        fill="none"
        stroke="currentColor"
        viewBox="0 0 24 24"
      >
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
      </svg>
    </button>

    <!-- Dropdown Popover Menu -->
    <transition
      enter-active-class="transition duration-150 ease-out"
      enter-from-class="transform scale-95 opacity-0 -translate-y-1"
      enter-to-class="transform scale-100 opacity-100 translate-y-0"
      leave-active-class="transition duration-100 ease-in"
      leave-from-class="transform scale-100 opacity-100 translate-y-0"
      leave-to-class="transform scale-95 opacity-0 -translate-y-1"
    >
      <div
        v-if="isOpen"
        class="absolute right-0 mt-2 w-52 sm:w-56 rounded-2xl bg-slate-900/95 backdrop-blur-md border border-slate-700/80 shadow-2xl p-1.5 z-50 text-slate-200"
        role="menu"
      >
        <div class="px-3 py-1.5 border-b border-slate-800 text-[10px] font-bold uppercase tracking-wider text-slate-400">
          Pilih Mata Uang / Currency
        </div>

        <div class="py-1 space-y-0.5">
          <button
            v-for="curr in CURRENCIES"
            :key="curr.code"
            @click="handleSelect(curr.code)"
            type="button"
            class="w-full flex items-center justify-between px-2.5 py-2 rounded-xl text-xs font-semibold transition-colors"
            :class="currentCurrency === curr.code
              ? 'bg-blue-600/20 text-blue-400 border border-blue-500/30 font-bold'
              : 'hover:bg-slate-800/80 text-slate-300 hover:text-white'"
            role="menuitem"
          >
            <div class="flex items-center gap-2.5">
              <!-- Flag Circle in dropdown -->
              <span class="w-5 h-5 rounded-full overflow-hidden shrink-0 border border-white/20 flex items-center justify-center">
                <!-- Indonesia -->
                <svg v-if="curr.code === 'IDR'" viewBox="0 0 512 512" class="w-full h-full">
                  <circle cx="256" cy="256" r="256" fill="#f0f0f0" />
                  <path d="M0 256C0 114.6 114.6 0 256 0s256 114.6 256 256H0z" fill="#e11d48" />
                </svg>
                <!-- Singapore -->
                <svg v-else-if="curr.code === 'SGD'" viewBox="0 0 512 512" class="w-full h-full">
                  <circle cx="256" cy="256" r="256" fill="#f0f0f0" />
                  <path d="M0 256C0 114.6 114.6 0 256 0s256 114.6 256 256H0z" fill="#ef4444" />
                  <path d="M120 70 A60 60 0 1 0 210 170 A55 55 0 1 1 120 70 Z" fill="#ffffff" />
                  <polygon points="190,95 194,105 205,105 196,112 199,122 190,116 181,122 184,112 175,105 186,105" fill="#ffffff" />
                </svg>
                <!-- Malaysia -->
                <svg v-else-if="curr.code === 'MYR'" viewBox="0 0 512 512" class="w-full h-full">
                  <circle cx="256" cy="256" r="256" fill="#f0f0f0" />
                  <path d="M0 36h512v36H0zm0 72h512v36H0zm0 72h512v36H0zm0 72h512v36H0zm0 72h512v36H0zm0 72h512v36H0z" fill="#dc2626" />
                  <path d="M0 256C0 114.6 114.6 0 256 0v256H0z" fill="#1e3a8a" />
                  <circle cx="120" cy="128" r="58" fill="#facc15" />
                  <circle cx="140" cy="128" r="48" fill="#1e3a8a" />
                  <circle cx="180" cy="128" r="24" fill="#facc15" />
                </svg>
              </span>

              <div class="text-left">
                <span class="block leading-tight">{{ curr.code }}</span>
                <span class="block text-[10px] text-slate-400 font-normal leading-tight">{{ curr.label }}</span>
              </div>
            </div>

            <!-- Active Checkmark -->
            <svg
              v-if="currentCurrency === curr.code"
              class="w-4 h-4 text-blue-400 shrink-0"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
            </svg>
          </button>
        </div>

        <div class="px-2.5 py-1.5 border-t border-slate-800 text-[10px] text-slate-400 leading-tight">
          💡 Estimasi kurs untuk turis. Transaksi resmi mengacu pada IDR.
        </div>
      </div>
    </transition>
  </div>
</template>
