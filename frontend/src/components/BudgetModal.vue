<script setup lang="ts">
import { ref, computed, watch } from 'vue'
import { useCategoriesStore } from '@/stores/categories'
import { useBudgetsStore } from '@/stores/budgets'
import { useUiStore } from '@/stores/ui'
import type { Budget } from '@/types/budget'
import type { SelectOption } from '@/components/SelectInput.vue'
import SelectInput from '@/components/SelectInput.vue'
import CurrencyInput from '@/components/CurrencyInput.vue'
import { XMarkIcon, BanknotesIcon, CheckIcon } from '@heroicons/vue/24/outline'

const props = defineProps<{
  modelValue: boolean
  budget?: Budget | null
  initialMonth?: number
  initialYear?: number
}>()

const emit = defineEmits<{
  (e: 'update:modelValue', val: boolean): void
  (e: 'saved'): void
}>()

const categoriesStore = useCategoriesStore()
const budgetsStore = useBudgetsStore()
const uiStore = useUiStore()

const categoryId = ref<string>('')
const amount = ref<number>(0)
const month = ref<number>(props.initialMonth ?? new Date().getMonth() + 1)
const year = ref<number>(props.initialYear ?? new Date().getFullYear())

const submitting = ref(false)
const errorMessage = ref('')

const isEditing = computed(() => !!props.budget)

const monthOptions: SelectOption[] = [
  { value: 1, label: 'Januari' },
  { value: 2, label: 'Februari' },
  { value: 3, label: 'Maret' },
  { value: 4, label: 'April' },
  { value: 5, label: 'Mei' },
  { value: 6, label: 'Juni' },
  { value: 7, label: 'Juli' },
  { value: 8, label: 'Agustus' },
  { value: 9, label: 'September' },
  { value: 10, label: 'Oktober' },
  { value: 11, label: 'November' },
  { value: 12, label: 'Desember' },
]

const currentYear = new Date().getFullYear()
const yearOptions: SelectOption[] = [
  { value: currentYear - 1, label: String(currentYear - 1) },
  { value: currentYear,     label: String(currentYear) },
  { value: currentYear + 1, label: String(currentYear + 1) },
]

const categoryOptions = computed<SelectOption[]>(() =>
  categoriesStore.expenseCategories.map((c) => ({
    value: String(c.id),
    label: c.name,
    icon: c.icon,
  }))
)

watch(
  () => props.modelValue,
  (open) => {
    if (open) {
      errorMessage.value = ''
      if (props.budget) {
        categoryId.value = String(props.budget.category_id)
        amount.value = props.budget.amount
        month.value = props.budget.month
        year.value = props.budget.year
      } else {
        categoryId.value = categoryOptions.value[0]?.value ? String(categoryOptions.value[0].value) : ''
        amount.value = 1000000
        month.value = props.initialMonth ?? new Date().getMonth() + 1
        year.value = props.initialYear ?? new Date().getFullYear()
      }
    }
  }
)

async function handleSubmit() {
  errorMessage.value = ''
  submitting.value = true

  try {
    if (isEditing.value && props.budget) {
      await budgetsStore.updateBudget(props.budget.id, amount.value)
      uiStore.showToast('Anggaran bulanan berhasil diperbarui!')
    } else {
      await budgetsStore.createBudget({
        category_id: Number(categoryId.value),
        amount: amount.value,
        month: Number(month.value),
        year: Number(year.value),
      })
      uiStore.showToast('Anggaran baru berhasil ditetapkan!')
    }
    emit('saved')
    emit('update:modelValue', false)
  } catch (err: any) {
    const d = err.response?.data
    if (d?.errors) {
      errorMessage.value = Object.values(d.errors as Record<string, string[]>).flat().join(' ')
    } else {
      errorMessage.value = d?.message ?? 'Gagal menyimpan budget.'
    }
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
      <!-- Backdrop -->
      <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" @click="$emit('update:modelValue', false)"></div>

      <!-- Modal Card -->
      <div
        class="relative bg-white dark:bg-slate-800 rounded-t-3xl sm:rounded-3xl w-full max-w-md shadow-2xl border border-slate-200/80 dark:border-slate-700 flex flex-col z-10 transition-all"
      >
        <!-- Mobile drag indicator -->
        <div class="w-12 h-1 bg-slate-200 dark:bg-slate-700 rounded-full mx-auto mt-2.5 mb-1 sm:hidden"></div>

        <!-- Header -->
        <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100 dark:border-slate-700/60">
          <div class="flex items-center gap-2.5">
            <div class="w-8 h-8 rounded-xl bg-primary-50 dark:bg-primary-950/40 text-primary-600 dark:text-primary-400 flex items-center justify-center">
              <BanknotesIcon class="w-5 h-5" />
            </div>
            <div>
              <h2 class="text-base font-bold text-slate-900 dark:text-white leading-tight">
                {{ isEditing ? 'Edit Budget' : 'Buat Budget Baru' }}
              </h2>
              <p class="text-xs text-slate-400 dark:text-slate-500">
                Atur batas pengeluaran bulanan per kategori
              </p>
            </div>
          </div>
          <button
            @click="$emit('update:modelValue', false)"
            class="p-1.5 rounded-xl text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors"
          >
            <XMarkIcon class="w-5 h-5" />
          </button>
        </div>

        <!-- Form -->
        <form @submit.prevent="handleSubmit" class="p-5 space-y-4">
          <!-- Kategori -->
          <div>
            <label class="label">Kategori Pengeluaran</label>
            <SelectInput
              v-model="categoryId"
              :options="categoryOptions"
              :disabled="isEditing"
              placeholder="Pilih kategori"
            />
            <p v-if="isEditing" class="text-[11px] text-slate-400 mt-1">Kategori tidak dapat diubah saat edit.</p>
          </div>

          <!-- Nominal -->
          <div>
            <label class="label">Batas Anggaran (Budget)</label>
            <CurrencyInput v-model="amount" :required="true" />
          </div>

          <!-- Periode Bulan & Tahun -->
          <div class="grid grid-cols-2 gap-3" v-if="!isEditing">
            <div>
              <label class="label">Bulan</label>
              <SelectInput v-model="month" :options="monthOptions" direction="up" placeholder="Bulan" />
            </div>
            <div>
              <label class="label">Tahun</label>
              <SelectInput v-model="year" :options="yearOptions" direction="up" placeholder="Tahun" />
            </div>
          </div>

          <!-- Error Alert -->
          <div v-if="errorMessage" class="p-3 rounded-xl bg-rose-50 dark:bg-rose-900/20 text-rose-600 text-xs">
            {{ errorMessage }}
          </div>

          <!-- Footer Buttons -->
          <div class="flex gap-2.5 pt-2">
            <button
              type="button"
              @click="$emit('update:modelValue', false)"
              class="btn-secondary flex-1 py-2.5 text-xs sm:text-sm"
            >
              Batal
            </button>
            <button
              type="submit"
              :disabled="submitting || !amount || !categoryId"
              class="btn-primary flex-1 py-2.5 text-xs sm:text-sm flex items-center justify-center gap-1.5 shadow-md"
            >
              <CheckIcon class="w-4 h-4 stroke-2" />
              <span>{{ submitting ? 'Menyimpan...' : 'Simpan Budget' }}</span>
            </button>
          </div>
        </form>
      </div>
    </div>
  </Teleport>
</template>
