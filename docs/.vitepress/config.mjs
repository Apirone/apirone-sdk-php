import { defineConfig } from 'vitepress'

// https://vitepress.dev/reference/site-config
export default defineConfig({
  title: "Apirone SDK PHP",
  description: "Crypto payments in five easy steps",
  base: '/apirone-sdk-php/',
  head: [['link', { rel: 'icon', href: '/apirone-sdk-php/favicon.ico' }]],
  cleanUrls: true,
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
          { text: 'Overview', link: '/overview' },
          { text: 'Five-steps guide', link: '/five-steps-guide' },
        ]
      },
      {
        text: 'Dive deeper',
        collapsed: true,
        items: [
          {text: 'Invoice', link: '/invoice'},
          {text: 'UserData', link: '/user-data'},
          {text: 'Settings class', link: '/settings'},
          {text: 'Render invoice', link: '/render'},
          {text: 'Utils', link: '/utils'},
        ]
      },
      {
        text: 'Usage examples',
        link: '/usage-examples',
      },
      {
        text: 'Deprecated pages',
        items: [
          {text: '__Overview', link: '/__overview'},
          {text: '__Old readme', link: '/__readme'},
        ]
      },
    ]
}

function getYear() {
  return new Date().getFullYear()
}