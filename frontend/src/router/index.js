import { createRouter, createWebHistory } from 'vue-router'
import { bootstrapAuth, useAuth } from '../services/auth'

import Login from '../pages/Login.vue'
import Home from '../pages/Home.vue'
import Register from '../pages/Register.vue'
import Groups from '../pages/Groups.vue'
import Schedule from '../pages/Schedule.vue'
import Payments from '../pages/Payments.vue'
import Attendance from '../pages/Attendance.vue'
import CoachAttendance from '../pages/CoachAttendance.vue'
import AdminUsers from '../pages/AdminUsers.vue'
import AdminGroups from '../pages/AdminGroups.vue'
import AdminSessions from '../pages/AdminSessions.vue'
import AdminHome from '../pages/AdminHome.vue'
import AdminPayments from '../pages/AdminPayments.vue'

const routes = [
  {
    path: '/',
    redirect: '/login'
  },
  {
    path: '/login',
    component: Login,
    meta: { guestOnly: true }
  },
  {
    path: '/home',
    component: Home,
    meta: { requiresAuth: true }
  },
  {
    path: '/admin',
    component: AdminHome,
    meta: { requiresAuth: true, adminOnly: true }
  },
  {
    path: '/register',
    component: Register,
    meta: { guestOnly: true }
  },
  {
    path: '/groups',
    component: Groups,
    meta: { requiresAuth: true }
  },
  {
    path: '/schedule',
    component: Schedule,
    meta: { requiresAuth: true }
  },
  {
    path: '/payments',
    component: Payments,
    meta: { requiresAuth: true, parentOnly: true }
  },
  {
    path: '/attendance',
    component: Attendance,
    meta: { requiresAuth: true }
  },
  {
    path: '/coach-attendance',
    component: CoachAttendance,
    meta: { requiresAuth: true, coachOnly: true }
  },
  {
    path: '/admin-users',
    component: AdminUsers,
    meta: { requiresAuth: true, adminOnly: true }
  },
  {
    path: '/manage-groups',
    component: AdminGroups,
    meta: { requiresAuth: true, adminOnly: true }
  },
  {
    path: '/manage-sessions',
    component: AdminSessions,
    meta: { requiresAuth: true, adminOnly: true }
  },
  {
    path: '/admin-payments',
    component: AdminPayments,
    meta: { requiresAuth: true, adminOnly: true }
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

router.beforeEach(async (to) => {
  const { isAuthenticated } = useAuth()

  if (!isAuthenticated.value) {
    if (to.meta?.requiresAuth) {
      return '/login'
    }

    return true
  }

  await bootstrapAuth()

  const { user } = useAuth()

  if (!user.value && to.meta?.requiresAuth) {
    return '/login'
  }

  if (user.value?.role === 'admin' && to.path === '/home') {
    return '/admin'
  }

  if (to.meta?.adminOnly && user.value?.role !== 'admin') {
    return '/home'
  }

  if (to.meta?.parentOnly && user.value?.role !== 'parent') {
    return '/home'
  }

  if (to.meta?.coachOnly && user.value?.role !== 'coach') {
    return '/home'
  }

  if (user.value?.role === 'coach' && to.path === '/attendance') {
    return '/coach-attendance'
  }

  if (
    user.value?.role === 'admin'
    && to.meta?.requiresAuth
    && !['/admin', '/admin-users', '/manage-groups', '/manage-sessions', '/admin-payments'].includes(to.path)
  ) {
    return '/admin'
  }

  if (to.meta?.guestOnly && user.value) {
    return user.value.role === 'admin' ? '/admin' : '/home'
  }

  return true
})

export default router
