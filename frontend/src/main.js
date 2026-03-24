import { createApp } from 'vue'
import App from './App.vue'
import router from './router'
import '@mdi/font/css/materialdesignicons.css'

import 'vuetify/styles'
import { createVuetify } from 'vuetify'
import { bootstrapAuth } from './services/auth'

const vuetify = createVuetify()

await bootstrapAuth()

createApp(App)
  .use(router)
  .use(vuetify)
  .mount('#app')
