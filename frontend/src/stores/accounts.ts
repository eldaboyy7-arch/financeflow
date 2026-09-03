import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import type { Account, AccountPayload } from '@/types/account'
import { accountsApi } from '@/api/accounts'

export const useAccountsStore = defineStore('accounts', () => {
  const accounts = ref<Account[]>([])
  const loading = ref(false)

  const activeAccounts = computed(() => accounts.value.filter((a) => a.is_active))

  const totalBalance = computed(() =>
    activeAccounts.value.reduce((sum, a) => sum + a.current_balance, 0)
  )

  async function fetchAccounts(force = false) {
    if (accounts.value.length === 0 || force) {
      loading.value = true
    }
    try {
      const { data } = await accountsApi.list()
      accounts.value = Array.isArray(data) ? data : (data as any).data ?? []
    } finally {
      loading.value = false
    }
  }

  async function createAccount(payload: AccountPayload) {
    const { data } = await accountsApi.create(payload)
    accounts.value.push(data)
    return data
  }

  async function updateAccount(id: number, payload: Partial<AccountPayload>) {
    const { data } = await accountsApi.update(id, payload)
    const idx = accounts.value.findIndex((a) => a.id === id)
    if (idx !== -1) accounts.value[idx] = data
    return data
  }

  async function deleteAccount(id: number) {
    await accountsApi.delete(id)
    accounts.value = accounts.value.filter((a) => a.id !== id)
  }

  return { accounts, loading, activeAccounts, totalBalance, fetchAccounts, createAccount, updateAccount, deleteAccount }
})
