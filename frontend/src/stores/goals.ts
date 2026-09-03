import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import { goalsApi } from '@/api/goals'
import type { Goal, CreateGoalPayload, ContributeGoalPayload } from '@/types/goal'

export const useGoalsStore = defineStore('goals', () => {
  const goals = ref<Goal[]>([])
  const loading = ref(false)
  const hasLoaded = ref(false)
  const error = ref('')

  const activeGoals = computed(() => goals.value.filter((g) => !g.is_completed))
  const completedGoals = computed(() => goals.value.filter((g) => g.is_completed))
  const totalTarget = computed(() => goals.value.reduce((s, g) => s + g.target_amount, 0))
  const totalSaved = computed(() => goals.value.reduce((s, g) => s + g.current_amount, 0))
  const overallPercentage = computed(() =>
    totalTarget.value > 0 ? Math.round((totalSaved.value / totalTarget.value) * 100) : 0
  )

  async function fetchGoals(force = false) {
    if (!hasLoaded.value || force) {
      loading.value = true
    }
    error.value = ''
    try {
      const { data } = await goalsApi.get()
      goals.value = data.data
      hasLoaded.value = true
    } catch (e: any) {
      error.value = e.response?.data?.message ?? 'Gagal memuat target menabung.'
    } finally {
      loading.value = false
    }
  }

  async function createGoal(payload: CreateGoalPayload) {
    const { data } = await goalsApi.create(payload)
    await fetchGoals(true)
    return data.data
  }

  async function updateGoal(id: number, payload: Partial<CreateGoalPayload>) {
    const { data } = await goalsApi.update(id, payload)
    await fetchGoals(true)
    return data.data
  }

  async function deleteGoal(id: number) {
    await goalsApi.delete(id)
    goals.value = goals.value.filter((g) => g.id !== id)
  }

  async function contributeGoal(id: number, payload: ContributeGoalPayload) {
    const res = await goalsApi.contribute(id, payload)
    await fetchGoals(true)
    return res.data
  }

  return {
    goals,
    loading,
    hasLoaded,
    error,
    activeGoals,
    completedGoals,
    totalTarget,
    totalSaved,
    overallPercentage,
    fetchGoals,
    createGoal,
    updateGoal,
    deleteGoal,
    contributeGoal,
  }
})
