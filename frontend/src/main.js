import { createApp } from 'vue'
import App from './App.vue'
import router from './router'
import '@mdi/font/css/materialdesignicons.css'

import 'vuetify/styles'
import { createVuetify } from 'vuetify'
import { bootstrapAuth } from './services/auth'

const vuetify = createVuetify({
  theme: {
    themes: {
      coachLight: {
        dark: false,
        colors: {
          primary: '#9c7cff'
        }
      },
      coachDark: {
        dark: true,
        colors: {
          primary: '#b79cff'
        }
      },
      adminLight: {
        dark: false,
        colors: {
          primary: '#f28c28'
        }
      },
      adminDark: {
        dark: true,
        colors: {
          primary: '#ff9e3d'
        }
      }
    }
  }
})

await bootstrapAuth()

createApp(App)
  .use(router)
  .use(vuetify)
  .mount('#app')
