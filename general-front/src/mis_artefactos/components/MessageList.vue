<template>
  <b-table :fields="fields" :items="items" striped small :responsive="responsive">
    <!-- A virtual column -->
    <template slot="index" slot-scope="data">
      <b-form-checkbox :id="'msg' + data.index"
                     :value="data.idMensaje">
        {{data.index + 1}}
      </b-form-checkbox>
    </template>
    <!-- A custom formatted column -->
    <template slot="estado" slot-scope="data">
      <i :class="['fa', estadoIcon(data.value)]"></i>
      {{data.value}}
    </template>
  </b-table>
</template>

<script>
export default {
  data: function () {
    return {
      fields: [
        // A virtual column that doesn't exist in items
        'index',
        // A column that needs custom formatting
        {key: 'de', label: 'De'},
        {key: 'asunto', label: 'Asunto'},
        {key: 'fecha', label: 'Fecha'},
        {key: 'estado', label: 'Estado'}
      ],
      items: [
        {de: 'VACCA DURAN NUMAEL', fecha: '2017-10-05', estado: 'Leido', asunto: 'Mensaje 01'},
        {de: 'VACCA ZABALETA LIZ VALERIA', fecha: '2017-09-05', estado: 'NO Leido', asunto: 'Mensaje 02'},
        {de: 'VACCA ZABALETA NAELA MARIA', fecha: '2017-11-05', estado: 'Leido', asunto: 'Mensaje 03'},
        {de: 'ZABALETA BARAGAN ELSA', fecha: '2017-10-05', estado: 'No Leido', asunto: 'Mensaje 04'},
        {de: 'VACCA DURAN NUMAEL', fecha: '2016-10-05', estado: 'Leido', asunto: 'Mensaje 05'}
      ]
    }
  },
  props: {
    responsive: {type: Boolean, default: false}
  },
  methods: {
    estadoIcon: function (estado) {
      return estado === 'Leido' ? 'fa-envelope-o' : 'fa-envelope-open-o'
      // return estado === 'Leido' ? 'icon-envelope' : 'icon-envelope-open'
    }
  }
}
</script>
