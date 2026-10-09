import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import type { Account, AccountPayload } from '@/types/account'
import { accountsApi } from '@/api/accounts'

export const useAccountsStore = defineStore('accounts', () => {
  const accounts = ref<Account[]>([])
  const loading = ref(false)

  const activeAccounts = computed(() => accounts.value.filter((a) => a.is_active))

  const totalBalance = computed(() =>
    activeAccounts.value.reduce((sum, a) => sum + Number(a.current_balance || 0), 0)
  )

  async function fetchAccounts(force = false) {
    if (accounts.value.length === 0 || force) {
      loading.value = true
    }
    try {
      const res = await accountsApi.list()
      // Backend wraps collection in { data: [...] }
      const raw = res.data
      accounts.value = Array.isArray(raw) ? raw : ((raw as any).data ?? [])
    } finally {
      loading.value = false
    }
  }

  async function createAccount(payload: AccountPayload) {
    const res = await accountsApi.create(payload)
    // Backend wraps single resource in { data: Account }
    const acc: Account = (res.data as any).data ?? res.data
    accounts.value.push(acc)
    return acc
  }

  async function updateAccount(id: number, payload: Partial<AccountPayload>) {
    const res = await accountsApi.update(id, payload)
    // Backend wraps single resource in { data: Account }
    const acc: Account = (res.data as any).data ?? res.data
    const idx = accounts.value.findIndex((a) => a.id === id)
    if (idx !== -1) accounts.value[idx] = acc
    return acc
  }

  async function deleteAccount(id: number) {
    await accountsApi.delete(id)
    accounts.value = accounts.value.filter((a) => a.id !== id)
  }

  return { accounts, loading, activeAccounts, totalBalance, fetchAccounts, createAccount, updateAccount, deleteAccount }
})
