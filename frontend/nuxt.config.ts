import vuetify, { transformAssetUrls } from 'vite-plugin-vuetify'
// https://nuxt.com/docs/api/configuration/nuxt-config
export default defineNuxtConfig({
  // devtools: { enabled: true },
  modules: [
    (_options, nuxt) => {
      nuxt.hooks.hook('vite:extendConfig', (config) => {
        // @ts-expect-error
        config.plugins.push(vuetify({ autoImport: true }))
      })
    },
    '@pinia/nuxt',
    //...
  ],
  app: {
    head: {
      script: [
        { src: `${process.env.API_URL}/api/restaurants/load-map` },
      ]
    }
  },
  devServer: {
    port: Number(process.env.DEV_SERVER_PORT || '3000'),
  },
  runtimeConfig: {
    public: {
      baseURL: process.env.API_URL || 'http://localhost:8000',
      captchaKey: process.env.GOOGLE_RECAPTCHA_API_KEY || '',
    },
  },
  build: {
    transpile: ['vuetify'],
  },

  vite: {
    vue: {
      template: {
        transformAssetUrls,
      },
    },
  },
  routeRules: {
    '/': { redirect: '/restaurants' },
  },
  plugins: []
})
