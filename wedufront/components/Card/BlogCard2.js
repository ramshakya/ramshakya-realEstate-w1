import Link from "next/link";
import Image from "next/image";
const BlogCard = (props) => {
	const { image, title, label, date, url } = props;
	return (
		<div className="blog-container home-blog-container">
			<div className="blog-img-container homesection">
				{<Image
					src={image}
					alt={"Blog Image"}
					width={300}
					height={230}
					layout="responsive"
					objectFit='cover'
					className="cardImages  img-fluid lazyload"
					placeholder="blur"
					blurDataURL={image}
					quality='1'
				/>}
			</div>
			<div className="blog-content">
				<h6 className="text-400 home-blog-title pt-4 pb-2 h5">{title}</h6>
				<p className="home-blog-content">Thinking of selling your home? Start with a complimentary home evaluation and let us handle selling your home at top dollar in the shortest amount of time.</p>
				<Link href={url}><a className="home-link-button">LEARN MORE »</a></Link>
			</div>
		</div>

	);
}
export default BlogCard;