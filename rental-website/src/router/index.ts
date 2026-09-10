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
  scrollBehavior(to, _from, savedPosition) {
    if (savedPosition) {
      return savedPosition
    }
    if (to.hash) {
      return { el: to.hash, behavior: 'smooth' }
    }
    return { top: 0, behavior: 'smooth' }
  }
})

export default router
