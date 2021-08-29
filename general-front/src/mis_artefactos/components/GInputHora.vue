<template>
  <div>
    <div style="width:90px;margin: 3px 3px 3px 3px;">
      <input
        @click="showModal = !showModal; actualizarFiltro(selectedHora, selectedMinuto, selectedHorario)"
        type="text"
        ref="inputHora"
        v-model="value"
        :disabled="disabled"
        class="form-control input-disabled"/>
    </div>
    <div>
    <!-- Modal Component -->
    <b-modal
      v-model="showModal"
      size="sm"
      id="modalHora"
      ref="modalHora">
      <div slot="modal-title">
        <p class="font-weight-light mb-1" style="font-size:20px; color:black;">
          Seleccione la hora
        </p>
      </div>
      <form @submit.stop.prevent="handleSubmit">
        <input
          @keyup.enter="formatDatetime()"
          type="text"
          ref="inputHora"
          v-model="value"
          class="form-control input-disabled"
          disabled/>
        </form>
        <b-form-select
          v-model="selectedHora"
          :options="horas"
          class="combos"
          :select-size="6"
          @change="changeHora">
        </b-form-select>
        <b-form-select
          v-model="selectedMinuto"
          :options="minutos"
          class="combos"
          :select-size="6"
          @change="changeMinuto"
          style="width:33%;display: inline;">
        </b-form-select>
        <b-form-select
          v-model="selectedHorario"
          :options="horarios"
          class="combos"
          :select-size="6"
          @change="changeHorario"
          style="width:33%;display: inline;">
        </b-form-select>
      <div slot="modal-footer" class="pull-right">
        <b-btn size="sm" variant="secundary" @click="showModal = false">
          cerrar
        </b-btn>
      </div>
    </b-modal>
    </div>
  </div>
</template>

<script>
export default {
  name: 'g-input-hora',
  props: ['valor', 'disabled'],
  components: {
  },
  created: function () {
    var x = null
    var hora = null
    var minuto = null
    var horario = 'AM'
    if (this.valor) {
      x = this.valor.split(':')
      hora = parseInt(x[0])
      minuto = x[1]
      this.selectedHora = x[0]
      this.selectedMinuto = minuto

      if (hora >= 12) {
        horario = 'PM'
        hora = hora - 12
        if (hora < 10) {
          hora = '0' + hora
        }
        if (parseInt(hora) === 0) {
          hora = 12
        }
      } else if (hora < 10) {
        hora = '0' + hora
        if (parseInt(hora) === 0) {
          hora = 12
        }
      }
      this.selectedHora = hora
      this.selectedHorario = horario
      this.value = hora + ':' + minuto + ' ' + horario
    }
  },
  data () {
    return {
      showModal: false,
      value: null,
      valueFormated: null,
      selectedHora: '01',
      horas: [
        { value: '01', text: '01' },
        { value: '02', text: '02' },
        { value: '03', text: '03' },
        { value: '04', text: '04' },
        { value: '05', text: '05' },
        { value: '06', text: '06' },
        { value: '07', text: '07' },
        { value: '08', text: '08' },
        { value: '09', text: '09' },
        { value: '10', text: '10' },
        { value: '11', text: '11' },
        { value: '12', text: '12' }
      ],
      selectedMinuto: '00',
      minutos: [
        { value: '00', text: '00' },
        { value: '01', text: '01' },
        { value: '02', text: '02' },
        { value: '03', text: '03' },
        { value: '04', text: '04' },
        { value: '05', text: '05' },
        { value: '06', text: '06' },
        { value: '07', text: '07' },
        { value: '08', text: '08' },
        { value: '09', text: '09' },
        { value: '10', text: '10' },
        { value: '11', text: '11' },
        { value: '12', text: '12' },
        { value: '13', text: '13' },
        { value: '14', text: '14' },
        { value: '15', text: '15' },
        { value: '16', text: '16' },
        { value: '17', text: '17' },
        { value: '18', text: '18' },
        { value: '19', text: '19' },
        { value: '20', text: '20' },
        { value: '21', text: '21' },
        { value: '22', text: '22' },
        { value: '23', text: '23' },
        { value: '24', text: '24' },
        { value: '25', text: '25' },
        { value: '26', text: '26' },
        { value: '27', text: '27' },
        { value: '28', text: '28' },
        { value: '29', text: '29' },
        { value: '30', text: '30' },
        { value: '31', text: '31' },
        { value: '32', text: '32' },
        { value: '33', text: '33' },
        { value: '34', text: '34' },
        { value: '35', text: '35' },
        { value: '36', text: '36' },
        { value: '37', text: '37' },
        { value: '38', text: '38' },
        { value: '39', text: '39' },
        { value: '40', text: '40' },
        { value: '41', text: '41' },
        { value: '42', text: '42' },
        { value: '43', text: '43' },
        { value: '44', text: '44' },
        { value: '45', text: '45' },
        { value: '46', text: '46' },
        { value: '47', text: '47' },
        { value: '48', text: '48' },
        { value: '49', text: '49' },
        { value: '50', text: '50' },
        { value: '51', text: '51' },
        { value: '52', text: '52' },
        { value: '53', text: '53' },
        { value: '54', text: '54' },
        { value: '55', text: '55' },
        { value: '56', text: '56' },
        { value: '57', text: '57' },
        { value: '58', text: '58' },
        { value: '59', text: '59' }
      ],
      selectedHorario: 'AM',
      horarios: [
        { value: 'AM', text: 'AM' },
        { value: 'PM', text: 'PM' }
      ]
    }
  },
  methods: {
    changeHora: function (hora) {
      this.actualizarFiltro(hora, this.selectedMinuto, this.selectedHorario)
    },
    changeMinuto: function (minuto) {
      this.actualizarFiltro(this.selectedHora, minuto, this.selectedHorario)
    },
    changeHorario: function (horario) {
      this.actualizarFiltro(this.selectedHora, this.selectedMinuto, horario)
    },
    actualizarFiltro: function (hora, minuto, horario) {
      var self = this
      self.value = hora + ':' + minuto + ' ' + horario
      if (horario === 'AM') {
        if (parseInt(hora) === 12 && parseInt(minuto) >= 1) {
          hora = (parseInt(hora) - 12)
        }
      }
      if (horario === 'PM') {
        if (parseInt(hora) === 12) {
          // hora = (parseInt(hora) + 12)
        } else if (parseInt(hora) < 10 && parseInt(hora) > 0) {
          hora = (parseInt(hora) + 12)
        } else if (parseInt(hora) > 9 && parseInt(hora) < 12) {
          hora = (parseInt(hora) + 12)
        }
      }
      self.valueFormated = hora + ':' + minuto
      this.$emit('input', self.valueFormated)
    }
  }
}
</script>
<style scoped>
.combos {
  width: 33%;
  display: inline-block;
  margin: -1px;
}
.input-disabled {
  background-color: #ffffff;
  cursor: pointer;
}
</style>
