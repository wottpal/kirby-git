import ValuesPush from './use/values-push.js'
import GitLog from './fields/git-log.vue'
import GitRevisions from './fields/git-revisions.vue'

panel.plugin("wottpal/git", {
  use: [ValuesPush],
  fields: {
    gitLog: GitLog,
    gitRevisions: GitRevisions
  }
})
