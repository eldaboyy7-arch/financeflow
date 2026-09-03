<script setup lang="ts">
import { ref, computed, watch } from 'vue'
import { useRecurringStore } from '@/stores/recurring'
import { useAccountsStore } from '@/stores/accounts'
import { useCategoriesStore } from '@/stores/categories'
import { useUiStore } from '@/stores/ui'
import type { RecurringItem } from '@/types/recurring'
import CurrencyInput from '@/components/CurrencyInput.vue'
import SelectInput from '@/components/SelectInput.vue'
import type { SelectOption } from '@/components/SelectInput.vue'
import { XMarkIcon, ArrowPathIcon, CheckIcon } from '@heroicons/vue/24/outline'

const props = defineProps<{
  modelValue: boolean
  recurring?: RecurringItem | null
}>()

const emit = defineEmits<{
  (e: 'update:modelValue', val: boolean): void
  (e: 'saved'): void
}>()

const recurringStore = useRecurringStore()
const accountsStore = useAccountsStore()
const categoriesStore = useCategoriesStore()
const uiStore = useUiStore()

const name = ref('')
const amount = ref(100000)
const type = ref<'expense' | 'income'>('expense')
const frequency = ref<'monthly' | 'weekly' | 'daily' | 'yearly'>('monthly')
const accountId = ref<string>('')
const categoryId = ref<string>('')
const startDate = ref(new Date().toISOString().split('T')[0])
const notes = ref('')
const submitting = ref(false)
const errorMessage = ref('')

const isEditing = computed(() => !!props.recurring)

const frequencyOptions: SelectOption[] = [
  { value: 'monthly', label: 'Bulanan (Setiap Bulan)' },
  { value: 'weekly',  label: 'Mingguan (Setiap Minggu)' },
  { value: 'daily',   label: 'Harian (Setiap Hari)' },
  { value: 'yearly',  label: 'Tahunan (Setiap Tahun)' },
]

const accountOptions = computed<SelectOption[]>(() =>
  accountsStore.activeAccounts.map((a) => ({
    value: String(a.id),
    label: `${a.icon} ${a.name}`,
  }))
)

const categoryOptions = computed<SelectOption[]>(() => {
  const cats = type.value === 'expense' ? categoriesStore.expenseCategories : categoriesStore.incomeCategories
  return cats.map((c) => ({
    value: String(c.id),
    label: `${c.icon} ${c.name}`,
  }))
})

watch(
  () => props.modelValue,
  (open) => {
    if (open) {
      errorMessage.value = ''
      if (props.recurring) {
        name.value = props.recurring.name
        amount.value = props.recurring.amount
        type.value = props.recurring.type
        frequency.value = props.recurring.frequency
        accountId.value = props.recurring.account ? String(props.recurring.account.id) : ''
        categoryId.value = props.recurring.category ? String(props.recurring.category.id) : ''
        startDate.value = props.recurring.next_due_date || new Date().toISOString().split('T')[0]
        notes.value = props.recurring.notes || ''
      } else {
        name.value = ''
        amount.value = 100000
        type.value = 'expense'
        frequency.value = 'monthly'
        accountId.value = accountOptions.value[0]?.value ? String(accountOptions.value[0].value) : ''
        categoryId.value = categoryOptions.value[0]?.value ? String(categoryOptions.value[0].value) : ''
        startDate.value = new Date().toISOString().split('T')[0]
        notes.value = ''
      }
    }
  }
)

async function handleSubmit() {
  if (!name.value.trim() || amount.value <= 0 || !accountId.value || !categoryId.value) return

  errorMessage.value = ''
  submitting.value = true
  try {
    if (isEditing.value && props.recurring) {
      await recurringStore.updateItem(props.recurring.id, {
        name: name.value,
        amount: amount.value,
        type: type.value,
        frequency: frequency.value,
        account_id: Number(accountId.value),
        category_id: Number(categoryId.value),
        next_due_date: startDate.value,
        notes: notes.value || undefined,
      })
      uiStore.showToast('Tagihan rutin berhasil diperbarui!')
    } else {
      await recurringStore.createItem({
        name: name.value,
        amount: amount.value,
        type: type.value,
        frequency: frequency.value,
        account_id: Number(accountId.value),
        category_id: Number(categoryId.value),
        start_date: startDate.value,
        notes: notes.value || undefined,
      })
      uiStore.showToast('Tagihan rutin baru berhasil dijadwalkan!')
    }
    emit('saved')
    emit('update:modelValue', false)
  } catch (err: any) {
    errorMessage.value = err.response?.data?.message ?? 'Gagal menyimpan tagihan rutin.'
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
            <div class="w-8 h-8 rounded-xl bg-primary-50 dark:bg-primary-950/40 text-primary-600 dark:text-primary-400 flex items-center justify-center">
              <ArrowPathIcon class="w-5 h-5" />
            </div>
            <div>
              <h2 class="text-base font-bold text-slate-900 dark:text-white leading-tight">
                {{ isEditing ? 'Edit Tagihan Rutin' : 'Tambah Tagihan Rutin' }}
              </h2>
              <p class="text-xs text-slate-400 dark:text-slate-500">Kelola langganan & pembayaran berkala</p>
            </div>
          </div>
          <button @click="$emit('update:modelValue', false)" class="p-1.5 rounded-xl text-slate-400 hover:text-slate-700 dark:hover:text-slate-200">
            <XMarkIcon class="w-5 h-5" />
          </button>
        </div>

        <!-- Form -->
        <form @submit.prevent="handleSubmit" class="p-5 space-y-4">
          <!-- Nama Tagihan -->
          <div>
            <label class="label">Nama Tagihan / Langganan</label>
            <input
              v-model="name"
              type="text"
              placeholder="Misal: Netflix, BPJS, Indihome, Sewa Kost"
              required
              class="input"
            />
          </div>

          <!-- Nominal -->
          <div>
            <label class="label">Nominal (Rp)</label>
            <CurrencyInput v-model="amount" :required="true" />
          </div>

          <!-- Frekuensi & Rekening -->
          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="label">Frekuensi</label>
              <SelectInput v-model="frequency" :options="frequencyOptions" placeholder="Pilih frekuensi" />
            </div>
            <div>
              <label class="label">Rekening</label>
              <SelectInput v-model="accountId" :options="accountOptions" placeholder="Pilih rekening" />
            </div>
          </div>

          <!-- Kategori & Jatuh Tempo -->
          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="label">Kategori</label>
              <SelectInput v-model="categoryId" :options="categoryOptions" direction="up" placeholder="Pilih kategori" />
            </div>
            <div>
              <label class="label">Jatuh Tempo Pertama</label>
              <input v-model="startDate" type="date" required class="input text-xs" />
            </div>
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
            <button type="submit" :disabled="submitting || !name.trim() || amount <= 0" class="btn-primary flex-1 py-2.5 text-xs sm:text-sm flex items-center justify-center gap-1.5 shadow-md">
              <CheckIcon class="w-4 h-4 stroke-2" />
              <span>{{ submitting ? 'Menyimpan...' : 'Simpan Tagihan' }}</span>
            </button>
          </div>
        </form>
      </div>
    </div>
  </Teleport>
</template>
