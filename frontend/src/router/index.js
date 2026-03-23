import { createRouter, createWebHistory } from 'vue-router'

import Login from '../pages/Login.vue'
import Dashboard from '../pages/Dashboard.vue'
import Register from '../pages/Register.vue'
import Groups from '../pages/Groups.vue'
import Schedule from '../pages/Schedule.vue'
import Payments from '../pages/Payments.vue'

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
  },
    {
    path: '/groups',
    component: Groups
  },
      {
    path: '/schedule',
    component: Schedule
  },
      {
    path: '/payments',
    component: Payments
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})



export default router