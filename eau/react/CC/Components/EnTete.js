import React, { Component } from 'react';
import { StyleSheet, View, Text, Image } from 'react-native';
import Button from 'react-native-button';


 

class EnTete extends Component 
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
            <View style={styles.menu_container}>
                <View style={styles.bouton_container}>
                    <Button
                        onPress={() => {this.props.navigation.openDrawer()}}> 
                        <Image source={require('../assets/Drawer.png')} style={{ height: 30, width: 30, }}/>
                    </Button>
                </View>
                <View style={styles.titre_container}>
                <Text style={styles.titleText}>
                    {this.props.titre}
                </Text>
                </View>
            </View>
        </View>
        
        
        )

    }
}


const styles = StyleSheet.create(
    {
        
        main_container:
        {
            backgroundColor : 'darkgrey',
        },


        menu_container:
        {
            marginTop: 35,
            backgroundColor : '#FF4500',
            flexDirection: 'row',
            height: 55,
            
        },

        titre_container:
        {
            marginTop: 12,
            marginLeft: 15,
            alignItems: 'flex-end'
        },

        bouton_container:
        {
            marginLeft :15,
            marginTop: 12
        },

        reste:
        {
            flex : 1,
            backgroundColor : 'darkgrey',
        },

        titleText: {
            fontSize: 22.5,
            fontWeight: "bold"
        },

    })

export default EnTete
