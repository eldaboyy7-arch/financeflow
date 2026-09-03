import api from './axios'
import type { Transaction, TransactionPayload, TransactionFilters, Transfer, TransferPayload } from '@/types/transaction'
import type { ApiCollectionResponse } from '@/types/api'

export const transactionsApi = {
  list: (filters?: TransactionFilters) =>
    api.get<ApiCollectionResponse<Transaction>>('/transactions', { params: filters }),

  create: (payload: TransactionPayload) =>
    api.post<Transaction>('/transactions', payload),

  update: (id: number, payload: Partial<TransactionPayload>) =>
    api.put<Transaction>(`/transactions/${id}`, payload),

  delete: (id: number) =>
    api.delete<{ message: string }>(`/transactions/${id}`),
}

export const transfersApi = {
  list: (page = 1) =>
    api.get<ApiCollectionResponse<Transfer>>('/transfers', { params: { page } }),

  create: (payload: TransferPayload) =>
    api.post<Transfer>('/transfers', payload),

  update: (id: number, payload: Partial<TransferPayload>) =>
    api.put<Transfer>(`/transfers/${id}`, payload),

  delete: (id: number) =>
    api.delete<{ message: string }>(`/transfers/${id}`),
}
