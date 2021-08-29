import Vue from 'vue'
import Router from 'vue-router'

// Containers
import Full from '@/containers/Full'

// Views
import Home from '@/views/Home'
import Error from '@/views/Error'

import Aplicacion from '@/views/Aplicacion'
import Rol from '@/views/Rol'
import Funcionalidad from '@/views/Funcionalidad'
import PersonaNatural from '@/views/PersonaNatural'
import CuentaUsuario from '@/views/CuentaUsuario'
Vue.use(Router)

/* const ViewRedirect = Vue.extend({
  render: function () {
    console.log('Route.path ' + this.$route.path)
    console.log(this.$route)
    this.$router.replace('/view_' + this.$route.path.substring(1))
  }
}) */

export default new Router({
  mode: 'hash',
  linkActiveClass: 'open active',
  scrollBehavior: () => ({ y: 0 }),
  routes: [
    {
      path: '/',
      redirect: '/home',
      name: 'Home',
      component: Full,
      children: [
        {
          path: 'home',
          name: 'Home',
          component: Home
        },
        {
          path: 'aplicacion',
          name: 'Aplicacion',
          component: Aplicacion
        },
        {
          path: 'rol',
          name: 'Rol',
          component: Rol
        },
        {
          path: 'funcionalidad',
          name: 'Funcionalidad',
          component: Funcionalidad
        },
        {
          path: 'personanatural',
          name: 'Gestionar Persona Natural',
          component: PersonaNatural
        },
        {
          path: 'cuentausuario',
          name: 'Gestionar Cuentas de Usuario',
          component: CuentaUsuario
        }
      ]
    },
    {
      path: '/error',
      name: 'Error',
      component: Error
    }
  ]
})
