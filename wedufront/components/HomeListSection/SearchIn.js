import SearchInCard from "../Card/SearchInCard"
import ShimmerEffect from "../../ReactCommon/Components/ShimmerEffect";
const SearchIn = (props) => {
	const cityData = props.cityData.city;
	const community = props.cityData.community;
	return (
		<div className="container pt-4 pb-5">
			<div className="row">
				<div className="title-wrapper">
					<h6 className="service-title">{props.headerText}</h6>
					<hr />
				</div>
				{props.isLoading &&
					<ShimmerEffect type="cardView" columnCls={"col-lg-3"} count={4} />
				}
				{cityData.map((item, index) => {
					return (
						<div className="col-md-4 col-lg-4 mb-4" key={'city' + index}>
							<SearchInCard
								CityName={item.CityName}
								count={item.count ? item.count : 0}
								image={item.Image}
								community={community}
							/>
						</div>
					)
				})}
			</div>
		</div>
	)
}
export default SearchIn;