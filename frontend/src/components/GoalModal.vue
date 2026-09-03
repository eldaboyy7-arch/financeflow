<script setup lang="ts">
import { ref, computed, watch } from 'vue'
import { useGoalsStore } from '@/stores/goals'
import { useAccountsStore } from '@/stores/accounts'
import { useUiStore } from '@/stores/ui'
import type { Goal } from '@/types/goal'
import CurrencyInput from '@/components/CurrencyInput.vue'
import SelectInput from '@/components/SelectInput.vue'
import type { SelectOption } from '@/components/SelectInput.vue'
import { XMarkIcon, SparklesIcon, CheckIcon } from '@heroicons/vue/24/outline'

const props = defineProps<{
  modelValue: boolean
  goal?: Goal | null
}>()

const emit = defineEmits<{
  (e: 'update:modelValue', val: boolean): void
  (e: 'saved'): void
}>()

const goalsStore = useGoalsStore()
const accountsStore = useAccountsStore()
const uiStore = useUiStore()

const name = ref('')
const targetAmount = ref(5000000)
const targetDate = ref('')
const icon = ref('🎯')
const color = ref('#6366F1')
const accountId = ref<string>('')
const submitting = ref(false)
const errorMessage = ref('')

const isEditing = computed(() => !!props.goal)

const emojiList = ['🎯', '💻', '🚗', '🏠', '✈️', '💍', '🎓', '🏥', '🏖️', '📱', '👶', '🛡️', '📦']

const accountOptions = computed<SelectOption[]>(() => [
  { value: '', label: 'Tanpa rekening sumber' },
  ...accountsStore.activeAccounts.map((a) => ({
    value: String(a.id),
    label: `${a.icon} ${a.name}`,
  })),
])

watch(
  () => props.modelValue,
  (open) => {
    if (open) {
      errorMessage.value = ''
      if (props.goal) {
        name.value = props.goal.name
        targetAmount.value = props.goal.target_amount
        targetDate.value = props.goal.target_date || ''
        icon.value = props.goal.icon || '🎯'
        color.value = props.goal.color || '#6366F1'
        accountId.value = props.goal.account ? String(props.goal.account.id) : ''
      } else {
        name.value = ''
        targetAmount.value = 5000000
        targetDate.value = ''
        icon.value = '🎯'
        color.value = '#6366F1'
        accountId.value = ''
      }
    }
  }
)

async function handleSubmit() {
  if (!name.value.trim() || targetAmount.value <= 0) return

  errorMessage.value = ''
  submitting.value = true
  try {
    if (isEditing.value && props.goal) {
      await goalsStore.updateGoal(props.goal.id, {
        name: name.value,
        target_amount: targetAmount.value,
        target_date: targetDate.value || undefined,
        icon: icon.value,
        color: color.value,
        account_id: accountId.value ? Number(accountId.value) : undefined,
      })
      uiStore.showToast('Target impian berhasil diperbarui!')
    } else {
      await goalsStore.createGoal({
        name: name.value,
        target_amount: targetAmount.value,
        target_date: targetDate.value || undefined,
        icon: icon.value,
        color: color.value,
        account_id: accountId.value ? Number(accountId.value) : undefined,
      })
      uiStore.showToast('Target impian baru berhasil dibuat!')
    }
    emit('saved')
    emit('update:modelValue', false)
  } catch (err: any) {
    errorMessage.value = err.response?.data?.message ?? 'Gagal menyimpan target impian.'
  } finally {
    submitting.value = false
  }
}
</script>

<template>
  <Teleport to="body">
    <div
      v-if="modelValue"
      class="fixed inset-0 z-50 flex items-end sm:items-center justify-center p-0 sm:p-4 overflow-y-auto"
    >
      <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" @click="$emit('update:modelValue', false)"></div>

      <div class="relative bg-white dark:bg-slate-800 rounded-t-3xl sm:rounded-3xl w-full max-w-md shadow-2xl border border-slate-200/80 dark:border-slate-700 flex flex-col z-10 transition-all">
        <!-- Header -->
        <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100 dark:border-slate-700/60">
          <div class="flex items-center gap-2.5">
            <div class="w-8 h-8 rounded-xl bg-primary-50 dark:bg-primary-950/40 text-primary-600 dark:text-primary-400 flex items-center justify-center text-lg">
              {{ icon }}
            </div>
            <div>
              <h2 class="text-base font-bold text-slate-900 dark:text-white leading-tight">
                {{ isEditing ? 'Edit Target Impian' : 'Buat Target Impian' }}
              </h2>
              <p class="text-xs text-slate-400 dark:text-slate-500">Rencanakan tabungan masa depanmu</p>
            </div>
          </div>
          <button @click="$emit('update:modelValue', false)" class="p-1.5 rounded-xl text-slate-400 hover:text-slate-700 dark:hover:text-slate-200">
            <XMarkIcon class="w-5 h-5" />
          </button>
        </div>

        <!-- Form -->
        <form @submit.prevent="handleSubmit" class="p-5 space-y-4">
          <!-- Icon Picker -->
          <div>
            <label class="label">Pilih Ikon</label>
            <div class="flex flex-wrap gap-1.5 p-2 bg-slate-50 dark:bg-slate-900/50 rounded-2xl border border-slate-200/60 dark:border-slate-700/60">
              <button
                v-for="e in emojiList"
                :key="e"
                type="button"
                @click="icon = e"
                :class="[
                  'w-8 h-8 rounded-xl text-base flex items-center justify-center transition-all',
                  icon === e ? 'bg-primary-500 text-white shadow-md scale-110' : 'hover:bg-slate-200 dark:hover:bg-slate-800',
                ]"
              >
                {{ e }}
              </button>
            </div>
          </div>

          <!-- Nama Target -->
          <div>
            <label class="label">Nama Target / Wishlist</label>
            <input
              v-model="name"
              type="text"
              placeholder="Misal: Dana Darurat, Beli MacBook, Liburan Bali"
              required
              class="input"
            />
          </div>

          <!-- Target Nominal -->
          <div>
            <label class="label">Target Nominal (Rp)</label>
            <CurrencyInput v-model="targetAmount" :required="true" />
          </div>

          <!-- Target Tanggal -->
          <div>
            <label class="label">Target Tanggal Tercapai (Opsional)</label>
            <input
              v-model="targetDate"
              type="date"
              class="input"
            />
          </div>

          <!-- Error Alert -->
          <div v-if="errorMessage" class="p-3 rounded-xl bg-rose-50 dark:bg-rose-900/20 text-rose-600 text-xs">
            {{ errorMessage }}
          </div>

          <!-- Buttons -->
          <div class="flex gap-2.5 pt-2">
            <button type="button" @click="$emit('update:modelValue', false)" class="btn-secondary flex-1 py-2.5 text-xs sm:text-sm">
              Batal
            </button>
            <button type="submit" :disabled="submitting || !name.trim() || targetAmount <= 0" class="btn-primary flex-1 py-2.5 text-xs sm:text-sm flex items-center justify-center gap-1.5 shadow-md">
              <CheckIcon class="w-4 h-4 stroke-2" />
              <span>{{ submitting ? 'Menyimpan...' : 'Simpan Target' }}</span>
            </button>
          </div>
        </form>
      </div>
    </div>
  </Teleport>
</template>
