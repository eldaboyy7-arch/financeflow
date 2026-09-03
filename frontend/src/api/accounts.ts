import api from './axios'
import type { Account, AccountPayload } from '@/types/account'
import type { ApiCollectionResponse } from '@/types/api'

export const accountsApi = {
  list: () =>
    api.get<Account[]>('/accounts'),

  create: (payload: AccountPayload) =>
    api.post<Account>('/accounts', payload),

  update: (id: number, payload: Partial<AccountPayload>) =>
    api.put<Account>(`/accounts/${id}`, payload),

  delete: (id: number) =>
    api.delete<{ message: string }>(`/accounts/${id}`),
}
