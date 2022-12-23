import axios from "axios";
import react,{useEffect} from "react";
const StatisticsArea=()=>{
	useEffect(()=>{
		const options = {
		  method: 'GET',
		  url: 'https://realty-in-ca1.p.rapidapi.com/properties/get-statistics',
		  params: {Longitude: '-85.8230136', Latitude: '49.1241922', CultureId: '1'},
		  headers: {
		    'x-rapidapi-host': 'realty-in-ca1.p.rapidapi.com',
		    'x-rapidapi-key': '0e3ec88dbcmshada36e7763f97c8p1e8044jsnfcc9ad331461'
		  }
		};

		axios.request(options).then(function (response) {
			console.log(response.data);
		}).catch(function (error) {
			console.error(error);
		});
	},[])
	
	return(
			<>
				<div className="container">
				    <div className="row">
				        <div className="col-md-12 col-lg-12">
				            <h4>GTA Statistics (All property types) *</h4>
				            <img src="../images/chart.png" width="100%"/>
				        </div>
				        <div className="col-md-4 col-lg-4"></div>
				        <div className="col-md-4 col-lg-4"></div>
				        <div className="col-md-4 col-lg-4">
				            <button type="button" className="thin rounded custom-button float-right"> View More Stats</button>
				        </div>
				        <div className="col-md-12 col-lg-12">
				            <ul className="stateUl">
				                <li>* Sales Record in GTA from 2005</li>
				                <li>Sales Record in South-west Ontario cities since 2018</li>
				                <li>Sales Record in Ottawa area since 2018</li>
				            </ul>
				            <p className="pt-2 pb-3">** Source: Based on analysis of information from past MLS® TRREB® CREA® ITSO® listings</p>
				        </div>
				    </div>
				</div>
			</>
		)
}
export default StatisticsArea;