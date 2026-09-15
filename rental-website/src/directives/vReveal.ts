import type { Directive } from 'vue'

export const vReveal: Directive<HTMLElement, number | undefined> = {
  mounted(el, binding) {
    const animation = binding.arg || 'fade-up'
    const delay = typeof binding.value === 'number' ? binding.value : 0

    el.classList.add('reveal-init', `reveal-${animation}`)
    if (delay) {
      el.style.transitionDelay = `${delay}ms`
    }

    const observer = new IntersectionObserver(
      (entries, obs) => {
        entries.forEach((entry) => {
          if (entry.isIntersecting) {
            el.classList.add('reveal-active')
            obs.unobserve(el)
          }
        })
      },
      {
        rootMargin: '0px 0px -40px 0px',
        threshold: 0.05,
      }
    )

    observer.observe(el)
    ;(el as any)._revealObserver = observer
  },
  unmounted(el) {
    if ((el as any)._revealObserver) {
      ;(el as any)._revealObserver.disconnect()
    }
  },
}
