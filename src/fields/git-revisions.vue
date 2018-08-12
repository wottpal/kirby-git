<template>
  <k-field v-bind="$attrs" v-if="revisions.length">
    <div v-if="revisions.length">
      <ul class="k-structure k-structure--git">
        <li v-for="(item) in paginatedRevisions" :key="item.commit" class="k-structure-item" @click="item.selected ? null : applyRevision(item)" ref="structureItem">
          <div class="k-structure-item-wrapper">
            <div class="k-structure-item-content">
              <p class="k-structure-item-text" v-bind:class="{ 'k-structure-item-text--isSelected' : item.selected }">
                <span class="k-structure-item-label">Date</span>
                <span>{{item.dateFormatted}}</span>
              </p>
              <p class="k-structure-item-text" v-bind:class="{ 'k-structure-item-text--isSelected' : item.selected }">
                <span class="k-structure-item-label">Commit</span>
                <span>{{item.commit}}</span>
              </p>
            </div>
          </div>
        </li>
      </ul>

      <k-pagination
        v-bind="paginationOptions"
        ref="pagination"
        @paginate="paginate($event)"
        />
    </div>

    <k-box v-else>
      No commits or no repository was found.
    </k-box>

  </k-field>
</template>

<script>
export default {
  props: {
    gitRevisions: Array,
    fields: {
      type: Array,
      default: []
    },
    limit: {
      default: 5,
      type: Number
    }
  },

  data: function() {
    return {
      revisions: [],
      paginatedRevisions: []
    }
  },

  created: function() {
    this.$events.$on('form.change', this.onFormChange);
    this.$events.$on('form.save', this.onFormSave);
    this.$events.$on('form.reset', this.onFormReset);
  },
  destroyed: function () {
    this.$events.$off('form.change', this.onFormChange);
    this.$events.$off('form.save', this.onFormSave);
    this.$events.$off('form.reset', this.onFormReset);
  },


  mounted: function () {
    this.initRevisions()
    this.paginate()
  },

  computed: {

    paginationOptions() {
      return {
        limit: this.limit,
        align: "center",
        details: true,
        keys: this.revisions.map( revision => revision.commit ),
        total: this.revisions.length,
        hide: false,
      }
    }

  },

  methods: {

    onFormChange() {

    },
    onFormSave() {

    },
    onFormReset() {
      if (this.revisions.length) this.revisions[0].selected = true
    },

    initRevisions() {
      console.log(this.gitRevisions)
      this.revisions = JSON.parse(JSON.stringify(this.gitRevisions))

      // Filter out revisions which have none of the given keys
      this.revisions = this.revisions.filter(revision => {
        const availableFields = Object.keys(revision['content'])
        const intersection = availableFields.filter(value => -1 !== this.fields.indexOf(value))
        revision.updateFields = intersection
        return intersection.length > 0
      })

      // Select latest revision and mark it as current
      if (this.revisions.length) {
        this.revisions[0].dateFormatted += " (Current)";
        this.revisions[0].selected = true
      }

    },

    applyRevision(revision) {
      this.revisions.forEach( revision => revision.selected = false )
      revision.selected = true
      this.$forceUpdate()

      for(let field of revision.updateFields) {
        const fieldContent = revision['content'][field]
        this.$events.$emit('values-push', { [field] : fieldContent })
      }
    },

    paginate(event) {
      let start = 0
      let end = Math.min(this.revisions.length, this.limit)

      if (event) {
        start = event.start - 1
        end = event.end
      }

      this.paginatedRevisions = this.revisions.slice(start, end)
    }

  },
}
</script>
