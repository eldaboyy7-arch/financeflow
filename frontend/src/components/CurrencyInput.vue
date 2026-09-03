<script setup lang="ts">
import { ref, watch, computed } from 'vue'

const props = defineProps<{
  modelValue: number
  min?: number
  required?: boolean
  placeholder?: string
}>()

const emit = defineEmits<{
  (e: 'update:modelValue', val: number): void
}>()

// Display value with dot separators
const displayValue = ref(formatDisplay(props.modelValue))

function formatDisplay(num: number): string {
  if (!num && num !== 0) return ''
  if (num === 0) return ''
  return num.toLocaleString('id-ID')
}

function parseValue(str: string): number {
  // Remove all dots (thousand separators)
  const cleaned = str.replace(/\./g, '').replace(/,/g, '')
  const num = parseInt(cleaned, 10)
  return isNaN(num) ? 0 : num
}

function onInput(e: Event) {
  const input = e.target as HTMLInputElement
  const cursorPos = input.selectionStart ?? 0
  const oldLen = input.value.length

  // Parse the raw value
  const raw = parseValue(input.value)
  emit('update:modelValue', raw)

  // Format and set display
  const formatted = raw > 0 ? formatDisplay(raw) : ''
  displayValue.value = formatted
  input.value = formatted

  // Restore cursor position (adjust for added/removed dots)
  const newLen = formatted.length
  const diff = newLen - oldLen
  const newPos = Math.max(0, cursorPos + diff)
  requestAnimationFrame(() => {
    input.setSelectionRange(newPos, newPos)
  })
}

function onFocus(e: FocusEvent) {
  const input = e.target as HTMLInputElement
  if (input.value === '0') {
    displayValue.value = ''
    input.value = ''
  }
}

function onBlur() {
  if (props.modelValue > 0) {
    displayValue.value = formatDisplay(props.modelValue)
  } else {
    displayValue.value = ''
  }
}

// Sync when parent changes modelValue
watch(() => props.modelValue, (val) => {
  const current = parseValue(displayValue.value)
  if (current !== val) {
    displayValue.value = formatDisplay(val)
  }
})
</script>

<template>
  <div class="relative">
    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-sm text-slate-400 font-medium pointer-events-none select-none">Rp</span>
    <input
      type="text"
      inputmode="numeric"
      class="input pl-10 tabular-nums"
      :value="displayValue"
      :placeholder="placeholder ?? '0'"
      :required="required"
      @input="onInput"
      @focus="onFocus"
      @blur="onBlur"
    />
  </div>
</template>
