import { createRouter, createWebHistory } from 'vue-router'

import Login from '../pages/Login.vue'
import Dashboard from '../pages/Dashboard.vue'
import Register from '../pages/Register.vue'

const routes = [
  {
    path: '/',
    redirect: '/login'
  },
  {
    path: '/login',
    component: Login
  },
  {
    path: '/dashboard',
    component: Dashboard
  },
  {
    path: '/register',
    component: Register
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

export default router