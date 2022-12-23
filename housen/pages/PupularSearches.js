import React, {useEffect,useState} from "react";
import Link from "next/link";
import TorontoSearches from "../components/Popular/TorontoSearches";
const PopularSearches = (props) =>{
	return(
			<>
				<TorontoSearches {...props}/>
			</>
		);
	

}
export default PopularSearches;