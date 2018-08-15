<template>
  <k-field v-bind="$attrs">

    <div v-if="log.length">
    <ul class="k-structure k-structure--git k-structure--noAction">
      <li v-for="item in paginatedLog" :key="item.hash" class="k-structure-item">
        <div class="k-structure-item-wrapper">
          <div class="k-structure-item-content">


            <p class="k-structure-item-text">
              <span class="k-structure-item-label">{{ item.commitType }}</span>
              <span class="k-structure-item-path">
                <span v-for="component in item.commitSubject">
                  {{ component }}
                </span>
              </span>
            </p>

            <p class="k-structure-item-text">
              <span class="k-structure-item-label">Author</span>
              <span>{{ item.author }}</span>
            </p>

            <p class="k-structure-item-text">
              <span class="k-structure-item-label">Date</span>
              <span>{{ item.dateFormatted }}</span>
            </p>


          </div>
        </div>
      </li>
    </ul>

    <k-pagination
      v-bind="paginationOptions"
      ref="pagination"
      @paginate="paginate($event)" />
    </div>


    <k-box v-else>
      No commits or no repository found.
    </k-box>

  </k-field>
</template>

<script>
export default {
  props: {
    gitLog: Array,
    kirbyOnly: {
      type: Boolean,
      default: true
    },
    limit: {
      default: 5,
      type: Number
    }
  },

  data: function() {
    return {
      log: [],
      paginatedLog: []
    };
  },

  computed: {

    paginationOptions() {
      return {
        limit: this.limit,
        align: "center",
        details: true,
        keys: this.log.map( commit => commit.hash ),
        total: this.log.length,
        hide: false,
      }
    }

  },

  mounted: function() {
    this.initLog()
    this.paginate()
  },

  methods: {

    initLog() {
      this.log = JSON.parse(JSON.stringify(this.gitLog));

      this.log = this.log.map(commit => {
        // Get Author
        const byString = "By: ";
        const byLocation = commit.message.indexOf(byString);

        if (byLocation != -1) {
          commit.author =
            commit.message.substring(byLocation + byString.length);
          commit.message = commit.message.substring(0, byLocation);
          commit.authorSource = "Kirby";
        } else {
          commit.authorSource = "Git";
        }

        // Get Page & Commit-Type
        const actionPattern = /(.*):\w*(.*)\w*/
        const match = actionPattern.exec(commit.message)

        if (commit.authorSource == "Kirby" && match && match.length >= 2) {
          commit.commitType = match[1]
          commit.commitSubject = match[2].split('/')
        } else {
          commit.commitType = "Developer Commit"
          commit.commitSubject = [ commit.message ]
        }

        return commit

      }).filter(commit => {
        // Filter out non-kirby commits if `kirbyOnly` is set
        return commit.authorSource == "Kirby" || !this.kirbyOnly

      })
    },

    paginate(event) {
      let start = 0
      let end = Math.min(this.log.length, this.limit)

      if (event) {
        start = event.start - 1
        end = event.end
      }

      this.paginatedLog = this.log.slice(start, end)
    }



  }
};
</script>

<style>

  /* Enlarge first column */
  /* .k-structure--git .k-structure-item-content {
    grid-template-columns: minmax(0,2fr) repeat(auto-fit,minmax(0,1fr));
  } */

  /* Structure-Field without any action */
  .k-structure--noAction .k-structure-item-content:hover {
    background: hsla(0,0%,98%,.75);
  }
  .k-structure--noAction .k-structure-item-text {
    cursor: default;
  }
  .k-structure--noAction .k-structure-item-text:hover {
    background: inherit;
  }

  /* Path Component with seperators */
  .k-structure-item-path span:last-of-type {
    font-weight: 600;
  }
  .k-structure-item-path span:not(:last-of-type) {
    opacity: .75;
  }
  .k-structure-item-path span:not(:last-of-type):after {
    opacity: .25;
    font-weight: normal;
    content: " / ";
  }

</style>
