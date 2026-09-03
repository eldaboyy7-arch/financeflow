import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import { recurringApi } from '@/api/recurring'
import type { RecurringItem, CreateRecurringPayload } from '@/types/recurring'

export const useRecurringStore = defineStore('recurring', () => {
  const items = ref<RecurringItem[]>([])
  const upcomingBills = ref<RecurringItem[]>([])
  const loading = ref(false)
  const hasLoaded = ref(false)
  const error = ref('')

  const activeItems = computed(() => items.value.filter((i) => i.is_active))
  const totalMonthlyCommitment = computed(() =>
    activeItems.value.reduce((s, i) => {
      if (i.type !== 'expense') return s
      if (i.frequency === 'daily') return s + i.amount * 30
      if (i.frequency === 'weekly') return s + i.amount * 4
      if (i.frequency === 'yearly') return s + i.amount / 12
      return s + i.amount
    }, 0)
  )

  async function fetchAll(force = false) {
    if (!hasLoaded.value || force) {
      loading.value = true
    }
    error.value = ''
    try {
      const [resAll, resUp] = await Promise.all([
        recurringApi.get(),
        recurringApi.upcoming(30),
      ])
      items.value = resAll.data.data
      upcomingBills.value = resUp.data.data
      hasLoaded.value = true
    } catch (e: any) {
      error.value = e.response?.data?.message ?? 'Gagal memuat tagihan rutin.'
    } finally {
      loading.value = false
    }
  }

  async function createItem(payload: CreateRecurringPayload) {
    const { data } = await recurringApi.create(payload)
    await fetchAll(true)
    return data.data
  }

  async function updateItem(id: number, payload: Partial<CreateRecurringPayload>) {
    const { data } = await recurringApi.update(id, payload)
    await fetchAll(true)
    return data.data
  }

  async function deleteItem(id: number) {
    await recurringApi.delete(id)
    items.value = items.value.filter((i) => i.id !== id)
    upcomingBills.value = upcomingBills.value.filter((i) => i.id !== id)
  }

  async function payBill(id: number) {
    const res = await recurringApi.pay(id)
    await fetchAll(true)
    return res.data
  }

  return {
    items,
    upcomingBills,
    loading,
    hasLoaded,
    error,
    activeItems,
    totalMonthlyCommitment,
    fetchAll,
    createItem,
    updateItem,
    deleteItem,
    payBill,
  }
})
