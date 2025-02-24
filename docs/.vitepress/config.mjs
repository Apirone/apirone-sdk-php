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
        text: 'Introduction',
        collapsed: false,
        items: [
          { text: 'Getting started', link: '/getting-started' },
          { text: 'Five-steps guide', link: '/five-steps-guide' },
        ]
      },
      {
        // text: 'Digging Deeper',
        text: 'Dive deeper',
        collapsed: false,
        items: [
          {text: 'Invoice', link: '/invoice'},
          {text: 'Settings', link: '/settings'},
          {text: 'UserData', link: '/user-data'},
          {text: 'Render', link: '/render'},
          {text: 'Utils', link: '/utils'},
        ]
      },
      {
        text: 'Usage examples',
        link: '/usage-examples',
      },
      // {
      //   text: 'Deprecated pages',
      //   items: [
      //     {text: '_Overview', link: '/deprecated/overview'},
      //     {text: '_README.md', link: '/deprecated/readme'},
      //   ]
      // },
    ]
}

function getYear() {
  return new Date().getFullYear()
}
