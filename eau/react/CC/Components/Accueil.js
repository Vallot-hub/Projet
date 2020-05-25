import React, { Component, Fragment } from 'react';
import { StyleSheet, View } from 'react-native';

import LinearGradient from 'react-native-linear-gradient';

import EnTete from '../Components/EnTete'


 
class Accueil extends Component 
{
    constructor(props)
    { 
        super(props);
        this.state = 
        {

        }
    }


    
    render()
    {   
        return(
    
        <View style={styles.main_container}>
            <EnTete titre='Accueil' navigation={this.props.navigation}/>
        

        </View>
              

        )

    }
}


const styles = StyleSheet.create(
{

    main_container:
    {
        flex : 1,
        backgroundColor : '#2F4F4F',
    },

    /*BoutonElec:
    {
        marginTop : 10,
        justifyContent: 'center',
        height : 50,
        fontSize: 30, 
        color: 'yellow',
        backgroundColor: '#841584'
    },

    BoutonConso:
    {
        marginTop : 10,
        justifyContent: 'center',
        height : 50,
        fontSize: 30, 
        color: 'green',
        backgroundColor: '#841584'
    },*/

    blanc:
    {
        color: 'white'
    },


})



export default Accueil

