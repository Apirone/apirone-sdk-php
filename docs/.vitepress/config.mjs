import { defineConfig } from 'vitepress'

export default defineConfig({
  title: "SDK PHP",
  titleTemplate: 'Apirone :title',
  description: "Crypto payments in five easy steps",
  base: '/apirone-sdk-php/',
  head: [['link', { rel: 'icon', href: '/apirone-sdk-php/favicon.ico' }]],
  cleanUrls: true,
  sitemap: {
    hostname: 'https://apirone.github.io/apirone-sdk-php'
  },
  themeConfig: {
    outline: [2,3],
    logo: '/logo.svg',
    externalLinkIcon: true,

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
        text: 'Introduction',
        collapsed: false,
        items: [
          { text: 'Getting started', link: '/getting-started' },
          { text: 'Five-steps guide', link: '/five-steps-guide' },
          { text: 'Invoice App', link: '/invoice-app' },
        ]
      },
      {
        text: 'Dive deeper',
        collapsed: false,
        items: [
          { text: 'Invoice class', link: '/invoice' },
          { text: 'InvoiceDetails class', link: '/invoice-details' },
          { text: 'UserData class', link: '/user-data' },
          { text: 'Settings class', link: '/settings' },
          { text: 'Currency class', link: '/currency' },
          { text: 'Network class', link: '/network' },
          { text: 'Utils class', link: '/utils' },
          { text: 'Database class', link: '/database' },
          { text: 'Logger class', link: '/logger' },
          { text: 'Local API class', link: '/api' },
        ]
      },
      {
        text: 'Usage examples',
        link: '/usage-examples',
      },
    ]
}

function getYear() {
  return new Date().getFullYear()
}
