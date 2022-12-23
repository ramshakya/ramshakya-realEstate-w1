import Image from "next/image";
import Link from "next/link";
const BlogCard = (props)=>{
	const {image,title,label,date,url} = props;
	let imageDivClass = 'w-100 position-relative vh-100';
	if(props.imageDivClass)
	{
		imageDivClass=props.imageDivClass
	}
	console.log(url);
	return(
		// <Link href={url}>
		<a  href={url} className="blog-link">
			<div className="blog-container">
				<div className="blog-img-container position-relative">
					{/*<img src={image} alt={title}/>*/}
							<Image
								src={image}
								layout={'responsive'}
								width={600}
								height={400}
								alt="blogs"
								objectFit={"cover"}
								placeholder="blur"
								blurDataURL={image}
								priority={true}
								quality='1'
							/> 
						{/* <img src={image} /> */}
						
					</div>
					<div className="blog-content">
						<div className="blog-label">{label}</div>
						<p className="blog-post-date">{date}</p>
						<h5 className="text-bold blog-title">{title}</h5>
					</div>
				</div>
		</a>
		// </Link>
		);
}
export default BlogCard;