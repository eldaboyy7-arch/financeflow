<script setup lang="ts">
import { ref, computed, watch } from 'vue'
import { useGoalsStore } from '@/stores/goals'
import { useAccountsStore } from '@/stores/accounts'
import { useUiStore } from '@/stores/ui'
import type { Goal } from '@/types/goal'
import CurrencyInput from '@/components/CurrencyInput.vue'
import SelectInput from '@/components/SelectInput.vue'
import type { SelectOption } from '@/components/SelectInput.vue'
import { XMarkIcon, PlusCircleIcon, MinusCircleIcon, CheckIcon } from '@heroicons/vue/24/outline'

const props = defineProps<{
  modelValue: boolean
  goal: Goal | null
}>()

const emit = defineEmits<{
  (e: 'update:modelValue', val: boolean): void
  (e: 'saved'): void
}>()

const goalsStore = useGoalsStore()
const accountsStore = useAccountsStore()
const uiStore = useUiStore()

const amount = ref(100000)
const type = ref<'deposit' | 'withdraw'>('deposit')
const accountId = ref<string>('')
const notes = ref('')
const submitting = ref(false)
const errorMessage = ref('')

const accountOptions = computed<SelectOption[]>(() => [
  { value: '', label: 'Tanpa memotong rekening' },
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
      amount.value = 100000
      type.value = 'deposit'
      notes.value = ''
      accountId.value = props.goal?.account ? String(props.goal.account.id) : ''
    }
  }
)

async function handleSubmit() {
  if (!props.goal || amount.value <= 0) return

  errorMessage.value = ''
  submitting.value = true
  try {
    await goalsStore.contributeGoal(props.goal.id, {
      amount: amount.value,
      type: type.value,
      account_id: accountId.value ? Number(accountId.value) : undefined,
      notes: notes.value || undefined,
    })
    uiStore.showToast(type.value === 'deposit' ? 'Setoran tabungan berhasil dicatat!' : 'Penarikan tabungan berhasil dicatat!')
    emit('saved')
    emit('update:modelValue', false)
  } catch (err: any) {
    errorMessage.value = err.response?.data?.message ?? 'Gagal memproses setoran tabungan.'
  } finally {
    submitting.value = false
  }
}
</script>

<template>
  <Teleport to="body">
    <div
      v-if="modelValue && goal"
      class="fixed inset-0 z-50 flex items-end sm:items-center justify-center p-0 sm:p-4 overflow-y-auto"
    >
      <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" @click="$emit('update:modelValue', false)"></div>

      <div class="relative bg-white dark:bg-slate-800 rounded-t-3xl sm:rounded-3xl w-full max-w-md shadow-2xl border border-slate-200/80 dark:border-slate-700 flex flex-col z-10 transition-all">
        <!-- Header -->
        <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100 dark:border-slate-700/60">
          <div class="flex items-center gap-2.5">
            <div class="w-9 h-9 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-lg">
              {{ goal.icon }}
            </div>
            <div>
              <h2 class="text-base font-bold text-slate-900 dark:text-white leading-tight">
                Tabung ke "{{ goal.name }}"
              </h2>
              <p class="text-xs text-slate-400 dark:text-slate-500">
                Terkumpul: Rp{{ Number(goal.current_amount).toLocaleString('id-ID') }} / Rp{{ Number(goal.target_amount).toLocaleString('id-ID') }}
              </p>
            </div>
          </div>
          <button @click="$emit('update:modelValue', false)" class="p-1.5 rounded-xl text-slate-400 hover:text-slate-700 dark:hover:text-slate-200">
            <XMarkIcon class="w-5 h-5" />
          </button>
        </div>

        <!-- Form -->
        <form @submit.prevent="handleSubmit" class="p-5 space-y-4">
          <!-- Type Toggle (Setor vs Tarik) -->
          <div class="grid grid-cols-2 gap-2 p-1 bg-slate-100 dark:bg-slate-900 rounded-2xl">
            <button
              type="button"
              @click="type = 'deposit'"
              :class="[
                'py-2 text-xs font-bold rounded-xl transition-all flex items-center justify-center gap-1.5',
                type === 'deposit'
                  ? 'bg-emerald-500 text-white shadow-md'
                  : 'text-slate-600 dark:text-slate-400 hover:text-slate-900',
              ]"
            >
              <PlusCircleIcon class="w-4 h-4" />
              <span>Setor Tabungan</span>
            </button>
            <button
              type="button"
              @click="type = 'withdraw'"
              :class="[
                'py-2 text-xs font-bold rounded-xl transition-all flex items-center justify-center gap-1.5',
                type === 'withdraw'
                  ? 'bg-rose-500 text-white shadow-md'
                  : 'text-slate-600 dark:text-slate-400 hover:text-slate-900',
              ]"
            >
              <MinusCircleIcon class="w-4 h-4" />
              <span>Tarik Tabungan</span>
            </button>
          </div>

          <!-- Nominal -->
          <div>
            <label class="label">Nominal (Rp)</label>
            <CurrencyInput v-model="amount" :required="true" />
          </div>

          <!-- Rekening Sumber -->
          <div>
            <label class="label">Rekening Sumber (Opsional)</label>
            <SelectInput v-model="accountId" :options="accountOptions" placeholder="Pilih rekening" />
          </div>

          <!-- Catatan -->
          <div>
            <label class="label">Catatan (Opsional)</label>
            <input v-model="notes" type="text" placeholder="Misal: Alokasi gaji bulanan, bonus..." class="input" />
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
            <button type="submit" :disabled="submitting || amount <= 0" class="btn-primary flex-1 py-2.5 text-xs sm:text-sm flex items-center justify-center gap-1.5 shadow-md">
              <CheckIcon class="w-4 h-4 stroke-2" />
              <span>{{ submitting ? 'Memproses...' : (type === 'deposit' ? 'Konfirmasi Setoran' : 'Konfirmasi Tarik') }}</span>
            </button>
          </div>
        </form>
      </div>
    </div>
  </Teleport>
</template>
