<template>
  <div>
    <header class="app-header navbar">

      <button class="navbar-toggler mobile-sidebar-toggler d-lg-none" type="button" @click="mobileSidebarToggle">
        <span class="navbar-toggler-icon"></span>
      </button>
      <b-link class="navbar-brand" to="#"></b-link>
      <button class="navbar-toggler sidebar-toggler d-md-down-none mr-auto" type="button" @click="sidebarToggle">
        <span class="navbar-toggler-icon"></span>
      </button>

      <b-navbar-nav class="ml-auto">
        <header-dropdown-account :user="user"/>
      </b-navbar-nav>

      <!--<button class="navbar-toggler aside-menu-toggler d-md-down-none" type="button" @click="asideToggle">
        <span class="navbar-toggler-icon"></span>
      </button>-->
      <!--<div class="ml-auto mr-4" style="cursor:pointer;color:white;" v-b-popover.hover="'Mensajeria'" @click="abrirMensajeria">
        <i class="icon-envelope"></i>
      </div>
      <div class="mr-4" style="cursor:pointer;color:white;" v-b-popover.hover="'Salir'" @click="salir">
        <i class="icon-logout"></i>
      </div>-->
    </header>
  </div>

</template>
<script>
import HeaderDropdownAccount from './Header/HeaderDropdownAccount'
export default {
  name: 'c-header',
  components: {HeaderDropdownAccount},
  props: {
    user: {
      type: Object,
      default: function () {
        return {id: '', nombre: '-'}
      }
    }
  },
  methods: {
    sidebarToggle (e) {
      e.preventDefault()
      document.body.classList.toggle('sidebar-hidden')
    },
    sidebarMinimize (e) {
      e.preventDefault()
      document.body.classList.toggle('sidebar-minimized')
    },
    mobileSidebarToggle (e) {
      e.preventDefault()
      document.body.classList.toggle('sidebar-mobile-show')
    },
    asideToggle (e) {
      e.preventDefault()
      document.body.classList.toggle('aside-menu-hidden')
    }
  }
}
</script>
