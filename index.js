

panel.plugin("wottpal/git", {

  // Props to @rasteiner
  use: [
    function (Vue) {
      const original = Vue.options.components["k-fields-section"]
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
  ],



  fields: {
    gitlog: {

      props: {
        gitLog: Array,
      },

      data: function() {
        return {
          log: [],
          reversed: false
        }
      },

      mounted: function () {
        this.initLog()
      },

      methods: {
        initLog() {
          this.log = JSON.parse(JSON.stringify(this.gitLog))

          this.log.map(item => {

            // Get k-Author
            const byString = 'By: '
            const byLocation = item.message.indexOf(byString)

            if (byLocation != -1) {
              item.author = item.message.substring(byLocation + byString.length)  + " (Kirby)"
              item.message = item.message.substring(0, byLocation)

            } else {
              item.author = item.author + " (Git)"
            }

            // Convert Date
            if (item.date) {
              item.date = new Date(item.date * 1000)
              const dateString = item.date.toLocaleDateString()
              const minutesString = item.date.getMinutes() < 10 ? "0" + item.date.getMinutes() : item.date.getMinutes()
              const timeString = item.date.getHours() + ":" + minutesString
              item.date = `${dateString} - ${timeString}`
            }

          })

          if (this.reversed) this.log = this.log.reverse()
        },

        reverse() {
          this.reversed = !this.reversed
          this.initLog()
        }
      },

      template: `
      <k-field v-bind="$attrs" :help="log.length + ' Commits'">

      <k-button :icon="this.reversed ? 'angle-down' : 'angle-up'" slot="options" @click="reverse" v-if="log.length" />

      <ul class="k-structure k-structure--git k-structure--noAction" v-if="log.length">
      <li v-for="item in log" :key="item.commit" class="k-structure-item">
      <div class="k-structure-item-wrapper">
      <div class="k-structure-item-content">
      <p class="k-structure-item-text">{{item.message}}</p>
      <p class="k-structure-item-text">{{item.author}}</p>
      <p class="k-structure-item-text">{{item.date}}</p>
      </div>
      </div>
      </li>
      </ul>

      <k-box v-else>
      No commits or no repository was found.
      </k-box>

      </k-field>
      `
    },




    gitRevisions: {

      props: {
        gitRevisions: Array,
        fields: Array,
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

          for(field of revision.updateFields) {
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

      template: `

      <k-field v-bind="$attrs" v-if="revisions.length">


      <ul class="k-structure k-structure--git" v-if="revisions.length">
        <li v-for="(item, idx) in paginatedRevisions" :key="item.commit" class="k-structure-item" @click="{ item.selected ? null : applyRevision(item) }" ref="structureItem">
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


      <k-box v-else>
      No commits or no repository was found.
      </k-box>

      </k-field>
      `
    }


  }
});
