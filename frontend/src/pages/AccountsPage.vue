<script setup lang="ts">
import { ref, onMounted, computed } from 'vue'
import { useAccountsStore } from '@/stores/accounts'
import { useUiStore } from '@/stores/ui'
import { useFormatCurrency } from '@/composables/useFormatCurrency'
import type { AccountPayload, AccountType } from '@/types/account'
import SelectInput from '@/components/SelectInput.vue'
import CurrencyInput from '@/components/CurrencyInput.vue'
import MoneySpinner from '@/components/MoneySpinner.vue'
import type { SelectOption } from '@/components/SelectInput.vue'
import {
  PlusIcon,
  PencilSquareIcon,
  TrashIcon,
  BuildingLibraryIcon,
  XMarkIcon,
} from '@heroicons/vue/24/outline'

const { formatCurrency } = useFormatCurrency()
const accountsStore = useAccountsStore()
const uiStore = useUiStore()

const showModal = ref(false)
const editingId = ref<number | null>(null)
const submitting = ref(false)
const modalError = ref('')

const accountTypeOptions: SelectOption[] = [
  { value: 'cash',        label: 'Tunai' },
  { value: 'bank',        label: 'Bank' },
  { value: 'e_wallet',    label: 'E-Wallet' },
  { value: 'credit_card', label: 'Kartu Kredit' },
  { value: 'other',       label: 'Lainnya' },
]

const defaultForm: AccountPayload = {
  name: '', type: 'cash', icon: '💵',
  color: '#10B981', opening_balance: 0, is_active: true,
}
const form = ref<AccountPayload>({ ...defaultForm })

onMounted(() => accountsStore.fetchAccounts())

function openCreate() {
  editingId.value = null
  form.value = { ...defaultForm }
  modalError.value = ''
  showModal.value = true
}

function openEdit(id: number) {
  const acc = accountsStore.accounts.find((a) => a.id === id)
  if (!acc) return
  editingId.value = id
  form.value = {
    name: acc.name,
    type: acc.type,
    icon: acc.icon,
    color: acc.color,
    opening_balance: acc.opening_balance,
    is_active: acc.is_active,
  }
  modalError.value = ''
  showModal.value = true
}

async function submitAccount() {
  submitting.value = true
  modalError.value = ''
  try {
    if (editingId.value) {
      await accountsStore.updateAccount(editingId.value, form.value)
      uiStore.showToast('Data rekening berhasil diperbarui!')
    } else {
      await accountsStore.createAccount(form.value)
      uiStore.showToast('Rekening baru berhasil dibuat!')
    }
    showModal.value = false
  } catch (err: any) {
    const data = err.response?.data
    if (data?.errors) {
      modalError.value = Object.values(data.errors as Record<string, string[]>).flat().join(' ')
    } else {
      modalError.value = data?.message ?? 'Gagal menyimpan rekening.'
    }
  } finally {
    submitting.value = false
  }
}

async function deleteAccount(id: number) {
  if (!confirm('Hapus rekening ini?')) return
  try {
    await accountsStore.deleteAccount(id)
    uiStore.showToast('Rekening berhasil dihapus.', 'info')
  } catch (err: any) {
    uiStore.showToast(err.response?.data?.message ?? 'Gagal menghapus rekening.', 'error')
  }
}
</script>

<template>
  <div class="space-y-5">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
      <div>
        <h1 class="text-xl font-bold text-slate-900 dark:text-white">Rekening</h1>
        <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-0.5">
          Total saldo: <span class="font-bold text-slate-800 dark:text-slate-100 tabular-nums">{{ formatCurrency(accountsStore.totalBalance) }}</span>
        </p>
      </div>
      <button @click="openCreate" class="btn-primary flex items-center justify-center gap-1.5 w-full sm:w-auto text-sm py-2.5 shadow-sm">
        <PlusIcon class="w-4 h-4" />
        Tambah Rekening
      </button>
    </div>

    <!-- Loading State -->
    <div v-if="accountsStore.loading" class="card p-4 sm:p-8 flex items-center justify-center">
      <MoneySpinner size="md" text="Memuat daftar rekening & dompet..." subtext="Menghitung total saldo aktif" />
    </div>

    <!-- Empty state -->
    <div v-else-if="!accountsStore.accounts.length" class="card p-12 flex flex-col items-center text-center">
      <div class="w-14 h-14 bg-slate-100 dark:bg-slate-700/60 rounded-2xl flex items-center justify-center mb-4">
        <BuildingLibraryIcon class="w-7 h-7 text-slate-400" />
      </div>
      <p class="text-sm font-semibold text-slate-700 dark:text-slate-300">Belum ada rekening</p>
      <p class="text-xs text-slate-400 dark:text-slate-500 mt-1 mb-4 max-w-xs">Tambahkan rekening atau dompet untuk mulai mencatat keuangan kamu</p>
      <button @click="openCreate" class="btn-primary text-sm py-2 px-4">Tambah Rekening Pertama</button>
    </div>

    <!-- Accounts grid -->
    <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3.5 sm:gap-4">
      <div
        v-for="acc in accountsStore.accounts"
        :key="acc.id"
        class="card p-5 hover:shadow-md transition-all group relative"
        :class="{ 'opacity-60': !acc.is_active }"
      >
        <div class="flex items-start justify-between mb-4">
          <div
            class="w-11 h-11 rounded-xl flex items-center justify-center text-xl shrink-0"
            :style="{ backgroundColor: acc.color + '20' }"
          >
            {{ acc.icon }}
          </div>

          <!-- Actions: visible on mobile, hover-revealed on desktop -->
          <div class="flex gap-1 opacity-100 sm:opacity-0 group-hover:opacity-100 transition-opacity">
            <button
              @click="openEdit(acc.id)"
              class="p-1.5 rounded-lg text-slate-400 hover:text-primary-600 hover:bg-primary-50 dark:hover:bg-primary-900/20 transition-colors"
              title="Edit"
            >
              <PencilSquareIcon class="w-4 h-4" />
            </button>
            <button
              @click="deleteAccount(acc.id)"
              class="p-1.5 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-900/20 transition-colors"
              title="Hapus"
            >
              <TrashIcon class="w-4 h-4" />
            </button>
          </div>
        </div>

        <p class="text-xs text-slate-400 dark:text-slate-500 mb-0.5 uppercase tracking-wider font-semibold">{{ acc.type_label }}</p>
        <p class="font-bold text-slate-900 dark:text-white text-base">{{ acc.name }}</p>
        <p class="text-xl font-extrabold mt-2 tabular-nums" :style="{ color: acc.color }">
          {{ formatCurrency(acc.current_balance) }}
        </p>

        <div class="mt-3.5 pt-3 border-t border-slate-100 dark:border-slate-700/50 flex items-center justify-between">
          <span
            class="inline-flex items-center gap-1 text-[11px] font-medium px-2 py-0.5 rounded-full"
            :class="acc.is_active
              ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400'
              : 'bg-slate-100 text-slate-500 dark:bg-slate-700 dark:text-slate-400'"
          >
            <span class="w-1.5 h-1.5 rounded-full" :class="acc.is_active ? 'bg-emerald-500' : 'bg-slate-400'"></span>
            {{ acc.is_active ? 'Aktif' : 'Nonaktif' }}
          </span>
          <span class="text-[11px] text-slate-400">Saldo Awal: {{ formatCurrency(acc.opening_balance) }}</span>
        </div>
      </div>
    </div>

    <!-- Modal (Bottom sheet on mobile) -->
    <Teleport to="body">
      <div v-if="showModal" class="fixed inset-0 z-50 flex items-end sm:items-center justify-center p-0 sm:p-4">
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" @click="showModal = false"></div>
        <div class="relative bg-white dark:bg-slate-800 rounded-t-3xl sm:rounded-2xl p-5 sm:p-6 w-full max-w-md shadow-2xl border border-slate-200/60 dark:border-slate-700 max-h-[90vh] overflow-y-auto">
          <!-- Drag bar for mobile -->
          <div class="w-10 h-1 bg-slate-200 dark:bg-slate-700 rounded-full mx-auto mb-3 sm:hidden"></div>

          <div class="flex items-center justify-between mb-5">
            <h3 class="text-base font-bold text-slate-900 dark:text-white">
              {{ editingId ? 'Ubah Rekening' : 'Tambah Rekening' }}
            </h3>
            <button @click="showModal = false" class="p-1.5 rounded-lg text-slate-400 hover:text-slate-700 dark:hover:text-slate-200">
              <XMarkIcon class="w-5 h-5" />
            </button>
          </div>

          <form @submit.prevent="submitAccount" class="space-y-4">
            <div>
              <label class="label">Nama Rekening</label>
              <input v-model="form.name" type="text" class="input" placeholder="BCA Tabungan, GoPay, Dompet Tunai..." required />
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
              <div>
                <label class="label">Jenis</label>
                <SelectInput v-model="form.type" :options="accountTypeOptions" placeholder="Pilih jenis" />
              </div>
              <div>
                <label class="label">Ikon <span class="text-slate-400 font-normal text-xs">(emoji)</span></label>
                <input v-model="form.icon" type="text" class="input text-center text-xl" maxlength="4" placeholder="💵" />
              </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
              <div>
                <label class="label">Saldo Awal</label>
                <CurrencyInput v-model="form.opening_balance" />
              </div>
              <div>
                <label class="label">Warna Aksen</label>
                <div class="flex gap-2 items-center">
                  <input v-model="form.color" type="color" class="h-10 w-12 rounded-xl cursor-pointer border border-slate-200 dark:border-slate-600 bg-transparent p-1" />
                  <input v-model="form.color" type="text" class="input text-xs font-mono uppercase" maxlength="7" />
                </div>
              </div>
            </div>

            <div v-if="editingId" class="flex items-center gap-2 pt-1">
              <input id="is_active" v-model="form.is_active" type="checkbox" class="w-4 h-4 text-primary-600 rounded" />
              <label for="is_active" class="text-sm text-slate-700 dark:text-slate-300">Rekening aktif</label>
            </div>

            <div v-if="modalError" class="text-sm text-rose-600 bg-rose-50 dark:bg-rose-900/20 rounded-xl px-3 py-2">{{ modalError }}</div>

            <div class="flex gap-2 pt-2">
              <button type="button" @click="showModal = false" class="btn-secondary flex-1">Batal</button>
              <button type="submit" :disabled="submitting" class="btn-primary flex-1">
                {{ submitting ? 'Menyimpan...' : 'Simpan' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </Teleport>
  </div>
</template>
