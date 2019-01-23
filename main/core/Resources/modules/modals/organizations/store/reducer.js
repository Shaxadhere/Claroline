import {makeListReducer} from '#/main/app/content/list/store'

import {selectors} from '#/main/core/modals/organizations/store/selectors'

const reducer = makeListReducer(selectors.STORE_NAME)

export {
  reducer
}
