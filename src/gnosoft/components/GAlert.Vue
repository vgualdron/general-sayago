<template>
    <b-modal ref="gAlert" hide-footer :title="title">
        <div class="d-block text-center">
            <h3>{{ message }}</h3>
        </div>
        <b-btn class="mt-3" variant="outline-danger" block @click="onCancelClick">{{ textCancel }}</b-btn>
        <b-btn class="mt-3" variant="outline-primary" block @click="onCancelOk">{{ textOk }}</b-btn>
    </b-modal>
</template>
<script>
import Vue from Vue

Vue.component('smart-list', {
  functional: true,
  render: function (createElement, context) {
    function appropriateListComponent () {
      var items = context.props.items
      if (items.length === 0)           return EmptyList
      if (typeof items[0] === 'object') return TableList
      if (context.props.isOrdered)      return OrderedList
      return UnorderedList
    }
    return createElement(
      appropriateListComponent(),
      context.data,
      context.children
    )
  },
  props: {
    items: {
      type: Array,
      required: true
    },
    isOrdered: Boolean
  },
  data: {
    title: 'Gnosoft Academico',
    message: 'Mensaje',
    textCancel: 'Cancelar',
    textOk: 'Aceptar',
    onCancel: function () {},
    onOK: function () {}
  },
  methods: {
    onCancelClick: function () {
      this.onCancel()
    },
    onOkClick: function () {
      this.onOk()
    }
  }
})
<script>
