import Link from "next/link";
import Image from "next/image";

const BlogCard1 = (props)=>{
	const {image,title,label,date,url,content} = props;
	return(
		<Link href={url}>
		<a href={url} className="blog-link">
		<div className="row">
			<div className={`${props.isLatest?'col-md-12 col-lg-12':'col-md-5 col-lg-5'}`}>
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
								quality='50'
							/> 
						</div>
				</div>
			</div>
			<div className={`${props.isLatest && props.isSearch ?'col-md-12 col-lg-12':'col-md-7 col-lg-7'}`}>
				<div className="blog-content latest-post-content">
					<div className="blog-label1">{label}</div>
					<h5 className="text-bold blog-title">{title}</h5>
					<p className="blog-post-date">{date}</p>
					<p className="blog-post-content" >{content?content.replace( /(<([^>]+)>)/ig, '').substring(0,100):""}...</p>
				</div>
			</div>
		</div></a>
		</Link>
		);
}
export default BlogCard1;