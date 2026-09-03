import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    // Auth routes
    {
      path: '/login',
      name: 'login',
      component: () => import('@/pages/auth/LoginPage.vue'),
      meta: { guest: true, depth: 0 },
    },
    {
      path: '/daftar',
      name: 'register',
      component: () => import('@/pages/auth/RegisterPage.vue'),
      meta: { guest: true, depth: 1 },
    },
    {
      path: '/lupa-password',
      name: 'forgot-password',
      component: () => import('@/pages/auth/ForgotPasswordPage.vue'),
      meta: { guest: true, depth: 2 },
    },
    {
      path: '/reset-password',
      name: 'reset-password',
      component: () => import('@/pages/auth/ResetPasswordPage.vue'),
      meta: { guest: true, depth: 3 },
    },

    // Protected app routes
    {
      path: '/',
      component: () => import('@/layouts/AppLayout.vue'),
      meta: { requiresAuth: true },
      children: [
        {
          path: '',
          name: 'dashboard',
          component: () => import('@/pages/DashboardPage.vue'),
        },
        {
          path: 'transaksi',
          name: 'transactions',
          component: () => import('@/pages/TransactionsPage.vue'),
        },
        {
          path: 'anggaran',
          name: 'budgets',
          component: () => import('@/pages/BudgetsPage.vue'),
        },
        {
          path: 'impian',
          name: 'goals',
          component: () => import('@/pages/GoalsPage.vue'),
        },
        {
          path: 'langganan',
          name: 'recurring',
          component: () => import('@/pages/RecurringPage.vue'),
        },
        {
          path: 'rekening',
          name: 'accounts',
          component: () => import('@/pages/AccountsPage.vue'),
        },
        {
          path: 'kategori',
          name: 'categories',
          component: () => import('@/pages/CategoriesPage.vue'),
        },
        {
          path: 'laporan',
          name: 'reports',
          component: () => import('@/pages/ReportsPage.vue'),
        },
        {
          path: 'pengaturan',
          name: 'settings',
          component: () => import('@/pages/SettingsPage.vue'),
        },
      ],
    },
  ],
})

// Navigation guards
router.beforeEach(async (to) => {
  const authStore = useAuthStore()

  // If token exists but no user, fetch user
  if (authStore.token && !authStore.user) {
    await authStore.fetchMe()
  }

  if (to.meta.requiresAuth && !authStore.isAuthenticated) {
    return { name: 'login' }
  }

  if (to.meta.guest && authStore.isAuthenticated) {
    return { name: 'dashboard' }
  }
})

export default router
