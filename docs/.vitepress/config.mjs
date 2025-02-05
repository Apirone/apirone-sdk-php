import { defineConfig } from 'vitepress'

// https://vitepress.dev/reference/site-config
export default defineConfig({
  title: "Apirone SDK PHP",
  description: "PHP library for working with the Apirone API",
  base: '/apirone-sdk-php/',
  head: [['link', { rel: 'icon', href: '/apirone-sdk-php/favicon.ico' }]],
  themeConfig: {
    // https://vitepress.dev/reference/default-theme-config
    outline: [1,3],
    logo: '/logo-primarySmall.svg',
    nav: nav(),

    sidebar: sidebar(),

    search: {
      provider: 'local'
    },

    socialLinks: [
      { icon: 'github', link: 'https://github.com/Apirone/apirone-sdk-php' }
    ],
    footer: {
      message: 'Released under the MIT License.',
      copyright: 'Copyright © 2017-'+ getYear() + ' Apirone OÜ. All Rights Reserved.'
    },
    lastUpdated: {
      text: 'Updated at',
      formatOptions: {
        dateStyle: 'short',
        timeStyle: 'medium'
      }
    }
  }
})

function nav() {
  return  [
      { text: 'API Docs', link: 'https://apirone.com/docs' },
      {
        text: 'Helpful',
        items: [
          { text: 'FAQ', link: 'https://apirone.com/faq' },
          { text: 'Blog', link: 'https://apirone.com/blog' },
          { text: 'How to', link: 'https://apirone.com/how-to' },
          { text: 'Testing Bench', link: 'https://examples.apirone.com' },
        ]
      }
    ]
}

function sidebar() {
  return  [
      {
        items: [
          { text: 'Intro', link: '/intro' },
          { text: 'Five steps', link: '/five-steps' },
          // { text: 'Wallet', link: '/Wallet' },
          // { text: 'Invoices', link: '/Invoices' },
          // { text: 'Services', link: '/Services' },
          // { text: 'Authorization', link: '/Authorization' },
          // { text: 'Helpers', link: '/Helpers' },
          // { text: 'Log handling', link: '/LogHandling' },
        ]
      },
    ]
}

function getYear() {
  return new Date().getFullYear()
}