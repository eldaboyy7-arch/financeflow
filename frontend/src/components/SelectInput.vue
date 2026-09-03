<script setup lang="ts">
import { ref, computed, onMounted, onBeforeUnmount, nextTick } from 'vue'
import { ChevronUpDownIcon, CheckIcon } from '@heroicons/vue/24/outline'

export interface SelectOption {
  value: string | number
  label: string
  icon?: string
}

const props = withDefaults(defineProps<{
  modelValue: string | number
  options: SelectOption[]
  placeholder?: string
  disabled?: boolean
  direction?: 'auto' | 'up' | 'down'
}>(), {
  placeholder: 'Pilih...',
  direction: 'auto',
})

const emit = defineEmits<{
  (e: 'update:modelValue', val: string | number): void
  (e: 'change'): void
}>()

const open = ref(false)
const openUp = ref(false)
const containerRef = ref<HTMLElement | null>(null)

const selectedOption = computed(() =>
  props.options.find((o) => String(o.value) === String(props.modelValue))
)

async function toggle() {
  if (props.disabled) return

  if (!open.value) {
    if (props.direction === 'up') {
      openUp.value = true
    } else if (props.direction === 'down') {
      openUp.value = false
    } else if (containerRef.value) {
      // Auto-detect available space below
      const rect = containerRef.value.getBoundingClientRect()
      const spaceBelow = window.innerHeight - rect.bottom
      const spaceAbove = rect.top
      // If less than 240px below and more space above, open upwards
      openUp.value = spaceBelow < 240 && spaceAbove > spaceBelow
    }
  }

  open.value = !open.value
}

function select(opt: SelectOption) {
  emit('update:modelValue', opt.value)
  emit('change')
  open.value = false
}

function onOutsideClick(e: MouseEvent) {
  if (containerRef.value && !containerRef.value.contains(e.target as Node)) {
    open.value = false
  }
}

onMounted(() => document.addEventListener('mousedown', onOutsideClick))
onBeforeUnmount(() => document.removeEventListener('mousedown', onOutsideClick))
</script>

<template>
  <div ref="containerRef" class="relative w-full">
    <!-- Trigger -->
    <button
      type="button"
      @click="toggle"
      :disabled="disabled"
      :class="[
        'input flex items-center justify-between gap-2 text-left cursor-pointer transition-all',
        open ? 'ring-2 ring-primary-500 border-transparent' : '',
        disabled ? 'opacity-50 cursor-not-allowed' : '',
        !selectedOption ? 'text-slate-400 dark:text-slate-500' : 'text-slate-900 dark:text-slate-100',
      ]"
    >
      <span class="flex items-center gap-2 truncate">
        <span v-if="selectedOption?.icon" class="shrink-0 text-base leading-none">{{ selectedOption.icon }}</span>
        <span class="truncate">{{ selectedOption?.label ?? placeholder }}</span>
      </span>
      <ChevronUpDownIcon
        class="w-4 h-4 shrink-0 text-slate-400 transition-transform duration-200"
        :class="open ? 'rotate-180' : ''"
      />
    </button>

    <!-- Dropdown Menu -->
    <Transition
      enter-active-class="transition duration-150 ease-out"
      :enter-from-class="openUp ? 'transform translate-y-2 opacity-0' : 'transform -translate-y-2 opacity-0'"
      enter-to-class="transform translate-y-0 opacity-100"
      leave-active-class="transition duration-100 ease-in"
      leave-from-class="transform translate-y-0 opacity-100"
      :leave-to-class="openUp ? 'transform translate-y-2 opacity-0' : 'transform -translate-y-2 opacity-0'"
    >
      <div
        v-if="open"
        :class="[
          'absolute z-[100] w-full min-w-full bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl shadow-2xl overflow-hidden',
          openUp ? 'bottom-full mb-1.5' : 'top-full mt-1.5',
        ]"
      >
        <ul class="py-1 max-h-56 overflow-y-auto">
          <li
            v-for="opt in options"
            :key="opt.value"
            @click="select(opt)"
            :class="[
              'flex items-center justify-between gap-2 px-3 py-2.5 text-xs sm:text-sm cursor-pointer transition-colors',
              String(opt.value) === String(modelValue)
                ? 'bg-primary-50 dark:bg-primary-950/60 text-primary-600 dark:text-primary-400 font-bold'
                : 'text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700 hover:text-slate-900 dark:hover:text-white',
            ]"
          >
            <span class="flex items-center gap-2 truncate">
              <span v-if="opt.icon" class="shrink-0 text-base leading-none">{{ opt.icon }}</span>
              <span class="truncate">{{ opt.label }}</span>
            </span>
            <CheckIcon
              v-if="String(opt.value) === String(modelValue)"
              class="w-4 h-4 shrink-0 text-primary-600 dark:text-primary-400 stroke-2"
            />
          </li>
        </ul>
      </div>
    </Transition>
  </div>
</template>
