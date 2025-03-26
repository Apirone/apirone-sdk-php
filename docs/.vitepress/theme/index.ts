// import type { Theme } from 'vitepress'
import { Theme } from 'vitepress'
import DefaultTheme from 'vitepress/theme'
import { yandexMetrika } from '@hywax/vitepress-yandex-metrika'
import './custom.css'

// export default DefaultTheme
export default {
  extends: DefaultTheme,
  enhanceApp(ctx) {
    yandexMetrika(ctx, {
      counter: {
        id: 100550221
      },
    })
  },
} satisfies Theme

export interface YandexMetrikaCounter {
  initParams: {
    clickmap: true
    trackLinks: true
    accurateTrackBounce: true
    webvisor: true
  }
}