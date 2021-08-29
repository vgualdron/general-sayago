<template>
  <div class="app">
    <AppHeader :user="user"/>
    <div class="app-body">
      <Sidebar :navItems="nav"/>
      <main class="main">
        <!--<breadcrumb :list="list"/>-->
        <div>
          <transition name="fade" mode="out-in" duration="500">
            <router-view></router-view>
          </transition>
        </div>
      </main>
      <AppAside/>
    </div>
    <AppFooter/>
  </div>
</template>

<script>
// import nav from '../_nav'
import { Header as AppHeader, Sidebar, Aside as AppAside, Footer as AppFooter, Breadcrumb } from '../components/'

export default {
  name: 'full',
  components: {
    AppHeader,
    Sidebar,
    AppAside,
    AppFooter,
    Breadcrumb
  },
  data () {
    return {
      nav: [], // nav.items,
      user: {}
    }
  },
  computed: {
    name () {
      return this.$route.name
    },
    list () {
      return this.$route.matched
    }
  },
  mounted: function () {
    this.inicializarMenu()
  },
  methods: {
    inicializarMenu: function () {
      let self = this
      this.$http.get('ws/usuario/lista_funcionalidades').then(resp => {
        self.nav = resp.data
        // Cargar Home
        this.cargarFuncionalidadHome()
      })
      this.getUserInfo()
    },
    getUserInfo: function () {
      let self = this
      return this.$http.get('ws/usuario/info').then(resp => {
        // self.updateFolders(resp.data.lista)
        self.user = resp.data
        self.$events.$emit('userLoaded', self.user)
      }, resp => {
        console.log('Falló cargando la informacion del usuario')
      })
    },
    filtrarFuncionalidadesOffline: function () {
      var self = this
      if (this.nav && this.nav.length > 0) {
        this.nav.forEach(function (elemento, i) {
          if (!elemento.offline) {
            console.log('Entro a eliminar :' + elemento.name)
            // Eliminar padre - por nombre o url
            self.nav.splice(i, 1)
          } else {
            if (elemento.children) {
              var hijas = elemento.children
              hijas.forEach(function (hija, indice) {
                if (!hija.offline) {
                  console.log('Entro a eliminar :' + hija.name)
                  // Eliminar hija - por url
                  hijas.splice(indice, 1)
                }
              })
            }
          }
        })
      }
    },
    cargarFuncionalidadHome: function () {
      if (this.nav && this.nav.length > 0) {
        let home = {
          name: 'Inicio',
          icon: 'icon-home',
          url: '/home'
        }
        this.nav.unshift(home)
      }
    }
  }
}
</script>
