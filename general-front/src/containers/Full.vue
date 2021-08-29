<template>
  <div class="app">
    <AppHeader fixed>
      <SidebarToggler class="d-lg-none" display="md" mobile />
      <b-link class="navbar-brand" to="#">
        <img class="navbar-brand-full" src="static/img/logo_banner_menu.png" width="89" height="25" alt="CoreUI Logo">
        <img class="navbar-brand-minimized" src="static/img/logo_banner_menu_minimized.png" width="30" height="30" alt="CoreUI Logo">
      </b-link>
      <SidebarToggler class="d-md-down-none" display="lg" />
      <!--<b-navbar-nav class="d-md-down-none">
        <b-nav-item class="px-3" to="/dashboard">1</b-nav-item>
        <b-nav-item class="px-3" to="/users" exact>2</b-nav-item>
        <b-nav-item class="px-3">3</b-nav-item>
      </b-navbar-nav>-->
      <b-navbar-nav class="ml-auto">
        <!--<b-nav-item class="d-md-down-none">
          <i class="icon-bell"></i>
          <b-badge pill variant="danger">5</b-badge>
        </b-nav-item>
        <b-nav-item class="d-md-down-none">
          <i class="icon-list"></i>
        </b-nav-item>
        <b-nav-item class="d-md-down-none">
          <i class="icon-location-pin"></i>
        </b-nav-item>-->
        <DefaultHeaderDropdownAccnt/>
      </b-navbar-nav>
      <!--<AsideToggler class="d-none d-lg-block" />-->
      <!--<AsideToggler class="d-lg-none" mobile />-->
    </AppHeader>
    <div class="app-body">
      <AppSidebar fixed>
        <SidebarHeader/>
        <SidebarForm/>
        <SidebarNav :navItems="nav"></SidebarNav>
        <SidebarFooter/>
        <SidebarMinimizer/>
      </AppSidebar>
      <main class="main">
        <Breadcrumb :list="list"/>
        <div class="container-fluid">
          <router-view></router-view>
        </div>
      </main>
      <AppAside fixed>
        <DefaultAside/>
      </AppAside>
    </div>
    <TheFooter>
      <!--footer-->
      <div>
        <a href="#">Pinchetas</a>
        <span class="ml-1">&copy; 2019</span>
      </div>
      <div class="ml-auto">
        <span class="mr-1">Por</span>
        <a href="#"> vgualdronLabs</a>
      </div>
    </TheFooter>
  </div>
</template>

<script>
import { Header as AppHeader, SidebarToggler, Sidebar as AppSidebar, SidebarFooter, SidebarForm, SidebarHeader, SidebarMinimizer, SidebarNav, Aside as AppAside, AsideToggler, Footer as TheFooter, Breadcrumb } from '@coreui/vue'
import DefaultAside from './DefaultAside'
import DefaultHeaderDropdownAccnt from './DefaultHeaderDropdownAccnt'

export default {
  name: 'FullContainer',
  components: {
    AsideToggler,
    AppHeader,
    AppSidebar,
    AppAside,
    TheFooter,
    Breadcrumb,
    DefaultAside,
    DefaultHeaderDropdownAccnt,
    SidebarForm,
    SidebarFooter,
    SidebarToggler,
    SidebarHeader,
    SidebarNav,
    SidebarMinimizer
  },
  data () {
    return {
      nav: []
    }
  },
  methods: {
    listarFuncionalidades: function () {
      var self = this
      console.log(process.env.VUE_APP_APLICACION_URL)
			let token_json = window.localStorage.getItem('token')
			let frm = {
        token: token_json,
        url: process.env.VUE_APP_APLICACION_URL
			}
      this.$http.post('ws/funcionalidad/listar_por_aplicacion_usuario.php', frm).then(resp => {
				let respuesta = resp.data
        this.nav = resp.data.funcionalidades
        this.$loader.close()
      }).catch(err => {
        this.$loader.close()
        if (err) {
          console.log(err)
          this.$toast.error('xxx' + err)
        }
      })
    }
  },
  created: function () {
    this.listarFuncionalidades()
	},
	mounted: function () {
    this.$loader.close()
    this.$loader.open({message: 'Cargando funcionalidades...'})
  },
  computed: {
    name () {
      return this.$route.name
    },
    list () {
      return this.$route.matched.filter((route) => route.name || route.meta.label )
    }
  }
}
</script>
