import Link from "next/link";
const SearchInCard=(props)=>{
	const {community,CityName,image}=props;
	let communityData=[];
	if(community!==undefined){
		communityData = community[CityName];
	}
	
	console.log("props city",communityData);
	let showImage = '../images/search_background1.jpg'
	if(image!==null){
		showImage=image
	}
	return(
			<>
				<div className="propety-card1">                
					<img src={showImage} className="city_images" width="100%" />                
					<div className="city_info">                    
						<small>SEARCH IN</small>                    
						<h4>{props.CityName}</h4>                
					</div>                
					<div className="citylinks hoverEffect">                   
						<ul className="citylinks_links">
						      {communityData.map((item)=>{
							return(
								<li><Link href="#"><a  title={item.Community}>{item.Community}</a></Link></li> 
							)
						})}                
							                 
						</ul>        
					</div>            
				</div>   
			</>
		)
}
export default SearchInCard;