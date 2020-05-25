// App.js

import React from 'react'
import Menu from './Nav/Menu.js'


export default class App extends React.Component {
  constructor(){
    super();
    global.IpApi = 'http://86.252.162.139/api_rest/api.php';
  }

  render() {
    return (
      <Menu/>
    )
  }
}
