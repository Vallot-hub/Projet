// Navigation/Navigation.js

import { createStackNavigator } from 'react-navigation-stack'
import { createAppContainer } from 'react-navigation'
import Electrovanne from '../Components/Electrovanne.js'
import Conso from '../Components/Consommation.js'
import Accueil from  '../Components/Accueil.js'


const SearchStackNavigator = createStackNavigator({
    Accueil: {
      screen: Accueil,
      navigationOptions: {
        title: 'Accueil'
      }
    },
    Electrovanne: { // Ici j'ai appelé la vue "Search" mais on peut mettre ce que l'on veut. C'est le nom qu'on utilisera pour appeler cette vue
      screen: Electrovanne,
      navigationOptions: {
        title: 'electrovanne'
      }
    },
    Consommation: {
      screen: Conso,
      navigationOptions: {
        title: 'Consommation'
      }
    }
  })



  export default createAppContainer(SearchStackNavigator)