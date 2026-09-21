import { createRouter, createWebHistory } from 'vue-router'

const router = createRouter({
  history: createWebHistory(),
  routes: [
    {
      path: '/',
      name: 'home',
      component: () => import('@/views/HomeView.vue'),
      meta: {
        title: '3 Putri Mulya - Rental Mobil Lepas Kunci & Driver Bintan',
        description: 'Rental mobil resmi di Bintan & Tanjungpinang. Armada terawat, harga lepas kunci & dengan supir transparan, serta paket tour wisata Bintan.'
      }
    },
    {
      path: '/armada',
      name: 'fleet',
      component: () => import('@/views/FleetView.vue'),
      meta: {
        title: 'Pilihan Armada Rental Mobil Bintan - 3 Putri Mulya',
        description: 'Daftar lengkap armada rental mobil di Bintan. City car Agya, MPV Avanza & Veloz, hingga HiAce Commuter & Premio Luxury.'
      }
    },
    {
      path: '/paket-tour-bintan',
      name: 'tour-packages',
      component: () => import('@/views/TourPackagesView.vue'),
      meta: {
        title: 'Paket Tour Bintan HiAce - Mulai Rp 1.400.000 All-In (Supir & BBM) | 3 Putri Mulya',
        description: 'Paket tour seharian keliling Pulau Bintan naik Toyota HiAce Commuter (15 kursi) mulai Rp 1.400.000 atau HiAce Premio Luxury (14 kursi VIP) mulai Rp 1.500.000. All-In sudah termasuk supir profesional & BBM. Rute Lagoi Bay, Trikora, Danau Biru, Gurun Pasir Bintan.'
      }
    },
    {
      path: '/paket-tour',
      redirect: '/paket-tour-bintan'
    },
    {
      path: '/layanan',
      name: 'services',
      component: () => import('@/views/LayananView.vue'),
      meta: {
        title: 'Layanan Rental Mobil & Supir Profesional - 3 Putri Mulya',
        description: 'Layanan sewa mobil lepas kunci, sewa dengan supir berpengalaman, antar jemput pelabuhan feri & bandara di Bintan.'
      }
    },
    {
      path: '/destinasi',
      name: 'destinations',
      component: () => import('@/views/DestinationsView.vue'),
      meta: {
        title: 'Inspirasi Destinasi Wisata Favorit di Bintan - 3 Putri Mulya',
        description: 'Panduan dan rekomendasi objek wisata terbaik di Pulau Bintan: Lagoi Bay, Treasure Bay, Pantai Trikora, Vihara 500 Lohan, dan Danau Biru.'
      }
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

router.afterEach((to) => {
  if (typeof document === 'undefined') return

  // 1. Dynamic Canonical Tag
  let canonicalEl = document.querySelector('link[rel="canonical"]')
  if (!canonicalEl) {
    canonicalEl = document.createElement('link')
    canonicalEl.setAttribute('rel', 'canonical')
    document.head.appendChild(canonicalEl)
  }
  const cleanPath = to.path === '/' ? '' : to.path
  canonicalEl.setAttribute('href', `https://www.3putrimulya.com${cleanPath}`)

  // 2. Dynamic Title
  if (to.meta?.title) {
    document.title = to.meta.title as string
  }

  // 3. Dynamic Meta Description
  if (to.meta?.description) {
    let metaDesc = document.querySelector('meta[name="description"]')
    if (!metaDesc) {
      metaDesc = document.createElement('meta')
      metaDesc.setAttribute('name', 'description')
      document.head.appendChild(metaDesc)
    }
    metaDesc.setAttribute('content', to.meta.description as string)
  }
})

export default router

