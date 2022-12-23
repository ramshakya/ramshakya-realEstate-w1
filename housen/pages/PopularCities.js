import React, {useEffect,useState} from "react";
import Link from "next/link";
import TorontoSearches from "../components/Popular/TorontoSearches";
const PopularCities = (props) =>{
	return(
			<>
				<TorontoSearches {...props}/>
			</>
		);
	

}
export default PopularCities;