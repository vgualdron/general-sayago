<template>
  <b-container>
    <b-row>
      <b-col></b-col>
      <b-col sm="10" md="8" lg="6" xl="5">
        <b-card class="animated fadeIn p-4">
          <b-img style="width: 100%;" src="static/img/acceso_denegado.jpg" class="img-responsive" alt="Washinhouse"/>
          <p class="font-weight-light" style="font-size:40px;">Error. No tiene los permisos necesarios!</p>
          
          
        </b-card>
      </b-col>
      <b-col></b-col>
    </b-row>
  </b-container>
</template>

<script>

export default {
  name: 'Error',
  data () {
    return {
    }
  },
  mounted () {
  },
  methods: {
  }
}
</script>
