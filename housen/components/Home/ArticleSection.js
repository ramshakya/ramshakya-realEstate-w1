import ArticleCard from "../Cards/ArticleCard"
const ArticleSection=(props)=>{
	const {article}=props;

	return(
		<>
			<div className="featuredListing mb-5 p-4 ">
			    <div className="row">
			        <div className="col-md-12 col-lg-12 pb-3">
			            <h5 className="featuredListingHeading">FEATURED ARTICLES</h5>
			        </div>
			    </div>
			    <div className="row">
			    {article.length > 0 &&  article.map((item,index)=>{
			    	let d = new Date(item.created_at).toDateString();
			    	return(
			    		<div className="col-md-4 col-lg-4" key={index}>
				        	<ArticleCard 
				        		image={item.MainImg}
								title={item.Title}
								label='Most Popular'
								date={d}
								isOne={true}
								url={'/blogs/' + item.Url}
								content={item.Content}
				        	/>
				        </div>
			    	)
			    })}
			        
			        
			    </div>
			</div>
		</>
		)
}
export default ArticleSection