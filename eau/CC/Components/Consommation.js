import React, { Component, Fragment } from 'react';
import { StyleSheet, FlatList, View, TextInput, Text, Switch } from 'react-native';
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
    }


    Consommation()
    {
        //let formdata = new FormData();
        //formdata.append("Id", 85)
        fetch('http://90.3.8.46/api_rest/api.php', {
            method: 'POST',
            //body: formdata
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
    
    render()
    {
        return(
            
        <View style={styles.main_container}>
            <EnTete titre='Consommation' navigation={this.props.navigation}/>
        
                
        <View style={styles.reste}>
            <Button
            style={styles.BoutonConso} 
            onPress={() => {this.Consommation();console.log(this.state.data)}}>
            Consommation
            </Button>
            <FlatList
          data={this.state.data}
          keyExtractor={({ id }, index) => id}
          renderItem={({ item }) => (
            <Text>{item.Date}  : {item.Consommation}</Text>
          )}
        />

        </View>
        </View>
              

        )

    }
}

/**/
const styles = StyleSheet.create(
{

    main_container:
    {
        flex : 1,
        backgroundColor : '#2F4F4F'    
    },

    reste:
    {
        flex : 10,
    },

    blanc:
    {
        color: 'white'
    },

})

export default Conso
