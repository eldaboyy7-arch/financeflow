<script setup lang="ts">
import { ref, onMounted, computed } from 'vue'
import { useCategoriesStore } from '@/stores/categories'
import { useUiStore } from '@/stores/ui'
import type { CategoryPayload } from '@/types/category'
import SelectInput from '@/components/SelectInput.vue'
import type { SelectOption } from '@/components/SelectInput.vue'
import { PlusIcon, PencilSquareIcon, TrashIcon, TagIcon, XMarkIcon } from '@heroicons/vue/24/outline'

const categoryTypeOptions: SelectOption[] = [
  { value: 'expense', label: 'Pengeluaran' },
  { value: 'income', label: 'Pemasukan' },
]

const categoriesStore = useCategoriesStore()
const uiStore = useUiStore()

const activeTab = ref<'income' | 'expense'>('expense')
const showModal = ref(false)
const editingId = ref<number | null>(null)
const submitting = ref(false)
const modalError = ref('')

const defaultForm: CategoryPayload = {
  name: '', type: 'expense', icon: '📦', color: '#6366F1',
}
const form = ref<CategoryPayload>({ ...defaultForm })

onMounted(() => categoriesStore.fetchCategories())

const displayedCategories = computed(() =>
  activeTab.value === 'income'
    ? categoriesStore.incomeCategories
    : categoriesStore.expenseCategories
)

function openCreate() {
  editingId.value = null
  form.value = { ...defaultForm, type: activeTab.value }
  modalError.value = ''
  showModal.value = true
}

function openEdit(id: number) {
  const cat = categoriesStore.categories.find((c) => c.id === id)
  if (!cat) return
  editingId.value = id
  form.value = { name: cat.name, type: cat.type, icon: cat.icon, color: cat.color }
  modalError.value = ''
  showModal.value = true
}

async function submitCategory() {
  submitting.value = true
  modalError.value = ''
  try {
    if (editingId.value) {
      await categoriesStore.updateCategory(editingId.value, form.value)
      uiStore.showToast('Kategori berhasil diperbarui!')
    } else {
      await categoriesStore.createCategory(form.value)
      uiStore.showToast('Kategori baru berhasil ditambahkan!')
    }
    showModal.value = false
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

async function deleteCategory(id: number) {
  if (!confirm('Hapus kategori ini?')) return
  try {
    await categoriesStore.deleteCategory(id)
    uiStore.showToast('Kategori berhasil dihapus.', 'info')
  } catch (err: any) {
    uiStore.showToast(err.response?.data?.message ?? 'Gagal menghapus kategori.', 'error')
  }
}
</script>

<template>
  <div class="space-y-5">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
      <div>
        <h1 class="text-xl font-bold text-slate-900 dark:text-white">Kategori</h1>
        <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-0.5">Kelola kategori pemasukan & pengeluaran</p>
      </div>
      <button @click="openCreate" class="btn-primary flex items-center justify-center gap-1.5 w-full sm:w-auto text-sm py-2.5 shadow-sm">
        <PlusIcon class="w-4 h-4" />
        Tambah Kategori
      </button>
    </div>

    <!-- Tabs -->
    <div class="flex gap-1 bg-slate-100 dark:bg-slate-800 rounded-xl p-1 w-full sm:w-fit">
      <button
        v-for="tab in [{ key: 'expense', label: 'Pengeluaran' }, { key: 'income', label: 'Pemasukan' }]"
        :key="tab.key"
        @click="activeTab = tab.key as any"
        :class="[
          'flex-1 sm:flex-none px-5 py-2 text-xs sm:text-sm font-semibold rounded-lg transition-all',
          activeTab === tab.key
            ? 'bg-white dark:bg-slate-700 text-slate-900 dark:text-white shadow-sm'
            : 'text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200',
        ]"
      >
        {{ tab.label }}
      </button>
    </div>

    <!-- Empty state -->
    <div v-if="!displayedCategories.length" class="card p-12 flex flex-col items-center text-center">
      <div class="w-14 h-14 bg-slate-100 dark:bg-slate-700/60 rounded-2xl flex items-center justify-center mb-4">
        <TagIcon class="w-7 h-7 text-slate-400" />
      </div>
      <p class="text-sm font-semibold text-slate-700 dark:text-slate-300">Belum ada kategori</p>
      <button @click="openCreate" class="btn-primary mt-4 text-sm py-2 px-4">Tambah Kategori</button>
    </div>

    <!-- Grid -->
    <div v-else class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-3">
      <div
        v-for="cat in displayedCategories"
        :key="cat.id"
        class="card p-3.5 sm:p-4 flex flex-col items-center text-center hover:shadow-md transition-all group relative"
      >
        <!-- Default badge -->
        <span
          v-if="cat.is_default"
          class="absolute top-2 right-2 text-[9px] font-medium bg-slate-100 dark:bg-slate-700 text-slate-400 dark:text-slate-400 px-1.5 py-0.5 rounded-full"
        >
          Bawaan
        </span>

        <div
          class="w-12 h-12 rounded-xl flex items-center justify-center text-2xl mb-2 shrink-0 shadow-sm"
          :style="{ backgroundColor: cat.color + '20' }"
        >
          {{ cat.icon }}
        </div>
        <p class="text-xs sm:text-sm font-bold text-slate-800 dark:text-slate-200 truncate w-full">{{ cat.name }}</p>

        <!-- Actions: visible on mobile, hover-revealed on desktop -->
        <div class="flex gap-1 mt-2.5 opacity-100 sm:opacity-0 group-hover:opacity-100 transition-opacity">
          <button
            @click="openEdit(cat.id)"
            class="p-1 rounded-lg text-slate-400 hover:text-primary-600 hover:bg-primary-50 dark:hover:bg-primary-900/20 transition-colors"
            title="Edit"
          >
            <PencilSquareIcon class="w-3.5 h-3.5" />
          </button>
          <button
            v-if="!cat.is_default"
            @click="deleteCategory(cat.id)"
            class="p-1 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-900/20 transition-colors"
            title="Hapus"
          >
            <TrashIcon class="w-3.5 h-3.5" />
          </button>
        </div>
      </div>
    </div>

    <!-- Modal (Bottom sheet on mobile) -->
    <Teleport to="body">
      <div v-if="showModal" class="fixed inset-0 z-50 flex items-end sm:items-center justify-center p-0 sm:p-4">
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" @click="showModal = false"></div>
        <div class="relative bg-white dark:bg-slate-800 rounded-t-3xl sm:rounded-2xl p-5 sm:p-6 w-full max-w-sm shadow-2xl border border-slate-200/60 dark:border-slate-700 max-h-[90vh] overflow-y-auto">
          <!-- Drag bar for mobile -->
          <div class="w-10 h-1 bg-slate-200 dark:bg-slate-700 rounded-full mx-auto mb-3 sm:hidden"></div>

          <div class="flex items-center justify-between mb-5">
            <h3 class="text-base font-bold text-slate-900 dark:text-white">
              {{ editingId ? 'Ubah Kategori' : 'Tambah Kategori' }}
            </h3>
            <button @click="showModal = false" class="p-1.5 rounded-lg text-slate-400 hover:text-slate-700 dark:hover:text-slate-200">
              <XMarkIcon class="w-5 h-5" />
            </button>
          </div>

          <form @submit.prevent="submitCategory" class="space-y-4">
            <div>
              <label class="label">Jenis</label>
              <SelectInput v-model="form.type" :options="categoryTypeOptions" placeholder="Pilih jenis" />
            </div>

            <div>
              <label class="label">Nama Kategori</label>
              <input v-model="form.name" type="text" class="input" placeholder="Contoh: Makanan, Gaji, dll." required />
            </div>

            <div class="grid grid-cols-2 gap-3">
              <div>
                <label class="label">Ikon <span class="text-slate-400 font-normal text-xs">(emoji)</span></label>
                <input v-model="form.icon" type="text" class="input text-center text-xl" maxlength="4" placeholder="📦" />
              </div>
              <div>
                <label class="label">Warna</label>
                <div class="flex gap-2 items-center">
                  <input v-model="form.color" type="color" class="h-10 w-12 rounded-xl cursor-pointer border border-slate-200 dark:border-slate-600 bg-transparent p-1" />
                  <input v-model="form.color" type="text" class="input text-xs font-mono uppercase" maxlength="7" />
                </div>
              </div>
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
