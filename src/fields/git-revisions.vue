<template>
  <k-field v-bind="$attrs" v-if="revisions.length">

    <div v-if="revisions.length">
      <ul class="k-structure k-structure--git">
        <li v-for="item in paginatedRevisions" :key="item.hash" class="k-structure-item" @click="item.selected ? null : applyRevision(item)" ref="structureItem" v-bind:class="{ 'k-structure-item--isSelected' : item.selected }">
          <div class="k-structure-item-wrapper">
            <div class="k-structure-item-content">

              <p v-for="(name, key) in infoColumns" class="k-structure-item-text">
                <span class="k-structure-item-label">
                  {{ name }}
                  <span v-if="name === 'Date' && item.first">(Latest)</span>
                </span>
                <span>{{ item[key] }}</span>
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
      No commits or no repository found.
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
    columns: {
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
      infoColumns: {
        dateFormatted: "Date"
      },
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
    this.initInfoColumns()
    this.initRevisions()
    this.paginate()
  },

  computed: {

    paginationOptions() {
      return {
        limit: this.limit,
        align: "center",
        details: true,
        keys: this.revisions.map( revision => revision.hash ),
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

    initInfoColumns() {
      const availableColumns = {
        author: "Author",
        hash: "Commit-Hash",
        message: "Commit-Message"
      }

      for (const column of this.columns) {
        if (column in availableColumns) {
          this.infoColumns[column] = availableColumns[column]
        }
      }
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

      // Gather associated author (Kirby/Git) for each commit
      this.revisions = this.revisions.map(revision => {
        const byString = "By: "
        const byLocation = revision.message.indexOf(byString)

        if (byLocation != -1) {
          revision.author = revision.message.substring(byLocation + byString.length)
          revision.authorSource = "Kirby"
        } else {
          revision.authorSource = "Git"
        }

        return revision
      })

      // Select latest revision and mark it as current
      if (this.revisions.length) {
        this.revisions[0].first = true
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

<style>

.k-structure-item--isSelected p {
  background: hsl(207, 97%, 97%);
  pointer-events: none;
}

</style>
