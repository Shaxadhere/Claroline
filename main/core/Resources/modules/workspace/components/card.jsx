import React from 'react'
import {PropTypes as T} from 'prop-types'
import get from 'lodash/get'

import {trans} from '#/main/app/intl/translation'
import {asset} from '#/main/app/config/asset'

import {DataCard} from '#/main/app/content/card/components/data'
import {Workspace as WorkspaceTypes} from '#/main/core/workspace/prop-types'

const WorkspaceCard = props =>
  <DataCard
    {...props}
    id={props.data.id}
    poster={props.data.thumbnail ? asset(props.data.thumbnail.url) : null}
    icon="fa fa-book"
    title={props.data.name}
    subtitle={props.data.code}
    flags={[
      get(props.data, 'meta.personal')                       && ['fa fa-user',      trans('personal_workspace')],
      get(props.data, 'meta.model')                          && ['fa fa-briefcase', trans('model')],
      get(props.data, 'display.displayable')                 && ['fa fa-eye',       trans('displayable_in_workspace_list')],
      get(props.data, 'registration.selfRegistration')       && ['fa fa-globe',     trans('public_registration')],
      get(props.data, 'registration.waitingForRegistration') && ['fa fa-hourglass', trans('pending')]
    ].filter(flag => !!flag)}
    contentText={get(props.data, 'meta.description')}
    footer={
      <span>
        created by <b>{get(props.data, 'meta.creator') ? props.data.meta.creator.name : trans('unknown')}</b>
      </span>
    }
  />

WorkspaceCard.propTypes = {
  data: T.shape(
    WorkspaceTypes.propTypes
  ).isRequired
}

export {
  WorkspaceCard
}