import ValuesPush from './use/values-push.js'
import GitLog from './fields/git-log.vue'
import GitRegivions from './fields/git-revisions.vue'

panel.plugin("wottpal/git", {
  use: [ValuesPush],
  fields: {
    gitlog: GitLog,
    gitRevisions: GitRegivions
  }
})
