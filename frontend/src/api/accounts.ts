import api from './axios'
import type { Account, AccountPayload } from '@/types/account'
import type { ApiSingleResponse } from '@/types/api'

export const accountsApi = {
  list: () =>
    api.get<{ data: Account[] }>('/accounts'),

  create: (payload: AccountPayload) =>
    api.post<ApiSingleResponse<Account>>('/accounts', payload),

  update: (id: number, payload: Partial<AccountPayload>) =>
    api.put<ApiSingleResponse<Account>>(`/accounts/${id}`, payload),

  delete: (id: number) =>
    api.delete<{ message: string }>(`/accounts/${id}`),
}
