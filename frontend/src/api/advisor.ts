import api from './axios'

export const advisorApi = {
  ask: (message: string, history: Array<{ role: string; text: string }> = []) =>
    api.post<{ reply: string }>('/ai/advisor', { message, history }),
}
