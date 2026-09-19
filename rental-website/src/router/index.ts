import { createRouter, createWebHistory } from 'vue-router'

const router = createRouter({
  history: createWebHistory(),
  routes: [
    {
      path: '/',
      name: 'home',
      component: () => import('@/views/HomeView.vue')
    },
    {
      path: '/armada',
      name: 'fleet',
      component: () => import('@/views/FleetView.vue')
    },
    {
      path: '/paket-tour-bintan',
      name: 'tour-packages',
      component: () => import('@/views/TourPackagesView.vue')
    },
    {
      path: '/paket-tour',
      redirect: '/paket-tour-bintan'
    },    {
      path: '/layanan',
      name: 'services',
      component: () => import('@/views/LayananView.vue')
    },

    {
      path: '/destinasi',
      name: 'destinations',
      component: () => import('@/views/DestinationsView.vue')
    },
    {
      path: '/inspirasi',
      redirect: '/destinasi'
    },
    {
      path: '/:pathMatch(.*)*',
      redirect: '/'
    }
  ],
  scrollBehavior(to, from, savedPosition) {
    if (savedPosition) {
      return savedPosition
    }
    if (to.hash) {
      // Same-page navigation: element is already mounted in DOM
      const isInitial = from.matched.length === 0
      if (!isInitial && from.path === to.path) {
        return { el: to.hash, behavior: 'smooth' }
      }

      // Cross-page navigation or initial entry with hash:
      // Wait for page-fade leave transition (250ms) + target component mount
      return new Promise((resolve) => {
        let attempts = 0
        const checkEl = () => {
          const el = document.querySelector(to.hash)
          if (el) {
            // Found target element! Small delay for layout paint stability
            setTimeout(() => {
              resolve({ el: to.hash, behavior: 'smooth' })
            }, 50)
          } else if (attempts < 30) {
            attempts++
            setTimeout(checkEl, 40)
          } else {
            resolve({ top: 0, behavior: 'smooth' })
          }
        }
        // Give the leaving transition (~250ms) time to finish
        setTimeout(checkEl, isInitial ? 50 : 260)
      })
    }
    return { top: 0, behavior: 'smooth' }
  }
})

export default router

