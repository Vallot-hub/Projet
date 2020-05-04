// Components/Search.js

import React, { Component } from 'react';
import { StyleSheet, View, Button, Text, Switch } from 'react-native';
import EnTete from '../Components/EnTete' 

//import Switch from 'react-native-customisable-switch';

class Electrovanne extends Component 
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
        formdata.append("DerniereValeur", 1)
        fetch('http://90.3.8.46/api_rest/api.php', {
         method: 'POST',
        body: formdata
        })
        .then((response) => response.json())
        .then((responsejson) => {

            var array = [];

        for (let prop in responsejson) 
        {
            array.push(responsejson[prop]);
        }
        var elec=array[0].Electrovanne;
        if (elec==1)
        {
            this.setState({switchValue:true})
        }
        else if (elec==0)
        {
            this.setState({switchValue:false})
        }
        console.log(array[0].Electrovanne)
        })
        .catch((error) => console.error(error))
      .finally(() => {
        this.setState({ Loading: false });
    });   
        
    }

    toggleSwitch = (value) => {this.setState({switchValue: value});
    this.electrovanne();}

     
    electrovanne() 
    {
        var val;
        this.state.switchValue ? val=0 : val=1;
        let formdata = new FormData();
        formdata.append("Etat_electrovanne", val)
        fetch('http://90.3.8.46/api_rest/api.php', {
            method: 'POST',
            body: formdata
    });
    }
    
    
    
    render()
    {
        return(
    
        <View style={styles.main_container}>
            <EnTete titre='Electrovanne' navigation={this.props.navigation}/>
        <View style={styles.electro}><Text style={styles.blanc}> Etat de l'électrovanne :          </Text>
            <Switch
                style={styles.switch}
                value={this.state.switchValue}
                onValueChange={this.toggleSwitch}
                trackColor={{ false: "#767577", true: "#81b0ff" }}
                thumbColor={this.state.SwitchValue ? "#f5dd4b" : "#f4f3f4"}
            />
        </View>
                
        
        </View>
              

        )

    }
}


const styles = StyleSheet.create(
{

    main_container:
    {
        flex : 1,
    },

    titre:
    {
        flex : 1,
        justifyContent: 'space-around',
        backgroundColor : '#FF4500',
        flexDirection: 'row',
    },

    electro:
    {
        flex : 2 ,
        alignItems: 'flex-start',
        backgroundColor : '#2F4F4F',
        flexDirection: 'row',
        //alignItems: 'center'
    },

    reste:
    {
        flex : 10,
    },

    blanc:
    {
        color: 'white'
    },

    TextInput :
    {
        marginTop: 0, 
        marginLeft: 5, 
        marginRight: 5, 
        height: 50, 
        borderColor: '#000000', 
        borderWidth: 1, 
        paddingLeft: 5 
    },

    electrovanne :
    {
        backgroundColor : 'blue'
    },

    switch :
    {
        transform: [{ scaleX: 1.5 }, { scaleY: 1.5}]
    }

})
/*
const SearchStackNavigator = createStackNavigator({
    Electrovanne: { // Ici j'ai appelé la vue "Search" mais on peut mettre ce que l'on veut. C'est le nom qu'on utilisera pour appeler cette vue
    screen: Electrovanne,
    navigationOptions: {
      title: 'electrovanne'
    }
    },
    Accueil: {
      screen: Accueil,
      navigationOptions: {
        title: 'Accueil'
      }
    },

    }
)*/

export default Electrovanne

