<template>
  <!-- Div Loading -->
  <div v-if="show" id="loading" ref="loading" style="display: block;">
    <div class="location_indicator">
      <img width="100px" src="static/img/loader_200.gif" alt="">
    </div>
    <p v-if="message" style="font-size: 16px;">{{message}}</p>
  </div>
  <!-- Div Loading -->
</template>
<script type ="text/babel">
import Vue from 'vue'
import BootstrapVue from 'bootstrap-vue'

Vue.use(BootstrapVue)

export default {
  components: {
  },
  data: function () {
    return {
      message: '',
      show: false
    }
  },
  methods: {
  }
}
</script>
<style scoped>
  #loading {
    background: rgba(56, 62, 75, 0.5);
    position: fixed;
    height: 100%;
    width: 100%;
    top: 0;
    left: 0;
    z-index: 9999;
    color: white;
    text-align: center;
    line-height: 94vh;
    font-size: 7vw;
  }
  .location_indicator {
    position: relative;
    width: 100px;
    margin: 0 auto;
    /* top: 44vh; */
    height: 100px;
  }
</style>
