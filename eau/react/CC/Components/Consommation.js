import React, { Component, Fragment } from 'react';
import { StyleSheet, FlatList, View, Text } from 'react-native';
import Button from 'react-native-button';
import EnTete from '../Components/EnTete'

 
class Conso extends Component 
{
    constructor(props)
    {
        super(props);
        this.state = 
        {
            switchValue:true,
            data :[],            
            loading :true
        }
        
        let formdata = new FormData();
        formdata.append("nb_valeur", 25)
        fetch(global.IpApi, {
            method: 'POST',
            body: formdata
        })
        .then((response) => response.json())
        .then((responsejson) => 
        {

        var array = [];

        for (let prop in responsejson) 
        {
            array.push(responsejson[prop]);
            
        }
        this.setState({data: array})
        
        })
      .catch((error) => console.error(error))
      .finally(() => {
        this.setState({ Loading: false });
    });
    }


    Consommation()
    {
        
    }
    
    keyExtractor = (item ,index ) => index.toString()

    render()
    {
        return(
            
        <View style={styles.main_container}>
            <EnTete titre='Consommation' navigation={this.props.navigation}/>
        
                
        <View style={styles.reste}>
        <Text style={styles.titre}> Dernière consommation </Text>
            <FlatList
            data={this.state.data}
            keyExtractor={this.keyExtractor}
            renderItem={({ item }) => (
                <View 
                style={styles.listItem}>  
                <Text style={styles.blanc}>{item.Date}  : {item.Consommation}</Text>
                </View>
          )}
        />
        <Button
            style={styles.BoutonConso} 
            onPress={() => {this.Consommation();console.log(this.state.data)}}>
            Plus
        </Button>

        </View>
        </View>
              

        )

    }
}
/**

 */
const styles = StyleSheet.create(
{

    main_container:
    {
        flex : 1,
        backgroundColor : '#2F4F4F'    
    },

    listItem:
    {
        backgroundColor: 'grey'
    },

    reste:
    {
        flex : 10,
        alignItems : 'center'
    },

    titre:
    {
        color: 'darkblue',
        fontSize: 20
    },

    blanc:
    {
        color: 'white',
        fontSize: 20
    },

})

export default Conso
