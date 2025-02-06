import { defineConfig } from 'vitepress'

// https://vitepress.dev/reference/site-config
export default defineConfig({
  title: "Apirone SDK PHP",
  description: "Crypto payments in five easy steps",
  base: '/apirone-sdk-php/',
  head: [['link', { rel: 'icon', href: '/apirone-sdk-php/favicon.ico' }]],
  themeConfig: {
    // https://vitepress.dev/reference/default-theme-config
    outline: [2,3],
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
        text: 'Getting started',
        collapsed: false,
        items: [
          { text: 'Intro', link: '/intro' },
          { text: 'Five-steps integration', link: '/five-steps' },
        ]
      },
      {
        text: 'Classes',
        collapsed: false,
        items: [
          {text: 'Invoice', link: '/invoice'},
          {text: 'AbstractModel', link: '/abstract-model'},
          {text: 'HistoryItem', link: '/history-item'},
          {text: 'InvoiceDetails', link: '/invoice-details'},
          {text: 'UserData', link: '/user-data'},
          {text: 'UserData/ExtraItem', link: '/extra-item'},
          {text: 'UserData/OrderItem', link: '/order-item'},
          {text: 'Settings', link: '/settings'},
          {text: 'Settings/Currency', link: '/currency'},
          {text: 'InvoiceDb', link: '/invoice-db'},
          {text: 'InvoiceQuery', link: '/invoice-query'},
          {text: 'Render', link: '/render'},
          {text: 'Utils', link: '/utils'},
        ]
      }
    ]
}

function getYear() {
  return new Date().getFullYear()
}