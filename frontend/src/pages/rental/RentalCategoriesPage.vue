<script setup lang="ts">
import { ref, onMounted, computed } from 'vue'
import api from '@/api/axios'
import { useUiStore } from '@/stores/ui'
import type { Category, CategoryPayload } from '@/types/category'
import MoneySpinner from '@/components/MoneySpinner.vue'
import SelectInput, { type SelectOption } from '@/components/SelectInput.vue'
import {
  PlusIcon,
  PencilSquareIcon,
  TrashIcon,
  TagIcon,
  XMarkIcon,
  CheckIcon,
} from '@heroicons/vue/24/outline'

const uiStore = useUiStore()

const activeTab = ref<'expense' | 'income'>('expense')
const categories = ref<Category[]>([])
const loading = ref(false)

const categoryTypeOptions: SelectOption[] = [
  { value: 'expense', label: 'Pengeluaran Operasional', icon: '🔴' },
  { value: 'income', label: 'Pemasukan Sewa', icon: '🟢' },
]

const showModal = ref(false)
const editingId = ref<number | null>(null)
const submitting = ref(false)
const modalError = ref('')

const presetColors = [
  '#2563EB', '#0D9488', '#16A34A', '#D97706',
  '#DC2626', '#475569', '#0284C7', '#0F172A'
]

const presetExpenseIcons = ['⛽', '🔧', '🧼', '💵', '🛣️', '📄', '⚙️', '🛡️', '📦', '🚦', '🔑', '🏷️']
const presetIncomeIcons = ['🚗', '👨‍✈️', '🛫', '⏱️', '🛡️', '📦', '💰', '🔑', '🤝', '🏷️']

const defaultForm: CategoryPayload & { is_rental: boolean } = {
  name: '',
  type: 'expense',
  icon: '⛽',
  color: '#2563EB',
  is_rental: true,
}
const form = ref({ ...defaultForm })

onMounted(() => fetchCategories())

async function fetchCategories() {
  loading.value = true
  try {
    const { data } = await api.get('/categories', { params: { mode: 'rental' } })
    categories.value = data.data ?? data ?? []
  } finally {
    loading.value = false
  }
}

const displayedCategories = computed(() =>
  categories.value.filter((c) => c.type === activeTab.value)
)

function openCreate() {
  editingId.value = null
  form.value = {
    ...defaultForm,
    type: activeTab.value,
    icon: activeTab.value === 'expense' ? '⛽' : '🚗',
  }
  modalError.value = ''
  showModal.value = true
}

function openEdit(cat: Category) {
  editingId.value = cat.id
  form.value = {
    name: cat.name,
    type: cat.type,
    icon: cat.icon || (cat.type === 'expense' ? '⛽' : '🚗'),
    color: cat.color || '#2563EB',
    is_rental: true,
  }
  modalError.value = ''
  showModal.value = true
}

async function submitCategory() {
  if (!form.value.name.trim()) {
    modalError.value = 'Nama kategori wajib diisi.'
    return
  }
  submitting.value = true
  modalError.value = ''
  try {
    if (editingId.value) {
      await api.put(`/categories/${editingId.value}`, form.value)
      uiStore.showToast('Kategori rental berhasil diperbarui!')
    } else {
      await api.post('/categories', { ...form.value, is_rental: true })
      uiStore.showToast('Kategori rental baru berhasil ditambahkan!')
    }
    showModal.value = false
    fetchCategories()
  } catch (err: any) {
    const data = err.response?.data
    if (data?.errors) {
      modalError.value = Object.values(data.errors as Record<string, string[]>).flat().join(' ')
    } else {
      modalError.value = data?.message ?? 'Gagal menyimpan kategori.'
    }
  } finally {
    submitting.value = false
  }
}

async function deleteCategory(cat: Category) {
  if (!confirm(`Hapus kategori "${cat.name}"?`)) return
  try {
    await api.delete(`/categories/${cat.id}`)
    uiStore.showToast('Kategori rental berhasil dihapus.', 'info')
    fetchCategories()
  } catch (err: any) {
    uiStore.showToast(err.response?.data?.message ?? 'Kategori tidak dapat dihapus karena telah memiliki riwayat transaksi.', 'error')
  }
}
</script>

<template>
  <div class="space-y-5">
    <!-- Header -->
    <div class="flex items-center justify-between gap-3">
      <div>
        <h1 class="text-lg sm:text-xl font-bold text-slate-900 dark:text-white">Kategori Rental Mobil</h1>
        <p class="text-[11px] sm:text-xs text-slate-500 dark:text-slate-400 mt-0.5">
          Atur pos biaya operasional dan pos sewa masuk armada Anda
        </p>
      </div>

      <button
        @click="openCreate"
        class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 dark:bg-white dark:hover:bg-slate-100 text-white dark:text-slate-900 text-xs sm:text-sm font-semibold shadow-sm transition-all shrink-0"
      >
        <PlusIcon class="w-4 h-4" />
        <span>Tambah</span>
      </button>
    </div>

    <!-- Tabs: Biaya Operasional vs Sewa Masuk -->
    <div class="flex p-1 bg-slate-100 dark:bg-slate-800 rounded-xl max-w-sm">
      <button
        @click="activeTab = 'expense'"
        :class="[
          'flex-1 py-2 text-xs sm:text-sm font-semibold rounded-lg transition-all',
          activeTab === 'expense'
            ? 'bg-white dark:bg-slate-700 text-slate-900 dark:text-white shadow-sm'
            : 'text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200'
        ]"
      >
        Biaya Operasional ({{ categories.filter(c => c.type === 'expense').length }})
      </button>
      <button
        @click="activeTab = 'income'"
        :class="[
          'flex-1 py-2 text-xs sm:text-sm font-semibold rounded-lg transition-all',
          activeTab === 'income'
            ? 'bg-white dark:bg-slate-700 text-slate-900 dark:text-white shadow-sm'
            : 'text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200'
        ]"
      >
        Sewa Masuk ({{ categories.filter(c => c.type === 'income').length }})
      </button>
    </div>

    <!-- Loading -->
    <div v-if="loading" class="card p-12 flex items-center justify-center">
      <MoneySpinner size="md" text="Memuat kategori rental..." />
    </div>

    <!-- Empty State -->
    <div v-else-if="displayedCategories.length === 0" class="card p-12 flex flex-col items-center text-center">
      <div class="w-14 h-14 bg-slate-100 dark:bg-slate-700/60 rounded-2xl flex items-center justify-center mb-4">
        <TagIcon class="w-7 h-7 text-slate-400" />
      </div>
      <p class="text-sm font-semibold text-slate-700 dark:text-slate-300">Belum ada kategori {{ activeTab === 'income' ? 'sewa masuk' : 'biaya operasional' }}</p>
      <p class="text-xs text-slate-400 dark:text-slate-500 mt-1 mb-4 max-w-xs">
        Tambahkan kategori agar transaksi rental armada Anda dapat dikelompokkan dengan rapi.
      </p>
      <button
        @click="openCreate"
        class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 dark:bg-white dark:hover:bg-slate-100 text-white dark:text-slate-900 text-xs sm:text-sm font-semibold shadow-sm transition-all"
      >
        <PlusIcon class="w-4 h-4" />
        Tambah Kategori Pertama
      </button>
    </div>

    <!-- Categories Grid -->
    <div v-else class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
      <div
        v-for="c in displayedCategories"
        :key="c.id"
        class="card p-4 hover:shadow-md transition-all group flex items-center justify-between"
      >
        <div class="flex items-center gap-3 min-w-0">
          <div
            class="w-10 h-10 rounded-xl flex items-center justify-center text-lg shrink-0"
            :style="{ backgroundColor: (c.color || '#2563EB') + '20' }"
          >
            {{ c.icon || '🏷️' }}
          </div>
          <div class="min-w-0">
            <h3 class="text-sm font-bold text-slate-900 dark:text-white truncate">
              {{ c.name }}
            </h3>
            <span class="text-[11px] text-slate-400">
              {{ c.type === 'income' ? 'Pemasukan' : 'Pengeluaran' }}
            </span>
          </div>
        </div>

        <!-- Actions -->
        <div class="flex items-center gap-1 opacity-100 sm:opacity-0 group-hover:opacity-100 transition-opacity">
          <button
            @click="openEdit(c)"
            class="p-1.5 rounded-lg text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700/60 transition-colors"
            title="Edit Kategori"
          >
            <PencilSquareIcon class="w-4 h-4" />
          </button>
          <button
            @click="deleteCategory(c)"
            class="p-1.5 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-900/20 transition-colors"
            title="Hapus Kategori"
          >
            <TrashIcon class="w-4 h-4" />
          </button>
        </div>
      </div>
    </div>

    <!-- Modal Form: Clean Neutral Styling (No Purple!) -->
    <Teleport to="body">
      <div v-if="showModal" class="fixed inset-0 z-50 flex items-end sm:items-center justify-center p-0 sm:p-4">
        <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" @click="showModal = false"></div>
        <div class="relative bg-white dark:bg-[#182234] rounded-t-3xl sm:rounded-2xl p-5 sm:p-6 w-full max-w-md shadow-2xl border border-slate-200/80 dark:border-slate-700 max-h-[92vh] overflow-y-auto">
          <!-- Mobile drag bar -->
          <div class="w-10 h-1 bg-slate-200 dark:bg-slate-700 rounded-full mx-auto mb-3 sm:hidden"></div>

          <div class="flex items-center justify-between mb-5">
            <h3 class="text-base sm:text-lg font-bold text-slate-900 dark:text-white">
              {{ editingId ? 'Ubah Kategori Rental' : 'Tambah Kategori Rental Baru' }}
            </h3>
            <button @click="showModal = false" class="p-1.5 rounded-lg text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
              <XMarkIcon class="w-5 h-5" />
            </button>
          </div>

          <form @submit.prevent="submitCategory" class="space-y-4">
            <div>
              <label class="block text-xs font-semibold text-slate-600 dark:text-slate-300 mb-1.5">Jenis Transaksi</label>
              <SelectInput
                v-model="form.type"
                :options="categoryTypeOptions"
                placeholder="Pilih Jenis Transaksi..."
              />
            </div>

            <!-- Nama Kategori -->
            <div>
              <label class="block text-xs font-semibold text-slate-600 dark:text-slate-300 mb-1.5">
                Nama Kategori <span class="text-rose-500">*</span>
              </label>
              <input
                v-model="form.name"
                type="text"
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800/80 text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 text-sm focus:outline-none focus:ring-2 focus:ring-slate-400 dark:focus:ring-slate-500 focus:bg-white dark:focus:bg-slate-800 transition-all"
                :placeholder="form.type === 'expense' ? 'cth. Servis Dinamo / AC' : 'cth. Sewa Wedding Car'"
                required
              />
            </div>

            <!-- Ikon Emoji -->
            <div>
              <label class="block text-xs font-semibold text-slate-600 dark:text-slate-300 mb-2">Pilih Ikon Emoji</label>
              <div class="flex gap-2 flex-wrap items-center">
                <button
                  v-for="icon in (form.type === 'expense' ? presetExpenseIcons : presetIncomeIcons)"
                  :key="icon"
                  type="button"
                  @click="form.icon = icon"
                  :class="[
                    'w-9 h-9 text-base rounded-xl flex items-center justify-center transition-all',
                    form.icon === icon
                      ? 'bg-slate-900 text-white dark:bg-white dark:text-slate-900 ring-2 ring-slate-900 dark:ring-white scale-105 shadow-sm'
                      : 'bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700'
                  ]"
                >
                  {{ icon }}
                </button>
              </div>
            </div>

            <!-- Warna Label -->
            <div>
              <label class="block text-xs font-semibold text-slate-600 dark:text-slate-300 mb-2">Warna Kategori</label>
              <div class="flex items-center gap-2.5 flex-wrap">
                <button
                  v-for="c in presetColors"
                  :key="c"
                  type="button"
                  @click="form.color = c"
                  :style="{ backgroundColor: c }"
                  :class="[
                    'w-7 h-7 rounded-full flex items-center justify-center transition-all hover:scale-110 relative',
                    form.color === c ? 'ring-2 ring-slate-800 dark:ring-white ring-offset-2 dark:ring-offset-slate-900 scale-105 shadow-sm' : 'opacity-80 hover:opacity-100'
                  ]"
                >
                  <CheckIcon v-if="form.color === c" class="w-3.5 h-3.5 text-white stroke-[3]" />
                </button>
              </div>
            </div>

            <div v-if="modalError" class="text-xs text-rose-600 dark:text-rose-400 bg-rose-50 dark:bg-rose-950/40 rounded-xl px-3.5 py-2.5 border border-rose-200/60 dark:border-rose-800/40">
              {{ modalError }}
            </div>

            <!-- Action Buttons: Neutral (No Purple!) -->
            <div class="flex gap-2.5 pt-2">
              <button
                type="button"
                @click="showModal = false"
                class="flex-1 py-2.5 px-4 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 text-sm font-semibold transition-all"
              >
                Batal
              </button>
              <button
                type="submit"
                :disabled="submitting"
                class="flex-1 py-2.5 px-4 rounded-xl bg-slate-900 hover:bg-slate-800 dark:bg-white dark:hover:bg-slate-100 text-white dark:text-slate-900 text-sm font-semibold transition-all shadow-sm disabled:opacity-60"
              >
                {{ submitting ? 'Menyimpan...' : (editingId ? 'Simpan Perubahan' : 'Tambah Kategori') }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </Teleport>
  </div>
</template>
