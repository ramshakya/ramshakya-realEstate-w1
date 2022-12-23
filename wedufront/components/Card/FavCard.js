const FavCard = (props)=>{
	const {image,title,cityname,price,buttonName,url} = props;
	return(
			<div className="col-md-3 col-lg-3">
				<div className="favorite-container">
					<div className="img-container">
						<img src={image} />
					</div>
					<div className="favourite-content">
						<p>{title}</p>
						<p>{cityname}</p>
						<p>Price : ${price}</p>
						<center><a href={url} className="btn updatebtn mb-3">{buttonName}</a></center>
					</div>
				</div>
			</div>
		);
}
export default FavCard;