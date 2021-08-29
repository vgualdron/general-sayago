<template>
  <transition name="bounce">
    <div :id="$gconfig.safeId('fp')" :class="['row', 'mb-1', 'g-file-progress-row', status, hidden]" v-if="show">
      <div class="col-sm-6 g-file-progress-text">
        <b-btn size="sm" @click="cancel"><i class="fa fa-close"></i></b-btn>
        {{ file && file.name }} ({{ file && file.size | fileSize }})
      </div>
      <div class="col-sm-6">
          <b-progress height="1.3rem" :value="loaded" :max="max" :variant="variant" show-progress></b-progress>
      </div>
    </div>
  </transition>
</template>
<script>
import Vue from 'vue'

export default {
  name: 'g-file-progress',
  props: {
    file: {
      type: File,
      default: null
    }
  },
  data: function () {
    return {
      variant: 'primary',
      hidden: '',
      status: '',
      loaded: 0,
      show: true,
      max: 100
    }
  },
  methods: {
    listener: function (e) {
      if (e.lengthComputable) {
        this.loaded = (e.loaded / e.total) * 100
      }
    },
    cancel: function () {
      Vue.gsender.cancel(this)
      this.$emit('cancel', this.file)
      var self = this
      setTimeout(function () {
        // self.hidden = 'hidden'
        self.show = false
      }, 1000)
    }
  },
  watch: {
    status: function (newVal, oldVal) {
      // console.log('status cambiado: ' + this.file.name + ' -> ' + newVal)
      if (newVal === 'error') {
        this.variant = 'danger'
        this.$emit('error', this.file)
      }
      // if (newVal === 'cancelado') {
      // this.$emit('cancel', this.file)
      // }
    }
  },
  mounted: function () {
    Vue.gsender.push(this)
  }
}
</script>
<style>
  .g-file-progress-row.error .g-file-progress-text {
    color: red;
  }
  .g-file-progress-row.cancelado .g-file-progress-text {
    color: gray;
  }
  .g-file-progress-row.cancelado.hidden {
    display: none;
  }
.bounce-enter-active {
  /* animation: bounce-in .5s; */
}
.bounce-leave-active {
  animation: bounce-in .5s reverse;
}
@keyframes bounce-in {
  0% {
    transform: scale(0);
  }
  50% {
    transform: scale(1.5);
  }
  100% {
    transform: scale(1);
  }
}
</style>
