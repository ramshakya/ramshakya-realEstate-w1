import Link from "next/link";
import ReactCarousel from './../../ReactCommon/Components/ReactCarousel';
import { useState, useEffect } from "react";
import Constants from "../../constants/Global"
import API from "../../ReactCommon/utility/api";
import Image from "next/image";
const PrePropertyCard = (props) => {
	const {
        id,
        Bedroom,
        Bathroom,
        PriceRange,
        City,
        BuildingName,
        Address,
        BuildingStatus,
        MediaImage,
        SaleStatus,
        SizeRange,
        BuildingType,
        Slug,
        Completion
        
    } = props.item;
     let image_url = Constants.defaultImage;
    if(MediaImage!==null){
    	let images = MediaImage.replace(/["\{\}\[\]]/gi, '');
	    let imageArray = images.split(",")
	    
	    if(imageArray.length>0){
	    	image_url = imageArray[0];
	    }
    }
    let add = Address.split(",");
    // console.log("Attechments",image_url);
	return(
		
		<>
			<div className="preConstructionCard">
								<div className="imageContainer position-relative">
									<Link href={'/projects/'+Slug}><a>
											{/*<Image
												src={image_url}
												layout={'responsive'}
												width={400}
												height={400}
												alt="blogs"
												objectFit={"cover"}
												placeholder="blur"
												blurDataURL={image_url}
												priority={true}
												quality='1'
											/>*/} 
										<img src={image_url} />
									</a></Link>
									<div className="propertyTypeLabel">{BuildingType}</div>

									<div className="propertyStatus">{Completion}</div>
								</div>
								<div className="contentArea">
									<h5 className="project_name">{BuildingName}</h5>
									<p className="project_address"><i className="fa fa-map-marker"> </i> {add.length?add[0]:''}</p>
									<p>{City}</p>
								</div>
							</div>
		</>
		)
}
export default PrePropertyCard;
