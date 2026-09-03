<script setup lang="ts">
import { ref, onMounted, computed } from 'vue'
import { transactionsApi, transfersApi } from '@/api/transactions'
import { useAccountsStore } from '@/stores/accounts'
import { useCategoriesStore } from '@/stores/categories'
import { useUiStore } from '@/stores/ui'
import { useFormatCurrency } from '@/composables/useFormatCurrency'
import type { Transaction, TransactionPayload, Transfer, TransferPayload } from '@/types/transaction'
import type { ApiPagination } from '@/types/api'
import DateInput from '@/components/DateInput.vue'
import SelectInput from '@/components/SelectInput.vue'
import CurrencyInput from '@/components/CurrencyInput.vue'
import ReceiptScannerModal from '@/components/ReceiptScannerModal.vue'
import MoneySpinner from '@/components/MoneySpinner.vue'
import EmptyState from '@/components/EmptyState.vue'
import type { SelectOption } from '@/components/SelectInput.vue'
import {
  PlusIcon,
  PencilSquareIcon,
  TrashIcon,
  ArrowsRightLeftIcon,
  ChevronLeftIcon,
  ChevronRightIcon,
  MagnifyingGlassIcon,
  FunnelIcon,
  InboxIcon,
  XMarkIcon,
  CameraIcon,
  SparklesIcon,
  ArrowDownTrayIcon,
  ArrowRightIcon,
  BuildingLibraryIcon,
} from '@heroicons/vue/24/outline'

const { formatCurrency, formatAmount, formatDate } = useFormatCurrency()
const accountsStore = useAccountsStore()
const categoriesStore = useCategoriesStore()
const uiStore = useUiStore()

const showScanner = ref(false)
const activeTab = ref<'all' | 'transactions' | 'transfers'>('all')

// ── Transactions State ─────────────────────────────────────────
const transactions = ref<Transaction[]>([])
const pagination = ref<ApiPagination | null>(null)
const loading = ref(false)

// ── Transfers State ────────────────────────────────────────────
const transfers = ref<Transfer[]>([])
const transfersLoading = ref(false)
const transfersPagination = ref<ApiPagination | null>(null)

// ── Filters ────────────────────────────────────────────────────
const filters = ref({
  type: '' as string,
  account_id: '' as string,
  category_id: '' as string,
  date_from: '',
  date_to: '',
  search: '',
  sort: 'date',
  order: 'desc',
  per_page: 15,
  page: 1,
})

// ── Select options ─────────────────────────────────────────────
const typeOptions = computed<SelectOption[]>(() => [
  { value: '', label: 'Semua Jenis' },
  { value: 'income', label: 'Pemasukan' },
  { value: 'expense', label: 'Pengeluaran' },
  { value: 'transfer', label: 'Transfer Antar Rekening' },
])

const accountOptions = computed<SelectOption[]>(() => [
  { value: '', label: 'Semua Rekening' },
  ...accountsStore.accounts.map((a) => ({ value: String(a.id), label: a.name })),
])

const categoryOptions = computed<SelectOption[]>(() => [
  { value: '', label: 'Semua Kategori' },
  ...categoriesStore.categories.map((c) => ({ value: String(c.id), label: c.name, icon: c.icon })),
])

const sortOptions: SelectOption[] = [
  { value: 'date', label: 'Terbaru' },
  { value: 'amount_desc', label: 'Terbesar' },
  { value: 'amount_asc', label: 'Terkecil' },
]

export interface UnifiedRecord {
  id: number
  uniqueKey: string
  kind: 'transaction' | 'transfer'
  type: 'income' | 'expense' | 'transfer'
  amount: number
  fee?: number
  date: string
  description?: string | null
  accountName: string
  fromAccount?: { id: number; name: string; color?: string; icon?: string }
  toAccount?: { id: number; name: string; color?: string; icon?: string }
  categoryName: string
  categoryIcon: string
  categoryColor: string
  rawTransaction?: Transaction
  rawTransfer?: Transfer
}

const displayRecords = computed<UnifiedRecord[]>(() => {
  const list: UnifiedRecord[] = []

  // Include Transactions (if activeTab is 'all' or 'transactions', and filter permits)
  if (activeTab.value !== 'transfers' && filters.value.type !== 'transfer') {
    for (const tx of transactions.value) {
      if (filters.value.type && filters.value.type !== tx.type) continue
      if (filters.value.category_id && String(tx.category?.id) !== String(filters.value.category_id)) continue
      if (filters.value.account_id && String(tx.account?.id) !== String(filters.value.account_id)) continue
      if (filters.value.search && !((tx.description || '') + (tx.category?.name || '')).toLowerCase().includes(filters.value.search.toLowerCase())) continue

      list.push({
        id: tx.id,
        uniqueKey: `tx-${tx.id}`,
        kind: 'transaction',
        type: tx.type,
        amount: tx.amount,
        date: tx.date,
        description: tx.description,
        accountName: tx.account?.name ?? '—',
        categoryName: tx.category?.name ?? 'Umum',
        categoryIcon: tx.category?.icon ?? '📦',
        categoryColor: tx.category?.color ?? '#6366F1',
        rawTransaction: tx,
      })
    }
  }

  // Include Transfers (if activeTab is 'all' or 'transfers', and filter permits)
  if (activeTab.value !== 'transactions' && filters.value.type !== 'income' && filters.value.type !== 'expense' && !filters.value.category_id) {
    for (const t of transfers.value) {
      if (filters.value.account_id && String(t.from_account_id) !== String(filters.value.account_id) && String(t.to_account_id) !== String(filters.value.account_id)) continue
      if (filters.value.search && !((t.description || '') + (t.from_account?.name || '') + (t.to_account?.name || '')).toLowerCase().includes(filters.value.search.toLowerCase())) continue

      list.push({
        id: t.id,
        uniqueKey: `tr-${t.id}`,
        kind: 'transfer',
        type: 'transfer',
        amount: t.amount,
        fee: t.fee,
        date: t.date,
        description: t.description,
        accountName: `${t.from_account?.name ?? '—'} ➔ ${t.to_account?.name ?? '—'}`,
        fromAccount: t.from_account,
        toAccount: t.to_account,
        categoryName: 'Transfer Antar Rekening',
        categoryIcon: '⇄',
        categoryColor: '#0066FF',
        rawTransfer: t,
      })
    }
  }

  return list.sort((a, b) => {
    if (filters.value.sort === 'amount_desc') return b.amount - a.amount
    if (filters.value.sort === 'amount_asc') return a.amount - b.amount
    const cmp = b.date.localeCompare(a.date)
    if (cmp !== 0) return cmp
    return b.id - a.id
  })
})

// ── Modal ──────────────────────────────────────────────────────
const showModal = ref(false)
const modalType = ref<'income' | 'expense' | 'transfer'>('expense')
const editingId = ref<number | null>(null)
const submitting = ref(false)
const modalError = ref('')

const getLocalToday = () => new Date().toLocaleDateString('en-CA')

const txForm = ref<TransactionPayload>({
  type: 'expense', amount: 0,
  date: getLocalToday(),
  category_id: 0, account_id: 0, description: '',
})
const txCategoryId = ref('')
const txAccountId = ref('')

const transferForm = ref<TransferPayload>({
  from_account_id: 0, to_account_id: 0,
  amount: 0, fee: 0,
  date: getLocalToday(),
  description: '',
})
const transferFromId = ref('')
const transferToId = ref('')

// ── Fetch Transactions ─────────────────────────────────────────
async function fetchTransactions(force = false) {
  if (transactions.value.length === 0 || force) {
    loading.value = true
  }
  try {
    const clean: Record<string, any> = {}
    Object.entries(filters.value).forEach(([k, v]) => {
      if (v !== '' && v !== undefined && v !== null) {
        if (k === 'sort' && v === 'amount_desc') { clean.sort = 'amount'; clean.order = 'desc'; return }
        if (k === 'sort' && v === 'amount_asc') { clean.sort = 'amount'; clean.order = 'asc'; return }
        if (k === 'order') return
        clean[k] = v
      }
    })
    const { data } = await transactionsApi.list(clean)
    transactions.value = data.data
    pagination.value = data.meta
  } finally {
    loading.value = false
  }
}

// ── Fetch Transfers ────────────────────────────────────────────
async function fetchTransfers(page = 1) {
  transfersLoading.value = true
  try {
    const { data } = await transfersApi.list(page)
    transfers.value = data.data
    transfersPagination.value = data.meta
  } catch (err) {
    console.error('Failed to load transfers:', err)
  } finally {
    transfersLoading.value = false
  }
}

async function deleteTransfer(id: number) {
  if (!confirm('Hapus riwayat transfer ini? Saldo rekening pengirim & penerima akan otomatis dikembalikan ke nominal sebelumnya.')) return
  try {
    await transfersApi.delete(id)
    await Promise.all([
      fetchTransfers(),
      fetchTransactions(),
      accountsStore.fetchAccounts(),
    ])
    uiStore.showToast('Transfer berhasil dibatalkan dan saldo dikembalikan.', 'info')
  } catch (err: any) {
    uiStore.showToast(err.response?.data?.message ?? 'Gagal menghapus transfer.', 'error')
  }
}

onMounted(async () => {
  await Promise.all([
    fetchTransactions(),
    fetchTransfers(),
    accountsStore.fetchAccounts(),
    categoriesStore.fetchCategories(),
  ])
})

let searchTimer: ReturnType<typeof setTimeout>
function onSearchInput() {
  clearTimeout(searchTimer)
  searchTimer = setTimeout(() => { filters.value.page = 1; fetchTransactions() }, 350)
}
function onFilterChange() { filters.value.page = 1; fetchTransactions() }

function resetFilters() {
  Object.assign(filters.value, { type: '', account_id: '', category_id: '', date_from: '', date_to: '', search: '', page: 1 })
  fetchTransactions()
}

const hasActiveFilter = computed(() =>
  filters.value.date_from || filters.value.date_to || filters.value.search ||
  filters.value.type || filters.value.account_id || filters.value.category_id
)
const hasActiveFilters = hasActiveFilter

// ── Modal actions ──────────────────────────────────────────────
function openModal(type: 'income' | 'expense' | 'transfer') {
  modalType.value = type
  editingId.value = null
  modalError.value = ''
  txCategoryId.value = ''
  txAccountId.value = ''
  transferFromId.value = ''
  transferToId.value = ''
  const today = getLocalToday()
  txForm.value = { type: type === 'transfer' ? 'expense' : type, amount: 0, date: today, category_id: 0, account_id: 0, description: '' }
  transferForm.value = { from_account_id: 0, to_account_id: 0, amount: 0, fee: 0, date: today, description: '' }
  showModal.value = true
}

function editTransaction(tx: Transaction) {
  editingId.value = tx.id
  modalType.value = tx.type
  txCategoryId.value = String(tx.category.id)
  txAccountId.value = String(tx.account.id)
  txForm.value = { type: tx.type, amount: tx.amount, date: tx.date, category_id: tx.category.id, account_id: tx.account.id, description: tx.description ?? '' }
  modalError.value = ''
  showModal.value = true
}

async function submitTransaction() {
  submitting.value = true
  modalError.value = ''
  try {
    if (modalType.value === 'transfer') {
      transferForm.value.from_account_id = Number(transferFromId.value)
      transferForm.value.to_account_id = Number(transferToId.value)
      await transfersApi.create(transferForm.value)
      activeTab.value = 'transfers'
      uiStore.showToast('Transfer saldo berhasil dilakukan!')
    } else {
      txForm.value.category_id = Number(txCategoryId.value)
      txForm.value.account_id = Number(txAccountId.value)
      if (editingId.value) {
        await transactionsApi.update(editingId.value, txForm.value)
        uiStore.showToast('Transaksi berhasil diperbarui!')
      } else {
        await transactionsApi.create(txForm.value)
        uiStore.showToast(txForm.value.type === 'income' ? 'Pemasukan berhasil dicatat!' : 'Pengeluaran berhasil dicatat!')
      }
      activeTab.value = 'transactions'
    }

    // Close modal on success
    showModal.value = false

    // Safely sync data in background
    Promise.all([
      fetchTransactions(true),
      fetchTransfers(1),
      accountsStore.fetchAccounts(),
    ]).catch((err) => console.error('Sync failed:', err))
  } catch (err: any) {
    const data = err.response?.data
    if (data?.errors) {
      const msgs = Object.values(data.errors as Record<string, string[]>).flat()
      modalError.value = msgs.join(' ')
    } else {
      modalError.value = data?.message ?? 'Terjadi kesalahan saat menyimpan.'
    }
  } finally {
    submitting.value = false
  }
}

async function deleteTransaction(id: number) {
  if (!confirm('Hapus transaksi ini?')) return
  await transactionsApi.delete(id)
  await Promise.all([fetchTransactions(), accountsStore.fetchAccounts()])
  uiStore.showToast('Transaksi berhasil dihapus.', 'info')
}

function changePage(page: number) { filters.value.page = page; fetchTransactions() }

const visiblePages = computed(() => {
  if (!pagination.value) return []
  const { current_page, last_page } = pagination.value
  const pages: number[] = []
  for (let i = Math.max(1, current_page - 2); i <= Math.min(last_page, current_page + 2); i++) pages.push(i)
  return pages
})

// ── Select options for modal ───────────────────────────────────
const categoriesForType = (type: 'income' | 'expense') =>
  (type === 'income' ? categoriesStore.incomeCategories : categoriesStore.expenseCategories)
    .map((c) => ({ value: String(c.id), label: c.name, icon: c.icon } as SelectOption))

const activeAccountOptions = computed<SelectOption[]>(() =>
  accountsStore.activeAccounts.map((a) => ({
    value: String(a.id),
    label: `${a.name} (${formatCurrency(a.current_balance)})`,
  }))
)

const selectedFromAccount = computed(() =>
  accountsStore.accounts.find((a) => String(a.id) === String(transferFromId.value))
)

const selectedToAccount = computed(() =>
  accountsStore.accounts.find((a) => String(a.id) === String(transferToId.value))
)

const selectedTxAccount = computed(() =>
  accountsStore.accounts.find((a) => String(a.id) === String(txAccountId.value))
)

import api from '@/api/axios'

const exporting = ref(false)

async function handleExport() {
  exporting.value = true
  try {
    const res = await api.get('/transactions/export', {
      params: {
        type: filters.value.type || undefined,
        account_id: filters.value.account_id || undefined,
        category_id: filters.value.category_id || undefined,
        date_from: filters.value.date_from || undefined,
        date_to: filters.value.date_to || undefined,
      },
      responseType: 'blob',
    })

    const blob = new Blob([res.data], { type: 'text/csv;charset=utf-8;' })
    const url = window.URL.createObjectURL(blob)
    const a = document.createElement('a')
    a.href = url
    a.download = `FinanceFlow_Transaksi_${new Date().toISOString().slice(0, 10)}.csv`
    document.body.appendChild(a)
    a.click()
    a.remove()
    window.URL.revokeObjectURL(url)
  } catch (e) {
    alert('Gagal mengunduh file export transaksi.')
  } finally {
    exporting.value = false
  }
}
</script>

<template>
  <div class="space-y-4">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
      <div>
        <h1 class="text-xl font-bold text-slate-900 dark:text-white">Transaksi</h1>
        <p class="text-xs text-slate-400 dark:text-slate-500 mt-0.5">Catat dan pantau riwayat arus kas kamu</p>
      </div>

      <!-- Action Buttons (Responsive Grid on Mobile) -->
      <div class="flex flex-wrap sm:flex-nowrap gap-1.5 sm:gap-2">
        <button
          @click="handleExport"
          :disabled="exporting"
          class="flex items-center justify-center gap-1.5 px-2.5 sm:px-3 py-2 text-xs sm:text-sm font-semibold bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-700 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-700 shadow-sm transition-all active:scale-95"
          title="Export Transaksi ke CSV / Excel"
        >
          <ArrowDownTrayIcon class="w-3.5 sm:w-4 h-3.5 sm:h-4 text-slate-500" />
          <span>{{ exporting ? 'Mengunduh...' : 'Export' }}</span>
        </button>
        <button
          @click="showScanner = true"
          class="flex-1 sm:flex-initial flex items-center justify-center gap-1.5 px-3 sm:px-4 py-2 text-xs sm:text-sm font-semibold bg-gradient-to-r from-primary-600 to-blue-600 hover:from-primary-700 hover:to-blue-700 text-white rounded-xl shadow-md transition-all active:scale-95"
        >
          <CameraIcon class="w-4 h-4 shrink-0" />
          <span>Scan Struk</span>
        </button>
        <button
          @click="openModal('expense')"
          class="flex items-center justify-center gap-1.5 px-2.5 sm:px-3.5 py-2 text-xs sm:text-sm font-medium bg-slate-900 dark:bg-white text-white dark:text-slate-900 hover:bg-slate-800 dark:hover:bg-slate-100 rounded-xl shadow-sm transition-all active:scale-95"
        >
          <PlusIcon class="w-3.5 sm:w-4 h-3.5 sm:h-4 shrink-0" />
          <span>Pengeluaran</span>
        </button>
        <button
          @click="openModal('income')"
          class="flex items-center justify-center gap-1.5 px-2.5 sm:px-3.5 py-2 text-xs sm:text-sm font-medium bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl shadow-sm transition-all active:scale-95"
        >
          <PlusIcon class="w-3.5 sm:w-4 h-3.5 sm:h-4 shrink-0" />
          <span>Pemasukan</span>
        </button>
        <button
          @click="openModal('transfer')"
          class="flex items-center justify-center gap-1.5 px-2.5 sm:px-3.5 py-2 text-xs sm:text-sm font-medium bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 rounded-xl shadow-sm transition-all active:scale-95"
        >
          <ArrowsRightLeftIcon class="w-3.5 sm:w-4 h-3.5 sm:h-4 shrink-0" />
          <span>Transfer</span>
        </button>
      </div>
    </div>

    <!-- Quick Account Balance Strip (Real-time Balances) -->
    <div v-if="accountsStore.activeAccounts.length" class="space-y-1.5">
      <div class="flex items-center justify-between">
        <span class="text-[11px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Saldo Rekening Saat Ini</span>
        <RouterLink to="/rekening" class="text-xs font-semibold text-[#0066FF] hover:underline">Kelola Rekening →</RouterLink>
      </div>
      <div class="flex items-center gap-2.5 overflow-x-auto pb-1 scrollbar-none">
        <div
          v-for="acc in accountsStore.activeAccounts"
          :key="acc.id"
          class="flex items-center gap-2.5 px-3.5 py-2.5 rounded-2xl bg-white dark:bg-slate-800 border border-slate-200/80 dark:border-slate-700/60 shrink-0 shadow-xs hover:border-blue-300 dark:hover:border-blue-600 transition-colors"
        >
          <div
            class="w-8 h-8 rounded-xl flex items-center justify-center text-xs shrink-0 font-extrabold"
            :style="{ backgroundColor: (acc.color || '#0066FF') + '18', color: acc.color || '#0066FF' }"
          >
            {{ acc.name.charAt(0).toUpperCase() }}
          </div>
          <div>
            <p class="text-[11px] font-semibold text-slate-500 dark:text-slate-400 leading-none truncate max-w-[120px]">{{ acc.name }}</p>
            <p class="text-xs font-black text-slate-900 dark:text-white leading-tight mt-1 tabular-nums">
              {{ formatCurrency(acc.current_balance) }}
            </p>
          </div>
        </div>
      </div>
    </div>

    <!-- Tab Switcher: Semua Mutasi vs Arus Kas vs Transfer Antar Rekening -->
    <div class="flex items-center gap-2 border-b border-slate-200 dark:border-slate-700/80 pb-2 overflow-x-auto no-scrollbar">
      <button
        @click="activeTab = 'all'"
        :class="[
          'flex items-center gap-2 px-3.5 sm:px-4 py-2 text-xs sm:text-sm font-bold rounded-xl transition-all shrink-0',
          activeTab === 'all'
            ? 'bg-[#0066FF] text-white shadow-sm shadow-[#0066FF]/25'
            : 'text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800'
        ]"
      >
        <span>Semua Mutasi</span>
        <span
          class="text-[10px] px-2 py-0.5 rounded-full font-bold"
          :class="activeTab === 'all' ? 'bg-white/20 text-white' : 'bg-slate-100 dark:bg-slate-800 text-slate-500'"
        >
          {{ displayRecords.length }}
        </span>
      </button>

      <button
        @click="activeTab = 'transactions'"
        :class="[
          'flex items-center gap-2 px-3.5 sm:px-4 py-2 text-xs sm:text-sm font-bold rounded-xl transition-all shrink-0',
          activeTab === 'transactions'
            ? 'bg-[#0066FF] text-white shadow-sm shadow-[#0066FF]/25'
            : 'text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800'
        ]"
      >
        <span>Arus Kas (Transaksi)</span>
        <span
          class="text-[10px] px-2 py-0.5 rounded-full font-bold"
          :class="activeTab === 'transactions' ? 'bg-white/20 text-white' : 'bg-slate-100 dark:bg-slate-800 text-slate-500'"
        >
          {{ pagination?.total ?? transactions.length }}
        </span>
      </button>

      <button
        @click="activeTab = 'transfers'"
        :class="[
          'flex items-center gap-2 px-3.5 sm:px-4 py-2 text-xs sm:text-sm font-bold rounded-xl transition-all shrink-0',
          activeTab === 'transfers'
            ? 'bg-[#0066FF] text-white shadow-sm shadow-[#0066FF]/25'
            : 'text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800'
        ]"
      >
        <ArrowsRightLeftIcon class="w-4 h-4 shrink-0" />
        <span>Transfer Antar Rekening</span>
        <span
          class="text-[10px] px-2 py-0.5 rounded-full font-bold"
          :class="activeTab === 'transfers' ? 'bg-white/20 text-white' : 'bg-slate-100 dark:bg-slate-800 text-slate-500'"
        >
          {{ transfersPagination?.total ?? transfers.length }}
        </span>
      </button>
    </div>

    <!-- ================= VIEW 1: SEMUA MUTASI & TRANSAKSI ================= -->
    <template v-if="activeTab === 'all' || activeTab === 'transactions'">
      <!-- Quick Notice: Transfer Records (Only when viewing transactions tab specifically) -->
      <div
        v-if="activeTab === 'transactions' && transfers.length > 0"
        class="flex items-center justify-between px-3.5 sm:px-4 py-2.5 bg-blue-50/70 dark:bg-blue-950/40 border border-blue-200/60 dark:border-blue-800/50 rounded-2xl text-xs transition-all shadow-xs"
      >
        <div class="flex items-center gap-2 text-slate-700 dark:text-slate-300 min-w-0">
          <ArrowsRightLeftIcon class="w-4 h-4 text-[#0066FF] dark:text-blue-400 shrink-0" />
          <span class="truncate">Ada <strong>{{ transfersPagination?.total ?? transfers.length }} riwayat transfer antar rekening</strong> tersimpan.</span>
        </div>
        <button
          @click="activeTab = 'transfers'"
          class="font-bold text-[#0066FF] dark:text-blue-400 hover:underline shrink-0 ml-2 text-xs flex items-center gap-1"
        >
          <span>Buka Riwayat Transfer</span>
          <span>→</span>
        </button>
      </div>

      <!-- Filters Card -->
      <div class="card p-3.5 sm:p-4 space-y-3">
        <div class="flex items-center justify-between">
          <div class="flex items-center gap-2">
            <FunnelIcon class="w-4 h-4 text-slate-400" />
            <span class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wide">Filter</span>
          </div>
          <button v-if="hasActiveFilters" @click="resetFilters" class="text-xs text-rose-500 hover:underline">
            Reset Filter
          </button>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-2.5">
          <SelectInput v-model="filters.type" :options="typeOptions" @change="onFilterChange" placeholder="Semua Jenis" />
          <SelectInput v-model="filters.account_id" :options="accountOptions" @change="onFilterChange" placeholder="Semua Rekening" />
          <SelectInput v-model="filters.category_id" :options="categoryOptions" @change="onFilterChange" placeholder="Semua Kategori" />
          <DateInput v-model="filters.date_from" @update:modelValue="onFilterChange" placeholder="Dari Tanggal" />
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-2.5 pt-1">
          <DateInput v-model="filters.date_to" @update:modelValue="onFilterChange" placeholder="Sampai Tanggal" />
          <div class="relative sm:col-span-2">
            <MagnifyingGlassIcon class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" />
            <input
              v-model="filters.search"
              @input="onSearchInput"
              type="text"
              placeholder="Cari transaksi atau transfer..."
              class="input pl-9"
            />
          </div>
          <SelectInput v-model="filters.sort" :options="sortOptions" @change="onFilterChange" placeholder="Urutan" />
        </div>
      </div>

      <!-- Data Container (Mobile Card List + Desktop Table) -->
      <div class="card overflow-hidden">
        <div v-if="loading || transfersLoading" class="flex items-center justify-center p-8">
          <MoneySpinner size="md" text="Menyinkronkan mutasi keuangan..." />
        </div>

        <div v-else-if="!displayRecords.length" class="py-12">
          <EmptyState
            type="transactions"
            title="Belum ada mutasi keuangan nih"
            description="Yuk catat transaksi pengeluaran, pemasukan, atau transfer antar rekening pertamamu."
            action-text="Catat Transaksi Pertama"
            @action="openModal('expense')"
          />
        </div>

        <div v-else>
          <!-- Mobile List Card View (Shown only on small screens) -->
          <div class="divide-y divide-slate-100 dark:divide-slate-700/50 block md:hidden">
            <template v-for="rec in displayRecords" :key="rec.uniqueKey">
              <!-- If record is a Transfer -->
              <div
                v-if="rec.kind === 'transfer'"
                class="p-3.5 space-y-2.5 hover:bg-slate-50 dark:hover:bg-slate-700/20 transition-colors"
              >
                <div class="flex items-center justify-between">
                  <span class="inline-flex items-center gap-1 text-[10px] px-2 py-0.5 rounded-full font-bold bg-blue-50 text-[#0066FF] dark:bg-blue-950/60 dark:text-blue-300 border border-blue-100 dark:border-blue-900/50">
                    <ArrowsRightLeftIcon class="w-3 h-3" />
                    <span>Transfer Rekening</span>
                  </span>
                  <span class="text-sm font-black text-[#0066FF] dark:text-blue-400 tabular-nums">
                    {{ formatCurrency(rec.amount) }}
                  </span>
                </div>

                <!-- From -> To Flow -->
                <div class="flex items-center gap-2 text-xs font-semibold">
                  <span class="px-2 py-1 rounded-lg bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-200 truncate max-w-[130px]">
                    {{ rec.fromAccount?.name }}
                  </span>
                  <ArrowRightIcon class="w-3.5 h-3.5 text-slate-400 shrink-0" />
                  <span class="px-2 py-1 rounded-lg bg-blue-50 dark:bg-blue-950/40 text-[#0066FF] dark:text-blue-300 truncate max-w-[130px]">
                    {{ rec.toAccount?.name }}
                  </span>
                </div>

                <!-- Footer details & Cancel action -->
                <div class="flex items-center justify-between text-xs text-slate-400 pt-1 border-t border-slate-100 dark:border-slate-800">
                  <div class="text-[11px] truncate mr-2">
                    <span v-if="rec.fee && rec.fee > 0" class="text-rose-500 font-medium mr-1.5">
                      Biaya: {{ formatCurrency(rec.fee) }} (Total: {{ formatCurrency(rec.amount + rec.fee) }})
                    </span>
                    <span v-else class="text-emerald-600 dark:text-emerald-400 font-medium mr-1.5">Bebas Biaya Admin</span>
                    <span v-if="rec.description">· {{ rec.description }}</span>
                  </div>
                  <button
                    @click="deleteTransfer(rec.id)"
                    class="text-xs text-rose-500 hover:text-rose-700 font-bold transition-colors shrink-0"
                  >
                    Batalkan
                  </button>
                </div>
              </div>

              <!-- If record is a Transaction -->
              <div
                v-else
                class="p-3.5 flex items-center gap-3 hover:bg-slate-50 dark:hover:bg-slate-700/20 transition-colors"
              >
                <!-- Category Icon -->
                <div
                  class="w-10 h-10 rounded-xl flex items-center justify-center text-lg shrink-0"
                  :style="{ backgroundColor: (rec.categoryColor || '#6366F1') + '18' }"
                >
                  {{ rec.categoryIcon }}
                </div>

                <!-- Details -->
                <div class="flex-1 min-w-0">
                  <div class="flex items-center justify-between gap-1">
                    <p class="text-sm font-semibold text-slate-900 dark:text-white truncate">
                      {{ rec.categoryName }}
                    </p>
                    <p
                      class="text-sm font-bold shrink-0 tabular-nums"
                      :class="rec.type === 'income' ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-500 dark:text-rose-400'"
                    >
                      {{ formatAmount(rec.amount, rec.type) }}
                    </p>
                  </div>

                  <div class="flex items-center justify-between text-xs text-slate-400 dark:text-slate-500 mt-0.5">
                    <p class="truncate">{{ rec.accountName }} <span v-if="rec.description">· {{ rec.description }}</span></p>
                    <span class="shrink-0 ml-2">{{ formatDate(rec.date) }}</span>
                  </div>
                </div>

                <!-- Actions -->
                <div class="flex items-center gap-0.5 shrink-0 ml-1">
                  <button
                    @click="editTransaction(rec.rawTransaction!)"
                    class="p-1.5 rounded-lg text-slate-400 hover:text-primary-600 active:bg-slate-100 dark:active:bg-slate-700 transition-colors"
                  >
                    <PencilSquareIcon class="w-4 h-4" />
                  </button>
                  <button
                    @click="deleteTransaction(rec.id)"
                    class="p-1.5 rounded-lg text-slate-400 hover:text-rose-600 active:bg-slate-100 dark:active:bg-slate-700 transition-colors"
                  >
                    <TrashIcon class="w-4 h-4" />
                  </button>
                </div>
              </div>
            </template>
          </div>

          <!-- Desktop Table View (Hidden on mobile) -->
          <table class="w-full text-sm hidden md:table">
            <thead class="border-b border-slate-100 dark:border-slate-700/60">
              <tr>
                <th class="text-left px-4 py-3 text-xs font-semibold text-slate-400 dark:text-slate-500 uppercase tracking-wide">Tanggal</th>
                <th class="text-left px-4 py-3 text-xs font-semibold text-slate-400 dark:text-slate-500 uppercase tracking-wide">Kategori / Jenis</th>
                <th class="text-left px-4 py-3 text-xs font-semibold text-slate-400 dark:text-slate-500 uppercase tracking-wide">Rekening / Alur</th>
                <th class="text-left px-4 py-3 text-xs font-semibold text-slate-400 dark:text-slate-500 uppercase tracking-wide hidden lg:table-cell">Keterangan</th>
                <th class="text-right px-4 py-3 text-xs font-semibold text-slate-400 dark:text-slate-500 uppercase tracking-wide">Jumlah</th>
                <th class="px-4 py-3 w-16"></th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-700/40">
              <tr v-for="rec in displayRecords" :key="rec.uniqueKey" class="hover:bg-slate-50/50 dark:hover:bg-slate-700/20 transition-colors group">
                <td class="px-4 py-3.5 text-xs text-slate-400 dark:text-slate-500 whitespace-nowrap">{{ formatDate(rec.date) }}</td>

                <!-- Kategori / Jenis -->
                <td class="px-4 py-3.5">
                  <div v-if="rec.kind === 'transfer'" class="flex items-center gap-2">
                    <span class="flex items-center justify-center w-7 h-7 rounded-lg text-xs bg-blue-50 dark:bg-blue-950/60 text-[#0066FF] dark:text-blue-400 shrink-0">
                      <ArrowsRightLeftIcon class="w-4 h-4" />
                    </span>
                    <div>
                      <span class="font-bold text-slate-900 dark:text-white text-xs sm:text-sm">Transfer Antar Rekening</span>
                      <span v-if="rec.fee && rec.fee > 0" class="block text-[10px] text-rose-500 font-medium">Biaya: {{ formatCurrency(rec.fee) }}</span>
                    </div>
                  </div>
                  <div v-else class="flex items-center gap-2">
                    <span class="flex items-center justify-center w-7 h-7 rounded-lg text-sm shrink-0" :style="{ backgroundColor: (rec.categoryColor || '#6366F1') + '18' }">
                      {{ rec.categoryIcon }}
                    </span>
                    <span class="font-medium text-slate-800 dark:text-slate-200">{{ rec.categoryName }}</span>
                  </div>
                </td>

                <!-- Rekening -->
                <td class="px-4 py-3.5">
                  <div v-if="rec.kind === 'transfer'" class="flex items-center gap-1.5 text-xs">
                    <span class="font-semibold px-2 py-0.5 rounded bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-200">{{ rec.fromAccount?.name }}</span>
                    <ArrowRightIcon class="w-3 h-3 text-slate-400" />
                    <span class="font-semibold px-2 py-0.5 rounded bg-blue-50 dark:bg-blue-950/40 text-[#0066FF] dark:text-blue-300">{{ rec.toAccount?.name }}</span>
                  </div>
                  <span v-else class="text-sm text-slate-500 dark:text-slate-400">{{ rec.accountName }}</span>
                </td>

                <!-- Keterangan -->
                <td class="px-4 py-3.5 hidden lg:table-cell">
                  <span class="text-xs text-slate-400 dark:text-slate-500 line-clamp-1 max-w-xs">{{ rec.description || '—' }}</span>
                </td>

                <!-- Jumlah -->
                <td class="px-4 py-3.5 text-right font-bold whitespace-nowrap tabular-nums"
                  :class="rec.type === 'income' ? 'text-emerald-600 dark:text-emerald-400' : rec.type === 'transfer' ? 'text-[#0066FF] dark:text-blue-400' : 'text-rose-500 dark:text-rose-400'"
                >
                  {{ rec.type === 'transfer' ? formatCurrency(rec.amount) : formatAmount(rec.amount, rec.type) }}
                </td>

                <!-- Aksi -->
                <td class="px-4 py-3.5">
                  <div class="flex items-center justify-end gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                    <template v-if="rec.kind === 'transfer'">
                      <button @click="deleteTransfer(rec.id)" class="p-1.5 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-900/20 transition-colors" title="Batalkan Transfer">
                        <TrashIcon class="w-4 h-4" />
                      </button>
                    </template>
                    <template v-else>
                      <button @click="editTransaction(rec.rawTransaction!)" class="p-1.5 rounded-lg text-slate-400 hover:text-primary-600 hover:bg-primary-50 dark:hover:bg-primary-900/20 transition-colors" title="Edit">
                        <PencilSquareIcon class="w-4 h-4" />
                      </button>
                      <button @click="deleteTransaction(rec.id)" class="p-1.5 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-900/20 transition-colors" title="Hapus">
                        <TrashIcon class="w-4 h-4" />
                      </button>
                    </template>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Pagination / Count Footer -->
        <div class="flex items-center justify-between px-4 py-3 border-t border-slate-100 dark:border-slate-700/60 text-xs text-slate-400">
          <p v-if="activeTab === 'all'">
            Menampilkan {{ displayRecords.length }} mutasi (transaksi & transfer)
          </p>
          <p v-else-if="pagination">
            {{ pagination.from }}–{{ pagination.to }} dari {{ pagination.total }} transaksi
          </p>
          <div v-if="activeTab === 'transactions' && pagination && pagination.last_page > 1" class="flex gap-1">
            <button :disabled="pagination.current_page === 1" @click="changePage(pagination.current_page - 1)" class="p-1.5 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-100 dark:hover:bg-slate-700 disabled:opacity-30 disabled:cursor-not-allowed">
              <ChevronLeftIcon class="w-4 h-4" />
            </button>
            <button v-for="p in visiblePages" :key="p" @click="changePage(p)" :class="['w-7 h-7 text-xs rounded-lg font-medium transition-colors', p === pagination.current_page ? 'bg-primary-600 text-white' : 'text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-700']">{{ p }}</button>
            <button :disabled="pagination.current_page === pagination.last_page" @click="changePage(pagination.current_page + 1)" class="p-1.5 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-100 dark:hover:bg-slate-700 disabled:opacity-30 disabled:cursor-not-allowed">
              <ChevronRightIcon class="w-4 h-4" />
            </button>
          </div>
        </div>
      </div>
    </template>

    <!-- ================= VIEW 2: TRANSFERS (TRANSFER ANTAR REKENING) ================= -->
    <template v-else>
      <div class="card overflow-hidden">
        <div v-if="transfersLoading" class="flex items-center justify-center p-8">
          <MoneySpinner size="md" text="Menyinkronkan riwayat transfer..." />
        </div>

        <div v-else-if="!transfers.length" class="py-12">
          <EmptyState
            type="transactions"
            title="Belum ada riwayat transfer"
            description="Pindahkan dana antar rekening untuk mencatat mutasi uang secara rapi."
            action-text="Transfer Sekarang"
            @action="openModal('transfer')"
          />
        </div>

        <div v-else>
          <!-- Mobile Cards for Transfers -->
          <div class="p-3 space-y-3 block md:hidden">
            <div
              v-for="t in transfers"
              :key="t.id"
              class="p-4 space-y-3 hover:bg-slate-50/70 dark:hover:bg-slate-700/20 transition-all rounded-2xl border border-slate-100 dark:border-slate-800/70 bg-white/50 dark:bg-slate-900/30 shadow-xs"
            >
              <!-- Card Header -->
              <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                  <div class="w-8 h-8 rounded-xl bg-blue-50 dark:bg-blue-950/60 text-[#0066FF] dark:text-blue-400 flex items-center justify-center font-bold">
                    <ArrowsRightLeftIcon class="w-4 h-4" />
                  </div>
                  <div>
                    <p class="text-xs font-bold text-slate-900 dark:text-white">Transfer Antar Rekening</p>
                    <p class="text-[10px] text-slate-400">{{ formatDate(t.date) }}</p>
                  </div>
                </div>

                <div class="text-right">
                  <p class="text-sm sm:text-base font-black text-[#0066FF] dark:text-blue-400 tabular-nums">
                    {{ formatCurrency(t.amount) }}
                  </p>
                  <span class="inline-block text-[10px] font-semibold text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/40 px-1.5 py-0.5 rounded-md">
                    Berhasil
                  </span>
                </div>
              </div>

              <!-- From -> To Accounts Flow -->
              <div class="p-2.5 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-100 dark:border-slate-700/50 flex items-center justify-between gap-2 text-xs">
                <div class="flex items-center gap-1.5 min-w-0">
                  <span class="w-2 h-2 rounded-full shrink-0" :style="{ backgroundColor: t.from_account?.color || '#94a3b8' }"></span>
                  <span class="font-semibold text-slate-800 dark:text-slate-200 truncate max-w-[120px]">{{ t.from_account?.name }}</span>
                </div>

                <div class="flex items-center gap-1 text-slate-400 shrink-0">
                  <span class="text-[10px] font-medium">ke</span>
                  <ArrowRightIcon class="w-3.5 h-3.5 text-[#0066FF]" />
                </div>

                <div class="flex items-center gap-1.5 min-w-0">
                  <span class="w-2 h-2 rounded-full shrink-0" :style="{ backgroundColor: t.to_account?.color || '#0066FF' }"></span>
                  <span class="font-semibold text-[#0066FF] dark:text-blue-300 truncate max-w-[120px]">{{ t.to_account?.name }}</span>
                </div>
              </div>

              <!-- Financial Breakdown -->
              <div class="text-[11px] space-y-1 pt-1 text-slate-500 dark:text-slate-400">
                <div class="flex items-center justify-between">
                  <span>Biaya Admin:</span>
                  <span :class="t.fee > 0 ? 'text-rose-500 font-semibold' : 'text-emerald-600 dark:text-emerald-400 font-semibold'">
                    {{ t.fee > 0 ? formatCurrency(t.fee) : 'Bebas Biaya (Rp 0)' }}
                  </span>
                </div>
                <div class="flex items-center justify-between font-semibold text-slate-700 dark:text-slate-300 border-t border-slate-100 dark:border-slate-800 pt-1">
                  <span>Total Dana Keluar:</span>
                  <span class="tabular-nums text-slate-900 dark:text-white font-bold">{{ formatCurrency(t.amount + (t.fee || 0)) }}</span>
                </div>
                <p v-if="t.description" class="text-[11px] text-slate-400 italic pt-0.5">
                  "{{ t.description }}"
                </p>
              </div>

              <!-- Actions -->
              <div class="flex items-center justify-end pt-2 border-t border-slate-100 dark:border-slate-800">
                <button
                  @click="deleteTransfer(t.id)"
                  class="text-xs text-rose-500 hover:text-rose-700 font-semibold px-2.5 py-1 rounded-lg hover:bg-rose-50 dark:hover:bg-rose-950/40 transition-colors flex items-center gap-1"
                >
                  <TrashIcon class="w-3.5 h-3.5" />
                  <span>Batalkan Transfer</span>
                </button>
              </div>
            </div>
          </div>

          <!-- Desktop Table for Transfers -->
          <table class="w-full text-sm hidden md:table">
            <thead class="border-b border-slate-100 dark:border-slate-700/60">
              <tr>
                <th class="text-left px-4 py-3 text-xs font-semibold text-slate-400 dark:text-slate-500 uppercase tracking-wide">Tanggal</th>
                <th class="text-left px-4 py-3 text-xs font-semibold text-slate-400 dark:text-slate-500 uppercase tracking-wide">Rekening Asal</th>
                <th class="text-left px-4 py-3 text-xs font-semibold text-slate-400 dark:text-slate-500 uppercase tracking-wide">Rekening Tujuan</th>
                <th class="text-left px-4 py-3 text-xs font-semibold text-slate-400 dark:text-slate-500 uppercase tracking-wide hidden lg:table-cell">Keterangan</th>
                <th class="text-right px-4 py-3 text-xs font-semibold text-slate-400 dark:text-slate-500 uppercase tracking-wide">Biaya Admin</th>
                <th class="text-right px-4 py-3 text-xs font-semibold text-slate-400 dark:text-slate-500 uppercase tracking-wide">Jumlah Ditransfer</th>
                <th class="px-4 py-3 w-16"></th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-700/40">
              <tr v-for="t in transfers" :key="t.id" class="hover:bg-slate-50/50 dark:hover:bg-slate-700/20 transition-colors group">
                <td class="px-4 py-3.5 text-xs text-slate-400 dark:text-slate-500 whitespace-nowrap">{{ formatDate(t.date) }}</td>
                <td class="px-4 py-3.5">
                  <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-slate-100 dark:bg-slate-700/70 font-semibold text-slate-800 dark:text-slate-200">
                    <span class="w-2 h-2 rounded-full" :style="{ backgroundColor: t.from_account?.color || '#94a3b8' }"></span>
                    {{ t.from_account?.name }}
                  </span>
                </td>
                <td class="px-4 py-3.5">
                  <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-blue-50 dark:bg-blue-950/40 font-semibold text-[#0066FF] dark:text-blue-300 border border-blue-100 dark:border-blue-900/40">
                    <span class="w-2 h-2 rounded-full" :style="{ backgroundColor: t.to_account?.color || '#0066FF' }"></span>
                    {{ t.to_account?.name }}
                  </span>
                </td>
                <td class="px-4 py-3.5 hidden lg:table-cell text-xs text-slate-400 dark:text-slate-500">
                  {{ t.description || '—' }}
                </td>
                <td class="px-4 py-3.5 text-right text-xs tabular-nums">
                  <span v-if="t.fee > 0" class="text-rose-500 font-semibold">+ {{ formatCurrency(t.fee) }}</span>
                  <span v-else class="text-emerald-600 dark:text-emerald-400 font-medium">Rp 0 (Gratis)</span>
                </td>
                <td class="px-4 py-3.5 text-right font-black text-slate-900 dark:text-white tabular-nums text-base">
                  {{ formatCurrency(t.amount) }}
                </td>
                <td class="px-4 py-3.5 text-right">
                  <button
                    @click="deleteTransfer(t.id)"
                    class="p-1.5 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-900/20 transition-colors opacity-0 group-hover:opacity-100"
                    title="Batalkan & Kembalikan Saldo"
                  >
                    <TrashIcon class="w-4 h-4" />
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </template>

    <!-- Modal (Bottom Sheet on Mobile, Centered Modal on Desktop) -->
    <Teleport to="body">
      <div v-if="showModal" class="fixed inset-0 z-50 flex items-end sm:items-center justify-center p-0 sm:p-4">
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" @click="showModal = false"></div>
        <div class="relative bg-white dark:bg-slate-800 rounded-t-3xl sm:rounded-2xl p-5 sm:p-6 w-full max-w-md shadow-2xl border border-slate-200/60 dark:border-slate-700 max-h-[90vh] overflow-y-auto">
          <!-- Mobile Drag Handle -->
          <div class="w-10 h-1 bg-slate-200 dark:bg-slate-700 rounded-full mx-auto mb-3 sm:hidden"></div>

          <div class="flex items-center justify-between mb-5">
            <h3 class="text-base font-bold text-slate-900 dark:text-white">
              {{ editingId ? 'Ubah' : 'Tambah' }}
              {{ modalType === 'income' ? 'Pemasukan' : modalType === 'expense' ? 'Pengeluaran' : 'Transfer' }}
            </h3>
            <button @click="showModal = false" class="p-1.5 rounded-lg text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors">
              <XMarkIcon class="w-5 h-5" />
            </button>
          </div>

          <!-- Transfer form -->
          <form v-if="modalType === 'transfer'" @submit.prevent="submitTransaction" class="space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
              <div>
                <label class="label">Dari Rekening</label>
                <SelectInput v-model="transferFromId" :options="activeAccountOptions" placeholder="Pilih rekening" />
                <div v-if="selectedFromAccount" class="mt-1 flex items-center justify-between text-[11px] font-semibold text-slate-500 dark:text-slate-400 px-0.5">
                  <span>Saldo tersedia:</span>
                  <span class="font-bold text-slate-900 dark:text-white tabular-nums">{{ formatCurrency(selectedFromAccount.current_balance) }}</span>
                </div>
              </div>
              <div>
                <label class="label">Ke Rekening</label>
                <SelectInput v-model="transferToId" :options="activeAccountOptions" placeholder="Pilih rekening" />
                <div v-if="selectedToAccount" class="mt-1 flex items-center justify-between text-[11px] font-semibold text-slate-500 dark:text-slate-400 px-0.5">
                  <span>Saldo saat ini:</span>
                  <span class="font-bold text-slate-900 dark:text-white tabular-nums">{{ formatCurrency(selectedToAccount.current_balance) }}</span>
                </div>
              </div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
              <div>
                <label class="label">Jumlah</label>
                <CurrencyInput v-model="transferForm.amount" :required="true" />
              </div>
              <div>
                <label class="label">Biaya Transfer</label>
                <CurrencyInput v-model="transferForm.fee" />
              </div>
            </div>
            <div>
              <label class="label">Tanggal</label>
              <DateInput v-model="transferForm.date" placeholder="Pilih tanggal" />
            </div>
            <div>
              <label class="label">Keterangan <span class="text-slate-400 font-normal">(opsional)</span></label>
              <input v-model="transferForm.description" type="text" class="input" placeholder="Catatan..." />
            </div>
            <div v-if="modalError" class="text-sm text-rose-600 bg-rose-50 dark:bg-rose-900/20 rounded-xl px-3 py-2">{{ modalError }}</div>
            <div class="flex gap-2 pt-1">
              <button type="button" @click="showModal = false" class="btn-secondary flex-1">Batal</button>
              <button type="submit" :disabled="submitting" class="flex-1 flex items-center justify-center gap-2 px-4 py-2.5 bg-amber-500 hover:bg-amber-600 text-white font-medium rounded-xl transition-colors disabled:opacity-50 shadow-sm">
                <ArrowsRightLeftIcon class="w-4 h-4" />
                {{ submitting ? 'Memproses...' : 'Transfer' }}
              </button>
            </div>
          </form>

          <!-- Income / Expense form -->
          <form v-else @submit.prevent="submitTransaction" class="space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
              <div>
                <label class="label">Jumlah</label>
                <CurrencyInput v-model="txForm.amount" :required="true" />
              </div>
              <div>
                <label class="label">Tanggal</label>
                <DateInput v-model="txForm.date" placeholder="Pilih tanggal" />
              </div>
            </div>
            <div>
              <label class="label">Kategori</label>
              <SelectInput
                v-model="txCategoryId"
                :options="categoriesForType(modalType as 'income' | 'expense')"
                placeholder="Pilih kategori"
              />
            </div>
            <div>
              <label class="label">Rekening</label>
              <SelectInput v-model="txAccountId" :options="activeAccountOptions" placeholder="Pilih rekening" />
              <div v-if="selectedTxAccount" class="mt-1 flex items-center justify-between text-[11px] font-semibold text-slate-500 dark:text-slate-400 px-0.5">
                <span>Saldo saat ini:</span>
                <span class="font-bold text-slate-900 dark:text-white tabular-nums">{{ formatCurrency(selectedTxAccount.current_balance) }}</span>
              </div>
            </div>
            <div>
              <label class="label">Keterangan <span class="text-slate-400 font-normal">(opsional)</span></label>
              <input v-model="txForm.description" type="text" class="input" placeholder="Catatan transaksi..." />
            </div>
            <div v-if="modalError" class="text-sm text-rose-600 bg-rose-50 dark:bg-rose-900/20 rounded-xl px-3 py-2">{{ modalError }}</div>
            <div class="flex gap-2 pt-1">
              <button type="button" @click="showModal = false" class="btn-secondary flex-1">Batal</button>
              <button type="submit" :disabled="submitting" :class="['flex-1 py-2.5 font-medium rounded-xl transition-colors disabled:opacity-50 shadow-sm', modalType === 'income' ? 'bg-emerald-600 hover:bg-emerald-700 text-white' : 'bg-primary-600 hover:bg-primary-700 text-white']">
                {{ submitting ? 'Menyimpan...' : 'Simpan' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </Teleport>

    <!-- Smart Receipt Scanner Modal -->
    <ReceiptScannerModal v-model="showScanner" @saved="fetchTransactions" />
  </div>
</template>
