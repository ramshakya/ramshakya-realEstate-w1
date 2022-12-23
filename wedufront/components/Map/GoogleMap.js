import React, { Component } from 'react';
    import { Map, GoogleApiWrapper, InfoWindow, Marker} from 'google-maps-react';
import Constants from "../../constants/GlobalConstants";
const mapStyles = {
  width: '100%',
  height: '94%'
};

export class MapContainer extends Component {
	constructor(props) {
        super(props);
        this.clearSearchBox = this.clearSearchBox.bind(this);
    }
	state = {
    showingInfoWindow: false,  // Hides or shows the InfoWindow
    activeMarker: {},          // Shows the active marker upon click
    selectedPlace: {}          // Shows the InfoWindow to the selected place upon a marker
  };
  onMarkerClick = (props, marker, e) =>
    this.setState({
      selectedPlace: props,
      activeMarker: marker,
      showingInfoWindow: true
    });

  onClose = props => {
    if (this.state.showingInfoWindow) {
      this.setState({
        showingInfoWindow: false,
        activeMarker: null
      });
    }
  };
  onPlaceChanged = ({ map, addplace } = this.props) => {
        const place = this.autoComplete.getPlace();

        if (!place.geometry) return;
        if (place.geometry.viewport) {
            map.fitBounds(place.geometry.viewport);
        } else {
            map.setCenter(place.geometry.location);
            map.setZoom(17);
        }

        addplace(place);
        this.searchInput.blur();
    };

    clearSearchBox() {
        this.searchInput.value = '';
    }
    
    componentDidMount() {
      // const script = document.createElement("script");
      // script.async = true;
      // script.src = "https://maps.googleapis.com/maps/api/js?key=AIzaSyBM8LFkdq9fISqWRWFt1u-rqsALyptk1g4";
      // script.onload = () => document.head.appendChild(script);
    }
  render() {
  	let lat = -1.2884;
	let lng = 36.8233;
	let googleSearch = 'Kenyatta International Convention Centre';
  	if(localStorage.getItem('googleSearch')){
	  	lat = localStorage.getItem('lat');
	  	lng = localStorage.getItem('lng');
	  	googleSearch = localStorage.getItem('googleSearch');
	  }
    return (
    	<>
      <Map
        google={this.props.google}
        zoom={14}
        style={mapStyles}
        className='mapWidth'
        initialCenter={
          {
            lat: lat,
            lng: lng
          }
        }
      >
      
      <Marker
          onClick={this.onMarkerClick}
          name={googleSearch}
        />
        <InfoWindow
          marker={this.state.activeMarker}
          visible={this.state.showingInfoWindow}
          onClose={this.onClose}
        >
          <div>
            <h4>{this.state.selectedPlace.name}</h4>
          </div>
        </InfoWindow>
        </Map>

        </>
    );
  }
}
let ApiKey = Constants.REACT_APP_GOOGLE_API_KEY;
      if(typeof window !== 'undefined'){
        if(localStorage.getItem('websetting')){
          let websetting =localStorage.getItem('websetting');
          if(websetting && websetting !=="undefined"){
            websetting = JSON.parse(websetting);
          if(websetting.GoogleMapApiKey!=null){
            ApiKey = websetting.GoogleMapApiKey;
          }  
          }
        } 
        
    }
   
export default GoogleApiWrapper({
  apiKey: ApiKey
})(MapContainer);