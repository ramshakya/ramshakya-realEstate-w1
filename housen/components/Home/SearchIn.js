import SearchInCard from "../Cards/SearchInCard"
const SearchIn=(props)=>{
	const cityData=props.cityData.city;
	const community = props.cityData.community;

	return(
			<>
				<div className="container pt-4 pb-5">
					<div className="row">
					{cityData.map((item)=>{
						return(
							<div className="col-md-4 col-lg-4 mb-4">
								<SearchInCard 
									CityName={item.CityName}
									image = {item.Image}
									community={community}
								/>
							</div>
							)
					})}
						
												
					</div>
				</div>
			</>
		)
}
export default SearchIn;