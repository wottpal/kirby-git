panel.plugin("wottpal/git", {
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

      <ul class="k-structure k-structure--gitLog" v-if="log.length">
        <li v-for="item in log" :key="item.commit" class="k-structure-item">
        <div class="k-structure-item-wrapper">
          <div class="k-structure-item-content">
            <p class="k-structure-item-text">{{item.message}}</p>
            <p class="k-structure-item-text">{{item.author}}</p>
            <p class="k-structure-item-text">{{item.date}}</p>
          </div>
        </li>
      </ul>

      <k-box v-else>
      No commits or no repository was found.
      </k-box>

      </k-field>
      `
    }

  }
});
