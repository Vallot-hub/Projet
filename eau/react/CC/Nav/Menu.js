import React from 'react';
import { createDrawerNavigator, DrawerItems } from 'react-navigation-drawer';
import { createAppContainer } from 'react-navigation'
import { View, SafeAreaView, ScrollView, Image, Text} from 'react-native'
import Electrovanne from '../Components/Electrovanne.js'
import Conso from '../Components/Consommation.js'
import Accueil from  '../Components/Accueil.js'
import EnTete from '../Components/EnTete'




const persoDrawer = (props) => (
    <SafeAreaView style={{ flex: 1}}>
        <View style={{height: 220, alignItems: 'center', backgroundColor: 'darkgrey'}}>
            <Image source={require('../assets/Drawer-image.png')} style={{ height: 180, width: 280, marginTop: 35}}/>
        </View>
        <ScrollView style={{ backgroundColor: '#2F4F4F'}}>
            <DrawerItems {...props} />
        </ScrollView>
    </SafeAreaView>
)


const Drawer = createDrawerNavigator({
    Accueil: {screen: Accueil},
    Consommation: {screen: Conso},
    Electrovanne: {screen: Electrovanne},
    EnTete: {screen: EnTete}
},
{
    contentComponent: persoDrawer
});

    
export default createAppContainer(Drawer);

       