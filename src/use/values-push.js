export default function (Vue) {
  const original = Vue.options.components["k-fields-section"]

  //apply this patch only once
  if(original.options.methods.valuesPush) return;

  Vue.component('k-fields-section', {
    extends: original,
    created() {
      this.$events.$on('values-push', this.valuesPush)
    },
    destroyed() {
      this.$events.$off('values-push', this.valuesPush)
    },
    methods: {
      valuesPush(values) {
        let changes = false
        for (let k in values) {
          if (k in this.values) {
            this.values[k] = values[k]
            changes = true
          }
        }

        if (changes) {
          this.input(this.values)
        }
      }
    }
  })
}
