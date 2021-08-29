<template>
      <div>
        <b-nav-item-dropdown right no-caret>
          <template slot="button-content">
            <img src="static/img/silueta.png" class="img-avatar" alt="Mi Cuenta" v-if="imgError">
            <img :src="img" class="img-avatar" alt="Mi Cuenta" v-else @error="imageError">
            <b-badge v-if="user.notificaciones && user.notificaciones.length > 0" pill variant="danger">
              {{user.notificaciones.length}}
            </b-badge>
          </template>
          <b-dropdown-header tag="div" class="text-center">Cuenta</b-dropdown-header>
          <b-dropdown-item v-if="imgError" disabled class="center fondo-silueta">
            <img src="static/img/silueta.png" class="img-user" alt="Mi Cuenta">
            <br/>
            <span class="color-nombre">{{user.nombreCompleto}}</span>
          </b-dropdown-item>
          <b-dropdown-item v-else disabled class="center">
            <img :src="img" class="img-user" alt="Mi Cuenta">
            <br/>
            <span class="color-nombre">{{user.nombreCompleto}}</span>
          </b-dropdown-item>
          <b-dropdown-item @click="abrirMensajeria"><i class="icon-envelope color-icon"></i> Mensajeria</b-dropdown-item>
          <b-dropdown-item v-if="permisoReporteador" @click="abrirReporteador"><i class="icon-chart color-icon"></i> Reporteador</b-dropdown-item>
          <b-dropdown-item v-if="permisoReclamaciones" @click="abrirReclamaciones"><i class="icon-chart color-icon"></i>Reclamaciones</b-dropdown-item>
          <b-dropdown-item @click="gestionarNotificaciones(true)"><i class="icon-bell color-icon"></i> Notificaciones <b-badge v-if="user.notificaciones && user.notificaciones.length > 0" variant="danger">{{user.notificaciones.length}}</b-badge></b-dropdown-item>
          <b-dropdown-item @click="salir"><i class="icon-logout color-icon"></i> Salir</b-dropdown-item>
        </b-nav-item-dropdown>
        <b-modal v-model="notificaciones" id="notificaciones" title="Notificaciones">
          <b-container>
            <b-row>
              <b-col>
                <b-list-group v-if="user.notificaciones && user.notificaciones.length > 0">
                  <b-list-group-item v-for="notificacion in user.notificaciones" :key="notificacion.idNotificacion" class="flex-column align-items-start">
                    <div class="d-flex w-100 justify-content-between">
                      <b-container>
                        <b-row>
                          <b-col class="text-left p-0" cols="12" sm="8">
                            <span style="font-wiehgt:14px;font-weight:bold;" v-html="notificacion.titulo" class="mb-1"></span>
                          </b-col>
                          <b-col class="text-left p-0">
                            <small>{{notificacion.fecha}}</small>
                          </b-col>
                        </b-row>
                        <b-row>
                          <p class="mb-1" style="text-align:left;" v-html="notificacion.descripcion"></p>
                        </b-row>
                      </b-container>
                      <p @click="eliminarNotificacion(notificacion)" style="cursor:pointer;"><i class="fa fa-times"></i></p>
                    </div>
                  </b-list-group-item>
                </b-list-group>
                <b-alert v-else show class="text-center">En estos momentos no tiene notificaciones pendientes</b-alert>
              </b-col>
            </b-row>
          </b-container>
          <div slot="modal-footer" class="pull-right">
            <b-btn size="sm" @click="gestionarNotificaciones(false)">
              Cerrar
            </b-btn>
          </div>
        </b-modal>
      </div>
</template>
<script>
import GMsgBox from '../../gnosoft/components/GMsgBox'

var _ = require('lodash')

export default {
  name: 'header-dropdown-account',
  props: {
    user: {
      type: Object,
      default: function () {
        return {}
      }
    }
  },
  data: function () {
    return {
      imgError: true,
      notificaciones: false,
      notificacionesLeidas: [],
      img: 'static/img/silueta.png',
      permisoReporteador: false,
      permisoReclamaciones: false,
      idReclamacion: null
    }
  },
  mounted: function () {
    // this.img = '../general/imgGeneral?id=' + this.user.id
    this.$events.$on('userLoaded', this.updateUserInfo)
    this.validarPermisoReporteador()
    this.validarPermisoReclamacion()
  },
  methods: {
    updateUserInfo: function (user) {
      this.img = '../general/imgGeneral?id=' + user.id
    },
    validarPermisoReporteador: function () {
      this.$http.get('/ws/general/validar_permiso_reporteador').then(resp => {
        this.permisoReporteador = resp.data.permiso
      }).catch(resp => {

      })
    },
    validarPermisoReclamacion: function () {
      this.$http.get('/ws/general/validar_permiso_reclamaciones').then(resp => {
        this.permisoReclamaciones = resp.data.permiso
        this.idReclamacion = resp.data.idAplicacion
      }).catch(resp => {

      })
    },
    imageError: function () {
      this.imgError = true
    },
    abrirMensajeria (e) {
      window.open('/mensajeria-v3/login-rt?rt=' + window.localStorage.getItem('refreshToken'), '_mensajeria')
    },
    abrirReporteador (e) {
      window.open('/general-ws/front/?v=3.0.1.38#/view_reporteador/login-rt?rt=' + window.localStorage.getItem('refreshToken'))
    },
    abrirReclamaciones (e) {
      window.open('/reclamaciones/login-rt?rt=' + window.localStorage.getItem('refreshToken') + '&idAplicacion=' + this.idReclamacion)
    },
    gestionarNotificaciones: function (mostrar) {
      this.notificaciones = mostrar
    },
    eliminarNotificacion: function (notificacion) {
      this.notificacionesLeidas.push(notificacion.idNotificacion)
      var indice = this.user.notificaciones.findIndex(e => e.idNotificacion === notificacion.idNotificacion)
      if (indice > -1) {
        this.user.notificaciones.splice(indice, 1)
      }
      this.eliminarNotificaciones()
    },
    eliminarNotificaciones: _.debounce(function () {
      this.$http.post('ws/usuario/eliminar_notificaciones', {notificaciones: this.notificacionesLeidas}).then(resp => {
        this.notificacionesLeidas = []
      })
    }, 1000),
    salir (e) {
      var self = this
      GMsgBox.confirm({
        message: '¿ Desea Salir de la Aplicación ?',
        onOk: function () {
          if (window.localStorage.getItem(self.user.id)) {
            self.$http.post('ws/usuario/eliminar_token', {token: window.localStorage.getItem(self.user.id)}).then(resp => {
              window.localStorage.removeItem(self.user.id)
            }).catch(resp => {
              console.log('Ocurrió un error al eliminar el token del usuario')
              console.log(resp)
            }).then(() => {
              window.localStorage.removeItem('accessToken')
              window.localStorage.removeItem('refreshToken')
              self.$router.push({path: 'login'})
            })
          } else {
            window.localStorage.removeItem('accessToken')
            window.localStorage.removeItem('refreshToken')
            self.$router.push({path: 'login'})
          }
        }
      })
    }
  }
}
</script>

<style scoped>
.img-user {
  width: 120px;
  height: 120px;
  border-radius: 60px;
}
.center {
  text-align: center;
}
.color-icon{
  color:#36a9e1;
}
.fondo-silueta{
  background-color: #f0f2f7;
}
.color-nombre{
  color:black;
}
</style>
