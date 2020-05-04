import React from 'react';
import { createDrawerNavigator, DrawerItems } from 'react-navigation-drawer';
import { createAppContainer } from 'react-navigation'
import {StyleSheet, View, SafeAreaView, ScrollView, Image, Text} from 'react-native'
import Electrovanne from '../Components/Electrovanne.js'
import Conso from '../Components/Consommation.js'
import Accueil from  '../Components/Accueil.js'
import EnTete from '../Components/EnTete'
import { createStackNavigator } from 'react-navigation-stack';



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

/*const Stack = createStackNavigator({
	FirstView: {
        screen: Accueil,
        navigationOptions: ({ navigation }) => ({
            header: <View style={{height: 100,
                justifyContent: 'center',
                alignItems: 'center',
                backgroundColor: 'orange',}}><Text>Header</Text></View>
          }),
    },
	SecondView: {
        screen: Conso,
        navigationOptions: ({ navigation }) => ({
            header: <View style={{height: 100,
                justifyContent: 'center',
                alignItems: 'center',
                backgroundColor: 'orange',
            }}><Text>Header</Text></View>
    }),
},
	ThirdView: {
        screen: Electrovanne,
        navigationOptions: ({ navigation }) => ({
            header: <View style={{height: 100,
                justifyContent: 'center',
                alignItems: 'center',
                backgroundColor: 'orange',
            }}><Text>Header</Text></View>
    }),
    }},
    {
        headerMode: 'float', // set this header mode to float so you can share the header
  initialRouteName: 'FirstView',
    }
);*/


const Drawer = createDrawerNavigator({
    Accueil: {screen: Accueil},
    Consommation: {screen: Conso},
    Electrovanne: {screen: Electrovanne},
},
{
    contentComponent: persoDrawer
});

const styles = StyleSheet.create(
    {
    
    main_container:
    {
       flex : 1,
    },
    })

    
export default createAppContainer(Drawer);

       