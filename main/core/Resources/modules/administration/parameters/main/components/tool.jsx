import React from 'react'

import {ToolPage} from '#/main/core/tool/containers/page'

import {Nav} from '#/main/core/administration/parameters/main/components/nav'
import {Settings} from '#/main/core/administration/parameters/main/components/settings'

const Tool = () =>
  <ToolPage>
    <div className="row">
      <div className="col-md-3">
        <Nav/>
      </div>
      <div className="col-md-9">
        <Settings/>
      </div>
    </div>
  </ToolPage>

export {
  Tool
}