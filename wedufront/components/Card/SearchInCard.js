import Link from "next/link";
import Image from "next/image";
const SearchInCard = (props) => {
	const { community, CityName, image } = props;
	let communityData = [];
	if (community !== undefined) {
		communityData = community[CityName];
	}
	let showImage = '../images/search_background1.jpg'
	if (image !== null) {
		showImage = image
	}
	return (
		<div className="propety-card1">
			<Image
				src={showImage}
				layout={'responsive'}
				width={600}
				height={330}
				alt="blogs"
				objectFit={"cover"}
				placeholder="blur"
				blurDataURL={showImage}
				priority={true}
				quality='20'
			/>
			<Link href={'/city/' + CityName}>
				<a className="text-white" title={props.CityName}>
					<div className="city_info">
						<small>SEARCH IN</small>
						<h6 className="h4">{props.CityName} ({props.count})</h6>
					</div>
				</a>
			</Link>
			<div className="citylinks hoverEffect">
				<ul className="citylinks_links">
					{communityData.map((item, index) => {
						return (
							<li key={'community' + index}><Link href={'/city/' + CityName + '/' + item.Community}><a title={item.Community}>{item.Community}</a></Link></li>
						)
					})}
				</ul>
			</div>
		</div>
	)
}
export default SearchInCard;