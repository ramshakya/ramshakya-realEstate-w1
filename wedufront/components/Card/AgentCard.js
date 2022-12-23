import Link from "next/link";
const AgentCard = (props)=>{
	const {image,name,url,buttonName} = props;
	return(
			
			<div className="row mt-3">
				<div className="col-md-4 col-lg-4">
					<div className="agent-image">
						<img src={image} />
					</div>
				</div>
				<div className="col-md-8 col-lg-8 agent-content">
					<div className="">
						<p className="text-center mb-3 staff-title">{name}</p>
						<center>
						<Link href={url}>
							<a href="#" className="common-btn">{buttonName}</a>
						</Link>
						</center>
					</div>
				</div>
			
			</div>
		);
}
export default AgentCard;