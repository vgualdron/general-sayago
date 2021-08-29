<template>
  <div>
    <a :class="styleClass" href="#" @click="click">
      <slot>{{ text }}</slot>
    </a>
    <input type="file" :multiple="multiple" style="display: none;" ref="input" @change="onFileChange"></input>
  </div>
</template>

<script>
export default {
  data () {
    return {
      selectedFile: null
    }
  },
  props: {
    accept: {
      type: String,
      default: ''
    },
    capture: {
      // Instruct input to capture from camera
      type: Boolean,
      default: false
    },
    text: {
      type: String,
      default: 'Seleccionar Archivo'
    },
    multiple: {
      type: Boolean,
      default: false
    },
    directory: {
      type: Boolean,
      default: false
    },
    styleClass: {
      type: String,
      default: ''
    }
  },
  watch: {
    selectedFile: function (newVal, oldVal) {
      if (newVal === oldVal) {
        return
      }

      if (!newVal && this.multiple) {
        this.$emit('input', [])
      } else {
        this.$emit('input', newVal)
      }
    }
  },
  methods: {
    click: function (e) {
      e.preventDefault()
      console.log('Click en archivos')
      this.$refs.input.click()
    },
    reset: function () {
      try {
        // Wrapped in try in case IE < 11 craps out
        this.$refs.input.value = ''
      } catch (e) {
      }

      // IE < 11 doesn't support setting input.value to '' or null
      // So we use this little extra hack to reset the value, just in case
      // This also appears to work on modern browsers as well.
      this.$refs.input.type = ''
      this.$refs.input.type = 'file'

      this.selectedFile = this.multiple ? [] : null
    },
    onFileChange: function (e) {
      // Always emit original event
      this.$emit('change', e)

      // Normal handling
      this.setFiles(e.target.files || e.dataTransfer.files)
    },
    setFiles: function (files) {
      if (!files) {
        this.selectedFile = null
        return
      }

      if (!this.multiple) {
        this.selectedFile = files[0]
        return
      }

      // Convert files to array
      const filesArray = []
      for (let i = 0; i < files.length; i++) {
        if (files[i].type.match(this.accept)) {
          filesArray.push(files[i])
        }
      }

      this.selectedFile = filesArray
    }
  }
}
</script>
