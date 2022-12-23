import Image from 'next/image'
import Constants from "../../constants/GlobalConstants"
const defaultImage = Constants.defaultImage
import homevalue from '../../public/images/homevalue.jpg'
const PropertyCard=(props)=>{
	return (
		<>
			<a href="http://test.cryptoestaterealty.com/property-details/C5436613" className="property-link"> 
			<div className="listing-card mb-4">
		      	<div className="listing-image">
		      		                               
		      			{/*<img src="https://mlsphotos.s3.ca-central-1.amazonaws.com/Photo-C5436613_0.jpeg" />*/}
		      			<Image
	                    src={homevalue}
	                    alt={"Property Image"}
	                    // layout='fill'
	                    width={400} 
	                    height={250} 
	                    layout="responsive"
	                    objectFit='cover'
	                    className="cardImages  img-fluid"
	                    placeholder="blur"
	                    blurDataURL = {homevalue}
	                    quality='1'
	                    />
		      		                          
		      	</div>
		      	<div className="top-ribbon1">Detached</div>
		      	<div className="top-ribbon2">
		      		<span className="span1">For Sale</span>
		      	</div>
		      	<div className="favourite-ribbon">
		      		<i className="fa fa-heart-o"></i>
		      		{/*<i className="fa fa-heart"></i>*/}
		      	</div>
		      	<div className="card-content">
		         	<h4>346 Jarvis St</h4>
		         	<p className="description">Tucked Away On A Quiet Lane Is A Rare...</p>
		         	<span className="spec" title="Bedrooms">3 <i className="fa fa-bed"></i></span>                                
		         	<span className="spec" title="Bathrooms">2 <i className="fa fa-bath"></i></span>                                                                
		         	<span className="spec" title="Bathrooms">500 <i className="fa fa-clone"></i><sup>2</sup></span>                                                                
		         	<span className="detail_btn">27 Days</span> 
		        </div>
		   	</div>
		   </a> 
		</>
		);
}
export default PropertyCard;