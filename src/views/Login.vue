<template>
  <b-container>
    <b-row>
      <b-col></b-col>
      <b-col sm="10" md="8" lg="6" xl="5">
        <b-card class="animated fadeIn p-4">
          <b-img style="width: 100%;" src="static/img/logo_login.jpg" class="img-responsive" alt="Washinhouse"/>
          <p class="font-weight-light" style="font-size:40px;">Bienvenido</p>
          <p class="text-muted">Ingrese su cuenta</p>
          <div class="mb-3">
            <input @keyup.enter="irContrasena()"
                   type="text"
                   ref="cuenta"
                   v-model="cuenta"
                   placeholder="Cuenta"
                   class="form-control"/>
          </div>
          <div class="mb-3">
            <!--<input type="text" placeholder="Contraseña" class="form-control">-->
            <input @keyup.enter="iniciarSesion()"
                   type="password"
                   ref="contrasena"
                   v-model="contrasena"
                   placeholder="Contraseña"
                   class="form-control"/>
          </div>
          <b-row>
            <b-col cols="12">
              <b-button @click="iniciarSesion" variant="primary" style="width: 100%;" class="px-4">Ingresar</b-button>
            </b-col>
          </b-row>
        </b-card>
      </b-col>
      <b-col></b-col>
    </b-row>
  </b-container>
</template>

<script>
import axios from 'axios'
import resource from 'vue-resource'
import GMsgBox from '@/gnosoft/components/GMsgBox'

const USER_ABORT_SESION = 'USER_ABORT_SESSION'
const NOT_PREVIUS_USER = 'NOT_PREVIUS_USER'

export default {
  name: 'Login',
  data () {
    return {
      cuenta: '',
      contrasena: '',
      loaderOpen: false,
      loaderTimeout: null
    }
  },
  created () {
  },
  methods: {
    irContrasena: function () {
      this.$refs.contrasena.focus()
    },
    startSession: function () {
      this.$router.push({path: 'home'})
    },
    iniciarSesion: function () {
      this.$loader.open({message: 'Iniciando Sesion ...'})
      if (this.cuenta === '') {
        this.$toast.error('Debe ingresar la cuenta del usuario')
        return
      }
      if (this.contrasena === '') {
        this.$toast.error('Debe ingresar la contraseña del usuario')
        return
      }

      var frm = {
        cuenta: this.cuenta,
        clave: this.contrasena
      }
      console.log('login frm ', frm)
      var self = this
      
      this.$http.post('/ws/inicio/autenticacion.php', frm).then(resp => {
      // this.$http.get('/ws/inicio/login.php', frm).then(resp => {
        let respuesta = resp.data
        window.localStorage.setItem('token', respuesta.token)
        self.$router.push({path: 'home'})
        self.$loader.close()
      }).catch(err => {
        if (err) {
          self.$loader.close()
          console.log(err)
          self.$toast.error(respuesta.mensaje)
        }
      })
    }
  }
}
</script>
