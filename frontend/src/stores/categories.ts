import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import type { Category, CategoryPayload } from '@/types/category'
import { categoriesApi } from '@/api/categories'

export const useCategoriesStore = defineStore('categories', () => {
  const categories = ref<Category[]>([])
  const loading = ref(false)

  const incomeCategories = computed(() => categories.value.filter((c) => c.type === 'income'))
  const expenseCategories = computed(() => categories.value.filter((c) => c.type === 'expense'))

  async function fetchCategories(force = false) {
    if (categories.value.length === 0 || force) {
      loading.value = true
    }
    try {
      const { data } = await categoriesApi.list()
      categories.value = Array.isArray(data) ? data : (data as any).data ?? []
    } finally {
      loading.value = false
    }
  }

  async function createCategory(payload: CategoryPayload) {
    const { data } = await categoriesApi.create(payload)
    categories.value.push(data)
    return data
  }

  async function updateCategory(id: number, payload: Partial<CategoryPayload>) {
    const { data } = await categoriesApi.update(id, payload)
    const idx = categories.value.findIndex((c) => c.id === id)
    if (idx !== -1) categories.value[idx] = data
    return data
  }

  async function deleteCategory(id: number) {
    await categoriesApi.delete(id)
    categories.value = categories.value.filter((c) => c.id !== id)
  }

  return { categories, loading, incomeCategories, expenseCategories, fetchCategories, createCategory, updateCategory, deleteCategory }
})
